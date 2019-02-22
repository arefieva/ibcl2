<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
require("engine_defs.php");
header("X-Powered-By: Apache\n");
header("Last-Modified: ".gmdate('D, d M Y H:i:s', time()-3600-$id*60)." GMT\n");
header("Expires: ".gmdate('D, d M Y H:i:s', time()+24*3600+$id)." GMT\n");
header("Cache-Control: max-age=1, must-revalidate\n");
header("Content-type: text/html; charset=UTF-8");
unset($dir_prefix);
for ($i=0; $i<$level; $i++){
	$dir_prefix .= "../";
}

define(dir_prefix, $dir_prefix);	//todo: remove constant entirely

require(dir_prefix."engine/engine.php");
use Illuminate\Cache\FileStore;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Cache\Repository;
use duncan3dc\Laravel\Blade;
use duncan3dc\Laravel\BladeInstance;
$db = new CDatabase();
define(useCache, false);
// $filestore = new FileStore(new Filesystem(), dir_prefix."cache/general/");
// $cache = new Repository($filestore);
// $blade = new BladeInstance(dir_prefix."engine/templates", dir_prefix."cache/views");
$blade = new BladeInstance(dir_prefix."engine/templates");

define(site_name, getSystemVariable("site_name"));
define(default_title, getSystemVariable("default_title"));

$ent['dir_prefix'] = $dir_prefix;
$ent['site_name'] = getSystemVariable($db, "site_name");
$globalMailTo = getSystemVariable($db, "globalMailTo");
if($id == route::idbyname('index') && $action == "")
	$is_index = true;
$blade->share('is_index', $is_index);
$blade->share('dir_prefix', $dir_prefix);
$blade->share('db', $db);
$blade->share('action', $action);

$pid = intval($pid);
$cid = intval($cid);
$c = intval($c);

global $p;
$p = $_GET['p'];

if (!isset($brand_id) || !is_numeric($brand_id)) {
	$brand_id = 0;
}

if($action=="doPostForm") $page['Body'] = doProcessForm($formid);

session_start();
session_write_close();

switch($action) {
	case "404":
		$response = get404Page();
		break;
	case "doPostForm":
		$response = doProcessForm($form);
		break;
	case "search":
		if ($cid) 
			$response = displayCatItems($cid);
		else 
			$response = getSearchResults($s);
		break;
	case "doAddToCart":
		$result = doAddToCart($_REQUEST['pid'], $_REQUEST['qty']);
		$response = json_encode($result);
		break;
	case "updateCartCount":
		$result = getCountItemsInCart();
		$response = json_encode($result);
		break;
	case "doUpdateCart":
		$result = doUpdateCart();
		$response = json_encode($result);
		break;
	case "getCart":
		$result = getCart();
		$response = json_encode($result);
		break;
	case "doDeleteFromCart":
		$result = doDeleteFromCart($item);
		$response = json_encode($result);
		break;
	case "order":
		$response = displayOrder();
		break;
	case "doProcessOrder":
		$response = doProcessOrder();
		break;
	case "postCalc":
		$response = getPostCalc($zip, $city);
		break;
	case "courierCalc":
		$response = getCourierCalc();
		break;
	case "cdekCalc":
		$response = getCdekCalc();
		break;
	case "doProcessPreorder":
		$response = doProcessPreorder();
		break;
	case "doAddReview":
		$response = addReview();
		break;
	case "payment": 
		$response = doPayment();
		break;
	default:
		switch($id){
			case route::idByName('index'):
				$response = displayIndexPage();
				break;
			case route::idByName('cat'):
				$response = processCatalogue();
				break;
			case route::idByName('hits'):
				$response = displayHits();
				break;
			case route::idByName('news'):
				$response = processNews();
				break;
			default: 
				$response = displayInnerPage($id);
		}
}

echo $response;
$db->close();