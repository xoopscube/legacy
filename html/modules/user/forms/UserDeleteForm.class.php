<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class User_UserDeleteForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.user.UserDeleteForm.TOKEN";
	}

	function prepare()
	{
	}
}

?>
