<?php
// $Id: imagesetimg.php,v 1.1 2007/05/15 02:34:37 minahito Exp $
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

class XoopsImagesetimg extends XoopsObject
{
	function XoopsImagesetimg()
	{
		$this->XoopsObject();
		$this->initVar('imgsetimg_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('imgsetimg_file', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('imgsetimg_body', XOBJ_DTYPE_SOURCE, null, false);
		$this->initVar('imgsetimg_imgset', XOBJ_DTYPE_INT, null, false);
	}
}

/**
* XOOPS imageset image handler class.  
* This class is responsible for providing data access mechanisms to the data source 
* of XOOPS imageset image class objects.
*
*
* @author  Kazumi Ono <onokazu@xoops.org>
*/

class XoopsImagesetimgHandler extends XoopsObjectHandler
{

    function &create($isNew = true)
    {
        $imgsetimg =new XoopsImagesetimg();
        if ($isNew) {
            $imgsetimg->setNew();
        }
        return $imgsetimg;
    }

    function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('imgsetimg').' WHERE imgsetimg_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                        $imgsetimg =new XoopsImagesetimg();
                    $imgsetimg->assignVars($this->db->fetchArray($result));
                        $ret =& $imgsetimg;
                }
            }
        }
        return $ret;
    }

    function insert(&$imgsetimg)
    {
        if (strtolower(get_class($imgsetimg)) != 'xoopsimagesetimg') {
            return false;
        }
        if (!$imgsetimg->isDirty()) {
            return true;
        }
        if (!$imgsetimg->cleanVars()) {
            return false;
        }
        foreach ($imgsetimg->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($imgsetimg->isNew()) {
            $imgsetimg_id = $this->db->genId('imgsetimg_imgsetimg_id_seq');
            $sql = sprintf("INSERT INTO %s (imgsetimg_id, imgsetimg_file, imgsetimg_body, imgsetimg_imgset) VALUES (%u, %s, %s, %s)", $this->db->prefix('imgsetimg'), $imgsetimg_id, $this->db->quoteString($imgsetimg_file), $this->db->quoteString($imgsetimg_body), $this->db->quoteString($imgsetimg_imgset));
        } else {
            $sql = sprintf("UPDATE %s SET imgsetimg_file = %s, imgsetimg_body = %s, imgsetimg_imgset = %s WHERE imgsetimg_id = %u", $this->db->prefix('imgsetimg'), $this->db->quoteString($imgsetimg_file), $this->db->quoteString($imgsetimg_body), $this->db->quoteString($imgsetimg_imgset), $imgsetimg_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($imgsetimg_id)) {
            $imgsetimg_id = $this->db->getInsertId();
        }
		$imgsetimg->assignVar('imgsetimg_id', $imgsetimg_id);
        return true;
    }

    function delete(&$imgsetimg)
    {
        if (strtolower(get_class($imgsetimg)) != 'xoopsimagesetimg') {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE imgsetimg_id = %u", $this->db->prefix('imgsetimg'), $imgsetimg->getVar('imgsetimg_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT i.* FROM '.$this->db->prefix('imgsetimg'). ' i LEFT JOIN '.$this->db->prefix('imgset_tplset_link'). ' l ON l.imgset_id=i.imgsetimg_imgset LEFT JOIN '.$this->db->prefix('imgset').' s ON s.imgset_id=l.imgset_id';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            $sql .= ' ORDER BY imgsetimg_id '.$criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $imgsetimg =new XoopsImagesetimg();
            $imgsetimg->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $imgsetimg;
            } else {
                $ret[$myrow['imgsetimg_id']] =& $imgsetimg;
            }
            unset($imgsetimg);
        }
        return $ret;
    }

    function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(i.imgsetimg_id) FROM '.$this->db->prefix('imgsetimg'). ' i LEFT JOIN '.$this->db->prefix('imgset_tplset_link'). ' l ON l.imgset_id=i.imgsetimg_imgset';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere().' GROUP BY i.imgsetimg_id';
        }
        if (!$result =& $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }

/**
 * Function-Documentation
 * @param type $imgset_id documentation
 * @param type $id_as_key = false documentation
 * @return type documentation
 * @author Kazumi Ono <onokazu@xoops.org>
 **/
    function &getByImageset($imgset_id, $id_as_key = false)
    {
        $ret =& $this->getObjects(new Criteria('imgsetimg_imgset', (int)$imgset_id), $id_as_key);
        return $ret;
    }

/**
 * Function-Documentation
 * @param type $filename documentation
 * @param type $imgset_id documentation
 * @return type documentation
 * @author Kazumi Ono <onokazu@xoops.org>
 **/
    function imageExists($filename, $imgset_id)
    {
        $criteria = new CriteriaCompo(new Criteria('imgsetimg_file', $filename));
        $criteria->add(new Criteria('imgsetimg_imgset', (int)$imgset_id));
        if ($this->getCount($criteria) > 0) {
            return true;
        }
        return false;
    }
}
?>
