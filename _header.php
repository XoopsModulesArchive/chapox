<?php
require_once '../../mainfile.php';
$mydirname = basename(dirname(__FILE__));

//	Check Directory naming
if (!preg_match('/^chapox([\d]?)$/', $mydirname, $matches))
	{ redirect_header(XOOPS_URL, 3, "invalid dirname: ".htmlspecialchars($mydirname)); exit(); }

//	Require CHAPOX library
require_once XOOPS_ROOT_PATH."/modules/${mydirname}/include/lib.php";

//	Use XoppsCube Ticket or not
if (!defined('CHAPOX_USE_XCTOKEN')) {
	if (class_exists('XoopsToken')) {
		define ('CHAPOX_USE_XCTOKEN', true);
	} else {
		define ('CHAPOX_USE_XCTOKEN', false);
	}
}

//	Status list
define ('CHAPOX_FREEZE_STATUS', 'Freeze');
$StatusList = array('Draft', 'Publish', CHAPOX_FREEZE_STATUS);

//	Get lid
$lid = 0;
$lid = (isset($_GET['lid']) ? intval($_GET['lid']) : 0);
if ($lid == 0) { $lid = (isset($_POST['lid']) ? intval($_POST['lid']) : 0); }

//	Get User
if (!is_object($xoopsUser)) { $uid = 0; $uname = CHAPOX_GUESTNAME; }
else { $uid = $xoopsUser->getVar('uid'); $uname = $xoopsUser->getVar('uname'); }

//	Permission
$P = $xoopsModuleConfig['OutlineEditorGroup'];
$editpermission['outline'] = false;
if	($P == XOOPS_GROUP_ANONYMOUS)	{ $editpermission['outline'] = true; }
elseif	(!is_object($xoopsUser))	{ $editpermission['outline'] = false; }
elseif	($xoopsUser->IsAdmin())		{ $editpermission['outline'] = true; }
elseif	($P == XOOPS_GROUP_USERS)	{ $editpermission['outline'] = true; }
else	{ foreach ($xoopsUser->getGroups() as $G) { if ($P == $G) { $editpermission['outline'] = true; } } }
$P = $xoopsModuleConfig['ContentEditorGroup'];
$editpermission['content'] = false;
if	($P == XOOPS_GROUP_ANONYMOUS)	{ $editpermission['content'] = true; }
elseif	(!is_object($xoopsUser))	{ $editpermission['content'] = false; }
elseif	($xoopsUser->IsAdmin())		{ $editpermission['content'] = true; }
elseif	($P == XOOPS_GROUP_USERS)	{ $editpermission['content'] = true; }
else	{ foreach ($xoopsUser->getGroups() as $G) { if ($P == $G) { $editpermission['content'] = true; } } }
?>