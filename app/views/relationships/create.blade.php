@extends('layouts.master')

@section('content')

<h2>
  Opprett ny relasjon
</h2>


<form role="form" action="{{ URL::action('RelationshipsController@postStore' )}}" method="POST">

{{--    <div class="form-group">
        <label class="col-sm-2 control-label" for="targetVocabulary">Målvokabular</label>
        <div class="col-sm-10">
            {{ Form::select('targetVocabulary', $vocabularyList, $targetVocabulary, array(
                'class' => 'selectpicker',
            )) }}
            (TODO: Huske sist brukte)
        </div>
    </div>--}}

    <div class="row">
    <div class="form-group col-sm-4">
        <label class="control-label" for="sourceConceptText">
            Kildebegrep
        </label>
        <div id="sourceConcept">
          {{ Form::text('sourceConcept', null, array(
            'id' => 'sourceConceptText',
            'class' => 'typeahead form-control',
          )) }}
        </div>
        <div>
          <input type="hidden" name="source_concept">
          <span id="sourceConceptDesc"><em>Ingen</em></span>
        </div>
    </div>


    <div class="form-group col-sm-4">
        <label class="control-label" for="state">
            Relasjon
        </label>
        <div>
            {{ Form::select('state', 
              $states,
              'exact',
              array('id' => 'state', 'class' => 'selectpicker')
          ) }}
        </div>
    </div>


    <div class="form-group col-sm-4">
        <label class="control-label" for="targetConceptText">
            Målbegrep
        </label>
        <div id="targetConcept">
            {{ Form::text('targetConcept', null, array(
                'id' => 'targetConceptText',
                'class' => 'typeahead form-control',
            )) }}
        </div>
        <div>
          <input type="hidden" name="target_concept">
            <span id="targetConceptDesc"><em>Ingen</em></span>
        </div>
    </div>

    </div>

    <div>
        <button type="submit" class="btn btn-primary">
            Opprett
        </button>        
    </div>

</form>

<script>
  
  $(function () {

    var config = [
      { name: 'sourceConcept', 
        url: '/concepts/search?vocabulary=1&q=%QUERY', 
        idfield: 'source_concept',
        descfield: 'sourceConceptDesc'
      },
      { name: 'targetConcept', 
        url: '/concepts/search?q=%QUERY',
        idfield: 'target_concept', 
        descfield: 'targetConceptDesc' 
      }
    ];

    config.forEach(function(c) {

      var hound = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        //prefetch: '../data/films/post_1960.json',
        limit: 10,
        remote: {
          url: c.url,
          ajax: { 
            type: 'GET',
            dataType: 'JSON'
          }
        }
      });
      hound.initialize();

      $('#' + c.name +' .typeahead')
          .typeahead({
            minLength: 2
          }, {
            name: c.name,
            displayKey: 'value',
            source: hound.ttAdapter(),
            templates: {
              empty: [
                '<div class="empty-message">',
                'ingen treff i prefLabel (nb)',
                '</div>'
              ].join('\n'),
              suggestion: function(o) {
                //console.log(o);
                return '<p><strong>' + o.label + '</strong> (' + o.vocabulary + ' : ' + o.identifier + ')</p>';
              }
            }
          })
         .on('typeahead:selected', function(e, s, d) {
            if (s) {
              console.log($('#' + c.idfield));
              $('input[name="' + c.idfield + '"]').val(s.id);
              $('#' + c.descfield).text(s.vocabulary + ': ' + s.identifier + ' – ' + s.label);            
            }
         })
         .on('typeahead:autocompleted', function(e, s, d) {
            if (s) {
              $('input[name="' + c.idfield + '"]').val(s.id);
              $('#' + c.descfield).text(s.vocabulary + ': ' + s.identifier + ' – ' + s.label);
            }
         })
         .on('typeahead:opened', function() {

          })
         .on('typeahead:closed', function() {
             
         });


    });

    $('.selectpicker').selectpicker();
    $('#sourceConceptText').focus();

  });

</script>

@stop