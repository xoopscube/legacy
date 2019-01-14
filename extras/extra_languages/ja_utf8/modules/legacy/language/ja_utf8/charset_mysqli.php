<?php
/**
 * @package Legacy
 * @brief This file is the callback declaring Mysql Client Handling. If this
 *        declaring causes display broken, you may skip this process by using
 *        the preload defining LEGACY_JAPANESE_ANTI_CHARSETMYSQL. 
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

if (!defined("LEGACY_JAPANESE_ANTI_CHARSETMYSQL")) {
    if (function_exists('mysqli_set_charset')) {
        mysqli_set_charset($GLOBALS['xoopsDB']->conn, 'utf8');
    } else {
        $GLOBALS['xoopsDB']->queryF("/*!40101 SET NAMES utf8 */");
        $GLOBALS['xoopsDB']->queryF("/*!40101 SET SESSION collation_connection=utf8_general_ci */");
    }
}
