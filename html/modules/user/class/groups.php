<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class UserGroupsObject extends XoopsSimpleObject
{
	function UserGroupsObject()
	{
		$this->initVar('groupid', XOBJ_DTYPE_INT, 0, true);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', true, 50);
		$this->initVar('description', XOBJ_DTYPE_TEXT, '', true);
		$this->initVar('group_type', XOBJ_DTYPE_STRING, '', true, 10);
	}
	
	function getUserCount()
	{
		$handler =& xoops_gethandler('member');
		return $handler->getUserCountByGroup($this->get('groupid'));
	}
}

class UserGroupsHandler extends XoopsObjectGenericHandler
{
	var $mTable = "groups";
	var $mPrimary = "groupid";
	var $mClass = "UserGroupsObject";
}

?>
