<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_HttpContext.class.php,v 1.4 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

define("XCUBE_CONTEXT_TYPE_DEFAULT", "web_browser");
define("XCUBE_CONTEXT_TYPE_WEB_SERVICE", "web_service");

/**
 * Encapsulates major HTTP specific information about a HTTP request.
 */
class XCube_HttpContext
{
    /**
     * Hashmap that can be used to organize and share data. Use setAttribute()
     * and get Attribute() to access this member property. But, direct access
     * is allowed, because PHP4 is unpossible to handle reference well.
     *
     * @var Array
     * @access protected
     */
    public $mAttributes = array();
    
    /**
     * The object which enables to read the request values.
     *
     * @access XCube_AbstractRequest
     */
    public $mRequest = null;
    
    /**
     * @var XCube_Principal
     */
    public $mUser = null;
    
    /**
     * String which expresses the type of the current request.
     * @var string
     */
    public $mType = XCUBE_CONTEXT_TYPE_DEFAULT;

    /**
     * The theme is one in one time of request.
     * A decided theme is registered with this property
     *
     * @access private
     */
    public $mThemeName = null;
    // !Fix PHP7
    public function __construct()
    //public function XCube_HttpContext()
    {
    }
    
    /**
     * Sets $value with $key to attributes. Use direct access to $mAttributes
     * if references are must, because PHP4 can't handle reference in the
     * signature of this member function.
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        $this->mAttributes[$key] = $value;
    }

    /**
     * Gets a value indicating whether the value specified by $key exists.
     * 
     * @param string $key
     * @return mixed
     */
    public function hasAttribute($key)
    {
        return isset($this->mAttributes[$key]);
    }
    
    /**
     * Gets a value of attributes with $key. If the value specified by $key
     * doesn't exist in attributes, gets null.
     * 
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return isset($this->mAttributes[$key]) ? $this->mAttributes[$key] : null;
    }

    /**
     * Sets the object which has a interface of XCube_AbstractRequest.
     *
     * @param XCube_AbstractRequest $request
     */
    public function setRequest(&$request)
    {
        $this->mRequest =& $request;
    }
    
    /**
     * Gets the object which has a interface of XCube_AbstractRequest.
     *
     * @return XCube_AbstractRequest
     */
    public function &getRequest()
    {
        return $this->mRequest;
    }

    /**
     * Sets the object which has a interface of XCube_Principal.
     *
     * @param XCube_AbstractPrincipal $principal
     */
    public function setUser(&$principal)
    {
        $this->mUser =& $principal;
    }
    
    /**
     * Gets the object which has a interface of XCube_Principal.
     *
     * @return XCube_AbstractPrincipal
     */
    public function &getUser()
    {
        return $this->mUser;
    }

    /**
     * Set the theme name.
     * 
     * @param $theme string
     * @deprecated
     */
    public function setThemeName($theme)
    {
        $this->mThemeName = $theme;
    }
    
    /**
     * Return the theme name.
     * 
     * @return string
     * @deprecated
     */
    public function getThemeName()
    {
        return $this->mThemeName;
    }
}

/**
 * This is an interface for request classes.
 */
class XCube_AbstractRequest
{
    /**
     * Gets a value of the current request.
     *
     * @param $key
     * @return mixed
     */
    public function getRequest($key)
    {
        return null;
    }
}

/**
 * Enables a program to read the HTTP values through XCubeAbstractRequest
 * interface.
 */
class XCube_HttpRequest extends XCube_AbstractRequest
{
    /**
     * Gets a value of the current HTTP request. The return value doesn't
     * include quotes which are appended by magic_quote_gpc, even if it's
     * active.
     * 
     * @param string $key
     * @return mixed
     */
    public function getRequest($key)
    {
        if (!isset($_GET[$key]) && !isset($_POST[$key])) {
            return null;
        }
        
        $value = isset($_GET[$key]) ? $_GET[$key] : $_POST[$key];
        
        if (!get_magic_quotes_gpc()) {
            return $value;
        }
        
        if (is_array($value)) {
            return $this->_getArrayRequest($value);
        }
        
        return stripslashes($value);
    }
    
    /**
     * Supports getRequest().
     *
     * @private
     * @param Array $arr
     * @return Array
     */
    public function _getArrayRequest($arr)
    {
        foreach (array_keys($arr) as $t_key) {
            if (is_array($arr[$t_key])) {
                $arr[$t_key] = $this->_getArrayRequest($arr[$t_key]);
            } else {
                $arr[$t_key] = stripslashes($arr[$t_key]);
            }
        }
        
        return $arr;
    }
}

/**
 * A kind of request objects. This class is free to register values.
 */
class XCube_GenericRequest extends XCube_AbstractRequest
{
    /**
     * Hash map which stores registered values.
     * @var Array
     */
    public $mAttributes = array();
     // !Fix PHP7
    public function __construct($arr = null)  
    //public function XCube_GenericRequest($arr = null)
    {
        if (is_array($arr)) {
            $this->mAttributes = $arr;
        }
    }

    public function getRequest($key)
    {
        if (!isset($this->mAttributes[$key])) {
            return null;
        }
        
        return $this->mAttributes[$key];
    }
}
