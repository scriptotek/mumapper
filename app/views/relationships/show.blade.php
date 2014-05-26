@extends('layouts.master')


@section('content')

<h2>
  Relasjon #{{ $relationship->id }}
</h2>

<table class="table relationship">
  <tr>
    <td style="background:#eee; width:40%;">
      <div class="heading">
        {{ $relationship->sourceConcept->representation() }}
	  </div>

      <p>
      @foreach ($relationship->sourceConcept->sourceRelationships as $c)
        @if ($c->targetConcept->id != $relationship->targetConcept->id)
        <div>
          har {{ $c->representationFrom($relationship->sourceConcept) }} 
        </div>
        @endif
      @endforeach
      </p>

      <p>
        {{ $relationship->sourceConcept->getRelatedContent() }}
      </p>


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
      <div class="heading">
        {{ $relationship->targetConcept->representation() }}
      </div>

      <p>
      @foreach ($relationship->targetConcept->targetRelationships as $c)
        @if ($c->sourceConcept->id != $relationship->sourceConcept->id)
        <div>
          har {{ $c->representationFrom($relationship->targetConcept) }}
        </div>
        @endif
      @endforeach
      </p>

      <p>
        {{ $relationship->targetConcept->getRelatedContent() }}
      </p>

    </td>
  </tr>

</table>

@stop
