<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyRenderBannerclientObject extends XoopsSimpleObject
{
	var $mBanners = array();
	var $_mBannersLoadedFlag = false;
	
	/**
	 * @todo A name of this property is a strange. banner finish?
	 */
	var $mFinishBanners = array();
	var $_mFinishBannersLoadedFlag = false;
	
	var $mBannerCount = null;
	var $_mBannerCountLoadedFlag = false;

	var $mFinishBannerCount = null;
	var $_mFinishBannerCountLoadedFlag = false;

	function LegacyRenderBannerclientObject()
	{
		$this->initVar('cid', XOBJ_DTYPE_INT, '', false);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', true, 60);
		$this->initVar('contact', XOBJ_DTYPE_STRING, '', true, 60);
		$this->initVar('email', XOBJ_DTYPE_STRING, '', true, 60);
		$this->initVar('login', XOBJ_DTYPE_STRING, '', true, 10);
		$this->initVar('passwd', XOBJ_DTYPE_STRING, '', true, 10);
		$this->initVar('extrainfo', XOBJ_DTYPE_TEXT, '', true);
	}

	function loadBanner()
	{
		if ($this->_mBannersLoadedFlag == false) {
			$handler =& xoops_getmodulehandler('banner', 'legacyRender');
			$this->mBanners =& $handler->getObjects(new Criteria('cid', $this->get('cid')));
			$this->_mBannersLoadedFlag = true;
		}
	}

	function loadBannerCount()
	{
		if ($this->_mBannerCountLoadedFlag == false) {
			$handler =& xoops_getmodulehandler('banner', 'legacyRender');
			$this->mBannerCount = $handler->getCount(new Criteria('cid', $this->get('cid')));
			$this->_mBannerCountLoadedFlag = true;
		}
	}

	function &createBanner()
	{
		$handler =& xoops_getmodulehandler('banner', 'legacyRender');
		$obj =& $handler->create();
		$obj->set('cid', $this->get('cid'));
		return $obj;
	}

	function loadBannerfinish()
	{
		if ($this->_mFinishBannersLoadedFlag == false) {
			$handler =& xoops_getmodulehandler('bannerfinish', 'legacyRender');
			$this->mFinishBanners =& $handler->getObjects(new Criteria('cid', $this->get('cid')));
			$this->_mFinishBannersLoadedFlag = true;
		}
	}

	function loadFinishBannerCount()
	{
		if ($this->_mFinishBannerCountLoadedFlag == false) {
			$handler =& xoops_getmodulehandler('bannerfinish', 'legacyRender');
			$this->mFinishBannerCount = $handler->getCount(new Criteria('cid', $this->get('cid')));
			$this->_mFinishBannerCountLoadedFlag = true;
		}
	}

	function &createBannerfinish()
	{
		$handler =& xoops_getmodulehandler('bannerfinish', 'legacyRender');
		$obj =& $handler->create();
		$obj->set('cid', $this->get('cid'));
		return $obj;
	}
}

class LegacyRenderBannerclientHandler extends XoopsObjectGenericHandler
{
	var $mTable = "bannerclient";
	var $mPrimary = "cid";
	var $mClass = "LegacyRenderBannerclientObject";

	function delete(&$obj)
	{
		$handler =& xoops_getmodulehandler('banner', 'legacyRender');
		$handler->deleteAll(new Criteria('cid', $obj->get('cid')));
		unset($handler);
	
		$handler =& xoops_getmodulehandler('bannerfinish', 'legacyRender');
		$handler->deleteAll(new Criteria('cid', $obj->get('cid')));
		unset($handler);
	
		return parent::delete($obj);
	}
}

?>
