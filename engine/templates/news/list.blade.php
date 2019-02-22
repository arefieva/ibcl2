@extends('site.layout_inner')
@section('content')
<div class="sortPagiBar clearfix">
    <span class="page-noite">{{$navbar_info}}</span>
    <div class="bottom-pagination">
    	{!! $navbar !!}
    </div>
 </div>
 
<div class="news-content">
		@foreach($news as $item)			
			<div class="new-item">
				<span class="date">{{$item['Date']}}</span><br>
				<div class="text">{{$item['Header']}}</div>
				<a href="{{$item['Link']}}"><span class="more">Подробнее</span></a>
			</div><!-- /.new-item -->
		@endforeach
</div>

<div class="sortPagiBar clearfix">
    <span class="page-noite">{{$navbar_info}}</span>
    <div class="bottom-pagination">
    	{!! $navbar !!}
    </div>
 </div>
@stop