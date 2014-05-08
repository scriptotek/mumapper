@extends('layouts.master')


@section('content')

Emner:
@foreach ($concepts as $concept)
	<a href="{{URL::action('ConceptsController@getShow', $concept->id) }}">
		{{ $concept->representation() }}
	</a>
@endforeach

@stop