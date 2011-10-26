<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File:     resource.db.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a database
 * -------------------------------------------------------------
 */

function smarty_resource_db_systemTpl($tpl_name)
{
    // Replace Legacy System Template name to Legacy Module Template name
    static $patterns = null;
    static $replacements = null;
    if (!$patterns) {
        $root=&XCube_Root::getSingleton();
        $systemTemplates = explode(',',$root->getSiteConfig('Legacy_RenderSystem','SystemTemplate',''));
        $prefix = $root->getSiteConfig('Legacy_RenderSystem','SystemTemplatePrefix','legacy');
        $patterns = preg_replace('/^\s*([^\s]*)\s*$/e', '"/".preg_quote("\1","/")."/"', $systemTemplates);
        $replacements = preg_replace('/^\s*system_([^\s]*)\s*/', $prefix.'_\1', $systemTemplates);
    }
    if ($patterns) {
        $tpl_name = preg_replace($patterns, $replacements,$tpl_name);
    }
    return $tpl_name;
}

function smarty_resource_db_source($tpl_name, &$tpl_source, &$smarty)
{
    $tpl_name = smarty_resource_db_systemTpl($tpl_name);
    $tplfile_handler =& xoops_gethandler('tplfile');
	$tplobj =& $tplfile_handler->find($GLOBALS['xoopsConfig']['template_set'], null, null, null, $tpl_name, true);
	if (count($tplobj) == 0 && $GLOBALS['xoopsConfig']['template_set'] != "default") {
		$tplobj =& $tplfile_handler->find('default', null, null, null, $tpl_name, true);
	}
	if (count($tplobj) > 0) {
		if (false != $smarty->xoops_canUpdateFromFile()) {
			$conf_theme = isset($GLOBALS['xoopsConfig']['theme_set']) ? $GLOBALS['xoopsConfig']['theme_set'] : 'default';
			if ($conf_theme != 'default') {
				switch ($tplobj[0]->getVar('tpl_type')) {
					case 'module':
						$filepath = XOOPS_THEME_PATH.'/'.$conf_theme.'/templates/'.$tplobj[0]->getVar('tpl_module').'/'.$tpl_name;
						break;
					case 'block':
						$filepath = XOOPS_THEME_PATH.'/'.$conf_theme.'/templates/'.$tplobj[0]->getVar('tpl_module').'/blocks/'.$tpl_name;
						break;
					default:
						$filepath = "";
						break;
				}
			} else {
				switch ($tplobj[0]->getVar('tpl_type')) {
					case 'module':
						$filepath = XOOPS_ROOT_PATH.'/modules/'.$tplobj[0]->getVar('tpl_module').'/templates/'.$tpl_name;
						break;
					case 'block':
						$filepath = XOOPS_ROOT_PATH.'/modules/'.$tplobj[0]->getVar('tpl_module').'/templates/blocks/'.$tpl_name;
						break;
					default:
						$filepath = "";
						break;
				}
			}
			if ($filepath != "" && file_exists($filepath)) {
				$file_modified = filemtime($filepath);
				if ($file_modified > $tplobj[0]->getVar('tpl_lastmodified')) {
					if (false != $fp = fopen($filepath, 'r')) {
						$filesource = fread($fp, filesize($filepath));
    					fclose($fp);
						$tplobj[0]->setVar('tpl_source', $filesource, true);
						$tplobj[0]->setVar('tpl_lastmodified', time());
						$tplobj[0]->setVar('tpl_lastimported', time());
    					$tplfile_handler->forceUpdate($tplobj[0]);
						$tpl_source = $filesource;
        				return true;
					}
				}
			}
		}
        $tpl_source = $tplobj[0]->getVar('tpl_source');
        return true;
    } else {
		return false;
	}
}

function smarty_resource_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    $tpl_name = smarty_resource_db_systemTpl($tpl_name);
    $tplfile_handler =& xoops_gethandler('tplfile');
    $tplobj =& $tplfile_handler->find($GLOBALS['xoopsConfig']['template_set'], null, null, null, $tpl_name, false);
	if (count($tplobj) == 0 && $GLOBALS['xoopsConfig']['template_set'] != "default") {
		$tplobj =& $tplfile_handler->find('default', null, null, null, $tpl_name, true);
	}
	if (count($tplobj) > 0) {
		if (false != $smarty->xoops_canUpdateFromFile()) {
			$conf_theme = isset($GLOBALS['xoopsConfig']['theme_set']) ? $GLOBALS['xoopsConfig']['theme_set'] : 'default';
			if ($conf_theme != 'default') {
				switch ($tplobj[0]->getVar('tpl_type')) {
					case 'module':
						$filepath = XOOPS_THEME_PATH.'/'.$conf_theme.'/templates/'.$tplobj[0]->getVar('tpl_module').'/'.$tpl_name;
						break;
					case 'block':
						$filepath = XOOPS_THEME_PATH.'/'.$conf_theme.'/templates/'.$tplobj[0]->getVar('tpl_module').'/blocks/'.$tpl_name;
						break;
					default:
						$filepath = "";
						break;
				}
			} else {
				switch ($tplobj[0]->getVar('tpl_type')) {
					case 'module':
						$filepath = XOOPS_ROOT_PATH.'/modules/'.$tplobj[0]->getVar('tpl_module').'/templates/'.$tpl_name;
						break;
					case 'block':
						$filepath = XOOPS_ROOT_PATH.'/modules/'.$tplobj[0]->getVar('tpl_module').'/templates/blocks/'.$tpl_name;
						break;
					default:
						$filepath = "";
						break;
				}
			}
			if ($filepath != "" && file_exists($filepath)) {
				$file_modified = filemtime($filepath);
				if ($file_modified > $tplobj[0]->getVar('tpl_lastmodified')) {
					$tpl_timestamp = $file_modified;
					return true;
				}
			}
		}
        $tpl_timestamp = $tplobj[0]->getVar('tpl_lastmodified');
        return true;
    } else {
		return false;
	}
}

function smarty_resource_db_secure($tpl_name, &$smarty)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_db_trusted($tpl_name, &$smarty)
{
    // not used for templates
}
?>