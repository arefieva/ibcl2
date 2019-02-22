<?php


function displayIndexPage(){
  return blade::render('site.index', getPageMeta(route::idByName('index')));
}

function displayInnerPage($id){	
  $page = db::single('SELECT * FROM Pages WHERE __id='.$id);
  if(!$page){
    die404();
  }
  $page['Title'] = $page['Header'];
  $page['Body'] = processText($page['Body']);
  if(!empty($page['MetaTitle'])){
    $page['Title'] = $page['MetaTitle'];
  }
  return blade::render('site.inner', $page);
}

function getPageMeta($pageId){
  $meta = db::single("select Header, MetaTitle, MetaKeywords, MetaDescription, altHeader from Pages where __id=".intval($pageId));
  $meta['Title'] = $meta['Header'];
  if(!empty($meta['MetaTitle'])){
    $meta['Title'] = $meta['MetaTitle'];
  }
  if($pageId == route::idByName('index')){
    $meta['Title'] = getSystemVariable('default_title');
  }
  return $meta;
}


function displayLogo($suffix = ""){
  global $db, $ent, $dir_prefix;
  global $site_logo;
  $suffix = mysql_real_escape_string($suffix);
  $res = $db->getData("
      select 
        Images.smallURL, 
        Images.bigURL 
      from 
        Images 
      where 
        Name='logo".$suffix."'
  ");

  if(count($res)>0){
    $url = ($res[0]['smallURL']!="")?$res[0]['smallURL']:$res[0]['bigURL'];
    $site_logo = $url;

    return "<a href='".$dir_prefix."'><img title='".getSystemVariable($db, "site_name")."' src='".$dir_prefix.$url."' alt='".getSystemVariable($db, "site_name")."'></a>";
  }
}


function displayBreadcrumbs($header){
  global $blade, $id, $cid, $pid, $c, $brand_id, $action;

 if (empty($brand_id)){
    $breadcrumbs = getBreadcrumbs($id, 'pages', 'Pages', 'Header');
  
 }

  if ($action == 'search'){
	  $breadcrumbs[0]['Name'] = 'Главная';
	  $breadcrumbs[0]['Link'] = dir_prefix;
	  $breadcrumbs[]['Name'] = $header;
  }
  if (!empty($brand_id)){
    $breadcrumbs[0]['Name'] = 'Главная';
    $breadcrumbs[0]['Link'] = dir_prefix;
  }
  
  if(!empty($c)){
    $breadcrumbs[]['Name'] = $header;
  }
  if(!empty($cid) && empty($brand_id)){
    $cat_breadcrumbs = getBreadcrumbs($cid, 'cid', 'CatCategories', 'Name');
    $breadcrumbs = array_merge($breadcrumbs, $cat_breadcrumbs);
  }
  if(!empty($pid) && empty($brand_id)){
    $cat_breadcrumbs = getBreadcrumbs(db::field("SELECT Category FROM CatItems WHERE __id=".$pid), 'cid', 'CatCategories', 'Name');
    $cat_breadcrumbs[]['Name'] = $header;
    $breadcrumbs = array_merge($breadcrumbs, $cat_breadcrumbs);
  }


  if (!empty($brand_id)){
	  	  
		global $brand, $redeclareObj;
		$brand = getBrandInfo($brand_id);
	  if ($cid) {
  		$catName = getCatCategoryName($cid);
      $brand_breadcrumbs[0]['Name'] = $brand['Name'];
      $brand_breadcrumbs[0]['Link'] = getRealLinkURL("brand:{$brand['__id']}");
      $brand_cat_breadcrumbs = getCatSections($cid, false, getBrandsRedeclareObj($brand));

      for ($i=count($brand_cat_breadcrumbs)-1; $i>=0; $i--) {
          $brand_cat_breadcrumbs_inv[$i] = $brand_cat_breadcrumbs[$i];
      }
       $brand_breadcrumbs = my_array_merge($brand_breadcrumbs, $brand_cat_breadcrumbs_inv);
	  } 
	$breadcrumbs = array_merge($breadcrumbs, $brand_breadcrumbs);
  }

  foreach ($breadcrumbs as &$item){
	if($item != end($breadcrumbs))
		$item['Body']="<li class='breadcrumb-item'>
			<a href='".$item['Link']."'>".$item['Name']."</a>
		</li>";
	else {/*if (count($breadcrumbs)>1)*/
		$item['Body']="<li class='breadcrumb-item current'>
			<span class='navigation_page'>".$item['Name']."</span>
		</li>";
	}
  }

  return $blade->render('breadcrumbs', ['breadcrumbs' => $breadcrumbs]);
}

function getBreadcrumbs($start, $sysname, $table, $nameField){
  $parent = $start;
  do{
    unset($c);
    $item = db::single("SELECT * FROM $table WHERE __id=".intval($parent));
    $parent = $item['Parent'];
    $c['Name'] = $item[$nameField];
    $c['Link'] = getRealLinkURL($sysname.':'.$item['__id']);
    $breadcrumbs[] = $c;
  }
  while($parent != 0);
  return array_reverse($breadcrumbs);
}

//===============================================================

function getPageName($id){
  global $db;
  $pg = $db->getData("select Header from Pages where __id='".intval($id)."'", true);
  return $pg[0]['Header'];
}

function getChildPages($id){
  global $db;
  $pg = $db->getData("select __id, Parent, Header, CustomOrder, Active from Pages where Parent='".intval($id)."' AND Active=1 order by CustomOrder", true);
  unset($pages);
  for ($i=0; $i<count($pg); $i++){
    unset($page);
    $page['id'] = $pg[$i]['__id'];
    $page['Header'] = $pg[$i]['Header'];
    $pages[] = $page;
  }
  return $pages;
}

//===============================================================

function is_parent($parent, $id, $table = 'Pages'){
  global $db;
  $pg = $db->getData("select Parent from $table where __id='".intval($id)."'", true);
  if($pg[0]['Parent'] == route::idbyname('index')) return false;
  return ($pg[0]['Parent'] == $parent) ? true : is_parent($parent, $pg[0]['Parent'], $table);
}

//===============================================================

function getParentAtLevel($level, $id, $table='Pages'){
  global $db;
  static $cnt = 0;
  $cnt++;
  if($cnt > 100)
    die('stop');
  $pg = $db->getData("select __id, Parent from $table where __id='".intval($id)."'", true);
  $parent = $pg[0]['Parent'];
  if(getPageLevel($id, $table)==$level) return $id;
  else if($parent!= route::idByName('index')) return getParentAtLevel($level, $parent, $table);
  else return false;
}

//===============================================================

function getPageLevel($id, $table='Pages'){
  global $db;
  $res = 1;
  $pg = $db->getData("select __id, Parent from $table where __id='".intval($id)."'", true);
  $parent = $pg[0]['Parent'];
  if($parent!=page_index&&$parent!="") $res += getPageLevel($parent, $table);
  else $res = 1;
  return $res;
}

//===============================================================

function getNearestPageHref($id, $additional = "", $pid=-1, $level=0){
  if(!is_numeric($id)) return false;
  if($level<1) {
    if($last_modified>=last_update) return $gnph;
  }
  global $db;

  $opid = $pid;

  if($pid==0) {
    $res = "";
    return $res;
  }

  if($pid<0) $pid = $id;

  $pg = $db->getData("select __id, Parent, DirectoryName from Pages where __id='".intval($pid)."'", true);


  if($pg[0]['DirectoryName']!="") {
    $res = getNearestPageHref($id, $additional, $pg[0]['Parent'], $level+1).$pg[0]['DirectoryName']."/";
    if($opid==-1) {
      if($additional!="") $res.=$additional;
      else $res.="";
    }
  }  else {
    // if($opid==-1 && $id==page_index_rus)
      // $res = getNearestPageHref($id, $additional, $pg[0]['Parent'], $level+1);
    // else 
    if($opid==-1)
      $res = getNearestPageHref($id, $additional, $pg[0]['Parent'], $level+1)."?id=".$id;
    else 
      $res = getNearestPageHref($id, $additional, $pg[0]['Parent'], $level+1);
  }
  if($level<1) {
    $phpStr = "<?
  $"."last_modified = ".time().";
  $"."gnph='".$res."';
?>";
  }
  return $res;
}

//===============================================================
function getTopParent($id, $table = 'Pages'){
  global $db;
  $pg = $db->getData("select __id, Parent from $table where __id='".intval($id)."'", true);
  $parent = $pg[0]['Parent'];
  if($parent!=""&&$parent!=0&&$parent!=route::idByName('index')) 
    $parent = getTopParent($parent);
  else 
    $parent = $id;
  return $parent;
}

function displayAboutPage(){
	global $db, $blade;
  $pg = $db->select("select * from Pages where __id=1");
  $ent = ProcessText($pg[0]["Body"]);
  return $blade->render('about', ['ent' => $ent]);
}

