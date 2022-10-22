<?php
/**
 * XCube_Identity.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      Defines the basic functionality of an identity object.
 */

class XCube_Identity {
	/**
	 * A name of the identity.
	 * @var string
	 */
	public $mName = '';

	/**
	 * The authentication type
	 * @var string
	 */
	public $_mAuthenticationType = '';

	public function __construct() {
	}

	/**
	 * Sets the authentication type.
	 *
	 * @param string $type
	 */
	public function setAuthenticationType( $type ) {
		$this->_mAuthenticationType = $type;
	}

	/**
	 * Gets the authentication type.
	 * @return string
	 */
	public function getAuthenticationType() {
		return $this->_mAuthenticationType;
	}

	/**
	 * Sets a name of this object.
	 *
	 * @param $name
	 */
	public function setName( $name ) {
		$this->mName = $name;
	}

	/**
	 * Gets a name of this object.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->mName;
	}

	/**
	 * Gets a value that indicates whether the user has been authenticated.
	 *
	 * @return void
	 */
	public function isAuthenticated() {
	}
}

/**
 * Defines the basic functionality of a principal object.
 */
class XCube_Principal {
	/**
	 * The identity object which is tied to this object.
	 */
	public $mIdentity;

	/**
	 * Roles in this object.
	 * @var string[]
	 */
	public $_mRoles = [];

	public function __construct( $identity, $roles = [] ) {
		$this->mIdentity =& $identity;
		$this->_mRoles   = $roles;
	}

	/**
	 * Gets a identity object which is tied to this object.
	 * @return XCube_Identity
	 */
	public function getIdentity() {
		return $this->mIdentity;
	}

	/**
	 * Gets a value that indicates whether this principal has a role specified by $rolename.
	 *
	 *
	 * @return void
	 * @var string $rolename
	 */
	public function isInRole( $rolename ) {
	}
}
