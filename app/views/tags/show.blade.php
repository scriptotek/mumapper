@extends('layouts.master')


@section('content')

	<h2>
		Merkelapp: {{ $tag->label }}
	</h2>

	<p>
		{{ $tag->description }}
	</p>

	<a href="{{ URL::action('RelationshipsController@getIndex') }}?tags%5B%5D={{ $tag->id }}&amp;format=worklist">
		{{ $tag->relationships()->count() }} relasjoner
	</a> er merket med denne merkelappen.

@stop