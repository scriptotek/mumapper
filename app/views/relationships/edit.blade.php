@extends('layouts.master')

@section('content')

<div class="row">
  <div class="col-sm-6">
    <h2>
      Relasjon #{{ $relationship->id }}
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
  <div class="col-sm-6">
    <div style="float:right; margin-top:1.8em;">

      @if ($next)
        <a class="btn btn-warning" href="{{ 
          URL::action('RelationshipsController@getEdit', $next->id) . $query
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
    <td style="background:#eee; width:40%;">
      <div style="text-align:center;font-size:80%;color:#888;">
        Begrep i kildevokabular:
      </div>
      <div class="heading">
        {{ $relationship->sourceConcept->representation() }}
      </div>
      <h4>Andre relasjoner:</h4>
      <ul>
      @foreach ($relationship->sourceConcept->sourceRelationships as $c)
        @if ($c->targetConcept->id != $relationship->targetConcept->id)
        <li>
          {{ $c->representationFrom($relationship->sourceConcept) }} 
        </li>
        @endif
      @endforeach
      </ul>
      <h4>Eksterne lenker:</h4>
      {{ $relationship->sourceConcept->getRelatedContent() }}

    </td>
    <td colspan="2" style="text-align:center; width:20%; background:#fff;">
      <div style="text-align:center;font-size:80%;color:#888;">→ Relasjonstype: →</div>
      <form role="form" method="POST" action="{{ URL::action('RelationshipsController@postUpdate', $relationship->id) }}">

        <div class="form-group">
          <label for="state" style="display:none;">Mapping relation</label>

          {{ Form::select('state', 
              $states,
              $relationship->latest_revision_state,
              array('id' => 'state', 'class' => 'selectpicker')
          ) }}
        <span style="color:#888; font-size: 90%;" id="rel-responsible">
        {{ $relationship->latestRevision->reviewed_by 
          ? 'Godkjent av ' . $relationship->latestRevision->reviewedBy->name
          : 'Foreslått av ' . $relationship->latestRevision->createdBy->name
        }}
        </span>
        </div>

        <div class="form-group">
          <label for="comment" class="sr-only">Kommentar</label>
          <input type="text" class="form-control" id="comment" name="comment" placeholder="Kommentar">
        </div>

        <button type="submit" id="save-btn" class="btn btn-primary">Lagre/godkjenn</button>

      </form>

    </td>
    <td style="background:#efe; width:40%;">
      <div style="text-align:center;font-size:80%;color:#888;">
        Begrep i målvokabular:
      </div>
      <div class="heading">
        {{ $relationship->targetConcept->representation() }}
      </div>
      <h4>Andre relasjoner:</h4>
      <ul>
      @foreach ($relationship->targetConcept->targetRelationships as $c)
        @if ($c->sourceConcept->id != $relationship->sourceConcept->id)
        <li>
          {{ $c->representationFrom($relationship->targetConcept) }}
        </li>
        @endif
      @endforeach
      </ul>
      <h4>Eksterne lenker:</h4>
      {{ $relationship->targetConcept->getRelatedContent() }}

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