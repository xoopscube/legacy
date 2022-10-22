<?php
/**
 * Database connection
 * @package    kernel
 * @subpackage database
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other Authors Mumincacao, 2008/09/20
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * base class
 */
include_once XOOPS_ROOT_PATH.'/class/database/database.php';

if (!defined('MYSQL_CLIENT_FOUND_ROWS')) {
    define('MYSQL_CLIENT_FOUND_ROWS', 2);
}

class XoopsMySQLDatabase extends XoopsDatabase
{
    /**
     * Database connection
     * @var resource
     */
    public $conn;

    /**
     * String for Emulation prepare
     * @var string
     */
    public $mPrepareQuery=null;

    /**
     * connect to the database
     *
     * @param bool $selectdb select the database now?
     * @return bool successful?
     */
    public function connect($selectdb = true)
    {
        if (XOOPS_DB_PCONNECT == 1) {
            $this->conn = @mysql_pconnect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, MYSQL_CLIENT_FOUND_ROWS);
        } else {
            $this->conn = @mysql_connect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, false, MYSQL_CLIENT_FOUND_ROWS);
        }

        if (!$this->conn) {
            $this->logger->addQuery('', $this->error(), $this->errno());
            return false;
        }

        if (false != $selectdb) {
            if (!mysql_select_db(XOOPS_DB_NAME)) {
                $this->logger->addQuery('', $this->error(), $this->errno());
                return false;
            }
        }

        // set sql_mode to '' for backward compatibility
        if (version_compare(mysql_get_server_info($this->conn), '5.6', '>=')) {
            mysql_query('SET SESSION sql_mode = \'\'', $this->conn);
        }

        return true;
    }

    /**
     * generate an ID for a new row
     *
     * This is for compatibility only. Will always return 0, because MySQL supports
     * autoincrement for primary keys.
     *
     * @param string $sequence name of the sequence from which to get the next ID
     * @return int always 0, because mysql has support for autoincrement
     */
    public function genId($sequence)
    {
        return 0; // will use auto_increment
    }

    /**
     * Get a result row as an enumerated array
     *
     * @param resource $result
     * @return array
     */
    public function fetchRow($result)
    {
        return @mysql_fetch_row($result);
    }

    /**
     * Fetch a result row as an associative array
     *
     * @param $result
     * @return array
     */
    public function fetchArray($result)
    {
        return @mysql_fetch_assoc($result);
    }

    /**
     * Fetch a result row as an associative array
     *
     * @param $result
     * @return array
     */
    public function fetchBoth($result)
    {
        return @mysql_fetch_array($result, MYSQL_BOTH);
    }

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return int
     */
    public function getInsertId()
    {
        return mysql_insert_id($this->conn);
    }

    /**
     * Get number of rows in result
     *
     * @param resource query result
     * @return int
     */
    public function getRowsNum($result)
    {
        return @mysql_num_rows($result);
    }

    /**
     * Get number of affected rows
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return mysql_affected_rows($this->conn);
    }

    /**
     * Close MySQL connection
     *
     */
    public function close()
    {
        mysql_close($this->conn);
    }

    /**
     * will free all memory associated with the result identifier result.
     *
     * @param resource query result
     * @return bool TRUE on success or FALSE on failure.
     */
    public function freeRecordSet($result)
    {
        return mysql_free_result($result);
    }

    /**
     * Returns the text of the error message from previous MySQL operation
     *
     * @return bool Returns the error text from the last MySQL function, or '' (the empty string) if no error occurred.
     */
    public function error()
    {
        return @mysql_error();
    }

    /**
     * Returns the numerical value of the error message from previous MySQL operation
     *
     * @return int Returns the error number from the last MySQL function, or 0 (zero) if no error occurred.
     */
    public function errno()
    {
        return @mysql_errno();
    }

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     *
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     */
    public function quoteString($str)
    {
        $str = '\''.mysql_real_escape_string($str, $this->conn).'\'';
        return $str;
    }

    /**
     * perform a query on the database
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    public function &queryF($sql, $limit=0, $start=0)
    {
        if (!empty($limit)) {
            if (empty($start)) {
                $sql .= ' LIMIT ' . (int)$limit;
            } else {
                $sql = $sql. ' LIMIT '.(int)$start.', '.(int)$limit;
            }
        }
        $result = mysql_query($sql, $this->conn);
        if ($result) {
            $this->logger->addQuery($sql);
            return $result;
        } else {
            $this->logger->addQuery($sql, $this->error(), $this->errno());
            $ret = false;
            return $ret;
        }
    }

    /**
     * perform a query
     *
     * This method is empty and does nothing! It should therefore only be
     * used if nothing is exactly what you want done! ;-)
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     *
     * @abstract
     */
    public function &query($sql, $limit=0, $start=0)
    {
    }

    /**
     * perform queries from SQL dump file in a batch
     *
     * @param string $file file path to an SQL dump file
     *
     * @return bool FALSE if failed reading SQL file or TRUE if the file has been read and queries executed
     */
    public function queryFromFile($file)
    {
        if (false !== ($fp = fopen($file, 'r'))) {
            include_once XOOPS_ROOT_PATH.'/class/database/sqlutility.php';
            $sql_queries = trim(fread($fp, filesize($file)));
            SqlUtility::splitMySqlFile($pieces, $sql_queries);
            foreach ($pieces as $query) {
                // [0] contains the prefixed query
                // [4] contains unprefixed table name
                $prefixed_query = SqlUtility::prefixQuery(trim($query), $this->prefix());
                if (false != $prefixed_query) {
                    $this->query($prefixed_query[0]);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get field name
     *
     * @param resource $result query result
     * @param int numerical field index
     * @return string
     */
    public function getFieldName($result, $offset)
    {
        return mysql_field_name($result, $offset);
    }

    /**
     * Get field type
     *
     * @param resource $result query result
     * @param int $offset numerical field index
     * @return string
     */
    public function getFieldType($result, $offset)
    {
        return mysql_field_type($result, $offset);
    }

    /**
     * Get number of fields in result
     *
     * @param resource $result query result
     * @return int
     */
    public function getFieldsNum($result)
    {
        return mysql_num_fields($result);
    }

    /**
     * Emulates prepare(), but this is TEST API.
     * @remark This is TEST API. This method should be called by only Legacy.
     * @param $query
     */
    public function prepare($query)
    {
        $count=0;
        while (false !== ($pos=strpos($query, '?'))) {
            $pre=substr($query, 0, $pos);
            $after='';
            if ($pos+1<=strlen($query)) {
                $after=substr($query, $pos+1);
            }

            $query=$pre.'{'.$count.'}'.$after;
            $count++;
        }
        $this->mPrepareQuery=$query;
    }

    /**
     * Emulates bind_param(), but this is TEST API.
     * @remark This is TEST API. This method should be called by only Legacy.
     */
    public function bind_param()
    {
        if (func_num_args()<2) {
            return;
        }

        $types=func_get_arg(0);
        $count=strlen($types);
        if (func_num_args()<$count) {
            return;
        }

        $searches= [];
        $replaces= [];
        for ($i=0;$i<$count;$i++) {
            $searches[$i]='{'.$i.'}';
            switch (substr($types, $i, 1)) {
                case 'i':
                    $replaces[$i]=(int)func_get_arg($i+1);
                    break;

                case 's':
                    $replaces[$i]=$this->quoteString(func_get_arg($i+1));
                    break;

                case 'd':
                    $replaces[$i]=floatval(func_get_arg($i + 1));
                    break;

                case 'b':
                    // Exception
                    die();
            }
        }

        $this->mPrepareQuery=str_replace($searches, $replaces, $this->mPrepareQuery);
    }

    /**
     * Executes prepared SQL with query(), but this is TEST API.
     * @remark This is TEST API. This method should be called by only Legacy.
     */
    public function &execute()
    {
        $result=&$this->query($this->mPrepareQuery);
        $this->mPrepareQuery=null;
        return $result;
    }

    /**
     * Executes prepared SQL with queryF(), but this is TEST API.
     * @remark This is TEST API. This method should be called by only Legacy.
     */
    public function &executeF()
    {
        $result=&$this->queryF($this->mPrepareQuery);
        $this->mPrepareQuery=null;
        return $result;
    }
}

/**
 * Safe Connection to a MySQL database.
 *
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 *
 * @package kernel
 * @subpackage database
 */
class XoopsMySQLDatabaseSafe extends XoopsMySQLDatabase
{

    /**
     * perform a query on the database
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    public function &query($sql, $limit=0, $start=0)
    {
        $result =& $this->queryF($sql, $limit, $start);
        return $result;
    }
}

/**
 * Read-Only connection to a MySQL database.
 *
 * This class allows only SELECT queries to be performed through its
 * {@link query()} method for security reasons.
 *
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 *
 * @package kernel
 * @subpackage database
 */
class XoopsMySQLDatabaseProxy extends XoopsMySQLDatabase
{

    /**
     * perform a query on the database
     *
     * this method allows only SELECT queries for safety.
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if unsuccessful
     */
    public function &query($sql, $limit=0, $start=0)
    {
        $sql = ltrim($sql);
        if (preg_match('/^SELECT/i', $sql)) {
            $ret = $this->queryF($sql, $limit, $start);
            return $ret;
        }
        $this->logger->addQuery($sql, 'Database update not allowed during processing of a GET request', 0);

        $ret = false;
        return $ret;
    }
}
