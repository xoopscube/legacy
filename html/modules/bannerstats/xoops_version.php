<?php

$modversion['dirname'] = 'bannerstats';
$modversion['name'] = 'Banner Stats';
$modversion['version'] = 1.0;
$modversion['description'] = 'Banner management and client statistics';
$modversion['author'] = 'Nuno Luciano aka gigamaster';
$modversion['credits'] = 'The XOOPSCube Project';
$modversion['help'] = '';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'images/module_bannerstats.png';
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

// Admin templates
/* $modversion['templates'][] = array('file' => 'admin/bannerstats_admin_banner_list.html', 'description' => 'Admin Banner List');
$modversion['templates'][] = array('file' => 'admin/bannerstats_admin_banner_edit.html', 'description' => 'Admin Banner Edit');
$modversion['templates'][] = array('file' => 'admin/bannerstats_admin_client_list.html', 'description' => 'Admin Client List');
$modversion['templates'][] = array('file' => 'admin/bannerstats_admin_client_edit.html', 'description' => 'Admin Client Edit');
 */
// Blocks
$modversion['blocks'][1]['file'] = 'banner.php';
$modversion['blocks'][1]['name'] = _MI_BANNERSTATS_BLOCK_BANNER;
$modversion['blocks'][1]['description'] = _MI_BANNERSTATS_BLOCK_BANNER_DESC;
$modversion['blocks'][1]['show_func'] = 'b_bannerstats_banner_show';
$modversion['blocks'][1]['edit_func'] = 'b_bannerstats_banner_edit';
$modversion['blocks'][1]['options'] = '0|0';
$modversion['blocks'][1]['template'] = 'bannerstats_block_banner.html';

