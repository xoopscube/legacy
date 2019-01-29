<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_ActionForm.class.php,v 1.4 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

if (!defined('XCUBE_CORE_PATH')) {
    define('XCUBE_CORE_PATH', dirname(__FILE__));
}

require_once XCUBE_CORE_PATH . '/XCube_Root.class.php';

require_once XCUBE_CORE_PATH . '/XCube_Property.class.php';
require_once XCUBE_CORE_PATH . '/XCube_Validator.class.php';
require_once XCUBE_CORE_PATH . '/XCube_FormFile.class.php';

/**
 * @public
 * @brief [Abstract] Fetches input values, valudates fetched values and passes them to some object.
 * 
 *   This class fetches the input value from the request value through the
 *   current context object and validate those values. It separates fetching & 
 *   validating from your main logic. Such classes is important in web
 *   program.
 * 
 *   Plus, this action form has features of one time token. It seems one kinds of
 *   validations. The token is registered in templates.
 * 
 *   This is suggestion of a simple action form. We do not force a module
 *   developer to use this. You can learn more full-scale action forms from JAVA
 *   and .NET and other PHP. And, you must use auto-generating tool when you need
 *   to ActionForm that is sub-class of this class.
 * 
 *   XCube_ActionForm contains the one-time token feature for CSRF. But, if the
 *   current HTTP request is from the web service, the token isn't needed.
 *   Therefore, this class decides whether to use the token with the information
 *   of the context.
 * 
 * @remarks
 *     This class is diable for XCube_Service, because the class uses SESSION
 *     directly. XCube_ActionForm will be changed in the near feature. Developers
 *     need to pay attention to spec change.
 * 
 * @todo The difference of array and no-array is too big.
 * @todo Form object should have getValue(), isNull(), toString().
 * @todo This form is impossible to be used in XCube_Service SOAP mode.
 */
class XCube_ActionForm
{
    /**
     * @protected
     * @brief [READ ONLY] XCube_HttpContext
     * 
     * The context object. Enables to access the HTTP-request information.
     * Basically, this member property is read only. Initialized in the constructor.
     */
    public $mContext = null;
    
    /**
     * @protected
     * @brief [READ ONLY] XCube_Principal
     * 
     * The object which has a interface of XCube_Principal. Enables to check
     * permissions of the current HTTP-request through principal object.
     * Basically, this member property is read only. Initialized in constructor.
     */
    public $mUser = null;
    
    /**
     * @protected
     * @brief XCube_FormProperty[]
     */
    public $mFormProperties = array();
    
    /**
     * @protected
     * @brief XCube_FieldProperty[]
     */
    public $mFieldProperties = array();
    
    /**
     * @protected
     * @brief bool
     * @attention
     *     This is temporary until we will decide the method of managing error.
     */
    public $mErrorFlag = false;
    
    /**
     * @private
     * @brief string[]
     */
    public $mErrorMessages = array();
    
    /**
     * @protected
     * @brief string
     * 
     * Token string as one time token.
     */
    public $_mToken = null;
    
    /**
     * @public
     * @brief Constructor.
     */
    // !Fix PHP7
    public function __construct()
    //public function XCube_ActionForm()
    {
        $root =& XCube_Root::getSingleton();
        $this->mContext =& $root->getContext();
        $this->mUser =& $this->mContext->getUser();
    }
    
    /**
     * @public
     * @brief [Abstract] Set up form properties and field properties.
     */
    public function prepare()
    {
    }
    
    /**
     * @public
     * @brief Gets the token name of this actionform's token.
     * @return string
     * 
     * Return token name. If the sub-class doesn't override this member
     * function, features about one time tokens aren't used.
     */
    public function getTokenName()
    {
        return null;
    }
    
    /**
     * @public
     * @brief Gets the token value of this actionform's token.
     * @return string
     * 
     * Generate token value, register it to sessions, return it. This member
     * function should be called in templates. The subclass can override this
     * to change the logic for generating token value.
     */
    public function getToken()
    {
        if ($this->_mToken == null) {
            srand(microtime() * 100000);
            $root=&XCube_Root::getSingleton();
            $salt = $root->getSiteConfig('Cube', 'Salt');
            $this->_mToken = md5($salt . uniqid(rand(), true));
            
            $_SESSION['XCUBE_TOKEN'][$this->getTokenName()] = $this->_mToken;
        }
        
        return $this->_mToken;
    }
    
    /**
     * @public
     * @brief Gets message about the failed validation of token.
     * @return string
     */
    public function getTokenErrorMessage()
    {
        return _TOKEN_ERROR;    //< FIXME
    }
    
    /**
     * @public
     * @brief Set raw value as the value of the form property.
     * 
     * This method is overloaded function.
     * 
     * \par XCube_ActionForm::set($name, $value)
     *   Set $value to $name property.
     *   \code
     *     $this->set('name', 'Bob');  // Set 'Bob' to 'name'.
     *   \endcode
     * 
     * \par XCube_ActionForm::set($name, $index, $value)
     *   Set $value to $name array property[$index].
     *   \code
     *     $this->set('names', 0, 'Bob');  // Set 'Bob' to 'names[0]'.
     *   \endcode
     */
    public function set()
    {
        if (isset($this->mFormProperties[func_get_arg(0)])) {
            if (func_num_args() == 2) {
                $value = func_get_arg(1);
                $this->mFormProperties[func_get_arg(0)]->setValue($value);
            } elseif (func_num_args() == 3) {
                $index = func_get_arg(1);
                $value = func_get_arg(2);
                $this->mFormProperties[func_get_arg(0)]->setValue($index, $value);
            }
        }
    }
    
    /**
     * @deprecated
     */
    public function setVar()
    {
        if (isset($this->mFormProperties[func_get_arg(0)])) {
            if (func_num_args() == 2) {
                $this->mFormProperties[func_get_arg(0)]->setValue(func_get_arg(1));
            } elseif (func_num_args() == 3) {
                $this->mFormProperties[func_get_arg(0)]->setValue(func_get_arg(1), func_get_arg(2));
            }
        }
    }
    
    /**
     * @public
     * @brief Gets raw value.
     * @param $key   string Name of form property.
     * @param $index string Subscript for array.
     * @return mixed
     * 
     * @attention
     *     This method returns raw values. Therefore if the value is used in templates,
     *     it must needs escaping.
     */
    public function get($key, $index=null)
    {
        return isset($this->mFormProperties[$key]) ? $this->mFormProperties[$key]->getValue($index) : null;
    }
    
    /**
     * @deprecated
     */
    public function getVar($key, $index=null)
    {
        return $this->get($key, $index);
    }
    
    /**
     * @public
     * @brief Gets form properties of this member property.
     * @return XCube_AbstractProperty[]
     * @attention
     *     This method may not be must. So it will be renamed in the near future.
     * @todo Check whether this method is must.
     */
    public function &getFormProperties()
    {
        return $this->mFormProperties;
    }
    
    /**
     * @public
     * @brief Fetches values through the request object.
     * @return void
     * @see getFromRequest
     * 
     *   Fetch the input value, set it and form properties. Those values can be
     *   got, through get() method. the sub-class can define own member function
     *   to fetch. Define member functions whose name is "fetch" + "form name".
     *   For example, to fetch "message" define "fetchMessage()" function. Those
     *   function of the sub-class set value to this action form.
     * \code
     *  function fetchModifytime()
     *  {
     *    $this->set('modifytime', time());
     *  }
     * \endcode
     */
    public function fetch()
    {
        foreach (array_keys($this->mFormProperties) as $name) {
            if ($this->mFormProperties[$name]->hasFetchControl()) {
                $this->mFormProperties[$name]->fetch($this);
            } else {
                $value = $this->mContext->mRequest->getRequest($name);
                $this->mFormProperties[$name]->set($value);
            }
            $methodName = "fetch" . ucfirst($name);
            if (method_exists($this, $methodName)) {
                // call_user_func(array($this,$methodName));
                $this->$methodName();
            }
        }
    }
    
    /**
     * @protected
     * @brief Validates the token.
     * @return void
     * 
     *   Validates the token. This method is deprecated, because XCube_Action will
     *   be changed for multi-layer. So this method is called by only this class.
     * 
     * @todo This method has to be remove, because it is using session directly.
     */
    public function _validateToken()
    {
        //
        // check onetime & transaction token
        //
        if ($this->getTokenName() != null) {
            $key = strtr($this->getTokenName(), '.', '_');
            $token = isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
            
            if (get_magic_quotes_gpc()) {
                $token = stripslashes($token);
            }
            
            $flag = true;
            
            if (!isset($_SESSION['XCUBE_TOKEN'][$this->getTokenName()])) {
                $flag = false;
            } elseif ($_SESSION['XCUBE_TOKEN'][$this->getTokenName()] != $token) {
                unset($_SESSION['XCUBE_TOKEN'][$this->getTokenName()]);
                $flag = false;
            }
            
            if (!$flag) {
                $message = $this->getTokenErrorMessage();
                if ($message == null) {
                    $this->mErrorFlag = true;
                } else {
                    $this->addErrorMessage($message);
                }
            }
            
            //
            // clear token
            //
            unset($_SESSION['XCUBE_TOKEN'][$this->getTokenName()]);
        }
    }
    
    
    /**
     * @public
     * @brief Validates fetched values.
     * @return void
     * 
     *   Execute validation, so if a input value is wrong, error messages are
     *   added to error message buffer. The procedure of validation is the
     *   following:
     * 
     *   \li 1. If this object have token name, validate one time tokens.
     *   \li 2. Call the validation member function of all field properties.
     *   \li 3. Call the member function that is defined in the sub-class.
     * 
     *   For a basis, validations are done by functions of each field properties.
     *   But, the sub-class can define own validation logic. Define member
     *   functions whose name is "validate" + "form name". For example, to
     *   validate "message" define "validateMessage()" function.
     */
    public function validate()
    {
        $this->_validateToken();
        
        foreach (array_keys($this->mFormProperties) as $name) {
            if (isset($this->mFieldProperties[$name])) {
                if ($this->mFormProperties[$name]->isArray()) {
                    foreach (array_keys($this->mFormProperties[$name]->mProperties) as $_name) {
                        $this->mFieldProperties[$name]->validate($this->mFormProperties[$name]->mProperties[$_name]);
                    }
                } else {
                    $this->mFieldProperties[$name]->validate($this->mFormProperties[$name]);
                }
            }
        }
        
        //
        // If this class has original validation methods, call it.
        //
        foreach (array_keys($this->mFormProperties) as $name) {
            $methodName = "validate" . ucfirst($name);
            if (method_exists($this, $methodName)) {
                // call_user_func(array($this,$methodName));
                $this->$methodName();
            }
        }
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this action form keeps error messages or error flag.
     * @return bool - If the action form is error status, returns true.
     */
    public function hasError()
    {
        return (count($this->mErrorMessages) > 0 || $this->mErrorFlag);
    }
    
    /**
     * @protected
     * @brief Adds an message to error message buffer of the form.
     * @param $message string
     */
    public function addErrorMessage($message)
    {
        $this->mErrorMessages[] = $message;
    }
    
    /**
     * @public
     * @brief Gets error messages.
     * @return string[]
     */
    public function getErrorMessages()
    {
        return $this->mErrorMessages;
    }
    
    /**
     * @public
     * @brief [Abstract] Initializes properties' values from an object.
     * @param $obj mixed
     * @return void
     * 
     *   Set initial values to this action form from a object. This member
     *   function mediates between the logic and the validation. For example,
     *   developers can use this method to load values from XoopsSimpleObject.
     * 
     *   This member function is abstract. But, the sub-class of this class
     *   doesn't have to implement this.
     */
    public function load(&$obj)
    {
    }
    
    /**
     * @public
     * @brief [Abstract] Updates an object with properties's values.
     * @param $obj mixed
     * @return void
     * 
     *   Set input values to a object from this action form. This member function
     *   mediates between the logic and the result of validations. For example,
     *   developers can use this method to set values to XoopsSimpleObject.
     * 
     *   This member function is abstract. But, the sub-class of this class
     *   doesn't have to implement this.
     */
    public function update(&$obj)
    {
    }
}

/**
 * @public
 * @brief [Abstract] Used for validating member property values of XCube_ActionForm.
 */
class XCube_FieldProperty
{
    /**
     * @protected
     * @brief XCube_ActionForm - Parent form contains this field property.
     */
    public $mForm;
    
    /**
     * @protected
     * @brief XCube_Validator[] - std::map<string, XCube_Validator*>
     */
    public $mDepends;
    
    /**
     * @protected
     * @brief Complex Array
     * @section section1 Complex Array
     *   $mMessages[$name]['message'] - string \n
     *   $mMessages[$name]['args'][]  - string
     * 
     * \code
     *   // Reference Define
     *   typedef std::map<int, string> ArgumentMap;
     *   struct MessageStrage
     *   {
     *     string Message;
     *     ArgumentMap args;
     *	 };
     * 
     *   typedef std::map<string, MessageStrage> MessageList;
     *   MessageList mMessages;
     * \endcode
     */
    public $mMessages;
    
    /**
     * @protected
     * @brief Hash-Map Array - std::map<string, mixed>
     */
    public $mVariables;
    
    /**
     * @public
     * @brief Constructor.
     * @param $form XCube_ActionForm - Parent form.
     * @remarks
     *     Only sub-classes of XCube_ActionForm calles this constructor. 
     */
    // !Fix PHP7
    public function __construct(&$form)
    //public function XCube_FieldProperty(&$form)
    {
        $this->mForm =& $form;
    }
    
    /**
     * @public
     * @brief Initializes the validator list of this field property with the depend rule name list.
     * @param $dependsArr string[]
     * @return void
     */
    public function setDependsByArray($dependsArr)
    {
        foreach ($dependsArr as $dependName) {
            $instance =& XCube_DependClassFactory::factoryClass($dependName);
            if ($instance !== null) {
                $this->mDepends[$dependName] =& $instance;
            }
            
            unset($instance);
        }
    }
    
    /**
     * @public
     * @brief Adds an error message which will be used in the case which '$name rule' validation is failed.
     * @param $name string - Depend rule name.
     * @param $message string - Error message.
     * @return void
     * 
     *   It's possible to add 3 or greater parameters.
     *   These additional parameters are used by XCube_Utils::formatString().
     * \code
     *   $field->addMessage('required', "{0:ucFirst} is requred.", "name");
     * \endcode
     *   This feature is helpful for automatic ActionForm generators.
     */
    public function addMessage($name, $message)
    {
        if (func_num_args() >= 2) {
            $args = func_get_args();
            $this->mMessages[$args[0]]['message'] = $args[1];
            for ($i = 0; isset($args[$i + 2]); $i++) {
                $this->mMessages[$args[0]]['args'][$i] = $args[$i + 2];
            }
        }
    }
    
    /**
     * @public
     * @brief Gets the error message rendered by XCube_Utils::formaString().
     * @param $name string - Depend rule name
     * @return string
     * 
     *   Gets the error message registered at addMessage(). If the message setting has some
     *   arguments, messages are rendered by XCube_Utils::formatString().
     * \code
     *   $field->addMessage('required', "{0:ucFirst} is requred.", "name");
     * 
     *   // Gets "Name is required."
     *   $field->renderMessage('required');
     * \endcode
     *   This feature is helpful for automatic ActionForm generators.
     */
    public function renderMessage($name)
    {
        if (!isset($this->mMessages[$name])) {
            return null;
        }
        
        $message = $this->mMessages[$name]['message'];
        
        if (isset($this->mMessages[$name]['args'])) {
            // Use an unity method.
            $message = XCube_Utils::formatString($message, $this->mMessages[$name]['args']);
        }
        
        return $message;
    }
    
    /**
     * @public
     * @brief Adds a virtual variable used by validators.
     * @param $name string - A name of the variable.
     * @param $value mixed - A value of the variable.
     * 
     *   Virtual varialbes are used for validating by validators. For example,
     *   XCube_MinlengthValidator needs a value indicationg a minimum length.
     * \code
     *   $field->addVar('minlength', 2);
     * \endcode
     */
    public function addVar($name, $value)
    {
        $this->mVariables[$name] = $value;
    }
    
    /**
     * @public
     * @brief Validates form-property with validators which this field property holds.
     * @attention
     *      Only XCube_ActionForm and its sub-classes should call this method.
     * @todo This class already has form property instance.
     */
    public function validate(&$form)
    {
        if (is_array($this->mDepends) && count($this->mDepends) > 0) {
            foreach ($this->mDepends as $name => $depend) {
                if (!$depend->isValid($form, $this->mVariables)) {
                    // Error
                    // NOTICE: This is temporary until we will decide the method of managing error.
                    $this->mForm->mErrorFlag = true;
                    
                    // TEST!!
                    $this->mForm->addErrorMessage($this->renderMessage($name));
                } else {
                    // OK
                }
            }
        }
    }
}

/**
 * @internal
 * @public
 * @brief Factory for generating validator objects.
 * @attention
 *     Only 'XCube_ActionForm' class should use this class.
 */
class XCube_DependClassFactory
{
    /**
     * @public
     * @internal
     * @brief [static] Gets a XCube_Validator object by the rule name (depend name).
     * @param $dependName string
     * @return XCube_Validator
     * @attention
     *     Only 'XCube_ActionForm' class should use this class.
     */
    public static function &factoryClass($dependName)
    {
        static $_cache;
        
        if (!is_array($_cache)) {
            $_cache = array();
        }
        
        if (!isset($_cache[$dependName])) {
            // or switch?
            $class_name = "XCube_" . ucfirst($dependName) . "Validator";
            if (XC_CLASS_EXISTS($class_name)) {
                $_cache[$dependName] = new $class_name();
            } else {
                // FIXME:: use delegate?
                die("This is an error message of Alpha or Beta series. ${dependName} Validator is not found.");
            }
        }

        return $_cache[$dependName];
    }
}
