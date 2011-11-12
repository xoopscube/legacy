<?php
/**
 * @package user
 * @version $Id: GroupAdminDeleteForm.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class User_GroupAdminDeleteForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.user.GroupAdminDeleteForm.TOKEN" . $this->get('group_id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['groupid'] =new XCube_IntProperty('groupid');

		//
		// Set field properties
		//
		$this->mFieldProperties['groupid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['groupid']->setDependsByArray(array('required'));
		$this->mFieldProperties['groupid']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_GROUPID);
	}
	
	function validateGroupid()
	{
		$groupid = $this->get('groupid');
		if ($groupid <= XOOPS_GROUP_ANONYMOUS) {
			$this->addErrorMessage("You can't delete this group.");
		}
	}

	function load(&$obj)
	{
		$this->set('groupid', $obj->get('groupid'));
	}

	function update(&$obj)
	{
		$obj->setVar('groupid', $this->get('groupid'));
	}
}

?>
