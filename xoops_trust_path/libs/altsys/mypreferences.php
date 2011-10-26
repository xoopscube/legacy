<?php
// ------------------------------------------------------------------------- //
//                      mypreferences.php (altsys)                           //
//                   - XOOPS altenative preferences -                        //
//                     GIJOE <http://www.peak.ne.jp/>                        //
// ------------------------------------------------------------------------- //

require_once dirname(__FILE__).'/class/AltsysBreadcrumbs.class.php' ;
include_once dirname(__FILE__).'/include/gtickets.php' ;
include_once dirname(__FILE__).'/include/altsys_functions.php' ;

// check access right (needs module_admin of this module)
if( ! is_object( $xoopsUser ) || ! is_object( $xoopsModule ) || ! $xoopsUser->isAdmin( $xoopsModule->mid() ) ) {
	die( 'Access Denied' ) ;
}


// initials
$db =& Database::getInstance();
$myts =& MyTextSanitizer::getInstance() ;

// language file
altsys_include_language_file( 'mypreferences' ) ;


$op = empty( $_GET['op'] ) ? 'showmod' : preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $_GET['op'] ) ;

if ($op == 'showmod') {
	$config_handler =& xoops_gethandler('config');
	$mod = $xoopsModule->mid() ;
	$config =& $config_handler->getConfigs(new Criteria('conf_modid', $mod));
	$count = count($config);
	if ($count < 1) {
		die( 'no configs' ) ;
	}
	include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
	$form = new XoopsThemeForm( _MD_A_MYPREFERENCES_FORMTITLE , 'pref_form', 'index.php?mode=admin&lib=altsys&page=mypreferences&op=save');
	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->get($mod);

	// language
	$language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ;

	// load modinfo.php if necessary (judged by a specific constant is defined)
	if( ! defined( '_MYMENU_CONSTANT_IN_MODINFO' ) || ! defined( _MYMENU_CONSTANT_IN_MODINFO ) ) {
		if( file_exists( "$mydirpath/language/$language/modinfo.php" ) ) {
			// user customized language file
			include_once "$mydirpath/language/$language/modinfo.php" ;
		} else if( file_exists( "$mytrustdirpath/language/$language/modinfo.php" ) ) {
			// default language file
			include_once "$mytrustdirpath/language/$language/modinfo.php" ;
		} else {
			// fallback english
			include_once "$mytrustdirpath/language/english/modinfo.php" ;
		}
	}

	// if has comments feature, need comment lang file
	if ($module->getVar('hascomments') == 1 && ! defined( '_CM_TITLE' ) ) {
		include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/comment.php';
	}

	// RMV-NOTIFY
	// if has notification feature, need notification lang file
	if ($module->getVar('hasnotification') == 1 && ! defined( '_NOT_NOTIFICATIONOPTIONS' ) ) {
		include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/notification.php';
	}

	$modname = $module->getVar('name');
	$button_tray = new XoopsFormElementTray("");
	// if ($module->getInfo('adminindex')) {
		//	$form->addElement(new XoopsFormHidden('redirect', XOOPS_URL.'/modules/'.$module->getVar('dirname').'/'.$module->getInfo('adminindex')));
	// }
	for ($i = 0; $i < $count; $i++) {
		$title4tray = (!defined($config[$i]->getVar('conf_desc')) || constant($config[$i]->getVar('conf_desc')) == '') ? constant($config[$i]->getVar('conf_title')) : constant($config[$i]->getVar('conf_title')).'<br /><br /><span style="font-weight:normal;">'.constant($config[$i]->getVar('conf_desc')).'</span>'; // GIJ
		$title = '' ; // GIJ
		switch ($config[$i]->getVar('conf_formtype')) {
		case 'textarea':
			$myts =& MyTextSanitizer::getInstance();
			if ($config[$i]->getVar('conf_valuetype') == 'array') {
				// this is exceptional.. only when value type is arrayneed a smarter way for this
				$ele = ($config[$i]->getVar('conf_value') != '') ? new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars(implode('|', $config[$i]->getConfValueForOutput())), 5, 50) : new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), '', 5, 50);
			} else {
				$ele = new XoopsFormTextArea($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()), 5, 50);
			}
			break;
		case 'select':
			$ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
			$options =& $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
			$opcount = count($options);
			for ($j = 0; $j < $opcount; $j++) {
				$optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
				$optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
				$ele->addOption($optval, $optkey);
			}
			break;
		case 'select_multi':
			$ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
			$options =& $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
			$opcount = count($options);
			for ($j = 0; $j < $opcount; $j++) {
				$optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
				$optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
				$ele->addOption($optval, $optkey);
			}
			break;
		case 'yesno':
			$ele = new XoopsFormRadioYN($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), _YES, _NO);
			break;
		case 'group':
			include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
			$ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
			break;
		case 'group_multi':
			include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
			$ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
			break;
		// RMV-NOTIFY: added 'user' and 'user_multi'
		case 'user':
			include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
			$ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
			break;
		case 'user_multi':
			include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
			$ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
			break;
		case 'password':
			$myts =& MyTextSanitizer::getInstance();
			$ele = new XoopsFormPassword($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
			break;
		case 'textbox':
		default:
			$myts =& MyTextSanitizer::getInstance();
			$ele = new XoopsFormText($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
			break;
		}
		$hidden = new XoopsFormHidden('conf_ids[]', $config[$i]->getVar('conf_id'));
		$ele_tray = new XoopsFormElementTray( $title4tray , '' ) ;
		$ele_tray->addElement($ele);
		$ele_tray->addElement($hidden);
		$form->addElement( $ele_tray ) ;
		unset($ele_tray);
		unset($ele);
		unset($hidden);
	}
	// $button_tray->addElement(new XoopsFormHidden('op', 'save'));
	$xoopsGTicket->addTicketXoopsFormElement( $button_tray , __LINE__ , 1800 , 'mypreferences' ) ;
	$button_tray->addElement(new XoopsFormButton('', 'button', _GO, 'submit'));
	$form->addElement( $button_tray ) ;
	xoops_cp_header();

	// GIJ patch start
	altsys_include_mymenu() ;
	$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
	if( $breadcrumbsObj->hasPaths() ) {
		$breadcrumbsObj->appendPath( XOOPS_URL.'/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mypreferences' , _PREFERENCES ) ;
	}
	echo "<h3 style='text-align:"._GLOBAL_LEFT.";'>".$module->getvar('name').' &nbsp; '._PREFERENCES."</h3>\n" ;
	// GIJ patch end

	$form->display();
	xoops_cp_footer();
	exit();
}

if ($op == 'save') {
	//if ( !admin_refcheck("/modules/$admin_mydirname/admin/") ) {
	//	exit('Invalid referer');
	//}
	if ( ! $xoopsGTicket->check( true , 'mypreferences' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$xoopsTpl = new XoopsTpl();
	$xoopsTpl->clear_all_cache();
	// regenerate admin menu file
	xoops_module_write_admin_menu(xoops_module_get_admin_menu());
	if ( !empty($_POST['conf_ids']) ) { $conf_ids = $_POST['conf_ids']; }
	$count = count($conf_ids);
	$tpl_updated = false;
	$theme_updated = false;
	$startmod_updated = false;
	$lang_updated = false;
	if ($count > 0) {
		for ($i = 0; $i < $count; $i++) {
			$config =& $config_handler->getConfig($conf_ids[$i]);
			$new_value =& $_POST[$config->getVar('conf_name')];
			if (is_array($new_value) || $new_value != $config->getVar('conf_value')) {
				// if language has been changed
				if (!$lang_updated && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') == 'language') {
					// regenerate admin menu file
					$xoopsConfig['language'] = $_POST[$config->getVar('conf_name')];
					xoops_module_write_admin_menu(xoops_module_get_admin_menu());
					$lang_updated = true;
				}

				// if default theme has been changed
				if (!$theme_updated && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') == 'theme_set') {
					$member_handler =& xoops_gethandler('member');
					$member_handler->updateUsersByField('theme', $_POST[$config->getVar('conf_name')]);
					$theme_updated = true;
				}

				// if default template set has been changed
				if (!$tpl_updated && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') == 'template_set') {
					// clear cached/compiled files and regenerate them if default theme has been changed
					if ($xoopsConfig['template_set'] != $_POST[$config->getVar('conf_name')]) {
						$newtplset = $_POST[$config->getVar('conf_name')];
						
						// clear all compiled and cachedfiles
						$xoopsTpl->clear_compiled_tpl();

						// generate compiled files for the new theme
						// block files only for now..
						$tplfile_handler =& xoops_gethandler('tplfile');
						$dtemplates =& $tplfile_handler->find('default', 'block');
						$dcount = count($dtemplates);

						// need to do this to pass to xoops_template_touch function
						$GLOBALS['xoopsConfig']['template_set'] = $newtplset;

						altsys_clear_templates_c() ;

/*						for ($i = 0; $i < $dcount; $i++) {
							$found =& $tplfile_handler->find($newtplset, 'block', $dtemplates[$i]->getVar('tpl_refid'), null);
							if (count($found) > 0) {
								// template for the new theme found, compile it
								xoops_template_touch($found[0]->getVar('tpl_id'));
							} else {
								// not found, so compile 'default' template file
								xoops_template_touch($dtemplates[$i]->getVar('tpl_id'));
							}
						}*/

						// generate image cache files from image binary data, save them under cache/
						$image_handler =& xoops_gethandler('imagesetimg');
						$imagefiles =& $image_handler->getObjects(new Criteria('tplset_name', $newtplset), true);
						foreach (array_keys($imagefiles) as $i) {
							if (!$fp = fopen(XOOPS_CACHE_PATH.'/'.$newtplset.'_'.$imagefiles[$i]->getVar('imgsetimg_file'), 'wb')) {
							} else {
								fwrite($fp, $imagefiles[$i]->getVar('imgsetimg_body'));
								fclose($fp);
							}
						}
					}
					$tpl_updated = true;
				}

				// add read permission for the start module to all groups
				if (!$startmod_updated	&& $new_value != '--' && $config->getVar('conf_catid') == XOOPS_CONF && $config->getVar('conf_name') == 'startpage') {
					$member_handler =& xoops_gethandler('member');
					$groups =& $member_handler->getGroupList();
					$moduleperm_handler =& xoops_gethandler('groupperm');
					$module_handler =& xoops_gethandler('module');
					$module =& $module_handler->getByDirname($new_value);
					foreach ($groups as $groupid => $groupname) {
						if (!$moduleperm_handler->checkRight('module_read', $module->getVar('mid'), $groupid)) {
							$moduleperm_handler->addRight('module_read', $module->getVar('mid'), $groupid);
						}
					}
					$startmod_updated = true;
				}

				$config->setConfValueForInput($new_value);
				$config_handler->insertConfig($config);
			}
			unset($new_value);
		}
	}

	redirect_header( 'index.php?mode=admin&lib=altsys&page=mypreferences' , 2 , _MD_A_MYPREFERENCES_UPDATED ) ;
}

?>