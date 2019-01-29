<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_Identity.class.php,v 1.3 2008/09/25 15:12:02 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_Identity extends XCube_Identity
{
        // !Fix PHP7
        public function __construct(&$xoopsUser)
    //public function Legacy_Identity(&$xoopsUser)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($xoopsUser);
        //parent::XCube_Identity();
        
        if (!is_object($xoopsUser)) {
            die('Exception');
        }
        
        $this->mName = $xoopsUser->get('uname');
    }
    
    public function isAuthenticated()
    {
        return true;
    }
}

class Legacy_AnonymousIdentity extends XCube_Identity
{
    public function isAuthenticated()
    {
        return false;
    }
}

/**
 * This principal is free to add roles. And, this is also an interface, because
 * addRole() is used as a common interface in Legacy. Therefore, the dev team
 * may add the interface class to this file.
 * 
 * [Role Naming Convention]
 * Module.{dirname}.Visitor is 'module_read'.
 * Module.{dirname}.Admin is 'module_admin'.
 */
class Legacy_GenericPrincipal extends XCube_Principal
{
    /**
     * Adds a role to this object.
     * @param $roleName string
     */
    public function addRole($roleName)
    {
        if (!$this->isInRole($roleName)) {
            $this->_mRoles[] = $roleName;
        }
    }
    
    public function isInRole($roleName)
    {
        return in_array($roleName, $this->_mRoles);
    }
}
