
	<ul style="max-height:300px;overflow:auto;">
		@if ($concept->vocabulary->label == 'WDNO')
		<li>
			<a target="wd" href="http://deweyno.pansoft.de/webdewey/index_11.html?recordId=ddc%3a{{ $concept->identifier }}">
				Norsk WebDewey
			</a>
		</li>
		<li>
			<a target="wd" href="http://dewey.org/webdewey/index_11.html?recordId=ddc%3a{{ $concept->identifier }}">
				Engelsk WebDewey
			</a>
		</li>
		@else

		<li>
			@if (isset($bs_query))
			<a target="lex" href="http://ask.bibsys.no/ask/action/result?cmd=&amp;kilde=biblio&amp;cql={{$bs_query}}&amp;sortering=sortdate-&amp;treffPrSide=50">
				Bibsys Ask</a>
			@endif

			@if (isset($oria_query))
			<a target="lex" href="http://bibsys-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/dlSearch.do?institution=UBO&amp;vid=UBO&amp;tab=library_catalogue&amp;prefLang=no_NO&amp;bulkSize=50&amp;query={{urlencode($oria_query)}}">
				Oria</a>
			@endif
		</li>
		<li>
			<a target="lex" href="http://www.nob-ordbok.uio.no/perl/ordbok.cgi?&amp;ant_bokmaal=5&amp;ant_nynorsk=5&amp;begge=+&amp;ordbok=begge&amp;OPP={{$pref_label}}">
				Bokmålsordboka
			</a>
		</li>
		@endif
		@if ($concept->vocabulary->label != 'WDNO')
		<li>
			<a target="lex" href="https://no.wikipedia.org/wiki/{{ $pref_label }}">Wikipedia:</a>
			<span id="wp{{ $concept->id }}">...</span>
		</li>
		<li>
			<a target="lex" href="https://snl.no/.search?query={{ $pref_label }}">SNL:</a>
			<span id="snl{{ $concept->id }}">...</span>
		</li>
		@endif
		@if (isset($oria_query))
		<li>
			<a target="lex" href="http://bibsys-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/dlSearch.do?institution=UBO&amp;vid=UBO&amp;tab=library_catalogue&amp;prefLang=no_NO&amp;bulkSize=50&amp;query={{urlencode($oria_query)}}">Oria:</a>
			<span id="oria{{ $concept->id }}">...</span>
		</li>
		@endif
	</ul>

	<script>
		document.addEventListener("DOMContentLoaded", function() {

			@if (isset($primo_field))
			var title = '{{ $pref_label }}';
			var primo_field = '{{ $primo_field }}';
			if (title == '') {
				$('#title_lookups{{ $concept->id }}').hide();
			} else {
				console.log('WP check: {{ $concept->id }}');
				$.getJSON('/apis/nowiki', {
					query: title
				}).done(function(response) {
					var extract = response.extract;
					console.log('WP done: {{ $concept->id }}');

					$('#wp{{ $concept->id }}').html('Not found');
					if (extract) {
						$('#wp{{ $concept->id }}').html(extract);
					}

				}).fail(function() {
					$('#primo{{ $concept->id }}').html('Error: Wikipedia search failed!');
				});

				console.log('SNL check: {{ $concept->id }}');
				$.getJSON('/apis/snl', {
					query: title
				}).done(function(response) {
					console.log('SNL done: {{ $concept->id }}');

					$('#snl{{ $concept->id }}').html('Not found');
					if (response.length > 0) {
						var x = '[' + response[0].title + '] ' + response[0].first_two_sentences;
						$('#snl{{ $concept->id }}').html(x);
					}
				}).fail(function() {
					$('#primo{{ $concept->id }}').html('Error: SNL search failed!');
				});

				$.getJSON('/apis/primo', {
					query: title,
					field: primo_field
				}).done(function(response) {
					console.log(response);
					$('#oria{{ $concept->id }}').html('No docs found');
					if (response.length > 0) {
						$('#oria{{ $concept->id }}').html(response[0].first_two_sentences);
					}
				}).fail(function() {
					$('#oria{{ $concept->id }}').html('Failed!');
				});
			}
			@endif

		});
	</script>