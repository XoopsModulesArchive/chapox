<?php
if (!defined('XOOPS_ROOT_PATH')) { exit(); }
$mydirname = basename(dirname(dirname(__FILE__)));

if (($mydirname != '') && !function_exists($mydirname.'_search')) {
	eval ('
	function '.$mydirname.'_search ($queryarray, $andor, $limit, $offset, $userid = 0) {
		return chapox_search_base("'.$mydirname.'", $queryarray, $andor, $limit, $offset, $userid);
	}
	');
}

if (!function_exists('chapox_search_base')) {

function chapox_search_base ($mydirname = 'chapox', $queryarray, $andor, $limit, $offset, $userid = 0) {
	if (!preg_match('/^chapox[\d]*$/', $mydirname)) { return ''; }
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$sql1 = "SELECT lid, title, updated, uid, mycontent, excerpt FROM ".$db->prefix("${mydirname}_contents");
	$sql2 = '';
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql2 .=	" ((mycontent LIKE '%${queryarray[0]}%')".
				" OR (excerpt LIKE '%${queryarray[0]}%')" .
				" OR (footnote LIKE '%${queryarray[0]}%')" .
				" OR (title LIKE '%${queryarray[0]}%'))";
		for($i=1; $i<$count; $i++){
			$sql2 .=	" $andor ";
			$sql2 .=	" ((mycontent LIKE '%${queryarray[$i]}%')".
					" OR (excerpt LIKE '%${queryarray[$i]}%')" .
					" OR (footnote LIKE '%${queryarray[$i]}%')" .
					" OR (title LIKE '%${queryarray[$i]}%'))";
		}
	}
	if ($userid != 0) {
		if ($sql2 != '') {
			$sql2 = " WHERE (uid = $userid) AND ($sql2)";
		} else {
			$sql2 = " WHERE (uid = $userid)";
		}
	} else {
		if ($sql2 != '') {
			$sql2 = " WHERE $sql2";
		} else {
			$sql2 = "";
		}
	}
	$sql = $sql1.$sql2." ORDER BY updated DESC";
	$result = $db->query($sql, $limit, $offset);
	$ret = array();
	$i = 0;
 	while($myrow = $db->fetchArray($result)){
		$ret[$i]['link']	= "content.php?lid=".$myrow['lid'];
		$ret[$i]['title']	= $myts->htmlSpecialChars($myrow['title']);;
		$ret[$i]['time']	= $myrow['updated'];
		$ret[$i]['uid']		= $myrow['uid'];
		if (isset($_GET['showcontext'])) {
			// $context = $myrow['mycontent'];
			$context = $myrow['excerpt'];
			$context = strip_tags($myts->displayTarea(strip_tags($context)));
			$ret[$i]['context'] = search_make_context($context,$queryarray);
		}
		$i++;
	}
	return $ret;
}

}
?>