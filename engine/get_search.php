<?php

function decorate($str, $this_one = false){
	if($this_one)
		return "<td style=\"padding: 4px 7px; background-color: #086FA1; color: #ffffff;\">".$str."</td>";
	return "<td style=\"padding: 4px 5px 4px 5px;\">".$str."</td>";
}

function makeLink($str, $params, $p, $this_one = false){
	global $dir_prefix;
	return "<a href=\"$dir_prefix"."search?$params&p=$p\">$str</a>";
}

function drawPageLinks($pages_total){
	$p = $_GET['p'];
	if(!$p)
		$p = 1;
	if($p > $pages_total)
		$p = $pages_total;
	
	foreach($_GET as $key => $value){
		if($key == 'p')
			continue;
		$params[] = $key.'='.$value;
	}
	$params = join('&', $params);
	
	if($pages_total < 10){
		/*
		 если страниц мало
		*/
		for($i = 1; $i <= $pages_total; $i++){
			if($i == $p){
				$pages[] = decorate("$i", true);
				continue;
			}
			
			$pages[] = decorate(makeLink("$i", $params, $i));
		}
	} else {
		/* 
			а если много...
			то рассмотрим 3 варианта:
			- выбрана страница $p <= 7
			- выбрана страница $p > ($pages_total - 7)
			- выбрана страница 7 < $p <= ($pages_total - 7) 
		*/
		if($p < 6){
			for($i = 1; $i <= 7; $i++){
				if($i == $p){
					$pages[] = decorate("$i", true);
					continue;
				}
				$pages[] = decorate(makeLink("$i", $params, $i));
			}
			// середина отрезка от a до b: (a + b) / 2
			// floor(($pages_total + 7) / 2) - середина отрезка от 7 до $pages_total
			$pages[] = decorate(makeLink("<b>&hellip;</b>", $params, floor(($pages_total + 7) / 2)));
			$pages[] = decorate(makeLink("$pages_total", $params, $pages_total));
		} else if($p > ($pages_total - 6)){
			$pages[] = decorate(makeLink("1", $params, 1));
			// здесь используется  floor(($pages_total - 5) / 2) потому, что это середина отрезка от 1 до $pages_total - 7 + 1 
			$pages[] = decorate(makeLink("<b>&hellip;</b>", $params, floor(($pages_total - 5) / 2)));
			for($i = ($pages_total - 7 + 1); $i <= $pages_total; $i++){
				if($i == $p){
					$pages[] = decorate("$i", true);
					continue;
				}
				$pages[] = decorate(makeLink("$i", $params, $i));
			}
		} else {
			$pages[] = decorate(makeLink("1", $params, 1));
			$pages[] = decorate(makeLink("<b>&hellip;</b>", $params, floor(($p - 2) / 2)));
			for($i = ($p - 3); $i <= ($p + 3); $i++){
				if($i == $p){
					$pages[] = decorate("$i", true);
					continue;
				}
				$pages[] = decorate(makeLink("$i", $params, $i));
			}
			$pages[] = decorate(makeLink("<b>&hellip;</b>", $params, floor(($pages_total + $p + 3) / 2)));
			$pages[] = decorate(makeLink("$pages_total", $params, $pages_total));
		} 
	}
	$pages = "<table border=\"0\" width=\"100\" cellpadding=\"0\" cellspacing=\"0\">".decorate("<b>Страницы:</b>").join("", $pages)."</table>";
	return $pages;
}

function getSearchResults($s){
	function getSamples($haystack, $needle, $limit=3){
		$haystack = " ".strip_tags($haystack);
		$width = 30;
		$count = 0;
		unset($samples);
		$hs = $haystack;
		do{
			for($i=0; $i<count($needle); $i++){
				$pos = strpos(strtoupper($haystack), strtoupper($needle[$i]));
				if($pos){
					$custom_needle = substr($haystack, $pos, strlen($needle[$i]));
					$pw = $pos-$width;
					if($pw < 0) 
						$pw = 0;
					$sample = str_replace($custom_needle, '<b>'.$custom_needle.'</b>', substr($haystack, $pw, $width*2+strlen($needle[$i])));
					$sample = substr($sample, strpos($sample, ' ')+1);
					$sample = substr($sample, 0, strrpos($sample, ' '));
					if(trim($sample) != '') {
						$samples.="<br>"."...".$sample."...";
						$count++;
					}
					$haystack = nl2br(substr($haystack, $pos+strlen($needle[$i])-1));
				}
			}
		} while ($pos && $count < $limit);
		return $samples;
	}

	global $db, $p, $l, $ent, $http, $special_locations, $dir_prefix, $blade, $mobile_dir;
	$pg = $p;
	$s = trim(strip_tags($s));
		$ent['s'] = $s;
	$ent['action'] = 'search';
	unset($result);

	if($http != 404 && $s == "") 
		$result['Header'] = (language<1)?"Карта сайта":"Site Map";
	else if($http != 404 && $s != "") {
		define("dir_prefix", "/");
		$result['Header'] = (language < 1)?"Результаты поиска":"Search Results";
	} else 
		$result['Header'] = (language<1)?"404 - страница не найдена":"HTTP 404 - Page not found";

	$result['PlainHeader'] = $result['Header'];
	$msgLineTooShort = (language<1)?"Строка для поиска слишком короткая!":"Your search phrase is too short!";
	$msgNotFound = (language<1)?"Извините, по вашему запросу (<b>".$s."</b>) ничего не найдено.":"We are sorry, but your search for <b>".$s."</b> produced no results.<br>Please try again or use our site map:";
	$msgPrev = (language<1)?"пред.":"prev.";
	$msgNext = (language<1)?"след.":"next";
	$msgResults = (language<1)?"Результаты":"Results";
	$msgOf = (language<1)?"из":"of";
	unset($pages);
	$ent['Header'] = $ent['Title'] = 'Результаты поиска';

	if(strlen($s) < 3) {
		$ent['message'] = 'Строка для поиска слишком короткая';
		$result['Body'] = parseTemplate(tplFromFile(dir_prefix."engine/templates/search_none.htm"), $ent);
		$ent['has_results'] = false;
		return $blade->render($mobile_dir.'search', $ent);
	}

	$os = explode(" ", $s);
	for ($i=0; $i<count($os); $i++) {
		$os[$i] = mysql_real_escape_string($os[$i]);
	}
	
	//===============================================================================
	//                                       pages
	//===============================================================================
	
	$query = "select * from Pages where Enabled='1' AND (Header like '%".join("%' AND Header like '%", $os)."%') OR (Body like '%".join("%' AND Body like '%", $os)."%' AND Body<>'') OR (MetaTitle like '%".join("%' AND MetaTitle like '%", $os)."%') OR (MetaDescription like '%".join("%' AND MetaDescription like '%", $os)."%') OR (MetaKeywords like '%".join("%' AND MetaKeywords like '%", $os)."%')";
	//echo($query."<br><br>");
	$res = $db->getData($query);
	$count = 0;
	for ($i=0; $i<count($res); $i++){
		if($res[0]['Enabled']=="1") {
			unset($page);
			$r = $res[$i];
			$url = getRealLinkURL("pages:".$r['__id']);
			$page['no'] = $count+1;
			$count++;
			$page['URL'] = $url;
			$page['Header'] = $r['Header'];
			$page['Samples'] = getSamples($r['Header']."<br>".$r['Body'], $os).'<br />';
			$pages[] = $page;
		}
	}

	
	//===============================================================================
	//                                       news
	//===============================================================================
	
	$query = "select * from News where (Header like '%".join("%' AND Header like '%", $os)."%') OR (Body like '%".join("%' AND Body like '%", $os)."%' AND Body<>'')";
	
	$res = $db->getData($query);
	for ($i=0; $i<count($res); $i++){
		unset($page);
		$r = $res[$i];
		$url = getRealLinkURL("news:".$r['__id']);
		$page['no'] = $count+1;
		$count++;
		$page['URL'] = $url;
		$page['Header'] = $r['Header'];
		$page['Samples'] = getSamples($r['Header']."<br>".$r['Body'], $os).'<br />';
		$pages[] = $page;
	}
	

	//===============================================================================
	//                                       cat categories
	//===============================================================================

	$query = "select * from CatCategories where ((Name like '%".join("%' AND Name like '%", $os)."%'))";
	$res = $db->getData($query);
	for ($i=0; $i<count($res); $i++){
		unset($page);
		$r = $res[$i];
		$url = getRealLinkURL("cat:".$r['__id']);
		$page['no'] = $count+1;
		$count++;
		$page['URL'] = $url;
		$page['Header'] = ((language<1)?"Разделы каталога :: ":"Catalogue Sections :: ").$r['Name'];
		$page['Samples'] = getSamples($r['Name']."|<br>".$r['Body'], $os);
		$pages[] = $page;
	}

	//===============================================================================
	//                                       cat items
	//===============================================================================

	$query = "select * from CatItems where ((Name like '%".join("%' AND Name like '%", $os)."%') OR (Body like '%".join("%' AND Body like '%", $os)."%'))";
	$res = $db->getData($query);
	for ($i=0; $i<count($res); $i++){
		unset($page);
		$r = $res[$i];
		$url = getRealLinkURL("pid:".$r['__id']);
		$page['no'] = $count+1;
		$count++;
		$page['URL'] = $url;
		$page['Header'] = ((language<1)?"Каталог :: ":"Catalogue :: ").$r['Name'];
		$page['Samples'] = getSamples($r['Name']."|<br>".$r['SmallDesc']."|<br>".$r['Body'], $os);
		$pages[] = $page;
	}

	if(count($pages)<1) {
		$ent['message'] = $msgNotFound;
		$result['Body'] .= parseTemplate(tplFromFile(dir_prefix."engine/templates/search_none.htm"), $ent);
		$ent['has_results'] = false;
		
		return $blade->render($mobile_dir.'search', $ent);
	}

	
	$entries_per_page = 15;
	
	$total = count($pages);
	$total_pages = floor($total / $entries_per_page);
	
	if($total_pages * $entries_per_page < $total) 
		$total_pages++;
	
	if($total_pages < 1) 
		$total_pages = 1;
	if($pg < 1) 
		$pg = 1;
	if($pg > $total_pages) 
		$pg = $total_pages;
	$first = ($pg - 1) * $entries_per_page;
	$last = $first + $entries_per_page - 1;
	if($last >= $total) 
		$last = $total - 1;
	if($pg > 1) 
		$prevlink = "<a href=\"../search/?l=".$l."&s=".$s."&p=".($pg-1)."\"><b>&laquo; ".$msgPrev."</b></a>";
	else 
		$prevlink = "&nbsp;";
	if($pg < $total_pages) 
		$nextlink = "<a href=\"../search/?l=".$l."&s=".$s."&p=".($pg+1)."\"><b>".$msgNext." &raquo;</b></a>";
	else 
		$nextlink = "&nbsp;";

	$ent['navbar'] = "<table class='search' width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"30%\" nowrap>".drawPageLinks($total_pages)."</td>
	<td align=\"center\"><strong>".$msgResults.": ".($first+1)." - ".($last+1)." ".$msgOf." ".$total."</strong></td><td width=\"30%\" align=\"right\" nowrap>&nbsp;</td></tr></table>";
	$ent['pages'] = drawPageLinks($total_pages);
	unset($res);
	for ($i=$first; $i<=$last; $i++){
		$res[] = $pages[$i];
	}
	$ent['has_results'] = true;
	$ent['results'] = $res;
	return $blade->render($mobile_dir.'search', $ent);
}
