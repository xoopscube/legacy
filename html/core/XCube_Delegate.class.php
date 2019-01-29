<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Delegate.class.php,v 1.9 2008/11/16 10:05:55 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * @public
 * @brief [Final] This class is an expression of reference in delegation mechanism for PHP4.
 * 
 * This class is adapt reference pointer for XCube_Delegate. Because XCube_Delegate is
 * virtual function pointers, it's impossible to hand variables as references to
 * XCube_Delegate::call(). In a such case, use this class as an adapter.
 * 
 * \code
 *   $object = new Object;
 *   $delegate->call($object); // In PHP4, functions will receive the copied value of $object.
 * 
 *   $object = new Object;
 *   $delegate->call(new XCube_Delegate($object)); // In PHP4, functions will receive the object.
 * \endcode
 */
class XCube_Ref
{
    /**
     * @private
     * @brief mixed
     */
    public $_mObject = null;

    /**
     * @public Constructor.
     * @param $obj mixed
     */
    // !Fix PHP7
    public function __construct(&$obj)
    //public function XCube_Ref(&$obj)
    {
        $this->_mObject =& $obj;
    }

    /**
     * @public
     * @internal
     * @brief [Secret Agreement] Gets the value which this class is adapting.
     * @return mixed
     * @attention
     *     Only XCube_Delegate & XCube_DelegateManager should call this method.
     */
    public function &getObject()
    {
        return $this->_mObject;
    }
}

//
// Constants for delegate priority.
// But, developers should use {first,normal,firnal} basically.
//

define("XCUBE_DELEGATE_PRIORITY_1", 10);
define("XCUBE_DELEGATE_PRIORITY_2", 20);
define("XCUBE_DELEGATE_PRIORITY_3", 30);
define("XCUBE_DELEGATE_PRIORITY_4", 40);
define("XCUBE_DELEGATE_PRIORITY_5", 50);
define("XCUBE_DELEGATE_PRIORITY_6", 60);
define("XCUBE_DELEGATE_PRIORITY_7", 70);
define("XCUBE_DELEGATE_PRIORITY_8", 80);
define("XCUBE_DELEGATE_PRIORITY_9", 90);
define("XCUBE_DELEGATE_PRIORITY_10", 100);

define("XCUBE_DELEGATE_PRIORITY_FIRST", XCUBE_DELEGATE_PRIORITY_1);
define("XCUBE_DELEGATE_PRIORITY_NORMAL", XCUBE_DELEGATE_PRIORITY_5);
define("XCUBE_DELEGATE_PRIORITY_FINAL", XCUBE_DELEGATE_PRIORITY_10);

define("XCUBE_DELEGATE_CHAIN_BREAK", -1);

/**
 * @public
 * @brief [Final] Used for the simple mechanism for common delegation in XCube.
 * 
 * A delegate can have $callback as connected function, $filepath for lazy
 * loading and $priority as order indicated.
 * 
 * \par Priority
 * 
 * Default of this parameter is XCUBE_DELEGATE_PRIORITY_NORMAL. Usually, this
 * parameter isn't specified. Plus, the magic number should be used to specify
 * priority. Use XCUBE_DELEGATE_PRIORITY_FIRST or XCUBE_DELEGATE_PRIORITY_FINAL
 * with Addition and Subtraction. (e.x. XCUBE_DELEGATE_PRIORITY_NORMAL - 1 )
 * 
 * @attention
 *     This is the candidate as new delegate style, which has foolish name to escape
 *     conflict with old XCube_Delegate. After replacing, we'll change all.
 */
class XCube_Delegate
{
    /**
     * @private
     * @brief Vector Array - The list of type of parameters.
     */
    public $_mSignatures = array();
    
    /**
     * @private
     * @brief Complex Array - This is Array for callback type data.
     */
    public $_mCallbacks = array();
    
    /**
     * @private
     * @brief bool
     */
    public $_mHasCheckSignatures = false;
    
    /**
     * @private
     * @brief bool
     * 
     * If register() is failed, this flag become true. That problem is raised,
     * when register() is called before $root come to have the delegate
     * manager.
     * 
     * @var bool
     */
    public $_mIsLazyRegister = false;
    
    /**
     * @private
     * @brief string - This is register name for lazy registering.
     */
    public $_mLazyRegisterName = null;

    /**
     * @private
     */
    public $_mUniqueID;
    
    /**
     * @public
     * @brief Constructor.
     * 
     * The parameter of the constructor is a variable argument style to specify
     * the sigunature of this delegate. If the argument is empty, signature checking
     * doesn't work. Empty arguments are good to use in many cases. But, that's
     * important to accent a delegate for making rightly connected functions.
     * 
     * \code
     *   $delegate =new XCube_Delegate("string", "string");
     * \endcode
     */
    // !Fix PHP7
    public function __construct()
    //public function XCube_Delegate()
    {
        if (func_num_args()) {
            $this->_setSignatures(func_get_args());
        }
        $this->_mUniqueID = uniqid(rand(), true);
    }
    
    /**
     * @private
     * @brief Set signatures for this delegate.
     * @param $args Vector Array - std::vector<string>
     * @return void
     * 
     * By this method, this function will come to check arguments with following
     * signatures at call().
     */
    public function _setSignatures($args)
    {
        $this->_mSignatures =& $args;
        for ($i=0, $max=count($args); $i<$max ; $i++) {
            $arg = $args[$i];
            $idx = strpos($arg, ' &');
            if ($idx !== false) {
                $args[$i] = substr($arg, 0, $idx);
            }
        }
        $this->_mHasCheckSignatures = true;
    }
    
    /**
     * @public
     * @brief Registers this object to delegate manager of root.
     * @param $delegateName string
     * @return bool
     */
    public function register($delegateName)
    {
        $root =& XCube_Root::getSingleton();
        if ($root->mDelegateManager != null) {
            $this->_mIsLazyRegister = false;
            $this->_mLazyRegisterName = null;
        
            return $root->mDelegateManager->register($delegateName, $this);
        }
        
        $this->_mIsLazyRegister = true;
        $this->_mLazyRegisterName = $delegateName;

        return false;
    }

    /**
     * @public
     * @brief [Overload] Connects functions to this object as callbacked functions
     * @return void
     * 
     * This method is virtual overload by sigunatures.
     * 
     * \code
     *   add(callback $callback, int priority = XCUBE_DELEGATE_PRIORITY_NORMAL);
     *   add(callback $callback, string filepath = null);
     *   add(callback $callback, int priority =... , string filepath=...);
     * \endcode
     */
    public function add($callback, $param2 = null, $param3 = null)
    {
        $priority = XCUBE_DELEGATE_PRIORITY_NORMAL;
        $filepath = null;
        
        if (!is_array($callback) && strstr($callback, '::')) {
            if (count($tmp = explode('::', $callback)) == 2) {
                $callback = $tmp;
            }
        }
        
        if ($param2 !== null) {
            if (is_int($param2)) {
                $priority = $param2;
                $filepath = ($param3 !== null && is_string($param3)) ? $param3 : null;
            } elseif (is_string($param2)) {
                $filepath = $param2;
            }
        }
        
        $this->_mCallbacks[$priority][] = array($callback, $filepath);
        ksort($this->_mCallbacks);
    }
    
    /**
     * @public
     * @brief Disconnects a function from this object.
     * @return void
     */
    public function delete($delcallback)
    {
        foreach (array_keys($this->_mCallbacks) as $priority) {
            foreach (array_keys($this->_mCallbacks[$priority]) as $idx) {
                $callback = $this->_mCallbacks[$priority][$idx][0];
                if (XCube_DelegateUtils::_compareCallback($callback, $delcallback)) {
                    unset($this->_mCallbacks[$priority][$idx]);
                }
                if (count($this->_mCallbacks[$priority])==0) {
                    unset($this->_mCallbacks[$priority]);
                }
            }
        }
    }

    /**
     * @public
     * @brief Resets all delegate functions from this object.
     * @return void
     * @attention
     *     This is the special method, so XCube doesn't recommend using this.
     */
    public function reset()
    {
        unset($this->_mCallbacks);
        $this->_mCallbacks = array();
    }

    /**
     * @public
     * @brief Calls connected functions of this object.
     */
    public function call()
    {
        $args = func_get_args();
        $num = func_num_args();
        
        if ($this->_mIsLazyRegister) {
            $this->register($this->_mLazyRegisterName);
        }
        
        if ($hasSig = $this->_mHasCheckSignatures) {
            if (count($mSigs = &$this->_mSignatures) != $num) {
                return false;
            }
        }
        
        for ($i=0 ; $i<$num ;$i++) {
            $arg = &$args[$i];
            if ($arg instanceof XCube_Ref) {
                $args[$i] =& $arg->getObject();
            }

            if ($hasSig) {
                if (!isset($mSigs[$i])) {
                    return false;
                }
                switch ($mSigs[$i]) {
                    case 'void':
                        break;
                    
                    case 'bool':
                        if (!empty($arg)) {
                            $args[$i] = $arg? true : false;
                        }
                        break;

                    case 'int':
                        if (!empty($arg)) {
                            $args[$i] = (int)$arg;
                        }
                        break;
                    
                    case 'float':
                        if (!empty($arg)) {
                            $args[$i] = (float)$arg;
                        }
                        break;

                    case 'string':
                        if (!empty($arg) && !is_string($arg)) {
                            return false;
                        }
                        break;
                    
                    default:
                        if (!is_a($arg, $mSigs[$i])) {
                            return false;
                        }
                }
            }
        }
        
        foreach ($this->_mCallbacks as $callback_arrays) {
            foreach ($callback_arrays as $callback_array) {
                list($callback, $file) = $callback_array;

                if ($file) {
                    require_once $file;
                }
                if (is_callable($callback)) {
                    if (call_user_func_array($callback, $args) === XCUBE_DELEGATE_CHAIN_BREAK) {
                        break 2;
                    }
                }
            }
        }
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this object has callback functions.
     * @return bool
     */
    public function isEmpty()
    {
        return (count($this->_mCallbacks) == 0);
    }

    /**
     * @public
     * @internal
     * @brief Gets the unique ID of this object.
     * @attention
     *     This is the special method, so XCube doesn't recommend using this.
     */
    public function getID()
    {
        return $this->_mUniqueID;
    }
}

/**
 * @public
 * @brief Manages for delegates.
 * 
 * This is the agent of un-registered delegate objects. Usually, connected
 * functions can't be added to un-registered delegates. When destination
 * delegates are un-registered yet, this manager is keeping those functions
 * and parameters until the destination delegate will be registered.
 * 
 * In other words, this class realizes lazy delegate registering.
 */
class XCube_DelegateManager
{
    /**
     * @protected
     * @brief Complex Array
     */
    public $_mCallbacks = array();

    /**
     * @protected
     * @brief Complex Array
     */
    public $_mCallbackParameters = array();
    
    /**
     * @protected
     * @brief Map Array - std::map<string, XCube_Delegate*>
     */
    public $_mDelegates = array();

    /**
     * @public
     * @brief Constructor.
     */
    // !Fix PHP7
    public function __construct()
    ///public function XCube_DelegateManager()
    {
    }
    
    /**
     * @public
     * @brief Adds $delegate as Delegate to the list of this manager.
     * @param $name string - Registration name.
     * @param $delegate XCube_Delegate - Delegate object which will be registered. 
     * @return bool
     * 
     * If some functions that want to connect to $delegate, have been entrusted yet,
     * this object calls add() of $delegate with their parameters.
     * 
     * Usually this member function isn't used as Cube's API by developers. In many
     * cases, XCube_Delegate::register() calls this.
     */
    public function register($name, &$delegate)
    {
        $mDelegate =& $this->_mDelegates[$name];
        if (isset($mDelegate[$id=$delegate->getID()])) {
            return false;
        } else {
            $mDelegate[$id] =& $delegate;
            
            $mcb = &$this->_mCallbacks[$name];
            if (isset($mcb) && count($mcb) > 0) {
                foreach ($mcb as $key=>$func) {
                    list($a, $b) = $this->_mCallbackParameters[$name][$key];
                    $delegate->add($func, $a, $b);
                }
            }
            
            return true;
        }
    }
    
    /**
     * @public
     * @brief Connects functions to the delegate that have the specified name.
     * @param $name string - Registration name.
     * @return void
     * 
     * If there aren't any delegates that have the specified name, this manager
     * entrust parameters to member properties. Then, when the delegate that
     * have the specified name will be registered, this manager will set these
     * parameters to the delegate.
     * 
     * @see XCube_Delegate::add()
     */
    public function add($name, $callback, $param3 = null, $param4 = null)
    {
        if (isset($this->_mDelegates[$name])) {
            foreach ($this->_mDelegates[$name] as $func) {
                $func->add($callback, $param3, $param4);
            }
        }
        $this->_mCallbacks[$name][] = $callback;
        $this->_mCallbackParameters[$name][] = array('0' => $param3, '1' => $param4);
    }
    
    /**
     * @public
     * @param $name string - Registration name
     * @brief Disconnects a function from the delegate that have the specified name.
     * @see XCube_Delegate::delete()
     */
    public function delete($name, $delcallback)
    {
        if (isset($this->_mDelegates[$name])) {
            foreach (array_keys($this->_mDelegates[$name]) as $key) {
                $this->_mDelegates[$name][$key]->delete($delcallback);
            }
        }
        if (isset($this->_mCallbacks[$name])) {
            foreach (array_keys($this->_mCallbacks[$name]) as $key) {
                $callback = $this->_mCallbacks[$name][$key];
                if (XCube_DelegateUtils::_compareCallback($callback, $delcallback)) {
                    unset($this->_mCallbacks[$name][$key]);
                    unset($this->_mCallbackParameters[$name][$key]);
                }
            }
        }
    }
    
    /**
     * @public
     * @brief Resets all functions off the delegate that have the specified name.
     * @param $name string - Registration name which will be resetted.
     * 
     * @see XCube_Delegate::reset()
     */
    public function reset($name)
    {
        if (isset($this->_mDelegates[$name])) {
            foreach (array_keys($this->_mDelegates[$name]) as $key) {
                $this->_mDelegates[$name][$key]->reset();
            }
        }
        if (isset($this->_mCallbacks[$name])) {
            unset($this->_mCallbacks[$name]);
            unset($this->_mCallbackParameters[$name]);
        }
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether the specified delegate has callback functions.
     * @param string $name string - Registration name.
     * @return bool
     */
    public function isEmpty($name)
    {
        if (isset($this->_mDelegates[$name])) {
            return $this->_mDelegates[$name]->isEmpty();
        }
        
        return isset($this->_mCallbacks[$name]) ? (count($this->_mCallbacks[$name]) == 0) : false;
    }
    
    
   /**
    * @public
    * @return Map Array - std::map<string, XCube_Delegate*>
    */
    public function getDelegates()
    {
        return $this->_mDelegates;
    }
}

/**
 * @public
 * @brief Utility class which collects utility functions for delegates.
 * 
 *    XCube_DelegateUtils::call("Delegate Name"[, fuction args...]); \n
 *    XCube_DelegateUtils::raiseEvent("Event Name"[, fuction params...]); \n
 *    $string = XCube_DelegateUtils::applyStringFilter("Filter Name", $string, [, option params...]); \n
 */
class XCube_DelegateUtils
{
    /**
     * @private
     * @brief Private Construct. In other words, it's possible to create an instance of this class.
     */
    // !Fix PHP7
    public function __construct()
    //public function XCube_DelegateUtils()
    {
    }

    public static function call()
    {
        $args = func_get_args();
        $num = func_num_args();
        if ($num == 1) {
            $delegateName = $args[0];
        } elseif ($num) {
            $delegateName = array_shift($args);
        } else {
            return false;
        }
        $m =& XCube_Root::getSingleton()->mDelegateManager;
        if ($m) {
            $delegates = $m->getDelegates();
            if (isset($delegates[$delegateName])) {
                $delegates = &$delegates[$delegateName];
                list($key) = array_keys($delegates);
                $delegate =& $delegates[$key];
            } else {
                $delegate = new XCube_Delegate;
                $m->register($delegateName, $delegate);
            }
        }
        return call_user_func_array(array(&$delegate, 'call'), $args);
    }

    /**
     * @deprecated Use call()
     * @public
     * @brief [Static] Utility method for calling event-delegates.
     * 
     * This method is a shortcut for calling delegates without actual delegate objects.
     * If there is not the delegate specified by the 1st parameter, the delegate will
     * be made right now. Therefore, this method is helpful for events.
     * 
     * @note
     *     \code
     *       XCube_DelegateUtils::raiseEvent("Module.A.Exception.Null");
     *     \endcode
     * 
     *     The uppering code equals the following code;
     * 
     *     \code
     *       {
     *	       $local =new XCube_Delegate();
     *	       $local->register("Module.A.Exception.Null");
     *	       $local->call();
     *	     }
     *     \endcode
     *
     * @attention
     *     Only event-owners should use this method. Outside program never calls other's
     *     events. This is a kind of XCube_Delegate rules. There is the following code;
     * 
     *     \code
     *        ClassA::check()
     *        {
     *          if ($this->mThing == null)
     *             XCube_DelegateUtils::raiseEvent("Module.A.Exception.Null");
     *	      }
     *     \endcode
     *
     *      In this case, other class never calls the event.
     *
     *      \code
     *        //
     *        // NEVER writes the following code;
     *        //
     *        $obj = new ClassA();
     *        if ($obj->mThing == null)
     *             XCube_DelegateUtils::raiseEvent("Module.A.Exception.Null");
     *     \endcode
     * 
     *     Other classes may call only ClassA::check();
     *
     * @param 1st  Delaget Name
     * @param 2nd and more : Delegate function parameters
     * @return bool
     */
    public static function raiseEvent()
    {
        if (func_num_args()) {
            $args = func_get_args();
            return call_user_func_array(array('XCube_DelegateUtils', 'call'), $args);
        }
    }

    /**
     * @public
     * @internal
     * @brief [Static] Calls a delegate string filter function. This method is multi-parameters.
     * 
     * This is a special shortcut for processing string filter.
     *
     * @param 1st string - Delaget Name
     * @param 2nd string
     * @param 3rd and more - Optional function paramaters
     * @return string
     */
    public static function applyStringFilter()
    {
        $args = func_get_args();
        $num = func_num_args();
        if ($num > 1) {
            $delegateName = $args[0];
            $string = $args[1];
            if (!empty($string) && is_string($string)) {
                return "";
            }
            $args[1] = new XCube_Ref($string);
            call_user_func_array(array('XCube_DelegateUtils', 'call'), $args);
            return $string;
        } else {
            return "";
        }
    }
    
    /**
     * @public
     * @internal
     * @brief [Static][Secret Agreement] Comparing two callback (PHP4 cannot compare Object exactly)
     * @param $callback1  : callback
     * @param $callback2  : callback
     * @return bool
     * 
     * @attention
     *     Only XCube_Delegate, XCube_DelegateManager and sub-classes of them should use this method. 
     */
    public static function _compareCallback($callback1, $callback2)
    {
        if (!is_array($callback1) && !is_array($callback2) && ($callback1 === $callback2)) {
            return true;
        } elseif (is_array($callback1) && is_array($callback2) && (gettype($callback1[0]) === gettype($callback2[0]))
                                                               && ($callback1[1] === $callback2[1])) {
            if (!is_object($callback1[0]) && ($callback1[0] === $callback2[0])) {
                return true;
            } elseif (is_object($callback1[0]) && (get_class($callback1[0]) === get_class($callback2[0]))) {
                return true;
            }
        }
        return false;
    }
}
