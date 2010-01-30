<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_Utils.class.php,v 1.5 2008/09/25 15:11:21 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/***
 * @internal
 * @public
 * @brief The collection of static utility functions for Legacy.
 */
class Legacy_Utils
{
	/***
	 * Checks whether must modules have been installed.
	 * @static
	 * @return mixed Returns hashmap including the list of uninstalled,
	 * disabled and recommended modules, basically. But, if there is no problem,
	 * returns true.
	 */
	function checkSystemModules()
	{
		$root=&XCube_Root::getSingleton();
		$systemModules = array_map('trim', explode(',', $root->getSiteConfig('Cube', 'SystemModules')));
		$recommendedModules = array_map('trim', explode(',', $root->getSiteConfig('Cube', 'RecommendedModules')));
		$moduleHandler =& xoops_gethandler('module');
		$uninstalledModules = array();
		$disabledModules = array();
		foreach ($systemModules as $systemModule) {
			if (!empty($systemModule)) {
				if (!($moduleObject =& $moduleHandler->getByDirname($systemModule))) {
					$uninstalledModules[] = $systemModule;
				}
				elseif (!$moduleObject->get('isactive')) {
					$disabledModules[] = $systemModule;
				}
			}
		}
		if (count($uninstalledModules) == 0 && count($disabledModules) == 0) {
			return true;
		}
		else {
			return array('uninstalled' =>$uninstalledModules, 'disabled'=>$disabledModules, 'recommended'=>$recommendedModules);
		}
	}
	
	/***
	 * Creates a instance of the module with the generating convention. And,
	 * returns it.
	 * @param XoopsModule $module
	 * @return Legacy_Module
	 */
	function &createModule($module)
	{
		$instance = null;

		//
		// TODO need cache here?
		//
		XCube_DelegateUtils::call('Legacy_Utils.CreateModule', new XCube_Ref($instance), $module);
		
		if (is_object($instance) && is_a($instance, 'Legacy_AbstractModule')) {
			return $instance;
		}
		
		$dirname = $module->get('dirname');
		
		//
		// IMPORTANT CONVENTION
		//
		$className = ucfirst($dirname) . "_Module";
		if (!XC_CLASS_EXISTS($className)) {
			$filePath = XOOPS_ROOT_PATH . "/modules/${dirname}/class/Module.class.php";
			if (file_exists($filePath)) {
				require_once $filePath;
			}
		}
		
		if (XC_CLASS_EXISTS($className)) {
			$instance =& new $className($module);
		}
		else {
			$instance =& new Legacy_ModuleAdapter($module);
		}
		
		return $instance;
	}
	
	/***
	 * Creates a instance of the block procedure with the generating convention.
	 * And, returns it.
	 * @static
	 * @return Legacy_BlockProcedure
	 */
	function &createBlockProcedure(&$block)
	{
		//
		// IMPORTANT CONVENTION
		//
		$retBlock = null;
		
		//
		// TODO need cache here?
		//
		XCube_DelegateUtils::call('Legacy_Utils.CreateBlockProcedure', new XCube_Ref($retBlock), $block);
		
		if (is_object($retBlock) && is_a($retBlock, 'Legacy_AbstractBlockProcedure')) {
			return $retBlock;
		}
		
		$func = $block->get('show_func');
		if (substr($func, 0, 4) == 'cl::') {
			$className = ucfirst($block->get('dirname')) . '_' . substr($func, 4);
			if (!XC_CLASS_EXISTS($className)) {
				$filePath = XOOPS_ROOT_PATH . '/modules/' . $block->get('dirname') . '/blocks/' . $block->get('func_file');
				if (!file_exists($filePath)) {
					$retBlock =& new Legacy_BlockProcedureAdapter($block);
					return $retBlock;
				}
				
				require_once $filePath;
				
				if (!XC_CLASS_EXISTS($className)) {
					$retBlock =& new Legacy_BlockProcedureAdapter($block);
					return $retBlock;
				}
			}
				
			$retBlock =& new $className($block);
		}
		else {
			$retBlock =& new Legacy_BlockProcedureAdapter($block);
		}
		
		return $retBlock;
	}
	
	/***
	 * Calls user controll event.
	 */
	function raiseUserControlEvent()
	{
		$root =& XCube_Root::getSingleton();
		foreach (array_keys($_REQUEST) as $key) {
			if (strpos($key, 'Legacy_Event_User_') === 0) {
				$eventName = substr($key, 18);
				XCube_DelegateUtils::call('Legacy.Event.User.' . $eventName);
				$root->mContext->mAttributes['userEvent'][$eventName] = true;
			}
		}
	}
	
	/***
	 * Converts the version of the module from $modversion value to interger
	 * number.
	 * @param string $version
	 * @return int
	 */
	function convertVersionFromModinfoToInt($version)
	{
		return round(100 * floatval($version));
	}

	/***
	 * Converts the version of the module from DB value to float.
	 * @param int $version
	 * @return float
	 */
	function convertVersionIntToFloat($version)
	{
		return round(floatval(intval($version) / 100), 2);
	}

    /**
     * getModuleConfig
     * 
     * @param   string	$type
     * @param   string	$dirname
     * 
     * @return  mix
    **/
    public static function getModuleConfig($type, $dirname)
    {
		$handler = self::getXoopsHandler('config');
		$configArr = $handler->getConfigsByDirname($dirname);
		return $configArr[$type];
    }

    /**
     * getUid
     * 
     * @param   void
     * 
     * @return  int
    **/
    public static function getUid()
    {
        $root = XCube_Root::getSingleton();
        return ($root->mContext->mUser->isInRole('Site.RegisteredUser')) ? $root->mContext->mXoopsUser->get('uid') : 0;
    }

    /**
     * getDirnameListByTrustName
     * 
     * @param   string	$trustName
     * 
     * @return  string[]
    **/
    public static function getDirnameListByTrustName(/*** string ***/ $trustName)
    {
        $list = array();
        $cri = new Criteria('isactive',0,'>');
        $cri->addSort('weight','ASC');
        $cri->addSort('mid','ASC');
        foreach(xoops_gethandler('module')->getObjects($cri) as $module)
        {
            if($module->getInfo('trust_dirname') == $trustName)
            {
                $list[] = $module->get('dirname');
            }
        }
        return $list;
    }

    /**
     * getTrustNameByDirname
     * 
     * @param   string	$dirname
     * 
     * @return  string
    **/
    public static function getTrustNameByDirname(/*** string ***/ $dirname)
    {
        $list = array();
        $cri = new Criteria('isactive',0,'>');
        $cri->addSort('weight','ASC');
        $cri->addSort('mid','ASC');
        foreach(xoops_gethandler('module')->getObjects($cri) as $module)
        {
            if($module->getInfo('dirname') == $dirname)
            {
                return $module->get('trust_dirname');
            }
        }
    }

    /**
     * getModuleIcon
     * 
     * @param   string	$dirname
     * @param   string	$baseIconPath
     * 
     * @return  string
    **/
	public static function getModuleIcon(/*** string ***/ $dirname, /*** string ***/ $baseIconPath="images/module_icon.png")
	{
		$moduleIconPath = 'images/module_icon.png';
		if(file_exists(XOOPS_MODULE_PATH .'/'. $dirname .'/'. $moduleIconPath)){
			return $moduleIconPath;
		}
		else{
			$xoopsIconPath = XOOPS_ROOT_PATH .'/'. $baseIconPath;
			$icon_cache_limit = 3600; // default 3600sec == 1hour
			session_cache_limiter('public');
		
			header("Expires: ".date('r',intval(time()/$icon_cache_limit)*$icon_cache_limit+$icon_cache_limit));
			header("Cache-Control: public, max-age=$icon_cache_limit");
			header("Last-Modified: ".date('r',intval(time()/$icon_cache_limit)*$icon_cache_limit));
			header("Content-type: image/png");
		
			if(function_exists('imagecreatefrompng') && function_exists('imagecolorallocate') && function_exists('imagestring') && function_exists('imagepng')) {
				$im = imagecreatefrompng($xoopsIconPath);
			
				$color = imagecolorallocate($im , 255 , 255 , 255); // white
				$px = (127 - 6 * strlen($dirname)) / 2;
				imagestring($im , 2 , $px , 5 , $dirname , $color);
				imagepng($im, XOOPS_MODULE_PATH .'/'. $dirname .'/'. $moduleIconPath);
				imagedestroy($im);
				return $moduleIconPath;
			} else {
				return $xoopsIconPath;
			}
		}
	}
}

?>
