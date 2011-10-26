<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Legacy_ModuleEditForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.legacy.ModuleEditForm.TOKEN" . $this->get('mid');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['mid'] =new XCube_IntProperty('mid');
		$this->mFormProperties['name'] =new XCube_StringProperty('name');
		$this->mFormProperties['weight'] =new XCube_IntProperty('weight');
		$this->mFormProperties['read_groupid'] =new XCube_IntArrayProperty('read_groupid');
		$this->mFormProperties['admin_groupid'] =new XCube_IntArrayProperty('admin_groupid');
		$this->mFormProperties['module_cache'] =new XCube_StringProperty('module_cache');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['mid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['mid']->setDependsByArray(array('required'));
		$this->mFieldProperties['mid']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_MOD_MID);
	
		$this->mFieldProperties['name'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['name']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['name']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_NAME, '255');
		$this->mFieldProperties['name']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _AD_LEGACY_LANG_NAME, '255');
		$this->mFieldProperties['name']->addVar('maxlength', '255');
	
		$this->mFieldProperties['weight'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['weight']->setDependsByArray(array('required', 'intRange'));
		$this->mFieldProperties['weight']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_WEIGHT);
		$this->mFieldProperties['weight']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_LEGACY_LANG_WEIGHT);
		$this->mFieldProperties['weight']->addVar('min', '0');
		$this->mFieldProperties['weight']->addVar('max', '255');
	
		$this->mFieldProperties['read_groupid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['read_groupid']->setDependsByArray(array('objectExist'));
		$this->mFieldProperties['read_groupid']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_GROUPID);
		$this->mFieldProperties['read_groupid']->addVar('handler', 'group');

		$this->mFieldProperties['admin_groupid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['admin_groupid']->setDependsByArray(array('objectExist'));
		$this->mFieldProperties['admin_groupid']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_GROUPID);
		$this->mFieldProperties['admin_groupid']->addVar('handler', 'group');

		$this->mFieldProperties['module_cache'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['module_cache']->setDependsByArray(array('required', 'objectExist'));
		$this->mFieldProperties['module_cache']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_AM_MODCACHE);
		$this->mFieldProperties['module_cache']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _MD_AM_MODCACHE);
		$this->mFieldProperties['module_cache']->addVar('handler', 'cachetime');

	}

/*
//Umm...some modules have no readgroup or no admingroup	
	function validateRead_groupid()
	{
		$groupid = $this->get('read_groupid');
		if (!(count($groupid))) {
			$this->addErrorMessage(_AD_LEGACY_ERROR_GROUPID);
		}
	}
	
	function validateAdmin_groupid()
	{
		$groupid = $this->get('admin_groupid');
		if (!(count($groupid))) {
			$this->addErrorMessage(_AD_LEGACY_ERROR_GROUPID);
		}
	}
*/
	function load(&$obj)
	{
		$this->set('mid', $obj->get('mid'));
		$this->set('name', $obj->get('name'));
		$this->set('weight', $obj->get('weight'));

		$root =& XCube_Root::getSingleton();
		$module_cache = !empty($root->mContext->mXoopsConfig['module_cache'][$obj->get('mid')]) ? $root->mContext->mXoopsConfig['module_cache'][$obj->get('mid')]: 0;
		$this->set('module_cache', $module_cache);
	}

	function update(&$obj)
	{

		$obj->set('name', $this->get('name'));
		$obj->set('weight', $this->get('weight'));
	}
}

?>
