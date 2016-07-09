@extends('layouts.master')


@section('content')

<h2>Aktivitet</h2>

<p>
	<a href="{{ URL::action('ActivityController@getComments') }}">Vis bare kommentarer</a>
</p>


<div class="panel panel-default history">
	<ul class="list-group">
	@foreach ($events as $evt)
  		<li class="list-group-item">
  			{{ $evt->asEvent(true) }}
		</li>
	@endforeach
	</ul>
</div>

{{ $results->links() }}

@stop