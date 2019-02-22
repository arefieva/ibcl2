<?php

function generateCatItemsPagination($total, $pl = 50, &$first = 0, &$last = 0)
{
	global $p;

	if (empty($pl)) {
		$pl = 50;
	}

	global $dir_prefix, $brand_id, $cid, $type;
	$prefix = $dir_prefix . 'cat/';
	$suffix = '';
	if (!empty($cid)) {
		$suffix .= '-cid-' . $cid;
	}
	if (!empty($brand_id)) {
		$suffix .= '-brand-' . $brand_id;
	}
	if (!empty($type)) {
		$suffix .= '-type-' . $type;
	}

	return generatePagionation($total, $p, $pl, $first, $last, $prefix, $suffix, 7, 'cat');
}

//=============================================================================

function generateArticlesPagination($total, $pl = 30, &$first = 0, &$last = 0)
{
	global $p;

	if (empty($pl)) {
		$pl = 30;
	}

	global $section;
	$prefix = getArticleSectionLink($section) ;

	return generatePagionation($total, $p, $pl, $first, $last, $prefix, null, 7, 'articles');
}

//=============================================================================

function generateAdminOrderPagination($total, $pl = 50, &$first = 0, &$last = 0)
{
	global $p, $st, $Period, $search;
	
	if (empty($p)) {
		$p = 1;
	}

	if (empty($pl)) {
		$pl = 50;
	}

	global $dir_prefix;
	$prefix = $dir_prefix . 'admin/home.php?action=orders&';
	$suffix = "&action=orders&st={$st}&Period={$Period}&search={$search}";

	return generatePagionation($total, $p, $pl, $first, $last, $prefix, $suffix);
}

//=============================================================================

function generateSearchPagination($total, $pl = 50, &$first = 0, &$last = 0)
{
	global $p, $s;

	if (empty($p)) {
		$p = 1;
	}

	if (empty($pl)) {
		$pl = 50;
	}

	global $dir_prefix;
	$prefix = $dir_prefix . 'search/?';
	$suffix = "&pl={$pl}&s={$s}";

	return generatePagionation($total, $p, $pl, $first, $last, $prefix, $suffix);
}

//=============================================================================
//=============================================================================

function generatePagionation($total, $pg, $pl, &$first = 0, &$last = 0, $prefix = "", $suffix = "", $limit = 7, $mod)
{
	$total_pages = floor($total / $pl);
	if ($total_pages * $pl < $total) {
		$total_pages++;
	}
	if ($total_pages < 1) {
		$total_pages = 1;
	}
	if (empty($pg) || $pg < 1) {
		$pg = 1;
	}
	if ($pg > $total_pages) {
		$pg = $total_pages;
	}
	$first = ($pg - 1) * $pl;
	$last = $first + $pl - 1;
	if ($last >= $total) {
		$last = $total - 1;
	}

	if ($total_pages == 1) {
		return "";
	}

	$from_page = 1;
	$to_page = $total_pages;

	if ($to_page - $from_page >= $limit) {
		if ($pg - ($limit - 1) / 2 < 1) {
			$to_page = min($total_pages, 1 + $limit - 1);
		} elseif ($pg + ($limit - 1) / 2 > $total_pages) {
			$from_page = max(1, $total_pages - $limit + 1);
		} else {
			$to_page = $pg + ($limit - 1) / 2;
			$from_page = $pg - ($limit - 1) / 2;
		}
	}

	if($mod == 'articles' || $mod == 'cat'){
		$delim = '-';
		$htm = '.htm';
	}else{
		$delim = '=';
	}

	$pages = array();
	for ($i = $from_page; $i <= $to_page; $i++) {
		$p = array();
		$p['pos'] = $i;
		if ($i == $pg) {
			$p['link'] = '<span>' . $i . '</span>';
		} else {
			$p['link'] = "<a href=\"" . $prefix . "p".$delim . $i . $suffix .$htm. "\">" . $i . "</a>";
		}
		$pages[] = $p;
	}
	$ent['pages'] = $pages;

	if ($pg == 1) {
		$ent['prev'] = 'prev';
	} else {
		$ent['prev'] = 'prev_a';
		$ent['prev_link'] = $prefix . 'p'. $delim . ($pg - 1) . $suffix.$htm;
	}

	if ($pg == $total_pages) {
		$ent['next'] = 'next';
	} else {
		$ent['next'] = 'next_a';
		$ent['next_link'] = $prefix . 'p'. $delim . ($pg + 1) . $suffix.$htm;
	}

	return parseTemplate(tplFromFile(dir_prefix . "engine/templates/pagination.htm"), $ent);
}
