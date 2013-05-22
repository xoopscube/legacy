<?php
/**
*
*/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH."/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Xupdate_Admin_ModuleInstallForm extends XCube_ActionForm
{
	/**
	 * getTokenName
	 *
	 * @param   void
	 *
	 * @return  string
	**/
		function getTokenName()
	{
		return "module.xupdate.Admin_ModuleInstallForm.TOKEN";
	}
	/**
	 * prepare
	 *
	 * @param   void
	 *
	 * @return  void
	**/
	public function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['id'] = new XCube_IntProperty('id');
		$this->mFormProperties['sid'] = new XCube_IntProperty('sid');

		$this->mFormProperties['addon_url'] = new XCube_StringProperty('addon_url');

		$this->mFormProperties['trust_dirname'] = new XCube_StringProperty('trust_dirname');
		$this->mFormProperties['dirname'] = new XCube_StringProperty('dirname');

		$this->mFormProperties['target_key'] = new XCube_StringProperty('target_key');
		$this->mFormProperties['target_type'] = new XCube_StringProperty('target_type');

		$this->mFormProperties['unzipdirlevel'] = new XCube_StringProperty('unzipdirlevel');

		$this->mFormProperties['license'] = new XCube_StringProperty('license');
		$this->mFormProperties['required'] = new XCube_StringProperty('required');

		//
		// Set field properties
		//
		$this->mFieldProperties['id'] = new XCube_FieldProperty($this);
		$this->mFieldProperties['id']->setDependsByArray(array('required'));
		$this->mFieldProperties['id']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, 'id');
		$this->mFieldProperties['sid'] = new XCube_FieldProperty($this);
		$this->mFieldProperties['sid']->setDependsByArray(array('required'));
		$this->mFieldProperties['sid']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_SID);

		$this->mFieldProperties['addon_url'] = new XCube_FieldProperty($this);
		$this->mFieldProperties['addon_url']->setDependsByArray(array('required'));
		$this->mFieldProperties['addon_url']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, 'addon_url');

		$this->mFieldProperties['trust_dirname'] = new XCube_FieldProperty($this);
		$this->mFieldProperties['trust_dirname']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['trust_dirname']->addMessage("maxlength",_MD_XUPDATE_ERROR_MAXLENGTH,'trust_dirname',"255");
		$this->mFieldProperties['trust_dirname']->addVar("maxlength",25);

		$this->mFieldProperties['dirname'] = new XCube_FieldProperty($this);
		$this->mFieldProperties['dirname']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['dirname']->addMessage("required",_MD_XUPDATE_ERROR_REQUIRED,_MD_XUPDATE_LANG_NAME,"255");
		$this->mFieldProperties['dirname']->addMessage("maxlength",_MD_XUPDATE_ERROR_MAXLENGTH,_MD_XUPDATE_LANG_NAME,"255");
		$this->mFieldProperties['dirname']->addVar("maxlength",255);

		$this->mFieldProperties['target_key'] = new XCube_FieldProperty($this);
		$this->mFieldProperties['target_key']->setDependsByArray(array('required'));
		$this->mFieldProperties['target_key']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, 'target_key');

		$this->mFieldProperties['target_type'] = new XCube_FieldProperty($this);
		$this->mFieldProperties['target_type']->setDependsByArray(array('required'));
		$this->mFieldProperties['target_type']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, 'target_type');

		//// not use "unzipdirlevel"
		//$this->mFieldProperties['unzipdirlevel'] = new XCube_FieldProperty($this);
		//$this->mFieldProperties['unzipdirlevel']->setDependsByArray(array('required'));
		//$this->mFieldProperties['unzipdirlevel']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, 'unzipdirlevel');

		$this->mFormProperties['html_only'] = new XCube_IntProperty('html_only');

		$this->mFieldProperties['license'] = new XCube_FieldProperty($this);
		$this->mFieldProperties['required'] = new XCube_FieldProperty($this);
	}

	/**
	 * load
	 *
	 * @param   XoopsSimpleObject  &$obj
	 *
	 * @return  void
	**/
	public function load(/*** XoopsSimpleObject ***/ &$obj)
	{

	}

	/**
	 * update
	 *
	 * @param   XoopsSimpleObject  &$obj
	 *
	 * @return  void
	**/
	public function update(/*** XoopsSimpleObject ***/ &$obj)
	{

	}

}//END CLASS

?>