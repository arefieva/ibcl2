<?php

function displaySlider(){
  $slides = db::select("SELECT * FROM Slider WHERE Active=1 ORDER BY custom_order");
  foreach ($slides as &$slide) {
    $item = db::single('SELECT * FROM CatItems WHERE Active=1 AND __id='.$slide['CatItemID']);
    if(!$item){
      continue;
    }
    $slide['Price'] = $item['Price'];
    $slide['PriceOld'] = $item['PriceOld'];
    $slide['Available'] = isAvailable($item['InStock']);
    $slide['Link'] = getRealLinkURL('pid:'.$item['__id']);
  }
  return blade::render('slider', ['slides' => $slides]);
}

function displayBrandsCarousel(){
	$category = intval(getSystemVariable('brands_category'));
	if(!$category)
		return;
	return blade::render('brands', ['brands' => db::select("SELECT * FROM Images WHERE Category=$category ORDER BY CustomOrder")]);
}


function processPicCategory($what){
  global $blade;
  $cat = str_replace("#", "", $what[0]);
  $display_labels = str_replace("#", "", $what[3]); 
  $photos = db::select("SELECT * FROM Images WHERE Category=".$cat." ORDER BY CustomOrder");
  return $blade->render('gallery', compact('photos', 'display_labels'));
}

function displayHitsIndex(){
	global $cid, $s, $p,$blade;
	$items = db::select("SELECT * FROM CatItems WHERE Active=1 AND Hit = 1 ORDER BY CustomOrder, Name LIMIT 3");
	if(!$items){
		return;
	}
	foreach($items as &$item)
	{
		if ($item['PriceOld']>0 and $item['Price']>0)
			$item['discount'] = (int)((1 - $item['Price']/$item['PriceOld'])*100);
		else 
			$item['discount'] = 0;
		$item['ListImg'] = getFirstGalleryImg($item['Gallery']);
		$item['Link'] = getRealLinkURL('pid:'.$item['__id']);
		if (!empty($item['Parameters']))
			$item['Parameters'] = getCatItemParams($item['Parameters']);
	}
	$items = processCatListItems($items);
	
	return $blade->render('hits-index', ['items' => $items]);
	
}

function displayHits(){
	global $cid, $s, $p, $blade , $limit, $sort, $sorts;
	$p = $_GET['p'];
	
	$sort = trim($sort);
	$limit = intval($limit) ?: 12;
	$where = "WHERE Active=1 AND Hit = 1";
	$total = db::field("SELECT COUNT(__id) as cnt FROM CatItems ".$where);

	$total_pages = floor($total / $limit) + 1;
	if ($total_pages * $limit - $total >= $limit) {
		$total_pages--;
	}
	if($p<1) $p = 1;
	if($total_pages < $p){
		$p = 1;
	}
	$first = ($p-1)*$limit;
	if($first<0) $first = 0;

	$items = db::select("SELECT * FROM CatItems WHERE Active=1 AND Hit = 1 ORDER BY CustomOrder, Name LIMIT ".$first.", ".$limit);
	if(!$items){
		return;
	}
	foreach($items as &$item)
	{
		if ($item['PriceOld']>0 and $item['Price']>0)
			$item['discount'] = (int)((1 - $item['Price']/$item['PriceOld'])*100);
		else 
			$item['discount'] = 0;		
	}

	$items = processCatListItems($items);
	$data['no_header'] = true;
	$data['Header'] = $data['Title'] = 'Популярные товары';
	
	$data['navbar_info'] = getNavbarInfo($first, $limit, count($items), $total, $total_pages);
	$data['navbar'] = generateNavbar($total, $p, $limit, ['cid' => $cid, 'limit' => $limit, 'sort' => $sort, 's' => $s]);
	
	$data['sorts'] = displaySorts($sort);
	$data['limits'] = displayLimits($limit);
	$data['items'] = $items;
	
	return $blade->render('hits.index', $data);
	
}

