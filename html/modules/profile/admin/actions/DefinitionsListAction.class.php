<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractListAction.class.php";

class Profile_Admin_DefinitionsListAction extends Profile_AbstractListAction
{
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
	function &_getFilterForm()
	{
		// $filter =new Profile_Admin_DefinitionsFilterForm();
		$filter =& $this->mAsset->create('filter', "admin.definitions");
		$filter->prepare($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	/**
	 * @protected
	 */
	function _getBaseUrl()
	{
		return "./index.php?action=DefinitionsList";
	}

	/**
	 * @public
	 */
	function executeViewIndex(&$render)
	{
		$render->setTemplateName("definitions_list.html");
		#cubson::lazy_load_array('definitions', $this->mObjects);
		$render->setAttribute('objects', $this->mObjects);
		$render->setAttribute('pageNavi', $this->mFilter->mNavi);
	}
}

?>
