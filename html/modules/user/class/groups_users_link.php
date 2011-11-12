<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class UserGroups_users_linkObject extends XoopsSimpleObject
{
	function UserGroups_users_linkObject()
	{
		$this->initVar('linkid', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('groupid', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('uid', XOBJ_DTYPE_INT, '0', true);
	}
}

class UserGroups_users_linkHandler extends XoopsObjectGenericHandler
{
	var $mTable = "groups_users_link";
	var $mPrimary = "linkid";
	var $mClass = "UserGroups_users_linkObject";
	
	function isUserOfGroup($uid, $groupid)
	{
		$criteria =new CriteriaCompo();
		$criteria->add(new Criteria('groupid', $groupid));
		$criteria->add(new Criteria('uid', $uid));
		
		$objs =& $this->getObjects($criteria);
		return (count($objs) > 0 && is_object($objs[0]));
	}
}

?>
