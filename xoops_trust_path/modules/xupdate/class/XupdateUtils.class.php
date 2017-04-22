<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) {
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
    public static function &getXoopsHandler(/*** string ***/ $name, /*** bool ***/ $optional = false)
    {
        // TODO will be emulated xoops_gethandler
        return xoops_gethandler($name, $optional);
    }

    /**
     * &getModuleHandler
     * 
     * @param   string  $name
     * @param   string  $dirname
     * 
     * @return  XoopsObjectHandleer
    **/
    public static function &getModuleHandler(/*** string ***/ $name, /*** string ***/ $dirname)
    {
        // TODO will be emulated xoops_getmodulehandler
        return xoops_getmodulehandler($name, $dirname);
    }

    /**
     * &getXupdateHandler
     * 
     * @param   string  $name
     * @param   string  $dirname
     * 
     * @return  XoopsObjectHandleer
    **/
    public static function &getXupdateHandler(/*** string ***/ $name, /*** string ***/ $dirname)
    {
        $asset = null;
        XCube_DelegateUtils::call(
            'Module.xupdate.Global.Event.GetAssetManager',
            new XCube_Ref($asset),
            $dirname
        );
        if (is_object($asset) && is_a($asset, 'Xupdate_AssetManager')) {
            return $asset->getObject('handler', $name);
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
    public static function toShow($str)
    {
        return htmlspecialchars(htmlspecialchars_decode($str), ENT_COMPAT, _CHARSET);
    }
    
    /**
     * Get redirect URL
     * @param string $url
     * @param int $limit
     * @return string
     */
    public static function getRedirectUrl($url, $redirect = 10, $curl_ssl_no_verify = false)
    {
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
            $url = self::curlGetRedirectUrl($url, $curl_ssl_no_verify);
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
    public static function curlGetRedirectUrl($url, $ch = null, $max_redirect = 10, $redirects = 0, $curl_ssl_no_verify = false)
    {
        if ($max_redirect < $redirects) {
            return $url;
        }
        if (! $ch) {
            $ch = curl_init($url);
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        self::setupCurlSsl($ch);
        self::setupCurlProxy($ch, $url);
        
        $data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 301 || $http_code == 302 || $http_code == 303 || $http_code == 307) {
            list($header) = explode("\r\n\r\n", $data, 2);
            if (preg_match('/(?:Location:|URI:)(.*?)\n/i', $header, $matches)) {
                $url = trim($matches[1]);
                curl_setopt($ch, CURLOPT_URL, $url);
                $redirects++;
                return self::curlGetRedirectUrl($url, $ch, $max_redirect, $redirects, $curl_ssl_no_verify);
            }
        }
        curl_close($ch);
        return $url;
    }
    
    /**
     * Setup cURL SSL options
     * 
     * @param int $ch
     * @return boolean
     */
    public static function setupCurlSsl($ch, $curl_ssl_no_verify = false)
    {
        if ($curl_ssl_no_verify) {
            return (
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false)
                &&
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false));
        } else {
            return (
                curl_setopt($ch, CURLOPT_CAINFO, XOOPS_TRUST_PATH . '/modules/xupdate/include/cacert.pem')
                    &&
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true)
                    &&
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2));
        }
    }
    
    /**
     * Setup cURL PROXY options
     * 
     * @param int $ch
     * @return array errors
     */
    public static function setupCurlProxy($ch, $url)
    {
        $errors = array();
        $proxy = '';
        
        if (! empty($_SERVER['HTTP_PROXY']) || ! empty($_SERVER['http_proxy'])) {
            $proxy = !empty($_SERVER['http_proxy'])? $_SERVER['http_proxy'] : $_SERVER['HTTP_PROXY'];
        }
        if (substr($url, 0, 5) === 'https' && (! empty($_SERVER['https_proxy']) || ! empty($_SERVER['HTTPS_PROXY']))) {
            $proxy = !empty($_SERVER['https_proxy'])? $_SERVER['https_proxy'] : $_SERVER['HTTPS_PROXY'];
        }
        if ($proxy) {
            $proxy = parse_url($proxy);
            if (!empty($proxy) && isset($proxy['host'])) {
                // url
                $proxyURL = (isset($proxy['scheme']) ? $proxy['scheme'] : 'http') . '://';
                $proxyURL .= $proxy['host'];
                
                if (isset($proxy['port'])) {
                    $proxyURL .= ":" . $proxy['port'];
                } elseif ('http://' == substr($proxyURL, 0, 7)) {
                    $proxyURL .= ":80";
                } elseif ('https://' == substr($proxyURL, 0, 8)) {
                    $proxyURL .= ":443";
                }
                try {
                    if (! curl_setopt($ch, CURLOPT_PROXY, $proxyURL)) {
                        throw new Exception('curl_setopt PROXY fail skip');
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
                // user:password
                if (isset($proxy['user'])) {
                    $proxyAuth = $proxy['user'];
                    if (isset($proxy['pass'])) {
                        $proxyAuth .= ':' . $proxy['pass'];
                    }
                    try {
                        if (! curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyAuth)) {
                            throw new Exception('curl_setopt PROXYUSERPWD fail skip');
                        }
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }
        }
        return $errors;
    }
    
    /**
     * Check, Is directory writable
     * 
     * @param string $dir
     * @return boolean
     */
    public static function checkDirWritable($dir)
    {
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
    
    /**
     * Check, Can make directory
     * 
     * @param unknown $targetDir
     * @return boolean
     */
    public static function checkMakeDirectory($targetDir)
    {
        return (@mkdir($targetDir.PATH_SEPARATOR.'mkdir_test') && @rmdir($targetDir.PATH_SEPARATOR.'mkdir_test'));
    }
    
    /**
     * check_http_timeout
     * 
     * @return void
     */
    public static function check_http_timeout()
    {
        static $start;
        if (! $start) {
            $start = time();
        }
        if (time() - $start > 270) {
            exit();
        }
    }

    public static function getCategoryList($dirname)
    {
        $isAssoc = true;
        $path = self::getModuleConfig($dirname, 'temp_path');
        $jsonData = file_get_contents(XOOPS_TRUST_PATH.'/'.$path.'/stores_json.ini.php');
        $storeData = json_decode($jsonData, $isAssoc);
        return self::convertEncoding($storeData['categories']);
    }
    
    /**
     * Convert encoding to _CHARSET from UTF-8
     * 
     * @param  mixed $arg
     * @return mixed
     */
    public static function convertEncoding($arg)
    {
        static $doConvert = null;
        if (is_null($doConvert)) {
            $doConvert = (function_exists('mb_convert_variables') && strtoupper(_CHARSET) !== 'UTF-8');
        }
        if ($doConvert) {
            mb_convert_variables(_CHARSET, 'UTF-8', $arg);
        }
        return $arg;
    }
}
