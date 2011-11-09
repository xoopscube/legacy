<?php
// $Id: configoption.php,v 1.1 2007/05/15 02:34:38 minahito Exp $
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
 * A Config-Option
 * 
 * @author	Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 * 
 * @package     kernel
 */
class XoopsConfigOption extends XoopsObject
{
    /**
     * Constructor
     */
    function XoopsConfigOption()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        $this->XoopsObject();
        $this->initVar('confop_id', XOBJ_DTYPE_INT, null);
        $this->initVar('confop_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('confop_value', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('conf_id', XOBJ_DTYPE_INT, 0);
        $initVars = $this->vars;
    }

    /**
     * Get a constract of confop_value
     */
    function getOptionKey()
    {
		return defined($this->get('confop_value')) ? constant($this->get('confop_value')) : $this->get('confop_value');
	}
	
    /**
     * Get a constract of confop_name
     */
	function getOptionLabel()
	{
		return defined($this->get('confop_name')) ? constant($this->get('confop_name')) : $this->get('confop_name');
	}
	/**
	 * Compare with contents of $config object. If it's equal, return true.
	 * This member function doesn't use 'conf_id' & 'conf_order' to compare.
	 * 
	 * @param XoopsConfigItem $config
	 * @return bool
	 */
	function isEqual(&$option)
	{
		$flag = true;
		
		$flag &= ($this->get('confop_name') == $option->get('confop_name'));
		$flag &= ($this->get('confop_value') == $option->get('confop_value'));
		
		return $flag;
	}
}

/**
 * XOOPS configuration option handler class.  
 * This class is responsible for providing data access mechanisms to the data source 
 * of XOOPS configuration option class objects.
 *
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 * @author  Kazumi Ono <onokazu@xoops.org>
 * 
 * @package     kernel
 * @subpackage  config
*/
class XoopsConfigOptionHandler extends XoopsObjectHandler
{

    /**
     * Create a new option
     * 
     * @param	bool    $isNew  Flag the option as "new"?
     * 
     * @return	object  {@link XoopsConfigOption} 
     */
    function &create($isNew = true)
    {
        $confoption =new XoopsConfigOption();
        if ($isNew) {
            $confoption->setNew();
        }
        return $confoption;
    }

    /**
     * Get an option from the database
     * 
     * @param	int $id ID of the option
     * 
     * @return	object  reference to the {@link XoopsConfigOption}, FALSE on fail
     */
    function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('configoption').' WHERE confop_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                        $confoption =new XoopsConfigOption();
                    $confoption->assignVars($this->db->fetchArray($result));
                        $ret =& $confoption;
                }
            }
        }
        return $ret;
    }

    /**
     * Insert a new option in the database
     * 
     * @param	object  &$confoption    reference to a {@link XoopsConfigOption} 
     * @return	bool    TRUE if successfull.
     */
    function insert(&$confoption)
    {
        if (strtolower(get_class($confoption)) != 'xoopsconfigoption') {
            return false;
        }
        if (!$confoption->isDirty()) {
            return true;
        }
        if (!$confoption->cleanVars()) {
            return false;
        }
        foreach ($confoption->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($confoption->isNew()) {
            $confop_id = $this->db->genId('configoption_confop_id_seq');
            $sql = sprintf("INSERT INTO %s (confop_id, confop_name, confop_value, conf_id) VALUES (%u, %s, %s, %u)", $this->db->prefix('configoption'), $confop_id, $this->db->quoteString($confop_name), $this->db->quoteString($confop_value), $conf_id);
        } else {
            $sql = sprintf("UPDATE %s SET confop_name = %s, confop_value = %s WHERE confop_id = %u", $this->db->prefix('configoption'), $this->db->quoteString($confop_name), $this->db->quoteString($confop_value), $confop_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($confop_id)) {
            $confop_id = $this->db->getInsertId();
        }
        $confoption->assignVar('confop_id', $confop_id);
        return $confop_id;
    }

    /**
     * Delete an option
     * 
     * @param	object  &$confoption    reference to a {@link XoopsConfigOption} 
     * @return	bool    TRUE if successful
     */
    function delete(&$confoption)
    {
        if (strtolower(get_class($confoption)) != 'xoopsconfigoption') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE confop_id = %u", $this->db->prefix('configoption'), $confoption->getVar('confop_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Get some {@link XoopsConfigOption}s 
     * 
     * @param	object  $criteria   {@link CriteriaElement} 
     * @param	bool    $id_as_key  Use the IDs as array-keys?
     * 
     * @return	array   Array of {@link XoopsConfigOption}s 
     */
    function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('configoption');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere().' ORDER BY confop_id '.$criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $confoption =new XoopsConfigOption();
            $confoption->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $confoption;
            } else {
                $ret[$myrow['confop_id']] =& $confoption;
            }
            unset($confoption);
        }
        return $ret;
    }
}
?>
