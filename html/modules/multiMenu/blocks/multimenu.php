<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

function a_multimenu_show($options) {
	$block = getMultiMenu( $options, 'multimenu01' );
	return $block;
}
function a_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}


function b_multimenu_show($options) {
	$block = getMultiMenu( $options, 'multimenu02' );
	return $block;
}
function b_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}


function c_multimenu_show($options) {
	$block = getMultiMenu( $options, 'multimenu03' );
	return $block;
}
function c_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}


function d_multimenu_show($options) {
	$block = getMultiMenu( $options, 'multimenu04' );
	return $block;
}
function d_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}


function e_multimenu_show($options) {
	$block = getMultiMenu( $options, 'multimenu05' );
	return $block;
}
function e_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}

function f_multimenu_show($options) {
	$block = getMultiMenu( $options, 'multimenu06' );
	return $block;
}
function f_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}

function g_multimenu_show($options) {
	$block = getMultiMenu( $options, 'multimenu07' );
	return $block;
}
function g_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}

function h_multimenu_show($options) {
	$block = getMultiMenu( $options, 'multimenu08' );
	return $block;
}
function h_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}

function flow_menu_show($options) {
	$block = getMultiMenu( $options, 'multimenu99' );
	return $block;
}
function flow_menu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}

/**
 *
 * @ MultiMenu block main function
 *
 */
function getMultiMenu( $options, $db_name  )
{
	global $xoopsDB, $xoopsUser, $xoopsModule;
	$myts = MyTextSanitizer::getInstance();
	$block = array();
	$inum = 0;
	$group = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
	$db = $xoopsDB->prefix( $db_name );
	$result = $xoopsDB->query("SELECT groups, link, title, target FROM ".$db." WHERE hide=0 ORDER BY weight ASC");
	while ( $myrow = $xoopsDB->fetchArray($result) ) {
		$title = $myts->makeTboxData4Show($myrow["title"]);
		if ( !XOOPS_USE_MULTIBYTES ) {
			if (strlen($myrow['title']) >= $options[0]) {
				$title = $myts->makeTboxData4Show(substr($myrow['title'],0,($options[0]-1)))."...";
			}
		}
		$groups = explode(" ",$myrow['groups']);
		if (count(array_intersect($group,$groups)) > 0) {
			// hacked by nobunobu start
			if (preg_match("/^[\s\-].*/",$myrow['link']) && inum > 0) {
				$isub =count($block['contents'][$inum-1]['sublinks']);
				if ($parent_active) {
					$block['contents'][$inum-1]['sublinks'][$isub]['name'] = $title;
					$link =  preg_replace("/^[\s\-]/","",$myrow['link']);
					//fix by domifara eregi -> preg_match for php5.3+
					if (preg_match("/^\[([a-z0-9_\-]+)\]((.)*)$/i", $link, $moduledir)) {
						$module_handler = xoops_gethandler( 'module' );
						$module =& $module_handler->getByDirname($moduledir[1]);
						if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
							$link = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
						}
					}
					$block['contents'][$inum-1]['sublinks'][$isub]['url'] = $link;
				}
				continue;
			}
			// hacked by nobunobu end
			$imenu['title'] = $title;
			$imenu['target'] = $myrow['target'];
			$imenu['sublinks'] = array();
			// [module_name]xxxx.php?aa=aa&bb=bb
			//fix by domifara eregi -> preg_match for php5.3+
			if (preg_match("/^\[([a-z0-9_\-]+)\]((.)*)$/i", $myrow['link'], $moduledir)) {
				$module_handler = xoops_gethandler( 'module' );
				$module = $module_handler->getByDirname($moduledir[1]);
				if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
					$imenu['link'] = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
					$parent_active = true;
				}
			// +[module_name]xxxx.php?aa=aa&bb=bb	view submenu
			//fix by domifara eregi -> preg_match for php5.3+
			}elseif (preg_match("/^\+\[([a-z0-9_\-]+)\]((.)*)$/i", $myrow['link'], $moduledir)) {
				$module_handler = xoops_gethandler( 'module' );
				$module = $module_handler->getByDirname($moduledir[1]);
				if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
					$imenu['link'] = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
					$parent_active = true;
					$mid = $module->getVar('mid');
					$sublinks = $module->subLink();
					if (count($sublinks) > 0)  {
						foreach($sublinks as $sublink){
							if ( !XOOPS_USE_MULTIBYTES ) {
								if (strlen($sublink['name']) >= $options[0]) {
									$sublink['name'] = $myts->makeTboxData4Show(substr($sublink['name'],0,($options[0]-1)))."...";
								}
							}
							$imenu['sublinks'][] = array('name' => $sublink['name'], 'url' => XOOPS_URL.'/modules/'.$moduledir[1].'/'.$sublink['url'] );
						}
					}
				}
			// @[module_name]xxxx.php?aa=aa&bb=bb	view submennu
			//fix by domifara eregi -> preg_match for php5.3+
			}elseif (preg_match("/^\@\[([a-z0-9_\-]+)\]((.)*)$/i", $myrow['link'], $moduledir)) {
				$module_handler = xoops_gethandler( 'module' );
				$module = $module_handler->getByDirname($moduledir[1]);
				if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
					$imenu['link'] = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
					$mid = $module->getVar('mid');
					$sublinks = $module->subLink();
					// hacked by nobunobu start
					if ( (!empty($xoopsModule)) && ($moduledir[1] == $xoopsModule->getVar('dirname')) ){
						$parent_active = true;
						if (count($sublinks) > 0) {
							foreach($sublinks as $sublink){
								if ( !XOOPS_USE_MULTIBYTES ) {
									if (strlen($sublink['name']) >= $options[0]) {
										$sublink['name'] = $myts->makeTboxData4Show(substr($sublink['name'],0,($options[0]-1)))."...";
									}
								}
								$imenu['sublinks'][] = array('name' => $sublink['name'], 'url' => XOOPS_URL.'/modules/'.$moduledir[1].'/'.$sublink['url'] );
							}
						}
					} else {
						$parent_active = false;
					// hacked by nobunobu end
					}
				}
			// &[module_name]xxxx.php?aa=aa&bb=bb	view submenu // hacked by nobunobu
			//fix by domifara eregi -> preg_match for php5.3+
			} elseif (preg_match("/^\&\[([a-z0-9_\-]+)\]((.)*)$/i", $myrow['link'], $moduledir)) {
				$module_handler = xoops_gethandler( 'module' );
				$module = $module_handler->getByDirname($moduledir[1]);
				if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
					$imenu['link'] = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
					$mid = $module->getVar('mid');
					if ( (!empty($xoopsModule)) && ($moduledir[1] == $xoopsModule->getVar('dirname')) ){
						$parent_active = true;
					} else {
						$parent_active = false;
					}
				}
			} else {
				$imenu['link'] = $myrow['link'];
			}
			$block['contents'][] = $imenu;
		}
		$inum++;
	}
	return $block;
}
?>