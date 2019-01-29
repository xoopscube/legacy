<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyRenderBannerObject extends XoopsSimpleObject
{
    public $mClient = null;
    public $_mClientLoadedFlag = false;

    // !Fix deprecated constructor for PHP 7.x
    public function __construct()
    // public function LegacyRenderBannerObject()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('bid', XOBJ_DTYPE_INT, '', false);
        $this->initVar('cid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('imptotal', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('impmade', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('clicks', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('imageurl', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('clickurl', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('date', XOBJ_DTYPE_INT, time(), true);
        $this->initVar('htmlbanner', XOBJ_DTYPE_BOOL, '0', true);
        $this->initVar('htmlcode', XOBJ_DTYPE_TEXT, '', true);
        $initVars=$this->mVars;
    }

    public function loadBannerclient()
    {
        if ($this->_mClientLoadedFlag == false) {
            $handler =& xoops_getmodulehandler('bannerclient', 'legacyRender');
            $this->mClient =& $handler->get($this->get('cid'));
            $this->_mClientLoadedFlag = true;
        }
    }
}

class LegacyRenderBannerHandler extends XoopsObjectGenericHandler
{
    public $mTable = "banner";
    public $mPrimary = "bid";
    public $mClass = "LegacyRenderBannerObject";
}
