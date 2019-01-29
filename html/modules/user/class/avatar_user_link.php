<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class UserAvatar_user_linkObject extends XoopsSimpleObject
{
    // !Fix deprecated constructor for PHP 7.x
    public function __construct()
    // public function UserAvatar_user_linkObject()
    {
        $this->initVar('avatar_id', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('user_id', XOBJ_DTYPE_INT, '0', true);
    }
}

class UserAvatar_user_linkHandler extends XoopsObjectGenericHandler
{
    public $mTable = "avatar_user_link";
    public $mPrimary = "";
    public $mClass = "UserAvatar_user_linkObject";
    
    public function &get($id)
    {
        $ret = null;
        return $ret;
    }
    
    public function _update(&$obj)
    {
        return $this->_insert($obj);
    }
    
    public function delete(&$obj, $force=false)
    {
        $id = $this->db->quoteString($obj->get('avatar_id'));
        $sql = "DELETE FROM " . $this->mTable . " WHERE avatar_id=" . $obj->get('avatar_id') . " AND user_id=" . $obj->get('user_id');

        return $force ? $this->db->queryF($sql) : $this->db->query($sql);
    }

    /**
     * Delete all of link informations about a user specified. 
     *
     * @return bool
     */
    public function deleteAllByUser(&$xoopsUser)
    {
        if (is_object($xoopsUser)) {
            $criteria =new Criteria('user_id', $xoopsUser->get('uid'));
            return $this->deleteAll($criteria);
        }
    }
}
