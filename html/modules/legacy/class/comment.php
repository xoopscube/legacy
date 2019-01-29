<?php
/**
 *
 * @package Legacy
 * @version $Id: comment.php,v 1.3 2008/09/25 15:11:22 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyCommentObject extends XoopsSimpleObject
{
    public $mUser = null;
    public $mModule = null;
    public $mStatus = null;
    // !Fix deprecated constructor for php 7.x
    public function __construct()
    // public function LegacyCommentObject()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('com_id', XOBJ_DTYPE_INT, '', true);
        $this->initVar('com_pid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('com_rootid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('com_modid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('com_itemid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('com_icon', XOBJ_DTYPE_STRING, '', true, 25);
        $this->initVar('com_created', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('com_modified', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('com_uid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('com_ip', XOBJ_DTYPE_STRING, '', true, 15);
        $this->initVar('com_title', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('com_text', XOBJ_DTYPE_TEXT, '', true);
        $this->initVar('com_sig', XOBJ_DTYPE_BOOL, '0', true);
        $this->initVar('com_status', XOBJ_DTYPE_INT, '1', true);
        $this->initVar('com_exparams', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('dohtml', XOBJ_DTYPE_BOOL, '0', true);
        $this->initVar('dosmiley', XOBJ_DTYPE_BOOL, '1', true);
        $this->initVar('doxcode', XOBJ_DTYPE_BOOL, '1', true);
        $this->initVar('doimage', XOBJ_DTYPE_BOOL, '1', true);
        $this->initVar('dobr', XOBJ_DTYPE_BOOL, '1', true);
        $initVars=$this->mVars;
    }
    
    /**
     * Load a user object who wrote this comment to $mUser. 
     */
    public function loadUser()
    {
        $handler =& xoops_gethandler('member');
        $this->mUser =& $handler->getUser($this->get('com_uid'));
    }
    
    /**
     * Load a module object to $mModule. 
     */
    public function loadModule()
    {
        $handler =& xoops_gethandler('module');
        $this->mModule =& $handler->get($this->get('com_modid'));
    }
    
    public function loadStatus()
    {
        $handler =& xoops_getmodulehandler('commentstatus', 'legacy');
        $this->mStatus =& $handler->get($this->get('com_status'));
    }
    
    public function getVar($key)
    {
        if ($key == 'com_text') {
            $ts =& MyTextSanitizer::sGetInstance();
            return $ts->displayTarea($this->get($key), $this->get('dohtml'), $this->get('dosmiley'), $this->get('doxcode'), $this->get('doimage'), $this->get('dobr'));
        } else {
            return parent::getVar($key);
        }
    }
}

class LegacyCommentHandler extends XoopsObjectGenericHandler
{
    public $mTable = "xoopscomments";
    public $mPrimary = "com_id";
    public $mClass = "LegacyCommentObject";

    /**
     * @var XCube_Delegate
     */
    public $mUpdateSuccess;
    
    /**
     * @var XCube_Delegate
     */
    public $mDeleteSuccess;
    // !Fix deprecated constructor for php 7.x
    public function __construct(&$db)
    // public function LegacyCommentHandler(&$db)
    {
        parent::XoopsObjectGenericHandler($db);
        
        $this->mUpdateSuccess =new XCube_Delegate();
        $this->mDeleteSuccess =new XCube_Delegate();
    }
    
    public function insert(&$comment, $force = false)
    {
        if (parent::insert($comment, $force)) {
            $this->mUpdateSuccess->call($comment);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Delete $comment and childlen of $comment.
     */
    public function delete(&$comment, $force = false)
    {
        $criteria =new Criteria('com_pid', $comment->get('com_id'));
        $this->deleteAll($criteria);
        
        if (parent::delete($comment, $force)) {
            $this->mDeleteSuccess->call($comment);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * Return array of module id that comments are written.
     * 
     * @return array
     */
    public function getModuleIds()
    {
        $ret = array();

        $sql = "SELECT DISTINCT com_modid FROM " . $this->mTable;
        $res = $this->db->query($sql);
        if ($res) {
            while ($row = $this->db->fetchArray($res)) {
                $ret[] = $row['com_modid'];
            }
        }
        
        return $ret;
    }
}
