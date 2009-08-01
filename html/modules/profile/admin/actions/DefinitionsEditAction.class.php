<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractEditAction.class.php";

class Profile_Admin_DefinitionsEditAction extends Profile_AbstractEditAction
{
	var $mTypeArr = array();
	var $mValidationArr = array();

	/**
	 * @protected
	 */
	function _getId()
	{
		return intval(xoops_getrequest('field_id'));
	}

	/**
	 * @protected
	 */
	function &_getHandler()
	{
		$handler =& $this->mAsset->load('handler', "definitions");
		return $handler;
	}

	/**
	 * @protected
	 */
	function _setupActionForm()
	{
		// $this->mActionForm =& new Profile_Admin_DefinitionsEditForm();
		$this->mActionForm =& $this->mAsset->create('form', "admin.edit_definitions");
		$this->mActionForm->prepare();
	}

	/**
	 * @public
	 */
	function prepare()
	{
		parent::prepare();
		$handler =& $this->_getHandler();
		$this->mTypeArr = $handler->getTypeList();
		$this->mValidationArr = $handler->getValidationList();
	}

	/**
	 * @public
	 */
	function executeViewInput(&$render)
	{
		$gHandler =& xoops_gethandler('group');
	
		$render->setTemplateName("definitions_edit.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		$render->setAttribute('groupArr', $gHandler->getObjects());
		$render->setAttribute('accessArr', explode(',', $this->mObject->get('access')));
		$render->setAttribute('typeArr', $this->mTypeArr);
		$render->setAttribute('validationArr', $this->mValidationArr);

	}

	/**
	 * @public
	 */
	function executeViewSuccess(&$render)
	{
		$this->mRoot->mController->executeForward("./index.php?action=DefinitionsList");
	}

	/**
	 * @public
	 */
	function executeViewError(&$render)
	{
		$this->mRoot->mController->executeRedirect("./index.php?action=DefinitionsList", 1, _MD_PROFILE_ERROR_DBUPDATE_FAILED);
	}

	/**
	 * @public
	 */
	function executeViewCancel(&$render)
	{
		$this->mRoot->mController->executeForward("./index.php?action=DefinitionsList");
	}
}

?>
