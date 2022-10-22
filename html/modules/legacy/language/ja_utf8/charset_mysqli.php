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

if (!defined('LEGACY_JAPANESE_ANTI_CHARSETMYSQL')) {
    if (function_exists('mysqli_set_charset')) {
        mysqli_set_charset($GLOBALS['xoopsDB']->conn, 'utf8mb4');
    } else {
        $GLOBALS['xoopsDB']->queryF('/*!50503 SET NAMES utf8mb4 */');
        $GLOBALS['xoopsDB']->queryF('/*!50503 SET SESSION collation_connection=utf8mb4_general_ci */');
    }
}
