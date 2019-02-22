<?php

function processNews(){
  global $db, $ent, $c, $dir;
  if($dir!="") {
   $c = getIDFromURL("News", $dir);
   if(!$c) die404();
  }
  if($c>0) return getNewsDetails($c);
  else return getNewsList();
}

function getNewsList(){
  global $id, $p;
  $where = "WHERE DateTime <= '".timeStr()."'";
  $total = db::field("SELECT COUNT(__id) as cnt FROM News ".$where);
  $page_limit = 6;
  if($p<1) $p = 1;
  $first = ($p-1)*$page_limit;
  if($first<0) $first = 0;
  $total_pages = round($total / $page_limit) + 1;
  $news = db::select("SELECT * FROM News $where ORDER BY DateTime DESC LIMIT ".$first.", ".$page_limit);

  $ent['navbar_info'] = getNavbarInfo($first, $page_limit, count($news), $total, $total_pages);

  foreach ($news as &$item) {
    $item['Date'] = date('d.m.Y', strtotime($item['DateTime']));
    $item['Link'] = getRealLinkURL('news:'.$item['__id']);
  }
  $ent['news'] = $news;
  $ent['navbar'] = generateNavbar($total, $p, $page_limit);
  /*Meta*/
  $page = db::single("SELECT * FROM Pages WHERE __id=".intval($id));
  $ent['Header'] = $page['Header'];
  $ent['MetaKeywords'] = $page['MetaKeywords'];
  $ent['MetaDescription'] = $page['MetaDescription'];
  if(!empty($page['MetTitle']))
    $ent['Title'] = $page['MetTitle'];
  else
    $ent['Title'] = $page['Header'];
  return blade::render('news.list', $ent);
}

function getNewsListForMenu(){
  global $id, $p;
  $where = "WHERE DateTime <= '".timeStr()."'";
  $total = db::field("SELECT COUNT(__id) as cnt FROM News ".$where);
  
  $news = db::select("SELECT * FROM News $where ORDER BY DateTime DESC LIMIT 3");

    foreach ($news as &$item) {
    $item['Date'] = date('d.m.Y', strtotime($item['DateTime']));
    $item['Link'] = getRealLinkURL('news:'.$item['__id']);
  }
  $ent['news'] = $news;
    /*Meta*/
  $page = db::single("SELECT * FROM Pages WHERE __id=".intval($id));
  $ent['Header'] = $page['Header'];
  $ent['MetaKeywords'] = $page['MetaKeywords'];
  $ent['MetaDescription'] = $page['MetaDescription'];
  if(!empty($page['MetTitle']))
    $ent['Title'] = $page['MetTitle'];
  else
    $ent['Title'] = $page['Header'];
  return blade::render('news.list-for-menu', $ent);
}

function getNewsDetails($id){
  $item = db::single("SELECT * FROM News WHERE __id=".$id);
  if(!$item)
    die404();
  $item['Date'] = date('d.m.Y', strtotime($item['DateTime']));
  $item['Link'] = getRealLinkURL('news:'.$item['__id']);
  $item['Body'] = processText($item['Body']);
  if(!empty($item['MetaTitle']))
    $item['Title'] = $item['MetaTitle'];
  else
    $item['Title'] = $item['Header'];
  return blade::render('news.details', $item);
}