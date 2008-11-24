<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class UserOnlineObject extends XoopsSimpleObject
{
	var $mModule = null;
	
	function UserOnlineObject()
	{
		$this->initVar('online_uid', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('online_uname', XOBJ_DTYPE_STRING, '', true, 25);
		$this->initVar('online_updated', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('online_module', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('online_ip', XOBJ_DTYPE_STRING, '', true, 15);
	}
	
	function loadModule()
	{
		if ($this->get('online_module')) {
			$handler =& xoops_gethandler('module');
			$this->mModule =& $handler->get($this->get('online_module'));
		}
	}
}

class UserOnlineHandler extends XoopsObjectGenericHandler
{
	var $mTable = "online";
	var $mPrimary = "";
	var $mClass = "UserOnlineObject";
}

?>
