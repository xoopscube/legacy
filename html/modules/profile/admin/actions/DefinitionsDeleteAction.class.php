<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractDeleteAction.class.php";

class Profile_Admin_DefinitionsDeleteAction extends Profile_AbstractDeleteAction
{
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
		// $this->mActionForm =new Profile_Admin_DefinitionsDeleteForm();
		$this->mActionForm =& $this->mAsset->create('form', "admin.delete_definitions");
		$this->mActionForm->prepare();
	}

	/**
	 * @public
	 */
	function executeViewInput(&$render)
	{
		$render->setTemplateName("definitions_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		#cubson::lazy_load('definitions', $this->mObject);
		$render->setAttribute('object', $this->mObject);
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
