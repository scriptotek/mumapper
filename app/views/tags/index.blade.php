@extends('layouts.master')

@section('content')

	<h2>
        Lister
	</h2>

	@foreach ($tags as $tag)
		<h3>
			<a href="{{ URL::action('RelationshipsController@getIndex') }}?tags%5B%5D={{ $tag->id }}&amp;format=worklist">
				<i class="glyphicon glyphicon-tag"></i>
				{{ $tag->label }}
			</a>
		</h3>
		<p style="color: #888;">
			{{ $tag->relationships()->count() }}
			relasjoner, hvorav 
			{{ $tag->relationships()->where('latest_revision_state', '=', 'exact')->count() }}
			manuelt vurdert som eksakte,
			{{ $tag->relationships()->where('latest_revision_state', '=', 'close')->count() }}
			som nær-eksakte,
			{{ $tag->relationships()->where('latest_revision_state', '=', 'broad')->count() }}
			som broadmatch,
			{{ $tag->relationships()->where('latest_revision_state', '=', 'narrow')->count() }}
			som narrowmatch
		</p>
		<p>
			{{ $tag->description }}		
		</p>
	@endforeach
	</ul>

@stop