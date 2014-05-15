@extends('relationships.index')

@section('results')

<p style="float:right;">
	{{ Form::select('perPage', $perPageOptions, $perPage, 
		array('class' => 'selectpicker')) }}
</p>

<strong>Viser {{ $relationships->count() }} av {{ $relationships->getTotal() }} relasjoner:</strong>

<table class="table">
	<tr>
		<th>
			<a href="{{ $sort_urls['source_concept'] }}">
				@if ($sort == 'source_concept')
					<span class="glyphicon glyphicon glyphicon-sort-by-attributes{{ $order == 'desc' ? '-alt' : '' }}"></span>
				@endif
				Kildebegrep
			</a>
		</th>
		<th>
			<a href="{{ $sort_urls['relationship'] }}">
				@if ($sort == 'relationship')
					<span class="glyphicon glyphicon glyphicon-sort-by-attributes{{ $order == 'desc' ? '-alt' : '' }}"></span>
				@endif
				Relasjon
			</a>
		</th>
		<th>
			<a href="{{ $sort_urls['target_concept'] }}">
				@if ($sort == 'target_concept')
					<span class="glyphicon glyphicon glyphicon-sort-by-attributes{{ $order == 'desc' ? '-alt' : '' }}"></span>
				@endif
				Målbegrep
			</a>
		</th>
		<th>
			<a href="{{ $sort_urls['updated_at'] }}">
				@if ($sort == 'updated_at')
					<span class="glyphicon glyphicon glyphicon-sort-by-attributes{{ $order == 'desc' ? '-alt' : '' }}"></span>
				@endif
				Sist endret
			</a>
		</th>
	</tr>

@foreach ($relationships as $rel)
	<tr>
		<td>
			{{ $rel->sourceConcept->representation() }}
		</td>
		<td style="white-space:nowrap;">
			<a href="{{URL::action('RelationshipsController@getEdit', $rel->id) . '?' . http_build_query($query) }}">
				{{ $rel->stateLabel() }}
				@if (count($rel->comments))
					<span class="glyphicon glyphicon-comment" title="Relasjonen har kommentarer"></span>
				@endif
			</a>
		</td>
		<td>
			{{ $rel->targetConcept->representation() }}
		</td>
		<td>
			{{ $rel->updated_at }}
		</td>
	</tr>
@endforeach
</table>


<div style="text-align:center;">
{{ $relationships->appends($query)->links() }}
</div>

@stop