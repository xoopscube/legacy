<?php
/**
 * @file
 * @package mydhtml
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

//
// Define a basic manifesto.
//
$modversion['name'] = _MI_MYDHTML_LANG_MYDHTML;
$modversion['version'] = 0.01;
$modversion['description'] = _MI_MYDHTML_DESC_MYDHTML;
$modversion['author'] = "HIKAWA Kilica http://xoopsdev.com/";
$modversion['credits'] = "HIKAWA Kilica";
$modversion['help'] = "help.html";
$modversion['license'] = "GPL";
$modversion['official'] = 0;
$modversion['image'] = "images/module_icon.png";
$modversion['dirname'] = "mydhtml";

$modversion['cube_style'] = true;
$modversion['disable_legacy_2nd_installer'] = false;

// TODO After you made your SQL, remove the following comment-out.
// $modversion['sqlfile']['mysql'] = "sql/mysql.sql";
##[cubson:tables]
##[/cubson:tables]

//
// Templates. You must never change [cubson] chunk to get the help of cubson.
//
$modversion['templates'][]['file'] = 'mydhtml_textarea.html';
$modversion['templates'][]['description'] = 'mydhtml_textarea.html';
##[cubson:templates]
##[/cubson:templates]

//
// Admin panel setting
//
$modversion['hasAdmin'] = 0;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

//
// Public side control setting
//
$modversion['hasMain'] = 0;
// $modversion['sub'][]['name'] = "";
// $modversion['sub'][]['url'] = "";

?>
