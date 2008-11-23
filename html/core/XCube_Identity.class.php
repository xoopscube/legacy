<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Identity.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * Defines the basic functionality of an identity object.
 */
class XCube_Identity
{
	/**
	 * A name of the identity.
	 * @var string
	 */
	var $mName = "";
	
	/**
	 * The authentication type
	 * @var string
	 */
	var $_mAuthenticationType = "";
	
	function XCube_Identity()
	{
	}
	
	/**
	 * Sets the authentication type.
	 * @param string $type
	 */
	function setAuthenticationType($type)
	{
		$this->_mAuthenticationType = $type;
	}
	
	/**
	 * Gets the authentication type.
	 * @return string
	 */
	function getAuthenticationType()
	{
		return $this->_mAuthenticationType;
	}
	
	/**
	 * Sets a name of this object.
	 */
	function setName($name)
	{
		$this->mName = $name;
	}
	
	/**
	 * Gets a name of this object.
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->mName;
	}
	
	/**
	 * Gets a value that indicates whether the user has been authenticated.
	 *
	 * @return bool
	 */
	function isAuthenticated()
	{
	}
}

/**
 * Defines the basic functionality of a principal object.
 */
class XCube_Principal
{
	/**
	 * The identity object which is tied to this object.
	 */
	var $mIdentity = null;
	
	/**
	 * Roles in this object.
	 * @var string[]
	 */
	var $_mRoles = array();
	
	function XCube_Principal($identity, $roles = array())
	{
		$this->mIdentity =& $identity;
		$this->_mRoles = $roles;
	}
	
	/**
	 * Gets a identity object which is tied to this object.
	 * @return XCube_Identity
	 */
	function getIdentity()
	{
		return $this->mIdentity;
	}
	
	/**
	 * Gets a value that indicates whether this principal has a role specified by $rolename.
	 *
	 * @var string $rolename
	 * @return bool
	 */	
	function isInRole($rolename)
	{
	}
}




?>