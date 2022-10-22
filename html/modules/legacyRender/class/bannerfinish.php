<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyRenderBannerfinishObject extends XoopsSimpleObject
{
    public $mClient = null;
    public $_mClientLoadedFlag = false;

    public function __construct()
    {
        $this->initVar('bid', XOBJ_DTYPE_INT, '', false);
        $this->initVar('cid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('impressions', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('clicks', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('datestart', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('dateend', XOBJ_DTYPE_INT, '0', true);
    }

    public function loadBannerclient()
    {
        if (false == $this->_mClientLoadedFlag) {
            $handler =& xoops_getmodulehandler('bannerclient', 'legacyRender');
            $this->mClient =& $handler->get($this->get('cid'));
            $this->_mClientLoadedFlag = true;
        }
    }
}

class LegacyRenderBannerfinishHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'bannerfinish';
    public $mPrimary = 'bid';
    public $mClass = 'LegacyRenderBannerfinishObject';
}
