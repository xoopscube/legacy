<?php
/**
 * /core/XCube_Permission.class.php
 * @package    XCube
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      XCube_PermissionUtils
 * @remark     In some other languages, such as C, a void function can't be used in an expression, only as a statement.
 * IDEs and other tools can warn the user when the return value of a void function is being used.
 * It isn't strictly necessary for the language itself to cover this.
 * https://wiki.php.net/rfc/void_return_type#use_of_void_functions_in_expressions
 */

class XCube_Permissions {
	public function getRolesOfAction() {
		$args       = func_get_args();
		$actionName = array_shift( $args );

		$root =& XCube_Root::getSingleton();

		return $root->mPermissionManager->getRolesOfAction( $actionName, $args );
	}
}

class XCube_AbstractPermissionProvider {
	public function __construct() {
	}

	public function prepare() {
	}

	public function getRolesOfAction( $actionName, $args ) {
	}
}
