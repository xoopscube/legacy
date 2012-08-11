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
	 * getModuleConfig
	 *
	 * @param   string  $name
	 * @param   bool  $optional
	 *
	 * @return  XoopsObjectHandler
	 **/
	public static function getModuleConfig(/*** string ***/ $dirname, /*** mixed ***/ $key)
	{
		$handler = self::getXoopsHandler('config');
		$conf = $handler->getConfigsByDirname($dirname);
		return (isset($conf[$key])) ? $conf[$key] : null;
	}
	
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
     * getListCriteria
     *
     * @param   string  $dirname
     * @param   int     $categoryId
     * @param   int     $order
     * @param   Lenum_Status    $status
     *
     * @return  XoopsObjectHandler
     **/
    public static function getListCriteria(/*** string ***/ $dirname, /*** int ***/ $categoryId=null, /*** int ***/ $order=null, /*** int ***/ $status=Lenum_Status::PUBLISHED)
    {
//    	$accessController = self::getAccessControllerModule($dirname);
    
    	$cri = new CriteriaCompo();
    
//     	//category
//     	if(isset($categoryId)){
//     		$cri->add(new Criteria('category_id', $categoryId));
//     	}
//     	else{
//     		//get permitted categories to show
//     		if($accessController instanceof XoopsModule && ($accessController->get('role')=='cat' || $accessController->get('role')=='group')){
//     			$idList = self::getPermittedIdList($dirname);
//     			if(count($idList)>0){
//     				$cri->add(new Criteria('category_id', $idList, 'IN'));
//     			}
//     		}
//     	}
    
    	return $cri;
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
