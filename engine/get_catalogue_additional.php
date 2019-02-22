<?php

function displayCatItemsType($type, $catID = 0)
{
	if (empty($type) || !isset($catID) || !is_numeric($catID)) {
		return;
	}
	switch ($type) {
		case "news":
			$where .= "`CatItems`.`isNew` = 1";
			$name = "Новинки";
			break;
		case "sale":
			$where .= "`CatItems`.`isSale` = 1";
			$name = "Распродажа";
			break;
		case "actions":
			$where .= "`CatItems`.`isAction` = 1";
			$name = "Акции";
			break;
		default:
			return;
	}

	if ($catID) {
		$cats = getChildCategories($catID);
		$cats[] = $catID;
		$where .= " AND `CatItems`.`Category` IN (" . join(",", $cats) . ")";
	}

	$data['items'] = loadCatItems($where, '', true);
	if (!$data['items']) {
		$data['items'] = '<div class="cat_error">Ничего не найдено по вашему запросу</div>';
	}

	$data['filters'] = getCatalogueFilters($catID);
	global $breadcrumbs, $curr_breadcrumbs, $altTitle, $altHeader;
	$altTitle = $altHeader = $curr_breadcrumbs = $name;
	return render("cat/type", $data);
}

//=============================================================================

function getCatalogueFilters($catID = 0)
{
	$data = array();
	global $brand_id, $type;
	$catID=intval($catID);
	$brand_id = intval($brand_id);
	$type = str_replace("/", "", str_replace(".", "", $type));
	if (!empty($type)) {
		$data['filter_select_' . $type] = "current";
	} elseif (empty($brand_id)) {
		$data['filter_select_all'] = "current";
	}
	$data['filters_all_link'] = getRealLinkURL("cid:{$catID}");
	$data['brands_filter'] = displayBrandFilter($catID, $data['brand_name']);
	$result = render("cat/filters", $data);
	return $result;
}

//=============================================================================

function getCatItemSeeeAlso($grp, $cur_id = 0)
{
	if (empty($grp)) {
		return false;
	}

	$cur_cond = "";
	if (!empty($cur_id)) {
		if (is_array($cur_id)) {
			$cur_cond = " AND `CatItems`.`__id` NOT IN (" . join(",", array_unique(array_map('intval', $cur_id))) . ")";
		} elseif (is_numeric($cur_id)) {
			$cur_cond = " AND `CatItems`.`__id` <> $cur_id";
		}
	}

	$tmp = explode(',', $grp);
	foreach ($tmp as &$id) {
		$id = intval(trim($id));
	}
	$ids = join(',', $tmp);

	$items = loadCatItems("`CatItems`.`__id` IN($ids)" . $cur_cond, "FIND_IN_SET(__id, '$ids')");
	if ($items) {
		return render('cat/items_carousel', array('seealso' => true, 'items' => $items));
	}
}


//=============================================================================

function getCatSections($parentID, $is_item = false, $redeclareObj = NULL)
{
	global $db, $catParents;

	$catParents = array();
	$sections = array();
	$i = 0;

	while ($parentID != 0) {
		$query = "
			SELECT __id, Name, Parent
				FROM CatCategories
				WHERE __id = $parentID
		";
		$pg = $db->getData($query);

		$catParents[] = $parentID;

		$parentID = $pg[0]['Parent'];

		if (empty($redeclareObj)) {
			$sections[$i]['Link'] = getRealLinkURL("cat:" . $pg[0]['__id']);
			$sections[$i]['Name'] = $pg[0]['Name'];
		} else {
			$sections[$i]['Link'] = $redeclareObj['funcURL']($redeclareObj['brand'], $pg[0]);
			$sections[$i]['Name'] = $redeclareObj['funcName']($redeclareObj['brand'], $pg[0]);
		}
		$i++;
	}
	
	if ($sections) {
		return $sections;
	}
}
