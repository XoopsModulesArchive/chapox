<?php
require_once './_header.php';

//	Get Data from Database
$chapox = new chapox_outline($mydirname);
$outline = $chapox->get_outline();

//	Template
include XOOPS_ROOT_PATH.'/header.php';
$xoopsOption['template_main'] = "${mydirname}_index.html";

$xoopsTpl->assign('xoops_module_header',
	'<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$mydirname.'/chapox.css" />'."\n".
	$xoopsTpl->get_template_vars("xoops_module_header"));

$xoopsTpl->assign('outline', $outline);

//	Comment
$_GET['CHAPOX'] = (isset($_GET['CHAPOX']) ? intval($_GET['CHAPOX']) : 1);
include XOOPS_ROOT_PATH.'/include/comment_view.php';

include_once './_footer.php';
?>