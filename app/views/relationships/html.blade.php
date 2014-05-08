@extends('relationships.index')

@section('results')

<strong>Viser {{ $relationships->count() }} av {{ $relationships->getTotal() }} relasjoner:</strong>

<ul class="list-group"></ul>
@foreach ($relationships as $rel)
	<li class="list-item">
		<a href="{{URL::action('RelationshipsController@getEdit', $rel->id) . '?' . http_build_query($query) }}">
			{{ $rel->representation(false, false) }}
		</a>
		({{ $rel->stateLabel() }})
	</li>
@endforeach
</ul>

<div style="text-align:center;">
{{ $relationships->appends($query)->links() }}
</div>

@stop