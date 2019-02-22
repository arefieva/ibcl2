@extends('site.layout_inner')
@section('content')
<div class="text-center margin-top-40 padding-top-10 padding-bottom-10 bg-success">
	<p><b>Заказ оформлен</b></p>
	<p>Благодарим вас за проявленный интерес<br>
	Информация о заказе отправлена на ваш Email<br>
	<a href="{{route::link('index')}}">На главную</a>
	</p>
</div>
@stop