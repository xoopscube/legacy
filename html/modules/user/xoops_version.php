<?php
// $Id: xoops_version.php,v 1.11 2008/10/12 03:55:37 minahito Exp $


// Manifesto
$modversion['dirname']          = 'user';
$modversion['name']             = _MI_USER_NAME;
$modversion['version']          = '2.50';
$modversion['detailed_version'] = '2.50.0';
$modversion['description']      = _MI_USER_NAME_DESC;
$modversion['author']           = 'The XOOPSCube Project';
$modversion['credits']          = 'Minahito, The XOOPSCube Project';
$modversion['license']          = 'GPL see LICENSE';
$modversion['image']            = 'images/module_users.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['cube_style']       = true;

// SQL
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = '{prefix}_{dirname}_mailjob';
$modversion['tables'][1] = '{prefix}_{dirname}_mailjob_link';

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Templates
$modversion['templates'][1]['file'] = 'user_userinfo.html';
$modversion['templates'][1]['description'] = 'Display a user information in userinfo.php';
$modversion['templates'][2]['file'] = 'user_userform.html';
$modversion['templates'][2]['description'] = 'Display login and register page to anonymouse user';
$modversion['templates'][3]['file'] = 'user_edituser.html';
$modversion['templates'][3]['description'] = 'When user edit own information, display this';
$modversion['templates'][4]['file'] = 'user_register_form.html';
$modversion['templates'][4]['description'] = '';
$modversion['templates'][5]['file'] = 'user_register_confirm.html';
$modversion['templates'][5]['description'] = '';
$modversion['templates'][6]['file'] = 'user_lostpass.html';
$modversion['templates'][6]['description'] = '';
$modversion['templates'][7]['file'] = 'user_default.html';
$modversion['templates'][7]['description'] = '';
$modversion['templates'][8]['file'] = 'user_avatar_edit.html';
$modversion['templates'][8]['description'] = '';
$modversion['templates'][9]['file'] = 'user_register_finish.html';
$modversion['templates'][9]['description'] = '';
$modversion['templates'][10]['file'] = 'user_misc_online.html';
$modversion['templates'][10]['description'] = '';
$modversion['templates'][11]['file'] = 'user_delete.html';
$modversion['templates'][11]['description'] = '';
$modversion['templates'][12]['file'] = 'user_delete_success.html';
$modversion['templates'][12]['description'] = '';

// Preference
$modversion['config'][]= [
    'name'        => 'allow_register',
    'title'       => '_MI_USER_CONF_ALLOW_REGISTER',
    'description' => '_MI_USER_CONF_ALW_RG_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     =>1
];

$modversion['config'][]= [
    'name'      => 'minpass',
    'title'     => '_MI_USER_CONF_MINPASS',
    'formtype'  => 'textbox',
    'valuetype' => 'int',
    'default'   =>5
];

$modversion['config'][]= [
    'name'      => 'minuname',
    'title'     => '_MI_USER_CONF_MINUNAME',
    'formtype'  => 'textbox',
    'valuetype' => 'int',
    'default'   =>4
];

$modversion['config'][]= [
    'name'      => 'maxuname',
    'title'     => '_MI_USER_CONF_MAXUNAME',
    'formtype'  => 'textbox',
    'valuetype' => 'int',
    'default'   =>10
];

$modversion['config'][]= [
    'name'      => 'allow_chgmail',
    'title'     => '_MI_USER_CONF_CHGMAIL',
    'formtype'  => 'yesno',
    'valuetype' => 'int',
    'default'   =>0
];

$modversion['config'][]= [
    'name'      => 'notify_method',
    'title'     => '_NOT_NOTIFYMETHOD',
    'formtype'  => 'select',
    'options'   => ['_NOT_METHOD_DISABLE' =>0, '_NOT_METHOD_PM' =>1, '_NOT_METHOD_EMAIL' =>2],
    'valuetype' => 'int',
    'default'   =>2
];

$modversion['config'][]= [
    'name'      => 'new_user_notify',
    'title'     => '_MI_USER_CONF_NEW_USER_NOTIFY',
    'formtype'  => 'yesno',
    'valuetype' => 'int',
    'default'   =>1
];

$modversion['config'][]= [
    'name'      => 'new_user_notify_group',
    'title'     => '_MI_USER_CONF_NEW_NTF_GROUP',
    'formtype'  => 'group',
    'valuetype' => 'int',
    'default'   =>XOOPS_GROUP_ADMIN
];

$modversion['config'][]= [
    'name'      => 'activation_type',
    'title'     => '_MI_USER_CONF_ACTV_TYPE',
    'formtype'  => 'select',
    'options'   => ['_MI_USER_CONF_ACTV_USER' =>0, '_MI_USER_CONF_ACTV_AUTO' =>1, '_MI_USER_CONF_ACTV_ADMIN' =>2],
    'valuetype' => 'int',
    'default'   =>0
];

$modversion['config'][]= [
    'name'        => 'activation_group',
    'title'       => '_MI_USER_CONF_ACTV_GROUP',
    'description' => '_MI_USER_CONF_ACTV_GROUP_DESC',
    'formtype'    => 'group',
    'valuetype'   => 'int',
    'default'     =>XOOPS_GROUP_ADMIN
];

$modversion['config'][]= [
    'name'      => 'uname_test_level',
    'title'     => '_MI_USER_CONF_UNAME_TEST_LEVEL',
    'formtype'  => 'select',
    'options'   => ['_MI_USER_CONF_UNAME_TEST_LEVEL_STRONG' =>0, '_MI_USER_CONF_UNAME_TEST_LEVEL_NORMAL' =>1, '_MI_USER_CONF_UNAME_TEST_LEVEL_WEAK' =>2],
    'valuetype' => 'int',
    'default'   =>1
];

$modversion['config'][]= [
    'name'      => 'avatar_allow_upload',
    'title'     => '_MI_USER_CONF_AVTR_ALLOW_UP',
    'formtype'  => 'yesno',
    'valuetype' => 'int',
    'default'   =>0
];

$modversion['config'][]= [
    'name'        => 'avatar_minposts',
    'title'       => '_MI_USER_CONF_AVATAR_MINPOSTS',
    'description' => '_MI_USER_CONF_AVT_MIN_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     =>0
];

$modversion['config'][]= [
    'name'      => 'avatar_width',
    'title'     => '_MI_USER_CONF_AVATAR_WIDTH',
    'formtype'  => 'textbox',
    'valuetype' => 'int',
    'default'   => '128'
];

$modversion['config'][]= [
    'name'      => 'avatar_height',
    'title'     => '_MI_USER_CONF_AVATAR_HEIGHT',
    'formtype'  => 'textbox',
    'valuetype' => 'int',
    'default'   => '128'
];

$modversion['config'][]= [
    'name'      => 'avatar_maxsize',
    'title'     => '_MI_USER_CONF_AVATAR_MAXSIZE',
    'formtype'  => 'textbox',
    'valuetype' => 'int',
    'default'   =>1_048_576
];

$modversion['config'][]= [
    'name'      => 'self_delete',
    'title'     => '_MI_USER_CONF_SELF_DELETE',
    'formtype'  => 'yesno',
    'valuetype' => 'int',
    'default'   =>0
];

$modversion['config'][]= [
    'name'      => 'self_delete_confirm',
    'title'     => '_MI_USER_CONF_SELF_DELETE_CONF',
    'formtype'  => 'textarea',
    'valuetype' => 'string',
    'default'   =>_MI_USER_CONF_SELF_DELETE_CONFIRM_DEFAULT
];

$modversion['config'][]= [
    'name'        => 'bad_unames',
    'title'       => '_MI_USER_CONF_BAD_UNAMES',
    'description' => '_MI_USER_CONF_BAD_UNAMES_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'array',
    'default'     => ['^admin', 'webmaster', '^xoops', '^cube', '^xcube', '^legacy', '^xcl', '^developer', '^develop', '^development', '^design', '^designer', '^support', '^info', '^information', '^client', '^customer', '^sales', '^billing']
];

$modversion['config'][]= [
    'name'        => 'bad_emails',
    'title'       => '_MI_USER_CONF_BAD_EMAILS',
    'description' => '_MI_USER_CONF_BAD_EMAILS_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'array',
    'default'     => ['xoopscube.jp$', 'xoopscube.org$']
];

$modversion['config'][]= [
    'name'        => 'reg_dispdsclmr',
    'title'       => '_MI_USER_CONF_DISPDSCLMR',
    'description' => '_MI_USER_CONF_DISPDSCLMR_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1
];

$modversion['config'][]= [
    'name'        => 'reg_disclaimer',
    'title'       => '_MI_USER_CONF_DISCLAIMER',
    'description' => '_MI_USER_CONF_DISCLAIMER_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'string',
    'default'     =>_MI_USER_CONF_DISCLAIMER_DESC_DEFAULT
];

$modversion['config'][]= [
    'name'        => 'usercookie',
    'title'       => '_MI_USER_CONF_USERCOOKIE',
    'description' => '_MI_USER_CONF_USERCOOKIE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'string',
    'default'     => 'wap_user'
];

$modversion['config'][]= [
    'name'      => 'use_ssl',
    'title'     => '_MI_USER_CONF_USE_SSL',
    'formtype'  => 'yesno',
    'valuetype' => 'int',
    'default'   => 0
];

$modversion['config'][]= [
    'name'      => 'sslpost_name',
    'title'     => '_MI_USER_CONF_SSLPOST_NAME',
    'formtype'  => 'textbox',
    'valuetype' => 'string',
    'default'   => 'xcl_ssl'
];

$modversion['config'][]= [
    'name'      => 'sslloginlink',
    'title'     => '_MI_USER_CONF_SSLLOGINLINK',
    'formtype'  => 'textbox',
    'valuetype' => 'string',
    'default'   => XOOPS_URL . '/login.php'
];

// Menu
$modversion['hasMain'] = 0;
$modversion['read_any'] = true;

// Block
$modversion['blocks'][1]['file']            = 'user_login.php';
$modversion['blocks'][1]['name']            = _MI_USER_BLOCK_LOGIN_NAME;
$modversion['blocks'][1]['description']     = _MI_USER_BLOCK_LOGIN_DESC;
$modversion['blocks'][1]['show_func']       = 'b_user_login_show';
$modversion['blocks'][1]['edit_func']       = 'b_user_login_edit';
$modversion['blocks'][1]['template']        = 'user_block_login.html';
$modversion['blocks'][1]['options']         = '0';
$modversion['blocks'][1]['visible_any']     = true;
$modversion['blocks'][1]['show_all_module'] = true;


$modversion['blocks'][2]['file'] = 'user_online.php';
$modversion['blocks'][2]['name'] = _MI_USER_BLOCK_ONLINE_NAME;
$modversion['blocks'][2]['description'] = _MI_USER_BLOCK_ONLINE_DESC;
$modversion['blocks'][2]['show_func'] = 'b_user_online_show';
$modversion['blocks'][2]['template'] = 'user_block_online.html';
$modversion['blocks'][2]['show_all_module'] = true;

$modversion['blocks'][3]['file'] = 'user_newusers.php';
$modversion['blocks'][3]['name'] = _MI_USER_BLOCK_NEWUSERS_NAME;
$modversion['blocks'][3]['description'] = _MI_USER_BLOCK_NEWUSERS_DESC;
$modversion['blocks'][3]['show_func'] = 'b_user_newusers_show';
$modversion['blocks'][3]['template'] = 'user_block_newusers.html';
$modversion['blocks'][3]['edit_func'] = 'b_user_newusers_edit';
$modversion['blocks'][3]['options'] = '10|1';
$modversion['blocks'][3]['show_all_module'] = true;

$modversion['blocks'][4]['file'] = 'user_topusers.php';
$modversion['blocks'][4]['name'] = _MI_USER_BLOCK_TOPUSERS_NAME;
$modversion['blocks'][4]['description'] = _MI_USER_BLOCK_TOPUSERS_DESC;
$modversion['blocks'][4]['show_func'] = 'b_user_topusers_show';
$modversion['blocks'][4]['template'] = 'user_block_topusers.html';
$modversion['blocks'][4]['edit_func'] = 'b_user_topusers_edit';
$modversion['blocks'][4]['options'] = '10|1';
$modversion['blocks'][4]['show_all_module'] = true;
