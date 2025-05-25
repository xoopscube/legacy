<?php
/**
 * Bannerstats - Module for XCL
 * Migrated banner files from legacyRender to Bannerstats
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

$modversion['dirname'] = 'bannerstats';
$modversion['name'] = 'Banner Stats';
$modversion['version'] = '2.51.0';
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

// Modules functions 
// onInstall, onUpdate, onUninstall
// $modversion['onInstall']   = 'oninstall.php';
// $modversion['onUpdate']    = 'onupdate.php';
// $modversion['onUninstall'] = 'onuninstall.php';
// Use the legacy_installer pattern
$modversion['legacy_installer']['installer']['class'] = 'Installer';
$modversion['legacy_installer']['updater']['class']   = 'Updater';
$modversion['legacy_installer']['uninstaller']['class'] = 'Uninstaller';
//Mysql
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'] = [
    '{prefix}_banner',
    '{prefix}_bannerclient',
    '{prefix}_bannerfinish'
];

// Admin
$modversion['hasAdmin'] = 1;

$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Main menu
$modversion['hasMain'] = 1;

// $modversion['sub'][1]['name'] = 'Client Login';
// $modversion['sub'][1]['url'] = 'index.php?action=Login';

// Templates
$modversion['templates'][] = array('file' => 'bannerstats_login.html', 'description' => 'Login Form');
$modversion['templates'][] = array('file' => 'bannerstats_stats.html', 'description' => 'Banner Stats');
$modversion['templates'][] = array('file' => 'bannerstats_error.html', 'description' => 'Bannerstats Error Page');
$modversion['templates'][] = array('file' => 'bannerstats_message.html', 'description' => 'Bannerstats Message Page');
$modversion['templates'][] = array('file' => 'bannerstats_change_url.html', 'description' => 'Change Banner URL Form');
$modversion['templates'][] = array('file' => 'bannerstats_support.html', 'description' => 'Banner Support Request Form');
$modversion['templates'][] = array('file' => 'blocks/bannerstats_block_campaign.html', 'description' => 'Campaign Banner Display Block');

$modversion['config'][]= [
    'name'        => 'banners',
    'title'       => '_MI_BANNERSTATS_CONF_BANNERS',
    'description' => '_MI_BANNERSTATS_CONF_BANNERS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1'  // default active
];
$modversion['config'][] = [
    'name'        => 'min_impressions',
    'title'       => '_MI_BANNERSTATS_MIN_IMPRESSIONS',
    'description' => '_MI_BANNERSTATS_MIN_IMPRESSIONS_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '1000' // default minimum
];
$modversion['config'][] = [
    'name'        => 'banner_alert_enable',
    'title'       => '_MI_BANNERSTATS_CONF_ENABLE_ALERTS',
    'description' => '_MI_BANNERSTATS_CONF_ENABLE_ALERTS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1, // 1 for yes, 0 for no
];
$modversion['config'][] = [
    'name'        => 'banner_alert_percent',
    'title'       => '_MI_BANNERSTATS_CONF_ALERT_THRESHOLD',
    'description' => '_MI_BANNERSTATS_CONF_ALERT_THRESHOLD_DESC',
    'formtype'    => 'text', // Could be a select box with predefined percentages
    'valuetype'   => 'int',
    'default'     => 90, // e.g., send alert when 90% of impressions are used
];
$modversion['config'][] = [
    'name'        => 'banner_alert_admin_email',
    'title'       => '_MI_BANNERSTATS_CONF_ADMIN_EMAIL',
    'description' => '_MI_BANNERSTATS_CONF_ADMIN_EMAIL_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'string',
    'default'     => $GLOBALS['xoopsConfig']['adminmail'], // Default to site admin email
];

// Blocks
$modversion['blocks'][1]['file'] = 'banner.php';
$modversion['blocks'][1]['name'] = _MI_BANNERSTATS_BLOCK_BANNER;
$modversion['blocks'][1]['description'] = _MI_BANNERSTATS_BLOCK_BANNER_DESC;
$modversion['blocks'][1]['show_func'] = 'b_bannerstats_banner_show';
$modversion['blocks'][1]['edit_func'] = 'b_bannerstats_banner_edit';
$modversion['blocks'][1]['options'] = '0|0';
$modversion['blocks'][1]['template'] = 'bannerstats_block_banner.html';

//client menu block
$modversion['blocks'][2]['file']        = 'bannerstats_blocks.php';
$modversion['blocks'][2]['name']        = '_MI_BANNERSTATS_BLOCK_CLIENT_MENU';
$modversion['blocks'][2]['description'] = '_MI_BANNERSTATS_BLOCK_CLIENT_MENU_DESC';
$modversion['blocks'][2]['show_func']   = 'b_bannerstats_client_menu_show';
// $modversion['blocks'][2]['edit_func']   = ''; // No edit function needed for this simple menu
$modversion['blocks'][2]['template']    = 'bannerstats_block_client_menu.html';
$modversion['blocks'][2]['visible']     = 1;
$modversion['blocks'][2]['can_clone']   = 0;

// Block 3: Campaign Banner Block
$modversion['blocks'][3]['file']        = 'bannerstats_menu.php';
$modversion['blocks'][3]['name']        = '_MI_BANNERSTATS_BLOCK_CAMPAIGN';
$modversion['blocks'][3]['description'] = '_MI_BANNERSTATS_BLOCK_CAMPAIGN_DESC';
$modversion['blocks'][3]['show_func']   = 'b_bannerstats_campaign_show';
$modversion['blocks'][3]['edit_func']   = 'b_bannerstats_campaign_edit';
$modversion['blocks'][3]['options']     = '-1|0|0'; // Default: cid (any)|bid (any random)|campaign_id (any)
$modversion['blocks'][3]['template']    = 'bannerstats_block_campaign.html';
$modversion['blocks'][3]['visible']     = 1;
$modversion['blocks'][3]['can_clone']   = 1; // Allow multiple instances with different settings
