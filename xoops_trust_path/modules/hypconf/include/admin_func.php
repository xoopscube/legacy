<?php
/*
 * Created on 2011/11/09 by nao-pon http://xoops.hypweb.net/
 * $Id: admin_func.php,v 1.3 2011/11/14 00:27:49 nao-pon Exp $
 */

function hypconfSetValue(& $config, $page) {
	require_once XOOPS_TRUST_PATH .'/class/hyp_common/preload/hyp_preload.php' ;;
	$dum = null;
	$hyp_preload = new HypCommonPreLoad($dum);
	foreach($config as $key => $conf) {
		$name = $conf['name'];
		if ($page === 'k_tai_conf') {
			// Reset each site values.
			if (isset($hyp_preload->k_tai_conf[$name.'#'.XOOPS_URL])) {
				$val = $hyp_preload->k_tai_conf[$name.'#'.XOOPS_URL];
			} else {
				$val = $hyp_preload->k_tai_conf[$name];
			}
		} else {
			$val = $hyp_preload->$name;
		}
		$config[$key]['value'] = $val;
		if (isset($conf['options']) && $conf['options'] === 'blocks') {
			$config[$key]['options'] = hypconfGetBlocks();
		}
	}
	return;
}

function hypconfGetBlocks() {
	static $ret = null;

	if (! is_null($ret)) return $ret;

	include_once(XOOPS_ROOT_PATH."/class/xoopsblock.php");
	$bobj = new XoopsBlock();
	$blocks = $bobj->getAllBlocks();
	$ret = array();
	if ($blocks) {
		foreach($blocks as $block) {
			$name = ($block->getVar("block_type") != "C") ? $block->getVar("name") : $block->getVar("title");
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

function hypconfSaveConf($config) {
	global $constpref, $mydirname;

	$section = $_POST['page'];

	$lines = array('['.$section.']');
	foreach($config as $conf){
		if (isset($_POST[$conf['name']]) || $conf['valuetype'] === 'array') {
			switch ($conf['valuetype']) {
				case 'int':
					$lines[] = $conf['name'] . ' = ' . (int)$_POST[$conf['name']];
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
	file_put_contents(XOOPS_TRUST_PATH . HYP_COMMON_PRELOAD_CONF, $data);

	redirect_header(XOOPS_URL  . '/modules/' . $mydirname . '/admin/index.php', 0, hypconf_constant($constpref . '_MSG_SAVED'));

}

function hypconfShowForm($config) {
	global $constpref, $mydirname, $mydirpath, $mytrustdirpath, $page, $xoopsConfig, $xoopsGTicket;
	$count = count($config);
	if ($count < 1) {
		die( 'no configs' ) ;
	}
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

function hypconf_constant($const) {
	return defined($const)? constant($const) : $const;
}