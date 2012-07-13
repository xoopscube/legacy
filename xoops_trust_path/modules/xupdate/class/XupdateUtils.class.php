<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

/**
 * Xupdate_Utils
**/
class Xupdate_Utils
{
    /**
     * &getXoopsHandler
     * 
     * @param   string  $name
     * @param   bool  $optional
     * 
     * @return  XoopsObjectHandler
    **/
    public static function &getXoopsHandler(/*** string ***/ $name,/*** bool ***/ $optional = false)
    {
        // TODO will be emulated xoops_gethandler
        return xoops_gethandler($name,$optional);
    }

    /**
     * &getModuleHandler
     * 
     * @param   string  $name
     * @param   string  $dirname
     * 
     * @return  XoopsObjectHandleer
    **/
    public static function &getModuleHandler(/*** string ***/ $name,/*** string ***/ $dirname)
    {
        // TODO will be emulated xoops_getmodulehandler
        return xoops_getmodulehandler($name,$dirname);
    }

    /**
     * &getXupdateHandler
     * 
     * @param   string  $name
     * @param   string  $dirname
     * 
     * @return  XoopsObjectHandleer
    **/
    public static function &getXupdateHandler(/*** string ***/ $name,/*** string ***/ $dirname)
    {
        $asset = null;
        XCube_DelegateUtils::call(
            'Module.xupdate.Global.Event.GetAssetManager',
            new XCube_Ref($asset),
            $dirname
        );
        if(is_object($asset) && is_a($asset, 'Xupdate_AssetManager'))
        {
            return $asset->getObject('handler',$name);
        }
    }

    /**
     * getEnv
     * 
     * @param   string  $key
     * 
     * @return  string
    **/
    public static function getEnv(/*** string ***/ $key)
    {
        return getenv($key);
    }

    /**
     * Text sanitizer for toShow
     * @param  string $str
     * @return string
     */
    public static function toShow($str) {
    	return htmlspecialchars(htmlspecialchars_decode($str));
    }
    
	/**
	 * Get redirect URL
	 * @param string $url
	 * @param int $limit
	 * @return string
	 */
	public static function getRedirectUrl($url, $limit = 4) {
		$headers = get_headers($url, 1);
		if($limit &&  preg_match('#^HTTP/\d\.\d\s+(301|302|303|307)#',$headers[0]) && isset($headers['Location'])) {
			return self::getRedirectUrl(trim($headers['Location']), --$limit);
		}
		return $url;
    }
}

?>
