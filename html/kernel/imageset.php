<?php
// $Id: imageset.php,v 1.1 2007/05/15 02:34:37 minahito Exp $
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

class XoopsImageset extends XoopsObject
{

	function XoopsImageset()
	{
		$this->XoopsObject();
		$this->initVar('imgset_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('imgset_name', XOBJ_DTYPE_TXTBOX, null, true, 50);
		$this->initVar('imgset_refid', XOBJ_DTYPE_INT, 0, false);
	}
}

/**
* XOOPS imageset handler class.  
* This class is responsible for providing data access mechanisms to the data source 
* of XOOPS imageset class objects.
*
*
* @author  Kazumi Ono <onokazu@xoops.org>
*/

class XoopsImagesetHandler extends XoopsObjectHandler
{

    function &create($isNew = true)
    {
        $imgset =new XoopsImageset();
        if ($isNew) {
            $imgset->setNew();
        }
        return $imgset;
    }

    function &get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('imgset').' WHERE imgset_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                        $imgset =new XoopsImageset();
                    $imgset->assignVars($this->db->fetchArray($result));
                        $ret =& $imgset;
                }
            }
        }
        return $ret;
    }

    function insert(&$imgset)
    {
        if (strtolower(get_class($imgset)) != 'xoopsimageset') {
            return false;
        }
        if (!$imgset->isDirty()) {
            return true;
        }
        if (!$imgset->cleanVars()) {
            return false;
        }
        foreach ($imgset->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($imgset->isNew()) {
            $imgset_id = $this->db->genId('imgset_imgset_id_seq');
            $sql = sprintf("INSERT INTO %s (imgset_id, imgset_name, imgset_refid) VALUES (%u, %s, %u)", $this->db->prefix('imgset'), $imgset_id, $this->db->quoteString($imgset_name), $imgset_refid);
        } else {
            $sql = sprintf("UPDATE %s SET imgset_name = %s, imgset_refid = %u WHERE imgset_id = %u", $this->db->prefix('imgset'), $this->db->quoteString($imgset_name), $imgset_refid, $imgset_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($imgset_id)) {
            $imgset_id = $this->db->getInsertId();
        }
		$imgset->assignVar('imgset_id', $imgset_id);
        return true;
    }

    function delete(&$imgset)
    {
        if (strtolower(get_class($imgset)) != 'xoopsimageset') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE imgset_id = %u", $this->db->prefix('imgset'), $imgset->getVar('imgset_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE imgset_id = %u", $this->db->prefix('imgset_tplset_link'), $imgset->getVar('imgset_id'));
        $this->db->query($sql);
        return true;
    }

    function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT i.* FROM '.$this->db->prefix('imgset'). ' i LEFT JOIN '.$this->db->prefix('imgset_tplset_link'). ' l ON l.imgset_id=i.imgset_id';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $imgset =new XoopsImageset();
            $imgset->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $imgset;
            } else {
                $ret[$myrow['imgset_id']] =& $imgset;
            }
            unset($imgset);
        }
        return $ret;
    }

    function linkThemeset($imgset_id, $tplset_name)
    {
        $imgset_id = (int)$imgset_id;
        $tplset_name = trim($tplset_name);
        if ($imgset_id <= 0 || $tplset_name == '') {
            return false;
        }
        if (!$this->unlinkThemeset($imgset_id, $tplset_name)) {
            return false;
        }
        $sql = sprintf("INSERT INTO %s (imgset_id, tplset_name) VALUES (%u, %s)", $this->db->prefix('imgset_tplset_link'), $imgset_id, $this->db->quoteString($tplset_name));
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        return true;
    }

    function unlinkThemeset($imgset_id, $tplset_name)
    {
        $imgset_id = (int)$imgset_id;
        $tplset_name = trim($tplset_name);
        if ($imgset_id <= 0 || $tplset_name == '') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE imgset_id = %u AND tplset_name = %s", $this->db->prefix('imgset_tplset_link'), $imgset_id, $this->db->quoteString($tplset_name));
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        return true;
    }

    function &getList($refid = null, $tplset = null)
    {
        $criteria = new CriteriaCompo();
        if (isset($refid)) {
            $criteria->add(new Criteria('imgset_refid', (int)$refid));
        }
        if (isset($tplset)) {
            $criteria->add(new Criteria('tplset_name', $tplset));
        }
        $imgsets =& $this->getObjects($criteria, true);
        $ret = array();
        foreach (array_keys($imgsets) as $i) {
            $ret[$i] = $imgsets[$i]->getVar('imgset_name');
        }
        return $ret;
    }
}
?>
