@extends('site.layout_inner')
@section('content')

@if($desc)
<div class="brand_desc">
	<div class="brand_img"><img src="{{$image}}" alt="{{$brand_name}}" style="border: 1px solid #dedede;"/></div>
	<div class="brand_body">{!!$desc!!}</div>
</div>
@endif
<div class="row categories">
{!!$cats!!}
</div>
@if($body)
<a id="body" class="yakor"></a>
<div class="clearfix" style="padding-top: 30px;">
	<div class="brand_desc">
		<div class="brand_img"><img src="{{$image}}" alt="{{$brand_name}}" style="border: 1px solid #dedede;"/></div>
		<div class="brand_body" style="color: #000;">{!!$body!!}</div>
	</div>
</div>
@endif

@stop