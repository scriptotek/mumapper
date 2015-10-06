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
    <div class="form-group col-sm-4 has-error">

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


    <div class="form-group col-sm-4 has-error">
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
        <button type="submit" class="btn btn-primary" disabled>
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
      }
      ,
      { name: 'targetConcept', 
        url: '/concepts/search?excludevocabulary=1&q=%QUERY',
        idfield: 'target_concept', 
        descfield: 'targetConceptDesc' 
      }
    ];

    var validFields = {};

    function checkValidity() {
      var disabled = false;
      config.forEach(function(c) {
        if (!validFields[c.name]) disabled = true;
      });
      $('button[type="submit"]').prop('disabled', disabled);
    }

    config.forEach(function(c) {

      validFields[c.name] = false;

      var selectedConcept = null;

      function selectConcept(field, concept) {
        selectedConcept = concept;
        if (concept != null) {
          console.log('selectConcept: ' + concept.identifier + ' - ' + concept.label);
          $('input[name="' + field.idfield + '"]').val(concept.id);
          $('#' + field.descfield).text(concept.vocabulary + ': ' + concept.identifier + ' – ' + concept.label);
          $('#' + field.name).parent().removeClass('has-error').addClass('has-success'); 
          validFields[c.name] = true;
        } else {
          console.log('selectConcept: null');
          $('input[name="' + field.idfield + '"]').val('');
          $('#' + field.descfield).text('');          
          $('#' + field.name).parent().removeClass('has-success').addClass('has-error'); 
          validFields[c.name] = false;
        }
        checkValidity();
      }

      var hound = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
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

      var txtField = $('#' + c.name +' .typeahead')

      txtField
          .typeahead({
            minLength: 2
          }, {
            name: c.name,
            displayKey: 'value',
            source: hound.ttAdapter(),
            templates: {
              empty: [
                '<div class="tt-empty">',
                '<p>Ingen treff</p>',
                '</div>'
              ].join('\n'),
              suggestion: function(o) {
                //console.log(o);
                return '<p><strong>' + o.label + '</strong> (' + o.vocabulary + ' : ' + o.identifier + ')</p>';
              }
            }
          })
         .on('typeahead:selected', function(e, s, d) {
            console.log('typeahead:selected');
            if (s) {
              selectConcept(c, s);
            }
         })
         .on('typeahead:autocompleted', function(e, s, d) {
            console.log('typeahead:autocompleted');
            if (s) {
              selectConcept(c, s);
            }
         })
         .on('typeahead:opened', function() {
            console.log('typeahead:opened');
            //console.log(hound.index.datums);
          })
         .on('typeahead:closed', function() {
            console.log('typeahead:closed');
         })
         .on('typeahead:matched', function(e, datum) {
            console.log('GOT typeahead:matched');
            selectConcept(c, datum);
         })
         .on('keyup', function (e) {
            var v = txtField.val();
            //console.log(selectedConcept);
            if (selectedConcept && v != selectedConcept.label && v != selectedConcept.identifier) {
              selectConcept(c, null);
            }
            window.hound = hound;

            var matches = hound.index.get(v);

            // console.log(v);
            // console.log(matches);
            // console.log(hound.index);
            // matches = hound.sorter(matches).slice(0, 10);
            // console.log(matches);
            
            return true;
         });


    });

    $('.selectpicker').selectpicker();
    $('#sourceConceptText').focus();

  });

</script>

@stop