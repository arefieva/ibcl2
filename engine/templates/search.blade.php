@extends('site.layout_inner')
@section('content')
<div class="text-default">
@if($has_results)
<table>
	<tr>
		<td colspan="2" class="text-center">Запрос: <b>"{{ $s }}"</b></td>
	</tr>
	<tr>
		<td colspan="2" class="text-center">{!! $navbar !!}</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr>
		</td>
	</tr>
	@foreach($results as $result)
	<tr>
		<td width="1%" valign="top">
			<strong>{{ $result['no'] }}.</strong>
		</td>
		<td valign="top">
			<strong><a href="{{ $result['URL'] }}">{{ $result['Header'] }}</a></strong>
			<div>
			{!! $result['Samples'] !!}
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<hr>
		</td>
	</tr>
	@endforeach
	<tr>
		<td colspan="2" class="text-center">{!! $navbar !!}</td>
	</tr>
</table>
@else
<div class="search-none">
	{!! $message !!}
	@if($message)
	<hr>
	@endif
</div>
@endif
</div>
@stop