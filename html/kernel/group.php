<?php
/**
 * group of users
 * @package    kernel
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @author Kazumi Ono <onokazu@xoops.org>
 * @package kernel
 */
class XoopsGroup extends XoopsObject
{

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        parent::__construct();
        $this->initVar('groupid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('group_type', XOBJ_DTYPE_OTHER, null, false);
        $initVars = $this->vars;
    }
    public function XoopsGroup()
    {
        return self::__construct();
    }
}


/**
* XOOPS group handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS group class objects.
*
* @author Kazumi Ono <onokazu@xoops.org>
* @copyright copyright (c) 2000-2003 XOOPS.org
* @package kernel
* @subpackage member
*/
class XoopsGroupHandler extends XoopsObjectHandler
{

    /**
     * create a new {@link XoopsGroup} object
     *
     * @param bool $isNew mark the new object as "new"?
     * @return object XoopsGroup reference to the new object
     *
     */
    public function &create($isNew = true)
    {
        $group =new XoopsGroup();
        if ($isNew) {
            $group->setNew();
        }

        $group->setVar('group_type', 'User');

        return $group;
    }

    /**
     * retrieve a specific group
     *
     * @param int $id ID of the group to get
     * @return object XoopsGroup reference to the group object, FALSE if failed
     */
    public function &get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $db = &$this->db;
            $sql = 'SELECT * FROM '.$db->prefix('groups').' WHERE groupid='.$id;
            if ($result = $db->query($sql)) {
                $numrows = $db->getRowsNum($result);
                if (1 == $numrows) {
                    $group = new XoopsGroup();
                    $group->assignVars($db->fetchArray($result));
                    $ret =& $group;
                }
            }
        }
        return $ret;
    }

    /**
     * insert a group into the database
     *
     * @param object reference to the group object
     * @return mixed ID of the group if inserted, FALSE if failed, TRUE if already present and unchanged.
     */
    public function insert(&$group)
    {
        $name = null;
        $description = null;
        $group_type = null;
        $groupid = null;
        if ('xoopsgroup' != strtolower(get_class($group))) {
            return false;
        }
        if (!$group->isDirty()) {
            return true;
        }
        if (!$group->cleanVars()) {
            return false;
        }
        foreach ($group->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $db = &$this->db;
        if ($group->isNew()) {
            $groupid = $db->genId('group_groupid_seq');
            $sql = sprintf('INSERT INTO %s (groupid, name, description, group_type) VALUES (%u, %s, %s, %s)', $db->prefix('groups'), $groupid, $db->quoteString($name), $db->quoteString($description), $db->quoteString($group_type));
        } else {
            $sql = sprintf('UPDATE %s SET name = %s, description = %s, group_type = %s WHERE groupid = %u', $db->prefix('groups'), $db->quoteString($name), $db->quoteString($description), $db->quoteString($group_type), $groupid);
        }
        if (!$result = $db->query($sql)) {
            return false;
        }
        if (empty($groupid)) {
            $groupid = $db->getInsertId();
        }
        $group->assignVar('groupid', $groupid);
        return true;
    }

    /**
     * remove a group from the database
     *
     * @param object $group reference to the group to be removed
     * @return bool FALSE if failed
     */
    public function delete(&$group)
    {
        if ('xoopsgroup' != strtolower(get_class($group))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE groupid = %u', $this->db->prefix('groups'), $group->getVar('groupid'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * retrieve groups from the database
     *
     * @param object $criteria {@link CriteriaElement} with conditions for the groups
     * @param bool $id_as_key should the groups' IDs be used as keys for the associative array?
     * @return mixed Array of groups
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $db = &$this->db;
        $sql = 'SELECT * FROM '.$db->prefix('groups');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $db->fetchArray($result)) {
            $group =new XoopsGroup();
            $group->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $group;
            } else {
                $ret[$myrow['groupid']] =& $group;
            }
            unset($group);
        }
        return $ret;
    }
}

/**
 * membership of a user in a group
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class XoopsMembership extends XoopsObject
{
    /**
     * constructor
     */
    public function __construct()
    {
        //parent::__construct();

        $this->initVar('linkid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('groupid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
    }
}

/**
* XOOPS membership handler class. (Singleton)
*
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS group membership class objects.
*
* @author Kazumi Ono <onokazu@xoops.org>
* @copyright copyright (c) 2000-2003 XOOPS.org
* @package kernel
*/
class XoopsMembershipHandler extends XoopsObjectHandler
{

    /**
     * create a new membership
     *
     * @param bool $isNew should the new object be set to "new"?
     * @return object XoopsMembership
     */
    public function &create($isNew = true)
    {
        $mship =new XoopsMembership();
        if ($isNew) {
            $mship->setNew();
        }
        return $mship;
    }

    /**
     * retrieve a membership
     *
     * @param int $id ID of the membership to get
     * @return mixed reference to the object if successful, else FALSE
     */
    public function &get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $db = &$this->db;
            $sql = 'SELECT * FROM '.$db->prefix('groups_users_link').' WHERE linkid='.$id;
            if ($result = $db->query($sql)) {
                $numrows = $db->getRowsNum($result);
                if (1 == $numrows) {
                    $mship =new XoopsMembership();
                    $mship->assignVars($db->fetchArray($result));
                    $ret =& $mship;
                }
            }
        }
        return $ret;
    }

    /**
     * inserts a membership in the database
     *
     * @param object $mship reference to the membership object
     * @return bool TRUE if already in DB or successful, FALSE if failed
     */
    public function insert(&$mship)
    {
        $groupid = null;
        $uid = null;
        $linkid = null;
        if ('xoopsmembership' != strtolower(get_class($mship))) {
            return false;
        }
        if (!$mship->isDirty()) {
            return true;
        }
        if (!$mship->cleanVars()) {
            return false;
        }
        foreach ($mship->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $db = &$this->db;
        if ($mship->isNew()) {
            $linkid = $db->genId('groups_users_link_linkid_seq');
            $sql = sprintf('INSERT INTO %s (linkid, groupid, uid) VALUES (%u, %u, %u)', $db->prefix('groups_users_link'), $linkid, $groupid, $uid);
        } else {
            $sql = sprintf('UPDATE %s SET groupid = %u, uid = %u WHERE linkid = %u', $db->prefix('groups_users_link'), $groupid, $uid, $linkid);
        }
        if (!$result = $db->query($sql)) {
            return false;
        }
        if (empty($linkid)) {
            $linkid = $this->db->getInsertId();
        }
        $mship->assignVar('linkid', $linkid);
        return true;
    }

    /**
     * delete a membership from the database
     *
     * @param object $mship reference to the membership object
     * @return bool FALSE if failed
     */
    public function delete(&$mship)
    {
        $groupm = null;
        if ('xoopsmembership' != strtolower(get_class($mship))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE linkid = %u', $this->db->prefix('groups_users_link'), $groupm->getVar('linkid'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * retrieve memberships from the database
     *
     * @param object $criteria {@link CriteriaElement} conditions to meet
     * @param bool $id_as_key should the ID be used as the array's key?
     * @return array array of references
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $db = &$this->db;
        $sql = 'SELECT * FROM '.$db->prefix('groups_users_link');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $db->fetchArray($result)) {
            $mship = new XoopsMembership();
            $mship->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $mship;
            } else {
                $ret[$myrow['linkid']] =& $mship;
            }
            unset($mship);
        }
        return $ret;
    }

    /**
     * count how many memberships meet the conditions
     *
     * @param object $criteria {@link CriteriaElement} conditions to meet
     * @return int
     */
    public function getCount($criteria = null)
    {
        $db = &$this->db;
        $sql = 'SELECT COUNT(*) FROM '.$db->prefix('groups_users_link');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        $result = $db->query($sql);
        if (!$result) {
            return 0;
        }
        [$count] = $db->fetchRow($result);
        return $count;
    }

    /**
     * delete all memberships meeting the conditions
     *
     * @param object $criteria {@link CriteriaElement} with conditions to meet
     * @return bool
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM '.$this->db->prefix('groups_users_link');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * retrieve groups for a user
     *
     * @param int $uid ID of the user
     * @return array array of groups the user belongs to
     */
    public function &getGroupsByUser($uid)
    {
        $ret = [];
        $db = &$this->db;
        $sql = 'SELECT groupid FROM '.$db->prefix('groups_users_link').' WHERE uid='.(int)$uid;
        $result = $db->query($sql);
        if (!$result) {
            return $ret;
        }
        while ([$groupid] = $db->fetchRow($result)) {
            $ret[] = $groupid;
        }
        return $ret;
    }

    /**
     * retrieve users belonging to a group
     *
     * @param int $groupid ID of the group
     * @param int $limit   number of entries to return
     * @param int $start   offset of first entry to return
     * @return array array of users belonging to the group
     */
    public function &getUsersByGroup($groupid, $limit=0, $start=0)
    {
        $ret = [];
        $db = &$this->db;
        $sql = 'SELECT uid FROM ' . $db->prefix('groups_users_link') . ' WHERE groupid='.(int)$groupid;

        $result = $db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ([$uid] = $db->fetchRow($result)) {
            $ret[] = $uid;
        }
        return $ret;
    }

    /**
     * @param     $groupid
     * @param int $limit
     * @param int $start
     * @return array
     * @see getUsersByGroup
     */
    public function &getUsersByNoGroup($groupid, $limit=0, $start=0)
    {
        $ret = [];

        $groupid = (int)$groupid;
        $db = &$this->db;
        $usersTable = $db->prefix('users');
        $linkTable = $db->prefix('groups_users_link');

        $sql = "SELECT u.uid FROM {$usersTable} u LEFT JOIN {$linkTable} g ON u.uid=g.uid," .
                "{$usersTable} u2 LEFT JOIN {$linkTable} g2 ON u2.uid=g2.uid AND g2.groupid={$groupid} " .
                "WHERE (g.groupid != {$groupid} OR g.groupid IS NULL) " .
                "AND (g2.groupid = {$groupid} OR g2.groupid IS NULL) " . 'AND u.uid = u2.uid AND g2.uid IS NULL GROUP BY u.uid';

        $result = $db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ([$uid] = $db->fetchRow($result)) {
            $ret[] = $uid;
        }
        return $ret;
    }
}
