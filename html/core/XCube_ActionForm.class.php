<?php
/**
 * XCube_ActionForm.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      [Abstract] Fetches input values, validates fetched values and passes them to an object.
 *
 *   This class fetches the input value from the request value through the
 *   current context object and validates those values. It separates fetching
 *   and the validation layer from your main logic.
 *   Such class is important in a web program.
 *
 *   Plus, this action form features one-time token.
 *   The passed token informs the API that the bearer of the token has been authorized to access the API and perform specific actions.
 *   The token is registered in templates.
 *
 *   This is a simple suggestion of action form. We do not force a module
 *   developer to use this. You can learn more full-scale action forms from JAVA
 *   and .NET and other PHP. And, you must use any other tool auto-generating
 *   what you need to ActionForm as a sub-class of this class.
 *
 *   XCube_ActionForm contains the one-time token feature to protect against CSRF attacks
 *   But, if the current HTTP request is from the web service, the token isn't needed.
 *   Therefore, this class decides whether to use the token based on context information.
 *
 * @remarks
 *     This class is disabled for XCube_Service, because the class uses SESSION directly.
 *     XCube_ActionForm might change in the near feature.
 *     Developers should pay attention to any spec change.
 *
 * @todo The difference of array and no-array is too big.
 * @todo Form object should have getValue(), isNull(), toString().
 * @todo This form cannot be used in XCube_Service SOAP mode.
 */

if ( ! defined( 'XCUBE_CORE_PATH' ) ) {
	define( 'XCUBE_CORE_PATH', __DIR__ );
}

require_once XCUBE_CORE_PATH . '/XCube_Root.class.php';

require_once XCUBE_CORE_PATH . '/XCube_Property.class.php';
require_once XCUBE_CORE_PATH . '/XCube_Validator.class.php';
require_once XCUBE_CORE_PATH . '/XCube_FormFile.class.php';


class XCube_ActionForm {
	/**
	 * @protected
	 * @brief [READ ONLY] XCube_HttpContext
	 *
	 * The context object. Allows to access the HTTP-request information.
	 * Basically, this member property is read only. Initialized in the constructor.
	 */
	public $mContext;

	/**
	 * @protected
	 * @brief [READ ONLY] XCube_Principal
	 *
	 * The object implementing the interface of XCube_Principal.
	 * Allows you to check the permissions of the current HTTP-request through principal object.
	 * Basically, this member property is read only. Initialized in constructor.
	 */
	public $mUser;

	/**
	 * @protected
	 * @brief XCube_FormProperty[]
	 */
	public $mFormProperties = [];

	/**
	 * @protected
	 * @brief XCube_FieldProperty[]
	 */
	public $mFieldProperties = [];

	/**
	 * @protected
	 * @brief bool
	 * @attention
	 *     This is temporary until we decide on the method of error handling.
	 */
	public $mErrorFlag = false;

	/**
	 * @private
	 * @brief string[]
	 */
	public $mErrorMessages = [];

	/**
	 * @protected
	 * @brief string
	 *
	 * Token string as one-time token.
	 */
	public $_mToken;

	/**
	 * @public
	 * @brief Constructor.
	 */
	public function __construct() {
		$root           =& XCube_Root::getSingleton();
		$this->mContext =& $root->getContext();
		$this->mUser    =& $this->mContext->getUser();
	}

	/**
	 * @public
	 * @brief [Abstract] Set up form properties and field properties.
	 */
	public function prepare() {
	}

	/**
	 * @public
	 * @brief Gets the token name of this actionform's token.
	 * @return string
	 *
	 * Return token name. If the sub-class doesn't override this member
	 * function, features about one-time tokens aren't used.
	 */
	public function getTokenName() {
		return null;
	}

	/**
	 * @public
	 * @brief Gets the token value of this actionform's token.
	 * @return string
	 *
	 * Generates a token value, stores it in sessions, returns it.
	 * This member function should be called in templates.
	 * A subclass can override it to modify the logic generating the token value.
	 */
	public function getToken() {
		if ( null === $this->_mToken ) {
			mt_srand( microtime( true ) * 100000 );
			$root          =& XCube_Root::getSingleton();
			$salt          = $root->getSiteConfig( 'Cube', 'Salt' );
			$this->_mToken = md5( $salt . uniqid( mt_rand(), true ) );

			$_SESSION['XCUBE_TOKEN'][ $this->getTokenName() ] = $this->_mToken;
		}

		return $this->_mToken;
	}

	/**
	 * @public
	 * @brief Gets a message about the failure of the token validation.
	 * @return string
	 */
	public function getTokenErrorMessage() {
		return _TOKEN_ERROR;    //< FIXME
	}

	/**
	 * @public
	 * @brief Sets the raw value as the value of the form property.
	 *
	 * This method is an overloaded function.
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
	public function set() {
		if ( isset( $this->mFormProperties[ func_get_arg( 0 ) ] ) ) {
			if ( func_num_args() === 2 ) {
				$value = func_get_arg( 1 );
				$this->mFormProperties[ func_get_arg( 0 ) ]->setValue( $value );
			} elseif ( func_num_args() === 3 ) {
				$index = func_get_arg( 1 );
				$value = func_get_arg( 2 );
				$this->mFormProperties[ func_get_arg( 0 ) ]->setValue( $index, $value );
			}
		}
	}

	/**
	 * @deprecated
	 */
	public function setVar() {
		if ( isset( $this->mFormProperties[ func_get_arg( 0 ) ] ) ) {
			if ( func_num_args() === 2 ) {
				$this->mFormProperties[ func_get_arg( 0 ) ]->setValue( func_get_arg( 1 ) );
			} elseif ( func_num_args() === 3 ) {
				$this->mFormProperties[ func_get_arg( 0 ) ]->setValue( func_get_arg( 1 ), func_get_arg( 2 ) );
			}
		}
	}

	/**
	 * @public
	 * @brief Gets raw value.
	 *
	 * @param string $key Name of form property.
	 * @param string $index Hint for array.
	 *
	 * @return mixed
	 *
	 * @attention
	 *     This method returns raw values.
	 *     Therefore, if the value is used in templates, it should require an escape.
	 */
	public function get( $key, $index = null ) {
		return isset( $this->mFormProperties[ $key ] ) ? $this->mFormProperties[ $key ]->getValue( $index ) : null;
	}

	/**
	 * @param      $key
	 * @param null $index
	 *
	 * @return mixed
	 * @deprecated
	 */
	public function getVar( $key, $index = null ) {
		return $this->get( $key, $index );
	}

	/**
	 * @public
	 * @brief Gets form properties of this member property.
	 * @return XCube_AbstractProperty[]
	 * @attention
	 *     This method is Not necessarily essential. It could be renamed in the near future.
	 * @todo Check whether this method is needed.
	 */
	public function &getFormProperties() {
		return $this->mFormProperties;
	}

	/**
	 * @public
	 * @brief Fetches values through the request object.
	 * @return void
	 * @see getFromRequest
	 *
	 *   Fetch the input value, set it and form properties.
	 *   These values can be retrieved by the get() method. The subclass can define its own member function to fetch the values.
	 *   Define member functions with name "fetch" + "form name".
	 *   For example, to fetch the "message", define the function "fetchMessage()".
	 *   The subclass function assigns a value to this action form.
	 * \code
	 *  function fetchModifytime()
	 *  {
	 *    $this->set('modifytime', time());
	 *  }
	 * \endcode
	 */
	public function fetch() {
		foreach ( array_keys( $this->mFormProperties ) as $name ) {
			if ( $this->mFormProperties[ $name ]->hasFetchControl() ) {
				$this->mFormProperties[ $name ]->fetch( $this );
			} else {
				$value = $this->mContext->mRequest->getRequest( $name );
				$this->mFormProperties[ $name ]->set( $value );
			}
			$methodName = 'fetch' . ucfirst( $name );
			if ( method_exists( $this, $methodName ) ) {
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
	 *   be changed for multi-layer. So this method is called only by this class.
	 *
	 * @todo This method has to be remove, because it is using session directly.
	 */
	public function _validateToken() {
		//
		// check one-time & transaction token
		//
		if ( null !== $this->getTokenName() ) {
			$key   = str_replace( '.', '_', $this->getTokenName() );
			$token = isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : null;

			$flag = true;

			if ( ! isset( $_SESSION['XCUBE_TOKEN'][ $this->getTokenName() ] ) ) {
				$flag = false;
			} elseif ( $_SESSION['XCUBE_TOKEN'][ $this->getTokenName() ] != $token ) {
				unset( $_SESSION['XCUBE_TOKEN'][ $this->getTokenName() ] );
				$flag = false;
			}

			if ( ! $flag ) {
				$message = $this->getTokenErrorMessage();
				if ( null === $message ) {
					$this->mErrorFlag = true;
				} else {
					$this->addErrorMessage( $message );
				}
			}

			//
			// clear token
			//
			unset( $_SESSION['XCUBE_TOKEN'][ $this->getTokenName() ] );
		}
	}


	/**
	 * @public
	 * @brief Validates fetched values.
	 * @return void
	 *
	 *   Performs validation, so that if an input value is wrong, error messages are added to the error message buffer.
	 *   The validation procedure is as follows:
	 *
	 *   \li 1. If this object has a token name, validate the unique one-time token.
	 *   \li 2. Call the member function to validate all the properties of the field.
	 *   \li 3. Call the member function that is defined in the subclass.
	 *
	 *   For a database, validations are performed by functions of the properties of each field.
	 *   But, the subclass can define its own validation logic.
	 *   Define member function with name "validate" + "form name".
	 *   For example, to validate "message", define the function "validateMessage()".
	 */
	public function validate() {
		$this->_validateToken();

		foreach ( array_keys( $this->mFormProperties ) as $name ) {
			if ( isset( $this->mFieldProperties[ $name ] ) ) {
				if ( $this->mFormProperties[ $name ]->isArray() ) {
					foreach ( array_keys( $this->mFormProperties[ $name ]->mProperties ) as $_name ) {
						$this->mFieldProperties[ $name ]->validate( $this->mFormProperties[ $name ]->mProperties[ $_name ] );
					}
				} else {
					$this->mFieldProperties[ $name ]->validate( $this->mFormProperties[ $name ] );
				}
			}
		}

		//
		// If this class has original validation methods, call them.
		//
		foreach ( array_keys( $this->mFormProperties ) as $name ) {
			$methodName = 'validate' . ucfirst( $name );
			if ( method_exists( $this, $methodName ) ) {
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
	public function hasError() {
		return ( count( $this->mErrorMessages ) > 0 || $this->mErrorFlag );
	}

	/**
	 * @protected
	 * @brief Adds a message to the form's error message buffer.
	 *
	 * @param string $message
	 */
	public function addErrorMessage( $message ) {
		$this->mErrorMessages[] = $message;
	}

	/**
	 * @public
	 * @brief Gets error messages.
	 * @return string[]
	 */
	public function getErrorMessages() {
		return $this->mErrorMessages;
	}

	/**
	 * @public
	 * @brief [Abstract] Initializes properties' values from an object.
	 *
	 * @param mixed $obj
	 *
	 * @return void
	 *
	 *   Set the initial values of this Action Form from an object.
	 *   This member function mediates between logic and validation.
	 *   For example, developers can use this method to load values from XoopsSimpleObject.
	 *
	 *   This member function is abstract. But, the subclass of this class does not have to implement it.
	 */
	public function load( &$obj ) {
	}

	/**
	 * @public
	 * @brief [Abstract] Updates an object with properties values.
	 *
	 * @param mixed $obj
	 *
	 * @return void
	 *
	 *   Set input values to a object from this action form.
	 *   This member function mediates between logic and the result of validations.
	 *   For example, developers can use this method to set values to XoopsSimpleObject.
	 *
	 *   This member function is abstract. But, the subclass of this class does not have to implement it.
	 */
	public function update( &$obj ) {
	}
}

/**
 * @public
 * @brief [Abstract] Used for validating member property values of XCube_ActionForm.
 */
class XCube_FieldProperty {
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
	 *   struct MessageStorage
	 *   {
	 *     string Message;
	 *     ArgumentMap args;
	 *     };
	 *
	 *   typedef std::map<string, MessageStorage> MessageList;
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
	 *
	 * @param XCube_ActionForm $form - Parent form.
	 *
	 * @remarks
	 *     Only sub-classes of XCube_ActionForm calles this constructor.
	 */
	public function __construct( &$form ) {
		$this->mForm =& $form;
	}

	/**
	 * @public
	 * @brief Initializes the list of validators for this field property with the list of dependency rule names.
	 *
	 * @param string[] $dependsArr
	 *
	 * @return void
	 */
	public function setDependsByArray( $dependsArr ) {
		foreach ( $dependsArr as $dependName ) {
			$instance =& XCube_DependClassFactory::factoryClass( $dependName );
			if ( null !== $instance ) {
				$this->mDepends[ $dependName ] =& $instance;
			}

			unset( $instance );
		}
	}

	/**
	 * @public
	 * @brief Adds an error message that will be used if the validation of the "$name" rule fails.
	 *
	 * @param string $name - Dependent rule name.
	 * @param string $message - Error message.
	 *
	 * @return void
	 *
	 *   It's possible to add 3 or more parameters.
	 *   These additional parameters are used by XCube_Utils::formatString().
	 * \code
	 *   $field->addMessage('required', "{0:ucFirst} is required.", "name");
	 * \endcode
	 *   This feature is helpful for automatic ActionForm generators.
	 */
	public function addMessage( $name, $message ) {
		if ( func_num_args() >= 2 ) {
			$args                                   = func_get_args();
			$this->mMessages[ $args[0] ]['message'] = $args[1];
			for ( $i = 0; isset( $args[ $i + 2 ] ); $i ++ ) {
				$this->mMessages[ $args[0] ]['args'][ $i ] = $args[ $i + 2 ];
			}
		}
	}

	/**
	 * @public
	 * @brief Gets the error message rendered by XCube_Utils::formaString().
	 *
	 * @param string $name - Dependent rule name.
	 *
	 * @return string
	 *
	 *   Gets the error message registered at addMessage().
	 *   If the message parameter has arguments, these are rendered by XCube_Utils::formatString().
	 * \code
	 *   $field->addMessage('required', "{0:ucFirst} is required.", "name");
	 *
	 *   // Gets "Name is required."
	 *   $field->renderMessage('required');
	 * \endcode
	 *   This feature is helpful for automatic ActionForm generators.
	 */
	public function renderMessage(string $name ) {
		if ( ! isset( $this->mMessages[ $name ] ) ) {
			return null;
		}

		$message = $this->mMessages[ $name ]['message'];

		if ( isset( $this->mMessages[ $name ]['args'] ) ) {
			// Use a unity method.
			$message = XCube_Utils::formatString( $message, $this->mMessages[ $name ]['args'] );
		}

		return $message;
	}

	/**
	 * @public
	 * @brief Adds a virtual variable used by validators.
	 *
	 * @param string $name - A name of the variable.
	 * @param mixed $value - A value of the variable.
	 *
	 *   Virtual variables are used to be validated by validators.
	 *   For example, XCube_MinlengthValidator needs a value indicating a minimum length.
	 * \code
	 *   $field->addVar('minlength', 2);
	 * \endcode
	 */
	public function addVar( $name, $value ) {
		$this->mVariables[ $name ] = $value;
	}

	/**
	 * @public
	 * @brief Validates the form property with the validators that the field's property contains.
	 * @attention
	 *      Only XCube_ActionForm and its sub-classes should call this method.
	 *
	 * @param $form
	 *
	 * @return null
	 * @todo  This class already has an instance of a form property.
	 */
	public function validate( &$form ) {
		if ( is_array( $this->mDepends ) && count( $this->mDepends ) > 0 ) {
			foreach ( $this->mDepends as $name => $depend ) {
				if ( ! $depend->isValid( $form, $this->mVariables ) ) {
					// Error
					// NOTICE: This is temporary until we decide how to handle the errors.
					$this->mForm->mErrorFlag = true;

					// TEST!!
					$this->mForm->addErrorMessage( $this->renderMessage( $name ) );
				} else {
					// OK check
					//!check empty block else
					return null;
				}
			}
		}
	}
}

/**
 * @internal
 * @public
 * @brief Factory to generate validator objects.
 * @attention
 *     Only 'XCube_ActionForm' class should use this class.
 */
class XCube_DependClassFactory {
	/**
	 * @public
	 *
	 * @param string $dependName
	 *
	 * @return XCube_Validator
	 * @attention
	 *     Only 'XCube_ActionForm' class should use this class.
	 * @internal
	 * @brief [static] Gets a XCube_Validator object by the rule name (dependent name).
	 */
	public static function &factoryClass(string $dependName ) {
		static $_cache;

		if ( ! is_array( $_cache ) ) {
			$_cache = [];
		}

		if ( ! isset( $_cache[ $dependName ] ) ) {
			// or switch?
			$class_name = 'XCube_' . ucfirst( $dependName ) . 'Validator';
			if ( XC_CLASS_EXISTS( $class_name ) ) {
				$_cache[ $dependName ] = new $class_name();
			} else {
				// FIXME:: use delegate?
				die( "This is an error message of Alpha or Beta series. ${dependName} Validator is not found." );
			}
		}

		return $_cache[ $dependName ];
	}
}
