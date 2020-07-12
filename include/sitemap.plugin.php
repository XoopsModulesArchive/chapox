<?php
if (!defined('XOOPS_ROOT_PATH')) { exit(); }
$mydirname = basename(dirname(dirname(__FILE__)));

if (($mydirname != '') && !function_exists('b_sitemap_'.$mydirname)) {
	eval ('
	function b_sitemap_'.$mydirname.' () { return b_sitemap_chapox_base ("'.$mydirname.'"); }
	');
}

if (!function_exists("b_sitemap_chapox_base")) {

function b_sitemap_chapox_base ($mydirname = 'chapox') {
	global $xoopsModuleConfig;
	if (!preg_match('/^chapox[\d]*$/', $mydirname)) { return ''; }

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	$mymap = array();
	$sql =	"SELECT lid, title FROM ".$db->prefix("${mydirname}_contents").
		" WHERE layer_num = 1 ORDER BY layer1, layer2, layer3, layer4, lid";
	$res = $db->query($sql);
	$i = 0;
	while (list($lid, $title) = $db->fetchRow($res)) {
		$lid = intval($lid);
		$title = $myts->htmlSpecialChars($title);
		$mymap['parent'][$i]['id']	= $lid;
		$mymap['parent'][$i]['url']	= "content.php?lid=$lid";
		$mymap['parent'][$i]['title']	= $title;
		if ($xoopsModuleConfig["show_subcategoris"]) {
			$sql2 =	"SELECT lid, title FROM ".$db->prefix("${mydirname}_contents").
				" WHERE layer_num = 2 AND layer1 = $lid ORDER BY layer1, layer2, layer3, layer4, lid";
			$res2 = $db->query($sql2);
			$j = 0;
			while (list($lid2, $title2) = $db->fetchRow($res2)) {
				$lid2 = intval($lid2);
				$title2 = $myts->htmlSpecialChars($title2);
				$mymap['parent'][$i]['child'][$j]['id']		= $lid2;
				$mymap['parent'][$i]['child'][$j]['title']	= $title2;
				$mymap['parent'][$i]['child'][$j]['image']	= 2;
				$mymap['parent'][$i]['child'][$j]['url']	= "content.php?lid=$lid2";
				$j++;
			}
		}
		$i++;
	}
	return $mymap;
}

}
?>