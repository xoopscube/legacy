<?php
/**
 * image category class object
 * @package    kernel
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsImagecategory extends XoopsObject
{
    public $_imageCount;

    public function __construct()
    {
        parent::__construct();
        $this->initVar('imgcat_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('imgcat_name', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('imgcat_display', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('imgcat_weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('imgcat_maxsize', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('imgcat_maxwidth', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('imgcat_maxheight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('imgcat_type', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('imgcat_storetype', XOBJ_DTYPE_OTHER, null, false);
    }

    public function setImageCount($value)
    {
        $this->_imageCount = (int)$value;
    }

    public function getImageCount()
    {
        return $this->_imageCount;
    }
}

/**
* XOOPS image caetgory handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS image category class objects.
*
*
* @author  Kazumi Ono <onokazu@xoops.org>
*/

class XoopsImagecategoryHandler extends XoopsObjectHandler
{

    public function &create($isNew = true)
    {
        $imgcat =new XoopsImagecategory();
        if ($isNew) {
            $imgcat->setNew();
        }
        return $imgcat;
    }

    public function &get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('imagecategory').' WHERE imgcat_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if (1 == $numrows) {
                    $imgcat =new XoopsImagecategory();
                    $imgcat->assignVars($this->db->fetchArray($result));
                    $ret =& $imgcat;
                }
            }
        }
        return $ret;
    }

    public function insert(&$imgcat)
    {
        if ('xoopsimagecategory' != strtolower(get_class($imgcat))) {
            return false;
        }
        if (!$imgcat->isDirty()) {
            return true;
        }
        if (!$imgcat->cleanVars()) {
            return false;
        }
        foreach ($imgcat->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($imgcat->isNew()) {
            $imgcat_id = $this->db->genId('imgcat_imgcat_id_seq');
            $sql = sprintf('INSERT INTO %s (imgcat_id, imgcat_name, imgcat_display, imgcat_weight, imgcat_maxsize, imgcat_maxwidth, imgcat_maxheight, imgcat_type, imgcat_storetype) VALUES (%u, %s, %u, %u, %u, %u, %u, %s, %s)', $this->db->prefix('imagecategory'), $imgcat_id, $this->db->quoteString($imgcat_name), $imgcat_display, $imgcat_weight, $imgcat_maxsize, $imgcat_maxwidth, $imgcat_maxheight, $this->db->quoteString($imgcat_type), $this->db->quoteString($imgcat_storetype));
        } else {
            $sql = sprintf('UPDATE %s SET imgcat_name = %s, imgcat_display = %u, imgcat_weight = %u, imgcat_maxsize = %u, imgcat_maxwidth = %u, imgcat_maxheight = %u, imgcat_type = %s WHERE imgcat_id = %u', $this->db->prefix('imagecategory'), $this->db->quoteString($imgcat_name), $imgcat_display, $imgcat_weight, $imgcat_maxsize, $imgcat_maxwidth, $imgcat_maxheight, $this->db->quoteString($imgcat_type), $imgcat_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($imgcat_id)) {
            $imgcat_id = $this->db->getInsertId();
        }
        $imgcat->assignVar('imgcat_id', $imgcat_id);
        return true;
    }

    public function delete(&$imgcat)
    {
        if ('xoopsimagecategory' != strtolower(get_class($imgcat))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE imgcat_id = %u', $this->db->prefix('imagecategory'), $imgcat->getVar('imgcat_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT c.* FROM '.$this->db->prefix('imagecategory').' c LEFT JOIN '.$this->db->prefix('group_permission')." l ON l.gperm_itemid=c.imgcat_id WHERE (l.gperm_name = 'imgcat_read' OR l.gperm_name = 'imgcat_write')";
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $where = $criteria->render();
            $sql .= ('' != $where) ? ' AND ' . $where : '';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $sql .= ' ORDER BY imgcat_weight, imgcat_id ASC';
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $imgcat =new XoopsImagecategory();
            $imgcat->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $imgcat;
            } else {
                $ret[$myrow['imgcat_id']] =& $imgcat;
            }
            unset($imgcat);
        }
        return $ret;
    }


    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('imagecategory').' i LEFT JOIN '.$this->db->prefix('group_permission')." l ON l.gperm_itemid=i.imgcat_id WHERE (l.gperm_name = 'imgcat_read' OR l.gperm_name = 'imgcat_write')";
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $where = $criteria->render();
            $sql .= ('' != $where) ? ' AND ' . $where : '';
        }
        if (!$result =& $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }

    public function &getList($groups = [], $perm = 'imgcat_read', $display = null, $storetype = null)
    {
        $criteria = new CriteriaCompo();
        if (is_array($groups) && !empty($groups)) {
            $criteriaTray = new CriteriaCompo();
            foreach ($groups as $gid) {
                $criteriaTray->add(new Criteria('gperm_groupid', $gid), 'OR');
            }
            $criteria->add($criteriaTray);
            if ('imgcat_read' == $perm || 'imgcat_write' == $perm) {
                $criteria->add(new Criteria('gperm_name', $perm));
                $criteria->add(new Criteria('gperm_modid', 1));
            }
        }
        if (isset($display)) {
            $criteria->add(new Criteria('imgcat_display', (int)$display));
        }
        if (isset($storetype)) {
            $criteria->add(new Criteria('imgcat_storetype', $storetype));
        }
        $categories =& $this->getObjects($criteria, true);
        $ret = [];
        foreach (array_keys($categories) as $i) {
            $ret[$i] = $categories[$i]->getVar('imgcat_name');
        }
        return $ret;
    }
}
