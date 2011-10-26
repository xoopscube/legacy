<?php
/**
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 3
 * @author Marijuana
 */
if (!defined('XOOPS_ROOT_PATH')) exit();
class MessageSettingsObject extends XoopsSimpleObject
{
  public function __construct()
  {
    $this->initVar('uid', XOBJ_DTYPE_INT, 0);
    $this->initVar('usepm', XOBJ_DTYPE_INT, 1, true);
    $this->initVar('tomail', XOBJ_DTYPE_INT, 0, true);
    $this->initVar('viewmsm', XOBJ_DTYPE_INT, 0, true);
    $this->initVar('pagenum', XOBJ_DTYPE_INT, 0, true);
    $this->initVar('blacklist', XOBJ_DTYPE_STRING, "");
  }
}

class MessageSettingsHandler extends XoopsObjectGenericHandler
{
  public $mTable = 'message_users';
  public $mPrimary = 'uid';
  public $mClass = 'MessageSettingsObject';

  public function __construct(&$db)
  {
    parent::XoopsObjectGenericHandler($db);
  }
  
  public function chkUser($uid)
  {
    $sql = "SELECT `uname` FROM `".$this->db->prefix('users')."` ";
    $sql.= "WHERE `uid` = ".$uid;
    $result = $this->db->query($sql);
    if ( $this->db->getRowsNum($result) != 1 ) {
      return false;
    } else {
      return true;
    }
  }
  
  public function getuidTouname($uname)
  {
    $uid = -1;
    $sql = "SELECT `uid` FROM `".$this->db->prefix('users')."` ";
    $sql.= "WHERE `uname` = ".$this->db->quoteString($uname);
    $result = $this->db->query($sql);
    list($uid) = $this->db->fetchRow($result);
    return $uid;
  }
}
?>
