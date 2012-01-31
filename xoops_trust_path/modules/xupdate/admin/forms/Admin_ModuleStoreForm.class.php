<?php
/**
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH."/core/XCube_ActionForm.class.php";

class Xupdate_Admin_ModuleStoreForm extends XCube_ActionForm
{
	/***
	 * If the request is GET, never return token name.
	 * By this logic, a action can have three page in one action.
	 */
	function getTokenName()
	{
		//
		//
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			return "module.xupdate.ModuleSettingsForm.TOKEN";
		}
		else {
			return null;
		}
	}

	/***
	 * For displaying the confirm-page, don't show CSRF error.
	 * Always return null.
	 */
	function getTokenErrorMessage()
	{
		return null;
	}

	function prepare()
	{
		// set properties
		$this->mFormProperties['name']=new XCube_StringArrayProperty('name');

		// set fields
		$this->mFieldProperties['name']=new XCube_FieldProperty($this);
		$this->mFieldProperties['name']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['name']->addMessage("required",_MD_XUPDATE_ERROR_REQUIRED,_MD_XUPDATE_LANG_NAME,"140");
		$this->mFieldProperties['name']->addMessage("maxlength",_MD_XUPDATE_ERROR_MAXLENGTH,_MD_XUPDATE_LANG_NAME,"140");
		$this->mFieldProperties['name']->addVar("maxlength",140);

	}
}

?>