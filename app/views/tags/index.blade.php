@extends('layouts.master')

@section('content')

	<h2>
        Lister
	</h2>

	@foreach ($tags as $tag)
		<h3>
			<a href="{{ URL::action('TagsController@getShow', $tag->id) }}">
				<i class="glyphicon glyphicon-list"></i>
				{{ $tag->label }}
			</a>
		</h3>
		<p style="color: #888;">
			{{ $tag->relationships()->count() }}
			mappinger, hvorav 
			{{ $tag->relationships()->where('latest_revision_state', '=', 'suggested')->count() }}
			forslag,
			{{ $tag->relationships()->where('latest_revision_state', '=', 'exact')->count() }}
			EQ,
			{{ $tag->relationships()->where('latest_revision_state', '=', 'close')->count() }}
			~EQ,
			{{ $tag->relationships()->where('latest_revision_state', '=', 'broad')->count() }}
			BM,
			{{ $tag->relationships()->where('latest_revision_state', '=', 'narrow')->count() }}
			NM,
			{{ $tag->relationships()->where('latest_revision_state', '=', 'rejected')->count() }}
			avslått
		</p>
		<p>
			{{ $tag->description }}		
		</p>
	@endforeach
	</ul>

@stop