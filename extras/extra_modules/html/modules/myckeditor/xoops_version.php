<?php
/**
 * @file
 * @package myckeditor
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

//
// Define a basic manifesto.
//
$modversion['name'] = _MI_MYCKEDITOR_LANG_MYCKEDITOR;
$modversion['version'] = 0.01;//pack2011 add class
$modversion['description'] = _MI_MYCKEDITOR_DESC_MYCKEDITOR;
$modversion['author'] = "HIKAWA Kilica http://xoopsdev.com/";
$modversion['credits'] = "HIKAWA Kilica";
$modversion['help'] = "CHANGES.html";
$modversion['license'] = "GPL";
$modversion['official'] = 0;
$modversion['image'] = "images/mydhtml.png";
$modversion['dirname'] = "myckeditor";

$modversion['cube_style'] = true;
$modversion['disable_legacy_2nd_installer'] = false;

// TODO After you made your SQL, remove the following comment-out.
// $modversion['sqlfile']['mysql'] = "sql/mysql.sql";
##[cubson:tables]
##[/cubson:tables]

//
// Templates. You must never change [cubson] chunk to get the help of cubson.
//
$modversion['templates'][]['file'] = 'myckeditor_textarea.html';
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
