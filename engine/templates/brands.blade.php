<h2>ПОПУЛЯРНЫЕ БРЕНДЫ</h2>
<div class="table-brand">
	@foreach($brands as $brand)
	<div class="table-brand-item">
		<a href="{{$dir_prefix}}{{$brand['Link']}}">
			<img alt="{{$brand['Name']}}" src="{{$dir_prefix}}{{$brand['smallURL']}}" />	
		</a>
		<a href="{{$dir_prefix}}{{$brand['Link']}}">
			<div class="desc">{{$brand['Description']}}</div>
		</a>
	</div>
	@endforeach
</div>