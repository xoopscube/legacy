<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_BannerclientObject extends XoopsSimpleObject
{
    public $mBanners = [];
    public $_mBannersLoadedFlag = false;

    /**
     * @todo A name of this property is a strange. banner finish?
     */
    public $mFinishBanners = [];
    public $_mFinishBannersLoadedFlag = false;

    public $mBannerCount = null;
    public $_mBannerCountLoadedFlag = false;

    public $mFinishBannerCount = null;
    public $_mFinishBannerCountLoadedFlag = false;

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('cid', XOBJ_DTYPE_INT, '', false);
        $this->initVar('name', XOBJ_DTYPE_STRING, '', true, 60);
        $this->initVar('contact', XOBJ_DTYPE_STRING, '', true, 60);
        $this->initVar('email', XOBJ_DTYPE_STRING, '', true, 60);
        $this->initVar('login', XOBJ_DTYPE_STRING, '', true, 10);
        $this->initVar('passwd', XOBJ_DTYPE_STRING, '', true, 10);
        $this->initVar('extrainfo', XOBJ_DTYPE_TEXT, '', true);
        $initVars=$this->mVars;
    }

    public function loadBanner()
    {
        if (false == $this->_mBannersLoadedFlag) {
            $handler =& xoops_getmodulehandler('banner', 'bannerstats');
            $this->mBanners =& $handler->getObjects(new Criteria('cid', $this->get('cid')));
            $this->_mBannersLoadedFlag = true;
        }
    }

    public function loadBannerCount()
    {
        if (false == $this->_mBannerCountLoadedFlag) {
            $handler =& xoops_getmodulehandler('banner', 'bannerstats');
            $this->mBannerCount = $handler->getCount(new Criteria('cid', $this->get('cid')));
            $this->_mBannerCountLoadedFlag = true;
        }
    }

    public function &createBanner()
    {
        $handler =& xoops_getmodulehandler('banner', 'bannerstats');
        $obj =& $handler->create();
        $obj->set('cid', $this->get('cid'));
        return $obj;
    }

    public function loadBannerfinish()
    {
        if (false == $this->_mFinishBannersLoadedFlag) {
            $handler =& xoops_getmodulehandler('bannerfinish', 'bannerstats');
            $this->mFinishBanners =& $handler->getObjects(new Criteria('cid', $this->get('cid')));
            $this->_mFinishBannersLoadedFlag = true;
        }
    }

    public function loadFinishBannerCount()
    {
        if (false == $this->_mFinishBannerCountLoadedFlag) {
            $handler =& xoops_getmodulehandler('bannerfinish', 'bannerstats');
            $this->mFinishBannerCount = $handler->getCount(new Criteria('cid', $this->get('cid')));
            $this->_mFinishBannerCountLoadedFlag = true;
        }
    }

    public function &createBannerfinish()
    {
        $handler =& xoops_getmodulehandler('bannerfinish', 'bannerstats');
        $obj =& $handler->create();
        $obj->set('cid', $this->get('cid'));
        return $obj;
    }
}

class Bannerstats_BannerclientHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'bannerclient';
    public $mPrimary = 'cid';
    public $mClass = 'Bannerstats_BannerclientObject';

    public function delete(&$obj, $force = false)
    {
        $handler =& xoops_getmodulehandler('banner', 'bannerstats');
        $handler->deleteAll(new Criteria('cid', $obj->get('cid')));
        unset($handler);

        $handler =& xoops_getmodulehandler('bannerfinish', 'bannerstats');
        $handler->deleteAll(new Criteria('cid', $obj->get('cid')));
        unset($handler);

        return parent::delete($obj);
    }
}
