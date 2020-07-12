<?php
include_once "../../../mainfile.php";
$myDirName = basename(dirname(dirname(__FILE__)));
$module_handler =& xoops_gethandler('module');
$module =& $module_handler->getByDirname($myDirName);
if (defined('XOOPS_CUBE_LEGACY')) {
header ('Location: '.XOOPS_URL.'/modules/legacy/admin/index.php?action=PreferenceEdit&confmod_id='.$module->getVar('mid'));
} else {
header ('Location: '.XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=showmod&mod='.$module->getVar('mid'));
}
?>