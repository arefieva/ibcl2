@extends('site.layout_inner')
@section('content')
<div class="text-center margin-top-40 padding-top-15 padding-bottom-15 bg-info">
	<span>Нажмите на кнопку ниже для перехода на защищённую страницу платёжной системы и оплаты заказа</span>
	<form method='post' action='https://merchant.intellectmoney.ru/ru/' >
		<input type='hidden' value='{{ $id }}' name='orderId'>
		<input type='hidden' value='{{ $eshopId }}' name='eshopId'>
		<input type='hidden' value='{{ $serviceName }}' name='serviceName'>
		<input type='hidden' value='{{ $Total }}' name='recipientAmount'>
		<input type='hidden' value='{{ $recipientCurrency }}' name='recipientCurrency'>
		<input type='hidden' value='{{ $successUrl }}' name='successUrl'>
		<input type='hidden' value='{{ $failUrl}}' name='failUrl'>
		<input type='hidden' value='' name='userName'>
		<input type='hidden' value='{{ $Email }}' name='user_email'>
		<br>
		<button class="button" type="submit" id="payment_btn" target="_blank">Оплатить</button>
	</form>
</div>
@stop