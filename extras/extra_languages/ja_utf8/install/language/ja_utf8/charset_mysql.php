<?php
/**
 *
 * @package Legacy
 * @version $Id: charset_mysql.php,v 1.4 2008/09/25 15:36:30 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
$set_charset = (XOOPS_DB_TYPE === 'mysqli')? 'mysqli_set_charset' : 'mysql_set_charset';
if (function_exists($set_charset)) {
    if (XOOPS_DB_TYPE === 'mysqli') {
        $set_charset($this->db->conn, 'utf8');
    } else {
        $set_charset('utf8');
    }
} else {
    $this->db->queryF("/*!40101 SET NAMES utf8 */");
    $this->db->queryF("/*!40101 SET SESSION collation_connection=utf8_general_ci */");
}
