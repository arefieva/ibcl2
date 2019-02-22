<?php
function processSubLinks($element){
  global $id, $db, $ent, $dir_prefix;

  if($element[0]>0) 
	$rt = $element[0];
  else 
	$rt = $id;
  $max_cols = $element[1];
 
  if($max_cols=="") $max_cols = 2;
  
  $submenu_template = $element[2];
  if($submenu_template=="") 
	$submenu_template = "sublinks1";
  
  if($max_cols<1) 
	$max_cols = 1;

  $bootstrap_per_line = ceil(12 / $max_cols);
  $bootstrap_per_line = ($bootstrap_per_line>12)?12:$bootstrap_per_line;
  $bootstrap_per_line = ($bootstrap_per_line<=0)?1:$bootstrap_per_line;

  $rec = $db->getData("select __id, Header, Abstract, CustomOrder from Pages where Parent='".intval($rt)."' AND Enabled='1' order by CustomOrder");

  unset($items);
  for ($i=0; $i<count($rec); $i++) {
	$ri 	= $rec[$i];
	unset($it);
	if($ri['__id']==$id)
	$it['class'] 	= "active"; 
	$it['Link'] 		= getRealLinkURL("pages:".$ri['__id']);
	$it['Header'] 		= $ri['Header'];
	$it['abstract'] 	= nl2br($ri['Abstract']);
	$items[] 			= $it;
  }
  $ent['items'] 		= $items;
  return blade::render('menu.sublinks', $ent);
}

function displaySiteMap(){
  $sitemap = getSitemap();
  return $sitemap;
}


function getSitemap($Parent = 0, $level = 0){
  global $blade;
  $res = db::select("SELECT * FROM Pages WHERE Parent=".$Parent." ORDER BY CustomOrder");
  $cat_id = route::idbyname('cat');
  foreach ($res as $item) {
	unset($c);
	if($item['__id'] == $cat_id){
	  $c['nextlevel'] = getCatSitemap();
	}else{
	  $c['nextlevel'] = getSitemap($item['__id'], $level+1);
	}
	$c['name'] = $item['Header'];
	$c['link'] = getRealLinkURL('pages:'.$item['__id']);
	$cats[] = $c;
  }
  $ent['menu'] = $cats;
	return $blade->render('menu.sitemap', $ent);
}


function getCatSitemap($Parent=0,$level=0){
  global $blade;
  $res = db::select("SELECT * FROM CatCategories WHERE Parent=".$Parent." ORDER BY CustomOrder");
  foreach ($res as $item) {
  unset($c);
  $c['name'] = $item['Name'];
  $c['link'] = getRealLinkURL('cid:'.$item['__id']);
  $c['nextlevel'] = getCatSitemap($item['__id'], $level+1);
  $cats[] = $c;
  }
  $ent['menu'] = $cats;
  return $blade->render('menu.sitemap', $ent);
}

/*----------Header menu----------*/
function displayHeaderMenu($template = "1"){
	global $id;
	$pages 		= db::select("SELECT * FROM Pages WHERE Active=1 AND Enabled=1 AND Parent=".route::idbyname('index')." ORDER BY CustomOrder");
	$topParent 	= getTopParent($id);
	foreach ($pages as &$page) {
		if($id == $page['__id'] || $topParent == $page['__id']){
		  $page['active'] = true;
		}
	}
	if($template=="1")
		return blade::render('menu.header', ['pages' => $pages]);
	else if($template=="2")
		return blade::render('menu.header_condensed', ['pages' => $pages]);

}


function displayCatMenu($template = "menu.cat"){
	global $dir_prefix;
	$cats = db::select("SELECT * FROM CatCategories WHERE Parent=0 AND Active='1' ORDER BY CustomOrder");
	foreach ($cats as &$cat) {
		$cat['ico'] 	= "icon";
		if($cat['ImageIcon'])
			$cat['ImageIcon'] 	= $dir_prefix.$cat['ImageIcon'];
		else
			$cat['ImageIcon'] 	= "";
		$cat['link'] 	= getRealLinkURL('cid:'.$cat['__id']);
	}
	return blade::render($template, ['cats' => $cats]);
}

