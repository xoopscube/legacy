<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
$this->db->queryF("/*!40101 SET NAMES utf8 */");
$this->db->queryF("/*!40101 SET SESSION character_set_database=utf8 */");
$this->db->queryF("/*!40101 SET SESSION character_set_server=utf8 */");
$this->db->queryF("/*!40101 ALTER DATABASE `".XOOPS_DB_NAME."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */");
