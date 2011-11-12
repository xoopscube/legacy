<?php
/**
 *
 * @package CubeUtils
 * @version $Id: xoops_version.php 1294 2008-01-31 05:32:20Z nobunobu $
 * @copyright Copyright 2006-2008 NobuNobuXOOPS Project <http://sourceforge.net/projects/nobunobuxoops/>
 * @author NobuNobu <nobunobu@nobunobu.com>
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if(!defined('XOOPS_ROOT_PATH')) exit ;
$mydirname = basename(dirname( __FILE__ )) ;

$modversion['name'] = $mydirname;
$modversion['version'] = '0.80';
$modversion['description'] = 'XOOPS Cube 2.1.x Utilities';
$modversion['credits'] = 'NobuNobu';
$modversion['author'] = 'http://www.nobunobu.com/';
$modversion['help'] = 'index.html';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
if (class_exists('XCube_Root')) {//ToDo: Detection of HD more elegant way
  $root =& XCube_Root::getSingleton();
  $controllerClass = strtolower(get_class($root->mController));
  if ( $controllerClass === 'hdlegacy_controller' ) {
    $modversion['image'] = 'images/cubeUtilsHD.png';
  } else {
    $modversion['image'] = 'images/cubeUtils.png';
  }
} else {
  $modversion['image'] = 'images/cubeUtils.png';
}
$modversion['dirname'] = $mydirname;

$modversion['cube_style'] = true;

$modversion['hasMain'] = 0;
$modversion['read_any'] = true;

// Templates
$modversion['templates'][1]['file'] = 'cubeUtils_userform.html';
$modversion['templates'][1]['description'] = 'Auto Logon Form';

//Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';
$modversion['hasconfig'] = 1;
$modversion['config'][1] = array(
	'name'			=> 'cubeUtils_use_autologin' ,
	'title'			=> '_MI_CUBE_UTILS_CFG1_MSG' ,
	'description'	=> '_MI_CUBE_UTILS_CFG1_DESC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
);
$modversion['config'][2] = array(
	'name'			=> 'cubeUtils_login_lifetime' ,
	'title'			=> '_MI_CUBE_UTILS_CFG2_MSG' ,
	'description'	=> '_MI_CUBE_UTILS_CFG2_DESC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 240 ,
);

// Blocks
$modversion['blocks'][1]['file'] = 'cubeUtils_login.php';
$modversion['blocks'][1]['name'] = _MI_CUBE_UTILS_BNAME1;
$modversion['blocks'][1]['description'] = 'Shows login block';
$modversion['blocks'][1]['show_func'] = 'b_cubeUtils_login_show';
$modversion['blocks'][1]['template'] = 'cubeUtils_block_login.html';
$modversion['blocks'][1]['visible_any'] = true;
$modversion['blocks'][1]['show_all_module'] = true;
// Blocks
$modversion['blocks'][2]['file'] = 'cubeUtils_langsel.php';
$modversion['blocks'][2]['name'] = _MI_CUBE_UTILS_BNAME2;
$modversion['blocks'][2]['description'] = 'Shows Select Language';
$modversion['blocks'][2]['show_func'] = 'b_cubeUtils_langsel_show';
$modversion['blocks'][2]['show_all_module'] = true;
// Blocks
$modversion['blocks'][3]['file'] = 'cubeUtils_igoogle.php';
$modversion['blocks'][3]['name'] = _MI_CUBE_UTILS_BNAME3;
$modversion['blocks'][3]['description'] = 'iGoogle Block';
$modversion['blocks'][3]['show_func'] = 'b_cubeUtils_igoogle_show';
$modversion['blocks'][3]['edit_func'] = 'b_cubeUtils_igoogle_edit';
$modversion['blocks'][3]['template'] = 'cubeUtils_block_igoogle.html';
// Blocks
$modversion['blocks'][4]['file'] = 'cubeUtils_whatsnew.php';
$modversion['blocks'][4]['name'] = _MI_CUBE_UTILS_BNAME4;
$modversion['blocks'][4]['description'] = 'Whats New Block';
$modversion['blocks'][4]['show_func'] = 'b_cubeUtils_whatsnew_show';
$modversion['blocks'][4]['edit_func'] = 'b_cubeUtils_whatsnew_edit';
$modversion['blocks'][4]['options'] = '5';
$modversion['blocks'][4]['template'] = 'cubeUtils_block_whatsnew.html';
?>
