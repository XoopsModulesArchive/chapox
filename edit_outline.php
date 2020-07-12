<?php
require_once './_header.php';

//	Check Permission
if (!$editpermission['outline']) {
	if ($uid == 0) { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_MUSTLOGINFIRST); exit(); }
		else { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_NOPERMTOEDIT); exit(); }
}

//	Ticket Handler
if (CHAPOX_USE_XCTOKEN) {
	$Token_handler = new XoopsMultiTokenHandler();
	$Ticket =& $Token_handler->create("${mydirname}outline", 2 * 60 * 60);
}

//	Get Data from Database
$chapox = new chapox_outline($mydirname);
$contents = $chapox->get_all_contents(100);

//	Template
include XOOPS_ROOT_PATH.'/header.php';
$xoopsTpl->assign('xoops_module_header',
	'<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$mydirname.'/chapox.css" />'."\n".
	$xoopsTpl->get_template_vars("xoops_module_header"));

$xoopsOption['template_main'] = "${mydirname}_edit_outline.html";
$xoopsTpl->assign('contents', $contents);
if (CHAPOX_USE_XCTOKEN) {
	$xoopsTpl->assign('ticket_hidden', $Ticket->getHtml());
} else {
	$xoopsTpl->assign('ticket_hidden', '');
}

include './_footer.php';
?>