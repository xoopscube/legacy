<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Permission.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * XCube_PermissionUtils
 */
class XCube_Permissions
{
    public function getRolesOfAction()
    {
        $args = func_get_args();
        $actionName = array_shift($args);
        
        $root =& XCube_Root::getSingleton();
        return $root->mPermissionManager->getRolesOfAction($actionName, $args);
    }
}

class XCube_AbstractPermissionProvider
{
    // !Fix PHP7
    public function __construct()
    //public function XCube_AbstractPermissionProvider()
    {
    }
    
    public function prepare()
    {
    }
    
    public function getRolesOfAction($actionName, $args)
    {
    }
}
