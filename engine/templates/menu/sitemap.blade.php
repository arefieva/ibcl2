<ul class="sitemap">
	@foreach($menu as $m)
	<li>
		<a href='{{ $m['link'] }}'>{{ $m['name'] }}</a>
	</li>
	@endforeach
</ul>