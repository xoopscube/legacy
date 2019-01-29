<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class UserGroups_users_linkObject extends XoopsSimpleObject
{
    public function UserGroups_users_linkObject()
    {
        self::__construct();
    }

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('linkid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('groupid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('uid', XOBJ_DTYPE_INT, '0', true);
        $initVars=$this->mVars;
    }
}

class UserGroups_users_linkHandler extends XoopsObjectGenericHandler
{
    public $mTable = "groups_users_link";
    public $mPrimary = "linkid";
    public $mClass = "UserGroups_users_linkObject";
    
    public function isUserOfGroup($uid, $groupid)
    {
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('groupid', $groupid));
        $criteria->add(new Criteria('uid', $uid));
        
        $objs =& $this->getObjects($criteria);
        return (count($objs) > 0 && is_object($objs[0]));
    }
}
