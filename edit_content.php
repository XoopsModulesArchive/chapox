<?php
require_once './_header.php';

//	Check lid
if ($lid == 0) { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_PARAMETER); exit(); }

//	Check Permission
if (!$editpermission['content']) {
	if ($uid == 0) { redirect_header(XOOPS_URL."/modules/${mydirname}/content.php?lid=$lid", 3, CHAPOX_ERR_MUSTLOGINFIRST); exit(); }
		else { redirect_header(XOOPS_URL."/modules/${mydirname}/content.php?lid=$lid", 3, CHAPOX_ERR_NOPERMTOEDIT); exit(); }
}

//	Ticket Handler
if (CHAPOX_USE_XCTOKEN) {
	$Token_handler = new XoopsMultiTokenHandler();
	$Ticket =& $Token_handler->create("${mydirname}edit", 2 * 60 * 60);
}

//	Get Data from Database
$chapox = new chapox_outline($mydirname);
if (!$chapox->content_exists($lid)) {
	redirect_header(XOOPS_URL."/modules/${mydirname}/", 0, CHAPOX_ERR_NOCONTENT); exit();
}
$content = $chapox->get_content($lid);

//	Check Freezed
if (($content['status'] == CHAPOX_FREEZE_STATUS) && ($content['uid'] != $uid) && (!$xoopsUser->IsAdmin())) {
	redirect_header(XOOPS_URL."/modules/${mydirname}/content.php?lid=$lid", 3, CHAPOX_ERR_FREEZED); exit();
}

//	Template
include XOOPS_ROOT_PATH.'/header.php';
$xoopsTpl->assign('xoops_module_header',
	'<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$mydirname.'/chapox.css" />'."\n".
	$xoopsTpl->get_template_vars("xoops_module_header"));
$xoopsOption['template_main'] = "${mydirname}_edit_content.html";

$xoopsTpl->assign('content', $content);
$xoopsTpl->assign('StatusList', $StatusList);
if (CHAPOX_USE_XCTOKEN) {
	$xoopsTpl->assign('ticket_hidden', $Ticket->getHtml());
} else {
	$xoopsTpl->assign('ticket_hidden', '');
}

include './_footer.php';
?>