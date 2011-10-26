<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Permission.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * XCube_PermissionUtils
 */
class XCube_Permissions
{
	function getRolesOfAction()
	{
		$args = func_get_args();
		$actionName = array_shift($args);
		
		$root =& XCube_Root::getSingleton();
		return $root->mPermissionManager->getRolesOfAction($actionName, $args);
	}
}

class XCube_AbstractPermissionProvider
{
	function XCube_AbstractPermissionProvider()
	{
	}
	
	function prepare()
	{
	}
	
	function getRolesOfAction($actionName, $args)
	{
	}
}

?>