<?php
/**
 * XCube_HttpContext.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      Encapsulates major HTTP specific information about an HTTP request
 */

define( 'XCUBE_CONTEXT_TYPE_DEFAULT', 'web_browser' );
define( 'XCUBE_CONTEXT_TYPE_WEB_SERVICE', 'web_service' );


class XCube_HttpContext {
	/**
	 * Hashmap that can be used to organize and share data. Use setAttribute()
	 * and get Attribute() to access this member property. But, direct access
	 * is allowed, because it is impossible to handle reference well on older PHP versions.
	 * Array
	 * @var
	 * @access protected
	 */
	public $mAttributes = [];

	/**
	 * The object which enables to read the request values.
	 *
	 * @access XCube_AbstractRequest
	 */
	public $mRequest;

	/**
	 * @var XCube_Principal
	 */
	public $mUser;

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
	public $mThemeName;

	public function __construct() {
	}

	/**
	 * Sets $value with $key to attributes. Use direct access to $mAttributes
	 * if references are must, because PHP4 can't handle reference in the
	 * signature of this member function.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function setAttribute( $key, $value ) {
		$this->mAttributes[ $key ] = $value;
	}

	/**
	 * Gets a value indicating whether the value specified by $key exists.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function hasAttribute( $key ) {
		return isset( $this->mAttributes[ $key ] );
	}

	/**
	 * Gets a value of attributes with $key. If the value specified by $key
	 * doesn't exist in attributes, gets null.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getAttribute( $key ) {
        //return isset( $this->mAttributes[ $key ] ) ? $this->mAttributes[ $key ] : null; gigamaster
		return $this->mAttributes[$key] ?? null;
	}

	/**
	 * Sets the object which has a interface of XCube_AbstractRequest.
	 *
	 * @param XCube_AbstractRequest $request
	 */
	public function setRequest( &$request ) {
		$this->mRequest =& $request;
	}

	/**
	 * Gets the object which has a interface of XCube_AbstractRequest.
	 *
	 * @return XCube_AbstractRequest
	 */
	public function &getRequest() {
		return $this->mRequest;
	}

	/**
	 * Sets the object which has a interface of XCube_Principal.
	 * XCube_AbstractPrincipal
	 *
	 * @param  $principal
	 */
	public function setUser( &$principal ) {
		$this->mUser =& $principal;
	}

	/**
	 * Gets the object which has a interface of XCube_Principal.
	 *
	 * @return \XCube_Principal
	 */
	public function &getUser() {
		return $this->mUser;
	}

	/**
	 * Set the theme name.
	 *
	 * @param string $theme
	 *
	 * @deprecated
	 */
	public function setThemeName(string $theme ) {
		$this->mThemeName = $theme;
	}

	/**
	 * Return the theme name.
	 *
	 * @return string
	 * @deprecated
	 */
	public function getThemeName() {
		return $this->mThemeName;
	}
}

/**
 * This is an interface for request classes.
 */
class XCube_AbstractRequest {
	/**
	 * Gets a value of the current request.
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function getRequest( $key ) {
		return null;
	}
}

/**
 * Enables a program to read the HTTP values through XCubeAbstractRequest
 * interface.
 */
class XCube_HttpRequest extends XCube_AbstractRequest {
	/**
	 * Gets a value of the current HTTP request.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getRequest( $key ) {
		if ( ! isset( $_GET[ $key ] ) && ! isset( $_POST[ $key ] ) ) {
			return null;
		}

		return isset( $_GET[ $key ] ) ? $_GET[ $key ] : $_POST[ $key ];
	}

	/**
	 * Supports getRequest().
	 * Array
	 * @private
	 *
	 * @param  $arr
	 *
	 * @return array
	 */
	public function _getArrayRequest( $arr ) {
		//trigger_error("assume magic_quotes_gpc is off", E_USER_NOTICE);
		return $arr;
	}
}

/**
 * A kind of request objects. This class is free to register values.
 */
class XCube_GenericRequest extends XCube_AbstractRequest {
	/**
	 * Hash map which stores registered values.
	 * Array
	 * @var
	 */
	public $mAttributes = [];

	public function __construct( $arr = null ) {
		if ( is_array( $arr ) ) {
			$this->mAttributes = $arr;
		}
	}

	public function getRequest( $key ) {
		if ( ! isset( $this->mAttributes[ $key ] ) ) {
			return null;
		}

		return $this->mAttributes[ $key ];
	}
}
