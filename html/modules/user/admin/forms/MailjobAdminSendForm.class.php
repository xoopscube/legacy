<?php
/**
 * @package user
 * @version $Id: MailjobAdminSendForm.class.php,v 1.1 2007/05/15 02:34:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class User_MailjobAdminSendForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.user.MailjobAdminSendForm.TOKEN" . $this->get('mailjob_id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['mailjob_id'] =new XCube_IntProperty('mailjob_id');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['mailjob_id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['mailjob_id']->setDependsByArray(array('required'));
		$this->mFieldProperties['mailjob_id']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_MAILJOB_ID);
	}

	function load(&$obj)
	{
		$this->set('mailjob_id', $obj->get('mailjob_id'));
	}
}

?>
