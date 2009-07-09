<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$modversion['name']        = _MI_FILEMANAGER_NAME;
$modversion['version']     = 0.98;
$modversion['description'] = _MI_FILEMANAGER_DESC;
$modversion['author']      = "Hiroki Seike http://beabo.net/";
$modversion['credits']     = "Hiroki Seike";
$modversion['help']        = "help.html";
$modversion['license']     = "See Help";
$modversion['image']       = "images/fileManager.png";
$modversion['dirname']     = "fileManager";
$modversion['cube_style']  = true;

$modversion['hasAdmin']        = 1;
$modversion['adminindex']      = "admin/index.php";
$modversion['adminmenu']       = "admin/menu.php";
$modversion['hasSearch']       = 0;
$modversion['use_smarty']      = 1;
$modversion['hasComments']     = 0;
$modversion['hasNotification'] = 0;
$modversion['hasMain']         = 0;
$modversion['hasconfig']       = 1;

$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][] = "{prefix}_{dirname}_token";

$modversion['config'][] = array(
    "name"        => "defaultpath" ,
    "title"       => "_MI_FILEMANAGER_PATH" ,
    "description" => "_MI_FILEMANAGER_PATH_DSC" ,
    "formtype"    => "textbox" ,
    "valuetype"   => "text" ,
    "default"     => ""
) ;

$modversion['config'][] = array(
    "name"        => "dirhandle" ,
    "title"       => "_MI_FILEMANAGER_DIRHANDLE" ,
    "description" => "_MI_FILEMANAGER_DIRHANDLE_DSC" ,
    "formtype"    => "yesno" ,
    "valuetype"   => "int" ,
    "default"     => 0
) ;


$modversion['config'][] = array(
    "name"        => "thumbsize" ,
    "title"       => "_MI_FILEMANAGER_THUMBSIZE" ,
    "description" => "_MI_FILEMANAGER_THUMBSIZE_DSC" ,
    "formtype"    => "select" ,
    "valuetype"   => "int" ,
    "default"     => 100 ,
    "options"     => array('60' => 60, '100' => 100, '150' => 150, '200' => 200, '250' => 250)
) ;

$modversion['config'][] = array(
    "name"        => "xoopsimagelock" ,
    "title"       => "_MI_FILEMANAGER_XOOPSLOCK" ,
    "description" => "_MI_FILEMANAGER_XOOPSLOCK_DSC" ,
    "formtype"    => "yesno" ,
    "valuetype"   => "int" ,
    "default"     => 1
) ;

$modversion['config'][] = array(
    "name"        => "debugon" ,
    "title"       => "_MI_FILEMANAGER_DEBUGON" ,
    "description" => "_MI_FILEMANAGER_DEBUGON_DSC" ,
    "formtype"    => "yesno" ,
    "valuetype"   => "int" ,
    "default"     =>  0
) ;


// TODO upload file extensions
$modversion['config'][] = array(
    "name"        => "extensions" ,
    "title"       => "_MI_FILEMANAGER_EXTENSIONS" ,
    "description" => "_MI_FILEMANAGER_EXTENSIONS_DSC" ,
    "formtype"    => "text" ,
    "valuetype"   => "string" ,
    "default"     => "gif|jpg|jpeg|png|avi|mov|wmv|mp3|mp4|flv|doc|xls|ods|odt|pdf"
) ;


// reserved  options setting 
$modversion['config'][] = array(
    "name"        => "ffmpeguse" ,
    "title"       => "_MI_FILEMANAGER_FUSE" ,
    "description" => "_MI_FILEMANAGER_FUSE_DSC" ,
    "formtype"    => "yesno" ,
    "valuetype"   => "int" ,
    "default"     => 0
) ;


$modversion['config'][] = array(
    "name"        => "ffmpegpath" ,
    "title"       => "_MI_FILEMANAGER_FPATH" ,
    "description" => "_MI_FILEMANAGER_FPATH_DSC" ,
    "formtype"    => "text" ,
    "valuetype"   => "string",
    "default"     => ""
) ;


$modversion['config'][] = array(
    "name"        => "ffmpegcapture" ,
    "title"       => "_MI_FILEMANAGER_FCAPTURE" ,
    "description" => "_MI_FILEMANAGER_FCAPTURE_DSC" ,
    "formtype"    => "select" ,
    "valuetype"   => "int" ,
    "default"     => 5 ,
    "options"     => array('3' => 3, '5' => 5, '10' => 10, '15' => 15)
) ;

$modversion['config'][] = array(
    "name"        => "ffmpegmoviefile" ,
    "title"       => "_MI_FILEMANAGER_FMOVIEFILE" ,
    "description" => "_MI_FILEMANAGER_FMOVIEFILE_DSC" ,
    "formtype"    => "text" ,
    "valuetype"   => "string" ,
    "default"     => "flv|avi|mwv|mov|mpg|qt|mov|mp4"
) ;

// On install & Update , Uninstall
$modversion['onInstall']   = '/include/oninstall.php' ;
$modversion['onUpdate']    = '/include/onupdate.php' ;
$modversion['onUninstall'] = '/include/onuninstall.php' ;


?>
