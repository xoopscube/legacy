<?php
/**
 * Abstruct handler class
 * @version $Rev$
 * @link $URL$
 */
class Openid_Handler_Abstract
{
    /**
     * @var XoopsDatabase
     */
    var $_db;

    /**
     * @var string
     */
    var $_error;
    
    /**
     * @var string
     */
    var $_tableName;

    /**
     * @var string
     */
    var $_keyField;

    /**
     * @var integer
     */
    var $_keyType;

    function Openid_Handler_Abstract()
    {
        $this->_db =& XoopsDatabaseFactory::getDatabaseConnection();
        $this->_keyType = XOBJ_DTYPE_INT;
    }

    /**
     * Get error message
     *
     * @return string
     */
    function getError()
    {
        return $GLOBALS['xoopsConfig']['debug_mode'] ? $this->_error : '';
    }

    /**
     * Get one record
     *
     * @param mixed $key
     * @return Openid_context OR false
     */
    function &get($key)
    {
        $format = 'SELECT * FROM `%s` WHERE `%s`=%s';
        $sql = sprintf($format, $this->_tableName, $this->_keyField, $this->_key4sql($key));

        return $this->_getOne($sql);
    }

    /**
     * Get count of record
     *
     * @return int
     */
    function getCount()
    {
        $format = 'SELECT COUNT(*) FROM `%s`';
        $sql = sprintf($format, $this->_tableName);

        return $this->_getCount($sql);
    }

    /**
     * Get object array
     *
     * @param int $limit
     * @param int $start
     * @return array
     */
    function &getObjects($limit = 30, $start = 0, $sort = null)
    {
        $format = 'SELECT * FROM `%s`';
        if ($sort) {
            $format .= ' ORDER BY ' . $sort;
        }
        $sql = sprintf($format, $this->_tableName);

        return $this->_getObjects($sql, $limit, $start);
    }

    /**
     * Delete record
     *
     * @param mixed $key
     * @return boolean
     */
    function delete($key)
    {
        $format = 'DELETE FROM `%s` WHERE `%s`=%s';
        $sql = sprintf($format, $this->_tableName, $this->_keyField, $this->_key4sql($key));

        if ($this->_query($sql)) {
            if ($this->_db->getAffectedRows() > 0) {
                return true;
            } else {
                $this->_error .= ' No record was deleted.';
                return false;
            }
        } else {
            return false;
        }
    }

    function _key4sql($value)
    {
        if ($this->_keyType == XOBJ_DTYPE_INT) {
            $value = intval($value);
        } else {
            $value = $this->_db->quoteString($value);
        }
    	return $value;
    }

    /**
     * Get one record by sql
     *
     * @param string $sql
     * @return Openid_context OR false
     */
    function &_getOne($sql)
    {
        $record = false;
        if ($result =& $this->_query($sql, 1)) {
            if ($row = $this->_db->fetchArray($result)) {
                require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
                $record = new Openid_Context();
            	foreach ($row as $key => $value) {
                    $record->set($key, $value);
                }
            }
        }
        return $record;
    }

    /**
     * Enter description here...
     *
     * @param string $sql
     * @param int $limit
     * @param int $start
     * @return array
     */
    function &_getObjects($sql, $limit = 0, $start = 0)
    {
        $ret = array();
        if ($result =& $this->_query($sql, $limit, $start)) {
            require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
            while ($row = $this->_db->fetchArray($result)) {
                $record = new Openid_Context();
                foreach ($row as $key => $value) {
                    $record->set($key, $value);
                }
                $ret[] =& $record;
                unset($record);
            }
        }
        return $ret;
    }

    /**
     * Get count of record
     *
     * @param string $sql
     * @return int
     */
    function _getCount($sql)
    {
        if ($result =& $this->_query($sql)) {
            list($count) = $this->_db->fetchRow($result);
            return $count;
        } else {
            return 0;
        }
    }

    /**
     * Enter description here...
     *
     * @param string $sql
     * @param int $limit
     * @param int $start
     * @return boolean
     */
    function &_query($sql, $limit = 0, $start = 0)
    {
        if (!$result =& $this->_db->query($sql, $limit, $start)) {
            $this->_error = $this->_db->error();
            $result = false;
        }
        return $result;
    }
}
?>