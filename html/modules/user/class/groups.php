<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class UserGroupsObject extends XoopsSimpleObject
{
    // !Fix deprecated constructor for PHP 7.x
    public function __construct()
    // public function UserGroupsObject()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('groupid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('name', XOBJ_DTYPE_STRING, '', true, 50);
        $this->initVar('description', XOBJ_DTYPE_TEXT, '', true);
        $this->initVar('group_type', XOBJ_DTYPE_STRING, '', true, 10);
        $initVars=$this->mVars;
    }
    
    public function getUserCount()
    {
        $handler =& xoops_gethandler('member');
        return $handler->getUserCountByGroup($this->get('groupid'));
    }
}

class UserGroupsHandler extends XoopsObjectGenericHandler
{
    public $mTable = "groups";
    public $mPrimary = "groupid";
    public $mClass = "UserGroupsObject";
}
