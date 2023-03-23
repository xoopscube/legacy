<?php
/**
 * imageset handler class
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS imageset class objects.
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

class XoopsImageset extends XoopsObject
{

    public function __construct()
    {
        parent::__construct();
        $this->initVar('imgset_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('imgset_name', XOBJ_DTYPE_TXTBOX, null, true, 50);
        $this->initVar('imgset_refid', XOBJ_DTYPE_INT, 0, false);
    }
}

/**
* @author  Kazumi Ono <onokazu@xoops.org>
*/

class XoopsImagesetHandler extends XoopsObjectHandler
{

    public function &create($isNew = true)
    {
        $imgset =new XoopsImageset();
        if ($isNew) {
            $imgset->setNew();
        }
        return $imgset;
    }

    public function &get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('imgset').' WHERE imgset_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if (1 == $numrows) {
                    $imgset =new XoopsImageset();
                    $imgset->assignVars($this->db->fetchArray($result));
                    $ret =& $imgset;
                }
            }
        }
        return $ret;
    }

    public function insert(&$imgset)
    {
        if ('xoopsimageset' != strtolower(get_class($imgset))) {
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
            $sql = sprintf('INSERT INTO %s (imgset_id, imgset_name, imgset_refid) VALUES (%u, %s, %u)', $this->db->prefix('imgset'), $imgset_id, $this->db->quoteString($imgset_name), $imgset_refid);
        } else {
            $sql = sprintf('UPDATE %s SET imgset_name = %s, imgset_refid = %u WHERE imgset_id = %u', $this->db->prefix('imgset'), $this->db->quoteString($imgset_name), $imgset_refid, $imgset_id);
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

    public function delete(&$imgset)
    {
        if ('xoopsimageset' != strtolower(get_class($imgset))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE imgset_id = %u', $this->db->prefix('imgset'), $imgset->getVar('imgset_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE imgset_id = %u', $this->db->prefix('imgset_tplset_link'), $imgset->getVar('imgset_id'));
        $this->db->query($sql);
        return true;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT i.* FROM '.$this->db->prefix('imgset'). ' i LEFT JOIN '.$this->db->prefix('imgset_tplset_link'). ' l ON l.imgset_id=i.imgset_id';
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
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

    public function linkThemeset($imgset_id, $tplset_name)
    {
        $imgset_id = (int)$imgset_id;
        $tplset_name = trim($tplset_name);
        if ($imgset_id <= 0 || '' == $tplset_name) {
            return false;
        }
        if (!$this->unlinkThemeset($imgset_id, $tplset_name)) {
            return false;
        }
        $sql = sprintf('INSERT INTO %s (imgset_id, tplset_name) VALUES (%u, %s)', $this->db->prefix('imgset_tplset_link'), $imgset_id, $this->db->quoteString($tplset_name));
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        return true;
    }

    public function unlinkThemeset($imgset_id, $tplset_name)
    {
        $imgset_id = (int)$imgset_id;
        $tplset_name = trim($tplset_name);
        if ($imgset_id <= 0 || '' == $tplset_name) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE imgset_id = %u AND tplset_name = %s', $this->db->prefix('imgset_tplset_link'), $imgset_id, $this->db->quoteString($tplset_name));
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        return true;
    }

    public function &getList($refid = null, $tplset = null)
    {
        $criteria = new CriteriaCompo();
        if (isset($refid)) {
            $criteria->add(new Criteria('imgset_refid', (int)$refid));
        }
        if (isset($tplset)) {
            $criteria->add(new Criteria('tplset_name', $tplset));
        }
        $imgsets =& $this->getObjects($criteria, true);
        $ret = [];
        foreach (array_keys($imgsets) as $i) {
            $ret[$i] = $imgsets[$i]->getVar('imgset_name');
        }
        return $ret;
    }
}
