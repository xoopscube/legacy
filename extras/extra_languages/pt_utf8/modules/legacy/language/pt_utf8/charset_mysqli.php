<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
if (function_exists('mysqli_set_charset')) {
    mysqli_set_charset($GLOBALS['xoopsDB']->conn, 'utf8');
} else {
    $GLOBALS['xoopsDB']->queryF("/*!40101 SET NAMES utf8 */");
    $GLOBALS['xoopsDB']->queryF("/*!40101 SET SESSION collation_connection=utf8_general_ci */");
}
