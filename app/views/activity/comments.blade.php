@extends('layouts.master')

@section('content')

<h2>Aktivitet : Kommentarer</h2>

<form role="form" class="form-inline well" method="GET" action="{{ URL::action('ActivityController@getComments') }}">
	Vis kommentarer
	som inneholder
	<input type="text" name="include[]" value="{{ array_get($include, 0) }}" class="form-control">
	og
	<input type="text" name="include[]" value="{{ array_get($include, 1) }}" class="form-control">
	men ikke <input type="text" name="exclude[]" value="{{ array_get($exclude, 0) }}" class="form-control">
	ei heller <input type="text" name="exclude[]" value="{{ array_get($exclude, 1) }}" class="form-control">
	<button type="submit" class="btn btn-primary">Vis</button>
</form>

<p>
	Viser {{ $comments->count() }} av {{ $comments->getTotal() }} kommentarer
</p>

<div class="panel panel-default history" style="margin-top:1.5em;">
	<ul class="list-group">
	@foreach ($comments as $com)
  		<li class="list-group-item">
  			{{ $com->asEvent(true) }}
		</li>
	@endforeach
	</ul>
</div>


{{ $comments->appends($querystring)->links() }}

@stop