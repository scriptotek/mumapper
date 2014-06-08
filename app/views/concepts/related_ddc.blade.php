@extends('concepts.related')
@section('items')
		<li>
			<a href="http://webdeweyno.pansoft.de/webdewey/index_11.html?recordId=ddc%3a{{ $id }}">
				Slå opp i norsk WebDewey
			</a>
		</li>
		<li>
			<a href="http://dewey.org/webdewey/index_11.html?recordId=ddc%3a{{ $id }}">
				Slå opp i engelsk WebDewey
			</a>
		</li>
		<li>
			<a href="http://dewey.info/class/{{ $id }}/about">
				Slå opp i dewey.info
			</a>
		</li>
@stop
@section('bottom')
	{{ $tree }}
@stop