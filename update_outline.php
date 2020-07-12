<?php
require_once './_header.php';
$myts =& MyTextSanitizer::getInstance();

//	Check Permission
if (!$editpermission['outline']) {
	if ($uid == 0) { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_MUSTLOGINFIRST); exit(); }
		else { redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_NOPERMTOEDIT); exit(); }
}

//	Ticket Handler
if (CHAPOX_USE_XCTOKEN) {
	$Token_handler = new XoopsMultiTokenHandler();
	$TokenValid = $Token_handler->autoValidate("${mydirname}outline");
	if (!$TokenValid) {
		redirect_header(XOOPS_URL."/modules/${mydirname}/edit_outline.php", 3, CHAPOX_ERR_INVALIDTCKET); exit();
	}
}

//	Check Command
$op = (isset($_POST['op']) ? intval($_POST['op']) : '');
if ($op != 'update_outline') {
	redirect_header(XOOPS_URL."/modules/${mydirname}/", 3, CHAPOX_ERR_UNKNOWN); exit();
}

//	Get Host Address
if (isset($_SERVER['REMOTE_HOST']) && ($_SERVER['REMOTE_HOST'] != '')) {
	$host = addslashes($_SERVER['REMOTE_HOST']);
} else {
	$host = addslashes($_SERVER['REMOTE_ADDR']);
}

//	Update database
$NowTime = time();
foreach ($_POST as $key => $val) {
	if (preg_match('/^lid([\d]+)$/', $key, $m1)) {
		$myid = intval($m1[1]); if ($myid == 0) { continue; }
		$lid = intval($val);
		$delete = (isset($_POST['delete'.$myid]) ? intval($_POST['delete'.$myid]) : 0);
		if ($delete == $myid) {
			$sql = "SELECT mystatus FROM ".$xoopsDB->prefix("${mydirname}_contents")." WHERE lid = $lid";
			list($mystatus) = $xoopsDB->fetchRow($xoopsDB->query($sql));
			if ($mystatus != CHAPOX_FREEZE_STATUS) {
				$sql = "DELETE FROM ".$xoopsDB->prefix("${mydirname}_contents")." WHERE lid = $lid";
				$xoopsDB->query($sql);
			}
		} else {
			if (!isset($_POST['layers'.trim(strval($myid))])) { continue; }
			$org_layers = $myts->stripSlashesGPC($_POST['layers'.trim(strval($myid))]);
			$layers = $org_layers;
			if (function_exists('mb_convert_kana')) {
				$layers = mb_convert_kana($org_layers, "asKV", _CHARSET);
			}
			preg_match('/^([\d]+)[\D]+([\d]+)[\D]+([\d]+)[\D]+([\d]+)/', '0'.$layers.'-0-0-0', $m2);
			$layer = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
			$layer_num = 4;
			for ($i = 1; $i < 5; $i++) {
				$layer[$i] = (isset($m2[$i]) ? intval($m2[$i]) : 0);
				if ($layer[$i] == 0) { $layer_num = $i - 1; break; }
			}
			if (!isset($_POST['title'.trim(strval($myid))])) { continue; }
			$title = addslashes($myts->stripSlashesGPC($_POST['title'.trim(strval($myid))]));
			if (($i == 1) && ($title == '')) { continue; }
			if ($title == '') { $title = addslashes("(No Title : $org_layers)"); }
			if ($lid == 0) {
				$sql =	"INSERT INTO ".$xoopsDB->prefix("${mydirname}_contents")." ".
					"(layer_num, layer1,layer2,layer3,layer4, title, uid, uname, posted, mystatus, host)".
					" VALUES ($layer_num, ${layer[1]}, ${layer[2]}, ${layer[3]}, ${layer[4]},".
					" '$title', $uid, '".addslashes($uname)."', $NowTime, 'Blank', '$host')";
				$xoopsDB->query($sql);
			} else {
				$sql =	"UPDATE ".$xoopsDB->prefix("${mydirname}_contents")." SET ".
					"layer_num = $layer_num, layer1 = ${layer['1']}, layer2 = ${layer['2']}, ".
					"layer3 = ${layer['3']}, layer4 = ${layer['4']}, ".
					"title = '$title' WHERE lid = $lid";
				$xoopsDB->query($sql);
			}
		}
	}
}

//	End of job
redirect_header(XOOPS_URL."/modules/${mydirname}/edit_outline.php", 0, CHAPOX_OK_EDITOUTLINE);
exit();
?>