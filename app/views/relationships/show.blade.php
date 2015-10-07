@extends('layouts.master')


@section('content')

<h2>
  {{ Lang::get('relationships.title') }} #{{ $relationship->id }}
</h2>

<table class="table relationship">
  <tr>
    <td style="width:40%;">

      <div style="text-align:center;font-size:80%;color:#888;">
        {{ Lang::get('relationships.source_vocabulary_concept') }}:
      </div>

      {{ $relationship->sourceConcept->extendedRepresentation($relationship->id) }}

    </td>
    <td colspan="2" style="text-align:center; width:20%; background:#fff; padding-top: 1em;">

      <strong>
        {{ $relationship->stateLabel() }}
      </strong>

      <div style="color:#888; font-size: 90%;" id="rel-responsible">
      {{ ($relationship->latestRevision->reviewed_at ? 'Godkjent av ' : 'i følge ')
         . $relationship->latestRevision->createdBy->name
         . '<br>' . $relationship->latestRevision->created_at
      }}
      </div>

    </td>
    <td style="width:40%;">

      <div style="text-align:center;font-size:80%;color:#888;">
        {{ Lang::get('relationships.target_vocabulary_concept') }}:
      </div>

      {{ $relationship->targetConcept->extendedRepresentation($relationship->id) }}

    </td>
  </tr>

</table>

@stop
