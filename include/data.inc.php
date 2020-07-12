<?php
if (!defined('XOOPS_ROOT_PATH')) { exit(); }
$mydirname = basename(dirname(dirname(__FILE__)));

if (($mydirname != '') && !function_exists($mydirname.'_new')) {
	eval ('
	function '.$mydirname.'_new ($limit=0, $offset=0) { return chapox_new_base ("'.$mydirname.'", $limit, $offset); }
	');
}
if (($mydirname != '') && !function_exists($mydirname.'_num')) {
	eval ('
	function '.$mydirname.'_num () { return chapox_num_base ("'.$mydirname.'"); }
	');
}
if (($mydirname != '') && !function_exists($mydirname.'_data')) {
	eval ('
	function '.$mydirname.'_data ($limit=0, $offset=0) { return chapox_data_base ("'.$mydirname.'", $limit, $offset); }
	');
}

require_once XOOPS_ROOT_PATH."/modules/${mydirname}/include/lib.php";

if (!function_exists('chapox_new_base')) {
function chapox_new_base ($mydirname, $limit=0, $offset=0) {
	$chapox = new chapox_outline($mydirname);
	$contents = $chapox->get_sorted_contents();
	$ret = array();
	$i = 0;
	foreach ($contents as $row) {
		if ($i < $offset) { continue; }
		$ret[$i]['id']		= $row['lid'];
		$ret[$i]['link']	= XOOPS_URL."/modules/${mydirname}/content.php?lid=".$row['lid'];
		//$ret[$i]['cat_link']	= '';
		//$ret[$i]['cat_name']	= '';
		$ret[$i]['title']	= $row['title'];
		$ret[$i]['time']	= $row['posted'];
		$ret[$i]['modified']	= $row['updated'];
		$ret[$i]['description']	= $row['excerpt'];
		//$ret[$i]['hits']	= 0;
		//$ret[$i]['replies']	= 0;
		$ret[$i]['uid']		= $row['uid'];
		//$ret[$i]['image']	= '';
		//$ret[$i]['width']	= 0;
		//$ret[$i]['height']	= 0;
		$i++;
		if ($i >= $limit + $offset) { break; }
	}
	return $ret;
}
}

if (!function_exists('chapox_num_base')) {
function chapox_num_base ($mydirname) {
	global $xoopsDB;
	$res = $xoopsDB->query("SELECT COUNT(lid) FROM ".$xoopsDB->prefix(addslashes($mydirname)."_contents")."");
	if ($xoopsDB->getRowsNum($res) == 0) {
		return 0;
	} else {
		list($num) = $xoopsDB->fetchRow($res);
		return intval($num);
	}
}
}

if (!function_exists('chapox_data_base')) {
function chapox_data_base ($mydirname, $limit=0, $offset=0) {
	global $xoopsDB;
	$sql = "SELECT * FROM ".$xoopsDB->prefix(addslashes($mydirname)."_contents")." ORDER BY updated DESC, posted DESC";
	$chapox = new chapox_outline($mydirname);
	$contents = $chapox->get_sorted_contents();
	$ret = array();
	$i = 0;
	foreach ($contents as $row) {
		if ($i < $offset) { continue; }
		$ret[$i]['id']		= $row['lid'];
		$ret[$i]['link']	= XOOPS_URL."/modules/${mydirname}/content.php?lid=".$row['lid'];
		$ret[$i]['title']	= $row['title'];
		$ret[$i]['time']	= $row['updated'];
		$i++;
		if ($i >= $limit + $offset) { break; }
	}
	return $ret;
}
}
?>