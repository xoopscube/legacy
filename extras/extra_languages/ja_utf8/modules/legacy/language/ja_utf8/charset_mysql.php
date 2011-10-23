<?php
/**
 * @package Legacy
 * @version $Id: charset_mysql.php,v 1.1 2007/05/25 06:05:02 minahito Exp $
 * @brief This file is the callback declaring Mysql Client Handling. If this
 *        declaring causes display broken, you may skip this process by using
 *        the preload defining LEGACY_JAPANESE_ANTI_CHARSETMYSQL. 
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

if (!defined("LEGACY_JAPANESE_ANTI_CHARSETMYSQL")) {
    $GLOBALS['xoopsDB']->queryF("/*!40101 SET NAMES utf8 */");
    //$GLOBALS['xoopsDB']->queryF("/*!40101 SET SESSION collation_connection=utf8 */");
}

?>