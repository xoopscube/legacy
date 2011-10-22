<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

require_once LECAT_TRUST_PATH . '/class/AbstractListAction.class.php';

/**
 * Lecat_CatListAction
**/
class Lecat_CatListAction extends Lecat_AbstractListAction
{
	/**
	 * &_getHandler
	 * 
	 * @param	void
	 * 
	 * @return	Lecat_CatHandler
	**/
	protected function &_getHandler()
	{
		$handler =& $this->mAsset->getObject('handler', 'cat');
		return $handler;
	}

	/**
	 * &_getFilterForm
	 * 
	 * @param	void
	 * 
	 * @return	Lecat_CatFilterForm
	**/
	protected function &_getFilterForm()
	{
		// $filter =new Lecat_CatFilterForm();
		$filter =& $this->mAsset->getObject('filter', 'cat',false);
		$filter->prepare($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	/**
	 * _getBaseUrl
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	protected function _getBaseUrl()
	{
		return Legacy_Utils::renderUri($this->mAsset->mDirname, 'cat');
	}

	/**
	 * getDefaultView
	 * 
	 * @param	void
	 * 
	 * @return	Enum
	**/
	public function getDefaultView()
	{
		$handler =& $this->_getHandler();
		$this->mObjects =& $handler->getTree();
	
		return LECAT_FRAME_VIEW_INDEX;
	}

	/**
	 * executeViewIndex
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewIndex(/*** XCube_RenderTarget ***/ &$render)
	{
		$render->setTemplateName($this->mAsset->mDirname . '_cat_list.html');
		$render->setAttribute('objects', $this->mObjects);
		$render->setAttribute('dirname', $this->mAsset->mDirname);
		//	$render->setAttribute('pageNavi', $this->mFilter->mNavi);
	}
}

?>
