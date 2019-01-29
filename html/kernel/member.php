<?php
// $Id: member.php,v 1.1 2007/05/15 02:34:37 minahito Exp $
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
require_once XOOPS_ROOT_PATH.'/kernel/user.php';
require_once XOOPS_ROOT_PATH.'/kernel/group.php';

/**
* XOOPS member handler class.
* This class provides simple interface (a facade class) for handling groups/users/
* membership data.
*
*
* @author  Kazumi Ono <onokazu@xoops.org>
* @copyright copyright (c) 2000-2003 XOOPS.org
* @package kernel
*/

class XoopsMemberHandler
{

    /**#@+
    * holds reference to group handler(DAO) class
    * @access private
    */
    public $_gHandler;

    /**
    * holds reference to user handler(DAO) class
    */
    public $_uHandler;

    /**
    * holds reference to membership handler(DAO) class
    */
    public $_mHandler;

    /**
    * holds temporary user objects
    */
    public $_members = array();
    /**#@-*/

    /**
     * constructor
     *
     */
    public function __construct(&$db)
    {
        $this->_gHandler =new XoopsGroupHandler($db);
        $this->_uHandler =new XoopsUserHandler($db);
        $this->_mHandler =new XoopsMembershipHandler($db);
    }
    public function XoopsMemberHandler(&$db)
    {
        return self::__construct($db);
    }

    /**
     * create a new group
     *
     * @return object XoopsGroup reference to the new group
     */
    public function &createGroup()
    {
        $ret =& $this->_gHandler->create();
        return $ret;
    }

    /**
     * create a new user
     *
     * @return object XoopsUser reference to the new user
     */
    public function &createUser()
    {
        $ret =& $this->_uHandler->create();
        return $ret;
    }

    /**
     * retrieve a group
     *
     * @param int $id ID for the group
     * @return object XoopsGroup reference to the group
     */
    public function &getGroup($id)
    {
        $ret =& $this->_gHandler->get($id);
        return $ret;
    }

    /**
     * retrieve a user
     *
     * @param int $id ID for the user
     * @return object XoopsUser reference to the user
     */
    public function &getUser($id)
    {
        if (!isset($this->_members[$id])) {
            $this->_members[$id] =& $this->_uHandler->get($id);
        }
        return $this->_members[$id];
    }
    
    public function &getUserByEmail($email)
    {
        $returnUser = null;

        $myts =& MyTextSanitizer::sGetInstance();    ///< @todo not depends
        $users =& $this->getUsers(new Criteria('email', $myts->addSlashes($email)));

        if (!is_array($users)) {
            $returnUser=null;
        } elseif (count($users) > 0) {
            $returnUser = is_object($users[0]) ? $users[0] : null;
        }
        
        return $returnUser;
    }

    /**
     * Delete function. This function is virtual overload function.
     * @param $object XoopsUser or XoopsGroup
     */
    public function delete(&$object)
    {
        if (is_a($object, "XoopsUser")) {
            return $this->deleteUser($object);
        } elseif (is_a($object, "XoopsGroup")) {
            return $this->deleteGroup($object);
        }
    }

    /**
     * delete a group
     *
     * @param object $group reference to the group to delete
     * @return bool FALSE if failed
     */
    public function deleteGroup(&$group)
    {
        $this->_gHandler->delete($group);
        $this->_mHandler->deleteAll(new Criteria('groupid', $group->getVar('groupid')));
        return true;
    }

    /**
     * delete a user
     *
     * @param object $user reference to the user to delete
     * @return bool FALSE if failed
     */
    public function deleteUser(&$user)
    {
        $this->_uHandler->delete($user);
        $this->_mHandler->deleteAll(new Criteria('uid', $user->getVar('uid')));
        return true;
    }

    /**
     * insert a group into the database
     *
     * @param object $group reference to the group to insert
     * @return bool TRUE if already in database and unchanged
     * FALSE on failure
     */
    public function insertGroup(&$group)
    {
        return $this->_gHandler->insert($group);
    }

    /**
     * insert a user into the database
     *
     * @param object $user reference to the user to insert
     * @return bool TRUE if already in database and unchanged
     * FALSE on failure
     */
    public function insertUser(&$user, $force = false)
    {
        return $this->_uHandler->insert($user, $force);
    }

    /**
     * retrieve groups from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the group's ID as key for the array?
     * @return array array of {@link XoopsGroup} objects
     */
    public function &getGroups($criteria = null, $id_as_key = false)
    {
        $groups =& $this->_gHandler->getObjects($criteria, $id_as_key);
        return $groups;
    }

    /**
     * retrieve users from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the group's ID as key for the array?
     * @return array array of {@link XoopsUser} objects
     */
    public function &getUsers($criteria = null, $id_as_key = false)
    {
        $users =& $this->_uHandler->getObjects($criteria, $id_as_key);
        return $users;
    }

    /**
     * get a list of groupnames and their IDs
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return array associative array of group-IDs and names
     */
    public function &getGroupList($criteria = null)
    {
        $groups =& $this->_gHandler->getObjects($criteria, true);
        $ret = array();
        foreach (array_keys($groups) as $i) {
            $ret[$i] = $groups[$i]->getVar('name');
        }
        return $ret;
    }

    /**
     * get a list of usernames and their IDs
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return array associative array of user-IDs and names
     */
    public function getUserList($criteria = null)
    {
        return $this->_uHandler->getUnames($criteria, true);
    }

    /**
     * add a user to a group
     *
     * @param int $group_id ID of the group
     * @param int $user_id ID of the user
     * @return object XoopsMembership
     */
    public function addUserToGroup($group_id, $user_id)
    {
        $group_ids =& $this->getGroupsByUser($user_id);
        if (!in_array($group_id, $group_ids)) {
            $mship =& $this->_mHandler->create();
            $mship->setVar('groupid', $group_id);
            $mship->setVar('uid', $user_id);
            return $this->_mHandler->insert($mship);
        }

        return true;
    }

    
    /**
     * remove a one user from a group.
     * @param int $group_id ID of the group
     * @param int $user_id ID of the user
     * @return bool success?
     */
    public function removeUserFromGroup($group_id, $user_id)
    {
        $user_ids = array($user_id);
        return $this->removeUsersFromGroup($group_id, $user_ids);
    }

    /**
     * remove a list of users from a group
     *
     * @param int $group_id ID of the group
     * @param array $user_ids array of user-IDs
     * @return bool success?
     */
    public function removeUsersFromGroup($group_id, $user_ids = array())
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('groupid', $group_id));
        $criteria2 = new CriteriaCompo();
        foreach ($user_ids as $uid) {
            $criteria2->add(new Criteria('uid', $uid), 'OR');
        }
        $criteria->add($criteria2);
        return $this->_mHandler->deleteAll($criteria);
    }

    /**
     * get a list of users belonging to a group
     *
     * @param int $group_id ID of the group
     * @param bool $asobject return the users as objects?
     * @param int $limit number of users to return
     * @param int $start index of the first user to return
     * @return array Array of {@link XoopsUser} objects (if $asobject is TRUE)
     * or of associative arrays matching the record structure in the database.
     */
    public function &getUsersByGroup($group_id, $asobject = false, $limit = 0, $start = 0)
    {
        $user_ids =& $this->_mHandler->getUsersByGroup($group_id, $limit, $start);
        if (!$asobject) {
            return $user_ids;
        } else {
            $ret = array();
            foreach ($user_ids as $u_id) {
                $user =& $this->getUser($u_id);
                if (is_object($user)) {
                    $ret[] =& $user;
                }
                unset($user);
            }
            return $ret;
        }
    }

    /**
     * @see getUsersByGroup
     */
    public function &getUsersByNoGroup($group_id, $asobject = false, $limit = 0, $start = 0)
    {
        $user_ids =& $this->_mHandler->getUsersByNoGroup($group_id, $limit, $start);
        if (!$asobject) {
            return $user_ids;
        } else {
            $ret = array();
            foreach ($user_ids as $u_id) {
                $user =& $this->getUser($u_id);
                if (is_object($user)) {
                    $ret[] =& $user;
                }
                unset($user);
            }
            return $ret;
        }
    }

    /**
     * get a list of groups that a user is member of
     *
     * @param int $user_id ID of the user
     * @param bool $asobject return groups as {@link XoopsGroup} objects or arrays?
     * @return array array of objects or arrays
     */
    public function &getGroupsByUser($user_id, $asobject = false)
    {
        $group_ids =& $this->_mHandler->getGroupsByUser($user_id);
        if (!$asobject) {
            return $group_ids;
        } else {
            $ret = array();
            foreach ($group_ids as $g_id) {
                $ret[] =& $this->getGroup($g_id);
            }
            return $ret;
        }
    }

    /**
     * log in a user
     *
     * @param string $uname username as entered in the login form
     * @param string $pwd password entered in the login form
     * @return object XoopsUser reference to the logged in user. FALSE if failed to log in
     */
    public function &loginUser($uname, $pwd)
    {
        $criteria = new CriteriaCompo(new Criteria('uname', $uname));
        if (is_callable('User_Utils::passwordVerify')) {
            $user = $this->_uHandler->getObjects($criteria, false);
            if ($user && count($user) === 1) {
                if (!User_Utils::passwordVerify($pwd, $user[0]->get('pass'))) {
                    $user = array();
                }
            } else {
                $user = array();
            }
        } else {
            if (is_callable('User_Utils::encryptPassword')) {
                $criteria->add(new Criteria('pass', User_Utils::encryptPassword($pwd)));
            } else {
                $criteria->add(new Criteria('pass', md5($pwd)));
            }
            $user = $this->_uHandler->getObjects($criteria, false);
        }
        if (!$user || count($user) != 1) {
            $ret = false;
            return $ret;
        }
        return $user[0];
    }

    /**
     * logs in a user with an md5 encrypted password
     *
     * @param string $uname username
     * @param string $md5pwd password encrypted with md5
     * @return object XoopsUser reference to the logged in user. FALSE if failed to log in
     */
    public function &loginUserMd5($uname, $md5pwd)
    {
        $criteria = new CriteriaCompo(new Criteria('uname', $uname));
        $criteria->add(new Criteria('pass', $md5pwd));
        $user =& $this->_uHandler->getObjects($criteria, false);
        if (!$user || count($user) != 1) {
            $ret = false;
            return $ret;
        }
        return $user[0];
    }

    /**
     * count users matching certain conditions
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return int
     */
    public function getUserCount($criteria = null)
    {
        return $this->_uHandler->getCount($criteria);
    }

    /**
     * count users belonging to a group
     *
     * @param int $group_id ID of the group
     * @return int
     */
    public function getUserCountByGroup($group_id)
    {
        return $this->_mHandler->getCount(new Criteria('groupid', $group_id));
    }

    /**
     * @see getUserCountByGroup
     * @return int
     */
    public function getUserCountByNoGroup($group_id)
    {
        $groupid = (int)$group_id;
        $usersTable = $this->_mHandler->db->prefix('users');
        $linkTable = $this->_mHandler->db->prefix('groups_users_link');

        $sql = "SELECT count(*) FROM ${usersTable} u LEFT JOIN ${linkTable} g ON u.uid=g.uid," .
                "${usersTable} u2 LEFT JOIN ${linkTable} g2 ON u2.uid=g2.uid AND g2.groupid=${groupid} " .
                "WHERE (g.groupid != ${groupid} OR g.groupid IS NULL) " .
                "AND (g2.groupid = ${groupid} OR g2.groupid IS NULL) " .
                "AND u.uid = u2.uid AND g2.uid IS NULL GROUP BY u.uid";

        $result = $this->_mHandler->db->query($sql);
        if (!$result) {
            return 0;
        }

        $count = $this->_mHandler->db->getRowsNum($result);

        return $count;
    }

    /**
     * updates a single field in a users record
     *
     * @param object $user reference to the {@link XoopsUser} object
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    public function updateUserByField(&$user, $fieldName, $fieldValue)
    {
        $user->setVar($fieldName, $fieldValue);
        return $this->insertUser($user);
    }

    /**
     * updates a single field in a users record
     *
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @param object $criteria {@link CriteriaElement} object
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    public function updateUsersByField($fieldName, $fieldValue, $criteria = null)
    {
        return $this->_uHandler->updateAll($fieldName, $fieldValue, $criteria);
    }

    /**
     * activate a user
     *
     * @param object $user reference to the {@link XoopsUser} object
     * @return bool successful?
     */
    public function activateUser(&$user)
    {
        if ($user->getVar('level') != 0) {
            return true;
        }
        $user->setVar('level', 1);
        return $this->_uHandler->insert($user, true);
    }
}
