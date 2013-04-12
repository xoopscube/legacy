<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

/**
 * Lecat_Utils
**/
class Lecat_Utils
{
	/**
	 * &getXoopsHandler
	 * 
	 * @param	string	$name
	 * @param	bool  $optional
	 * 
	 * @return	XoopsObjectHandler
	**/
	public static function &getXoopsHandler(/*** string ***/ $name,/*** bool ***/ $optional = false)
	{
		// TODO will be emulated xoops_gethandler
		return xoops_gethandler($name,$optional);
	}

	/**
	 * getEnv
	 * 
	 * @param	string	$key
	 * 
	 * @return	string
	**/
	public static function getEnv(/*** string ***/ $key)
	{
		return getenv($key);
	}

    /**
     * getModuleConfig
     *
     * @param   string  $dirname
     * @param   string  $key
     *
     * @return  mixed
     **/
    public static function getModuleConfig(/*** string ***/ $dirname, /*** string ***/ $key)
    {
        static $config = array();
        if(! isset($config[$dirname])){
            $chandler = xoops_gethandler('config');
            $config[$dirname] = $chandler->getConfigsByDirname($dirname);
        }
        return isset($config[$dirname][$key]) ? $config[$dirname][$key] : null;
    }

    public static function getImageNameList(/*** string ***/ $dirname)
    {
        $list = array();
        return trim(self::getModuleConfig($dirname, 'images')) ? preg_split('/\x0d\x0a|\x0d|\x0a/', self::getModuleConfig($dirname, 'images'), null) : array();
    }

	/**
	 * getActorList
	 * 
	 * @param	string	$dirname
	 * 
	 * @return	string[]
	**/
	public static function getActorList(/*** string ***/ $dirname)
	{
		$handler = xoops_gethandler('config');
		$conf = $handler->getConfigsByDirname($dirname);
	
		return isset($conf['actors']) ? unserialize($conf['actors']) : array('key'=>array('viewer','poster','manager'),'title'=>array('Viewer', 'Poster', 'Manager'),'default'=>array(1,1,0));
	}

    /**
     * getClientList
     * 
     * @param   string  $dirname
     * 
     * @return  array
    **/
	public static function getClientList(/*** string ***/ $dirname)
	{
		$clients = array();
		$list = array();
		XCube_DelegateUtils::call('Legacy_CategoryClient.GetClientList', new XCube_Ref($clients), $dirname);
		foreach($clients as $module){
			$list[] = array('dirname'=>trim($module['dirname']), 'dataname'=>trim($module['dataname']), 'fieldname'=>trim($module['fieldname']));
		}
		return $list;
	}
}

?>
