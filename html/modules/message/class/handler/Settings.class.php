<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.4.0
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2024 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
class MessageSettingsObject extends XoopsSimpleObject
{
    public function __construct()
    {
        $mModuleConfig = XCube_Root::getSingleton()->mContext->mModuleConfig;

        $this->initVar('uid', XOBJ_DTYPE_INT, 0);
        $this->initVar('usepm', XOBJ_DTYPE_INT, $mModuleConfig['usepm'], true);
        $this->initVar('tomail', XOBJ_DTYPE_INT, $mModuleConfig['tomail'], true);
        $this->initVar('viewmsm', XOBJ_DTYPE_INT, $mModuleConfig['viewmsm'], true);
        $this->initVar('pagenum', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('blacklist', XOBJ_DTYPE_STRING, '');
    }
}

class MessageSettingsHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'message_users';
    public $mPrimary = 'uid';
    public $mClass = 'MessageSettingsObject';

//    public function __construct(&$db)
//    {
//        parent::__construct($db);
//    }

    public function chkUser($uid)
    {
        $sql = 'SELECT `uname` FROM `' . $this->db->prefix('users') . '` ';
        $sql.= 'WHERE `uid` = ' . $uid;
        $result = $this->db->query($sql);
        return !(1 !== $this->db->getRowsNum($result));
    }

    public function getuidTouname($uname)
    {
        $uid = -1;
        $sql = 'SELECT `uid` FROM `' . $this->db->prefix('users') . '` ';
        $sql.= 'WHERE `uname` = ' . $this->db->quoteString($uname);
        $result = $this->db->query($sql);
        [$uid] = $this->db->fetchRow($result);
        return $uid;
    }
}
