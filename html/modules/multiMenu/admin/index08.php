<?php

include "../../../include/cp_header.php";
include_once ('admin_function.php');
$menu_num = "08";
$op = '';
foreach ($_POST as $k => $v) {
	${$k} = $v;
}

if (isset($_GET['op'])) {
	$op = $_GET['op'];
	if (isset($_GET['id'])) {
		$id = intval($_GET['id']);
	}
	if (isset($_GET['weight'])) {
		$weight = intval($_GET['weight']);
	}
}
switch($op) {
case "new":
	im_admin_new($menu_num);
	break;
case "edit":
	im_admin_edit($menu_num, $id);
	break;
case "update":
	im_admin_update($menu_num, $id, $title, $link, $hide, $groups, $target);
	break;
case "del":
	im_admin_del($menu_num, $id, $del);
	break;
case "move":
	im_admin_move($menu_num, $id, $weight);
	im_admin_list($menu_num);
	break;
default:
	im_admin_list($menu_num);
	break;
}
?>