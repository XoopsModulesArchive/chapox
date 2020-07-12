<?php
require_once './_header.php';

//	Check Permission
if ((!$editpermission['outline']) && (!$editpermission['content'])) {
	if ($uid == 0) { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_MUSTLOGINFIRST); exit(); }
		else { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_NOPERMTOEDIT); exit(); }
}

//	Get Sorting Parameters
$sort  = (isset($_GET['sort'])  ? $_GET['sort']  : '');
$order = (isset($_GET['order']) ? $_GET['order'] : 'DESC');

//	Get Data from Database
$chapox = new chapox_outline($mydirname);
if ($sort != '') { $contents = $chapox->get_sorted_contents($sort, $order); }
	else { $contents = $chapox->get_all_contents(); }

//	Template
include XOOPS_ROOT_PATH.'/header.php';
$xoopsTpl->assign('xoops_module_header',
	'<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$mydirname.'/chapox.css" />'."\n".
	$xoopsTpl->get_template_vars("xoops_module_header"));

$xoopsOption['template_main'] = "${mydirname}_stat.html";
$xoopsTpl->assign('contents', $contents);

include './_footer.php';
?>