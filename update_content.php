<?php
require_once './_header.php';
$myts =& MyTextSanitizer::getInstance();

if ($lid == 0) { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_PARAMETER); exit(); }

//	Check Permission
if (!$editpermission['content']) {
	if ($uid == 0) { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_MUSTLOGINFIRST); exit(); }
		else { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_NOPERMTOEDIT); exit(); }
}

//	Ticket Handler
if (CHAPOX_USE_XCTOKEN) {
	$Token_handler = new XoopsMultiTokenHandler();
	$TokenValid = $Token_handler->autoValidate("${mydirname}edit");
	if (!$TokenValid) {
		redirect_header(XOOPS_URL."/modules/${mydirname}/edit_content.php?lid=$lid", 3, CHAPOX_ERR_INVALIDTCKET); exit();
	}
}

//	Check command
$op = (isset($_POST['op']) ? intval($_POST['op']) : '');
if ($op != 'update_content') {
	redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_UNKNOWN); exit();
}

//	Check Freezed
$sql = "SELECT * FROM ".$xoopsDB->prefix("${mydirname}_contents")." WHERE lid = $lid";
$DB_DATA = $xoopsDB->fetchArray($xoopsDB->query($sql));
if (($DB_DATA['mystatus'] == CHAPOX_FREEZE_STATUS) && ($DB_DATA['uid'] != $uid) && (!$xoopsUser->IsAdmin())) {
	redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_FREEZED); exit();
}

//	Get Posted Data
$IN_DATA = array();
$IN_DATA['status'] =	(isset($_POST['status']) ? $myts->stripSlashesGPC($_POST['status']) : '');
$IN_DATA['title'] =	(isset($_POST['title']) ? $myts->stripSlashesGPC($_POST['title']) : '');
$IN_DATA['excerpt'] =	(isset($_POST['excerpt']) ? $myts->stripSlashesGPC($_POST['excerpt']) : '');
$IN_DATA['mycontent'] =	(isset($_POST['mycontent']) ? $myts->stripSlashesGPC($_POST['mycontent']) : '');
$IN_DATA['footnote'] =	(isset($_POST['footnote']) ? $myts->stripSlashesGPC($_POST['footnote']) : '');

//	AddSlashes
$SLASHED = array('uname' => addslashes($uname));
foreach ($IN_DATA as $key => $val) {
	$SLASHED[$key] = addslashes($val);
}
//	Get Host Address
if (isset($_SERVER['REMOTE_HOST']) && ($_SERVER['REMOTE_HOST'] != '')) {
	$host = addslashes($_SERVER['REMOTE_HOST']);
} else {
	$host = addslashes($_SERVER['REMOTE_ADDR']);
}

//	Update Database
$NowTime = time();
$sql =	"UPDATE ".$xoopsDB->prefix("${mydirname}_contents")." SET ".
	"excerpt = '${SLASHED['excerpt']}', mycontent = '${SLASHED['mycontent']}', footnote = '${SLASHED['footnote']}', ".
	"mystatus = '${SLASHED['status']}', updated = $NowTime, ".
	"uid = $uid, uname = '${SLASHED['uname']}', host = '$host' WHERE lid = $lid";
$xoopsDB->query($sql);

//	Finish Job
redirect_header(XOOPS_URL."/modules/${mydirname}/content.php?lid=$lid", 0, CHAPOX_OK_EDITCONTENT);
exit();
?>