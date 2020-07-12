<?php
require_once "./_header.php";
require_once XOOPS_ROOT_PATH."/class/template.php";
require_once XOOPS_ROOT_PATH."/modules/${mydirname}/include/lib.php";

header ('Content-Type:text/html; charset='._CHARSET);
$tpl = new XoopsTpl();
$tpl->xoops_setCaching(2);
$tpl->xoops_setCacheTime(0);

//	Get Data from Database
$chapox = new chapox_outline($mydirname);
$outline = $chapox->get_outline();
$tpl->assign('outline', $outline);

//	Display Contents
$tpl->assign('mydirname', $mydirname);

$tpl->assign('xoops_pagetitle',	htmlspecialchars($xoopsModule->getVar('name')));
$tpl->assign('xoops_sitename',	htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
$tpl->assign('xoops_slogan',	htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES));

//$tpl->display(XOOPS_ROOT_PATH."/modules/${mydirname}/templates/${mydirname}_print.html");
$tpl->display("db:${mydirname}_print.html");
?>
