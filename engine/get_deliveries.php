<?php

$deliveries = [
  'courier' => [
                'name_admin' => 'Курьером',
               ],
  'cdek' =>    [
                'name_admin' => 'СДЭК',
               ],
  'post' =>    [
                'name_admin' => 'Почта России',
               ]
];


function getDeliveryCost($order, $order_info){
  switch ($order['Delivery']) {
    case 'courier':
      return getCourierCalc();    
    case 'cdek':
      try{
        $result = calculateCdek($order_info, $order['receiverCityId']);
      }catch(Exception $e){
        $result = 0;
      } 
      return $result;
    case 'post':
      return getPostCalc($order['Zip'], $order['City']);
  }
}



function getCdekCalc() {
  global $db, $cookie_cartID, $receiverCityId;
  $res = mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cookie_cartID)."'"));
  $order_info = getOrderInfo($res['Items'], $ui['UserID']);
  try{
    return calculateCdek($order_info, intval($receiverCityId));
  }
  catch(Exception $e){
    if(is_ajax())
     return json_encode(null);
    return 0;
  }
}

//===============================================================

function calculateCdek($order_info, $receiverCityId) {
  global $db;
  $weight = $order_info['total_weight'];
  $calc = new CalculatePriceDeliveryCdek();  
  $calc->setSenderCityId(44); //устанавливаем город-отправитель
  $calc->setReceiverCityId($receiverCityId); //устанавливаем город-получатель
  $total_volume = array_reduce($order_info['items'], function($carry = 0, $item){
    return $carry += $item['length']*$item['width']*$item['height']/5000;
  });
  $weight = $total_volume > $order_info['total_weight'] ? $total_volume : $order_info['total_weight'];
  if($weight > 30){
    $tariff = 16;
  }else{
    $tariff = 11;
  }
  $calc->setTariffId($tariff); //устанавливаем тариф по-умолчанию
  $calc->setModeDeliveryId(3); //устанавливаем режим доставки
  foreach($order_info['items'] as $item) {
   $calc->addGoodsItemBySize($item['weight'], $item['length'], $item['width'], $item['height']);
  }
  $calc->calculate();
  $res = $calc->getResult();
  $result = $res['result'];
  return $result['price'];
}


function getPostCalc($zip, $city) {
  global $db, $ent, $cookie_cartID;
  $calc = array();
  $msg = 0;
  $addPrice = getSystemVariable($db, 'pickup_price');
  if(strlen(trim($zip)) != 6) 
    return false;
  $zip = mysql_real_escape_string(trim($zip));
  if(!$order_info) {
    $res = mysql_fetch_array($db->query("select * from Cart where __id='".mysql_real_escape_string($cookie_cartID)."'"));
    $Items = $res['Items'];
    $order_info = getOrderInfo($Items, $ui['UserID']);
  }
  $res_calc = russianpostcalc_api_calc("101000", $zip, $order_info['total_weight'], $order_info['total']);
   if($res_calc['Status'] != 'OK'){
     if($res_calc['msg']['text'] != "")
       $error = $res_calc['msg']['text'];
     else{
       $error = $res_calc['Message'];
     }
     return false;
  }
  $vavaluable_parcel_cost = intval($res_calc['Отправления']['ЦеннаяПосылка']['Доставка']);
  $vavaluable_parcel_avia_cost = intval($res_calc['Отправления']['ЦеннаяАвиаПосылка']['Доставка']);
  if($vavaluable_parcel_cost != 0){
    $cost = $vavaluable_parcel_cost;
  }else{
    $cost = $vavaluable_parcel_avia_cost;
  }
  $cost += $addPrice;   

  $msg = 1;
  $calc['cost'] = $cost;
  if(is_ajax()) {
    return json_encode($cost);
  } else {
    return $cost;
  }
}


function russianpostcalc_api_calc($from_index, $to_index, $weight, $ob_cennost_rub) {
  $request = array(
    "f" => $from_index,
    "t" => $to_index,
    "w" => $weight*1000,
    "v" => $ob_cennost_rub,
    "o" => "php"
  );
  $Response = file_get_contents("http://www.postcalc.ru/api.php?".http_build_query($request));
  if ( substr($Response,0,3) == "\x1f\x8b\x08" )  $Response = gzinflate(substr($Response,10,-8));

  return unserialize($Response);
}


function getCourierCalc(){
  if(is_ajax())
    return json_encode(['cost' => getSystemVariable($db, "courierCost")*1]);
  return getSystemVariable($db, "courierCost")*1;
}