<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractDeleteAction.class.php";

class Profile_DataDeleteAction extends Profile_AbstractDeleteAction
{
	/**
	 * @protected
	 */
	function _getId()
	{
		return intval(xoops_getrequest('uid'));
	}

	/**
	 * @protected
	 */
	function &_getHandler()
	{
		$handler =& $this->mAsset->load('handler', "data");
		return $handler;
	}

	/**
	 * @protected
	 */
	function _setupActionForm()
	{
		// $this->mActionForm =new Profile_DataDeleteForm();
		$this->mActionForm =& $this->mAsset->create('form', "delete_data");
		$this->mActionForm->prepare();
	}

	/**
	 * @public
	 */
	function executeViewInput(&$render)
	{
		$render->setTemplateName("profile_data_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		#cubson::lazy_load('data', $this->mObject);
		$render->setAttribute('object', $this->mObject);
	}

	/**
	 * @public
	 */
	function executeViewSuccess(&$render)
	{
		$this->mRoot->mController->executeForward("./index.php?action=DataList");
	}

	/**
	 * @public
	 */
	function executeViewError(&$render)
	{
		$this->mRoot->mController->executeRedirect("./index.php?action=DataList", 1, _MD_PROFILE_ERROR_DBUPDATE_FAILED);
	}

	/**
	 * @public
	 */
	function executeViewCancel(&$render)
	{
		$this->mRoot->mController->executeForward("./index.php?action=DataList");
	}
}

?>
