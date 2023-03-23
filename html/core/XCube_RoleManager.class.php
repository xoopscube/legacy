<?php
/**
 * /core/XCube_RoleManager.class.php
 * @package XCube
 * @version    XCL 2.3.3
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      Provider class to manage role information with the store.
 */

class XCube_RoleManager {
	public function getRolesForUser( $username = null ) {
	}
}

/**
 * The utility class which handles role information without the root object.
 */
class XCube_Role {
	public function getRolesForUser( $username = null ) {
		$root =& XCube_Root::getSingleton();

		return $root->mRoleManager->getRolesForUser( $username );
	}
}
