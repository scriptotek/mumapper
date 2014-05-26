@extends('layouts.master')


@section('content')

<h2>
  {{ Lang::get('relationships.title') }} #{{ $relationship->id }}
</h2>

<table class="table relationship">
  <tr>
    <td style="background:#eee; width:40%;">

      <div class="heading">
        {{ $relationship->sourceConcept->representation() }}
      </div>
      <h4>{{ Lang::get('relationships.other_relationships') }}:</h4>
      <ul>
      @foreach ($relationship->sourceConcept->sourceRelationships as $c)
        @if ($c->targetConcept->id != $relationship->targetConcept->id)
        <li>
          {{ $c->representationFrom($relationship->sourceConcept) }}
        </li>
        @endif
      @endforeach
      </ul>
      <h4>{{ Lang::get('relationships.external_resources') }}:</h4>
      {{ $relationship->sourceConcept->getRelatedContent() }}

    </td>
    <td colspan="2" style="text-align:center; width:20%; background:#fff;">

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
    <td style="background:#efe; width:40%;">

      <div style="text-align:center;font-size:80%;color:#888;">
        {{ Lang::get('relationships.target_vocabulary_concept') }}:
      </div>
      <div class="heading">
        {{ $relationship->targetConcept->representation() }}
      </div>
      <h4>{{ Lang::get('relationships.other_relationships') }}:</h4>
      <ul>
      @foreach ($relationship->targetConcept->targetRelationships as $c)
        @if ($c->sourceConcept->id != $relationship->sourceConcept->id)
        <li>
          {{ $c->representationFrom($relationship->targetConcept) }}
        </li>
        @endif
      @endforeach
      </ul>
      <h4>{{ Lang::get('relationships.external_resources') }}:</h4>
      {{ $relationship->targetConcept->getRelatedContent() }}

    </td>
  </tr>

</table>

@stop
