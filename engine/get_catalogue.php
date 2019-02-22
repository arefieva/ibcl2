<?php
require_once (dir_prefix . 'engine/get_catalogue_generic.php');
require_once(dir_prefix . "engine/get_catalogue_additional.php");
	
$sorts = [
	'available' => ['name' => 'по наличию', 'condition' => 'InStock>0 DESC, CustomOrder'],
	'name' => ['name' => 'по названию &darr;', 'condition' => 'Name'],
	'name_desc' => ['name' => 'по названию &uarr;', 'condition' => 'Name DESC'],
	'price' => ['name' => 'по цене &darr;', 'condition' => 'Price, Price=0'],
	'price_desc' => ['name' => 'по цене &uarr;', 'condition' => 'Price DESC'],
];

$limits = [
	12,
	24,
	36,
	48
];

function getTopCatCategory(){
	global $db, $blade;
	$topcategories = db::select("SELECT * FROM CatCategories WHERE Parent=0 ORDER BY CustomOrder");
	
	foreach ($topcategories as &$cat){
		$cat['Body']="<option value=".$cat['__id'].">".$cat['Name']."</option>";
		
	}

	$ent['categories'] = $topcategories;
	return blade::render('cat.top-cat-category', $ent);
}

function getTopCatParent($id){
	global $db;
	$pg 	= $db->getData("select __id, Parent from CatCategories where __id='" . intval($id) . "'");
	$parent = $pg[0]['Parent'];
	if ($parent != "" && $parent != 0)
		$parent = getTopCatParent($parent);
	else
		$parent = $id;
	return $parent;
}


function getChildCategories($parent = 0){
	global $db;
	unset($r);
	$res = $db->getData("select __id from CatCategories where Parent='" . intval($parent) . "'", true);
	for ($i = 0; $i < count($res); $i++) {
		if ($res[$i]['__id'] > 0) {
			$r[] = $res[$i]['__id'];
			$r = my_array_merge($r, getChildCategories($res[$i]['__id']));
		}
	}
	return $r;
}


function processCatalogue(){
	global $db, $ent, $p, $pg, $cid, $pid, $dir_prefix, $dir, $brandir, $brand_id, $type;
	if (!empty($brandir)) {
		$brand_id = getBrandIDfromVirtualURL($brandir);
	}
	
	if (is_numeric($_GET['pid']) && $_GET['pid'] != "") {
		$res = $db->getData("select DirectoryName from CatItems where __id='" . intval($_GET['pid']) . "'");
		if ($res[0]['DirectoryName'] != "") {
			header("Location: cat/" . $res[0]['DirectoryName'] . ".htm", true, 301);
			die();
		}
	}	
	if (!empty($brand_id)) {
		return displayBrandCatalogue($brand_id, $cid);
	}

	if (!empty($type)) {
		return displayCatItemsType($type, $cid);
	}

	if ($dir != "") {
		$c = getIDFromURL("CatCategories", $dir);
		if (!$c) {
			$c = getIDFromURL("CatItems", $dir);
			if (!$c)
				headerNotFound();
			else
				$pid = $c;
		} else {
			$cid = $c;
		}
	}
	$cid = intval($cid);
	if ($pid > 0)
		return getCatItem($pid);
	else
		return getCatCategory($cid);
}


function getCatCategory($cid){
	if(db::field("SELECT COUNT(__id) as cnt FROM CatCategories WHERE Active=1 AND Parent=".$cid) > 0){
		return displayChildCatCategories($cid);
	}
	return displayCatItems($cid);
}

function displayChildCatCategories($cid, $template = 'cat.categories'){
	global $blade, $p;
	$where 		= "where Active=1 AND Parent=".$cid;
	$categories = db::select("SELECT * FROM CatCategories where Active=1 AND Parent=$cid ORDER BY CustomOrder");

	foreach ($categories as &$category) {
		$category['Link'] = getRealLinkURL('cid:'.$category['__id']);
		$category['Count'] = countCatItems($category['__id']);
		if(!empty($category['Image']))
			$category['Img'] = $category['Image'];
		else
			$category['Img'] = 'images/img/noimage.png';
	}

	$ent['categories'] = $categories;

	if($cid == 0){
		$meta = getPageMeta(route::idbyname('cat'));
		$meta['Body'] = processText(db::field("SELECT Body FROM Pages WHERE __id=".route::idbyname('cat')));
	} else {
		$meta = getCatCategoryMeta($cid);
		$meta['Body'] = processText(db::field("SELECT Body FROM CatCategories WHERE __id=$cid"));
	}
	$ent = array_merge($ent, $meta);


	return $blade->render($template, $ent);
}

function emptyChildCatCategories($cid){
	global $blade, $p;
	$categories = db::select("SELECT * FROM CatCategories where Active=1 AND Parent=$cid ORDER BY CustomOrder");

	if (empty($categories)) return true;
	else return false;
	
}

function getCatItem($pid){
	global $blade;
	$item = db::single("SELECT * FROM CatItems WHERE __id=".$pid." AND Active=1 AND 1c_new=0");
	if(!$item){
		die404();
	}
	$item['gallery'] = db::select("SELECT smallURL, bigURL FROM Images WHERE Category=".$item['Gallery']." ORDER BY CustomOrder");
	if(empty($item['gallery'])){
		$item['gallery'][]['bigURL'] = 'images/img/noimage.png';
	}
	$item['Header'] = $item['Name'];
	$item['Available'] = isAvailable($item['InStock']);
	$item['AvailableString'] = getAvailableString($item['InStock']);
	$item['Body'] = processText($item['Body']);
	$item['Price'] = getPrice($item['Price']);
	$item['PriceOld'] = getPrice($item['PriceOld']);
	$item['Reviews'] = displayReviews($item['__id']);
	$item['SeeAlso'] = displaySeeAlso($item['SeeAlso']);
 	 if(!empty($item['MetTitle']))
    	$item['Title'] = $item['MetTitle'];
  	else
    	$item['Title'] = $item['Name'];
    $item['no_header'] = true;
	if (!empty($item['Parameters']))
			$item['Parameters'] = getCatItemParams($item['Parameters']);
	
	$query	 = "SELECT Body FROM CatCategories WHERE __id=".$item['Category'];
	$cat_info = db::single($query);
	$item['SectionDesc'] = $cat_info['Body'];;
	
	$item['Brandname'] = getBrandName($item['Brand']);
	$di = getSystemVariable("delivery_info");
	$item['delivery_info'] = nl2br($di);
	return $blade->render('cat.details', $item);
}

function getBrandName($brand_id){
	global $db;
	if(empty($brand_id))
		return "No brand";
	else {
		$brand = $db->getData("SELECT Name FROM `Brands` WHERE __id = $brand_id");
		return $brand[0]['Name'];
	}	
}

function displayCatIndex(){
	$groups = [
		'new' => ['name' => 'Новинки', 'condition' => 'New = 1'],
		'special' => ['name' => 'Спецпредложения', 'condition' => 'Special = 1'],
		'hit' => ['name' => 'Хиты продаж', 'condition' => 'Hit = 1']
	];
	$topcategories = db::select("SELECT * FROM CatCategories WHERE Parent=0 ORDER BY CustomOrder");
	foreach ($topcategories as $category){
		unset($c);
		unset($grps);
		foreach ($groups as $key => $group){
			unset($g);
			$child_categories = getChildCategories($category['__id']);
			$query = "SELECT * FROM CatItems WHERE Active=1 AND Category IN (".implode(',', $child_categories).") AND $group[condition] ORDER BY CustomOrder, Name LIMIT 4";
			if($child_categories)
				$g['items'] = processCatListItems(db::select($query));
			if(count($g['items']) < 1)
				continue;
			$g['name'] = $group['name'];
			$g['key'] = $key;
			$grps[] = $g;
		}
		if(count($grps) < 1)
			continue;
		$c['groups'] = $grps;
		$c['Name'] = $category['Name'];
		$c['__id'] = $category['__id'];
		$c['Link'] = getRealLinkURL('cid:'.$category['__id']);
		$c['ImageIndex'] = $category['ImageIndex'];
		$cats[] = $c;
	}

	$ent['categories'] = $cats;
	return blade::render('cat.index', $ent);
}

function displayCatItems($cid){
	global $blade, $p, $limit, $sort, $sorts, $cid, $s, $action;
	$p = $_GET['p'];
	
	$sort = trim($sort);
	$limit = intval($limit) ?: 12;
	$s = trim($s);
	if(array_key_exists($sort, $sorts)){
		$orderby = $sorts[$sort]['condition'];
	}else{
		$orderby = $sorts['available']['condition'];
	}
	
	if(!empty($s)){
		$s = mysql_real_escape_string($s);
		$where.= "AND ( CatItems.Code LIKE '%".$s."%' OR CatItems.Name LIKE '%".$s."%' OR CatItems.BriefList LIKE '%".$s."%')";
	}
	
	if($cid>=0&&$action == 'search'){
		$s = trim(strip_tags($s));
		$os = explode(" ", $s);
		for ($i=0; $i<count($os); $i++) {
			$os[$i] = mysql_real_escape_string($os[$i]);
		}
		$first = ($p-1)*$limit;
		if($first<0) $first = 0;

		$cats = db::select("SELECT CatCategories.__id FROM CatCategories WHERE TopParent=$cid");
		$where = "where Active=1 AND Created=1 AND ((Name like '%".join("%' AND Name like '%", $os)."%') OR (Body like '%".join("%' AND Body like '%", $os)."%'))";

		$categories = getChildCategories($cid);
		$total = db::field("SELECT COUNT(__id) FROM CatItems $where AND Category IN (".implode(',', $categories).")");
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

		$items = db::select("SELECT * FROM CatItems $where AND Category IN (".implode(',', $categories).") ORDER BY $orderby LIMIT ".$first.", ".$limit);

		$ent['navbar_info'] = getNavbarInfo($first, $limit, count($items), $total, $total_pages);
		$ent['navbar'] = generateNavbar($total, $p, $limit, ['cid' => $cid, 'limit' => $limit, 'sort' => $sort, 's' => $s]);
		$ent['sorts'] = displaySorts($sort);
		$ent['limits'] = displayLimits($limit);

	}
	else {
		if ($cid>=0){
			$where = "where CatItems.Active=1 AND CatItems.Created=1 AND CatItems.Category=".$cid;
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

			$items = db::select("SELECT CatItems.*, CatCategories.Name as CategoryName FROM CatItems LEFT JOIN CatCategories ON CatItems.Category=CatCategories.__id $where ORDER BY $orderby LIMIT ".$first.", ".$limit);
			$total = db::field("SELECT COUNT(__id) as cnt FROM CatItems ".$where);
		}
		else {
			$items = db::select("SELECT CatItems.*, CatCategories.Name as CategoryName FROM CatItems LEFT JOIN CatCategories ON CatItems.Category=CatCategories.__id $where ORDER BY $orderby LIMIT ".$first.", ".$limit);
			$total = db::field("SELECT COUNT(__id) as cnt FROM CatItems ".$where);
		}
	}
	
	foreach($items as &$item)
	{
		if ($item['PriceOld']>0 and $item['Price']>0)
			$item['discount'] = (int)((1 - $item['Price']/$item['PriceOld'])*100);
		else 
			$item['discount'] = 0;
	}
	
	
	$ent['items'] = processCatListItems($items);
	if ($action != 'search')
	{
		$ent['navbar_info'] = getNavbarInfo($first, $limit, count($items), $total, $total_pages);

		$ent['navbar'] = generateNavbar($total, $p, $limit, ['cid' => $cid, 'limit' => $limit, 'sort' => $sort, 's' => $s]);
		$ent['sorts'] = displaySorts($sort);
		$ent['limits'] = displayLimits($limit);
	}

	$meta = getCatCategoryMeta($cid);
	$meta['Body'] = processText(db::field("SELECT Body FROM CatCategories WHERE __id=$cid"));
	$ent = array_merge($ent, $meta);
	$ent['cid'] = $cid;
	$ent['p'] = $p;
	$ent['s'] = $s;

	if($action == 'search'){
		$ent['Title'] = 'Результаты поиска';
		$ent['Header'] = 'Поиск';
	}
	return $blade->render('cat.items', $ent);
}

function processCatListItems($items){
	foreach ($items as &$item) {
		$item['Link'] = getRealLinkURL('pid:'.$item['__id']);
		$item['ListImg'] = getFirstGalleryImg($item['Gallery']);
		$item['Price'] = getPrice($item['Price']);
		$item['PriceOld'] = getPrice($item['PriceOld']);
		$item['Available'] = isAvailable($item['InStock']);
		$item['AvailableString'] = getAvailableString($item['InStock']);
		$item['Rating'] = str_replace('.', '', MRound(db::field("SELECT SUM(Rating)/COUNT(*) as rating FROM Reviews WHERE Active=1 AND CatItemID=".$item['__id']), 2));
		if(empty($item['ListImg'])){
			$item['ListImg'] = 'images/img/noimage.png';
		}
		if (!empty($item['Parameters']))
			$item['Parameters'] = getCatItemParams($item['Parameters']);
	}
	return $items;
}


function MRound($num,$parts) {
    $res = $num * $parts;
    $res = round($res);
    return $res /$parts;
}


function getCatCategoryMeta($cid){
	$meta = db::single("select Name, MetaTitle, MetaKeywords, MetaDescription from CatCategories where __id=".$cid);
	$meta['Header'] = $meta['Name'];
	$meta['Title'] = $meta['Header'];
	if(!empty($meta['MetaTitle'])){
		$meta['Title'] = $meta['MetaTitle'];
	}
	return $meta;
}

function getFirstGalleryImg($galleryId){
	return db::field("SELECT smallURL FROM Images WHERE Category=".$galleryId." ORDER BY CustomOrder LIMIT 1");
}

function countCatItems($category){
	$categories = getChildCategories($category);
	if(!$categories){
		return db::field("SELECT COUNT(__id) FROM CatItems WHERE Active=1 AND Category=".$category);
	}
	return db::field("SELECT COUNT(__id) FROM CatItems WHERE Active=1 AND Category IN (".implode(',', $categories).")");
}

function isAvailable($InStock){
	return $InStock > 0 ? true : false;
}

function getAvailableString($InStock){
	return isAvailable($InStock) ? '<span class="green is-available">В наличии</span>' : '<span class="red no-avaliable">Под заказ. Срок поставки от 10 дней</span>';
}

function displaySorts($selected){
	global $sorts;
	foreach ($sorts as $key => $opts) {
		unset($c);
		$c['name'] = $opts['name'];
		$c['value'] = $key;
		if($selected == $key){
			$c['selected'] = 'selected';
		}
		$result[] = $c;
	}
	return $result;
}

function displayLimits($selected){
	global $limits;
	foreach ($limits as $value) {
		unset($c);
		$c['value'] = $value;
		if($selected == $value){
			$c['selected'] = 'selected';
		}
		$result[] = $c;
	}
	return $result;
}


function addWatchedCatItems(){
	global $pid;
	if(!empty($pid) && !in_array($pid, $_SESSION['watched'])){
		$_SESSION['watched'][] = $pid;
	}
}

function displayWatchedCatItems(){
	global $pid;
	$watched = $_SESSION['watched'];
	if( empty($watched) || (count($watched) == 1 && $watched[0] == $pid) ){
		return;
	}
	$items = processCatListItems(db::select("SELECT * FROM CatItems WHERE __id IN (".implode(',', $watched).") AND InStock > 0 ORDER BY RAND() LIMIT 3"));
	if(!$items){
		return;
	}
	return blade::render('cat.watched', ['items' => $items]);
}

function displaySeeAlso($itemsString){
	if(empty($itemsString)){
		return;
	}
	$items = processCatListItems(db::select('SELECT * FROM CatItems WHERE __id IN ('.$itemsString.') AND Active=1 AND InStock > 0'));
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
	return blade::render('cat.seealso', ['seealso' => $items]);	
}

function getCatItemParams($params)
{
	$result = array();

	$arr = explode("\n", $params);

	foreach ($arr as $row) {
		list($name, $val) = array_map("trim", explode(":", $row));
		if (!empty($name) && !empty($val)) {
			$result[] = array(
				'name' => $name,
				'val' => $val
			);
		}
	}

	return $result;
}

/*================================================================================================*/
function loadCatCategories($parent, $where = '1', $redeclareObj = NULL)
{
	if (!isset($parent) || !is_numeric($parent)) {
		return;
	}
	if (empty($where)) {
		$where = "1";
	}
	$parent = intval($parent);
	$lvl = getCatCategoryLevel($parent);

	global $db, $blade;

	switch ($lvl) {
		case 0:
			$query = "
				SELECT " . getCatCategoryDBfields("cat") . "
				FROM `CatCategories` cat
						INNER JOIN `CatCategories` item_cat ON cat.`__id` = item_cat.`TopParent`
						LEFT JOIN `CatItems` ON item_cat.`__id` = `CatItems`.`Category`
				WHERE {$where} AND cat.`Parent` = {$parent} AND cat.`Active` = 1
				GROUP BY cat.`__id`
				ORDER BY cat.`CustomOrder`
			";
			break;

		case 1:
			$query = "
				SELECT " . getCatCategoryDBfields("cat") . "
				FROM `CatCategories` cat
						INNER JOIN `CatCategories` item_cat ON cat.`__id` = item_cat.`SecondParent`
						LEFT JOIN `CatItems` ON item_cat.`__id` = `CatItems`.`Category`
				WHERE {$where} AND cat.`Parent` = {$parent} AND cat.`Active` = 1
				GROUP BY cat.`__id`
				ORDER BY cat.`CustomOrder`
			";
			break;

		default:
			$query = "
				SELECT " . getCatCategoryDBfields("cat") . "
				FROM `CatCategories` cat
						LEFT JOIN `CatItems` ON cat.`__id` = `CatItems`.`Category`
				WHERE {$where} AND cat.`Parent` = $parent
						AND cat.`Active` = 1
				GROUP BY cat.`__id`
				ORDER BY cat.`CustomOrder`
			";
			break;
	}
	
	$cats = $db->getData($query, true);

	if ($cats) {
		foreach ($cats as &$cat) {
			$cat['name'] = "catlist{$cat['__id']}";
			$cat['class'] = "catlist";

			$cat['subcats'] = loadSubCatCategories($cat['__id'], $where, $lvl + 1, $redeclareObj);

		}

		$data['cats'] = prepareCatCategories($cats, $redeclareObj);
	}

	$data['items'] = loadCatItems("Category = {$parent} AND {$where}", "", true);

	if (!empty($data['cats']) || !empty($data['items'])) {
		$result = $blade->render('cat.cats', $data);
	} else {
		$result = '<div class="cat_error">В разделе нет позиций</div>';
	}

	return $result;
}

function loadSubCatCategories($parent, $where = "1", $lvl = 1, $redeclareObj = NULL)
{
	if (!isset($parent) || !is_numeric($parent)) {
		return;
	}
	if (empty($where)) {
		$where = "1";
	}

	global $db;

	switch ($lvl) {
		case 0:
			$query = "loadSubCatCategories: lvl is 0 - it's not sub cat!";
			break;

		case 1:
			$query = "
				SELECT " . getCatCategoryDBfields("cat") . ", COUNT(`CatItems`.`__id`) AS cnt
				FROM `CatCategories` cat
						INNER JOIN `CatCategories` item_cat ON cat.`__id` = item_cat.`SecondParent`
						LEFT JOIN `CatItems` ON item_cat.`__id` = `CatItems`.`Category`
				WHERE {$where} AND cat.`Parent` = '$parent' AND cat.`Active` = 1 AND item_cat.`Active` = 1
				GROUP BY cat.`__id`
				ORDER BY cat.`CustomOrder`
			";
			break;

		default:
			$query = "
				SELECT " . getCatCategoryDBfields("cat") . ", COUNT(`CatItems`.`__id`) AS cnt
				FROM `CatCategories` cat
						LEFT JOIN `CatItems` ON cat.`__id` = `CatItems`.`Category`
				WHERE {$where} AND cat.`Parent` = $parent
						AND cat.`Active` = 1
				GROUP BY cat.`__id`
				ORDER BY cat.`CustomOrder`
			";
			break;
	}
	$cats = $db->getData($query, true);

	if (!$cats) {
		return false;
	}

	return prepareCatCategories($cats, $redeclareObj);
}

function prepareCatCategories($cats, $redeclareObj = NULL)
{
	global $dir_prefix;

	foreach ($cats as $i => &$cat) {
		if (!$cat['cnt']) {
			$cat['cnt'] = getCatCategoryItemsNumber($cat['__id']);
			if (!$cat['cnt']) {
				unset($cats[$i]);
				continue;
			}
		}
		if (!empty($redeclareObj)) {
			$cat['link'] = $redeclareObj['funcURL']($redeclareObj['brand'], $cat);
			$cat['Name'] = $redeclareObj['funcName']($redeclareObj['brand'], $cat);
		} else {
			$cat['link'] = getRealLinkURL("cid:" . $cat['__id']);
		}
		$cat['Image'] = $dir_prefix . ($cat['Image'] ? : "images/img/noimage.png");
	}

	return $cats;
}

//=============================================================================

function loadCatItems($where = "1", $orderby = "CustomOrder", $render = false)
{
	if (empty($where)) {
		$where = "1";
	}
	if (empty($orderby)) {
		$orderby = "CatItems.CustomOrder";
	}

	global $db, $blade, $limit, $sort, $sorts, $s, $p, $brand_id, $cid;
	$p = $_GET['p'];

	$sort = trim($sort);
	if(array_key_exists($sort, $sorts)){
		$orderby = "CatItems.".$sorts[$sort]['condition'];
		if ($sorts[$sort]['condition'] === "InStock>0 DESC, CustomOrder")
			$orderby = "CatItems.InStock>0 DESC, CatItems.CustomOrder";
		else if ($sorts[$sort]['condition'] === "Price, Price=0")
			$orderby = "CatItems.Price, CatItems.Price=0";
	}else{
		$orderby = "CatItems.InStock>0 DESC, CatItems.CustomOrder";
	}
	$limit = intval($limit) ?: 12;
	
	$first = ($p-1)*$limit;
	if($first<0) $first = 0;

	$total = db::field("SELECT COUNT(__id) FROM CatItems WHERE {$where} AND `CatItems`.`Active` = 1");
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

	$query = "
		SELECT " . getCatItemDBfields() . " , `Brands`.`Image` as BrandImg, `Brands`.`Name` as BrandName
		FROM `CatItems`
		LEFT JOIN `Brands` ON `Brands`.`__id` = `CatItems`.`Brand`
		WHERE {$where} AND `CatItems`.`Active` = 1
		ORDER BY $orderby
		LIMIT ".$first.", ".$limit;

	$items = $db->getData($query, true);

	if (!$items) {
		return false;
	}
	
	foreach($items as &$item)
	{
		if ($item['PriceOld']>0 and $item['Price']>0)
			$item['discount'] = (int)((1 - $item['Price']/$item['PriceOld'])*100);
		else 
			$item['discount'] = 0;
	}
	
	foreach ($items as &$item) {
		$item = prepareCatItem($item);
	}

	if ($render) {
		$data['sorts'] = displaySorts($sort);
		$data['limits'] = displayLimits($limit);
		$data['items'] = $items;

		$data['navbar_info'] = getNavbarInfo($first, $limit, count($items), $total, $total_pages);

		$data['navbar'] = generateNavbar($total, $p, $limit, ['cid' => $cid, 'brand_id' => $brand_id, 'limit' => $limit, 'sort' => $sort, 's' => $s]);
		$data['cid'] = $cid;
		$data['p'] = $p;
		$data['s'] = $s;
		$data['brand_id'] = $brand_id;

		return $blade->render('cat.items-brands', $data);/*render('cat/items', $data);*/
	} else {
		return $items;
	}
}

function prepareCatItem($item)
{
	global $dir_prefix;
	$item['dir_prefix'] = $dir_prefix;
	$item['Link'] = getRealLinkURL("pid:" . $item['__id']);
	$item['ListImg'] = getFirstGalleryImg($item['Gallery']);
	$item['Price'] = getPrice($item['Price']);
	$item['PriceOld'] = getPrice($item['PriceOld']);
	$item['Available'] = isAvailable($item['InStock']);
	$item['AvailableString'] = getAvailableString($item['InStock']);
	$item['desc'] = $item['Body'];
	$item['id'] = $item['__id'];
	
	if (!empty($item['Parameters']))
	{
		$item['Parameters'] = getCatItemParams($item['Parameters']);
	}
	$item['Available'] = $item['InStock'] ? 1 : 0;
	return $item;
}

function getPrice($Price){
	return ceil($Price);
}

//=============================================================================

function getCatCategoryDBfields($prefix = '`CatCategories`')
{
	return "{$prefix}.`__id`, {$prefix}.`Parent`, {$prefix}.`Name`, {$prefix}.`Image`";
}

function getCatItemDBfields($prefix = '`CatItems`')
{
	return "{$prefix}.`__id`, {$prefix}.`Name`, {$prefix}.`Code`, {$prefix}.`Price`,{$prefix}.`PriceOld`, {$prefix}.`BriefList`, "
			. "{$prefix}.`Category`, {$prefix}.`New`, {$prefix}.`Special`, {$prefix}.`Active`, {$prefix}.`InStock` ,{$prefix}.`Parameters`, {$prefix}.`Brand`, {$prefix}.`Gallery`";
}
