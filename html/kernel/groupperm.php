<?php
// $Id: groupperm.php,v 1.7 2008/11/02 11:51:05 minahito Exp $
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

define("GROUPPERM_VAL_MODREAD",   "module_read");
define("GROUPPERM_VAL_MODADMIN",  "module_admin");
define("GROUPPERM_VAL_BLOCKREAD", "block_read");

/**
 * 
 * 
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A group permission
 * 
 * These permissions are managed through a {@link XoopsGroupPermHandler} object
 * 
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsGroupPerm extends XoopsObject
{

    /**
     * Constructor
     * 
     */
    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        $this->XoopsObject();
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
        
        if ($this->get('gperm_modid') != 1) {
            $module =& $mHandler->get($this->get('gperm_modid'));
            if (!is_object($module)) {
                return false;
            }
        }
        
        if ($this->get('gperm_name') == GROUPPERM_VAL_MODREAD
            || $this->get('gperm_name') == GROUPPERM_VAL_MODADMIN) {
            $mHandler = xoops_gethandler('module');
            $module =& $mHandler->get($this->get('gperm_itemid'));
            if (!is_object($module)) {
                return false;
            }
        } elseif ($this->get('gperm_name') == GROUPPERM_VAL_BLOCKREAD) {
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
     * @return	bool    $isNew  Flag the object as "new"?
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
                if ($numrows == 1) {
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
        if (strtolower(get_class($perm)) != 'xoopsgroupperm') {
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
        if (strtolower(get_class($perm)) != 'xoopsgroupperm') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE gperm_id = %u", $this->db->prefix('group_permission'), $perm->getVar('gperm_id'));
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
        $ret = array();
        $limit = $start = 0;
        $db = &$this->db;
        $sql = 'SELECT * FROM '.$db->prefix('group_permission');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
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
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
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
        $sql = sprintf("DELETE FROM %s", $this->db->prefix('group_permission'));
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
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
        if ($count == 1) {
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
        $ret = array();

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
        $ret = array();

        $criteria =& $this->getCriteria($gperm_name, $gperm_itemid, array(), $gperm_modid);

        $perms =& $this->getObjects($criteria, true);
        foreach (array_keys($perms) as $i) {
            $ret[] = $perms[$i]->getVar('gperm_groupid');
        }
        return $ret;
    }

    /**
     * Generate a criteria from given params
     * 
     * @param	string  $gperm_name       Name of permission
     * @param	int     $gperm_itemid     ID of an item
     * @param	int     $gperm_groupid    ID of a group
     * @param	int     $gperm_modid      ID of a module
     *
     * @return	CriteiaCompo
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
