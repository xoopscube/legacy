<?php
// $Id: pgsqldatabase.php,v 1.2 2008/09/20 16:04:40 mumincacao Exp $
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
 * @author	    aotake <aotake@bmath.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * base class
 */
include_once XOOPS_ROOT_PATH.'/class/database/database.php';

//if (!defined('MYSQL_CLIENT_FOUND_ROWS')) {
//	define('MYSQL_CLIENT_FOUND_ROWS', 2);
//}

/**
 * connection to a pgsql database
 * 
 * @abstract
 * 
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 * 
 * @package     kernel
 * @subpackage  database
 */
class XoopsPdoDatabase extends XoopsDatabase
{
	/**
	 * Database connection
	 * @var resource
	 */
	var $conn;

	/**
	 * String for Emulation prepare
	 * @var string
	 */
	var $mPrepareQuery=null;

	/**
	 * connect to the database
	 * 
     * @param bool $selectdb select the database now?
     * @return bool successful?
	 */
	function connect($selectdb = true)
    {
        throw new Zend_Exception("継承したクラスで connect() を実装してください");
    }

	/**
	 * generate an ID for a new row
     * 
     * This is for compatibility only. Will always return 0, because PgSQL supports
     * autoincrement for primary keys.
     * 
     * @param string $sequence name of the sequence from which to get the next ID
     * @return int always 0, because pgsql has support for autoincrement
	 */
	function genId($sequence)
    {
        throw new Zend_Exception("継承したクラスで genId() を実装してください");
    }

	/**
	 * Get a result row as an enumerated array
	 * 
     * @param resource $result
     * @return array
	 */
	function fetchRow($result)
	{
		//return @mysql_fetch_row($result);
        if($result instanceof Zend_Db_Statement_Pdo){
    		return $result->fetch(PDO::FETCH_NUM);
        } else {
            return 0;
        }
	}

	/**
	 * Fetch a result row as an associative array
	 *
     * @return array
	 */
	function fetchArray($result)
    {
        //return @mysql_fetch_assoc( $result );
        if($result instanceof Zend_Db_Statement_Pdo){
            return $result->fetch();
        } else {
            return array();
        }
    }

    /**
     * Fetch a result row as an associative array
     *
     * @param Zend_Db_Statement_Pdo
     * @return array
     */
    function fetchBoth($result)
    {
        //return @mysql_fetch_array( $result, MYSQL_BOTH );
        if($result instanceof Zend_Db_Statement_Pdo){
            return $result->fetch(Zend_Db::FETCH_BOTH);
        } else {
            return array();
        }
    }

	/**
	 * Get the ID generated from the previous INSERT operation
	 * 
     * @return int
	 */
	function getInsertId()
	{
        throw new Zend_Exception("継承したクラスで getInsertId() を実装してください");
	}

	/**
	 * Get number of rows in result
	 * 
     * @param Zend_Db_Statement_Pdo
     * @return int
	 */
	function getRowsNum($result)
	{
		return $result->rowCount();
	}

	/**
	 * Get number of affected rows
	 *
     * @return int
	 */
	function getAffectedRows()
	{
        // $this->result = Zend_Db_Statement_Pdo
        return $this->result->rowCount();
	}

	/**
	 * Close PgSQL connection
	 * 
	 */
	function close()
	{
        // php のマニュアルによると「PDO: Assign the value of NULL to the PDO object」とあったので。
        $this->conn = null;
	}

	/**
	 * will free all memory associated with the result identifier result.
	 * 
     * @param Zend_Db_Statement_Pdo obuject
     * @return bool TRUE on success or FALSE on failure. 
	 */
	function freeRecordSet($result)
	{
		return $result->closeCursor();
	}

	/**
	 * Returns the text of the error message from previous PgSQL operation
	 * 
     * @return bool Returns the error text from the last PgSQL function, or '' (the empty string) if no error occurred. 
	 */
	function error()
	{
        if($this->result instanceof Zend_Exception){
            return $this->result->getMessage();
        } else {
		    return $this->result->errorInfo();
        }
	}

	/**
	 * Returns the numerical value of the error message from previous PgSQL operation 
	 * 
     * @return int Returns the error number from the last PgSQL function, or 0 (zero) if no error occurred. 
	 */
	function errno()
	{
        if($this->result instanceof Zend_Exception){
            return 255;// TODO: ダミーで 255 としてるけどエラーの個数を返したいところ？
        }
        else if ($this->result instanceof Zend_Db_Statement_Pdo) {
            return $this->result->errorCode();
        }
        else {
		    return 0; // Pdo だから Zend_Exception じゃなければエラーじゃないと思ってよいか？
        }
	}

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     * 
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     */
    function quoteString($str)
    {
        //$str = '\''.mysql_real_escape_string($str, $this->conn).'\'';
        $str = $this->conn->quote($str);
        return $str;
    }

    /**
     * perform a query on the database
     * 
     * @param string $sql a valid PgSQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    function &queryF($sql, $limit=0, $start=0)
	{
		if ( !empty($limit) ) {
			if (empty($start)) {
                $sql .= ' LIMIT ' . (int)$limit;
			}
            else
            {
                $sql = $sql. ' LIMIT '.(int)$start.', '.(int)$limit;
            }
		}
        try {
    		$this->result = $this->conn->query($sql);
			$this->logger->addQuery($sql);
			return $this->result;
        } catch (Exception $e) {
            $this->logger->addQuery($sql, $e->getMessage(), $e->getErrorCode());
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
     * @param string $sql a valid PgSQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * 
     * @abstract
	 */
	function &query($sql, $limit=0, $start=0)
	{

    }

    /**
	 * perform queries from SQL dump file in a batch
	 * 
     * @param string $file file path to an SQL dump file
     * 
     * @return bool FALSE if failed reading SQL file or TRUE if the file has been read and queries executed
	 */
	function queryFromFile($file){
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
	function getFieldName($result, $offset)
	{
		$info = $result->getColumnMeta($offset);
        return $info["name"];
	}

	/**
	 * Get field type
	 *
     * @param resource $result query result
     * @param int $offset numerical field index
     * @return string
	 */
    function getFieldType($result, $offset)
	{
		$info = $result->getColumnMeta($offset);
        return $info["pdo_type"];
	}

	/**
	 * Get number of fields in result
	 *
     * @param resource $result query result
     * @return int
	 */
	function getFieldsNum($result)
	{
		return $result->columnCount();
	}

	/**
	 * Emulates prepare(), but this is TEST API.
	 * @remark This is TEST API. This method should be called by only Legacy.
	 */
	function prepare($query)
	{
		$count=0;
		while(($pos=strpos($query,'?'))!==false) {
			$pre=substr($query,0,$pos);
			$after='';
			if($pos+1<=strlen($query))
				$after=substr($query,$pos+1);
				
			$query=$pre.'{'.$count.'}'.$after;
			$count++;
		}
		$this->mPrepareQuery=$query;
	}

	/**
	 * Emulates bind_param(), but this is TEST API.
	 * @remark This is TEST API. This method should be called by only Legacy.
	 */
	function bind_param()
	{
		if(func_num_args()<2)
			return;

		$types=func_get_arg(0);
		$count=strlen($types);
		if(func_num_args()<$count)
			return;

		$searches=array();
		$replaces=array();
		for($i=0;$i<$count;$i++) {
			$searches[$i]='{'.$i.'}';
			switch(substr($types,$i,1)) {
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

		$this->mPrepareQuery=str_replace($searches,$replaces,$this->mPrepareQuery);
	}

	/**
	 * Executes prepared SQL with query(), but this is TEST API.
	 * @remark This is TEST API. This method should be called by only Legacy.
	 */
	function &execute()
	{
		$result=&$this->query($this->mPrepareQuery);
		$this->mPrepareQuery=null;
		return $result;
	}

	/**
	 * Executes prepared SQL with queryF(), but this is TEST API.
	 * @remark This is TEST API. This method should be called by only Legacy.
	 */
	function &executeF()
	{
		$result=&$this->queryF($this->mPrepareQuery);
		$this->mPrepareQuery=null;
		return $result;
	}
}

/**
 * Safe Connection to a PgSQL database.
 * 
 * 
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * 
 * @package kernel
 * @subpackage database
 */
class XoopsPdoDatabaseSafe extends XoopsPdoDatabase
{

    /**
     * perform a query on the database
     * 
     * @param string $sql a valid PgSQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
	function &query($sql, $limit=0, $start=0)
	{
		$result =& $this->queryF($sql, $limit, $start);
		return $result;
	}
}

/**
 * Read-Only connection to a PgSQL database.
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
class XoopsPdoDatabaseProxy extends XoopsPdoDatabase
{

    /**
     * perform a query on the database
     * 
     * this method allows only SELECT queries for safety.
     * 
     * @param string $sql a valid PgSQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if unsuccessful
     */
	function &query($sql, $limit=0, $start=0)
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
?>
