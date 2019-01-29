<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class UserUsersObject extends XoopsSimpleObject
{
    //
    // TODO naming rule
    //
    public $Groups = array();
    public $_mGroupsLoadedFlag = false;
    
    public $_mRankLoadedFlag = false;
    public $mRank;
    
    public function UserUsersObject()
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
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('name', XOBJ_DTYPE_STRING, '', false, 60);
        $this->initVar('uname', XOBJ_DTYPE_STRING, '', true, 25);
        $this->initVar('email', XOBJ_DTYPE_STRING, '', true, 60);
        $this->initVar('url', XOBJ_DTYPE_STRING, '', false, 100);
        $this->initVar('user_avatar', XOBJ_DTYPE_STRING, 'blank.gif', false, 30);
        $this->initVar('user_regdate', XOBJ_DTYPE_INT, time(), true);
        $this->initVar('user_icq', XOBJ_DTYPE_STRING, '', false, 15);
        $this->initVar('user_from', XOBJ_DTYPE_STRING, '', false, 100);
        $this->initVar('user_sig', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('user_viewemail', XOBJ_DTYPE_BOOL, '0', false);
        $this->initVar('actkey', XOBJ_DTYPE_STRING, '', false, 8);
        $this->initVar('user_aim', XOBJ_DTYPE_STRING, '', false, 18);
        $this->initVar('user_yim', XOBJ_DTYPE_STRING, '', false, 25);
        $this->initVar('user_msnm', XOBJ_DTYPE_STRING, '', false, 100);
        $this->initVar('pass', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('posts', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('attachsig', XOBJ_DTYPE_BOOL, '0', false);
        $this->initVar('rank', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('level', XOBJ_DTYPE_INT, '1', false);
        $this->initVar('theme', XOBJ_DTYPE_STRING, '', false, 100);
        $this->initVar('timezone_offset', XOBJ_DTYPE_FLOAT, '0.0', false);
        $this->initVar('last_login', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('umode', XOBJ_DTYPE_STRING, '', false, 10);
        $this->initVar('uorder', XOBJ_DTYPE_BOOL, '0', false);
        $this->initVar('notify_method', XOBJ_DTYPE_INT, '1', false);
        $this->initVar('notify_mode', XOBJ_DTYPE_INT, '0', false);
        $this->initVar('user_occ', XOBJ_DTYPE_STRING, '', false, 100);
        $this->initVar('bio', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('user_intrest', XOBJ_DTYPE_STRING, '', false, 150);
        $this->initVar('user_mailok', XOBJ_DTYPE_BOOL, '1', false);
        $initVars=$this->mVars;
    }
    
    public function getGroups()
    {
        return $this->Groups;
    }
    
    public function getNumGroups()
    {
        $this->_loadGroups();
        return count($this->Groups);
    }
    
    //
    // TODO naming rule
    //
    public function _loadGroups()
    {
        if (!$this->_mGroupsLoadedFlag) {
            $handler =& xoops_getmodulehandler('groups_users_link', 'user');
            $links =& $handler->getObjects(new Criteria('uid', $this->get('uid')));
            foreach ($links as $link) {
                $this->Groups[] = $link->get('groupid');
            }
        }
        
        $this->_mGroupsLoadedFlag = true;
    }

    
    public function _loadRank()
    {
        if (!$this->_mRankLoadedFlag) {
            $t_rank = xoops_getrank($this->get('rank'), $this->get('posts'));
            $rank_id = $t_rank['id'];
            
            $handler =& xoops_getmodulehandler('ranks');
            $this->mRank =& $handler->get($rank_id);
        
            $this->_mRankLoadedFlag = true;
        }
    }
    
    public function getRank()
    {
        if (!$this->_mRankLoadedFlag) {
            $this->_loadRank();
        }
            
        return $this->mRank;
    }
}

class UserUsersHandler extends XoopsObjectGenericHandler
{
    public $mTable = "users";
    public $mPrimary = "uid";
    public $mClass = "UserUsersObject";
    
    public function &get($id)
    {
        $obj =& parent::get($id);
        
        if (is_object($obj)) {
            $obj->_loadGroups();
        }
        
        return $obj;
    }
    
    public function &getObjects($criteria = null, $limit = null, $start = null, $id_as_key = false)
    {
        $objects =& parent::getObjects($criteria, $limit, $start, $id_as_key);

        if (count($objects)) {
            foreach (array_keys($objects) as $key) {
                $objects[$key]->_loadGroups();
            }
        }
        
        return $objects;
    }

    /**
     * Return the array which consists of an integer as the uid. This member
     * function is more speedy than getObjects().
     * 
     * @return Array
     */
    public function &getUids($criteria = null, $limit = null, $start = null, $id_as_key = false)
    {
        $ret = array();

        $sql = "SELECT uid FROM " . $this->mTable;
        
        $limit = 0;
        $start = 0;

        if ($criteria !== null && is_a($criteria, 'CriteriaElement')) {
            $where = $this->_makeCriteria4sql($criteria);
            
            if (trim($where)) {
                $sql .= " WHERE " . $where;
            }
            
            $sorts = array();
            foreach ($criteria->getSorts() as $sort) {
                $sorts[] = $sort['sort'] . ' ' . $sort['order'];
            }
            if ($criteria->getSort() != '') {
                $sql .= " ORDER BY " . implode(',', $sorts);
            }
            
            $limit=$criteria->getLimit();
            $start=$criteria->getStart();
        }
        
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        
        while ($row = $this->db->fetchArray($result)) {
            $ret[] = $row['uid'];
        }
        
        return $ret;
    }
    
    public function insert(&$user, $force = false)
    {
        // check pass colmun length of users table
        if (!defined('XCUBE_CORE_USER_PASS_LEN_FIXED') && is_callable('User_Utils::checkUsersPassColumnLength')) {
            User_Utils::checkUsersPassColumnLength();
        }

        if (parent::insert($user, $force)) {
            $flag = true;
            
            $user->_loadGroups();

            $handler =& xoops_getmodulehandler('groups_users_link', 'user');
            $oldLinkArr =& $handler->getObjects(new Criteria('uid', $user->get('uid')), $force);
            
            //
            // Delete
            //
            $oldGroupidArr = array();
            foreach (array_keys($oldLinkArr) as $key) {
                $oldGroupidArr[] = $oldLinkArr[$key]->get('groupid');
                if (!in_array($oldLinkArr[$key]->get('groupid'), $user->Groups)) {
                    $handler->delete($oldLinkArr[$key], $force);
                }
            }

            foreach ($user->Groups as $gid) {
                if (!in_array($gid, $oldGroupidArr)) {
                    $link =& $handler->create();
                
                    $link->set('groupid', $gid);
                    $link->set('uid', $user->get('uid'));
                
                    $flag =& $handler->insert($link, $force);
                
                    unset($link);
                }
            }
            
            return $flag;
        }
        
        return false;
    }
}
