<?php
/**
 * Message module for private messages and forward to email
 * 
 * @package    Message
 * @version    2.5.0
 * @author     Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2025 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class MessageOutboxObject extends XoopsSimpleObject
{
    public function __construct()
    {
        $this->initVar('outbox_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('to_uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('title', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('message', XOBJ_DTYPE_TEXT, '', true);
        $this->initVar('utime', XOBJ_DTYPE_INT, time(), true);
    }
}

class MessageOutboxHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'message_outbox';
    public $mPrimary = 'outbox_id';
    public $mClass = 'MessageOutboxObject';
  
    public function __construct(&$db)
    {
        parent::__construct($db);
    }
  
    public function getOutboxCount($uid)
    {
        $criteria = new CriteriaCompo(new Criteria('uid', $uid));
        return $this->getCount($criteria);
    }
  
    public function deleteDays($day)
    {
        if ($day < 1) {
            return;
        }
        $time = time() - ($day * 86400);
        $sql = 'DELETE FROM `' . $this->mTable . '` ';
        $sql.= 'WHERE `utime` < ' . $time;
        $this->db->queryF($sql);
    }

    public function getReceiveUserList($uid = 0, $fuid = 0)
    {
        $ret = [];
        $sql = 'SELECT u.`uname`,u.`uid` FROM `' . $this->db->prefix('users') . '` u, ';
        $sql.= '`'.$this->mTable . '` i ';
        $sql.= 'WHERE i.`to_uid` = u.`uid` ';
        $sql.= 'AND i.`uid` = ' . $uid . ' ';
        $sql.= 'GROUP BY u.`uname`, u.`uid`';
    
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            if ($fuid == $row['uid']) {
                $row['select'] = true;
            } else {
                $row['select'] = false;
            }
            $ret[] = $row;
        }
        return $ret;
    }
}
