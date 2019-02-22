<?php

function doProcessOrder(){
	global $db, $ent, $cookie_cartID, $HTTP_HOST, $Date, $payments, $receiverCityId,
		   $Name, $Zip, $Region, $City, $Country, $Address, $Phone, $Payment, $Delivery, $DeliveryCost,
		   $UserID, $Email, $Total, $ui, $Comment, $Status, $payments, $deliveries, $dir_prefix, $globalMailTo;
	unset($pg);
	$pg['Header'] = $pg['Title'] = $pg['PlainHeader'] = "Оформление заказа";
	$error = false;
	$Name 		= trim($Name);
	$Email 		= trim($Email);
	$Phone 		= trim($Phone);
	$Region 	= trim($Region);
	$City 		= trim($City);
	$Address	= trim($Address);
	$Comment 	= trim($Comment);
	$Delivery 	= trim($Delivery);
	$Payment 	= trim($Payment);
	$order_info = getOrderInfo($db->field("select Items from Cart where __id='".mysql_real_escape_string($cookie_cartID)."'"), $ui['UserID']);
	$its 		= $order_info['items'];
	if(!$its){
		header("Location: ".$dir_prefix."order/");
	}
	foreach ($its as $it) {
		if($it['instock'] < $it['qty']){
			$it['qty'] = $it['instock'];
		}
	}
	if(!in_array($Payment, array_keys($payments)) || !in_array($Delivery, array_keys($deliveries))){
		return blade::render("order.error", $pg);
	}
	if($Delivery == 'cdek' && !validateFormInput('Name, Phone, Email, Region, City, Address, Payment')){
		return blade::render("order.error", $pg);
	}
	if($Delivery == 'post' && !validateFormInput('Name, Phone, Email, Region, City, Address, Zip, Payment')){
		return blade::render("order.error", $pg);
	}
	if($Delivery == 'courier' && !validateFormInput('Name, Phone, Email')){
		return blade::render("order.error", $pg);
	}
	$ent 			= my_array_merge($ent, populateFromPost());
	$dt 			= time();
	$Date 			= timestr();
	$Total 			= $order_info['total'];
	$DeliveryCost	= getDeliveryCost(compact('Zip', 'City', 'Delivery', 'receiverCityId'), $order_info);
	if($Total != 0){
		startTransaction();
		$id = $db->insert("Orders");
		foreach ($its as $it) {
			db::query("INSERT INTO OrderItems SET OrderID=".$id.", CatItemID=".$it['id'].", qty=".$it['qty'].", Price=".$it['actual_price']);
		}
		commit();
	}
	// send email to admin
	$ent['id'] 			= $id;
	$ent['items'] 		= $order_info['items'];
	$ent['subtotal'] 	= $order_info['subtotal'];
	$ent['shipping'] 	= $order_info['shipping'];
	$ent['total_rur'] 	= $order_info['total_rur'];
	$ent['total'] 		= $order_info['total'];
	$ent['DateStr'] 	= $DateStr;
	$ent['Name'] 		= $Name;
	$ent['Delivery'] 	= $deliveries[$Delivery]['name'];
	$ent['DeliveryCost'] = $DeliveryCost;
	$ent['Payment'] 	= $payments[$Payment]['name'];
	$mailFrom 			= $Email;
	$mailSubject 		= "Заказ на сайте ".getSystemVariable($db, "admin_title");
	$mailBody 			= blade::render('order.emails.admin', $ent);
	doMail($globalMailTo, $mailSubject, $mailBody, $mailFrom, $ContactName);
	// Письмо покупателю 
	$ent['DateStr']	= fmtDate(timeStr());
	$mailFrom 		= $globalMailTo;
	$mailFromName 	= getSystemVariable($db, "site_name");
	$mailTo 		= $Email;
	$mailSubject 	= getSystemVariable($db, "site_name").' Заказ №'.$id.' успешно оформлен.';
	$mailBody 		= blade::render('order.emails.customer', $ent);
	doMail($mailTo, $mailSubject, $mailBody, $mailFrom, $mailFromName);
	deleteCart();
	if($Payment == 'intellectmoney'){
		return displayPayment($id);
	}
	return blade::render('order.done', $pg);
}



function displayOrder(){
	global $db, $ent, $a1, $cookie_cartID, $cookie_Name, $cookie_CompanyName, $cookie_zip, $cookie_City, $cookie_Country, $cookie_Address, $cookie_Phone, $cookie_Email, $cookie_Comments, $cookie_uname, $ui, $act1, $blade, $payments;
	$ent['Header'] = $ent['Title'] = "Корзина заказа";
	unset($cart);
	if($cookie_cartID!=""){
		$res = mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cookie_cartID)."'"));
		$order_info = getOrderInfo($res['Items']);
		$ent['items'] = $order_info['items'];
		$ent['cost_info'] = $order_info['cost_info'];
		$ent['total_rur'] = $order_info['total_rur'];
		$ent['total'] = $order_info['total'];
	}
	$ent['payments'] = displayPayments();
	list($ent['a'], $ent['b'], $ent['ans']) = get_question();
	return $blade->render('order.form', $ent);
}

function displayOrder2(){
	global $db, $ent, $a1, $cookie_cartID, $cookie_Name, $cookie_CompanyName, $cookie_zip, $cookie_City, $cookie_Country, $cookie_Address, $cookie_Phone, $cookie_Email, $cookie_Comments, $cookie_uname, $ui, $act1, $blade, $payments;
	$ent['Header'] = $ent['Title'] = "Корзина заказа";
	unset($cart);
	if($cookie_cartID!=""){
		$res = mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cookie_cartID)."'"));
		$order_info = getOrderInfo($res['Items']);
		$ent['items'] = $order_info['items'];
		$ent['cost_info'] = $order_info['cost_info'];
		$ent['total_rur'] = $order_info['total_rur'];
		$ent['total'] = $order_info['total'];
		$ent['photo'] = $order_info['photo'];
	}	
	$ent['payments'] = displayPayments();
	list($ent['a'], $ent['b'], $ent['ans']) = get_question();
	return $blade->render('order.form-dropdown-cart', $ent);
}


function displayFormPreorder(){
	return blade::render('preorder.form');
}

function doProcessPreorder(){
	global $Name, $Email, $Phone, $City, $pid, $CatItemID, $DateTime;
	$CatItemID = intval($pid);
	$Email = trim($Email);
	$Phone = trim($Phone);
	$City = trim($City);
	$Name = trim($Name);
	if(empty($CatItemID) || empty($Name) || empty($Email)){
		$response['message'] = '<div class="alert alert-danger">Не заполнены все необходимые поля</div>';
		$response['back_text'] = 'Назад';
		$response['back_link'] = "$.fancybox.open('#preorder')";
		return json_encode(['html' => blade::render('forms.response', $response)]);
	}
	$DateTime = date('Y-m-d H:i:s');
	db::insert('Preorders');
	$response['message'] = '<div class="alert alert-success">Предзаказ успешно оформлен</div>';
	$response['back_text'] = 'Закрыть';
	$response['back_link'] = "$.fancybox.close()";
	return json_encode(['html' => blade::render('forms.response', $response), 'success' => 1]);
}

