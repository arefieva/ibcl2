@extends('site.layout_inner')
@section('css-before')
<base href="http://server2.webisgroup.ru/be_clean/">
@stop
@section('content')
<div class="margin-top-45 text-center">
	Запрашиваемой страницы не существует<br>
	<a href="{{$dir_prefix}}">На главную</a>
</div>
@stop