<?php
/**
 *
 * @package Legacy
 * @copyright Copyright 2005-2013 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 * @brief This file is the callback declaring Mysql Client Handling. If this
 *        declaring causes display broken, you may skip this process by using
 *        the preload defining LEGACY_JAPANESE_ANTI_CHARSETMYSQL. 
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

if (!defined("LEGACY_JAPANESE_ANTI_CHARSETMYSQL")) {
    if (function_exists('mysqli_set_charset')) {
        mysqli_set_charset($GLOBALS['xoopsDB']->conn, 'ujis');
    } else {
        $GLOBALS['xoopsDB']->queryF("/*!40101 SET NAMES ujis */");
        $GLOBALS['xoopsDB']->queryF("/*!40101 SET SESSION collation_connection=ujis_japanese_ci */");
    }
}
