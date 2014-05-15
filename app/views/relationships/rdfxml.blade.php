@extends('relationships.index')

@section('header')
  <link rel="stylesheet" href="//yandex.st/highlightjs/8.0/styles/atelier-forest.light.min.css">
  <script src="//yandex.st/highlightjs/8.0/highlight.min.js"></script>
@stop

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