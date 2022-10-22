<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

$this->db->queryF( '/*!50503 SET NAMES utf8mb4 */' );
$this->db->queryF( '/*!50503 SET SESSION character_set_server=utf8mb4 */' );
$this->db->queryF( '/*!50503 ALTER DATABASE `' . XOOPS_DB_NAME . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */' );
$this->db->queryF( '/*!50503 SET SESSION collation_connection=utf8mb4_general_ci */' );
/*
+--------------------------+--------------------+
| VARIABLE_NAME            | VARIABLE_VALUE     |
+--------------------------+--------------------+
| CHARACTER_SET_CLIENT     | utf8mb4            |
| CHARACTER_SET_CONNECTION | utf8mb4            |
| CHARACTER_SET_RESULTS    | utf8mb4            |
| COLLATION_CONNECTION     | utf8mb4_general_ci |
+--------------------------+--------------------+
*/
