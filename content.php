<?php
require_once './_header.php';

//	Get My Content
$chapox = new chapox_outline($mydirname);
if (!$chapox->content_exists($lid)) {
	redirect_header(XOOPS_URL."/modules/${mydirname}/", 0, 'CHAPOX_ERR_NOCONTENT');
	exit();
}
$content = $chapox->get_content($lid);

//	Get My Outline
$outline = $chapox->get_outline($lid, $content['layer_num']);

//	Get Prev. & Next
$next = $chapox->get_next_content($lid);
$prev = $chapox->get_prev_content($lid);

//	Template
include XOOPS_ROOT_PATH.'/header.php';
$xoopsOption['template_main'] = "${mydirname}_content.html";

$xoopsTpl->assign('xoops_module_header',
	'<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$mydirname.'/chapox.css" />'."\n".
	$xoopsTpl->get_template_vars("xoops_module_header"));

//	Edit Permission Check
$myeditpermission = $editpermission['content'];
if ($myeditpermission && $content['status'] == CHAPOX_FREEZE_STATUS) {
	if (!isset($xoopsUser)) { $myeditpermission = false; }
	if (($content['uid'] != $uid) && !$xoopsUser->IsAdmin()) { $myeditpermission = false; }
}
$xoopsTpl->assign('myeditpermission', $myeditpermission);

$xoopsTpl->assign('lid', $lid);
$xoopsTpl->assign('outline', $outline);
$xoopsTpl->assign('content', $content);
$xoopsTpl->assign('next', $next);
$xoopsTpl->assign('prev', $prev);

//	Breadcrumbs List
$myts =& MyTextSanitizer::getInstance();
$br_list = '&nbsp;&gt;&nbsp;';
if (($content['part'] != 0) && ($content['layer_num'] > 1)) {
	$br_list .= $chapox->get_content_link($content['part'], 0, 0, 0);
	if (($content['chapter'] != 0) && ($content['layer_num'] > 2)) {
		$br_list .= '&nbsp;&gt;&nbsp;'.$chapox->get_content_link($content['part'], $content['chapter'], 0, 0);
		if (($content['section'] != 0) && ($content['layer_num'] > 3)) {
			$br_list .= '&nbsp;&gt;&nbsp;'.$chapox->get_content_link($content['part'], $content['chapter'], $content['section'], 0);
		}
	}
}
$xoopsTpl->assign('br_list', $br_list);

include XOOPS_ROOT_PATH.'/include/comment_view.php';

include './_footer.php';
?>