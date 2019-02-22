<?php

$payments = [
	'cash' => ['name' => 'Наличными при получении', 'description' => 'Наличными при получении'],
	'cod' =>  ['name' => 'Наложенный платеж', 'description' => 'Наложенный платеж (оплата при получении на почте)'],
	//'intellectmoney' => ['name' => 'Онлайн оплата (IntellectMoney)', 'description' => 'Онлайн оплата или банковский перевод (банковские карты, электронные кошельки - более 12 способов оплаты)']
];


function displayPayments(){
	global $payments;
	foreach ($payments as $key => $value) {
		unset($c);
		$c['value'] = $key;
		$c['name'] = $value['name'];
		$c['description'] = $value['description'];
		$result[] = $c;
	}
	return $result;
}



function doPayment() {
	global $db, $ent, $status, $altTitle;

	if ($status == 'success') {
		$res = '<div class="text-center alert alert-success"><b>Оплата успешно завершена.</b><br><br>
			Мы начали комплектовать Ваш заказ и скоро свяжемся с Вами.<br>
			Благодарим Вас за покупку в нашем магазине!<br></div>';

		$altTitle = 'Оплата успешно завершена';
	} else if ($status == 'fail') {
		$res = '<div class="text-center alert alert-danger">Ошибка завершения оплаты<br></div>'; 

		$altTitle = 'Ошибка завершения оплаты';
	} else {
		global $eshopId, $orderId, $serviceName, $eshopAccount, $recipientAmount, $recipientCurrency,
		$paymentStatus, $userName, $userEmail, $paymentData, $hash;

		$eshopId = $_REQUEST["eshopId"];
		$orderId = $_REQUEST["orderId"];
		$serviceName = $_REQUEST["serviceName"];
		$eshopAccount = $_REQUEST["eshopAccount"];
		$recipientAmount = $_REQUEST["recipientAmount"];
		$recipientCurrency = $_REQUEST["recipientCurrency"];
		$paymentStatus = $_REQUEST["paymentStatus"];
		$userName = $_REQUEST["userName"];
		$userEmail = $_REQUEST["userEmail"];
		$paymentData = $_REQUEST["paymentData"];

		$checksum = 999999;

		$for_hash = $eshopId . "::" .
				$orderId . "::" .
				$serviceName . "::" .
				$eshopAccount . "::" .
				$recipientAmount . "::" .
				$recipientCurrency . "::" .
				$paymentStatus . "::" .
				$userName . "::" .
				$userEmail . "::" .
				$paymentData . "::" .
				getSystemVariable($db, "secretKey");

		// Получаем наш вариант контрольной подписи
		$my_hash = strtolower(md5($for_hash));

		if ($my_hash == $hash) {
			$checksum = 1;
			$orderId = intval($orderId);
			$res = $db->getData("SELECT * FROM Orders WHERE __id = '{$orderId}'");
			if ($res) {

				$paymentStatus = intval($paymentStatus);
				$orderId = intval($orderId);

				if ($paymentStatus == 5) {
					global $globalMailTo, $payments;
					$ent = array_merge($ent, $res[0]);

					$db->query("UPDATE Orders SET paymentStatus = '{$paymentStatus}' WHERE __id = '{$orderId}' ");
					
					$ent['payment'] = $payments['intellectmoney']['name'] . " <b>(заказ успешно оплачен)</b>";

					$ent['qty'] = $res[0]['qty'];
					$ent['Name'] = $res[0]['name'];
					$ent['Date'] = date("d.m.Y H:i", mysqltimestamp2unix($res[0]['Date']));
					$delivery = $res[0]['Delivery'];
					$ent['fullCost'] = $recipientAmount . ' (оплачено в онлайне)';
					$ent['id'] = $orderId;
					
					global $deliveries, $payments, $colors;
					$ent['color'] = $colors[$res[0]['color']]['name'];
					$ent['payment'] = $payments[$res[0]['payment']]['name'];
					$ent['deliveryInfo'] = $deliveries[$res[0]['deliveries']]['name_admin']." (стоимость доставки: ".$res[0]['deliveryPrice']."р.)";
					
					
					$mailFrom = $ent['email'];
					$mailSubject = "ОПЛАЧЕН заказ на сайте " . getSystemVariable($db, "admin_title");
					$mailBodyUser = blade::render('order.emails.payment', $ent);
					doMail($mailFrom, $mailSubject, $mailBodyUser, $globalMailTo, $ContactName);
				} else {
					$db->query("UPDATE Orders SET paymentStatus = '{$paymentStatus}' WHERE __id = '{$orderId}' ");
				}

				echo "OK\n";
			}
		} else {
			$checksum = 0;
			echo "bad sign\n";
		}
		die;
	}
	return blade::render('site.inner', ['Body' => $res, 'Title' => 'Оплата заказа', 'Header' => 'Оплата заказа']);
}



//===============================================================
function displayPayment($OrderID){
	global $dir_prefix, $db;
	$order = db::single("select Total, Email from Orders where __id='".$OrderID."'");
	if(!$order){
		header('Location: '.$dir_prefix.'order/');
	}
	$ent['Header'] = $ent['Title'] = 'Оплата заказа';
	$url = 'http://' . $_SERVER['HTTP_HOST'];
	$ent['eshopId'] = getSystemVariable($db, "shop_id");
	$ent['serviceName'] = "Оплата заказа №{$OrderID}";
	$ent['successUrl'] = $url . '?action=payment&status=success';
	$ent['failUrl'] = $url . '?action=payment&status=fail';
	$ent['recipientCurrency'] = 'RUB';
	$ent['id'] = $OrderID;
	$ent['Total'] = $order['Total'];
	$ent['Email'] = $order['Email'];
	return blade::render('order.payment', $ent);
}

