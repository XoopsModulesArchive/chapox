<?php
require_once './_header.php';
$myts =& MyTextSanitizer::getInstance();

//	Check Permission
if (!$editpermission['outline']) {
	if ($uid == 0) { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_MUSTLOGINFIRST); exit(); }
		else { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_NOPERMTOEDIT); exit(); }
}

//	Get Parameters
$step  = (isset($_POST['step'])  ? intval($_POST['step'])  : 1);
$op = (isset($_POST['op']) ? $_POST['op'] : '');

//	Ticket Handler
$Token_handler = new XoopsSingleTokenHandler();
$TokenValid = $Token_handler->autoValidate("${mydirname}renumber");
$Ticket =& $Token_handler->create("${mydirname}renumber", 600);

//	Get Data from Database
$chapox = new chapox_outline($mydirname);
$contents = $chapox->get_all_contents();

//	Renumber
$renumbered = chapox_renumber($contents, $step);

//	Update database
if ($op == 'renumber_outline_go') {
	if (!$TokenValid) {
		redirect_header(XOOPS_URL."/modules/${mydirname}/edit_outline.php", 3, CHAPOX_ERR_INVALIDTCKET);
		exit();
	}
	foreach ($renumbered as $content) {
		$lid = $content['lid'];
		if ($lid != 0) {
			$sql =	"UPDATE ".$xoopsDB->prefix("${mydirname}_contents")." SET".
				" layer1 = ${content['new_layer1']}, layer2 = ${content['new_layer2']},".
				" layer3 = ${content['new_layer3']}, layer4 = ${content['new_layer4']} WHERE lid = $lid";
			$xoopsDB->query($sql);
		}
	}
	//	End of job
	redirect_header(XOOPS_URL."/modules/${mydirname}/edit_outline.php", 0, CHAPOX_OK_EDITOUTLINE);
	exit();
}

//	Template for preview
include XOOPS_ROOT_PATH.'/header.php';
$xoopsTpl->assign('xoops_module_header',
	'<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$mydirname.'/chapox.css" />'."\n".
	$xoopsTpl->get_template_vars("xoops_module_header"));

$xoopsOption['template_main'] = "${mydirname}_renumber.html";
$xoopsTpl->assign('step', $step);
$xoopsTpl->assign('contents', $renumbered);
$xoopsTpl->assign('ticket_hidden', $Ticket->getHtml());

require_once './_footer.php';
?>