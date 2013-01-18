<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractViewAction.class.php";

class Profile_Admin_DefinitionsViewAction extends Profile_AbstractViewAction
{
	/**
	 * @public
	 */
	function _getId()
	{
		return intval(xoops_getrequest('field_id'));
	}

	/**
	 * @public
	 */
	function &_getHandler()
	{
		$handler =& $this->mAsset->load('handler', "definitions");
		return $handler;
	}

	/**
	 * @public
	 */
	function executeViewSuccess(&$render)
	{
		$render->setTemplateName("definitions_view.html");
		#cubson::lazy_load('definitions', $this->mObject);
		$render->setAttribute('object', $this->mObject);
	}

	/**
	 * @public
	 */
	function executeViewError(&$render)
	{
		$this->mRoot->mController->executeRedirect("./index.php?action=DefinitionsList", 1, _MD_PROFILE_ERROR_CONTENT_IS_NOT_FOUND);
	}
}

?>
