@extends('relationships.index')

@section('results')

<p>
	<a href="{{ $directLink }}">
		<em class="glyphicon glyphicon-save"></em>
		Last ned hele datasettet
	</a>
</p>

<p>
Viser <em>opptil</em> {{ $relationships->count() }} av {{ $relationships->getTotal() }} relasjoner: (RDF-representasjonene inkluderer ikke <em>foreslåtte</em> eller <em>avslåtte</em> mappinger)
</p>

<pre><code class="language-xml">{{{ $data }}}</code></pre>

<script>hljs.initHighlightingOnLoad();</script>

@stop