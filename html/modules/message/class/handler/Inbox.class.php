<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.5.0
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2024 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
class MessageInboxObject extends XoopsSimpleObject
{
    public function __construct()
    {
        $this->initVar('inbox_id', XOBJ_DTYPE_INT, 0);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('from_uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('title', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('message', XOBJ_DTYPE_TEXT, '', true);
        $this->initVar('utime', XOBJ_DTYPE_INT, time(), true);
        $this->initVar('is_read', XOBJ_DTYPE_INT, 0);
        $this->initVar('uname', XOBJ_DTYPE_STRING, '', true, 100);
    }
  
    public function setVar($key, $value)
    {
        $this->set($key, $value);
    }
  
    public function set($key, $value)
    {
        switch ($key) {
      case 'subject':     $key = 'title';    break;
      case 'from_userid': $key = 'from_uid'; break;
      case 'msg_text':    $key = 'message';  break;
      case 'to_userid':   $key = 'uid';      break;
      case 'read_msg':    $key = 'is_read';  break;
      case 'msg_time':    $key = 'utime';    break;
    }
    
        $this->assignVar($key, $value);
    }
}

class MessageInboxHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'message_inbox';
    public $mPrimary = 'inbox_id';
    public $mClass = 'MessageInboxObject';
  
    public function __construct(&$db)
    {
        parent::__construct($db);
    }
  
    public function getCountUnreadByFromUid($uid)
    {
        $criteria = new CriteriaCompo(new Criteria('is_read', 0));
        $criteria->add(new Criteria('uid', $uid));
        return $this->getCount($criteria);
    }
  
    public function getInboxCount($uid)
    {
        $criteria = new CriteriaCompo(new Criteria('uid', $uid));
        return $this->getCount($criteria);
    }
  
    public function getSendUserList($uid = 0, $fuid = 0)
    {
        $ret = [];
        $sql = 'SELECT u.`uname`,u.`uid` FROM `' . $this->db->prefix('users') . '` u, ';
        $sql.= '`'.$this->mTable . '` i ';
        $sql.= 'WHERE i.`from_uid` = u.`uid` ';
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
  
    public function deleteDays($day, $type)
    {
        if ($day < 1) {
            return;
        }
        $time = time() - ($day * 86400);
        $sql = 'DELETE FROM `' . $this->mTable . '` ';
        $sql.= 'WHERE `utime` < ' . $time . ' ';
        if (0 == $type) {
            $sql.= 'AND `is_read` = 1 ';
        } else {
            $sql.= 'AND `is_read` < 2 ';
        }
        $this->db->queryF($sql);
    }
  
    public function _makeCriteria4sql($criteria)
    {
        $this->_chane_old($criteria);
        return parent::_makeCriteria4sql($criteria);
    }
  
    private function _chane_old(&$criteria)
    {
        if (is_a($criteria, 'CriteriaElement')) {
            if ($criteria->hasChildElements()) {
                for ($i = 0; $i < $criteria->getCountChildElements(); $i++) {
                    $this->_chane_old($criteria->criteriaElements[$i]);
                }
            } elseif ('Criteria' == get_class($criteria)) {
                switch ($criteria->column) {
          case 'read_msg': $criteria->column = 'is_read'; break;
          case 'to_userid': $criteria->column = 'uid'; break;
          case 'subject': $criteria->column = 'title'; break;
          case 'from_userid': $criteria->column = 'from_uid'; break;
          case 'msg_text': $criteria->column = 'message'; break;
          case 'msg_time': $criteria->column = 'utime';    break;
        }
            }
        }
    }
}
