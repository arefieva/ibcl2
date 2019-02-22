@extends('site.layout_inner')
@section('content')
<div class="row categories">
	@foreach($categories as $cat)
	<div class="col-lg-3 col-md-4 col-xs-6 categories-item">
		<div class="categories-item-content">
			<div class="categories-item-thumb">
				<a href="{{$cat['Link']}}"><img src="{{$dir_prefix}}{{$cat['Img']}}" alt="{{$cat['Name']}}"></a>
			</div>
			<h5 class="categories-item-name">
				<a href="{{$cat['Link']}}">{{$cat['Name']}}</a>
				<span>({{$cat['Count']}})</span>
			</h5>
		</div>
	</div>
	@endforeach
</div>
@stop