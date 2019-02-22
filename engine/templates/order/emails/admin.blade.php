<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
</head>
<body>
Здравствуйте!<br>
<br>
На сайте {{$site_name}} был оформлен новый заказ.<br>

Номер заказа - {{$id}}<br>

Состав заказа:<br>
--------------------------------<br>
@foreach($items as $item)
{{$item['name']}} (арт. {{$item['code']}}) - {{$item['qty']}} шт. @if(!empty($item['price'])) <i>(сумма: {{$item['cost']}} руб.)</i> @else <i><u>под заказ</u></i> @endif<br>
@endforeach
--------------------------------
<br>
Доставка: {{$Delivery}} @if(!empty($DeliveryCost))- {{$DeliveryCost}} р.@endif<br>
Оплата: {{$Payment}}<br><br>

Информация о покупателе:<br>
--------------------------------<br>
<br>
<i>Имя:</i> {{$Name}}<br>
<i>Телефон:</i> {{$Phone}}<br>
<i>Email:</i> {{$Email}}<br>
<i>Регион:</i> {{$Region}}<br>
<i>Город:</i> {{$City}}<br>
<i>Адрес:</i> {{$Address}}<br>
<i>Почтовый индекс:</i> {{$Zip}}<br>
@if(!empty($Comment))
<i>Комментарии к заказу:<br>
------------------------------</i><br>
{{$Comment}}<br>
@endif
<br>
Хорошего дня!<hr>Робот приёма заказов на сайте {{$site_name}}
</body>
</html>