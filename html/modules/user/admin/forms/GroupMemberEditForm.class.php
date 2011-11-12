<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class User_GroupMemberEditForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.user.GroupMemberEditForm.TOKEN";
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['groupid'] =new XCube_IntProperty('groupid');
		$this->mFormProperties['uid'] =new XCube_IntArrayProperty('uid');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['uid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['uid']->setDependsByArray(array('intRange'));
		$this->mFieldProperties['uid']->addMessage('intRange', _AD_USER_ERROR_REQUEST_IS_WRONG);
		$this->mFieldProperties['uid']->addVar('min', '1');
		$this->mFieldProperties['uid']->addVar('max', '2');
	}
}

?>
