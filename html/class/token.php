<?php
/**
 *
 * @package Legacy
 * @version $Id: token.php,v 1.3 2008/09/25 15:12:42 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

define('XOOPS_TOKEN_TIMEOUT', 0);
define('XOOPS_TOKEN_PREFIX', "XOOPS_TOKEN_");

if (!defined('XOOPS_SALT')) {
    define('XOOPS_SALT', substr(md5(XOOPS_DB_PREFIX.XOOPS_DB_USER.XOOPS_ROOT_PATH), 5, 8));
}

define('XOOPS_TOKEN_SESSION_STRING', "X2_TOKEN");
define('XOOPS_TOKEN_MULTI_SESSION_STRING', "X2_MULTI_TOKEN");

define('XOOPS_TOKEN_DEFAULT', 'XOOPS_TOKEN_DEFAULT');

/**
 * This class express token. this has name, token's string for inquiry,
 * lifetime, serial number. this does not have direct validation method,
 * therefore this does not depend on $_Session and $_Request.
 *
 * You can refer to a handler class for this token. this token class
 * means ticket, and handler class means ticket agent. there is a strict
 * ticket agent type(XoopsSingleTokenHandler), and flexible ticket agent
 * for the tab browser(XoopsMultiTokenHandler).
 */
class XoopsToken
{
    /**
     * token's name. this is used for identification.
     * @access protected
     */
    public $_name_;

    /**
     * token's string for inquiry. this should be a random code for security.
     * @access private
     */
    public $_token_;

    /**
     * the unixtime when this token is effective.
     *
     * @access protected
     */
    public $_lifetime_;

    /**
     * unlimited flag. if this is true, this token is not limited in lifetime.
     */
    public $_unlimited_;

    /**
     * serial number. this used for identification of tokens of same name tokens.
     *
     * @access private
     */
    public $_number_=0;

    /**
     * @param   $name   this token's name string.
     * @param   $timeout    effective time(if $timeout equal 0, this token will become unlimited)
     */
    public function XoopsToken($name, $timeout = XOOPS_TOKEN_TIMEOUT)
    {
        $this->_name_ = $name;

        if ($timeout) {
            $this->_lifetime_ = time() + $timeout;
            $this->_unlimited_ = false;
        } else {
            $this->_lifetime_ = 0;
            $this->_unlimited_ = true;
        }

        $this->_token_ = $this->_generateToken();
    }


    /**
     * Returns random string for token's string.
     *
     * @access protected
     * @return string
     */
    public function _generateToken()
    {
        srand(microtime()*100000);
        return md5(XOOPS_SALT.$this->_name_.uniqid(rand(), true));
    }

    /**
     * Returns this token's name.
     *
     * @access public
     * @return string
     */
    public function getTokenName()
    {
        return XOOPS_TOKEN_PREFIX.$this->_name_."_".$this->_number_;
    }

    /**
     * Returns this token's string.
     *
     * @access public
     * @return  string
     */
    public function getTokenValue()
    {
        return $this->_token_;
    }

    /**
     * Set this token's serial number.
     *
     * @access public
     * @param   $serial_number  serial number
     */
    public function setSerialNumber($serial_number)
    {
        $this->_number_ = $serial_number;
    }

    /**
     * Returns this token's serial number.
     *
     * @access public
     * @return  int
     */
    public function getSerialNumber()
    {
        return $this->_number_;
    }

    /**
     * Returns hidden tag string that includes this token. you can use it
     * for <form> tag's member.
     *
     * @access public
     * @return  string
     */
    public function getHtml()
    {
        return @sprintf('<input type="hidden" name="%s" value="%s" />', $this->getTokenName(), $this->getTokenValue());
    }

    /**
     * Returns url string that includes this token. you can use it for
     * hyper link.
     *
     * @return  string
     */
    public function getUrl()
    {
        return $this->getTokenName()."=".$this->getTokenValue();
    }

    /**
     * If $token equals this token's string, true is returened.
     *
     * @return  bool
    */
    public function validate($token=null)
    {
        return ($this->_token_==$token && ($this->_unlimited_ || time()<=$this->_lifetime_));
    }
}

/**
 * This class express ticket agent and ticket collector. this publishes
 * token, keeps a token to server to check it later(next request).
 *
 * You can create various agents by extending the derivative class. see
 * default(sample) classes.
 */
class XoopsTokenHandler
{
    /**
     * @access private
     */
    public $_prefix ="";


    /**
     * Create XoopsToken instance, regist(keep to server), and returns it.
     *
     * @access public
     * @param   $name   this token's name string.
     * @param   $timeout    effective time(if $timeout equal 0, this token will become unlimited)
     */
    public function &create($name, $timeout = XOOPS_TOKEN_TIMEOUT)
    {
        $token =new XoopsToken($name, $timeout);
        $this->register($token);
        return $token;
    }

    /**
     * Fetches from server side, and returns it.
     *
     * @access public
     * @param   $name   token's name string.
     * @return XoopsToken
     */
    public function &fetch($name)
    {
        $ret = null;
        if (isset($_SESSION[XOOPS_TOKEN_SESSION_STRING][$this->_prefix.$name])) {
            $ret =& $_SESSION[XOOPS_TOKEN_SESSION_STRING][$this->_prefix.$name];
        }
        return $ret;
    }

    /**
     * Register token to session.
     */
    public function register(&$token)
    {
        $_SESSION[XOOPS_TOKEN_SESSION_STRING][$this->_prefix.$token->_name_] = $token;
    }

    /**
     * Unregister token to session.
     */
    public function unregister(&$token)
    {
        unset($_SESSION[XOOPS_TOKEN_SESSION_STRING][$this->_prefix.$token->_name_]);
    }

    /**
     * If a token of the name that equal $name is registered on session,
     * this method will return true.
     *
     * @access  public
     * @param   $name   token's name string.
     * @return  bool
     */
    public function isRegistered($name)
    {
        return isset($_SESSION[XOOPS_TOKEN_SESSION_STRING][$this->_prefix.$name]);
    }

    /**
     * This method takes out token's string from Request, and validate
     * token with it. if it passed validation, this method will return true.
     *
     * @access  public
     * @param   $token  XoopsToken
     * @param   $clearIfValid   If token passed validation, $token will be unregistered.
     * @return  bool
     */
    public function validate(&$token, $clearIfValid)
    {
        $req_token = isset($_REQUEST[ $token->getTokenName() ]) ?
                trim($_REQUEST[ $token->getTokenName() ]) : null;

        if ($req_token) {
            if ($token->validate($req_token)) {
                if ($clearIfValid) {
                    $this->unregister($token);
                }
                return true;
            }
        }
        return false;
    }
}

class XoopsSingleTokenHandler extends XoopsTokenHandler
{
    public function autoValidate($name, $clearIfValid=true)
    {
        if ($token =& $this->fetch($name)) {
            return $this->validate($token, $clearIfValid);
        }
        return false;
    }

    /**
     * static method.
     * This method was created for quick protection of default modules.
     * this method will be deleted in the near future.
     * @deprecated
     * @return bool
    */
    public static function &quickCreate($name, $timeout = XOOPS_TOKEN_TIMEOUT)
    {
        $handler =new XoopsSingleTokenHandler();
        $ret =& $handler->create($name, $timeout);
        return $ret;
    }

    /**
     * static method.
     * This method was created for quick protection of default modules.
     * this method will be deleted in the near future.
     * @deprecated
     * @return bool
    */
    public static function quickValidate($name, $clearIfValid=true)
    {
        $handler = new XoopsSingleTokenHandler();
        return $handler->autoValidate($name, $clearIfValid);
    }
}

/**
 * This class publish a token of the different same name of a serial number
 * for the tab browser.
 */
class XoopsMultiTokenHandler extends XoopsTokenHandler
{
    public function &create($name, $timeout=XOOPS_TOKEN_TIMEOUT)
    {
        $token =new XoopsToken($name, $timeout);
        $token->setSerialNumber($this->getUniqueSerial($name));
        $this->register($token);
        return $token;
    }

    public function &fetch($name, $serial_number)
    {
        $ret = null;
        if (isset($_SESSION[XOOPS_TOKEN_MULTI_SESSION_STRING][$this->_prefix.$name][$serial_number])) {
            $ret =& $_SESSION[XOOPS_TOKEN_MULTI_SESSION_STRING][$this->_prefix.$name][$serial_number];
        }
        return $ret;
    }

    public function register(&$token)
    {
        $_SESSION[XOOPS_TOKEN_MULTI_SESSION_STRING][$this->_prefix.$token->_name_][$token->getSerialNumber()] = $token;
    }

    public function unregister(&$token)
    {
        unset($_SESSION[XOOPS_TOKEN_MULTI_SESSION_STRING][$this->_prefix.$token->_name_][$token->getSerialNumber()]);
    }

    public function isRegistered($name, $serial_number)
    {
        return isset($_SESSION[XOOPS_TOKEN_MULTI_SESSION_STRING][$this->_prefix.$name][$serial_number]);
    }

    public function autoValidate($name, $clearIfValid=true)
    {
        $serial_number = $this->getRequestNumber($name);
        if ($serial_number!==null) {
            if ($token =& $this->fetch($name, $serial_number)) {
                return $this->validate($token, $clearIfValid);
            }
        }
        return false;
    }

    /**
     * static method.
     * This method was created for quick protection of default modules.
     * this method will be deleted in the near future.
     * @deprecated
     * @return bool
    */
    public static function &quickCreate($name, $timeout = XOOPS_TOKEN_TIMEOUT)
    {
        $handler =new XoopsMultiTokenHandler();
        $ret =& $handler->create($name, $timeout);
        return $ret;
    }

    /**
     * static method.
     * This method was created for quick protection of default modules.
     * this method will be deleted in the near future.
     * @deprecated
     * @return bool
    */
    public static function quickValidate($name, $clearIfValid=true)
    {
        $handler = new XoopsMultiTokenHandler();
        return $handler->autoValidate($name, $clearIfValid);
    }

    /**
     * @param   $name   string
     * @return  int
     */
    public function getRequestNumber($name)
    {
        $str = XOOPS_TOKEN_PREFIX.$name."_";
        foreach ($_REQUEST as $key=>$val) {
            if (preg_match("/".$str."(\d+)/", $key, $match)) {
                return intval($match[1]);
            }
        }

        return null;
    }

    public function getUniqueSerial($name)
    {
        if (isset($_SESSION[XOOPS_TOKEN_MULTI_SESSION_STRING][$name])) {
            if (is_array($_SESSION[XOOPS_TOKEN_MULTI_SESSION_STRING][$name])) {
                for ($i=0;isset($_SESSION[XOOPS_TOKEN_MULTI_SESSION_STRING][$name][$i]);$i++);
                return $i;
            }
        }

        return 0;
    }
}
