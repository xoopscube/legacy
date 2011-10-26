<?php
/**
 * @package user
 * @version $Id: GroupAdminEditForm.class.php,v 1.1 2007/05/15 02:34:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class User_GroupAdminEditForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.user.GroupAdminEditForm.TOKEN" . $this->get('groupid');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['groupid'] =new XCube_IntProperty('groupid');
		$this->mFormProperties['name'] =new XCube_StringProperty('name');
		$this->mFormProperties['description'] =new XCube_TextProperty('description');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['groupid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['groupid']->setDependsByArray(array('required'));
		$this->mFieldProperties['groupid']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_GROUPID);
	
		$this->mFieldProperties['name'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['name']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['name']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_GROUP_NAME, '50');
		$this->mFieldProperties['name']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _AD_USER_LANG_GROUP_NAME, '50');
		$this->mFieldProperties['name']->addVar('maxlength', '50');
	}

	function load(&$obj)
	{
		$this->set('groupid', $obj->get('groupid'));
		$this->set('name', $obj->get('name'));
		$this->set('description', $obj->get('description'));
	}

	function update(&$obj)
	{
		$obj->set('groupid', $this->get('groupid'));
		$obj->set('name', $this->get('name'));
		$obj->set('description', $this->get('description'));
	}
}

?>
