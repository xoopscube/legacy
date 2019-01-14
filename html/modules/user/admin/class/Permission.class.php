<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

define('USER_PERMISSION_NONE', 0);
define('USER_PERMISSION_READ', 1);
define('USER_PERMISSION_ADMIN', 2);

class User_Permission
{
    public $mGroupId;
    public $mName;
    public $mValue = USER_PERMISSION_NONE;

    /**
     * User_PermissionItem
     */
    public $mItem;

    public function User_Permission($groupId, &$item)
    {
        $this->mGroupId = $groupId;
        $this->mItem =& $item;
        $this->_load();
    }
    
    public function getId()
    {
        return $this->mItem->getId();
    }
    
    public function getValue()
    {
        return $this->mValue;
    }

    public function setValue($value)
    {
        $value = intval($value);
        $this->mValue = $value & (USER_PERMISSION_READ | USER_PERMISSION_ADMIN);
    }
    
    public function _load()
    {
        $this->mValue = $this->mItem->loadPermission($this->mGroupId);
    }

    /**
     * Save a permission to database.
     */
    public function save()
    {
        $gpermHandler =& xoops_gethandler('groupperm');
        
        $name = $this->mItem->getReadPermName();
        if ($name) {
            $gperm =& $this->_createGperm($name);
            if (!$gpermHandler->insert($gperm)) {
                return false;
            }
        }

        $name = $this->mItem->getAdminPermName();
        if ($name) {
            $gperm =& $this->_createGperm($name);
            if ($gpermHandler->insert($gperm)) {
                return false;
            }
        }
        
        return true;
    }

    public function _createGperm($gperm_name = null)
    {
        $gpermHandler =& xoops_gethandler('groupperm');
        $gperm =& $gpermHandler->create();
        
        $gperm->setVar('gperm_groupid', $this->mGroupId);
        $gperm->setVar('gperm_itemid', $this->getId());
        $gperm->setVar('gperm_modid', 1);
        $gperm->setVar('gperm_name', $gperm_name);
        
        return $gperm;
    }
}

class User_PermissionItem
{
    /**
     * @return int
     */
    public function getId()
    {
    }

    /**
     * Return name
     */
    public function getName()
    {
    }
    
    /**
     * Return the url of module's control panel.
     */
    public function getAdminUrl()
    {
    }

    /**
     * @return bool
     */
    public function isActive()
    {
    }

    public function loadPermission($groupId)
    {
    }
    
    /**
     * @return string
     */
    public function getReadPermName()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getAdminPermName()
    {
        return null;
    }
}

class User_PermissionModuleItem extends User_PermissionItem
{
    public $mModule;
    
    public function User_PermissionModuleItem(&$module)
    {
        $this->mModule =& $module;
    }
    
    public function getId()
    {
        return $this->mModule->getVar('mid');
    }

    public function getName()
    {
        return $this->mModule->getProperty('name');
    }
    
    public function getAdminUrl()
    {
    }
    
    public function isActive()
    {
        return true;
    }

    public function loadPermission($groupId)
    {
        $ret = USER_PERMISSION_NONE;

        $gpermHandler =& xoops_gethandler('groupperm');
        if ($gpermHandler->checkRight("module_admin", $this->mModule->getVar('mid'), $groupId)) {
            $ret |= USER_PERMISSION_ADMIN;
        }
        
        if ($gpermHandler->checkRight("module_read", $this->mModule->getVar('mid'), $groupId)) {
            $ret |= USER_PERMISSION_READ;
        }

        return $ret;
    }

    public function getReadPermName()
    {
        return "module_read";
    }

    public function getAdminPermName()
    {
        return "module_admin";
    }
}

class User_PermissionBlockItem extends User_PermissionItem
{
    public $mBlock;
    
    public function User_PermissionBlockItem(&$block)
    {
        $this->mBlock =& $block;
    }
    
    public function getId()
    {
        return $this->mBlock->getVar('bid');
    }
    
    public function getName()
    {
        return $this->mBlock->getProperty('title');
    }
    
    public function getAdminUrl()
    {
    }
    
    public function isActive()
    {
        return $this->mBlock->getProperty('visible')==1 ? true : false;
    }

    public function loadPermission($groupId)
    {
        $ret = USER_PERMISSION_NONE;

        $gpermHandler =& xoops_gethandler('groupperm');
        if ($gpermHandler->checkRight("block_read", $this->mBlock->getVar('bid'), $groupId, 1, true)) {
            $ret |= USER_PERMISSION_READ;
        }

        return $ret;
    }

    
    public function getReadPermName()
    {
        return "block_read";
    }
}

/**
 * @internal
 * This class exists for X2 system module.
 */
class User_PermissionSystemAdminItem extends User_PermissionItem
{
    public $mId;
    public $mName;
    
    public function User_PermissionSystemAdminItem($id, $name)
    {
        $this->mId = $id;
        $this->mName = $name;
    }

    public function getId()
    {
        return $this->mId;
    }

    public function getName()
    {
        return $this->mName;
    }
    
    public function getAdminUrl()
    {
    }
    
    public function isActive()
    {
        return true;
    }

    public function loadPermission($groupId)
    {
        $ret = USER_PERMISSION_NONE;
        
        $gpermHandler =& xoops_gethandler('groupperm');
        if ($gpermHandler->checkRight("system_admin", $this->mId, $groupId)) {
            $ret |= USER_PERMISSION_ADMIN;
        }
        
        return $ret;
    }

    public function getAdminPermName()
    {
        return "system_admin";
    }
}
