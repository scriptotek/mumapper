@extends('layouts.master')


@section('content')

	<h2>
		{{ $concept->vocabulary->label }}: {{ $concept->notation ?: $concept->prefLabel() }}
	</h2>
	<small>ID: {{ $concept->id }}</small>

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
				Etiketter:
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
				Ekstra:
			</th>
			<td>
				<p>
					{{ $concept->getRelatedContent() }}
				</p>
			</td>
		</tr>
	</table>

	
	<ul>
		
	</ul>


<pre><code class="language-xml">{{{ $concept->rdfRepresentation() }}}</code></pre>

<script>hljs.initHighlightingOnLoad();</script>

@stop