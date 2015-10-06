@extends('layouts.master')


@section('content')

<article
  vocab="http://www.w3.org/2004/02/skos/core#"
  typeof="Concept"
  resource="{{ $concept->uri() }}">
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
					<span style="color: #999;">{{ $label->class }} ({{ $label->lang }}):</span>
						<span
						 property="{{ $label->class }}"
						 lang="{{ $label->lang }}">

					@if(($label->class == 'altLabel') && ($concept->vocabulary->label == 'DDK23'))
						<em>(sensurert)</em>
					@else
						{{ $label->value }}
					@endif
					</span>
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
					<li property="{{ $rel->stateAsSkos() }}" resource="{{ $rel->targetConcept->uri() }}" >
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
				{{ $concept->getRelatedContent() }}
				<ul>
					<li>
						Søk mot Bibsys Ask som utnytter mappingene:<br>
						<a href="{{ $concept->broadSearchUrl() }}">{{ $concept->broadSearchCQL() }}</a>
					</li>
				</ul>
			</td>
		</tr>
	</table>

</article>

{{-- <pre><code class="language-xml">{{{ $concept->rdfRepresentation() }}}</code></pre> --}}

<script>hljs.initHighlightingOnLoad();</script>

@stop