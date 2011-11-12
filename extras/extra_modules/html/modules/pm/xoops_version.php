<?php
// $Id: xoops_version.php,v 1.11 2008/10/12 03:55:37 minahito Exp $

$modversion['name'] = _MI_PM_NAME;
$modversion['version'] = 2.00;
$modversion['description'] = _MI_PM_NAME_DESC;
$modversion['author'] = "";
$modversion['credits'] = "XOOPS Cube Project";
$modversion['help'] = "help.html";
$modversion['license'] = "GPL see LICENSE";
$modversion['image'] = "images/pm.png";
$modversion['dirname'] = "pm";

$modversion['cube_style'] = true;

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "menu.php";

// Templates
$modversion['templates'][1]['file'] = 'viewpmsg.html';
$modversion['templates'][1]['description'] = 'This is used when user view the list of private messages';
$modversion['templates'][2]['file'] = 'readpmsg.html';
$modversion['templates'][2]['description'] = 'This is used when user read a private message.';
$modversion['templates'][3]['file'] = 'pmlite.html';
$modversion['templates'][3]['description'] = 'This is used when user work pmlite.php.';
$modversion['templates'][4]['file'] = 'pm_pmlite_success.html';
$modversion['templates'][4]['description'] = 'Display pm send message.';
$modversion['templates'][5]['file'] = 'pm_delete_one.html';
$modversion['templates'][5]['description'] = 'Display confirm deleting a message.';

//Preference
$modversion['config'][] = array (
		"name" => "send_type",
		"title" => "_MI_PM_CONF_SEND_TYPE",
		"description" => "_MI_PM_CONF_SEND_TYPE_DESC",
		"formtype" => "select",
		"options" => array(_MI_PM_CONF_SEND_TYPE_COMBO=>0, _MI_PM_CONF_SEND_TYPE_TEXT=>1),
		"valuetype" => "int",
		"default" => 0
	);

// Menu
$modversion['hasMain'] = 0;

?>
