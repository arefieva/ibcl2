<?php

function validateInput() { // validate input against malicious activity
	if($_REQUEST['dir_prefix']!=""||$_REQUEST['level']!="") return false;

	global $id, $action;
	if(is_array($id)||is_array($action)) return false;
	if(strstr($action, "../")||strstr($action, chr(0))) return false;
	if($id!=""&&!is_numeric($id)) return false;
	if($_REQUEST['c']!=""&&!is_numeric($_REQUEST['c'])) return false;
	if($_REQUEST['cid']!=""&&!is_numeric($_REQUEST['cid'])&&$_REQUEST['cid']!="special"&&$_REQUEST['cid']!="new") return false;
	if($_REQUEST['pid']!=""&&!is_numeric($_REQUEST['pid'])) return false;

	if($action==""&&!is_numeric($id)) return false;

	return true;
}

if(!validateInput()) die("Request blocked.");

?>