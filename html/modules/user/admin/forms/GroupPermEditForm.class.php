<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class User_GroupPermEditForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.user.GroupPermEditForm.TOKEN";
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['system'] =new XCube_BoolArrayProperty('system');
		$this->mFormProperties['module'] =new XCube_BoolArrayProperty('module');
		$this->mFormProperties['module_admin'] =new XCube_BoolArrayProperty('module_admin');
		$this->mFormProperties['block'] =new XCube_BoolArrayProperty('block');
	
		//
		// Set field properties
		//
	}
}

?>
