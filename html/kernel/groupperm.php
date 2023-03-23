<?php
/**
 * Group permission
 * These permissions are managed through a {@link XoopsGroupPermHandler} object
 * @package    kernel
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2008/11/02
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

define('GROUPPERM_VAL_MODREAD', 'module_read');
define('GROUPPERM_VAL_MODADMIN', 'module_admin');
define('GROUPPERM_VAL_BLOCKREAD', 'block_read');


/**
 * @package     kernel
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsGroupPerm extends XoopsObject
{

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        parent::__construct();
        $this->initVar('gperm_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('gperm_groupid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('gperm_itemid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('gperm_modid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('gperm_name', XOBJ_DTYPE_OTHER, null, false);
        $initVars = $this->vars;
    }
    public function XoopsGroupPerm()
    {
        return self::__construct();
    }

    public function cleanVars()
    {
        if (!parent::cleanVars()) {
            return false;
        }

        // The following validation code doesn't have this class,
        // because the validation code accesses handlers.
        // But, this follows traditional architecture of XOOPS2.

        $gHandler = xoops_gethandler('group');
        $group =& $gHandler->get($this->get('gperm_groupid'));
        if (!is_object($group)) {
            return false;
        }

        $mHandler = xoops_gethandler('module');

        if (1 != $this->get('gperm_modid')) {
            $module =& $mHandler->get($this->get('gperm_modid'));
            if (!is_object($module)) {
                return false;
            }
        }

        if (GROUPPERM_VAL_MODREAD == $this->get('gperm_name')
            || GROUPPERM_VAL_MODADMIN == $this->get('gperm_name')) {
            $mHandler = xoops_gethandler('module');
            $module =& $mHandler->get($this->get('gperm_itemid'));
            if (!is_object($module)) {
                return false;
            }
        } elseif (GROUPPERM_VAL_BLOCKREAD == $this->get('gperm_name')) {
            $bHandler = xoops_gethandler('block');
            $block =& $bHandler->get($this->get('gperm_itemid'));
            if (!is_object($block)) {
                return false;
            }
        }

        return true;
    }
}


/**
* XOOPS group permission handler class.
*
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS group permission class objects.
* This class is an abstract class to be implemented by child group permission classes.
*
* @see          XoopsGroupPerm
* @author       Kazumi Ono  <onokazu@xoops.org>
* @copyright	copyright (c) 2000-2003 XOOPS.org
*/
class XoopsGroupPermHandler extends XoopsObjectHandler
{

    /**
     * Create a new {@link XoopsGroupPerm}
     *
     * @param bool $isNew
     * @return \XoopsGroupPerm $isNew  Flag the object as "new"?
     */
    public function &create($isNew = true)
    {
        $perm =new XoopsGroupPerm();
        if ($isNew) {
            $perm->setNew();
        }
        return $perm;
    }

    /**
     * Retrieve a group permission
     *
     * @param	int $id ID
     *
     * @return	object  {@link XoopsGroupPerm}, FALSE on fail
     */
    public function &get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $db = &$this->db;
            $sql = sprintf('SELECT * FROM %s WHERE gperm_id = %u', $db->prefix('group_permission'), $id);
            if ($result = $db->query($sql)) {
                $numrows = $db->getRowsNum($result);
                if (1 == $numrows) {
                    $perm =new XoopsGroupPerm();
                    $perm->assignVars($db->fetchArray($result));
                    $ret =& $perm;
                }
            }
        }
        return $ret;
    }

    /**
     * Store a {@link XoopsGroupPerm}
     *
     * @param	object  &$perm  {@link XoopsGroupPerm} object
     *
     * @return	bool    TRUE on success
     */
    public function insert(&$perm)
    {
        if ('xoopsgroupperm' != strtolower(get_class($perm))) {
            return false;
        }
        if (!$perm->isDirty()) {
            return true;
        }
        if (!$perm->cleanVars()) {
            return false;
        }

        foreach ($perm->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $db = &$this->db;
        if ($perm->isNew()) {
            $gperm_id = $db->genId('group_permission_gperm_id_seq');
            $sql = sprintf('INSERT INTO %s (gperm_id, gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (%u, %u, %u, %u, %s)', $db->prefix('group_permission'), $gperm_id, $gperm_groupid, $gperm_itemid, $gperm_modid, $db->quoteString($gperm_name));
        } else {
            $sql = sprintf('UPDATE %s SET gperm_groupid = %u, gperm_itemid = %u, gperm_modid = %u WHERE gperm_id = %u', $db->prefix('group_permission'), $gperm_groupid, $gperm_itemid, $gperm_modid, $gperm_id);
        }
        if (!$result = $db->query($sql)) {
            return false;
        }
        if (empty($gperm_id)) {
            $gperm_id = $this->db->getInsertId();
        }
        $perm->assignVar('gperm_id', $gperm_id);
        return true;
    }

    /**
     * Delete a {@link XoopsGroupPerm}
     *
     * @param	object  &$perm
     *
     * @return	bool    TRUE on success
     */
    public function delete(&$perm)
    {
        if ('xoopsgroupperm' != strtolower(get_class($perm))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE gperm_id = %u', $this->db->prefix('group_permission'), $perm->getVar('gperm_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve multiple {@link XoopsGroupPerm}s
     *
     * @param	object  $criteria   {@link CriteriaElement}
     * @param	bool    $id_as_key  Use IDs as array keys?
     *
     * @return	array   Array of {@link XoopsGroupPerm}s
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $db = &$this->db;
        $sql = 'SELECT * FROM '.$db->prefix('group_permission');
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
            $perm =new XoopsGroupPerm();
            $perm->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $perm;
            } else {
                $ret[$myrow['gperm_id']] =& $perm;
            }
            unset($perm);
        }
        return $ret;
    }

    /**
     * Count some {@link XoopsGroupPerm}s
     *
     * @param	object  $criteria   {@link CriteriaElement}
     *
     * @return	int
     */
    public function getCount($criteria = null)
    {
        $db = &$this->db;
        $sql = 'SELECT COUNT(*) FROM '.$db->prefix('group_permission');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        $result = $db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $db->fetchRow($result);
        return $count;
    }

    /**
     * Delete all permissions by a certain criteria
     *
     * @param	object  $criteria   {@link CriteriaElement}
     *
     * @return	bool    TRUE on success
     */
    public function deleteAll($criteria = null)
    {
        $sql = sprintf('DELETE FROM %s', $this->db->prefix('group_permission'));
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Delete all module specific permissions assigned for a group
     *
     * @param	int  $gperm_groupid ID of a group
     * @param	int  $gperm_modid ID of a module
     *
     * @return	bool TRUE on success
     */
    public function deleteByGroup($gperm_groupid, $gperm_modid = null)
    {
        $criteria = new CriteriaCompo(new Criteria('gperm_groupid', (int)$gperm_groupid));
        if (isset($gperm_modid)) {
            $criteria->add(new Criteria('gperm_modid', (int)$gperm_modid));
        }
        return $this->deleteAll($criteria);
    }

    /**
     * Delete all module specific permissions
     *
     * @param	int  $gperm_modid ID of a module
     * @param	string  $gperm_name Name of a module permission
     * @param	int  $gperm_itemid ID of a module item
     *
     * @return	bool TRUE on success
     */
    public function deleteByModule($gperm_modid, $gperm_name = null, $gperm_itemid = null)
    {
        $criteria = new CriteriaCompo(new Criteria('gperm_modid', (int)$gperm_modid));
        if (isset($gperm_name)) {
            $criteria->add(new Criteria('gperm_name', $gperm_name));
            if (isset($gperm_itemid)) {
                $criteria->add(new Criteria('gperm_itemid', (int)$gperm_itemid));
            }
        }
        return $this->deleteAll($criteria);
    }
    /**#@-*/

    /**
     * Delete
     * @param $gperm_groupid
     */
    public function deleteBasicPermission($gperm_groupid)
    {
        $criteria = new CriteriaCompo(new Criteria('gperm_groupid', $gperm_groupid));
        $criteria->add(new Criteria('gperm_modid', 1));
        $criteria2 = new CriteriaCompo(new Criteria('gperm_name', 'system_admin'));
        $criteria2->add(new Criteria('gperm_name', 'module_admin'), 'OR');
        $criteria2->add(new Criteria('gperm_name', 'module_read'), 'OR');
        $criteria2->add(new Criteria('gperm_name', 'block_read'), 'OR');
        $criteria->add($criteria2);
        $this->deleteAll($criteria);
    }

    /**
     * Check permission
     *
     * @param	string    $gperm_name       Name of permission
     * @param	int       $gperm_itemid     ID of an item
     * @param	int/array $gperm_groupid    A group ID or an array of group IDs
     * @param	int       $gperm_modid      ID of a module
     * @param	bool      $bypass_admincheck Do not XOOPS_GROUP_ADMIN check if true.
     *
     * @return	bool    TRUE if permission is enabled
     */
    public function checkRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1, $bypass_admincheck = false)
    {
        if (empty($gperm_groupid)) {
            return false;
        }

        if (!$bypass_admincheck &&
            (is_array($gperm_groupid)?in_array(XOOPS_GROUP_ADMIN, $gperm_groupid):(XOOPS_GROUP_ADMIN == $gperm_groupid))) {
            return true;
        }

        $criteria =& $this->getCriteria($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid);
        if ($this->getCount($criteria) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Add a permission
     *
     * @param	string  $gperm_name       Name of permission
     * @param	int     $gperm_itemid     ID of an item
     * @param	int     $gperm_groupid    ID of a group
     * @param	int     $gperm_modid      ID of a module
     *
     * @return	bool    TRUE if success
     */
    public function addRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)
    {
        $criteria =& $this->getCriteria($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid);
        $count = $this->getCount($criteria);
        if (1 == $count) {
            return true;    // Only one record already exist. do nothing.
        } elseif ($count > 1) {
            // This case occurs when group_permission table exists from older versions of XOOPS.
            // So, once clear all and insert new record.
            $this->removeRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid);
        }

        $perm =& $this->create();
        $perm->setVar('gperm_name', $gperm_name);
        $perm->setVar('gperm_groupid', $gperm_groupid);
        $perm->setVar('gperm_itemid', $gperm_itemid);
        $perm->setVar('gperm_modid', $gperm_modid);
        return $this->insert($perm);
    }

    /**
     * Remove a permission
     *
     * @param	string  $gperm_name       Name of permission
     * @param	int     $gperm_itemid     ID of an item
     * @param	int     $gperm_groupid    ID of a group
     * @param	int     $gperm_modid      ID of a module
     *
     * @return	bool    TRUE jf success
     */
    public function removeRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)
    {
        $criteria =& $this->getCriteria($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid);
        return $this->deleteAll($criteria);
    }

    /**
     * Get all item IDs that a group is assigned a specific permission
     *
     * @param	string    $gperm_name       Name of permission
     * @param	int/array $gperm_groupid    A group ID or an array of group IDs
     * @param	int       $gperm_modid      ID of a module
     *
     * @return  array     array of item IDs
     */
    public function getItemIds($gperm_name, $gperm_groupid, $gperm_modid = 1)
    {
        $ret = [];

        $criteria =& $this->getCriteria($gperm_name, 0, $gperm_groupid, $gperm_modid);

        $perms =& $this->getObjects($criteria, true);
        foreach (array_keys($perms) as $i) {
            $ret[] = $perms[$i]->getVar('gperm_itemid');
        }
        return array_unique($ret);
    }

    /**
     * Get all group IDs assigned a specific permission for a particular item
     *
     * @param	string  $gperm_name       Name of permission
     * @param	int     $gperm_itemid     ID of an item
     * @param	int     $gperm_modid      ID of a module
     *
     * @return  array   array of group IDs
     */
    public function getGroupIds($gperm_name, $gperm_itemid, $gperm_modid = 1)
    {
        $ret = [];

        $criteria =& $this->getCriteria($gperm_name, $gperm_itemid, [], $gperm_modid);

        $perms =& $this->getObjects($criteria, true);
        foreach (array_keys($perms) as $i) {
            $ret[] = $perms[$i]->getVar('gperm_groupid');
        }
        return $ret;
    }

    /**
     * Generate a criteria from given params
     *
     * @param string $gperm_name    Name of permission
     * @param int    $gperm_itemid  ID of an item
     * @param int    $gperm_groupid ID of a group
     * @param int    $gperm_modid   ID of a module
     *
     * @return \CriteriaCompo
     */
    public function &getCriteria($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)
    {
        $criteria = new CriteriaCompo(new Criteria('gperm_modid', (int)$gperm_modid));
        $criteria->add(new Criteria('gperm_name', $gperm_name));
        $gperm_itemid = (int)$gperm_itemid;
        if ($gperm_itemid > 0) {
            $criteria->add(new Criteria('gperm_itemid', $gperm_itemid));
        }
        if (is_array($gperm_groupid)) {
            if (count($gperm_groupid) > 0) {
                $criteria2 = new CriteriaCompo();
                foreach ($gperm_groupid as $gid) {
                    $criteria2->add(new Criteria('gperm_groupid', (int)$gid), 'OR');
                }
                $criteria->add($criteria2);
            }
        } else {
            $criteria->add(new Criteria('gperm_groupid', (int)$gperm_groupid));
        }
        return $criteria;
    }
}
