<?php

//===========================================================================================
function getIDFromURL($tableName, $directory) {
	global $db;
	$res = $db->getData("select __id from `".mysql_real_escape_string($tableName)."` where DirectoryName='".mysql_real_escape_string($directory)."'");
	if(count($res)<1) {
		return false;
	}
	else return $res[0]['__id'];
}
//===========================================================================================
function getVirtualURL($tableName, $id, $prefix = "") {
	global $db;
	$res = $db->getData("select DirectoryName from `".mysql_real_escape_string($tableName)."` where __id='".intval($id)."'");
	if($res[0]['DirectoryName']=="") return $prefix.$id.".htm";
	else return $res[0]['DirectoryName'].".htm";
}
//===========================================================================================
function processLink($what){
  global $special_locations;
  $text 		= $what[0];
  $location 	= urldecode($what[1]);
  $target 		= "";
  $rel 			= "";
  $noindex1 	= "";
  $noindex2 	= "";

  $location = str_replace("EMAIL:mailto:", "EMAIL:", $location);

  if(strstr($location, "URL:")){ // get URL
    $loc = trim(str_replace("URL:", "", str_replace("&grid;", "#", $location)));
    if(!strstr($loc, "javascript:")){

    	if ( !strstr($loc, "tel:") && !strstr($loc, "skype:") && !strstr($loc, "tel:") && !strstr($loc, "://")) 
			$loc = "http://".$loc;

		if(!strstr($loc, $_SERVER['HTTP_HOST']) && !strstr($loc, "tel:") && !strstr($loc, "skype:") && !strstr($loc, "tel:")) {
			$noindex1 	= "<noindex>";
			$noindex2 	= "</noindex>";
			$target 	= ' target="_blank" ';
			$rel 		= " rel=\"nofollow\" ";
		}
    }
    $loc = str_replace("&amp;grid;", "#", $loc);
	$loc = str_replace("&grid;", "#", $loc);
  } else if(strstr(" ".$location, "EMAIL:")) { // get email

    $email = str_replace("EMAIL:", "", $location);
    if(strstr($text, "@")) $text = encode_everything(trim($text));
    $loc = encode_everything("mailto:".$email);

  } else // get site page
    $loc = getRealLinkURL($location);
  
  $res = $noindex1."<a".$target.$rel." href=\"".$loc."\">".$text."</a>".$noindex2;
  return $res;
}
//===========================================================================================
function getRealLinkURL($id, $absolute = false) { 
	global $dir_prefix, $is_subscribe, $admin_baseURL;
	$site_root = "";

	if($absolute){
		$dp = "http://$_SERVER[SERVER_NAME]/";
	}else{
		$dp = $dir_prefix;
	}

	$location = explode(":", $id);
	if(count($location)>2) {
  		$location[0] = $location[1];
  		$location[1] = $location[2];
	}

	if(count($location)<2) $res = $dp.$site_root;
	else {
  		$module = $location[0];
  
  		$id = $location[1];
  		switch ($module) {

			case "cid":
			case "cat": 	$res = $dp."cat/".getVirtualURL("CatCategories", $id, "cat");
							break;

            case "catitems":
			case "pid": 	$res = $dp."cat/".getVirtualURL("CatItems", $id, "product");
							break;

			case "news": 	$res = $dp."news/".getVirtualURL("News", $id);
							break;
							
			case "brand":
							$res = $dp. "cat/" . getBrandVirtualURL($id);
							break;

			case "URL":		$id = urldecode($id);
							if(!strstr($id, "://")) $id = "http://".str_replace("http://", "", urldecode($id)); 
							$res = $id;
							break;

    		default:		$url = getNearestPageHref($id);

							if($id == route::idbyname('index')) 
								$url = "";

							if($is_subscribe) 
								$res = $admin_baseURL."/".$url;

							else if(!strstr(" ".strtolower($url), "http://")&&!strstr(" ".strtolower($url), "javascript:")) 
								$res = $dp.$url;
							else 
								$res = $url;
  		}
	}

	$res = str_replace("http:||", "http://", str_replace("//", "/", str_replace("http://", "http:||", $res)));

	return $res;
}

//===========================================================================================

function getBrandVirtualURL($id)
{
	global $db;

	$res = $db->getData("SELECT DirectoryName FROM `Brands` WHERE __id = '" . intval($id) . "'");

	if (!$res[0]['DirectoryName']) {
		return "brand" . $id . ".htm";
	} else {
		return "brand-" . $res[0]['DirectoryName'] . ".htm";
	}
}

function getBrandIDfromVirtualURL($url)
{
	global $db;

	$res = $db->getData("SELECT __id FROM `Brands` WHERE DirectoryName = '" . mysql_real_escape_string($url) . "'");

	if ($res) {
		return $res[0]['__id'];
	}
}

function getBrandIDfromVirtualURLfromOld($url)
{
	global $db;

	$res = $db->getData("SELECT `BrandID` FROM `BrandsOldLinks` WHERE `OldLink` = '" . mysql_real_escape_string($url) . "'");

	if ($res) {
		return $res[0]['BrandID'];
	}
}

//===========================================================================================
