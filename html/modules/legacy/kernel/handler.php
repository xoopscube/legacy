<?php
/**
 *
 * @package    Legacy
 * @author     Nobuhiro YASUTOMI, PHP8
 * @version    $Id: handler.php,v 1.7 2008/11/14 10:46:37 mumincacao Exp $
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * NOTE: This class has only one primary key and one table.
 */
class XoopsObjectGenericHandler extends XoopsObjectHandler
{
    public $mTable = null;
    public $mPrimary = null;
    public $mClass = null;
    public $mDirname = null;
    public $mDataname = null;

    /**
     * A instance of xoops simple object to get type information.
     */
    public $_mDummyObj = null;

    public function __construct(&$db)
    {
        parent::__construct($db);
        if (!is_null($this->mTable)) {
            $tableArr = explode('_', $this->mTable);
        } else {
            $tableArr = [];
        }
        $this->mDirname = array_shift($tableArr);
        $this->mDataname = implode('_', $tableArr);
        $this->mTable = $this->db->prefix($this->mTable);
    }

    public function &create($isNew = true)
    {
        $obj = null;
        if (XC_CLASS_EXISTS($this->mClass)) {
            $obj =new $this->mClass();
            $obj->mDirname = $this->getDirname();
            if ($isNew) {
                $obj->setNew();
            }
        }
        return $obj;
    }

    public function &get($id)
    {
        $ret = null;

        $criteria =new Criteria($this->mPrimary, $id);
        $objArr =& $this->getObjects($criteria);

        if (1 == count($objArr)) {
            $ret =& $objArr[0];
        }

        return $ret;
    }

    /**
     * Return array of object with $criteria.
     *
     * @access public
     * @param CriteriaElement $criteria
     * @param int  $limit
     * @param int  $start
     * @param bool $id_as_key
     *
     * @return array
     */
    public function &getObjects($criteria = null, $limit = null, $start = null, $id_as_key = false)
    {
        $ret = [];

        $sql = 'SELECT * FROM `' . $this->mTable . '`';

        if (null !== $criteria && $criteria instanceof \CriteriaElement) {
            $where = $this->_makeCriteria4sql($criteria);
            
            if (trim((string) $where)) {
                $sql .= ' WHERE ' . $where;
            }

            $sorts = [];
            foreach ($criteria->getSorts() as $sort) {
                $sorts[] = '`' . $sort['sort'] . '` ' . $sort['order'];
            }
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . implode(',', $sorts);
            }

            if (null === $limit) {
                $limit = $criteria->getLimit();
            }

            if (null === $start) {
                $start = $criteria->getStart();
            }
        } else {
            if (null === $limit) {
                $limit = 0;
            }

            if (null === $start) {
                $start = 0;
            }
        }

        $db = $this->db;
        $result = $db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while ($row = $db->fetchArray($result)) {
            $obj =new $this->mClass();
            $obj->mDirname = $this->getDirname();
            $obj->assignVars($row);
            $obj->unsetNew();

            if ($id_as_key) {
                $ret[$obj->get($this->mPrimary)] =& $obj;
            } else {
                $ret[]=&$obj;
            }

            unset($obj);
        }

        return $ret;
    }

    /**
     * Return array of primary id with $criteria.
     *
     * @param CriteriaElement $criteria
     * @param int  $limit
     * @param int  $start
     *
     * @return array
     */
    public function getIdList($criteria = null, $limit = null, $start = null)
    {
        $ret = [];

        $sql = 'SELECT `' . $this->mPrimary . '` FROM `' . $this->mTable . '`';

        if (null !== $criteria && $criteria instanceof \CriteriaElement) {
            $where = $this->_makeCriteria4sql($criteria);

            if (trim($where)) {
                $sql .= ' WHERE ' . $where;
            }

            $sorts = [];
            foreach ($criteria->getSorts() as $sort) {
                $sorts[] = '`' . $sort['sort'] . '` ' . $sort['order'];
            }
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . implode(',', $sorts);
            }

            if (null === $limit) {
                $limit = $criteria->getLimit();
            }

            if (null === $start) {
                $start = $criteria->getStart();
            }
        } else {
            if (null === $limit) {
                $limit = 0;
            }

            if (null === $start) {
                $start = 0;
            }
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return $ret;
        }

        while ($row = $this->db->fetchArray($result)) {
            $ret[] = $row[$this->mPrimary];
        }

        return $ret;
    }

    public function getCount($criteria = null)
    {
        $sql= 'SELECT COUNT(*) c FROM `' . $this->mTable . '`';

        if (null !== $criteria && $criteria instanceof \CriteriaElement) {
            $where = $this->_makeCriteria4sql($criteria);

            if ($where) {
                $sql .= ' WHERE ' . $where;
            }
        }

        return $this->_getCount($sql);
    }

    /**
     * @access private
     * @param null $sql
     * @return bool
     */
    public function _getCount($sql = null)
    {
        $result=$this->db->query($sql);

        if (!$result) {
            return false;
        }

        $ret = $this->db->fetchArray($result);

        return $ret['c'];
    }

    public function insert(&$obj, $force = false)
    {
        if (!is_a($obj, $this->mClass)) {
            return false;
        }

        $new_flag = false;

        if ($obj->isNew()) {
            $new_flag = true;
            $sql = $this->_insert($obj);
        } else {
            $sql = $this->_update($obj);
        }

        $result = $force ? $this->db->queryF($sql) : $this->db->query($sql);

        if (!$result) {
            return false;
        }

        if ($new_flag) {
            $obj->setVar($this->mPrimary, $this->db->getInsertId());
            $this->_callDelegate('Add', $obj);
        } else {
            $this->_callDelegate('Update', $obj);
        }

        return true;
    }

    /**
     * @access private
     * @param $obj
     * @return string
     */
    public function _insert(&$obj)
    {
        $fields = [];
        $fileds= [];
        $values= [];

        $arr = $this->_makeVars4sql($obj);

        foreach ($arr as $_name => $_value) {
            $fields[] = "`{$_name}`";
            $values[] = $_value;
        }

        $sql = @sprintf('INSERT INTO `' . $this->mTable . '` ( %s ) VALUES ( %s )', implode(',', $fields), implode(',', $values));

        return $sql;
    }

    /**
     * @access private
     * @param $obj
     * @return string
     */
    public function _update(&$obj)
    {
        $set_lists= [];
        $where = '';

        $arr = $this->_makeVars4sql($obj);

        foreach ($arr as $_name => $_value) {
            if ($_name == $this->mPrimary) {
                $where = "`{$_name}`={$_value}";
            } else {
                $set_lists[] = "`{$_name}`={$_value}";
            }
        }

        $sql = @sprintf('UPDATE `' . $this->mTable . '` SET %s WHERE %s', implode(',', $set_lists), $where);

        return $sql;
    }

    /**
     * SQL generation helper
     * @param xoopsObject $obj
     * @return Array
    */
    public function _makeVars4sql(&$obj)
    {
        $ret = [];
        foreach ($obj->gets() as $key => $value) {
            if (null === $value) {
                $ret[$key] = 'NULL';
            } else {
                switch ($obj->mVars[$key]['data_type']) {
                    case XOBJ_DTYPE_STRING:
                    case XOBJ_DTYPE_TEXT:
                        $ret[$key] = $this->db->quoteString($value);
                        break;

                    default:
                        $ret[$key] = $value;
                }
            }
        }

        return $ret;
    }

    public function _makeCriteria4sql($criteria)
    {
        if (null == $this->_mDummyObj) {
            $this->_mDummyObj =& $this->create();
        }

        return $this->_makeCriteriaElement4sql($criteria, $this->_mDummyObj);
    }

    /**
     * @param CriteriaElement   $criteria
     * @param XoopsSimpleObject $obj
     * @return string|null
     */
    public function _makeCriteriaElement4sql($criteria, &$obj)
    {
        if ($criteria instanceof \CriteriaElement) {
            if ($criteria->hasChildElements()) {
                $maxCount = $criteria->getCountChildElements();
                $queryString = $this->_makeCriteria4sql($criteria->getChildElement(0));
                for ($i = 1; $i < $maxCount; $i++) {
                    $queryString .= ' ' . $criteria->getCondition($i) . ' ' . $this->_makeCriteria4sql($criteria->getChildElement($i));
                }
                return '('.$queryString.')';
            } else {
                //
                // Render
                //
                $name = $criteria->getName();
                $value = $criteria->getValue();
                if (null != $name && isset($obj->mVars[$name])) {
                    if (null === $value) {
                        $criteria->operator = '=' == $criteria->getOperator() ? 'IS' : 'IS NOT';
                        $value = 'NULL';
                    } elseif (in_array(strtoupper($criteria->operator), ['IN', 'NOT IN'])) {
                        $value = is_array($value) ? $value : explode(',', $value);
                        $typ = $obj->mVars[$name]['data_type'];
                        foreach ($value as $val) {
                            $tmp[] = $this->_escapeValue($val, $typ);
                        }
                        if (isset($tmp)) {
                            $value = '('.implode(',', $tmp).')';
                        } else {
                            $value = '("")';
                        }
                    } else {
                        $value = $this->_escapeValue($value, $obj->mVars[$name]['data_type']);
                    }
                } else {
                    $value = $this->db->quoteString($value);
                }

                return null != $name ? $name . ' ' . $criteria->getOperator() . ' ' . $value : null;
            }
        }
    }

    public function _escapeValue($value, $type)
    {
        switch ($type) {
            case XOBJ_DTYPE_BOOL:
                return $value ? 1 : 0;
            case XOBJ_DTYPE_INT:
                return (int)$value;
            case XOBJ_DTYPE_FLOAT:
                return (float)$value;
            default:
                return $this->db->quoteString($value);
        }
        return null;
    }

    /**
     * Delete $obj.
     *
     * @param      $obj
     * @param bool $force
     * @return bool
     */
    public function delete(&$obj, $force = false)
    {
        //
        // Because Criteria can generate the most appropriate sentence, use
        // criteria even if this approach is few slow.
        //
        $criteria =new Criteria($this->mPrimary, $obj->get($this->mPrimary));
        $sql = 'DELETE FROM `' . $this->mTable . '` WHERE ' . $this->_makeCriteriaElement4sql($criteria, $obj);

        $result = $force ? $this->db->queryF($sql) : $this->db->query($sql);
        if (true == $result) {
            $this->_callDelegate('delete', $obj);
        }

        return $result;
    }

    /**
     * Delete plural objects by $criteria. If the sub-class want to override
     * the procedure of delete, delete() is better. This member function
     * fetches objects by $criteria and casts these objects into delete()
     * inside.
     *
     * @param Criteria $criteria
     * @param bool     $force
     * @return bool
     */
    public function deleteAll($criteria, $force = false)
    {
        $objs =& $this->getObjects($criteria);

        $flag = true;

        foreach ($objs as $obj) {
            $flag &= $this->delete($obj, $force);
        }

        return $flag;
    }

    /**
     * getDirname
     *
     * @param	void
     *
     * @return	string
    **/
    public function getDirname()
    {
        return $this->mDirname;
    }

    /**
     * getDataname
     *
     * @param void
     *
     * @return	string[]
     */
    public function getDataname()
    {
        return $this->mDataname;
    }

    /**
     * _callDelegate
     *
     * @param	string	$type
     * @param	XoopsSimpleObject	&$obj
     *
     * @return	string
    **/
    public function _callDelegate(/*** string ***/ $type, /*** XoopsSimpleObject ***/ &$obj)
    {
        $arr = explode('_', $this->mTable);
        if (isset($arr[2])) {
            $tableName = $arr[2];
            for ($i=3;$i<count($arr);$i++) {
                $tableName .= '_'.$arr[$i];
            }
            XCube_DelegateUtils::call(sprintf('Module.%s.Event.%s.%s', $this->getDirname(), $type, $tableName), new XCube_Ref($obj));
        } else {
            XCube_DelegateUtils::call(sprintf('Module.%s.Event.%s', $this->getDirname(), $type), new XCube_Ref($obj));
        }
    }
}
