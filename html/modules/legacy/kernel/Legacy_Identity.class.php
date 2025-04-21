<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_Identity.class.php,v 1.3 2008/09/25 15:12:02 kilica Exp $
 * @copyright (c) 2005-2025 The XOOPSCube Project
 * @license GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_Identity extends XCube_Identity
{
    public function __construct(&$xoopsUser)
    {
        parent::__construct($xoopsUser);

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
     * @param string $roleName
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
