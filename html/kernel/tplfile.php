<?php
/**
 * template file class object
 * and handler class
 * @package    kernel
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2008/08/26
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
class XoopsTplfile extends XoopsObject
{

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        parent::__construct();
        $this->initVar('tpl_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('tpl_refid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tpl_tplset', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('tpl_file', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('tpl_desc', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('tpl_lastmodified', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tpl_lastimported', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('tpl_module', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('tpl_type', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('tpl_source', XOBJ_DTYPE_SOURCE, null, false);
        $initVars = $this->vars;
    }
    public function XoopsTplfile()
    {
        return $this->__construct();
    }

    public function &getSource()
    {
        $ret =& $this->getVar('tpl_source');
        return $ret;
    }

    public function getLastModified()
    {
        return $this->getVar('tpl_lastmodified');
    }
}

/**
* XOOPS template file handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS template file class objects.
*
* @author  Kazumi Ono <onokazu@xoops.org>
*/

class XoopsTplfileHandler extends XoopsObjectHandler
{

    public function &create($isNew = true)
    {
        $tplfile =new XoopsTplfile();
        if ($isNew) {
            $tplfile->setNew();
        }
        return $tplfile;
    }

    public function &get($id, $getsource = false)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            if (!$getsource) {
                $sql = 'SELECT * FROM '.$this->db->prefix('tplfile').' WHERE tpl_id='.$id;
            } else {
                $sql = 'SELECT f.*, s.tpl_source FROM '.$this->db->prefix('tplfile').' f LEFT JOIN '.$this->db->prefix('tplsource').' s  ON s.tpl_id=f.tpl_id WHERE f.tpl_id='.$id;
            }
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if (1 == $numrows) {
                    $ret =new XoopsTplfile();
                    $ret->assignVars($this->db->fetchArray($result));
                }
            }
        }
        return $ret;
    }

    public function loadSource(&$tplfile)
    {
        if ('xoopstplfile' !== strtolower(get_class($tplfile))) {
            return false;
        }
        if (!$tplfile->getVar('tpl_source')) {
            $sql = 'SELECT tpl_source FROM '.$this->db->prefix('tplsource').' WHERE tpl_id='.$tplfile->getVar('tpl_id');
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $myrow = $this->db->fetchArray($result);
            $tplfile->assignVar('tpl_source', $myrow['tpl_source']);
        }
        return true;
    }

    public function insert(&$tplfile)
    {
        $tpl_module = null;
        $tpl_refid = null;
        $tpl_tplset = null;
        $tpl_file = null;
        $tpl_desc = null;
        $tpl_lastmodified = null;
        $tpl_lastimported = null;
        $tpl_type = null;
        if ('xoopstplfile' !== strtolower(get_class($tplfile))) {
            return false;
        }
        if (!$tplfile->isDirty()) {
            return true;
        }
        if (!$tplfile->cleanVars()) {
            return false;
        }
        foreach ($tplfile->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($tplfile->isNew()) {
            $tpl_id = $this->db->genId('tplfile_tpl_id_seq');
            $sql = sprintf('INSERT INTO %s (tpl_id, tpl_module, tpl_refid, tpl_tplset, tpl_file, tpl_desc, tpl_lastmodified, tpl_lastimported, tpl_type) VALUES (%u, %s, %u, %s, %s, %s, %u, %u, %s)', $this->db->prefix('tplfile'), $tpl_id, $this->db->quoteString($tpl_module), $tpl_refid, $this->db->quoteString($tpl_tplset), $this->db->quoteString($tpl_file), $this->db->quoteString($tpl_desc), $tpl_lastmodified, $tpl_lastimported, $this->db->quoteString($tpl_type));
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            if (empty($tpl_id)) {
                $tpl_id = $this->db->getInsertId();
            }
            if (isset($tpl_source) && '' !== $tpl_source) {
                $sql = sprintf('INSERT INTO %s (tpl_id, tpl_source) VALUES (%u, %s)', $this->db->prefix('tplsource'), $tpl_id, $this->db->quoteString($tpl_source));
                if (!$result = $this->db->query($sql)) {
                    $this->db->query(sprintf('DELETE FROM %s WHERE tpl_id = %u', $this->db->prefix('tplfile'), $tpl_id));
                    return false;
                }
            }
            $tplfile->assignVar('tpl_id', $tpl_id);
        } else {
            $sql = sprintf('UPDATE %s SET tpl_tplset = %s, tpl_file = %s, tpl_desc = %s, tpl_lastimported = %u, tpl_lastmodified = %u WHERE tpl_id = %u', $this->db->prefix('tplfile'), $this->db->quoteString($tpl_tplset), $this->db->quoteString($tpl_file), $this->db->quoteString($tpl_desc), $tpl_lastimported, $tpl_lastmodified, $tpl_id);
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            if (isset($tpl_source) && '' !== $tpl_source) {
                $sql = sprintf('UPDATE %s SET tpl_source = %s WHERE tpl_id = %u', $this->db->prefix('tplsource'), $this->db->quoteString($tpl_source), $tpl_id);
                if (!$result = $this->db->query($sql)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function forceUpdate(&$tplfile)
    {
        $tpl_tplset = null;
        $tpl_file = null;
        $tpl_desc = null;
        $tpl_lastimported = null;
        $tpl_lastmodified = null;
        $tpl_id = null;
        if ('xoopstplfile' !== strtolower(get_class($tplfile))) {
            return false;
        }
        if (!$tplfile->isDirty()) {
            return true;
        }
        if (!$tplfile->cleanVars()) {
            return false;
        }
        foreach ($tplfile->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if (!$tplfile->isNew()) {
            $sql = sprintf('UPDATE %s SET tpl_tplset = %s, tpl_file = %s, tpl_desc = %s, tpl_lastimported = %u, tpl_lastmodified = %u WHERE tpl_id = %u', $this->db->prefix('tplfile'), $this->db->quoteString($tpl_tplset), $this->db->quoteString($tpl_file), $this->db->quoteString($tpl_desc), $tpl_lastimported, $tpl_lastmodified, $tpl_id);
            if (!$result = $this->db->queryF($sql)) {
                return false;
            }
            if (isset($tpl_source) && '' != $tpl_source) {
                $sql = sprintf('UPDATE %s SET tpl_source = %s WHERE tpl_id = %u', $this->db->prefix('tplsource'), $this->db->quoteString($tpl_source), $tpl_id);
                if (!$result = $this->db->queryF($sql)) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function delete(&$tplfile)
    {
        if ('xoopstplfile' !== strtolower(get_class($tplfile))) {
            return false;
        }
        $id = $tplfile->getVar('tpl_id');
        $sql = sprintf('DELETE FROM %s WHERE tpl_id = %u', $this->db->prefix('tplfile'), $id);
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE tpl_id = %u', $this->db->prefix('tplsource'), $id);
        $this->db->query($sql);
        return true;
    }

    /**
     * Delete the plural of record by a cetain criteria.
     *
     * @param null $criteria
     * @return bool
     */
    public function deleteAll($criteria = null)
    {
        $sql = sprintf('SELECT tpl_id FROM %s', $this->db->prefix('tplfile'));
        $sql .= ' ' . $criteria->renderWhere();

        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $sql = sprintf('DELETE FROM %s WHERE tpl_id=%u', $this->db->prefix('tplsource'), $row['tpl_id']);
            $this->db->query($sql);
        }

        $sql = sprintf('DELETE FROM %s', $this->db->prefix('tplfile'));
        $sql .= ' ' . $criteria->renderWhere();

        return $this->db->query($sql);
    }

    public function &getObjects($criteria = null, $getsource = false, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        if ($getsource) {
            $sql = 'SELECT f.*, s.tpl_source FROM '.$this->db->prefix('tplfile').' f LEFT JOIN '.$this->db->prefix('tplsource').' s ON s.tpl_id=f.tpl_id';
        } else {
            $sql = 'SELECT * FROM '.$this->db->prefix('tplfile');
        }
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere().' ORDER BY tpl_refid';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $tplfile =new XoopsTplfile();
            $tplfile->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $tplfile;
            } else {
                $ret[$myrow['tpl_id']] =& $tplfile;
            }
            unset($tplfile);
        }
        return $ret;
    }

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('tplfile');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result =& $this->db->query($sql)) {
            return 0;
        }
        [$count] = $this->db->fetchRow($result);
        return $count;
    }

    public function getModuleTplCount($tplset)
    {
        $ret = [];
        $sql = 'SELECT tpl_module, COUNT(tpl_id) AS count FROM ' . $this->db->prefix('tplfile') . ' WHERE tpl_tplset=' . $this->db->quoteString($tplset) . ' GROUP BY tpl_module';
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            if ('' !== $myrow['tpl_module']) {
                $ret[$myrow['tpl_module']] = $myrow['count'];
            }
        }
        return $ret;
    }

    public function &find($tplset = null, $type = null, $refid = null, $module = null, $file = null, $getsource = false)
    {
        $criteria = new CriteriaCompo();
        if (isset($tplset)) {
            $criteria->add(new Criteria('tpl_tplset', addslashes(trim($tplset))));
        }
        if (isset($module)) {
            $criteria->add(new Criteria('tpl_module', $module));
        }
        if (isset($refid)) {
            $criteria->add(new Criteria('tpl_refid', $refid));
        }
        if (isset($file)) {
            $criteria->add(new Criteria('tpl_file', addslashes(trim($file))));
        }
        if (isset($type)) {
            if (is_array($type)) {
                $criteria2 = new CriteriaCompo();
                foreach ($type as $t) {
                    $criteria2->add(new Criteria('tpl_type', addslashes(trim($t))), 'OR');
                }
                $criteria->add($criteria2);
            } else {
                $criteria->add(new Criteria('tpl_type', addslashes(trim($type))));
            }
        }
        $ret =& $this->getObjects($criteria, $getsource, false);
        return $ret;
    }

    public function templateExists($tplname, $tplset_name)
    {
        $criteria = new CriteriaCompo(new Criteria('tpl_file', addslashes(trim($tplname))));
        $criteria->add(new Criteria('tpl_tplset', addslashes(trim($tplset_name))));
        return $this->getCount($criteria) > 0;
    }
}
