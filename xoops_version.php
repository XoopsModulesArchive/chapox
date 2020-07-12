<?php
//  ------------------------------------------------------------------------ //
//                        CHAPOX - XOOPS Module                              //
//                   Copyright (c) 2005 taquino.net                          //
//                     <http://xoops.taquino.net/>                           //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------- //

if (!defined('XOOPS_ROOT_PATH')) { exit(); }
$mydirname = basename(dirname(__FILE__));
if (!preg_match('/^chapox([\d]?)$/', $mydirname, $matches)) echo ("invalid dirname: ".htmlspecialchars($mydirname).'<br />');

$modversion['name']		= strtoupper($mydirname);
$modversion['version']		= '0.07b';
$modversion['description']	= 'CHAPOX by Taq';
$modversion['credits']		= "Taquino";
$modversion['author']		= "http://xoops.taquino.net/";
$modversion['help']		= '';
$modversion['license']		= "GPL";
$modversion['official']		= 0;
$modversion['image']		= "images/${mydirname}.png";
$modversion['dirname']		= $mydirname;
//$modversion['onInstall']	= 'admin/xoops_install.php';
//$modversion['onIUninstall']	= 'admin/xoops_uninstall.php';
$modversion['onUpdate']	= 'admin/xoops_update.php';

// Templates
$modversion['templates'][1]['file']		= "${mydirname}_index.html";
$modversion['templates'][1]['description']	= 'INDEX PAGE';
$modversion['templates'][2]['file']		= "${mydirname}_content.html";
$modversion['templates'][2]['description']	= 'ARTICLE PAGE';
$modversion['templates'][3]['file']		= "${mydirname}_edit_content.html";
$modversion['templates'][3]['description']	= 'EDIT TEMPLATE';
$modversion['templates'][4]['file']		= "${mydirname}_edit_outline.html";
$modversion['templates'][4]['description']	= 'EDIT OUTLIE TEMPLATE';
$modversion['templates'][5]['file']		= "${mydirname}_renumber.html";
$modversion['templates'][5]['description']	= 'PREVIEW (CONFIRM) RENUMBER TEMPLATE';
$modversion['templates'][6]['file']		= "${mydirname}_stat.html";
$modversion['templates'][6]['description']	= 'STATUS PAGE';
$modversion['templates'][7]['file']		= "${mydirname}_print.html";
$modversion['templates'][7]['description']	= 'STATUS PAGE';

// Sql files
$modversion['sqlfile']['mysql']	= "sql/mysql_${mydirname}.sql";
// Tables created by sql file (without prefix!)
$modversion['tables'][0]	= "${mydirname}_contents";

// Admin things
$modversion['hasAdmin']		= 1;
$modversion['adminindex']	= "admin/index.php";
$modversion['adminmenu']	= "admin/menu.php";

// Search
$modversion['hasSearch']	= 1;
$modversion['search']['file']	= "include/search.inc.php";
$modversion['search']['func']	= "${mydirname}_search";

// Main contents
$modversion['hasMain']	= 1;
// Sub contents
$module_handler	=& xoops_gethandler('module');
$module	=& $module_handler->getByDirname($mydirname);
if(is_object($module)) {
	$i = 1;
	$config_handler	=& xoops_gethandler('config');
	$config		=& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
	if ($config['SubMenu'] ) {
		$myts	=& MyTextSanitizer::getInstance();
		$db	=& Database::getInstance();
		$res	= $db->query("SELECT lid, title, layer1 FROM ".$db->prefix("${mydirname}_contents")." WHERE layer_num = 1 ORDER BY layer1, lid");
		while (list($lid, $title, $layer1) = $db->fetchRow($res)) {
			$modversion['sub'][$i] = array('name' => ' - '.$myts->htmlSpecialChars($title), 'url' => "content.php?lid=$lid");
			$i++;
		}
	}
}

// Comments
$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'CHAPOX';
$modversion['comments']['pageName'] = 'index.php';
//$modversion['comments']['extraParams'] = array('uid');
$modversion['comments']['callbackFile'] = 'include/comment_functions.php';
$modversion['comments']['callback']['update'] = $mydirname.'_com_update';
$modversion['comments']['callback']['approve'] = $mydirname.'_com_approve';

// Config Settings
// Config Settings  0 -> Dummy
$modversion['config'][0]['name']	= 'Dummy';
$modversion['config'][0]['title']	= '_MI_CHAPOX_MYDIRNAME_TITLE';
$modversion['config'][0]['description']	= '_MI_CHAPOX_MYDIRNAME_DESC';
$modversion['config'][0]['formtype']	= 'select';
$modversion['config'][0]['valuetype']	= 'text';
$modversion['config'][0]['options']	= array($mydirname => $mydirname);
$modversion['config'][0]['default']	= $mydirname;

$member_handler =& xoops_gethandler('member');
$groups =& $member_handler->getGroups();
$myts =& MyTextSanitizer::getInstance();
$gcount = count($groups);
$gid_array = array();
for ($i = 0; $i < $gcount; $i++) {
	if ($groups[$i]->getVar('groupid') != 3) {
		$gid_array[$myts->htmlSpecialChars($groups[$i]->getVar('name'))] =$groups[$i]->getVar('groupid');
	}
}
$modversion['config'][1]['name']	= 'OutlineEditorGroup';
$modversion['config'][1]['title']	= '_MI_CHAPOX_CONFIG1_TITLE';
$modversion['config'][1]['description']	= '_MI_CHAPOX_CONFIG1_DESC';
$modversion['config'][1]['formtype']	= 'select';
$modversion['config'][1]['valuetype']	= 'int';
$modversion['config'][1]['options']	= $gid_array;
$modversion['config'][1]['default']	= XOOPS_GROUP_ADMIN;

$modversion['config'][2]['name']	= 'ContentEditorGroup';
$modversion['config'][2]['title']	= '_MI_CHAPOX_CONFIG2_TITLE';
$modversion['config'][2]['description']	= '_MI_CHAPOX_CONFIG2_DESC';
$modversion['config'][2]['formtype']	= 'select';
$modversion['config'][2]['valuetype']	= 'int';
$modversion['config'][2]['options']	= $gid_array;
$modversion['config'][2]['default']	= XOOPS_GROUP_ADMIN;

$modversion['config'][3]['name']	= 'SubMenu';
$modversion['config'][3]['title']	= '_MI_CHAPOX_CONFIG3_TITLE';
$modversion['config'][3]['description']	= '_MI_CHAPOX_CONFIG3_DESC';
$modversion['config'][3]['formtype']	= 'yesno';
$modversion['config'][3]['valuetype']	= 'int';
$modversion['config'][3]['default']	= 0;

// Notification
$modversion['hasNotification'] = 0;

// Blocks
$modversion['blocks'][1]['file']	= "chapox_new.php";
$modversion['blocks'][1]['name']	= $mydirname._MI_CHAPOX_BLOCK_NEW_TITLE;
$modversion['blocks'][1]['description']	= $mydirname._MI_CHAPOX_BLOCK_NEW_DESC;
$modversion['blocks'][1]['show_func']	= "b_${mydirname}_new";
$modversion['blocks'][1]['edit_func']	= "b_${mydirname}_new_edit";
$modversion['blocks'][1]['template']	= $mydirname."_list.html";
$modversion['blocks'][1]['options']	= "5";
$modversion['blocks'][1]['can_clone']	= false;
?>