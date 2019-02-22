<?php

function displayCatLeftMenu($cur = 0)
{
	if (!isset($cur) || !is_numeric($cur)) {
		return false;
	}

	return _getCatLeftMenu($cur);
}

function _getCatLeftMenu($cur = 0, $parent = 0, $lvl = 0)
{
	if (!isset($cur) || !is_numeric($cur) || !isset($parent) || !is_numeric($parent)) {
		return;
	}

	global $db, $catParents;

	switch ($lvl) {
		case 0: // пока так, т.к. не требуется количество для категорий верхнего уровня
		default:
			$query = "
				SELECT __id, Name
				FROM CatCategories
				WHERE Parent = $parent AND Active = 1
				ORDER BY CustomOrder
			";
			break;

		case 1:
			$query = "
				SELECT cat.`__id`, cat.`Name`, COUNT(`CatItems`.`__id`) AS cnt
				FROM `CatCategories` cat
						INNER JOIN `CatCategories` item_cat ON cat.`__id` = item_cat.`SecondParent`
						LEFT JOIN `CatItems` ON item_cat.`__id` = `CatItems`.`Category`
				WHERE cat.`Parent` = $parent AND cat.`Active` = 1 AND item_cat.`Active` = 1 AND `CatItems`.`Active` = 1
				GROUP BY cat.`__id`
				ORDER BY cat.`CustomOrder`
			";
			break;

		default:
			$query = "
				SELECT cat.`__id`, cat.`Name`, COUNT(`CatItems`.`__id`) AS cnt
				FROM `CatCategories` cat
						LEFT JOIN `CatItems` ON cat.`__id` = `CatItems`.`Category`
				WHERE cat.`Parent` = $parent
						AND cat.`Active` = 1 AND (`CatItems`.`Active` = 1 OR `CatItems`.`Active` IS NULL)
				GROUP BY cat.`__id`
				ORDER BY cat.`CustomOrder`
			";
			break;
	}
	
	
	$cats = $db->getData($query);

	if (!$cats) {
		return "";
	}

	foreach ($cats as $i => &$ct) {
		if (!$ct['cnt']) {
			$ct['cnt'] = getCatCategoryItemsNumber($ct['__id']);
			if (!$ct['cnt']) {
				unset($cats[$i]);
				continue;
			}
		}
		$ct['name'] = $ct['Name'];
		$ct['title'] = strip_tags($ct['name']);
		$in_array = in_array($ct['__id'], $catParents);

		if ($in_array) {
			$ct['submenu'] = _getCatLeftMenu($cur, $ct['__id'], $lvl + 1);
		}
		$ct['link'] = getRealLinkURL("cid:" . $ct['__id']);
		if ($ct['__id'] == $cur) {
			$ct['class'] = " class='current'";
		} elseif ($in_array) {
			$ct['class'] = " class='cparent'";
		}
	}

	return render("leftmenu", array("pages" => $cats));
}

//=============================================================================

function getCatCategoryItemsNumber($catID)
{
	if (empty($catID) || !is_numeric($catID)) {
		return;
	}
	global $db;
	$query = "
		SELECT COUNT(`__id`) cnt
		FROM `CatItems`
		WHERE `Category` IN (" . join(",", my_array_merge(array($catID), getChildCategories($catID))) . ")
	";
	$cnt = $db->getData($query);
	return $cnt[0]['cnt'];
}
