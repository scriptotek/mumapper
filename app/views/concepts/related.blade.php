	<ul>
		@yield('items')
		<li>
			Dokumenter:
			<a href="http://ask.bibsys.no/ask/action/result?cmd=&amp;kilde=biblio&amp;cql={{$bs_query}}&amp;sortering=sortdate-&amp;treffPrSide=50">
				Bibsys Ask</a>
			/
			<a href="http://bibsys-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/dlSearch.do?institution=UBO&amp;vid=UBO&amp;tab=library_catalogue&amp;prefLang=no_NO&amp;bulkSize=50&amp;query={{$oria_query}}">
				Oria</a>
		</li>
	</ul>
	@yield('bottom')
	<em style="color:#999;">[Vise bokstatistikk, osv…]</em>