@extends('relationships.index')

@section('results')

<p>
	Merk at RDF-representasjonene kun inkluderer <em>godkjente</em> mappinger. 
</p>

<a href="{{ $directLink }}">Last ned</a>
<pre><code>{{{ $data }}}</code></pre>

<script>hljs.initHighlightingOnLoad();</script>

@stop