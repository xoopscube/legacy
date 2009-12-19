<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
if (!defined("LEGACY_JAPANESE_ANTI_CHARSETMYSQL")) {
$GLOBALS['xoopsDB']->queryF("/*!40101 SET NAMES utf8 */");
$GLOBALS['xoopsDB']->queryF("/*!40101 SET SESSION collation_connection=utf8 */");
}
?>