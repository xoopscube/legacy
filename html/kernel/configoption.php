<?php
/**
 * Config-Option
 * @package    kernel
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}


class XoopsConfigOption extends XoopsObject
{

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        parent::__construct();
        $this->initVar('confop_id', XOBJ_DTYPE_INT, null);
        $this->initVar('confop_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('confop_value', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('conf_id', XOBJ_DTYPE_INT, 0);
        $initVars = $this->vars;
    }

    /**
     * Get a constract of confop_value
     */
    public function getOptionKey()
    {
        return defined($this->get('confop_value')) ? constant($this->get('confop_value')) : $this->get('confop_value');
    }

    /**
     * Get a constract of confop_name
     */
    public function getOptionLabel()
    {
        return defined($this->get('confop_name')) ? constant($this->get('confop_name')) : $this->get('confop_name');
    }

    /**
     * Compare with contents of $config object. If it's equal, return true.
     * This member function doesn't use 'conf_id' & 'conf_order' to compare.
     *
     * @param $option
     * @return bool
     */
    public function isEqual(&$option)
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
    public function &create($isNew = true)
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
    public function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('configoption').' WHERE confop_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if (1 == $numrows) {
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
    public function insert(&$confoption)
    {
        $confop_name = null;
        $confop_value = null;
        $conf_id = null;
        $confop_id = null;
        if ('xoopsconfigoption' != strtolower(get_class($confoption))) {
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
            $sql = sprintf('INSERT INTO %s (confop_id, confop_name, confop_value, conf_id) VALUES (%u, %s, %s, %u)', $this->db->prefix('configoption'), $confop_id, $this->db->quoteString($confop_name), $this->db->quoteString($confop_value), $conf_id);
        } else {
            $sql = sprintf('UPDATE %s SET confop_name = %s, confop_value = %s WHERE confop_id = %u', $this->db->prefix('configoption'), $this->db->quoteString($confop_name), $this->db->quoteString($confop_value), $confop_id);
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
    public function delete(&$confoption)
    {
        if ('xoopsconfigoption' != strtolower(get_class($confoption))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE confop_id = %u', $this->db->prefix('configoption'), $confoption->getVar('confop_id', 'n'));
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
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('configoption');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
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
