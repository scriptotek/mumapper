@extends('layouts.master')


@section('content')

	<h2>
		{{ $concept->vocabulary->label }}: {{ $concept->notation ?: $concept->prefLabel() }}
	</h2>
	<small style="color:#999;">Ikke-stabil ID: {{ $concept->id }}</small>

	@if ($concept->draft)
	<p class="bg-danger" style="padding:1em;">
		<i class="glyphicon glyphicon-exclamation-sign"></i>
		Dette begrepet ble ikke funnet i originalvokabularet sitt.
	</p>
	@endif

	<table class="table">
		<tr>
			<th>
				URI:
			</th>
			<td>
				{{ $concept->uri() }}
			</td>
		</tr>
		<tr>
			<th>
				Termer:
			</th>
			<td>
				<ul>
					@foreach ($concept->labels as $label)
					<li>
						<span style="color: #999;">
							{{ $label->class }} ({{ $label->lang }}):
						</span>
						{{ $label->value }}
					</li>
					@endforeach
				</ul>		
			</td>
		</tr>
		<tr>
			<th>
				Mappinger:
			</th>
			<td>
				<ul>
					@foreach ($concept->sourceRelationships as $rel)
					<li>
						{{ $rel->representationFrom($concept) }}
					</li>
					@endforeach
					@foreach ($concept->targetRelationships as $rel)
					<li>
						{{ $rel->representationFrom($concept) }}
					</li>
					@endforeach
				</ul>		
			</td>
		</tr>
		<tr>
			<th>
				Relatert:
			</th>
			<td>
				<p>
					{{ $concept->getRelatedContent() }}
				</p>
				<ul>
					<li>
						Søk mot Bibsys Ask som utnytter mappingene:<br>
						<a href="{{ $concept->broadSearchUrl() }}">{{ $concept->broadSearchCQL() }}</a>
					</li>
				</ul>
			</td>
		</tr>
	</table>

	
	<ul>
		
	</ul>


<pre><code class="language-xml">{{{ $concept->rdfRepresentation() }}}</code></pre>

<script>hljs.initHighlightingOnLoad();</script>

@stop