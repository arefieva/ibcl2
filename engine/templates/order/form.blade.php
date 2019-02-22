@extends('site.layout_inner')
@section('js')
<script type="text/javascript" src="{{$dir_prefix}}images/js/order.js"></script>
<script type="text/javascript" src="{{$dir_prefix}}images/js/order_calculate.js"></script>
@stop
@section('content')
@if(count($items) < 1)
<div class="margin-top-40 text-center">
	В вашей корзине нет товаров<br>
	<a href="{{route::link('cat')}}">В каталог</a>
</div>
@else
<div class="order">
	<form method="POST" action="./">
		<table class="order-table">
			<thead>
				<tr>
					<th class="hidden-xs text-center">№</th>
					<th>Наименование</th>
					<th class="hidden-xs">Цена, руб.</th>
					<th>Количество</th>
					<th>Стоимость, руб.</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@foreach($items as $item)
				<tr>
					<td class="hidden-xs text-center">
						{{$item['no']}}.
						<input type="hidden" name="item[]" value="{{$item['id']}}">
					</td>
					<td><a href="{{$item['Link']}}">{{$item['Name']}} @if(!empty($item['Code'])) (Арт. {{$item['Code']}}) @endif</a></td>
					<td class="hidden-xs">{{$item['price']}}</td>
					<td>
	                    @include('partials._qty', ['attributes' => ['name' => 'qty', 'value' => $item['qty']]])<br>
					</td>
					<td>
						{{$item['cost']}}
					</td>
					<td>
						<a onclick="return order.delete({{$item['no']}})" href="javascript:void(0)"><span class="hidden-xs">удалить</span> <span class="visible-xs"><i class="fa fa-remove f-20"></i></span></a>
					</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td class="order-recalculate" colspan="6" align="right">
						<span class="order-recalculate-info">Итого: <span class="order-recalculate-price">{{$total_rur}} руб</span></span>&nbsp;&nbsp;&nbsp;
						<button type="button" class="button button-grey" onclick="return order.update()">Пересчитать</button>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>


	<form id="order-form" class="form-validate" method="POST" action="./">
		<input type="hidden" name="action" value="doProcessOrder">
		<input type="hidden" name="order_price" value="{{$total}}">
		<input id="receiverCityId" type="hidden" name="receiverCityId" value="">
		<div class="margin-top-30">
			<h3>1. Выберите способ доставки:</h3>
		</div>
		<div id="delivery-choose">
			<div class="row margin-top-15">
				<div class="col-sm-4 delivery-choose-var">
					<div class="delivery-choose-group">
						По всей России
					</div>
					<div class="delivery-choose-name">
						<label class="radio-fancy"><input type="radio" name="Delivery" value="cdek" checked'><span></span> СДЭК</label>
					</div>
					<div class="delivery-choose-info">
						<div class="delivery-choose-info-img">
							<img src="{{$dir_prefix}}images/img/delivery-cdek.png">
						</div>
						<div class="delivery-choose-info-text">
							В удобное для Вас время<br>
							Сроки доставки 1-2 дня
						</div>
					</div>
				</div>

				@foreach($deliveries as $group)
				<div class="col-md-3">
					{{$group['name']}}:<br>
					@foreach($group['items'] as $item)
					<label><input data-delivery-name="{{$group['name']}}: {{$item['name']}}" type="radio" name="Delivery" value="{{$item['value']}}" @if($item == reset($deliveries[0]['items'])) checked @endif> {{$item['name']}}</label><br>
					@endforeach
				</div>
				@endforeach
			</div>
			<div class="margin-top-15 text-right">
				<button id="go-next" type="button" class="button">Оформить заказ</button>
			</div>
		</div>
		<div id="delivery-choosen" class="margin-top-15">
			<span id="delivery-choosen-name"></span>
			&nbsp;&nbsp;&nbsp;<a id="delivery-choosen-rechange" href="javascript:void(0)">изменить</a>
		</div>
		<div id="next">
			<div id="step-2" class="margin-top-30">
				<h3>2. Данные для оформления заказа</h3>
				<div class="margin-top-15">
					<input type="text" name="Name" class="input" placeholder="Фамилия, имя, отчество ">
				</div>
				<div class="margin-top-15">
					<input type="text" name="Phone" class="input" placeholder="Телефон ">
				</div>
				<div class="margin-top-15">
					<input type="text" name="Email" class="input" placeholder="Email ">
				</div>
				<div class="margin-top-15">
					<input type="text" name="Region" class="input" placeholder="Регион ">
				</div>
				<div class="margin-top-15">
					<input id="city" type="text" name="City" class="input" placeholder="Город / населенный пункт ">
				</div>
				<div class="margin-top-15">
					<textarea class="input" name="Address" placeholder="Адрес (улица, дом, квартира) " cols="2"></textarea>
				</div>
				<div class="margin-top-15">
					<input id="zip_error" name="Zip_error" type="hidden" value="1">
					<input id="zip" type="text" name="Zip" class="input" placeholder="Почтовый индекс ">
				</div>
				<div class="margin-top-15">
					<textarea name="Comment" class="input" placeholder="Ваши комментарии и особые пожелания к заказу" cols="2"></textarea>
				</div>
				
			</div>
			<div id="step-3" class="margin-top-30">
				<h3>3. Способ оплаты</h3>
				<div class="margin-top-15">
					@foreach($payments as $payment)
					<div class="payment-item">
						<label><input type="radio" name="Payment" value="{{$payment['value']}}" @if($payment == reset($payments)) checked @endif> {{$payment['description']}}</label><br>
					</div>
					@endforeach
				</div>
			</div>
			<div id="step-4" class="margin-top-30">
				<h3>4. Сумма к оплате</h3>
				<div class="margin-top-15">
					Стоимость заказа: <b id="order-price">{{$total}} руб.</b><br>
					Стоимость доставки: <b id="order-delivery-price">Уточните у менеджера</b><br>
					Итого: <b id="order-total-price">{{$total}} руб.</b>
				</div>
				<div class="margin-top-15">
					<button type="submit" class="button">Подтвердить заказ</button>
				</div>
			</div>
		</div>
	</form>
</div>
@endif
@stop