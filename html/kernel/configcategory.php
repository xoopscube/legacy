<?php
/**
 * Category of configs
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


class XoopsConfigCategory extends XoopsObject
{

    public function __construct()
    {
        parent::__construct();
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
                if (1 == $numrows) {
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
        if ('xoopsconfigcategory' != strtolower(get_class($confcat))) {
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
            $sql = sprintf('INSERT INTO %s (confcat_id, confcat_name, confcat_order) VALUES (%u, %s, %u)', $this->db->prefix('configcategory'), $confcat_id, $this->db->quoteString($confcat_name), $confcat_order);
        } else {
            $sql = sprintf('UPDATE %s SET confcat_name = %s, confcat_order = %u WHERE confcat_id = %u', $this->db->prefix('configcategory'), $this->db->quoteString($confcat_name), $confcat_order, $confcat_id);
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
        if ('xoopsconfigcategory' != strtolower(get_class($confcat))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE confcat_id = %u', $this->db->prefix('configcategory'), $confcat->getVar('confcat_id'));
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
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('configcategory');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
            $sort = !in_array($criteria->getSort(), ['confcat_id', 'confcat_name', 'confcat_order']) ? 'confcat_order' : $criteria->getSort();
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
