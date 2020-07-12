<?php
if (!defined('XOOPS_ROOT_PATH')) { exit(); }
$myfulldirname = dirname(__FILE__);
if (!preg_match('!/modules/([a-zA-Z0-9_\-]+)/blocks$!', $myfulldirname, $m)) {
	preg_match('!modules.([a-zA-Z0-9_\-]+).blocks$!', $myfulldirname, $m); }
$mydirname = $m[1];

require_once XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/include/lib.php';

if (!function_exists($mydirname.'_new')) {
	eval ('function b_'.$mydirname.'_new ($options) { return b_chapox_new_base("'.$mydirname.'", $options); }');
}
if (!function_exists($mydirname.'_new_edit')) {
	eval ('function b_'.$mydirname.'_new_edit ($options) { return b_chapox_new_edit_base($options); }');
}

if (!function_exists('b_chapox_new_base')) {
function b_chapox_new_base ($mydirname = '', $options) {
	$limit = ((intval($options[0]) > 0) ? intval($options[0]) : 5);
	$chapox = new chapox_outline($mydirname);
	$myrows = $chapox->get_sorted_contents('updated', 'DESC');
	// $myrows = $chapox->get_sorted_contents();
	$block = array('mydirname' => urlencode($mydirname));
	$i = 1;
	foreach ($myrows as $row) {
		if ($row['updated'] == 0) { break; }
		$block['outline'][] = $row;
		if ($i++ >= $limit) { break; }
	}
	return $block;
}
}

if (!function_exists('b_chapox_new_edit_base')) {
function b_chapox_new_edit_base ($options) {
	$form  = _MB_CHAPOX_NUMBER_OF_TITLES.'&nbsp;:<input type="text" name="options[]" value="'.$options[0].'" />';
	return $form;
}
}
?>