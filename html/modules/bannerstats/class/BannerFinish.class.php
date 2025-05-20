<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_BannerfinishObject extends XoopsSimpleObject
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
        // Add the missing fields to store banner content information
        $this->initVar('imageurl', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('clickurl', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('htmlbanner', XOBJ_DTYPE_BOOL, '0', true);
        $this->initVar('htmlcode', XOBJ_DTYPE_TEXT, '', true);
    }

    public function loadBannerclient()
    {
        if (false == $this->_mClientLoadedFlag) {
            $handler =& xoops_getmodulehandler('bannerclient', 'bannerstats');
            $this->mClient =& $handler->get($this->get('cid'));
            $this->_mClientLoadedFlag = true;
        }
    }
}

class Bannerstats_BannerfinishHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'bannerfinish';
    public $mPrimary = 'bid';
    public $mClass = 'Bannerstats_BannerfinishObject';
}
