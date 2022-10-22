<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

    if (function_exists('mysqli_set_charset')) {
        mysqli_set_charset($GLOBALS['xoopsDB']->conn, 'utf8mb4');
    } else {
        $GLOBALS['xoopsDB']->queryF('/*!50503 SET NAMES utf8mb4 */');
        $GLOBALS['xoopsDB']->queryF('/*!50503 SET SESSION collation_connection=utf8mb4_general_ci */');
    }


