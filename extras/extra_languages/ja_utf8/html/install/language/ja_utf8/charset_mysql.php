<?php
/**
 *
 * @package Legacy
 * @version $Id: charset_mysql.php,v 1.4 2008/09/25 15:36:30 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    $this->db->queryF("/*!40101 SET NAMES utf8 */");
    $this->db->queryF("/*!40101 SET SESSION character_set_database=utf8 */");
    $this->db->queryF("/*!40101 SET SESSION character_set_server=utf8 */");
//    $this->db->queryF("/*!40101 SET SESSION collation_connection=ujis_japanese_ci */");
//    $this->db->queryF("/*!40101 SET SESSION collation_database=ujis_japanese_ci */");
//    $this->db->queryF("/*!40101 SET SESSION collation_server=ujis_japanese_ci */");
?>
