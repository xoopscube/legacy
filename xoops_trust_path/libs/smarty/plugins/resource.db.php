<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File:     resource.db.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a database
 * Version:  for XOOPS Cube Legacy 2.2
 *           Based on Hodajuku Distribution 1.04 resource.db.php
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
	if (!$tpl = smarty_resource_db_tplinfo($tpl_name, $smarty)) {
		return false;
	}
	if (is_object($tpl)) {
		$tpl_source = $tpl->getVar('tpl_source', 'n');
	} else {
		$tpl_source = file_get_contents($tpl) ;
	}
	return true;
}

function smarty_resource_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
	$tpl_name = smarty_resource_db_systemTpl($tpl_name);
	if (!$tpl = smarty_resource_db_tplinfo($tpl_name, $smarty)) {
		return false;
	}
	if (is_object($tpl)) {
		$tpl_timestamp = $tpl->getVar('tpl_lastmodified', 'n');
	} else {
		$tpl_timestamp = filemtime($tpl);
	}
	return true;
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

// return object(XoopsTplfile) or string(filepath)
function smarty_resource_db_tplinfo($tpl_name, $smarty)
{
	static $cache = array();
	static $tplset = null;
	static $theme = null;
	static $theme_default = null;
	static $entries = null;
	
	global $xoopsConfig;
	
	// 1st, check the cache
	if (isset($cache[$tpl_name])) {
		return $cache[$tpl_name];
	}

	if (is_null($tplset)) {
		$tplset = isset($xoopsConfig['template_set']) ? $xoopsConfig['template_set']: 'default' ;
		$theme = isset($xoopsConfig['theme_set']) ? $xoopsConfig['theme_set'] : 'default';
		
		if (($_pos = strpos($theme, '_')) && substr($theme, $_pos) !== '_default') {
			$theme_default = substr($theme, 0, $_pos) . '_default';
		} else {
			$theme_default = '';
		}
		$root = XCube_Root::getSingleton();
		if (! $resourceDiscoveryOrder = $root->getSiteConfig('Smarty', 'ResourceDiscoveryOrder')) {
			$resourceDiscoveryOrder = 'Theme,ThemeD3,ThemeDefault,ThemeDefaultD3,DbTplSet';
		}
		$entries = array_map('strtoupper', array_map('trim', explode(',', $resourceDiscoveryOrder)));
	}
	
	@list($dirname , $base_tpl_name) = explode('_' , $tpl_name , 2) ;
	$mytrustdirname = Legacy_ResourcedbUtils::getTrustPath($dirname);
	
	foreach($entries as $entry) {
		switch($entry) {
			case 'THEME':
				// check templates under themes/(theme)/templates/ (file template)
				$filepath = XOOPS_THEME_PATH . '/' . $theme . '/templates/' . $tpl_name ;
				if (is_file($filepath)) {
					return $cache[$tpl_name] = $filepath ;
				}
				break;
				
			case 'THEMED3':
				// check templates under themes/(theme)/templates/(trust based template)
				if($mytrustdirname && $base_tpl_name) {
					$filepath = XOOPS_THEME_PATH . '/' . $theme . '/templates/' . $mytrustdirname . '/' . $base_tpl_name ;
					if (is_file($filepath)) {
						return $cache[$tpl_name] = $filepath ;
					}
				}
				break;
				
			case 'THEMEDEFAULT':
				// check templates under themes/(theme prefix)_default/templates/ (file template)
				if ($theme_default) {
					$filepath = XOOPS_THEME_PATH . '/' . $theme_default . '/templates/' . $tpl_name ;
					if (is_file($filepath)) {
						return $cache[$tpl_name] = $filepath ;
					}
				}
				break;
				
			case 'THEMEDEFAULTD3':
				// check templates under themes/(theme prefix)_default/templates/(trust based template)
				if($theme_default && $mytrustdirname && $base_tpl_name) {
					$filepath = XOOPS_THEME_PATH . '/' . $theme_default . '/templates/' . $mytrustdirname . '/' . $base_tpl_name ;
					if (is_file($filepath)) {
						return $cache[$tpl_name] = $filepath ;
					}
				}
				break;
				
			case 'DBTPLSET':
				// find a DB template of the selected tplset
				// check template update
				$tplfileHandler =& xoops_gethandler('tplfile');
				$tplObj = $tplfileHandler->find($tplset, null, null, null, $tpl_name, true);
				if(!empty($tplObj)) {
					return $cache[$tpl_name] = $tplObj[0];
				}
				break;
				
			DEFAULT:
		}
	}
	
	// Finally, find a DB template in default tplset
	if (! isset($tplfileHandler)) {
		$tplfileHandler =& xoops_gethandler('tplfile');
	}
	$tplObj = $tplfileHandler->find('default', null, null, null, $tpl_name, true);
	if(empty($tplObj)){
		return false;
	}
	//update template if admin user and new template file exists
	if(XCube_Root::getSingleton()->mContext->mUser->isInRole('Site.Administrator') && $smarty->xoops_canUpdateFromFile()){
		Legacy_ResourcedbUtils::updateTemplate($tplObj[0]);
	}
	return $cache[$tpl_name] = $tplObj[0];
}

class Legacy_ResourcedbUtils
{
	public static function getModuleTemplatePath(XoopsTplfile $tplObj)
	{
		$block = ($tplObj->getVar('tpl_type')==='block') ? '/blocks' : null;
		$dirname = $tplObj->getVar('tpl_module');
		$modulePath = $dirname.'/templates'.$block;
	
		//Case 1:under public root_path with dirname in template name like 'cat'
		$publicPath = XOOPS_MODULE_PATH.'/'.$modulePath.'/'.$tplObj->getVar('tpl_file');
		if(is_file($publicPath)){
			return $publicPath;
		}
	
		//prepare for Case 2 and Case 3
		if(! $trustDirname = self::getTrustPath($dirname)){
			return false;
		}
		$filename = preg_replace('/'.$dirname.'/', $trustDirname, $tplObj->getVar('tpl_file'), 1);
	
		//Case 2:under public root_path with trust_dirname in template name like 'lecat'
		$publicPath = XOOPS_MODULE_PATH.'/'.$modulePath.'/'.$filename;
		if(is_file($publicPath)){
			return $publicPath;
		}
		//Case 3:under trust_path
		$trustPath = XOOPS_TRUST_PATH.'/modules/'.$trustDirname.'/templates'.$block.'/'.$filename;
		if(is_file($trustPath)){
			return $trustPath;
		}
		return false;
	}

	public static function getTrustPath(/*** string ***/ $dirname)
	{
		if(is_file(XOOPS_ROOT_PATH.'/modules/'.$dirname.'/mytrustdirname.php')){
			@include XOOPS_ROOT_PATH.'/modules/'.$dirname.'/mytrustdirname.php' ;
			return $mytrustdirname;
		}
		else{
			$root = XCube_Root::getSingleton();
			$handler = xoops_gethandler('module');
			$module = $handler->getByDirname($dirname);
			return ($module && ($trustDirname = $module->get('trust_dirname'))) ? $trustDirname : null;
		}
	}

	public static function updateTemplate($tplObj)
	{
		if($filepath = self::getModuleTemplatePath($tplObj)){
			$file_modified = filemtime($filepath);
			if ($file_modified > $tplObj->getVar('tpl_lastmodified')) {
				if (false != $fp = fopen($filepath, 'r')) {
					$handler = xoops_gethandler('tplfile');
					$filesource = fread($fp, filesize($filepath));
					fclose($fp);
					$tplObj->setVar('tpl_source', $filesource, true);
					$tplObj->setVar('tpl_lastmodified', time());
					$tplObj->setVar('tpl_lastimported', time());
					$handler->forceUpdate($tplObj);
				}
			}
		}
	}
}

?>