@foreach($news as $item)
<li class="menu-item">
	<span class="date">{{$item['Date']}}</span><br>
	<div class="text"><a href="{{$item['Link']}}">{{$item['Header']}}...</a></div>
	<a href="{{$item['Link']}}"><span class="more"><i class="fa fa-angle-double-right" aria-hidden="true"></i>Подробнее</span></a>
</li><!-- /.menu-item -->

@endforeach
