<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Service.class.php,v 1.4 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * @internal
 * @brief This is a kind of MACRO like C for XCube_Service.
 */
function S_PUBLIC_FUNC($definition)
{
    $pos = strpos($definition, '(');
    if ($pos > 0) {
        $params = array();
        foreach (explode(',', substr($definition, $pos + 1, -1)) as $t_param) {
            if ($t_param) {
                list($k, $v) = explode(' ', trim($t_param));
                $params[$k] = $v;
            }
        }
        $ret = array('in' => $params);
        list($ret['out'], $ret['name']) = explode(' ', substr($definition, 0, $pos));
        return $ret;
    }
    return null;
}

/**
 * @public
 * @brief [Abstract] This class is a collection for functions.
 * 
 * @bug This class does NOT work perfectly. It's fatal...
 * @todo Fix fatal bugs.
 */
class XCube_Service
{
    /**
     * @protected
     * @brief string
     */
    public $mServiceName = "";
    
    /**
     * @protected
     * @brief string
     */
    public $mNameSpace = "";
    
    /**
     * @protected
     */
    public $mClassName = "XCube_Service";
    
    /**
     * @protected
     * @brief XCube_ActionStrategy(?) --- 'deprecated'
     * @deprecated
     */
    public $_mActionStrategy = null;
    
    public $_mTypes = array();
    
    public $_mFunctions = array();
    
    // !Fix PHP7
    public function __construct()
    //public function XCube_Service()
    {
    }
    
    public function prepare()
    {
    }
    
    public function addType($className)
    {
        $this->_mTypes[] = $className;
    }
    
    public function addFunction()
    {
        $args = func_get_args();
        $n = func_num_args();
        $arg0 = &$args[0];

        if ($n == 3) {
            $this->_addFunctionStandard($arg0, $args[1], $args[2]);
        } elseif ($n == 1 && is_array($arg0)) {
            $this->_addFunctionStandard($arg0['name'], $arg0['in'], $arg0['out']);
        }
    }
    
    public function _addFunctionStandard($name, $in, $out)
    {
        $this->_mFunctions[$name] = array(
            'out' => $out,
            'name' => $name,
            'in' => $in
        );
    }

    /**
     * @var   string          $name
     * @param XCube_Procedure $procedure
     */
    public function register($name, &$procedure)
    {
    }
}

/**
 * @public
 * @brief [Experiment Class] The adapter for a service class.
 * 
 * This class is the adapter of a service class.
 * I give a caller the interface that resembled NUSOAP.
 */
class XCube_AbstractServiceClient
{
    public $mService;
    public $mClientErrorStr;
    
    public $mUser = null;
    
    // !Fix PHP7
    public function __construct(&$service)
    //public function XCube_AbstractServiceClient(&$service)
    {
        $this->mService =& $service;
    }
    
    public function prepare()
    {
    }
    
    public function setUser(&$user)
    {
        $this->mUser =& $user;
    }

    public function call($operation, $params)
    {
    }
    
    public function getOperationData($operation)
    {
    }

    public function setError($message)
    {
        $this->mClientErrorStr = $message;
    }

    public function getError()
    {
        return !empty($this->mClientErrorStr) ? $this->mClientErrorStr : $this->mService->mErrorStr;
    }
}

/**
 * @public
 * @brief [Abstract] Interface to be used for accessing a Service.
 * 
 * The client object for XCube_Service(Inner service). This class calls
 * functions directly, but exchanges the request object of the context to
 * enable the service logic to get values by the request object. After calls,
 * restores the original request object.
 */
class XCube_ServiceClient extends XCube_AbstractServiceClient
{
    public function call($operation, $params)
    {
        $this->mClientErrorStr = null;
        
        if (!is_object($this->mService)) {
            $this->mClientErrorStr = "This instance is not connected to service";
            return null;
        }
        
        $root =& XCube_Root::getSingleton();
        $request_bak =& $root->mContext->mRequest;
        unset($root->mContext->mRequest);
        
        $root->mContext->mRequest = new XCube_GenericRequest($params);
        
        if (isset($this->mService->_mFunctions[$operation])) {
            $ret = call_user_func(array($this->mService, $operation));
            
            unset($root->mContext->mRequest);
            $root->mContext->mRequest =& $request_bak;
            
            return $ret;
        } else {
            $this->mClientErrorStr = "operation ${operation} not present.";
            return null;
        }
    }
}
