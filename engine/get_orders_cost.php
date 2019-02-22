<?php

//14+0.0-5@1300|
function getOrderInfo($items, $use_id = false){

  global $db, $dir_prefix;
  unset($result);
  $total                = 0;
  $can_calculate_price  = true;
  $count                = 0;
  $weight = 0;
  $result['shipping']   = 0;
  $discount             = 0;
  
  if(!$use_id){
    $items = explode("|", trim($items));
  }else{
    $items = getOrderItems($items);
  }
  $count = count($items);
  $shipping_cost        = getSystemVariable($db, "shipping_cost");

  $total_shipping_cost  = 0;

  for ($i=0; $i<$count; $i++){
        unset($ci);
        $ptext  = "";
        $item   = $items[$i];
        if(!$use_id){
          list($pos, $actual_price)   = explode("@", $item);
          list($pos, $qty)            = explode("-", $pos);
          list($pos, $params)         = explode("+", $pos);
        }else{
          $pos = $item['id'];
          $qty = $item['qty'];
          $actual_price = $item['Price'];
        }
        $ci['params']               = $params;
        $params                     = explode(".", $params);
        $ci['id']                   = $pos;
        $ci['no']                   = $i+1;
      
        $query = "
            select 
              CatItems.__id, 
              CatItems.Code, 
              CatItems.Price, 
              CatItems.InStock, 
              CatItems.Name, 
              CatItems.Width, 
              CatItems.Height, 
              CatItems.Length, 
              CatItems.Weight, 
              Images.smallURL as smallImage,
              CatCategories.Name as CatName 
            from 
              CatItems 
            left join 
              CatCategories 
            on 
              CatCategories.__id = CatItems.Category 
            left join 
              ( 
                select __id, smallURL, Category, CustomOrder from Images
              ) Images
            on 
              Images.Category = CatItems.Gallery and Images.Category>0 and Images.CustomOrder=(select min(CustomOrder) from Images where Images.Category=CatItems.Gallery)
            where 
              CatItems.__id='".intval($pos)."'
        ";
        $res = $db->getData($query);
        $res = $res[0];


        if($res['CatName']!="") 
            $res['CatName'].=": ";
        
        $ci['name']   = $res['CatName'].trim($res['Name']);
        $ci['Name']   = trim($res['Name']);
        $ci['Link']   = getRealLinkURL('pid:'.$res['__id']);
        $ci['code']   = $res['Code'];
        $ci['instock']   = $res['InStock'];
        $ci['weight'] = $res['Weight'];
        $ci['length'] = $res['Length'];
        $ci['width'] = $res['Width'];
        $ci['height'] = $res['Height'];
        $weight+=$res['Weight'] * $qty;
        if( $res['Code'] != "" ) 
          $ci['name'] .= "<br>Код товара: ".$res['Code'];

        $ci['href']   = getRealLinkURL("pid:".$res['__id']);
        $Currency     = $res['Currency'];

        if($actual_price>0)
          $price      = $actual_price;
        else
          $price      = $res['Price'];

        if($price < 0.02){              // !
          $price = 0;
        }

      if($ptext)
        $ci['parameters']   = $ptext;


      $shipping             = $shipping_cost * $qty;

      $ci['shipping_cost']  = $shipping;

      $total_shipping_cost  += $shipping;
      
      $cost                 = $price * $qty + $shipping;

      $ci['actual_price']   = $price;
      $ci['price']          = format_price($price);
      $ci['qty']            = $qty;
      $ci['cost']           = format_price($cost);
      $ci['photo']          = ($res['smallImage']!="")?("<img src='".$dir_prefix.$res['smallImage']."'>"):"";


      if($ci['name']!="") {
        $total += $cost;
        $cart[] = $ci;
      }
   }

   $result['shipping']          = $total_shipping_cost;
   $result['shipping_formated'] = format_price($total_shipping_cost);

   $result['discount_amount'] = $total/100 * $discount;
   $result['subtotal']        = $total - $result['discount_amount'];
   
   $result['total']           = $result['subtotal'];
   $result['total_rur']       = str_replace(",", ".", format_price($result['total']));
   $result['items']           = $cart;
   $result['count']           = count($cart);
   $result['total_weight'] = floatval($weight);
   if($can_calculate_price){
      $result['cost_info']    = "Общая стоимость: ".format_price($result['total'])." руб.";
      if($total_shipping_cost>0){
        $result['cost_info']    = "Стоимость товаров: ".format_price($result['total'])." <span class='fa fa-ruble'></span>";
        $result['cost_info']    .= "<br>Включая стоимость доставки: ".$result['shipping_formated']." <span class='fa fa-ruble'></span>";
        $result['cost_info']    .= "<br><h3>К оплате: ".format_price($result['total'])." <span class='fa fa-ruble'></span></h3>";
      }

      $result['has_price']    = true;
   } else {
      $result['cost_info']    = "Общая стоимость заказа потребует уточнения";
      $result['has_price']    = false;
   }

   $result['discount']        = $discount;
   return $result;
}


function getOrderItems($orderID){
  $items = db::select('SELECT * FROM OrderItems WHERE OrderID='.$orderID);

  $i = 0;
  foreach ($items as &$item) { 
    unset($product);
    $product = db::select("SELECT * FROM CatItems WHERE __id=".$item['CatitemID']);
    unset($order_item);
    $order_item['qty'] = $item['qty'];
    $order_item['cost'] = $item['qty']*$item['Price'];
    $order_item['id'] = $item['CatitemID'];
    $order_item['i'] = $i;
    $order_item['no'] = ++$i;
    if ($product!=NULL&&$product!=''){
      $order_item['Name'] = $product[0]['Name'];
      $order_item['Width'] = $product[0]['Width'];
      $order_item['Height'] = $product[0]['Height'];
      $order_item['Length'] = $product[0]['Length'];
      $order_item['Weight'] = $product[0]['Weight'];
      $order_item['Price'] = $product[0]['Price'];
    }    
    else
      $order_item['Name'] = 'Позиция удалена из каталога';
    $order_items[] = $order_item;
    
  }
  return $order_items;
}

function getCatItemPrice($item, $user_id = 0, &$discount = 0)
{
	if (empty($item)) {
		return 0;
	}

	$discount = getCatCategoryMarkup($item['Category']);
	if ($discount) {
		$discount /= 100;
		$price = $item['Price'] * $discount;
		$discount = 1 - $discount;
	} else {
		$price = $item['Price'];
	}

	return round($price, 2);
}

function getCatCategoryMarkup($catID)
{
	if (empty($catID) || !is_numeric($catID)) {
		return 0;
	}

	global $cacheCatCategoryMarkup;

	if (!isset($cacheCatCategoryMarkup)) {
		$cacheCatCategoryMarkup = array();
	}

	if (!isset($cacheCatCategoryMarkup[$catID])) {
		global $db;
		$query = "SELECT `Markup`, `Parent` FROM `CatCategories` WHERE __id = {$catID}";
		$cat = $db->getData($query);

		if ($cat[0]['Markup']) {
			$cacheCatCategoryMarkup[$catID] = $cat[0]['Markup'];
		} elseif ($cat[0]['Parent']) {
			$cacheCatCategoryMarkup[$catID] = getCatCategoryMarkup($cat[0]['Parent']);
		} else {
			$cacheCatCategoryMarkup[$catID] = 0;
		}
	}

	return $cacheCatCategoryMarkup[$catID];
}

//======================================================================