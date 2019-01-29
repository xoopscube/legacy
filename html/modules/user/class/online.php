<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class UserOnlineObject extends XoopsSimpleObject
{
    public $mModule = null;
    // !Fix PHP7
    public function __construct()
    //public function UserOnlineObject()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('online_uid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('online_uname', XOBJ_DTYPE_STRING, '', true, 25);
        $this->initVar('online_updated', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('online_module', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('online_ip', XOBJ_DTYPE_STRING, '', true, 15);
        $initVars=$this->mVars;
    }
    
    public function loadModule()
    {
        if ($this->get('online_module')) {
            $handler =& xoops_gethandler('module');
            $this->mModule =& $handler->get($this->get('online_module'));
        }
    }
}

class UserOnlineHandler extends XoopsObjectGenericHandler
{
    public $mTable = "online";
    public $mPrimary = "";
    public $mClass = "UserOnlineObject";
}
