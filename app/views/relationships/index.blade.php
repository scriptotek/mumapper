@extends('layouts.master')


@section('content')

<div class="row">
    <div class="col-sm-10">
        <h2>Relasjoner</h2>
    </div>
    <div class="col-sm-2">
        <a href="{{ URL::action('RelationshipsController@getCreate') }}" class="btn btn-success" style="margin-top:1.5em; float:right;">
            Oprett ny
        </a>
    </div>
</div>

<div class="panel panel-default">

    <form class="form-horizontal panel-body" role="form" method="GET" action="{{ URL::action('RelationshipsController@index') }}">
        Vis relasjoner med
        <div class="form-group">
            <label class="col-sm-2 control-label">kildevokabular:</label>
            <div class="col-sm-10">
                {{ Form::select('sourceVocabularies[]', $vocabularyList, $sourceVocabularies,
                    array('class' => 'selectpicker',
                        'multiple' => true,
                        'data-none-selected-text' => 'alle'
                    )) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="targetVocabularies[]">målvokabular:</label>
            <div class="col-sm-10">
                {{ Form::select('targetVocabularies[]', $vocabularyList, $targetVocabularies, 
                    array('class' => 'selectpicker',
                        'multiple' => true,
                        'data-none-selected-text' => 'alle'
                    )) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="state">tilstand(er):</label>
            <div class="col-sm-10">
                {{ Form::select('states[]', $states, $selectedStates, 
                    array('class' => 'selectpicker',
                        'multiple' => true,
                        'data-none-selected-text' => 'alle'
                        )) }}

                {{ Form::select('reviewstate', $reviewStates, $selectedReviewState,
                    array('class' => 'selectpicker')) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="state">sist endret av:</label>
            <div class="col-sm-10">
                {{ Form::select('lastModifiedBy', $users, $lastModifiedBy, 
                    array('class' => 'selectpicker')) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="state">liste(r):</label>
            <div class="col-sm-2">
                {{ Form::select('tagsOp', $tagsOp, $selectedTagsOp, 
                    array('class' => 'selectpicker', 'data-width' => '100%' )) }}               
            </div>
            <div class="col-sm-8">
                {{ Form::select('tags[]', $tags, $selectedTags, 
                    array('class' => 'selectpicker',
                        'multiple' => true,
                        'data-none-selected-text' => 'alle'
                    )) }}
            </div>
        </div>

der kildebegrep har
        <div class="form-group">
            <label class="col-sm-2 control-label" for="state">term:</label>
            <div class="col-sm-6">
                {{ Form::text('label', $label, array(
                    'class' => 'form-control',
                    'placeholder' => 'F.eks. «Optikk»'
                ) ) }}
            </div>
            <div class="col-sm-4">
                Støtter trunkering foran og bak med %.
            </div>
        </div>

og målbegrep har

        <div class="form-group">
            <label class="col-sm-2 control-label" for="state">notasjon:</label>
            <div class="col-sm-6">
                {{ Form::text('notation', $notation, array(
                    'class' => 'form-control',
                    'placeholder' => 'F.eks. «535»'
                ) ) }}
            </div>
            <div class="col-sm-4">
                Støtter trunkering foran og bak med %.
            </div>
        </div>

        {{ Form::hidden('sort', $sort) }}
        {{ Form::hidden('order', $order) }}
        {{ Form::hidden('perPage', $perPage) }}

        <button type="submit" class="btn btn-primary" name="format" value="worklist">Vis arbeidsliste</button>
        <button type="submit" class="btn btn-primary" name="format" value="inline-rdfxml">Vis RDF/XML</button>
        <button type="submit" class="btn btn-primary" name="format" value="inline-turtle">Vis RDF/Turtle</button>

    </form>
</div>

@yield('results')

<script>

    document.addEventListener("DOMContentLoaded", function(event) {
      $('.selectpicker').selectpicker();
      $('select[name="perPage"]').on('change', function (e) {
            var qs = '<?php unset($query["perPage"]); echo http_build_query($query); ?>';
            var b = './relationships?';
            var url = (qs.length > 0) ? b + qs + '&' : b;
            url = url + 'perPage=' + $(this).val();
            window.location = url;
      });
  });

</script>

@stop