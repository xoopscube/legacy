<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractListAction.class.php";

class Profile_DataListAction extends Profile_AbstractListAction
{
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
	function &_getFilterForm()
	{
		// $filter =& new Profile_DataFilterForm();
		$filter =& $this->mAsset->create('filter', "data");
		$filter->prepare($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	/**
	 * @protected
	 */
	function _getBaseUrl()
	{
		return "./index.php?action=DataList";
	}

	/**
	 * @public
	 */
	function executeViewIndex(&$render)
	{
		$render->setTemplateName("profile_data_list.html");
		#cubson::lazy_load_array('data', $this->mObjects);
		$render->setAttribute('objects', $this->mObjects);
		$render->setAttribute('pageNavi', $this->mFilter->mNavi);
	}
}

?>
