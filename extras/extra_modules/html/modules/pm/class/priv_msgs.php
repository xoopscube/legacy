<?php
/**
 * @package pm
 * @version $Id: priv_msgs.php,v 1.1 2007/05/15 02:35:26 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class PmPriv_msgsObject extends XoopsSimpleObject
{
    public function PmPriv_msgsObject()
    {
        $this->initVar('msg_id', XOBJ_DTYPE_INT, '', true);
        $this->initVar('msg_image', XOBJ_DTYPE_STRING, 'icon1.gif', false, 100);
        $this->initVar('subject', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('from_userid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('to_userid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('msg_time', XOBJ_DTYPE_INT, time(), true);
        $this->initVar('msg_text', XOBJ_DTYPE_TEXT, '', true);
        $this->initVar('read_msg', XOBJ_DTYPE_BOOL, '0', true);
    }
}

class PmPriv_msgsHandler extends XoopsObjectGenericHandler
{
    public $mTable = "priv_msgs";
    public $mPrimary = "msg_id";
    public $mClass = "PmPriv_msgsObject";
}
