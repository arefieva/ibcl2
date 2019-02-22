<?php
function formatPrice($what)
{
	return str_replace(".", ",", str_replace(",", " ", number_format($what, 2)));
}
//=======================================================================
function headerNotFound() {
	header("HTTP/1.1 404 Not Found");
}

function get404Page() {
	global $blade;
	$ent['Title'] = $ent['Header'] ='404 страница не найдена';
	/*use <base> tag*/
	/*may be problems with constant dir_prefix*/
	unset($GLOBALS['dir_prefix']);
	$blade->share('dir_prefix', '');
	header("HTTP/1.1 404 Not Found");
	return $blade->render("site.404", $ent);
}

function die404(){
	global $dir_prefix;
	echo get404Page();
	die();
}
//=======================================================================
function format_price($what){
  $price = number_format($what, 2, ',', ' ');
  $price = str_replace(",00", "", $price);
  return $price;
}
//=======================================================================
function my_setcookie($name, $value, $time="", $path = "/", $domain = "") {
	global $HTTP_HOST;

	if($domain=="") $domain = $HTTP_HOST;
	if($time=="") $time = time()+30*24*3600;
	$name = trim($name);
	setcookie($name, $value, $time, $path, str_replace("www.", "", strtolower($domain)));
	setcookie($name, $value, $time, $path, "www.".str_replace("www.", "", strtolower($domain)));
}
//=======================================================================
function strip_slashes_all(){
	foreach($_POST as $key=>$value){
		global $$key;
		$$key = stripslashes($value);
	}
	foreach($_COOKIE as $key=>$value){
		global $$key;
		$$key = stripslashes($value);
	}
}
//=======================================================================
function hashPswd($pswd = '', $salt = '') { 
	return crypt($pswd, '$2y$11$' . sha1(md5("IdER93j") . sha1($salt)) . '$');
}
//=======================================================================
function generatePass($length = 15, $nosymbols = false){
	if(!$nosymbols)
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789)(*&^%$#@!;";
	else
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		
	$string = "";
	
	for ($i=0; $i<$length; $i++) {
		$pos = rand(0, strlen($chars)-1);
		$string.=substr($chars, $pos, 1);
	}	
	return $string;
}
//=======================================================================
function displayMessage($what){
	global $ent;
	$ent['message'] = $what;
	$what = parseTemplate(tplFromFile(dir_prefix."engine/templates/message.htm"), $ent);
	return $what;
}
//=======================================================================
function my_nl2br($what){
	$n = "\n";
	$r = "\r";
	$what = str_replace($r, "", $what);
	$what = str_replace($n.$n, $n, trim($what));
	$what = nl2br($what);
	return $what;
}
//=======================================================================
function processQuotes($name){
	return str_replace("\"", "&quot;", $name);
}
//=======================================================================
function processDate($what, $template = 0){
	global $monthArr;
	$dt = getdate(MySQLtimestamp2unix($what));
	if($template==0) {
		$dd = $dt['mday'];
		$mm = $monthArr[$dt['mon']-1];
		$yy = $dt['year'];

		$hh = $dt['hours'];
		$min = $dt['minutes'];
		$ss = $dt['seconds'];

		$res = date("d.m.Y", MySQLtimestamp2unix($what));
		
	} else {
		$res = date("d", MySQLtimestamp2unix($what))."<span>".$monthArr[$dt['mon']-1]."</span>";
	}
	return $res;
}
//=======================================================================
function day_has_news($day, $month, $year, $dates){
  if($dates[$year][$month][$day]>0) return true;
  else return false;
}
//=======================================================================
function month_has_news($month, $year, $dates){
  if($dates[$year][$month]['total']>0) return true;
  else return false;
}
//=======================================================================
function year_has_news($year, $dates){
  if($dates[$year]['total']>0) return true;
  else return false;
}
//=======================================================================
function combineDates($where) {
	global $nd;
	unset($result);
	for ($i=0; $i<count($where); $i++) {
		$dt = getDate(mysqlTimeStamp2Unix($where[$i]['DateTime']));

		$rt = $result[$dt['year']][$dt['mon']][$dt['mday']];
		if($rt<1) $rt = 0;
		$rt++;
		$result[$dt['year']][$dt['mon']][$dt['mday']] = $rt;

		$rt = $result[$dt['year']][$dt['mon']]['total'];
		if($rt<1) $rt = 0;
		$rt++;
		$result[$dt['year']][$dt['mon']]['total'] = $rt;

		$rt = $result[$dt['year']]['total'];
		if($rt<1) $rt = 0;
		$rt++;
		$result[$dt['year']]['total'] = $rt;
	}
	return $result;
}
//=======================================================================
function displayFooterLink(){
	global $db, $dir_prefix, $debugFooterLinks;

	if (!empty($debugFooterLinks)) {
		$link[0] = array(
			'pre_text' => "тестовое заполнение ссылки",
			'text' => "на сайт компании Webis Group",
			'post_text' => "для тестирования свободного места",
			'title' => "Webis Group",
			'webis_href' => "http://www.webisgroup.ru/"
		);
	} else {
		$uri = mysql_real_escape_string($_SERVER['REQUEST_URI']);

		$query = "
			SELECT pre_text, text, post_text, title, webis_href
			FROM FooterLinks
		";
		$link = $db->getData($query . "WHERE url = '{$uri}'");

		if (!$link) {
			$link = $db->getData($query . "WHERE url = ''");
		}
	}

	if (!$link) {
		$link[0] = array(
			'pre_text' => "<noindex>",
			'text' => "создание сайтов",
			'post_text' => "</noindex>",
			'title' => "создание сайтов",
			'webis_href' => "http://www.webisgroup.ru/"
		);
	}

	$link[0]['dir_prefix'] = $dir_prefix;

	return parseTemplateFromFile(dir_prefix . "engine/templates/footer_link.htm", $link[0]);
}
//=======================================================================
function generateNavbar($total, $pg, $npc, $params="")
{
	global $blade;
	$prefix = "./";
	$suffix = "";

	if ($pg < 1) {
		$pg = 1;
	}
	$total_pages = round($total / $npc) + 1;
	if ($total_pages * $npc - $total >= $npc) {
		$total_pages--;
	}

	if ($total_pages < 1) {
		$total_pages = 1;
	}
	if ($pg > $total_pages) {
		$pg = $total_pages;
	}
	$first = ($pg - 1) * $npc;
	if ($first < 0) {
		$first = 0;
	}
	$last = $first + $npc - 1;
	if ($last >= $total) {
		$last = $total - 1;
	}
	if ($total_pages == 1) {
		return "";
	}
	$ent['prev'] = 1;
	$ent['next'] = $total_pages;
	$ent['width'] = 24;
	$limit = 3;
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

	$pages = array();
	for ($i = $from_page; $i <= $to_page; $i++) {
		$pages[] = array(
			'pos' => $i,
			'tag' =>  "a",
			'active' => $pg == $i ? true:false
		);
	}
	if($pg==1)
		$ent['prev'] = false;
	else
		$ent['prev'] = true;
	$ent['prevlink'] = $pg-1;
	if($pg==intval($total_pages))
		$ent['next'] = false;
	else
		$ent['next'] = true;
	$ent['nextlink'] = $pg+1;
			
	$ent['pages'] = $pages;
	$ent['params']=$params;
	$ent['link_all'] = $link_all;

	if($total_pages - $pg > 1 /*&& $ent['nextlink']-$pg>1*/){
		$ent['showlast'] = true;
		$ent['last'] = $total_pages;
	}
	$ent['showlast'] = false;
	if(language == 0){
		$ent['prev_word'] = "назад";
		$ent['next_word'] = "вперед";
	}else{
		$ent['prev_word'] = "prev";
		$ent['next_word'] = "next";
	}
	if(!empty($params)){
		$ent['params'] = "&".http_build_query($params);
	}
	return $blade->render('pagination', $ent);
}

function getNavbarInfo($first, $page_limit, $resultCount, $total, $total_pages){
  $num_pages = $page_limit > $total ? $total_pages-1: $total_pages;
  if($num_pages == 0)
  	return 'Всего: '.$total;
  return ($first == 0 ?: $first+1).'-'.($first + $resultCount).' из '.$total.' ('.$num_pages.' '.pluralForm($num_pages, 'страница', 'страницы', 'страниц').')';
}


function pluralForm($n, $form1 = "вариант", $form2 = "варианта", $form5 = "вариантов")
{
	$n = abs($n) % 100;
	$n1 = $n % 10;
	if ($n > 10 && $n < 20) {
		return $form5;
	}
	if ($n1 > 1 && $n1 < 5) {
		return $form2;
	}
	if ($n1 == 1) {
		return $form1;
	}

	return $form5;
}