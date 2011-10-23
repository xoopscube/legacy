<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Pm_DeleteOneForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.pm.DeleteOneForm.TOKEN" . $this->get('msg_id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['msg_id'] =& new XCube_IntProperty('msg_id');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['msg_id'] =& new XCube_FieldProperty($this);
		$this->mFieldProperties['msg_id']->setDependsByArray(array('required','objectExist'));
		$this->mFieldProperties['msg_id']->addMessage('required', _MD_PM_ERROR_REQUIRED, _MD_PM_LANG_MSG_ID);
		$this->mFieldProperties['msg_id']->addMessage('objectExist', _MD_PM_ERROR_OBJECTEXIST, _MD_PM_LANG_MSG_ID);
		$this->mFieldProperties['msg_id']->addVar('handler', 'privmessage');
	}

	function load(&$obj)
	{
		$this->set('msg_id', $obj->get('msg_id'));
	}

	function update(&$obj)
	{
		$obj->set('msg_id', $this->get('msg_id'));
	}
}

?>
