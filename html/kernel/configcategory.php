<?php
// $Id: configcategory.php,v 1.1 2007/05/15 02:34:37 minahito Exp $
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
 * 
 * 
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */


/**
 * A category of configs
 * 
 * @author	Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 * 
 * @package     kernel
 */
class XoopsConfigCategory extends XoopsObject
{
    /**
     * Constructor
     * 
     */
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('confcat_id', XOBJ_DTYPE_INT, null);
        $this->initVar('confcat_name', XOBJ_DTYPE_OTHER, null);
        $this->initVar('confcat_order', XOBJ_DTYPE_INT, 0);
    }
    public function XoopsConfigCategory()
    {
        return self::__construct();
    }

    /**
     * Get a constract of name
     */
    public function getName()
    {
        return defined($this->get('confcat_name')) ? constant($this->get('confcat_name')) : $this->get('confcat_name');
    }
}


/**
 * XOOPS configuration category handler class.  
 * 
 * This class is responsible for providing data access mechanisms to the data source 
 * of XOOPS configuration category class objects.
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 * 
 * @package     kernel
 * @subpackage  config
 */
class XoopsConfigCategoryHandler extends XoopsObjectHandler
{

    /**
     * Create a new category
     * 
     * @param	bool    $isNew  Flag the new object as "new"?
     * 
     * @return	object  New {@link XoopsConfigCategory} 
     */
    public function &create($isNew = true)
    {
        $confcat =new XoopsConfigCategory();
        if ($isNew) {
            $confcat->setNew();
        }
        return $confcat;
    }

    /**
     * Retrieve a {@link XoopsConfigCategory} 
     * 
     * @param	int $id ID
     * 
     * @return	object  {@link XoopsConfigCategory}, FALSE on fail
     */
    public function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('configcategory').' WHERE confcat_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                    $confcat =new XoopsConfigCategory();
                    $confcat->assignVars($this->db->fetchArray($result), false);
                    $ret =& $confcat;
                }
            }
        }
        return $ret;
    }

    /**
     * Store a {@link XoopsConfigCategory}
     * 
     * @param	object   &$confcat  {@link XoopsConfigCategory}
     * 
     * @return	bool    TRUE on success
     */
    public function insert(&$confcat)
    {
        if (strtolower(get_class($confcat)) != 'xoopsconfigcategory') {
            return false;
        }
        if (!$confcat->isDirty()) {
            return true;
        }
        if (!$confcat->cleanVars()) {
            return false;
        }
        foreach ($confcat->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($confcat->isNew()) {
            $confcat_id = $this->db->genId('configcategory_confcat_id_seq');
            $sql = sprintf("INSERT INTO %s (confcat_id, confcat_name, confcat_order) VALUES (%u, %s, %u)", $this->db->prefix('configcategory'), $confcat_id, $this->db->quoteString($confcat_name), $confcat_order);
        } else {
            $sql = sprintf("UPDATE %s SET confcat_name = %s, confcat_order = %u WHERE confcat_id = %u", $this->db->prefix('configcategory'), $this->db->quoteString($confcat_name), $confcat_order, $confcat_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($confcat_id)) {
            $confcat_id = $this->db->getInsertId();
        }
        $confcat->assignVar('confcat_id', $confcat_id);
        return $confcat_id;
    }

    /**
     * Delelete a {@link XoopsConfigCategory}
     * 
     * @param	object  &$confcat   {@link XoopsConfigCategory}
     * 
     * @return	bool    TRUE on success
     */
    public function delete(&$confcat)
    {
        if (strtolower(get_class($confcat)) != 'xoopsconfigcategory') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE confcat_id = %u", $this->db->prefix('configcategory'), $confcat->getVar('confcat_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Get some {@link XoopsConfigCategory}s
     * 
     * @param	object  $criteria   {@link CriteriaElement} 
     * @param	bool    $id_as_key  Use the IDs as keys to the array?
     * 
     * @return	array   Array of {@link XoopsConfigCategory}s
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('configcategory');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            $sort = !in_array($criteria->getSort(), array('confcat_id', 'confcat_name', 'confcat_order')) ? 'confcat_order' : $criteria->getSort();
            $sql .= ' ORDER BY '.$sort.' '.$criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $confcat =new XoopsConfigCategory();
            $confcat->assignVars($myrow, false);
            if (!$id_as_key) {
                $ret[] =& $confcat;
            } else {
                $ret[$myrow['confcat_id']] =& $confcat;
            }
            unset($confcat);
        }
        return $ret;
    }
}
