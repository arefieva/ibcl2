@extends('site.layout_inner')
@section('content')
<div class="news-details text-default">
	@if(!empty($bigImage))
	<div class="news-details-img">
		<a href="{{$dir_prefix}}{{$bigImage}}" class="fb-img">
			<img src="{{$dir_prefix}}{{$bigImage}}">
		</a>
	</div>
	@endif
	{!! $Body !!}
	<div class="space-10"></div>
	<a href="{{route::link('news')}}">&laquo; назад к списку</a>
	<div class="clearfix"></div>
</div>
@stop