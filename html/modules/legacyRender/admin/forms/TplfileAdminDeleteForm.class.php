<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class LegacyRender_TplfileAdminDeleteForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.legacyRender.TplfileAdminDeleteForm.TOKEN";
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['tpl_id'] =new XCube_IntProperty('tpl_id');
	
		//
		// Set field properties
		//
	
		$this->mFieldProperties['tpl_id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['tpl_id']->setDependsByArray(array('required'));
		$this->mFieldProperties['tpl_id']->addMessage('required', _AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_TPL_ID);
	}

	function load(&$obj)
	{
		$this->set('tpl_id', $obj->get('tpl_id'));
	}

	function update(&$obj)
	{
		$obj->set('tpl_id', $this->get('tpl_id'));
	}
}

?>
