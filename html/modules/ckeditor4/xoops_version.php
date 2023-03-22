<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.3.1
 * @author     Other authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}

// Manifesto.
$modversion['dirname']      = "ckeditor4";
$modversion['name']         = _MI_CKEDITOR4_LANG_CKEDITOR4;
$modversion['version']      = '4.20.2';
$modversion['description']  = _MI_CKEDITOR4_DESC_CKEDITOR4;
$modversion['author']       = "Naoki Sawada (aka nao-pon) https://xoops.hypweb.net";
$modversion['credits']      = "Naoki Sawada (aka nao-pon). Nuno Luciano (aka gigamaster) XCL PHP7 ";
$modversion['license']      = "GPL";
$modversion['image']        = "images/module_ckeditor.svg";
$modversion['icon']         = 'images/module_icon.svg';
$modversion['help']         = "help.html";
$modversion['official']     = 0;
$modversion['cube_style']   = true;

$modversion['disable_legacy_2nd_installer'] = false;


// Templates
$modversion['templates'][]['file'] = 'ckeditor4_textarea.html';


// Admin panel setting
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Public side control setting
$modversion['hasMain'] = 0;
// $modversion['sub'][]['name'] = "";
// $modversion['sub'][]['url'] = "";

//$modversion['config'][] = array(
//		'name'			=> 'editors' ,
//		'title'			=> '_MI_CKEDITOR4_EDITORS',
//		'description'	=> '_MI_CKEDITOR4_EDITORS_DESC',
//		'formtype'		=> ((defined('_MI_LEGACY_DETAILED_VERSION') && version_compare(_MI_LEGACY_DETAILED_VERSION, 'CorePack 20120825', '>='))? 'checkbox_br' : 'select_multi'),
//		'valuetype'		=> 'array',
//		'default'		=> array( 'html', 'bbcode'),
//		'options'		=> array( 'HTML' => 'html', 'XOOPS(BB)Code' => 'bbcode')
//) ;

$modversion['config'][] = array(
		'name'			=> 'toolbar_admin' ,
		'title'			=> '_MI_CKEDITOR4_TOOLBAR_ADMIN',
		'description'	=> '_MI_CKEDITOR4_TOOLBAR_ADMIN_DESC',
		'formtype'		=> 'textarea' ,
		'valuetype'		=> 'string' ,
		'default'		=> 'Full'
)

;$modversion['config'][] = array(
        'name'			=> 'uiColor' ,
        'title'			=> '_MI_CKEDITOR4_TOOLBAR_UI_COLOR',
        'description'	=> '_MI_CKEDITOR4_TOOLBAR_UI_COLOR_DESC',
        'formtype'		=> 'color' ,
        'valuetype'		=> 'string' ,
        'default'		=> '#405060'
) ;

$modversion['config'][] = array(
		'name'			=> 'special_groups' ,
		'title'			=> '_MI_CKEDITOR4_SPECIAL_GROUPS',
		'description'	=> '_MI_CKEDITOR4_SPECIAL_GROUPS_DESC',
		'formtype'		=> ((defined('_MI_LEGACY_DETAILED_VERSION') && version_compare(_MI_LEGACY_DETAILED_VERSION, 'CorePack 20120825', '>='))? 'group_checkbox' : 'group_multi') ,
		'valuetype'		=> 'array' ,
		'default'		=> array()
) ;

$modversion['config'][] = array(
		'name'			=> 'toolbar_special_group' ,
		'title'			=> '_MI_CKEDITOR4_TOOLBAR_SPECIAL_GROUP',
		'description'	=> '_MI_CKEDITOR4_TOOLBAR_SPECIAL_GROUP_DESC',
		'formtype'		=> 'textarea' ,
		'valuetype'		=> 'string' ,
		'default'		=> 'Full'
) ;

$modversion['config'][] = array(
		'name'			=> 'toolbar_user' ,
		'title'			=> '_MI_CKEDITOR4_TOOLBAR_USER',
		'description'	=> '_MI_CKEDITOR4_TOOLBAR_USER_DESC',
		'formtype'		=> 'textarea' ,
		'valuetype'		=> 'string' ,
		'default'		=> '[["PasteText","-","Undo","Redo" ],["Bold","Italic","Underline","Strike","-","TextColor","-","RemoveFormat","FontSize"],["NumberedList","BulletedList","Outdent","Indent","Blockquote"],["Link","Image","Smiley","PageBreak"],["Maximize", "ShowBlocks","-","CodeSnippet"]]'
) ;

$modversion['config'][] = array(
		'name'			=> 'toolbar_guest' ,
		'title'			=> '_MI_CKEDITOR4_TOOLBAR_GUEST',
		'description'	=> '_MI_CKEDITOR4_TOOLBAR_GUEST_DESC',
		'formtype'		=> 'textarea' ,
		'valuetype'		=> 'string' ,
		'default'		=> '[["PasteText","-","Undo","Redo" ],["Bold","Italic","Underline","Strike","-","TextColor","-","RemoveFormat","FontSize"],["NumberedList","BulletedList","Outdent","Indent","Blockquote"],["Link","Image","Smiley","PageBreak"],["Maximize", "ShowBlocks"]]'
) ;

$modversion['config'][] = array(
		'name'			=> 'toolbar_bbcode' ,
		'title'			=> '_MI_CKEDITOR4_TOOLBAR_BBCODE',
		'description'	=> '_MI_CKEDITOR4_TOOLBAR_BBCODE_DESC',
		'formtype'		=> 'textarea' ,
		'valuetype'		=> 'string' ,
		'default'		=> '[["Source"],["PasteText","-","Undo","Redo" ],["Bold","Italic","Underline","Strike","-","TextColor","-","RemoveFormat","FontSize"],["NumberedList","BulletedList","Outdent","Indent","Blockquote"],["Link","Image","Smiley","PageBreak"]]'
) ;

$modversion['config'][] = array(
		'name'			=> 'contentsCss' ,
		'title'			=> '_MI_CKEDITOR4_CONTENTSCSS',
		'description'	=> '_MI_CKEDITOR4_CONTENTSCSS_DESC',
		'formtype'		=> 'textarea' ,
		'valuetype'		=> 'string' ,
		'default'		=> '<head>'
) ;

$modversion['config'][] = array(
		'name'			=> 'extraPlugins' ,
		'title'			=> '_MI_CKEDITOR4_EXTRAPLUGINS',
		'description'	=> '_MI_CKEDITOR4_EXTRAPLUGINS_DESC',
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'string' ,
		'default'		=> ''
) ;

$modversion['config'][] = array(
		'name'			=> 'customConfig' ,
		'title'			=> '_MI_CKEDITOR4_CUSTOMCONFIG',
		'description'	=> '_MI_CKEDITOR4_CUSTOMCONFIG_DESC',
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'string' ,
		'default'		=> 'config.js'
) ;

$modversion['config'][] = array(
		'name'			=> 'enterMode' ,
		'title'			=> '_MI_CKEDITOR4_ENTERMODE',
		'description'	=> '_MI_CKEDITOR4_ENTERMODE_DESC',
		'formtype'		=> 'radio' ,
		'valuetype'		=> 'int' ,
		'default'		=> 1,
		'options'		=> array('P'=>1, 'BR'=>2)
) ;

$modversion['config'][] = array(
		'name'			=> 'shiftEnterMode' ,
		'title'			=> '_MI_CKEDITOR4_SHIFTENTERMODE',
		'description'	=> '_MI_CKEDITOR4_SHIFTENTERMODE_DESC',
		'formtype'		=> 'radio' ,
		'valuetype'		=> 'int' ,
		'default'		=> 2,
		'options'		=> array('P'=>1, 'BR'=>2)
) ;

$modversion['config'][] = array(
		'name'			=> 'allowedContent' ,
		'title'			=> '_MI_CKEDITOR4_ALLOWEDCONTENT',
		'description'	=> '_MI_CKEDITOR4_ALLOWEDCONTENT_DESC',
		'formtype'		=> 'yesno' ,
		'valuetype'		=> 'int' ,
		'default'		=> 1
) ;

$modversion['config'][] = array(
		'name'			=> 'autoParagraph' ,
		'title'			=> '_MI_CKEDITOR4_AUTOPARAGRAPH',
		'description'	=> '_MI_CKEDITOR4_AUTOPARAGRAPH_DESC',
		'formtype'		=> 'yesno' ,
		'valuetype'		=> 'int' ,
		'default'		=> 0
) ;

$modversion['config'][] = array(
		'name'			=> 'xelfinder' ,
		'title'			=> '_MI_CKEDITOR4_XELFINDER',
		'description'	=> '_MI_CKEDITOR4_XELFINDER_DESC',
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'string' ,
		'default'		=> 'xelfinder'
) ;

$modversion['config'][] = array(
		'name'			=> 'uploadHash' ,
		'title'			=> '_MI_CKEDITOR4_UPLOADHASH',
		'description'	=> '_MI_CKEDITOR4_UPLOADHASH_DESC',
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'string' ,
		'default'		=> ''
) ;

$modversion['config'][] = array(
		'name'			=> 'imgShowSize' ,
		'title'			=> '_MI_CKEDITOR4_IMGSHOWSIZE',
		'description'	=> '_MI_CKEDITOR4_IMGSHOWSIZE_DESC',
		'formtype'		=> 'textbox' ,
		'valuetype'		=> 'string' ,
		'default'		=> '200'
) ;

$modversion['onUpdate'] = 'admin/onupdate.inc.php';
