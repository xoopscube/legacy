<?php
/**
 * imageset image handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of imageset image class objects
 * @package    kernel
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsImagesetimg extends XoopsObject
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('imgsetimg_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('imgsetimg_file', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('imgsetimg_body', XOBJ_DTYPE_SOURCE, null, false);
        $this->initVar('imgsetimg_imgset', XOBJ_DTYPE_INT, null, false);
    }
}

/**
* @author  Kazumi Ono <onokazu@xoops.org>
*/

class XoopsImagesetimgHandler extends XoopsObjectHandler
{

    public function &create($isNew = true)
    {
        $imgsetimg =new XoopsImagesetimg();
        if ($isNew) {
            $imgsetimg->setNew();
        }
        return $imgsetimg;
    }

    public function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('imgsetimg').' WHERE imgsetimg_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if (1 == $numrows) {
                    $imgsetimg =new XoopsImagesetimg();
                    $imgsetimg->assignVars($this->db->fetchArray($result));
                    $ret =& $imgsetimg;
                }
            }
        }
        return $ret;
    }

    public function insert(&$imgsetimg)
    {
        if ('xoopsimagesetimg' != strtolower(get_class($imgsetimg))) {
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
            $sql = sprintf('INSERT INTO %s (imgsetimg_id, imgsetimg_file, imgsetimg_body, imgsetimg_imgset) VALUES (%u, %s, %s, %s)', $this->db->prefix('imgsetimg'), $imgsetimg_id, $this->db->quoteString($imgsetimg_file), $this->db->quoteString($imgsetimg_body), $this->db->quoteString($imgsetimg_imgset));
        } else {
            $sql = sprintf('UPDATE %s SET imgsetimg_file = %s, imgsetimg_body = %s, imgsetimg_imgset = %s WHERE imgsetimg_id = %u', $this->db->prefix('imgsetimg'), $this->db->quoteString($imgsetimg_file), $this->db->quoteString($imgsetimg_body), $this->db->quoteString($imgsetimg_imgset), $imgsetimg_id);
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

    public function delete(&$imgsetimg)
    {
        if ('xoopsimagesetimg' != strtolower(get_class($imgsetimg))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE imgsetimg_id = %u', $this->db->prefix('imgsetimg'), $imgsetimg->getVar('imgsetimg_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT i.* FROM '.$this->db->prefix('imgsetimg'). ' i LEFT JOIN '.$this->db->prefix('imgset_tplset_link'). ' l ON l.imgset_id=i.imgsetimg_imgset LEFT JOIN '.$this->db->prefix('imgset').' s ON s.imgset_id=l.imgset_id';
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
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

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(i.imgsetimg_id) FROM '.$this->db->prefix('imgsetimg'). ' i LEFT JOIN '.$this->db->prefix('imgset_tplset_link'). ' l ON l.imgset_id=i.imgsetimg_imgset';
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
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
     * @param bool $id_as_key = false documentation
     * @return type documentation
     * @author Kazumi Ono <onokazu@xoops.org>
     */
    public function &getByImageset($imgset_id, $id_as_key = false)
    {
        $ret =& $this->getObjects(new Criteria('imgsetimg_imgset', (int)$imgset_id), $id_as_key);
        return $ret;
    }

    /**
     * Function-Documentation
     * @param type $filename  documentation
     * @param type $imgset_id documentation
     * @return bool documentation
     * @author Kazumi Ono <onokazu@xoops.org>
     */
    public function imageExists($filename, $imgset_id)
    {
        $criteria = new CriteriaCompo(new Criteria('imgsetimg_file', $filename));
        $criteria->add(new Criteria('imgsetimg_imgset', (int)$imgset_id));
        if ($this->getCount($criteria) > 0) {
            return true;
        }
        return false;
    }
}
