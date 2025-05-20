<?php
/**
 * Bannerstats - Module for XCL
 * Migrated banner files from legacyRender to Bannerstats
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @author     Kazuhisa Minato aka minahito, Core developer
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 * @since      v 1.1 2007/05/15 02:34:17 minahito
 **/

$modversion['dirname'] = 'bannerstats';
$modversion['name'] = 'Banner Stats';
$modversion['version'] = 1.0;
$modversion['description'] = 'Banner management and client statistics';
$modversion['author'] = 'Nuno Luciano aka gigamaster';
$modversion['credits'] = 'The XOOPSCube Project';
$modversion['help'] = '';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['icon'] = 'images/module_icon.svg';
$modversion['image'] = 'images/module_image.svg';
$modversion['cube_style'] = true;
$modversion['read_any'] = true;

// Admin
$modversion['hasAdmin'] = 1;

$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Main menu
$modversion['hasMain'] = 1;

$modversion['sub'][1]['name'] = 'Client Login';
$modversion['sub'][1]['url'] = 'index.php?action=Login';

// Templates
$modversion['templates'][] = array('file' => 'bannerstats_login.html', 'description' => 'Login Form');
$modversion['templates'][] = array('file' => 'bannerstats_stats.html', 'description' => 'Banner Stats');
$modversion['templates'][] = array('file' => 'bannerstats_error.html', 'description' => 'Bannerstats Error Page');
$modversion['templates'][] = array('file' => 'bannerstats_message.html', 'description' => 'Bannerstats Message Page');
$modversion['templates'][] = array('file' => 'bannerstats_change_url_form.html', 'description' => 'Change Banner URL Form');
$modversion['templates'][] = array('file' => 'bannerstats_request_support_form.html', 'description' => 'Banner Support Request Form');

$modversion['config'][]= [
    'name'        => 'banners',
    'title'       => '_MI_BANNERSTATS_CONF_BANNERS',
    'description' => '_MI_BANNERSTATS_CONF_BANNERS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0
];
$modversion['config'][] = [
    'name'        => 'min_impressions',
    'title'       => '_MI_BANNERSTATS_MIN_IMPRESSIONS', // Define this language constant
    'description' => '_MI_BANNERSTATS_MIN_IMPRESSIONS_DESC', // Define this language constant
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 1 // Set your desired default minimum
];
// Blocks
$modversion['blocks'][1]['file'] = 'banner.php';
$modversion['blocks'][1]['name'] = _MI_BANNERSTATS_BLOCK_BANNER;
$modversion['blocks'][1]['description'] = _MI_BANNERSTATS_BLOCK_BANNER_DESC;
$modversion['blocks'][1]['show_func'] = 'b_bannerstats_banner_show';
$modversion['blocks'][1]['edit_func'] = 'b_bannerstats_banner_edit';
$modversion['blocks'][1]['options'] = '0|0';
$modversion['blocks'][1]['template'] = 'bannerstats_block_banner.html';

