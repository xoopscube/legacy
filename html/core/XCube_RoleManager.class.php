<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_RoleManager.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * The provider class which handles role informations with the store.
 */
class XCube_RoleManager
{
	function getRolesForUser($username = null)
	{
	}
}

/**
 * The utility class which handles role information without the root object.
 */
class XCube_Role
{
	function getRolesForUser($username = null)
	{
		$root =& XCube_Root::getSingleton();
		return $root->mRoleManager->getRolesForUser($username);
	}
}

?>