<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractEditAction.class.php";

class User_AbstractDeleteAction extends User_AbstractEditAction
{
	function isEnableCreate()
	{
		return false;
	}

	function _doExecute()
	{
		return $this->mObjectHandler->delete($this->mObject);
	}
}

?>
