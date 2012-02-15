<?php
/*
 * 2011/09/09 16:45
 * MultiMenu class function
 * copyright(c) Yoshi Sakai at Bluemoon inc 2011
 * GPL ver3.0 All right reserved.
 */
class getMultiMenu {
  var $block = array();

  function getMultiMenu(){
  }
  function getblock( $options, $db_name  ) {
	global $xoopsDB, $xoopsUser, $xoopsModule;

	$myts =& MyTextSanitizer::getInstance();
	$block = array();
	$inum = 0;
	$group = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
	$db = $xoopsDB->prefix( $db_name );
	$result = $xoopsDB->query("SELECT id, groups, link, title, target FROM ".$db." WHERE hide=0 ORDER BY weight ASC");
	while ( $myrow = $xoopsDB->fetchArray($result) ) {
		//$title = $myts->makeTboxData4Show($myrow["title"]);
		$title = $myts->stripSlashesGPC($myrow["title"]);	// by bluemoon
		if ( !XOOPS_USE_MULTIBYTES ) {
			if (strlen($myrow['title']) >= $options[0]) {
				$title = $myts->makeTboxData4Show(substr($myrow['title'],0,($options[0]-1)))."...";
			}
		}
		$title = preg_replace("/\[XOOPS_URL\]/",XOOPS_URL,$title);
		$groups = explode(" ",$myrow['groups']);
		if (count(array_intersect($group,$groups)) > 0) {
			// hacked by nobunobu start
			if (preg_match("/^[\s\-].*/",$myrow['link'])) {
				$isub =count($block['contents'][$inum-1]['sublinks']);
				if ($parent_active) {
					$block['contents'][$inum-1]['sublinks'][$isub]['name'] = $title;
					$link =  preg_replace("/^[\s\-]/","",$myrow['link']);
//fix by domifara eregi -> preg_match for php5.3+
					if (preg_match("/^\[([a-z0-9_\-]+)\]((.)*)$/i", $link, $moduledir)) {
						$module_handler = & xoops_gethandler( 'module' );
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
			$imenu['id'] = $myrow['id'];
			$imenu['title'] = $title;
			$imenu['target'] = $myrow['target'];
			$imenu['sublinks'] = array();

			// [module_name]xxxx.php?aa=aa&bb=bb
//fix by domifara eregi -> preg_match for php5.3+
			if (preg_match("/^\[([a-z0-9_\-]+)\]((.)*)$/i", $myrow['link'], $moduledir)) {
				$module_handler = & xoops_gethandler( 'module' );
				$module =& $module_handler->getByDirname($moduledir[1]);
				if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
					$imenu['link'] = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
					$imenu['mid'] = $module->mid();
					$parent_active = true;
				}

			// +[module_name]xxxx.php?aa=aa&bb=bb	view submenu
//fix by domifara eregi -> preg_match for php5.3+
			}elseif (preg_match("/^\+\[([a-z0-9_\-]+)\]((.)*)$/i", $myrow['link'], $moduledir)) {
				$module_handler = & xoops_gethandler( 'module' );
				$module =& $module_handler->getByDirname($moduledir[1]);
				if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
					$imenu['link'] = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
					$imenu['mid'] = $module->mid();
					$parent_active = true;

					$mid = $module->getVar('mid');
					$sublinks =& $module->subLink();
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
				$module_handler = & xoops_gethandler( 'module' );
				$module =& $module_handler->getByDirname($moduledir[1]);
				if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
					$imenu['link'] = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
					$imenu['mid'] = $module->mid();

					$mid = $module->getVar('mid');
					$sublinks =& $module->subLink();

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
				$module_handler = & xoops_gethandler( 'module' );
				$module =& $module_handler->getByDirname($moduledir[1]);
				if ( is_object( $module ) && $module->getVar( 'isactive' ) ) {
					$imenu['link'] = XOOPS_URL."/modules/".$moduledir[1]."/".$moduledir[2];
					$imenu['mid'] = $module->mid();

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
			if (isset($imenu['link'])){
				$slash = substr( $imenu['link'], strlen($imenu['link'])-1, 1 );
				if ( $slash == '/' ) {
					$imenu['link'] .= "index.php";
				}
			}
			$block['contents'][$inum] = $imenu;
			$inum++;
		}
	}
	$this->block = $block;
	return $block;
  }
  function replace_userinfo($str) {
	global $xoopsUser;
	if ($xoopsUser){
		$str = preg_replace("/\[xoops_uid\]/",$xoopsUser->uid(),$str);
	}
	return $str;
  }
  function getModuleConfig( $name, $mid ) {
  	$ret = NULL;
	$config_handler =& xoops_gethandler('config');
	$config =& $config_handler->getConfigsByCat(0, $mid);
	if ( isset($config[$name]) ) $ret = preg_split('/,|[\r\n]+/',$config[$name]);
	return $ret;
  }
  function assign_module_css($css_file) {
    global $xoopsTpl;

	$css_file = preg_replace("/\[XOOPS_URL\]/i",XOOPS_URL,$css_file);
	$header = '<link rel="stylesheet" type="text/css" media="all" href="'.$css_file.'" />';
	$xoopsTpl->assign('xoops_block_header', $header);
  }
  function assign_css() {
    $module_handler = & xoops_gethandler( 'module' );
	$module =& $module_handler->getByDirname("multiMenu");
	$mid = $module->getVar('mid');
	$css_file = $this->getModuleConfig('css_file',$mid);
	$this->assign_module_css($css_file[0]);
  }
  function theme_menu($modname="multiMenu") {
    $module_handler = & xoops_gethandler( 'module' );
	$module = $module_handler->getByDirname($modname);
	$mid = $module->getVar('mid');
	$theme_menu = $this->getModuleConfig('theme_menu',$mid);
	return intval($theme_menu[0]);
  }
}
?>