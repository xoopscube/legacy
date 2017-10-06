<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
 * @package     kernel
 * @subpackage  database
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * base class
 */
include_once XOOPS_ROOT_PATH.'/class/database/database.php';

/**
 * connection to a mysql database
 * 
 * @abstract
 * 
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 * 
 * @package     kernel
 * @subpackage  database
 */
class XoopsMysqliDatabase extends XoopsDatabase
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
        $this->conn = mysqli_init();
        
        if (!$this->conn) {
            $this->logger->addQuery('', $this->error(), $this->errno());
            return false;
        }
        
        if (XOOPS_DB_PCONNECT == 1 && version_compare(PHP_VERSION, '5.3.0', '>=')) {
            mysqli_real_connect($this->conn, 'p:'.XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, null, null, null, MYSQLI_CLIENT_FOUND_ROWS);
        } else {
            mysqli_real_connect($this->conn, XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, null, null, null, MYSQLI_CLIENT_FOUND_ROWS);
        }
        
        if ($selectdb != false) {
            if (!mysqli_select_db($this->conn, XOOPS_DB_NAME)) {
                $this->logger->addQuery('', $this->error(), $this->errno());
                return false;
            }
        }
        
        // set sql_mode to '' for backward compatibility
        if (version_compare(mysqli_get_server_info($this->conn), '5.6', '>=')) {
            mysqli_query($this->conn, 'SET SESSION sql_mode = \'\'');
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
        return @ mysqli_fetch_row($result);
    }

    /**
     * Fetch a result row as an associative array
     *
     * @return array
     */
    public function fetchArray($result)
    {
        return @ mysqli_fetch_assoc($result);
    }

    /**
     * Fetch a result row as an associative array
     *
     * @return array
     */
    public function fetchBoth($result)
    {
        return @ mysqli_fetch_array($result, MYSQLI_BOTH);
    }

    /**
     * Get the ID generated from the previous INSERT operation
     * 
     * @return int
     */
    public function getInsertId()
    {
        return mysqli_insert_id($this->conn);
    }

    /**
     * Get number of rows in result
     * 
     * @param resource query result
     * @return int
     */
    public function getRowsNum($result)
    {
        return @ mysqli_num_rows($result);
    }

    /**
     * Get number of affected rows
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return mysqli_affected_rows($this->conn);
    }

    /**
     * Close MySQL connection
     * 
     */
    public function close()
    {
        mysqli_close($this->conn);
    }

    /**
     * will free all memory associated with the result identifier result.
     * 
     * @param resource query result
     * @return bool TRUE on success or FALSE on failure. 
     */
    public function freeRecordSet($result)
    {
        return mysqli_free_result($result);
    }

    /**
     * Returns the text of the error message from previous MySQL operation
     * 
     * @return bool Returns the error text from the last MySQL function, or '' (the empty string) if no error occurred. 
     */
    public function error()
    {
        return @ mysqli_error($this->conn);
    }

    /**
     * Returns the numerical value of the error message from previous MySQL operation 
     * 
     * @return int Returns the error number from the last MySQL function, or 0 (zero) if no error occurred. 
     */
    public function errno()
    {
        return @ mysqli_errno($this->conn);
    }

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     * 
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     */
    public function quoteString($str)
    {
        $str = '\''.mysqli_real_escape_string($this->conn, $str).'\'';
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
        $result = mysqli_query($this->conn, $sql);
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
                if ($prefixed_query != false) {
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
        if ($finfo = mysqli_fetch_field_direct($result, $offset)) {
            return $finfo->orgname? $finfo->orgname : $finfo->name;
        } else {
            return false;
        }
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
        if ($finfo = mysqli_fetch_field_direct($result, $offset)) {
            return $finfo->type;
        } else {
            return false;
        }
    }

    /**
     * Get number of fields in result
     *
     * @param resource $result query result
     * @return int
     */
    public function getFieldsNum($result)
    {
        return mysqli_num_fields($result);
    }
    
    /**
     * Sets the default client character set
     *
     * @param string $charset The charset to be set as default
     * @return bool
     */
    public function setCharset($charset)
    {
        return mysqli_set_charset($this->conn, $charset);
    }

    /**
     * Emulates prepare(), but this is TEST API.
     * @remark This is TEST API. This method should be called by only Legacy.
     */
    public function prepare($query)
    {
        $count=0;
        while (($pos=strpos($query, '?'))!==false) {
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

        $searches=array();
        $replaces=array();
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
                    $replaces[$i]=doubleval(func_get_arg($i+1));
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
class XoopsMysqliDatabaseSafe extends XoopsMysqliDatabase
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
class XoopsMysqliDatabaseProxy extends XoopsMysqliDatabase
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
