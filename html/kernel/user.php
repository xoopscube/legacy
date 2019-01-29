<?php
// $Id: user.php,v 1.4 2008/10/26 04:00:40 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://xoopscube.jp/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
/**
 * Class for users 
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class XoopsUser extends XoopsObject
{

    /**
     * Array of groups that user belongs to 
     * @var array
     * @access private
     */
    public $_groups = array();
    /**
     * @var bool is the user admin? 
     * @access private
     */
    public $_isAdmin = null;
    /**
     * @var string user's rank
     * @access private
     */
    public $_rank = null;
    /**
     * @var bool is the user online?
     * @access private
     */
    public $_isOnline = null;

    /**
     * constructor 
     * @param array $id Array of key-value-pairs to be assigned to the user. (for backward compatibility only)
     * @param int $id ID of the user to be loaded from the database.
     */
    public function __construct($id = null)
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
        } else {
            $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
            $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 60);
            $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, true, 25);
            $this->initVar('email', XOBJ_DTYPE_TXTBOX, null, true, 256);
            $this->initVar('url', XOBJ_DTYPE_TXTBOX, null, false, 100);
            $this->initVar('user_avatar', XOBJ_DTYPE_TXTBOX, null, false, 30);
            $this->initVar('user_regdate', XOBJ_DTYPE_INT, null, false);
            $this->initVar('user_icq', XOBJ_DTYPE_TXTBOX, null, false, 15);
            $this->initVar('user_from', XOBJ_DTYPE_TXTBOX, null, false, 100);
            $this->initVar('user_sig', XOBJ_DTYPE_TXTAREA, null, false, null);
            $this->initVar('user_viewemail', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('actkey', XOBJ_DTYPE_OTHER, null, false);
            $this->initVar('user_aim', XOBJ_DTYPE_TXTBOX, null, false, 18);
            $this->initVar('user_yim', XOBJ_DTYPE_TXTBOX, null, false, 25);
            $this->initVar('user_msnm', XOBJ_DTYPE_TXTBOX, null, false, 100);
            $this->initVar('pass', XOBJ_DTYPE_TXTBOX, null, false, 255);
            $this->initVar('posts', XOBJ_DTYPE_INT, null, false);
            $this->initVar('attachsig', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('rank', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('level', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('theme', XOBJ_DTYPE_OTHER, null, false);
            $this->initVar('timezone_offset', XOBJ_DTYPE_OTHER, null, false);
            $this->initVar('last_login', XOBJ_DTYPE_INT, 0, false);
            $this->initVar('umode', XOBJ_DTYPE_OTHER, null, false);
            $this->initVar('uorder', XOBJ_DTYPE_INT, 1, false);
            // RMV-NOTIFY
            $this->initVar('notify_method', XOBJ_DTYPE_OTHER, 1, false);
            $this->initVar('notify_mode', XOBJ_DTYPE_OTHER, 0, false);
            $this->initVar('user_occ', XOBJ_DTYPE_TXTBOX, null, false, 100);
            $this->initVar('bio', XOBJ_DTYPE_TXTAREA, null, false, null);
            $this->initVar('user_intrest', XOBJ_DTYPE_TXTBOX, null, false, 150);
            $this->initVar('user_mailok', XOBJ_DTYPE_INT, 1, false);
            $initVars = $this->vars;
        }
    
        // for backward compatibility
        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $member_handler = xoops_gethandler('member');
                $user =& $member_handler->getUser($id);
                foreach ($user->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
            }
        }
    }
    public function XoopsUser($id = null)
    {
        return self::__construct($id);
    }

    /**
     * check if the user is a guest user
     *
     * @return bool returns false
     *
     */
    public function isGuest()
    {
        return false;
    }


    /**
     * Updated by Catzwolf 11 Jan 2004
     * find the username for a given ID
     * 
     * @param int $userid ID of the user to find
     * @param int $usereal switch for usename or realname
     * @return string name of the user. name for "anonymous" if not found.
     */
    public static function getUnameFromId($userid, $usereal = 0)
    {
        $userid = (int)$userid;
        $usereal = (int)$usereal;
        if ($userid > 0) {
            static $nameCache;
            $field = $usereal?'name':'uname';
            if (isset($nameCache[$field][$userid])) {
                return $nameCache[$field][$userid];
            }
            $member_handler = xoops_gethandler('member');
            $user =& $member_handler->getUser($userid);
            if (is_object($user)) {
                return ($nameCache[$field][$userid] = $user->getVar($field));
            }
        }
        return $GLOBALS['xoopsConfig']['anonymous'];
    }
    /**
     * increase the number of posts for the user 
     *
     * @deprecated
     */
    public function incrementPost()
    {
        $member_handler = xoops_gethandler('member');
        return $member_handler->updateUserByField($this, 'posts', $this->getVar('posts') + 1);
    }
    /**
     * set the groups for the user
     * 
     * @param array $groupsArr Array of groups that user belongs to
     */
    public function setGroups($groupsArr)
    {
        if (is_array($groupsArr)) {
            $this->_groups =& $groupsArr;
        }
    }
    /**
     * get the groups that the user belongs to
     * 
     * @param $bReget When this is true, this object gets group informations from DB again.
     *                This is a special method for the BASE(CMS core) functions, you should
     *                not use this proactivity.
     * @return array array of groups 
     */
    public function getGroups($bReget = false)
    {
        if ($bReget) {
            unset($this->_groups);
        }
        
        if (empty($this->_groups)) {
            $member_handler = xoops_gethandler('member');
            $this->_groups = $member_handler->getGroupsByUser($this->getVar('uid'));
        }
        return $this->_groups;
    }
    
    public function getNumGroups()
    {
        if (empty($this->_groups)) {
            $this->getGroups();
        }
        return count($this->_groups);
    }
    
    
    /**
     * alias for {@link getGroups()}
     * @see getGroups()
     * @return array array of groups
     * @deprecated
     */
    public function groups()
    {
        return $this->getGroups();
    }
    /**
     * Is the user admin ?
     *
     * This method will return true if this user has admin rights for the specified module.<br />
     * - If you don't specify any module ID, the current module will be checked.<br />
     * - If you set the module_id to -1, it will return true if the user has admin rights for at least one module
     *
     * @param int $module_id check if user is admin of this module
     * @return bool is the user admin of that module?
     */
    public function isAdmin($module_id = null)
    {
        if ($module_id === null) {
            global $xoopsModule;
            $module_id = isset($xoopsModule) ? $xoopsModule->getVar('mid', 'n') : 1;
        } elseif ((int)$module_id < 1) {
            $module_id = 0;
        }
        static $moduleperm_handler;
        isset($moduleperm_handler) || $moduleperm_handler = xoops_gethandler('groupperm');
        return $moduleperm_handler->checkRight('module_admin', $module_id, $this->getGroups());
    }
    /**
     * get the user's rank
     * @return array array of rank ID and title
     */
    public function rank()
    {
        if (!isset($this->_rank)) {
            $this->_rank = xoops_getrank($this->getVar('rank'), $this->getVar('posts'));
        }
        return $this->_rank;
    }
    /**
     * is the user activated?
     * @return bool
     */
    public function isActive()
    {
        if ($this->getVar('level') == 0) {
            return false;
        }
        return true;
    }
    /**
     * is the user currently logged in? 
     * @return bool
     */
    public function isOnline()
    {
        if (!isset($this->_isOnline)) {
            $onlinehandler = xoops_gethandler('online');
            $this->_isOnline = ($onlinehandler->getCount(new Criteria('online_uid', $this->getVar('uid', 'N'))) > 0) ? true : false;
        }
        return $this->_isOnline;
    }
    /**#@+
     * specialized wrapper for {@link XoopsObject::getVar()}
     * 
     * kept for compatibility reasons.
     * 
     * @see XoopsObject::getVar()
     * @deprecated
     */
    /**
     * get the users UID 
     * @return int
     */
    public function uid()
    {
        return $this->getVar('uid');
    }
    
    /**
     * get the users name
     * @param string $format format for the output, see {@link XoopsObject::getVar()}
     * @return string 
     */
    public function name($format="S")
    {
        return $this->getVar("name", $format);
    }
    
    /**
     * get the user's uname
     * @param string $format format for the output, see {@link XoopsObject::getVar()}
     * @return string
     */
    public function uname($format="S")
    {
        return $this->getVar("uname", $format);
    }
    
    /**
     * get the user's email 
     * 
     * @param string $format format for the output, see {@link XoopsObject::getVar()}
     * @return string
     */
    public function email($format="S")
    {
        return $this->getVar("email", $format);
    }
    
    public function url($format="S")
    {
        return $this->getVar("url", $format);
    }
    
    public function user_avatar($format="S")
    {
        return $this->getVar("user_avatar");
    }
    
    public function user_regdate()
    {
        return $this->getVar("user_regdate");
    }
    
    public function user_icq($format="S")
    {
        return $this->getVar("user_icq", $format);
    }
    
    public function user_from($format="S")
    {
        return $this->getVar("user_from", $format);
    }
    public function user_sig($format="S")
    {
        return $this->getVar("user_sig", $format);
    }
    
    public function user_viewemail()
    {
        return $this->getVar("user_viewemail");
    }
    
    public function actkey()
    {
        return $this->getVar("actkey");
    }
    
    public function user_aim($format="S")
    {
        return $this->getVar("user_aim", $format);
    }
    
    public function user_yim($format="S")
    {
        return $this->getVar("user_yim", $format);
    }
    
    public function user_msnm($format="S")
    {
        return $this->getVar("user_msnm", $format);
    }
    
    public function pass()
    {
        return $this->getVar("pass");
    }
    
    public function posts()
    {
        return $this->getVar("posts");
    }
    
    public function attachsig()
    {
        return $this->getVar("attachsig");
    }
    
    public function level()
    {
        return $this->getVar("level");
    }
    
    public function theme()
    {
        return $this->getVar("theme");
    }
    
    public function timezone()
    {
        return $this->getVar("timezone_offset");
    }
    
    public function umode()
    {
        return $this->getVar("umode");
    }
    
    public function uorder()
    {
        return $this->getVar("uorder");
    }
   
    // RMV-NOTIFY
    public function notify_method()
    {
        return $this->getVar("notify_method");
    }

    public function notify_mode()
    {
        return $this->getVar("notify_mode");
    }
 
    public function user_occ($format="S")
    {
        return $this->getVar("user_occ", $format);
    }
    
    public function bio($format="S")
    {
        return $this->getVar("bio", $format);
    }
    
    public function user_intrest($format="S")
    {
        return $this->getVar("user_intrest", $format);
    }
    
    public function last_login()
    {
        return $this->getVar("last_login");
    }

    /**
     * This class has avatar in uploads directory. 
     * @return bool
     */
    public function hasAvatar()
    {
        $avatar=$this->getVar('user_avatar');
        if (!$avatar || $avatar=="blank.gif") {
            return false;
        }

        $file=XOOPS_UPLOAD_PATH."/".$avatar;
        return file_exists($file);
    }
    
    /**
     *
     * Return Abs URL for displaying avatar.
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        if ($this->hasAvatar()) {
            return XOOPS_UPLOAD_URL."/".$this->getVar('user_avatar');
        }

        return null;
    }

    /**#@-*/
}

/**
 * Class that represents a guest user
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class XoopsGuestUser extends XoopsUser
{
    /**
     * check if the user is a guest user
     *
     * @return bool returns true
     *
     */
    public function isGuest()
    {
        return true;
    }
    
    public function getGroups($bReget = false)
    {
        return XOOPS_GROUP_ANONYMOUS;
    }
}


/**
* XOOPS user handler class.  
* This class is responsible for providing data access mechanisms to the data source 
* of XOOPS user class objects.
*
* @author  Kazumi Ono <onokazu@xoops.org>
* @copyright copyright (c) 2000-2003 XOOPS.org
* @package kernel
*/
class XoopsUserHandler extends XoopsObjectHandler
{

    /**
     * create a new user
     * 
     * @param bool $isNew flag the new objects as "new"?
     * @return object XoopsUser
     */
    public function &create($isNew = true)
    {
        $user =new XoopsUser();
        if ($isNew) {
            $user->setNew();
        }
        return $user;
    }

    /**
     * retrieve a user
     * 
     * @param int $id UID of the user
     * @return mixed reference to the {@link XoopsUser} object, FALSE if failed
     */
    public function &get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('users').' WHERE uid='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                    $user =new XoopsUser();
                    $user->assignVars($this->db->fetchArray($result));
                    $ret =& $user;
                }
            }
        }
        return $ret;
    }

    /**
     * insert a new user in the database
     * 
     * @param object $user reference to the {@link XoopsUser} object
     * @param bool $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(&$user, $force = false)
    {
        if (strtolower(get_class($user)) != 'xoopsuser') {
            return false;
        }
        if (!$user->isDirty()) {
            return true;
        }
        if (!$user->cleanVars()) {
            return false;
        }
        // check pass colmun length of users table
        if (!defined('XCUBE_CORE_USER_PASS_LEN_FIXED') && is_callable('User_Utils::checkUsersPassColumnLength')) {
            User_Utils::checkUsersPassColumnLength();
        }
        foreach ($user->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        // RMV-NOTIFY
        // Added two fields, notify_method, notify_mode
        if ($user->isNew()) {
            $config = xoops_gethandler('config');
            $options = $config->getConfigs(new Criteria('conf_name', 'notify_method'));
            if (isset($options) and (count($options) == 1)) {
                $notify_method = $options[0]->getvar('conf_value');
            }
            $uid = $this->db->genId('users_uid_seq');
            $sql = sprintf("INSERT INTO %s (uid, uname, name, email, url, user_avatar, user_regdate, user_icq, user_from, user_sig, user_viewemail, actkey, user_aim, user_yim, user_msnm, pass, posts, attachsig, rank, level, theme, timezone_offset, last_login, umode, uorder, notify_method, notify_mode, user_occ, bio, user_intrest, user_mailok) VALUES (%u, %s, %s, %s, %s, %s, %u, %s, %s, %s, %u, %s, %s, %s, %s, %s, %u, %u, %u, %u, %s, %.2f, %u, %s, %u, %u, %u, %s, %s, %s, %u)", $this->db->prefix('users'), $uid, $this->db->quoteString($uname), $this->db->quoteString($name), $this->db->quoteString($email), $this->db->quoteString($url), $this->db->quoteString($user_avatar), time(), $this->db->quoteString($user_icq), $this->db->quoteString($user_from), $this->db->quoteString($user_sig), $user_viewemail, $this->db->quoteString($actkey), $this->db->quoteString($user_aim), $this->db->quoteString($user_yim), $this->db->quoteString($user_msnm), $this->db->quoteString($pass), $posts, $attachsig, $rank, $level, $this->db->quoteString($theme), $timezone_offset, 0, $this->db->quoteString($umode), $uorder, $notify_method, $notify_mode, $this->db->quoteString($user_occ), $this->db->quoteString($bio), $this->db->quoteString($user_intrest), $user_mailok);
        } else {
            $sql = sprintf("UPDATE %s SET uname = %s, name = %s, email = %s, url = %s, user_avatar = %s, user_icq = %s, user_from = %s, user_sig = %s, user_viewemail = %u, user_aim = %s, user_yim = %s, user_msnm = %s, posts = %d,  pass = %s, attachsig = %u, rank = %u, level= %u, theme = %s, timezone_offset = %.2f, umode = %s, last_login = %u, uorder = %u, notify_method = %u, notify_mode = %u, user_occ = %s, bio = %s, user_intrest = %s, user_mailok = %u WHERE uid = %u", $this->db->prefix('users'), $this->db->quoteString($uname), $this->db->quoteString($name), $this->db->quoteString($email), $this->db->quoteString($url), $this->db->quoteString($user_avatar), $this->db->quoteString($user_icq), $this->db->quoteString($user_from), $this->db->quoteString($user_sig), $user_viewemail, $this->db->quoteString($user_aim), $this->db->quoteString($user_yim), $this->db->quoteString($user_msnm), $posts, $this->db->quoteString($pass), $attachsig, $rank, $level, $this->db->quoteString($theme), $timezone_offset, $this->db->quoteString($umode), $last_login, $uorder, $notify_method, $notify_mode, $this->db->quoteString($user_occ), $this->db->quoteString($bio), $this->db->quoteString($user_intrest), $user_mailok, $uid);
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        if (empty($uid)) {
            $uid = $this->db->getInsertId();
        }
        $user->assignVar('uid', $uid);
        return true;
    }

    /**
     * delete a user from the database
     * 
     * @param object $user reference to the user to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
    public function delete(&$user, $force = false)
    {
        if (strtolower(get_class($user)) != 'xoopsuser') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE uid = %u", $this->db->prefix("users"), $user->getVar('uid'));
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * Get unames from the database
     * 
     * @param object $criteria {@link CriteriaElement} conditions to be met
     * @param bool $id_as_key use the UID as key for the array?
     * @return array array of uname
     */
    public function getUnames($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('users');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            if (!$id_as_key) {
                $ret[] = $myrow['uname'];
            } else {
                $ret[$myrow['uid']] = $myrow['uname'];
            }
        }
        return $ret;
    }

    /**
     * retrieve users from the database
     * 
     * @param object $criteria {@link CriteriaElement} conditions to be met
     * @param bool $id_as_key use the UID as key for the array?
     * @return array array of {@link XoopsUser} objects
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('users');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $user =new XoopsUser();
            $user->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $user;
            } else {
                $ret[$myrow['uid']] =& $user;
            }
            unset($user);
        }
        return $ret;
    }
    
    /**
     This method is called from pmlite.php. Wmm..
     Type:expand (no using criteria).
     @author minahito
     */
    public function &getObjectsByLevel($level=0)
    {
        $ret=array();
        $level=(int)$level;
        $result = $this->db->query("SELECT * FROM ".$this->db->prefix("users")." WHERE level > $level ORDER BY uname");
        if (!$result) {
            return $ret;
        }

        while ($myrow=$this->db->fetchArray($result)) {
            $user=new XoopsUser();
            $user->assignVars($myrow);
            $ret[]=&$user;
            unset($user);
        }
        
        return $ret;
    }

    /**
     * count users matching a condition
     * 
     * @param object $criteria {@link CriteriaElement} to match
     * @return int count of users
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('users');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }

    /**
     * delete users matching a set of conditions
     * 
     * @param object $criteria {@link CriteriaElement} 
     * @return bool FALSE if deletion failed
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM '.$this->db->prefix('users');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Change a value for users with a certain criteria
     * 
     * @param   string  $fieldname  Name of the field
     * @param   string  $fieldvalue Value to write
     * @param   object  $criteria   {@link CriteriaElement} 
     * 
     * @return  bool
     **/
    public function updateAll($fieldname, $fieldvalue, $criteria = null)
    {
        $set_clause = is_numeric($fieldvalue) ? $fieldname.' = '.$fieldvalue : $fieldname.' = '.$this->db->quoteString($fieldvalue);
        $sql = 'UPDATE '.$this->db->prefix('users').' SET '.$set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }
}
