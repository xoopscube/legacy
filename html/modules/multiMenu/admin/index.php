<?php
require "../../../include/cp_header.php";
require ('admin_function.php');

$menu_num = isset($_GET['mnum']) ? sprintf("%02d",intval($_GET['mnum'])) : "01" ;

require ('admin_action.php');
?>