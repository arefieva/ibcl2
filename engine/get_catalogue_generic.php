<?php

function getCatCategoryLevel($catID = 0)
{
	if (empty($catID) || !is_numeric($catID)) {
		return 0;
	}

	global $db;
	$cat = mysql_fetch_array($db->query("SELECT `Parent`, `TopParent`, `SecondParent` FROM `CatCategories` WHERE `__id` = {$catID}"));

	if ($cat['Parent'] == 0) {
		return 1;
	}

	if ($cat['Parent'] == $cat['TopParent']) {
		return 2;
	}

	if ($cat['Parent'] == $cat['SecondParent']) {
		return 3;
	}

	return 4;
}

//=============================================================================

function redirectCatToHRUIfNecessary($cat_dir) {
	global $dir, $p;
	if($cat_dir!=""&&$dir==""&&intval($p)<=1) {
		header("Location: /cat/".$cat_dir.".htm", true, 301);
		die;
	}
}

//=============================================================================

function getCatCategoryInfo($catID)
{
	if (empty($catID) || !is_numeric($catID)) {
		return;
	}

	global $cacheCatCategoryInfo;

	if (!isset($cacheCatCategoryInfo)) {
		$cacheCatCategoryInfo = array();
	}

	if (!isset($cacheCatCategoryInfo[$catID])) {
		global $db;

		$query = "
			SELECT *
			FROM `CatCategories`
			WHERE `__id` = {$catID}
		";
		$cacheCatCategoryInfo[$catID] = mysql_fetch_array($db->query($query));
	}

	return $cacheCatCategoryInfo[$catID];
}

function getCatCategoryName($catID)
{
	if (empty($catID) || !is_numeric($catID)) {
		return;
	}

	$info = getCatCategoryInfo($catID);

	if ($info) {
		return $info['Name'];
	}
}

//=============================================================================
