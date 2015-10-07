@extends('layouts.master')

@section('content')

<div class="row">
  <div class="col-sm-4">
    <h2>
      {{ Lang::get('relationships.title') }} #{{ $relationship->id }}
    </h2>
    <div style="font-size: 85%;">
      @foreach ($relationship->tags as $tag)
        <a href="{{ URL::action('TagsController@getShow', $tag->id) }}">
          <i class="glyphicon glyphicon-tag"></i>
          {{$tag->label}}
        </a>
      @endforeach
      {{ $relationship->weight
        ? '<strong>Vekting</strong>: ' . $relationship->weight
        : ''
      }}
    </div>
  </div>
  <div class="col-sm-8" style="padding-top:.4em;">

    <div style="float:right; margin-left: .5em;">
      @if ($nextId != -1)
        <a class="btn btn-warning" href="{{ 
          URL::action('RelationshipsController@show', $nextId) . $query
        }}">
          Hopp til neste
        </a>
      @else
        <em style="color: #bbb; font-size:80%; text-align:center;display:block;">
          Ingen flere relasjoner<br>
          i arbeidslisten
        </em>
      @endif
    </div>

    <p style="font-size:80%; text-align:right;">
      <a href="{{ URL::action('RelationshipsController@index') . $query }}">Nåværende arbeidsliste</a>:

      @if ($args['targetVocabularies'])
        <span class="criterium">
          målvokabular IN (
          @foreach ($args['targetVocabularies'] as $x)
            "{{Vocabulary::find($x)->label}}"
          @endforeach
          )
        </span>
      @endif

      @if ($args['selectedStates'])
        <span class="criterium">
          tilstand IN (
          @foreach ($args['selectedStates'] as $x)
            "{{$x}}"
          @endforeach
          )
        </span>
      @endif

      @if ($args['selectedReviewState'])
        <span class="criterium">
          godkjenningsstatus = {{ $args['selectedReviewState'] }}
        </span>
      @endif

      @if ($args['selectedTags'])
        <span class="criterium">
          liste {{ $args['selectedTagsOp'] == 'and' ? 'ALL' : 'ANY' }} (
          @foreach ($args['selectedTags'] as $x)
            "{{ Tag::find($x)->label }}"
          @endforeach
          )
        </span>
      @endif

      @if ($args['label'])
        <span class="criterium">
          term = "{{ $args['label'] }}"
        </span>
      @endif

      @if ($args['notation'])
        <span class="criterium">
          notasjon = "{{ $args['notation'] }}"
        </span>
      @endif

  </div>
</div>
<!--
Hjelp:

platypus skos:broadMatch ex2:eggLayingAnimals
ex1:platypus skos:relatedMatch ex2:eggs.
ex1:animal skos:exactMatch ex2:animals.
-->
<!--

The SKOS mapping properties are skos:closeMatch, skos:exactMatch, skos:broadMatch, skos:narrowMatch and skos:relatedMatch

-->
<table class="table relationship">
  <tr>
    <td style="width:40%;">

      <div style="text-align:center;font-size:80%;color:#888;">
        {{ Lang::get('relationships.source_vocabulary_concept') }}:
      </div>

      {{ $relationship->sourceConcept->extendedRepresentation('source', $relationship->id) }}

    </td>
    <td colspan="2" style="text-align:center; width:20%; background:#fff;">
      <div style="text-align:center;font-size:80%;color:#888;">→ Mapping relation: →</div>
      <form role="form" method="POST" action="{{ URL::action('RelationshipsController@postUpdate', $relationship->id) }}">

        <div class="form-group">
          <label for="state" style="display:none;">Mapping relation</label>

          {{ Form::select('state', 
              $states,
              $relationship->latest_revision_state,
              array('id' => 'state', 'class' => 'selectpicker')
          ) }}
        <div style="color:#666; font-size: 90%;" id="rel-responsible">
        {{ ($relationship->latestRevision->reviewed_at ? 'Godkjent av ' : 'i følge ')
           . $relationship->latestRevision->createdBy->name
        }}
        </div>
        </div>

        <div class="form-group">
          <label for="comment" class="sr-only">{{ Lang::get('relationships.comment') }}</label>
          <input type="text" class="form-control" id="comment" name="comment" placeholder="Kommentar">
        </div>

        <input type="hidden" name="query" value="{{ $query }}">
        <input type="hidden" name="next" value="{{ $nextId }}">

        <button type="submit" id="save-btn" class="btn btn-primary">Lagre/godkjenn</button>

      </form>

    </td>
    <td style="width:40%;">

      <div style="text-align:center;font-size:80%;color:#888;">
        {{ Lang::get('relationships.target_vocabulary_concept') }}:
      </div>

      {{ $relationship->targetConcept->extendedRepresentation('target', $relationship->id) }}

    </td>
  </tr>

</table>

<div class="panel panel-default history">
  <div class="panel-heading">Aktivitet</div>
  <ul class="list-group">
    @foreach ($relationship->history() as $event)
    <li class="list-group-item">
      {{ $event }}
    </li>
    @endforeach
    <li class="list-group-item">
      <form role="form"  class="row" method="POST" action="{{ URL::action('RelationshipsController@postAddComment', $relationship->id) }}">
        <div class="col-sm-10">
          <input name="comment" type="text" placeholder="Kommentar" class="form-control">
        </div>
        <div class="col-sm-2">
          <button type="submit" class="btn btn-default">Kommentér</button>        
        </div>
      </form>
  </ul>
</div>

<script>

  $(function () {

    function updateFormState() {

      var canReview = {{ $canReview }};

      if ($('.selectpicker').val() == initState) {

        $('#rel-responsible').css('visibility', 'visible');

        if ($('.selectpicker').val() == 'suggested') {

          $('#save-btn')
            .removeClass('btn-success').removeClass('btn-primary')
            .text('Lagre / godkjenn')
            .prop('disabled', true);

        } else if (canReview) {

          $('#save-btn')
            .addClass('btn-success').removeClass('btn-primary')
            .text('Godkjenn')
            .prop('disabled', false);

        } else {

          $('#save-btn')
            .addClass('btn-success').removeClass('btn-primary')
            .text('Godkjenn')
            .prop('disabled', true);

        }

      } else {

        // don't change display since we don't want the form layout to change
        $('#rel-responsible').css('visibility', 'hidden'); 

        $('#save-btn')
          .addClass('btn-primary').removeClass('btn-success')
          .text('Lagre')
          .prop('disabled', false);

      }
    };

    $('#state option[value="exact"]').attr('data-subtext','skos:exactMatch')
    $('#state option[value="close"]').attr('data-subtext','skos:closeMatch')
    $('#state option[value="broad"]').attr('data-subtext','skos:broadMatch')
    $('#state option[value="narrow"]').attr('data-subtext','skos:narrowMatch')
    $('#state option[value="related"]').attr('data-subtext','skos:relatedMatch')

    $('.selectpicker').selectpicker();
    $('.selectpicker').focus();
    var initState = $('.selectpicker').val();
    $('.selectpicker').on('change', updateFormState);
    updateFormState();

  });

</script>

@stop