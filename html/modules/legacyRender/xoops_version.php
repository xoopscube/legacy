<?php
/**
 * @version $Id: xoops_version.php,v 1.12 2008/10/12 03:55:38 minahito Exp $
 * @package legacyRender
 */


// Manifesto
$modversion['dirname']          = 'legacyRender';
$modversion['name']             = _MI_LEGACYRENDER_NAME;
$modversion['version']          = '2.32';
$modversion['detailed_version'] = '2.32.1';
$modversion['description']      = _MI_LEGACYRENDER_NAME_DESC;
$modversion['author']           = 'The XOOPSCube Project Team';
$modversion['credits']          = 'The XOOPSCube Project Team';
$modversion['license']          = 'GPL see LICENSE';
$modversion['image']            = 'images/module_render.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['cube_style']       = true;


// SQL
// $modversion['sqlfile']['mysql'] = "sql/mysql.sql";
// $modversion['tables'][] = "legacyrender_theme";

// Menu
$modversion['hasMain'] = 0;

// Admin
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/menu.php';


// Template
$modversion['templates'][1]['file']= 'legacy_render_dialog.html';

// Preference
$modversion['config'][]= [
    'name'        => 'logotype',
    'title'       => '_MI_LR_LOGO',
    'description' => '_MI_LR_LOGO_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => XOOPS_URL . '/images/logo.png'
];
$modversion['config'][]= [
    'name'        => 'favicon',
    'title'       => '_MI_LR_FAVICON',
    'description' => '_MI_LR_FAVICON_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => XOOPS_URL . '/favicon.svg'
];

$modversion['config'][]= [
    'name'        => 'banners',
    'title'       => '_MI_LEGACYRENDER_CONF_BANNERS',
    'description' => '_MI_LEGACYRENDER_CONF_BANNERS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0
];
$modversion['config'][]= [
    'name'        => 'pagetitle',
    'title'       => '_MI_LR_PAGETITLE_FORMAT',
    'description' => '_MI_LR_PAGETITLE_FORMAT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '{modulename} {action} [pagetitle]:[/pagetitle] {pagetitle}'
];
$modversion['config'][]= [
    'name'        => 'meta_keywords',
    'title'       => '_MI_LR_META_KEYWORDS',
    'description' => '_MI_LR_META_KEYWORDS_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => 'application, web, website, best, top, content, internet, free, news, components, design, development, frameworks, javascript, libraries'
];

$modversion['config'][]= [
    'name'        => 'meta_description',
    'title'       => '_MI_LR_META_DESCRIPTION',
    'description' => '_MI_LR_META_DESCRIPTION_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => 'XCL is a modular architecture and extensible web application platform written in PHP.'
];

$modversion['config'][]= [
    'name'        => 'meta_robots',
    'title'       => '_MI_LR_META_ROBOTS',
    'description' => '_MI_LR_META_ROBOTS_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => ['_MI_LR_ROBOT_INDEXFOLLOW' => 'index,follow', '_MI_LR_ROBOT_NOINDEXFOLLOW' => 'noindex,follow', '_MI_LR_ROBOT_INDEXNOFOLLOW' => 'index,nofollow', '_MI_LR_ROBOT_NOINDEXNOFOLLOW' => 'noindex,nofollow'],
    'default'     => 'index,follow'
];

$modversion['config'][]= [
    'name'        => 'meta_rating',
    'title'       => '_MI_LR_META_RATING',
    'description' => '_MI_LR_META_RATING_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => ['_MI_LR_ROBOT_METAOGEN' => 'general', '_MI_LR_ROBOT_METAO14YRS' => '14 years', '_MI_LR_ROBOT_METAOREST' => 'restricted', '_MI_LR_ROBOT_METAOMAT' => 'mature'],
    'default'     => 'general'
];

$modversion['config'][]= [
    'name'        => 'meta_author',
    'title'       => '_MI_LR_META_AUTHOR',
    'description' => '_MI_LR_META_AUTHOR_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'XOOPSCube Project'
];

$modversion['config'][]= [
    'name'        => 'meta_copyright',
    'title'       => '_MI_LR_META_COPYRIGHT',
    'description' => '_MI_LR_META_COPYRIGHT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'Copyright &copy; 2001-2022'
];


// Meta Webmaster Tools
$modversion['config'][]= [
    'name'        => 'meta_bing',
    'title'       => 'Bing meta tag',
    'description' => 'Log into Bing Webmaster Tools and get the meta tag option to verify your site.',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => ''
];

$modversion['config'][]= [
    'name'        => 'meta_google',
    'title'       => 'Webmaster Google',
    'description' => 'Log into Google Search Console and get the meta tag verification',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => ''
];

$modversion['config'][]= [
    'name'        => 'meta_yandex',
    'title'       => 'Webmaster Yandex',
    'description' => 'Log into Yandex search console and get the meta tag verification',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => ''
];

$modversion['config'][]= [
    'name'        => 'meta_fb_app',
    'title'       => 'Facebook App',
    'description' => 'Get your Facebook App ID',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => ''
];

$modversion['config'][]= [
    'name'        => 'meta_twitter_site',
    'title'       => 'Twitter site user',
    'description' => 'Get your Twitter site ID',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '@cubson'
];

// Settings
$modversion['config'][]= [
    'name'        => 'footer',
    'title'       => '_MI_LR_FOOTER',
    'description' => '_MI_LR_FOOTER_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => 'Powered by XCL 2.3.1 © 2005-2023 <a href="https://github.com/xoopscube/" rel="noopener">The XOOPSCube Project</a>'
];



$modversion['config'][]= [
    'name'        => 'css_file',
    'title'       => '_MI_LR_CSS_FILE',
    'description' => '_MI_LR_CSS_FILE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => XOOPS_URL . '/common/js/jquery-ui.min.css'
];


$modversion['config'][]= [
    'name'        => 'jquery_core',
    'title'       => '_MI_LR_JQUERY_CORE',
    'description' => '_MI_LR_JQUERY_CORE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => XOOPS_URL . '/common/js/jquery.min.js'
];

$modversion['config'][]= [
    'name'        => 'jquery_ui',
    'title'       => '_MI_LR_JQUERY_UI',
    'description' => '_MI_LR_JQUERY_UI_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => XOOPS_URL . '/common/js/jquery-ui.min.js'
];

$modversion['config'][]= [
    'name'        => 'feed_url',
    'title'       => '_MI_LR_FEED_URL',
    'description' => '_MI_LR_FEED_URL_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => ''
];
