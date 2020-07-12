<?php
if (!defined('XOOPS_ROOT_PATH')) { exit(); }
$mydirname = basename(dirname(dirname(__FILE__)));

if (!function_exists('xoops_module_update_'.$mydirname)) {
	eval ('
	function xoops_module_update_'.$mydirname.' (&$module, $prev_version)
		{ return xoops_module_update_chapox_base ("'.$mydirname.'", &$module, $prev_version); }
	');
}

if (!function_exists('xoops_module_update_chapox_base')) {
function xoops_module_update_chapox_base ($mydirname, &$module, $prev_version) {
	global $xoopsDB;
	if ($prev_version < 5) {
		$sql =	"ALTER TABLE ".$xoopsDB->prefix($mydirname.'_contents').
			" CHANGE status mystatus varchar(255) NOT NULL default ''";
		$xoopsDB->queryF($sql);
	}
	return true;
}
}
?>