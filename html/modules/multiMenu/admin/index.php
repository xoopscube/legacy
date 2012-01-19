<?php
require "../../../include/cp_header.php";
require ('admin_function.php');
require_once dirname(dirname(__FILE__)).'/include/gtickets.php' ;

$menu_num = isset($_GET['mnum']) ? sprintf("%02d",intval($_GET['mnum'])) : "01" ;

require ('admin_action.php');
?>