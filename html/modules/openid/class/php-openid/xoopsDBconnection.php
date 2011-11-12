<?php
/**
 * Emulate a PEAR database connection.
 * @version $Rev$
 * @link $URL$
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH . '/modules/openid/class/php-openid/Auth/OpenID/DatabaseConnection.php';

class OpenID_XoopsDBconnection extends Auth_OpenID_DatabaseConnection
{
    /**
     * @param XoopsDB $db
     */
    function OpenID_XoopsDBconnection(&$db)
    {
        $this->_db =& $db;
    }

    /**
     * Run an SQL query with the specified parameters, if any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return mixed $result The result of calling this connection's
     * internal query function.  The type of result depends on the
     * underlying database engine.  This method is usually used when
     * the result of a query is not important, like a DDL query.
     */
    function query($sql, $params = array())
    {
        $result =& $this->_db->queryF($this->_generateQuery($sql, $params));
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Run an SQL query and return the first row of the result set, if
     * any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return array $result The first row of the result set, if any,
     * keyed on column name.  False if no such result was found.
     */
    function getRow($sql, $params = array())
    {
        $result =& $this->_db->query($this->_generateQuery($sql, $params));
        if ($result) {
            if ($row = $this->_db->fetchArray($result)) {
                return $row;
            }
        }
        return false;
    }

    /**
     * Run an SQL query with the specified parameters, if any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return array $result An array of arrays representing the
     * result of the query; each array is keyed on column name.
     */
    function getAll($sql, $params = array())
    {
        $result =& $this->_db->query($this->_generateQuery($sql, $params));
        if ($result) {
            $records = array();
            while ($row = $this->_db->fetchArray($result)) {
                $records[] = $row;
            }
            if (count($records) > 0) {
                return $records;
            }
        }
        return false;
    }

    /**
     * @return int
     */
    function affectedRows()
    {
        return $this->_db->getAffectedRows();
    }

    function setFetchMode()
    {
        //not implement
    }

    /**
     * @param string $sql
     * @param array $params
     * @return string
     */
    function _generateQuery($sql, $params = array())
    {
        $tokens = preg_split('/([?!])/', $sql, -1, PREG_SPLIT_DELIM_CAPTURE);
        $preparedQuery = '';
        $i = 0;

        foreach ($tokens as $key => $value) {
            switch ($value) {
                case '?':
                    $param = $params[$i++];
                    if (!is_int($param)) {
                        $param = "'" . mysql_real_escape_string($param) . "'";
                    }
                    $preparedQuery .= $param;
                    break;
                case '!':
                    $preparedQuery .= $params[$i++];
                    break;
                default:
                    $preparedQuery .= $value;
                    break;
            }
        }
        return $preparedQuery;
    }
}
?>