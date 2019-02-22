<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
</head>
<body>
Здравствуйте!<br>

Сообщаем Вам, что Ваш заказ (номер {{$id}} от {{$DateStr}}) поступил в обработку.<br>
<br>

<table width="100%" border="1" cellspacing="0" cellpadding="2">
	<tr>
		<td height="30" align="right">
			<strong>№</strong>
		</td>
		<td height="30" >
			<strong>Наименование</strong>
		</td>
		<td nowrap >
			<strong>Цена, р.</strong>
		</td>
		<td>
			<strong>Количество</strong>
		</td>
		<td nowrap>
			<strong>Стоимость, р. &nbsp;</strong>
		</td>
	</tr>
	@foreach($items as $item)
	<tr valign="top">
		<td width="1%" align="right" nowrap >
			<b>{{$item['no']}}.</b>
		</td>
		<td valign="top" >
			{{$item['name']}}
		</td>
		<td width="1%" valign="top" nowrap >
			{{$item['price']}}
		</td>
		<td width="1%" valign="top" nowrap >
			{{$item['qty']}} {{$item['units']}}
		</td>
		<td width="1%" valign="top" nowrap >
			{{$item['cost']}}
		</td>
	</tr>
	@endforeach
	<tr>
		<td height="30" colspan="5" align="right" nowrap>
			<strong>Стоимость заказа: {{$total}} р.</strong>
		</td>
	</tr>
	<tr>
		<td height="30" colspan="5" align="right" nowrap>
			<strong>Стоимость доставки: {{$DeliveryCost}} р.</strong>
		</td>
	</tr>
	<tr>
		<td height="30" colspan="5" align="right" nowrap>
			<strong>Общая стоимость: {{$total + $DeliveryCost}} р.</strong>
		</td>
	</tr>
</table>

Имя: {{$Name}}<br>
Телефон: {{$Phone}}<br>
Email: {{$Email}}<br>
<br>
<br>
Мы свяжемся с Вами в ближайшее время - благодарим Вас за интерес к нашей продукции!
<hr>
С уважением,<br>
администрация сайта {{$site_name}}
</body>
</html>