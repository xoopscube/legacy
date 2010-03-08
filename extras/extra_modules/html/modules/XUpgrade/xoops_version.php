<?php

if (!defined('XUPGRADE_ENABLE_TEMPLATEPORTING')) {
	define('XUPGRADE_ENABLE_TEMPLATEPORTING', false);
}

//
// Define a basic manifesto.
//
$modversion['name'] = _MI_XUPGRADE_LANG_XUPGRADE;
$modversion['version'] = 0.13;
$modversion['description'] = _MI_XUPGRADE_DESC_XUPGRADE;
$modversion['author'] = "";
$modversion['credits'] = "";
$modversion['help'] = "help.html";
$modversion['license'] = "GPL";
$modversion['image'] = "images/XUpgrade.png";
$modversion['dirname'] = "XUpgrade";

$modversion['cube_style'] = true;

$modversion['legacy_installer']['installer']['class'] = "Installer";
$modversion['legacy_installer']['updater']['class'] = "Updater";

//
// Admin panel setting
//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

//
// Public side control setting
//
$modversion['hasMain'] = 0;
// $modversion['sub'][]['name'] = "";
// $modversion['sub'][]['url'] = "";

?>
