<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

define('USER_PERMISSION_NONE', 0);
define('USER_PERMISSION_READ', 1);
define('USER_PERMISSION_ADMIN', 2);

class User_Permission
{
	var $mGroupId;
	var $mName;
	var $mValue = USER_PERMISSION_NONE;

	/**
	 * User_PermissionItem
	 */
	var $mItem;

	function User_Permission($groupId, &$item)
	{
		$this->mGroupId = $groupId;
		$this->mItem =& $item;
		$this->_load();
	}
	
	function getId()
	{
		return $this->mItem->getId();
	}
	
	function getValue()
	{
		return $this->mValue;
	}

	function setValue($value)
	{
		$value = intval($value);
		$this->mValue = $value & ( USER_PERMISSION_READ | USER_PERMISSION_ADMIN );
	}
	
	function _load()
	{
		$this->mValue = $this->mItem->loadPermission($this->mGroupId);
	}

	/**
	 * Save a permission to database.
	 */
	function save()
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

	function _createGperm($gperm_name = null)
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
	function getId()
	{
	}

	/**
	 * Return name
	 */
	function getName()
	{
	}
	
	/**
	 * Return the url of module's control panel.
	 */
	function getAdminUrl()
	{
	}

	/**
	 * @return bool
	 */
	function isActive()
	{
	}

	function loadPermission($groupId)
	{
	}
	
	/**
	 * @return string
	 */
	function getReadPermName()
	{
		return null;
	}

	/**
	 * @return string
	 */
	function getAdminPermName()
	{
		return null;
	}
}

class User_PermissionModuleItem extends User_PermissionItem
{
	var $mModule;
	
	function User_PermissionModuleItem(&$module)
	{
		$this->mModule =& $module;
	}
	
	function getId()
	{
		return $this->mModule->getVar('mid');
	}

	function getName()
	{
		return $this->mModule->getProperty('name');
	}
	
	function getAdminUrl()
	{
	}
	
	function isActive()
	{
		return true;
	}

	function loadPermission($groupId)
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

	function getReadPermName()
	{
		return "module_read";
	}

	function getAdminPermName()
	{
		return "module_admin";
	}
}

class User_PermissionBlockItem extends User_PermissionItem
{
	var $mBlock;
	
	function User_PermissionBlockItem(&$block)
	{
		$this->mBlock =& $block;
	}
	
	function getId()
	{
		return $this->mBlock->getVar('bid');
	}
	
	function getName()
	{
		return $this->mBlock->getProperty('title');
	}
	
	function getAdminUrl()
	{
	}
	
	function isActive()
	{
		return $this->mBlock->getProperty('visible')==1 ? true : false;
	}

	function loadPermission($groupId)
	{
		$ret = USER_PERMISSION_NONE;

		$gpermHandler =& xoops_gethandler('groupperm');
		if ($gpermHandler->checkRight("block_read", $this->mBlock->getVar('bid'), $groupId, 1, true)) {
			$ret |= USER_PERMISSION_READ;
		}

		return $ret;
	}

	
	function getReadPermName()
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
	var $mId;
	var $mName;
	
	function User_PermissionSystemAdminItem($id, $name)
	{
		$this->mId = $id;
		$this->mName = $name;
	}

	function getId()
	{
		return $this->mId;
	}

	function getName()
	{
		return $this->mName;
	}
	
	function getAdminUrl()
	{
	}
	
	function isActive()
	{
		return true;
	}

	function loadPermission($groupId)
	{
		$ret = USER_PERMISSION_NONE;
		
		$gpermHandler =& xoops_gethandler('groupperm');
		if ($gpermHandler->checkRight("system_admin", $this->mId, $groupId)) {
			$ret |= USER_PERMISSION_ADMIN;
		}
		
		return $ret;
	}

	function getAdminPermName()
	{
		return "system_admin";
	}
}

?>