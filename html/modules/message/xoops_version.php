<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

if (!isset($root)) {
    $root = XCube_Root::getSingleton();
}

$mydirpath = basename(dirname(dirname(__FILE__))) ;

$modversion['name'] = _MI_MESSAGE_NAME;
$modversion['dirname'] = basename(dirname(__FILE__));
$modversion['version'] = 2.3;
$modversion['description'] = _MI_MESSAGE_DESC;
$modversion['author'] = 'Marijuana';
$modversion['credits'] = "XOOPS Cube Project";
$modversion['help'] = "help.html";
$modversion['license'] = "MIT LICENSE";
$modversion['image']       = 'images/module_icon.png';
$modversion['mcl_update'] = 'message';

$modversion['cube_style'] = true;
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][] = '{prefix}_{dirname}_inbox';
$modversion['tables'][] = '{prefix}_{dirname}_outbox';
$modversion['tables'][] = '{prefix}_{dirname}_users';

$modversion['legacy_installer']['installer']['class'] = 'myInstaller';
$modversion['legacy_installer']['updater']['class'] = 'myUpdater';

$modversion['templates'][] = array('file' => 'message_inboxlist.html');
$modversion['templates'][] = array('file' => 'message_inboxview.html');
$modversion['templates'][] = array('file' => 'message_outboxlist.html');
$modversion['templates'][] = array('file' => 'message_outboxview.html');
$modversion['templates'][] = array('file' => 'message_new.html');
$modversion['templates'][] = array('file' => 'message_usersearch.html');
$modversion['templates'][] = array('file' => 'message_favorites.html');
$modversion['templates'][] = array('file' => 'message_settings.html');
$modversion['templates'][] = array('file' => 'message_userinfo.html');
$modversion['templates'][] = array('file' => 'message_blaclist.html');

$modversion['hasMain'] = 1;
$modversion['sub'][] = array('name' => _MI_MESSAGE_SUB_SEND, 'url' => 'index.php?action=send');
$modversion['sub'][] = array('name' => _MI_MESSAGE_SUB_NEW, 'url' => 'index.php?action=new');
if ($root->mServiceManager->getService('UserSearch') != null) {
    $modversion['sub'][] = array('name' => _MI_MESSAGE_SUB_SEARCH, 'url' => 'index.php?action=search');
    $modversion['sub'][] = array('name' => _MI_MESSAGE_SUB_FAVORITES, 'url' => 'index.php?action=favorites');
}
$modversion['sub'][] = array('name' => _MI_MESSAGE_SUB_SETTINGS, 'url' => 'index.php?action=settings');


$modversion['hasAdmin'] = 1;

$modversion['config'][0]['name']        = 'pagenum';
$modversion['config'][0]['title']        = '_MI_MESSAGE_PAGENUM';
$modversion['config'][0]['description'] = '_MI_MESSAGE_PAGENUM_DESC';
$modversion['config'][0]['formtype']    = 'textbox';
$modversion['config'][0]['valuetype']    = 'int';
$modversion['config'][0]['default']    = '15';

$modversion['config'][1]['name']        = 'savedays';
$modversion['config'][1]['title']        = '_MI_MESSAGE_SAVEDAYS';
$modversion['config'][1]['description'] = '_MI_MESSAGE_SAVEDAYS_DESC';
$modversion['config'][1]['formtype']    = 'textbox';
$modversion['config'][1]['valuetype']    = 'int';
$modversion['config'][1]['default']    = '90';

$modversion['config'][2]['name']        = 'newalert';
$modversion['config'][2]['title']        = '_MI_MESSAGE_NEWALERT';
$modversion['config'][2]['description'] = '_MI_MESSAGE_NEWALERT_DESC';
$modversion['config'][2]['formtype']    = 'yesno';
$modversion['config'][2]['valuetype']    = 'int';
$modversion['config'][2]['default']    = '1';
/*
$modversion['config'][3]['name']		= 'userinfo';
$modversion['config'][3]['title']		= '_MI_MESSAGE_USERINFO';
$modversion['config'][3]['description'] = '_MI_MESSAGE_USERINFO_DESC';
$modversion['config'][3]['formtype']	= 'yesno';
$modversion['config'][3]['valuetype']	= 'int';
$modversion['config'][3]['default'] 	= '1';
*/
$modversion['config'][4]['name']        = 'dletype';
$modversion['config'][4]['title']        = '_MI_MESSAGE_DELTYPE';
$modversion['config'][4]['description'] = '_MI_MESSAGE_DELTYPE_DESC';
$modversion['config'][4]['formtype']    = 'yesno';
$modversion['config'][4]['valuetype']    = 'int';
$modversion['config'][4]['default']    = '1';

$modversion['config'][5]['name']        = 'usepm';
$modversion['config'][5]['title']        = '_MI_MESSAGE_DEFAULT_USEPM';
$modversion['config'][5]['description'] = '_MI_MESSAGE_DEFAULT_USEPM_DESC';
$modversion['config'][5]['formtype']    = 'yesno';
$modversion['config'][5]['valuetype']    = 'int';
$modversion['config'][5]['default']    = '1';

$modversion['config'][6]['name']        = 'tomail';
$modversion['config'][6]['title']        = '_MI_MESSAGE_DEFAULT_TOMAIL';
$modversion['config'][6]['description'] = '_MI_MESSAGE_DEFAULT_TOMAIL_DESC';
$modversion['config'][6]['formtype']    = 'yesno';
$modversion['config'][6]['valuetype']    = 'int';
$modversion['config'][6]['default']    = '0';

$modversion['config'][7]['name']        = 'viewmsm';
$modversion['config'][7]['title']        = '_MI_MESSAGE_DEFAULT_VIEWMSM';
$modversion['config'][7]['description'] = '_MI_MESSAGE_DEFAULT_VIEWMSM_DESC';
$modversion['config'][7]['formtype']    = 'yesno';
$modversion['config'][7]['valuetype']    = 'int';
$modversion['config'][7]['default']    = '0';

$modversion['blocks'][0]['file']        = 'message_block.class.php';
$modversion['blocks'][0]['name']        = _MI_MESSAGE_BLOCK_NAME;
$modversion['blocks'][0]['description'] = '';
$modversion['blocks'][0]['show_func']    = '';
$modversion['blocks'][0]['class']        = 'Block';
$modversion['blocks'][0]['template']    = 'message_block_template.html';
$modversion['blocks'][0]['visible']    = '1';
$modversion['blocks'][0]['func_num']    = 1;
