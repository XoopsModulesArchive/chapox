<?php
if (!defined('XOOPS_ROOT_PATH')) { exit(); }

if (!defined('CHAPOX_LIB_LOADED')) {
define ('CHAPOX_LIB_LOADED', true);

require_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

//	Text Render & Sanitize
function chapox_displayTarea ($str = '') {
	$myts =& MyTextSanitizer::getInstance();
	// AllowHTML, AllowSmileys, AllowXCode, AllowInlineImages, ConvertLinebreaks
	$str = $myts->displayTarea($str,0,1,1,1,1);
	return $str;
}

class chapox_outline {
	var $outline, $titles, $mydirname;
	function chapox_outline ($mydirname = 'chapox') {
		global $xoopsDB;
		if (isset($this->mydirname) && ($this->mydirname == $mydirname)) { return; }
		$this->mydirname = $mydirname;
		$this->outline = array();
		$this->titles = array();
		if (!preg_match('/^chapox[\d]*$/', $mydirname)) { return; }
		$sql = "SELECT * FROM ".$xoopsDB->prefix("${mydirname}_contents")." WHERE mystatus NOT LIKE 'Backup%' ORDER BY layer1,layer2,layer3,layer4,lid";
		$res = $xoopsDB->query($sql);
		$i = 0;
		while ($d = $xoopsDB->fetchArray($res)) {
			$this->outline[$i] = $d;
			$i++;
			$lid = intval($d['lid']);
			$this->titles[$lid] = $d['title'];
		}
		return;
	}
	function get_outline ($lid = 0, $filter_layer = 0) {
		$lid = intval($lid);
		$filter_layer = intval($filter_layer);
		$c = 0;
		$o = array();
		$layer = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
		if ($lid != 0) {
			$m = $this->get_content($lid);
			for ($i = 1; $i < 5; $i++) { $layer[$i] = $m[sprintf('layer%1d', $i)]; }
			foreach ($this->outline as $d) {
				$f = true;
				for ($i = 1; $i < 5; $i++) {
					if (($layer[$i] != 0) && ($layer[$i] != $d[sprintf('layer%1d', $i)])) { $f = false; }
				}
				if ($f && ($d['layer_num'] > $filter_layer)) {
					$o[$c] = chapox_db2display($d);
					$c++;
				}
			}
		} else {
			foreach ($this->outline as $d) {
				$o[$c] = chapox_db2display($d);
				$c++;
			}
		}
		return array('count' => $c, 'contents' => $o);
	}
	function get_all_contents ($i = 1) {
		$i = intval($i);
		$c = array();
		foreach ($this->outline as $d) {
			$c[$i] = chapox_db2display($d);
			$c[$i]['myid'] = $i;
			$i++;
		}
		return $c;
	}
	function get_sorted_contents ($field_name = '', $order = '') {
		if (($order == '') && ($field_name == '')) { $order = 'DESC'; }
		$SortOrder = (preg_match('/DESC/i', $order) ? SORT_DESC : SORT_ASC);
		$c = array(); $d = array();
		foreach ($this->outline as $key => $row) {
			if ($field_name == '') {
				$sort[$key]  = max($row['posted'], $row['updated']);
			} else {
				if (!isset($row[$field_name])) { return false; }
				$sort[$key]  = $row[$field_name];
			}
			$c[$key] = chapox_db2display($row);
			$d[$key] = $key;
		}
		array_multisort($sort, $SortOrder, $d, SORT_ASC, $c);
		return $c;
	}
	function get_content ($lid = 0) {
		foreach ($this->outline as $d) {
			if ($d['lid'] == $lid) { return chapox_db2display($d); }
		}
		return '';
	}
	function get_next_content ($lid = 0) {
		$o = array('lid' => 0);
		foreach ($this->outline as $d) {
			if ($o['lid'] == $lid) { return chapox_db2display($d); }
			$o = $d;
		}
		return array('lid' => 0);
	}
	function get_prev_content ($lid = 0) {
		$o = array('lid' => 0);
		$PrevExists = false;
		foreach ($this->outline as $d) {
			if ($PrevExists && ($d['lid'] == $lid)) { return chapox_db2display($o); }
			$o = $d; $PrevExists = true;
		}
		return array('lid' => 0);
	}
	function content_exists ($lid = 0) {
		foreach ($this->outline as $d) {
			if ($d['lid'] == $lid) { return true; }
		}
		return false;
	}
	function last_updated () {
		$lid = 0;
		$max_updated = 0;
		foreach ($this->outline as $d) {
			if ($d['updated'] > $max_updated) { $max_updated = $d['updated']; $lid = $d['lid']; }
		}
		return $lid;
	}
	function get_content_link ($part = 0, $chapter = 0, $section = 0, $subsection = 0) {
		$myts =& MyTextSanitizer::getInstance();
		foreach ($this->outline as $d) {
			if ((intval($d['layer1']) == $part) && (intval($d['layer2']) == $chapter) && (intval($d['layer3']) == $section) && (intval($d['layer4']) == $subsection)) {
				return	'<a href="content.php?lid='.intval($d['lid']).'">'.
					$myts->htmlSpecialChars($d['title']).'</a>';
			}
		}
		return '';
	}
}

function chapox_db2display ($DB_DATA) {
	$myts =& MyTextSanitizer::getInstance();
	$content = array();
	if (!is_array($DB_DATA)) { return $content; }
	$content['layer_num'] =		intval($DB_DATA['layer_num']);
	$content['indent'] =		$content['layer_num'];
	$content['lid'] =		intval($DB_DATA['lid']);
	$content['layer1'] =		intval($DB_DATA['layer1']);
	$content['layer2'] =		intval($DB_DATA['layer2']);
	$content['layer3'] =		intval($DB_DATA['layer3']);
	$content['layer4'] =		intval($DB_DATA['layer4']);
	$content['part'] =		$content['layer1'];
	$content['chapter'] =		$content['layer2'];
	$content['section'] =		$content['layer3'];
	$content['subsection'] =	$content['layer4'];
	$content['p_c_s_s'] =		$content['part'];
	$content['p_c_s_s_f'] =		sprintf('% 2d', $content['part']);
	if ($content['chapter'] != 0) {
		$content['p_c_s_s'] .=		'-'.$content['chapter'];
		$content['p_c_s_s_f'] .=	' -'.sprintf('% 2d', $content['chapter']);
		if ($content['section'] != 0) {
			$content['p_c_s_s'] .=		'-'.$content['section'];
			$content['p_c_s_s_f'] .=	' -'.sprintf('% 2d', $content['section']);
			if ($content['subsection'] != 0) {
				$content['p_c_s_s'] .=		'-'.$content['subsection'];
				$content['p_c_s_s_f'] .=	' -'.sprintf('% 2d', $content['subsection']);
			}
		}
	}
	$content['p_c_s_s_f'] =		str_replace(' ', '&nbsp;', $content['p_c_s_s_f']);
	$content['title'] =		$myts->htmlSpecialChars($DB_DATA['title']);
	$content['excerpt'] =		$myts->htmlSpecialChars($DB_DATA['excerpt']);
	$content['footnote'] =		chapox_displayTarea($DB_DATA['footnote']);
//	$content['footnote'] =		$myts->htmlSpecialChars($DB_DATA['footnote']);
	$content['mycontent'] =		chapox_displayTarea($DB_DATA['mycontent']);
	$content['mycontentsize'] =	strlen(trim($content['mycontent']));
	$content['status'] =		$myts->htmlSpecialChars($DB_DATA['mystatus']);
	$content['uid'] =		intval($DB_DATA['uid']);
	$content['uname'] =		$myts->htmlSpecialChars($DB_DATA['uname']);
	$content['posted'] =		intval($DB_DATA['posted']);
	$content['updated'] =		intval($DB_DATA['updated']);
	if ($content['posted'] == 0) {
					$content['posted_date'] = '';
	} else {
					$content['posted_date'] =	date('Y-m-d', $content['posted']);
//					$content['posted_date'] =	date(_SHORTDATESTRING, $content['posted']);
	}
	if ($content['updated'] == 0) {
					$content['updated_date'] = '';
	} else {
					$content['updated_date'] =	date('Y-m-d', $content['updated']);
//					$content['updated_date'] =	date(_SHORTDATESTRING, $content['updated']);
	}
	$formD1 = 			new XoopsFormDhtmlTextArea(CHAPOX_MYCONTENT, 'mycontent',
					$myts->htmlSpecialChars($DB_DATA['mycontent']), 20, 80);
	$content['mycontentform'] =	$formD1->render();
	$formD2 = 			new XoopsFormDhtmlTextArea(CHAPOX_EXCERPT, 'excerpt',
					$myts->htmlSpecialChars($DB_DATA['excerpt']), 4, 80);
	$content['excerptform'] =	$formD2->render();
	$formD3 = 			new XoopsFormDhtmlTextArea(CHAPOX_FOOTNOTE, 'footnote',
					$myts->htmlSpecialChars($DB_DATA['footnote']), 4, 80);
	$content['footnoteform'] =	$formD3->render();
	return $content;
}

function chapox_renumber ($contents, $step = 1) {
	$layer1 = 0; $layer2 = 0; $layer3 = 0; $layer4 = 0;
	$old1 = 0; $old2 = 0; $old3 = 0; $old4 = 0;
	$prev1 = 0; $prev2 = 0; $prev3 = 0; $prev4 = 0;
	if ($step <= 0) { $step = 1; }
	$result = array();
	$i = 0;
	foreach ($contents as $content) {
		$prev1 = $old1; $prev2 = $old2; $prev3 = $old3; $prev4 = $old4;
		$old1 = (isset($content['layer1']) ? intval($content['layer1']) : 0);
		$old2 = (isset($content['layer2']) ? intval($content['layer2']) : 0);
		$old3 = (isset($content['layer3']) ? intval($content['layer3']) : 0);
		$old4 = (isset($content['layer4']) ? intval($content['layer4']) : 0);
		$lid = (isset($content['lid']) ? intval($content['lid']) : 0);
		$layer4 += $step;
		if ($old1 == 0) {
			$layer4 = 0; $layer3 = 0; $layer2 = 0; $layer1 = $step;
		}
		elseif ($old1 != $prev1) {
			$layer4 = 0; $layer3 = 0; $layer2 = 0; $layer1 += $step;
			if ($old2 != 0) { $layer2 = $step;
				if ($old3 != 0) { $layer3 = $step;
					if ($old4 != 0) { $layer4 = $step; }
				}
			}
		}
		elseif ($old2 == 0) {
			$layer4 = 0; $layer3 = 0; $layer2 = 0; $layer1 += $step;
		}
		elseif ($old2 != $prev2) {
			$layer4 = 0; $layer3 = 0; $layer2 += $step;
			if ($old3 != 0) { $layer3 = $step;
				if ($old4 != 0) { $layer4 = $step; }
			}
		}
		elseif ($old3 == 0) {
			$layer4 = 0; $layer3 = 0; $layer2 += $step;
		}
		elseif ($old3 != $prev3) {
			$layer4 = 0; $layer3 += $step;
			if ($old4 != 0) { $layer4 = $step; }
		}
		elseif ($old4 == 0) {
			$layer4 = 0; $layer3 += $step;
		}
		$content['new_p_c_s_s'] = strval($layer1);
		if ($layer2 != 0) {
			$content['new_p_c_s_s'] .= '-'.strval($layer2);
			if ($layer3 != 0) {
				$content['new_p_c_s_s'] .= '-'.strval($layer3);
				if ($layer4 != 0) {
					$content['new_p_c_s_s'] .= '-'.strval($layer4);
				}
			}
		}
		$content['new_layer1'] = $layer1;
		$content['new_layer2'] = $layer2;
		$content['new_layer3'] = $layer3;
		$content['new_layer4'] = $layer4;
		$result[$i] = $content;
		$i++;
	}
	return $result;
}

}
?>