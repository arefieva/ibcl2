<?php

function displayBrandCatalogue($brandID, $catID = 0)
{
	if (empty($brandID) || !is_numeric($brandID) || !isset($catID) || !is_numeric($catID)) {
		return;
	}
	global $ent, $dir_prefix;
	$brand = getBrandInfo($brandID);
	global $altHeader, $altTitle, $MetaTitle, $MetaKeywords, $MetaDescription, $blade;
	if ($catID) {
		$catName = getCatCategoryName($catID);
		global $brandName;
		$brandName = $brand['Name'];
		
		$MetaTitle = $MetaKeywords = $MetaDescription = $altTitle = $altHeader = $curr_breadcrumbs = $catName." (производитель: ".$brandName.")";
	} else {
		$altTitle = $altHeader  = $brand['Name'];
		$MetaTitle = $MetaKeywords = $MetaDescription = $brand['Name'];
		$ent['image'] = $brand['image'];
		if (trim($brand['SmallDesc'])) {
			$ent['desc'] = $brand['SmallDesc'] . ' &emsp; <a href="#body" class=more><span>подробнее</span></a>';
		}
		if ($brand['Image2']) {
			$ent['image2'] = '<img class="brand_img" src="' . $dir_prefix . $brand['Image2'] . '" alt="">';
		} else {
			$ent['image2'] = '';
		}
		$ent['body'] = processText($brand['Body'], 1);
	}
	
	$ent['Header'] = $brand['Name'];
	$ent['Title'] = $brand['Name'];
	$item['no_header'] = true;
	$ent['brand_name'] = $brand['Name'];
	$ent['cats'] = loadCatCategories($catID, "`CatItems`.`Brand` = {$brandID}", getBrandsRedeclareObj($brand));

	return  $blade->render('brands.cat', $ent);
}

function getBrandCatSections($brand)
{
	if (empty($brand) || empty($brand['__id'])) {
		return;
	}

	return array(array(
			'title' => $brand['Name'],
			'href' => getRealLinkURL("brand:" . $brand['__id'])
	));
}

function prepareBrand($brand, $catID = 0)
{
	global $dir_prefix;
	if ($catID) {
		$brand['href'] = brandCatSectionsURL($brand, getCatCategoryInfo($catID));
	} else {
		$brand['href'] = getRealLinkURL("brand:{$brand['__id']}");
	}
	$brand['image'] = $dir_prefix . ($brand['Image'] ? : "images/dot.png");

	return $brand;
}

//=============================================================================

function displayBrandFilter($catID = 0, &$brandName = '')
{

	global $ent, $db, $brand_id;

	if (!$catID) {
		$query = "
			SELECT " . getBrandDBfields() . "
			FROM `Brands`
			ORDER BY `CustomOrder`
		";
	} else {
		$cats = getChildCategories($catID);
		$cats[] = $catID;

		$query = "
			SELECT " . getBrandDBfields() . "
			FROM `Brands`
					INNER JOIN `CatItems` ON `Brands`.`__id` = `CatItems`.`Brand`
			WHERE `CatItems`.`Category` IN (" . join(",", $cats) . ")
			GROUP BY `Brands`.`__id`
			ORDER BY `Brands`.`CustomOrder`
		";
	}
	$brands = $db->getData($query);

	foreach ($brands as &$brand) {
		$brand = prepareBrand($brand, $catID);
		if ($brand['__id'] == $brand_id) {
			$brand['current'] = " current";
			$brandName = $brand['Name'];
		}
	}

	if (!$brand_id || empty($brandName)) {
		$brandName = "По брендам";
	}

	$ent['brands'] = $brands;
	$ent['letters'] = getBrandFilterLetters($brands);

	return parseTemplateFromFile(TPLS_DIR . 'brands/filter.htm', $ent);
}

function getBrandFilterLetters($brands)
{
	$letters = array();
	foreach ($brands as $brand) {
		if (!in_array($brand['Letter'], $letters)) {
			$letters[] = $brand['Letter'];
		}
	}
	sort($letters);
	$result = array();
	foreach ($letters as $letter) {
		$result[] = array('letter' => $letter);
	}
	return $result;
}

//=============================================================================

function displayBrandList()
{
	global $ent, $db, $dir_prefix;
	$filename = dir_prefix . "tmp/dbl";
	include($filename.".php");
	if ($last_modified > last_update) {
		$result = str_replace("#dp#", $dir_prefix, join("", file($filename.".htm")));
		return $result;
	}
	
	$query = "
		SELECT " . getBrandDBfields() . "
		FROM `Brands`
		ORDER BY `CustomOrder`
	";
	$brands = $db->getData($query);

	foreach ($brands as &$brand) {
		$brand = prepareBrand($brand);
	}

	$ent['brands'] = $brands;

	$result = parseTemplateFromFile(dir_prefix."engine/templates/brands/list.htm", $ent);/*TPLS_DIR*/

	$fl = fopen($filename.".htm", "w");
	$result1 = str_replace($dir_prefix, "#dp#", $result);
	fwrite($fl, $result1);
	fclose($fl);
	
	$phpStr = "<?
$" . "last_modified = " . time() . ";
?>";
	$fl = fopen($filename.".php", "w");
	fwrite($fl, $phpStr);
	fclose($fl);

	return $result;
}

//=============================================================================

function getBrandInfo($brandID)
{
	if (empty($brandID) || !is_numeric($brandID)) {
		return;
	}
	global $cacheBrandInfo;

	if (!isset($cacheBrandInfo)) {
		$cacheBrandInfo = array();
	}

	if (!isset($cacheBrandInfo[$brandID])) {
		global $db;

		$query = "SELECT * FROM `Brands` WHERE `__id` = {$brandID}";
		$cacheBrandInfo[$brandID] = prepareBrand(mysql_fetch_array($db->query($query)));
	}

	return $cacheBrandInfo[$brandID];
}

function getBrandDBfields($prefix = '`Brands`')
{
	return "{$prefix}.*";
}

//=============================================================================

function getBrandsRedeclareObj($brand)
{
	global $cid;
	
	return array(
		'brand' => $brand,
		'funcURL' => 'brandCatSectionsURL',
		'funcName' => 'brandCatSectionsName'
	);
}

function brandCatSectionsURL($brand, $cat)
{
	global $dir_prefix;

	if ((empty($cat) || empty($cat['__id'])) ) {
		 echo "empty brand";
		$res = getRealLinkURL("brand:{$brand['__id']}");
	} else {
		$res = $dir_prefix. "cat/brand-{$brand['__id']}-cid-{$cat['__id']}.htm";
	}

	return $res;
}

function brandCatSectionsName($brand, $cat)
{
	return $cat['Name']; 
}
