<?php
/**
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 3
 * @author Marijuana
 */
 if (!defined('XOOPS_ROOT_PATH')) {
     exit();
 }

require_once XOOPS_ROOT_PATH.'/modules/legacy/admin/class/ModuleInstaller.class.php';

class Message_myInstaller extends Legacy_ModuleInstaller
{
    /**
     * For backward compatibility
     */
    public function Message_myInstaller()
    {
        self::__construct();
    }

    public function __construct() 
    {
        parent::__construct();
    }

    public function executeInstall()
    {
        if (version_compare(PHP_VERSION, '5.0.0', '>')) {
            if ($this->check_pm()) {
                return parent::executeInstall();
            }
        } else {
            $this->mLog->addError(_MI_MESSAGE_INSTALL_ERROR);
        }
        return false;
    }
  
    public function check_pm()
    {
        $hand = xoops_gethandler('module');
        $obj = $hand->getByDirname('pm');
        if (is_object($obj)) {
            $this->mLog->addError(_MI_MESSAGE_INSTALL_ERROR2);
            return false;
        }
        return true;
    }
  
    public function _processScript()
    {
        $root = XCube_Root::getSingleton();
        $db = $root->mController->getDB();
    
    /*
    $INBOX = "INSERT INTO `".$db->prefix('message_inbox')."` (`inbox_id`, `uid`, `from_uid`, `title`, `message`, `utime`, `is_read`) SELECT 0, to_userid, from_userid, subject, msg_text, msg_time, read_msg FROM `".$db->prefix('priv_msgs')."`";
    $OUTBOX = "INSERT INTO `".$db->prefix('message_outbox')."` (`outbox_id`, `uid`, `to_uid`, `title`, `message`, `utime`) SELECT 0, from_userid, to_userid, subject, msg_text, msg_time FROM `".$db->prefix('priv_msgs')."`";
    if ( $db->queryF($INBOX) ) {
      $this->mLog->addReport('Update to inbox.');
      if ( $db->queryF($OUTBOX) ) {
        $this->mLog->addReport('Update to outbox.');
      }
    }
    */
    
    //--- Start ---
    $INBOX = "INSERT INTO `".$db->prefix('message_inbox')."` (`inbox_id`, `uid`, `from_uid`, `title`, `message`, `utime`, `is_read`) VALUES (0, %d, %d, %s, %s, %d, %d)";
        $OUTBOX = "INSERT INTO `".$db->prefix('message_outbox')."` (`outbox_id`, `uid`, `to_uid`, `title`, `message`, `utime`) VALUES (0, %d, %d, %s, %s, %d)";

        $num = 0;
        $sql = "SELECT * FROM `".$db->prefix('priv_msgs')."` ORDER BY `msg_id`";
        $result = $db->query($sql);
        while ($val = $db->fetchArray($result)) {
            $sql = sprintf($INBOX, $val['to_userid'], $val['from_userid'], $db->quoteString($val['subject']), $db->quoteString($val['msg_text']), $val['msg_time'], $val['read_msg']);
            $db->queryF($sql);
      
            $sql = sprintf($OUTBOX, $val['from_userid'], $val['to_userid'], $db->quoteString($val['subject']), $db->quoteString($val['msg_text']), $val['msg_time']);
            $db->queryF($sql);
            $num++;
        }
        $this->mLog->addReport('Update to '.$num.' records.');
    //--- End ---
    }
}
