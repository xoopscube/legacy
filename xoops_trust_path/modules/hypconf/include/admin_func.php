<?php
/*
 * Created on 2011/11/09 by nao-pon http://xoops.hypweb.net/
 * $Id: admin_func.php,v 1.11 2011/12/13 08:12:18 nao-pon Exp $
 */

function hypconfSetValue(& $config, $page) {
	global $constpref;

	require_once XOOPS_TRUST_PATH .'/class/hyp_common/preload/hyp_preload.php' ;;
	$dum = null;
	$hyp_preload = new HypCommonPreLoad($dum);
	$error = array();
	foreach($config as $key => $conf) {
		if ($key === 'error' || $key === 'contents') continue;
		if ($key === 'main_switch') {
			if (! $hyp_preload->$conf) {
				$error[] = str_replace('$1', hypconf_constant($constpref . '_' . strtoupper($conf)), hypconf_constant($constpref . '_MAIN_SWITCH_NOT_ENABLE'));
			}
			unset($config['main_switch']);
			continue;
		}
		$name = $conf['name'];
		if ($page === 'k_tai_conf') {
			// Reset each site values.
			if (isset($hyp_preload->k_tai_conf[$name.'#'.XOOPS_URL])) {
				$val = $hyp_preload->k_tai_conf[$name.'#'.XOOPS_URL];
			} else {
				$val = $hyp_preload->k_tai_conf[$name];
			}
		} else {
			if (isset($hyp_preload->$name)) {
				$val = $hyp_preload->$name;
			} else {
				$val = null;
			}
		}
		if (substr($conf['valuetype'], 0, 5) === 'file:') {
			$file = substr($conf['valuetype'], 5);
			$config[$key]['value'] = @ file_get_contents(hypconf_get_data_filename($file));
		} elseif ($conf['valuetype'] === 'int' && is_null($val)) {
			$config[$key]['value'] = 'null';
		} else {
			$config[$key]['value'] = $val;
			if (isset($conf['options']) && $conf['options'] === 'blocks') {
				$config[$key]['options'] = hypconfGetBlocks();
			} else if (isset($conf['options']) && $conf['options'] === 'modules') {
				$config[$key]['options'] = hypconfGetModules();
			} else if (isset($conf['options']) && $conf['options'] === 'xpwikis') {
				$config[$key]['options'] = hypconfGetModules('xpwiki', true);
			}
		}
	}
	if ($error) {
		if (!isset($config['error'])) $config['error'] = array();
		$config['error'] = array_merge($config['error'], $error);
	}
	return;
}

function hypconfGetBlocks() {
	static $ret = null;

	if (! is_null($ret)) return $ret;

	include_once(XOOPS_ROOT_PATH."/class/xoopsblock.php");
	$bobj = new XoopsBlock();
	$blocks = $bobj->getAllBlocks('object', null, true);
	$ret = array();
	if ($blocks) {
		foreach($blocks as $block) {
			$name = $block->getVar('title')? $block->getVar('title') : $block->getVar('name');
			$bid = $block->getVar("bid");
			if ($module = hypconfGetModuleName($block->getVar("mid"))) {
				$ret[$module . ':' . $name] = array(
					'confop_value' => $bid,
					'confop_name' => $module . ':' . $name
				);
			}
		}
		ksort($ret);
	}
	return $ret;
}

function hypconfGetModuleName($mid) {
	global $constpref;

	if (!$mid) return  hypconf_constant($constpref . '_COUSTOM_BLOCK');

	static $ret = array();

	if (isset($ret[$mid])) return $ret[$mid];

	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->get($mid);

	if (is_object($module)) {
		$ret[$mid] = $module->getVar('name');
	} else {
		$ret[$mid] = false;
	}

	return $ret[$mid];
}

function hypconfGetModules($trust_dirname = '', $add_notuse = false) {
	global $constpref;

	if ($add_notuse) {
		$ret = array(array('confop_value' => '', 'confop_name' => hypconf_constant($constpref . '_XPWIKI_RENDER_NONE')));
		$sorter = array('#');
	} else {
		$sorter = $ret = array();
	}
	$module_handler =& xoops_gethandler('module');
	$criteria = new CriteriaCompo(new Criteria('isactive', 1));
	$modules =& $module_handler->getObjects($criteria);
	foreach($modules as $module) {
		if (! $trust_dirname || $module->getInfo('trust_dirname') === $trust_dirname) {
			$ret[] = array(
				'confop_value' => $module->getVar('dirname'),
				'confop_name' => $module->getVar('name')
			);
			$sorter[] = $module->getVar('name');
		}
	}
	array_multisort($sorter, SORT_ASC, SORT_STRING, $ret);
	return $ret;
}

function hypconfSaveConf($config) {
	global $constpref, $mydirname;

	$section = $_POST['page'];

	$lines = array('['.$section.']');
	foreach($config as $conf){
		if (isset($_POST[$conf['name']]) || $conf['valuetype'] === 'array') {
			switch (substr($conf['valuetype'], 0, 5)) {
				case 'int':
					if (strtolower($_POST[$conf['name']]) === 'null') {
						$lines[] = $conf['name'] . ' = -1';
					} else {
						$lines[] = $conf['name'] . ' = ' . (int)$_POST[$conf['name']];
					}
					break;
				case 'float':
					$lines[] = $conf['name'] . ' = ' . (float)$_POST[$conf['name']];
					break;
				case 'text':
					$lines[] = $conf['name'] . ' = "' . str_replace('"', '\\"', trim($_POST[$conf['name']])) . '"';
					break;
				case 'array':
					if (empty($_POST[$conf['name']])) {
						$lines[] = $conf['name'] . '[] = ""';
					} else {
						foreach($_POST[$conf['name']] as $key => $val) {
							$lines[] = $conf['name'] . '[] = "' . str_replace('"', '\\"', trim($val)) . '"';
						}
					}
					break;
				case 'file:':
					$file = substr($conf['valuetype'], 5);
					if ($_POST[$conf['name']]) {
						file_put_contents(hypconf_get_data_filename($file), $_POST[$conf['name']]);
						$lines[] = $conf['name'] . ' = "' . $file . ':' . time() . '"';
					} else {
						@ unlink(hypconf_get_data_filename($file));
						$lines[] = $conf['name'] . ' = ""';
					}
					break;

				default:

			}
		}
	}

	$ini = join("\n", $lines) . "\n";

	if ($data = @ file_get_contents(XOOPS_TRUST_PATH . HYP_COMMON_PRELOAD_CONF)) {
		$data = preg_replace('/\['.$section.'\].+?(\n\[|$)/s', $ini . '$1', $data, 1, $count);
		if (! $count) {
			$data .= $ini;
		}
	} else {
		$data = $ini;
	}
	if (file_put_contents(XOOPS_TRUST_PATH . HYP_COMMON_PRELOAD_CONF, $data)) {
		if ($section === 'xpwiki_render') {
			if (isset($_POST['xpwiki_render_dirname']) && $_POST['xpwiki_render_dirname']) {
				@ touch(XOOPS_ROOT_PATH . '/modules/'.$_POST['xpwiki_render_dirname'].'/private/cache/pukiwiki.ini.php');
			}
		}
	}

	redirect_header(XOOPS_URL  . '/modules/' . $mydirname . '/admin/index.php', 0, hypconf_constant($constpref . '_MSG_SAVED'));

}

function hypconfShowForm($config) {
	global $constpref, $mydirname, $mydirpath, $mytrustdirpath, $page, $xoopsConfig, $xoopsGTicket;
	if (! $config) {
		die( 'no configs' ) ;
	}
	if (isset($config['error'])) {
		echo '<div class="error">' . join('</div><div class="error">', $config['error']) . '</div>';
		unset($config['error']);
	}
	if (isset($config['contents'])) {
		echo $config['contents'];
		unset($config['contents']);
	}
	if ($config) {
		$count = count($config);
		include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
		include_once dirname(dirname(__FILE__)).'/class/formcheckbox.php';
		if (! XC_CLASS_EXISTS('XoopsFormBreak')) {
			include_once dirname(dirname(__FILE__)).'/class/formbreak.php';
		}

		$form = new XoopsThemeForm( hypconf_constant($constpref . '_ADMENU_' . strtoupper($page)) , 'pref_form', 'index.php');
		$button_tray = new XoopsFormElementTray("");

		for ($i = 0; $i < $count; $i++) {
			$description = defined($config[$i]['description'])? constant($config[$i]['description']) : '';
			//$title4tray = (!$description) ? hypconf_constant($config[$i]['title']) : hypconf_constant($config[$i]['title']).'<br /><br /><span style="font-weight:normal;">'.hypconf_constant($config[$i]['description']).'</span>'; // GIJ
			$title4tray = hypconf_constant($config[$i]['title']);
			$title = '' ; // GIJ
			switch ($config[$i]['formtype']) {
			case 'textarea':
				$myts =& MyTextSanitizer::getInstance();
				if ($config[$i]['valuetype'] == 'array') {
					// this is exceptional.. only when value type is arrayneed a smarter way for this
					$ele = ($config[$i]['value'] != '') ? new XoopsFormTextArea($title, $config[$i]['name'], $myts->htmlspecialchars(implode('|', $config[$i]['value'])), 5, 50) : new XoopsFormTextArea($title, $config[$i]['name'], '', 5, 50);
				} else {
					$ele = new XoopsFormTextArea($title, $config[$i]['name'], $myts->htmlspecialchars($config[$i]['value']), 5, 50);
					$ele->setExtra('class="norich"');
				}
				break;
			case 'select':
				$size = 1;
				if (! empty($config[$i]['size'])) {
					$size = $config[$i]['size'];
				}
				$ele = new XoopsFormSelect($title, $config[$i]['name'], $config[$i]['value'], $size);
				$options = $config[$i]['options'];
				$opcount = count($options);
				foreach($options as $option) {
					$optval = defined($option['confop_value']) ? constant($option['confop_value']) : $option['confop_value'];
					$optkey = defined($option['confop_name']) ? constant($option['confop_name']) : $option['confop_name'];
					$ele->addOption($optval, $optkey);
				}
				break;
			case 'select_multi':
				$size = 5;
				if (! empty($config[$i]['size'])) {
					$size = $config[$i]['size'];
				}
				$ele = new XoopsFormSelect($title, $config[$i]['name'], $config[$i]['value'], $size, true);
				$options = $config[$i]['options'];
				foreach($options as $option) {
					$optval = defined($option['confop_value']) ? constant($option['confop_value']) : $option['confop_value'];
					$optkey = defined($option['confop_name']) ? constant($option['confop_name']) : $option['confop_name'];
					$ele->addOption($optval, $optkey);
				}
				break;
			case 'check':
				$ele = new HypconfFormCheckBox($title, $config[$i]['name'], $config[$i]['value']);
				if (! empty($config[$i]['width'])) {
					//$ele->setWidth($config[$i]['width']);
				}
				$options = $config[$i]['options'];
				foreach($options as $option) {
					$optval = defined($option['confop_value']) ? hypconf_constant($option['confop_value']) : $option['confop_value'];
					$optkey = defined($option['confop_name']) ? hypconf_constant($option['confop_name']) : $option['confop_name'];
					$ele->addOption($optval, $optkey);
				}
				break;
			case 'yesno':
				$ele = new XoopsFormRadioYN($title, $config[$i]['name'], $config[$i]['value'], _YES, _NO);
				break;
			case 'password':
				$size = 50;
				if (! empty($config[$i]['size'])) {
					$size = $config[$i]['size'];
				}
				$myts =& MyTextSanitizer::getInstance();
				$ele = new XoopsFormPassword($title, $config[$i]['name'], $size, 255, $myts->htmlspecialchars($config[$i]['value']));
				break;
			case 'textbox':
			default:
				$size = 50;
				if (! empty($config[$i]['size'])) {
					$size = $config[$i]['size'];
				}
				$myts =& MyTextSanitizer::getInstance();
				$ele = new XoopsFormText($title, $config[$i]['name'], $size, 255, $myts->htmlspecialchars($config[$i]['value']));
				if ($config[$i]['valuetype'] === 'int') $ele->setExtra(' style="text-align:right;"');
				break;
			}
			$ele_tray = new XoopsFormElementTray( $title4tray , '' ) ;
			$ele_tray->addElement($ele);
			$form->addElement( $ele_tray ) ;
			if ($description) {
				$form->insertBreak('<span style="font-weight:normal;">' . $description .'</span>', 'odd');
			}
			unset($ele_tray);
			unset($ele);
		}
		$button_tray->addElement(new XoopsFormHidden('op', 'save'));
		$button_tray->addElement(new XoopsFormHidden('page', $page));
		$xoopsGTicket->addTicketXoopsFormElement( $button_tray , __LINE__ , 1800 , 'hypconf' ) ;
		$button_tray->addElement(new XoopsFormButton('', 'button', _GO, 'submit'));
		$form->addElement( $button_tray ) ;

		$form->display();
	}
}

function hypconf_get_data_filename($file) {
	return XOOPS_TRUST_PATH . '/uploads/hyp_common/' . urlencode(substr(XOOPS_URL, 7)) . '_' . $file;
}

function hypconf_constant($const) {
	return defined($const)? constant($const) : $const;
}