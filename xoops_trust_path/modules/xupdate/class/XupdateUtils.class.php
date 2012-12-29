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
	public static function getRedirectUrl($url, $redirect = 10) {
		if ($headers = @ get_headers($url, 1)) {
			$location = isset($headers['Location'])? $headers['Location'] : (isset($headers['location'])? $headers['location'] : '');
			if ($location) {
				if (is_array($location)) {
					$url = array_pop($location);
				} else {
					$url = $location;
				}
			}
		} else {
			$url = self::curlGetRedirectUrl($url);
		}
		return $url;
	}
    
	/**
	 * Get redirect URL with cURL
	 * 
	 * @param $url
	 * @param $ch
	 * @param $max_redirect
	 * @param $redirects
	 * @return string
	 */
	public static function curlGetRedirectUrl($url, $ch = null, $max_redirect = 10, $redirects = 0) {
		if ($max_redirect < $redirects) {
			return $url;
		}
		if (! $ch) {
			$ch = curl_init($url);
		}
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		
		$data = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == 301 || $http_code == 302 || $http_code == 303 || $http_code == 307) {
			list($header) = explode("\r\n\r\n", $data, 2);
			if (preg_match('/(?:Location:|URI:)(.*?)\n/i', $header, $matches)) {
				$url = trim($matches[1]);
				curl_setopt($ch, CURLOPT_URL, $url);
				$redirects++;
				return self::curlGetRedirectUrl($url, $ch, $max_redirect, $redirects);
			}
		}
		curl_close($ch);
		return $url;
	}
	
    /**
     * Check, Is directory writable
     * 
     * @param string $dir
     * @return boolean
     */
    public static function checkDirWritable($dir) {
    	$ret = false;
    	$dir = rtrim($dir, '/\\');
    	if (!empty($dir) && is_dir($dir)) {
    		$test = $dir . DIRECTORY_SEPARATOR . 'writable.check';
    		if (@ touch($test)) {
    			$ret = true;
    			unlink($test);
    		} else {
    			$ret = false;
    		}
    	}
    	return $ret;
    }
}

?>
