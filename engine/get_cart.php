<?php

//===============================================================
function getCountItemsInCart(){
	global $cookie_cartID, $db, $ui;
	$res 		= $db->getData("select Items from Cart where __id='".mysql_real_escape_string($cookie_cartID)."'");
    $order_info = getOrderInfo($res[0]['Items'], $ui['UserID']);
	return ['count' => $order_info['count'], 'total' => $order_info['total_rur'], 'units' => pluralForm($order_info['count'], 'товар', 'товара', 'товаров')];
}
//===============================================================
function doUpdateCart(){
	global $db, $qty, $item, $cookie_cartID;
	$cartID = $cookie_cartID;
	for($i = 0; $i<count($item); $i++) {
		$instock = db::field("SELECT InStock FROM CatItems WHERE __id=".$item[$i]);
		if($instock < $qty[$i]){
			$qty[$i] = $instock;
		}
	}
	$res 	= mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cartID)."'"));
	$items 	= explode("|", $res['Items']);

	for ($i=0; $i<count($items); $i++){
		list($pos, $q) 	= explode("-", $items[$i]);
		if($qty[$i]>0)
			$items_new[$i] 		= $pos."-".$qty[$i];
	}
	doSaveCart($cartID, $items_new);
}

//===============================================================
function doEraseCart($cartID){
	global $db;
	unset($items);
	doSaveCart($cartID, $items);
}

function deleteCart(){
	global $cookie_cartID, $HTTP_HOST, $db;
	my_setCookie("cookie_cartID", 0, time() - 1, "/", $HTTP_HOST);
	$db->query("delete from Cart where __id='".intval($cookie_cartID)."'");
}


//===============================================================
function doDeleteFromCart($item){
  global $db, $cookie_cartID;
  $cartID = $cookie_cartID;
  $res = mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cartID)."'"));
  $items = explode("|", $res['Items']);
  foreach ($item as $posID) {
  	unset($items[$posID-1]);
  }
  doSaveCart($cartID, $items);
}

//===============================================================
function doAddToCart($prodID, $qty, $multiple){
	global $db, $Items, $params_compiled, $params, $__ctime, $HTTP_HOST, $cookie_cartID;
	$cartID = $cookie_cartID;
	$Items = "";
	if($prodID >= 1 && $qty >= 1) {
		unset($items);
		$__ctime 		= timeStr();
		$newcart 		= false;
		if( $cartID < 1 )
			$newcart 	= true;
		else {
			$res 		= mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cartID)."'"));
			if(!$res) 
				$newcart= true;
			else 
				$items 	= explode("|", $res['Items']);
		}

		// add to existing item, if available
		$added_to_existing = false;
		for ($i=0; $i<count($items); $i++) {
			$it = $items[$i];
			list($pid, $pq) = explode("-", $it);
			if($pid == $prodID && !$added_to_existing && $prodID!="") {
				$added_to_existing = true;
				$items[$i] = $pid."-".($qty+$pq);
			}
		}
		if(!$added_to_existing) 
			$items[] = $prodID."-".$qty;

		if($newcart) {
			$cookie_cartID = $db->insert("Cart");
			setcookie("cookie_cartID", $cookie_cartID, time()+30*24*3600, "/", $HTTP_HOST); // expires in 30 days
		}
		doSaveCart($cookie_cartID, $items);
	}
}
//===============================================================
function doSaveCart($cartID, $its){
	global $db, $Items, $__ctime;
	
	$__ctime 	= timeStr();
	$Items 		= join("|", $its);
	
	$Items		= ltrim($Items, "|");
	$Items		= rtrim($Items, "|");
	
	if($Items=="")
		$Items = NULL;
	
	$query = "update Cart set __ctime='".$__ctime."', Items='".mysql_real_escape_string(trim($Items))."' where __id='".mysql_real_escape_string($cartID)."'";
	$db->query($query); 
}
//===============================================================
function displayCart(){
    global $db, $ent, $action, $blade, $multiple, $cookie_cartID, $pid, $qty, $referer, $id, $HTTP_REFERER, $ui;
    $db->query("delete from Cart where __ctime<'".timeStr(time()-30*24*60*60)."'");
    if($cookie_cartID!=""){
		$res = mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cookie_cartID)."'"));
		$order_info = getOrderInfo($res['Items'], $ui['UserID']);
		$count 		= $order_info['count'];
		$total 		= $order_info['subtotal'];
		$ent['price_total'] = format_price($total);
		$ent['cart'] 		= $order_info['items'];
		$ent['cost_info'] 	= $order_info['cost_info'];
		$ent['total'] 		= $order_info['count'];
    }
	$ent['phone'] = getSystemVariable($db, 'footer_phone');
    return $blade->render('order.cart', $ent);
}

function getCart(){
	global $db, $ent, $cookie_cartID, $pid, $qty, $id, $ui;

    if($cookie_cartID!=""){
		$res = mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cookie_cartID)."'"));
		$order_info = getOrderInfo($res['Items']);
		$ent['items'] = $order_info['items'];
		$ent['photo'] = $order_info['photo'];
		$ent['cost_info'] = $order_info['cost_info'];
    }
    return $ent['items'];
}