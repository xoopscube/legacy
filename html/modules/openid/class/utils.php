<?php
/**
 * utility class collecting static helper functions
 * @version $Rev$
 * @link $URL$
 */
class OpenID_Utils
{
    function redirect($message, $toFrompage = false)
    {
        if ($toFrompage) {
            $url = XOOPS_URL . '/';
            /* @var $cookie Openid_Context */
            $cookie =& OpenID_Utils::load('context');
            $cookie->accept('openid_frompage', 'string', 'cookie');
            if ($frompage = $cookie->get('openid_frompage')) {
                $parsed = parse_url(XOOPS_URL);
                $url = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : 'http://';
                if (isset($parsed['host'])) {
                    $url .= $parsed['host'];
                    if (isset($parsed['port'])) {
                        $url .= ':' . $parsed['port'];
                    }
                } else {
                    $url .= xoops_getenv('HTTP_HOST');
                }
                $url .= trim($frompage);
            }
        } else {
            $url = XOOPS_URL . '/';
        }

        unset($_SESSION['openid_response']);
        setcookie('openid_frompage', '', time() - 3600);
        redirect_header($url, 2, $message);
    }

    function &load($name)
    {
        if (file_exists($hnd_file = XOOPS_ROOT_PATH . "/modules/openid/class/handler/{$name}.php")) {
            include_once $hnd_file;
            $className = 'Openid_Handler_' . ucfirst($name);
        } else {
            include_once XOOPS_ROOT_PATH . "/modules/openid/class/{$name}.php";
            $className = 'Openid_' . ucfirst($name);
        }
        $instance = new $className();
        return $instance;
    }

    function reset()
    {
        unset($_SESSION['openid_response']);
        setcookie('openid_frompage', '', time() - 3600);
    }

    function loadEncoder()
	{
        global $xoopsConfig;
        $fileName  = XOOPS_ROOT_PATH . '/modules/openid/language/';
        $fileName .= $xoopsConfig['language'] . '/encoder.php';
        if (file_exists($fileName)) {
            require_once $fileName;
        } else {
            require_once XOOPS_ROOT_PATH . '/modules/openid/class/encoder.php';
        }
    }

    function validateToken()
    {
        global $xoopsSecurity;
        if (class_exists('XoopsMultiTokenHandler')) {
            if (!XoopsMultiTokenHandler::quickValidate(XOOPS_TOKEN_DEFAULT)) {
                return false;
            }
        } elseif (is_object($xoopsSecurity)) {
            if (!$xoopsSecurity->validateToken()) {
                return false;
            }
        }
        return true;
    }
}
?>