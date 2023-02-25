<?php
/**
 *
 * @package Legacy
 * @version $Id: xoops_version.php,v 1.13 2008/09/25 14:31:43 kilica Exp $
 * @copyright (c) 2005-2023 The XOOPSCube Project
 * @license   GPL v2.0
 *
 */

$modversion['dirname']          = 'legacy';
$modversion['name']             = _MI_LEGACY_NAME;
$modversion['version']          = '2.32';
$modversion['detailed_version'] = '2.32.1';
$modversion['description']      = _MI_LEGACY_NAME_DESC;
$modversion['author']           = 'The XOOPSCube Project Team';
$modversion['credits']          = 'The XOOPSCube Project Team';
$modversion['help']             = 'help.html';
$modversion['license']          = 'GPL see LICENSE';
$modversion['image']            = 'images/module_settings.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['cube_style']       = true;

// Custom installer
$modversion['legacy_installer']['updater']['class'] = 'ModuleUpdater';
$modversion['legacy_installer']['updater']['filepath'] = XOOPS_LEGACY_PATH . '/admin/class/Legacy_Updater.class.php';

//
// Database Setting
//

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Templates
$modversion['templates'][1]['file'] = 'legacy_misc_ssllogin.html';
$modversion['templates'][1]['description'] = 'Template SSL login';
$modversion['templates'][2]['file'] = 'legacy_misc_smilies.html';
$modversion['templates'][2]['description'] = '';
$modversion['templates'][3]['file'] = 'legacy_search_form.html';
$modversion['templates'][3]['description'] = 'Template Search Form';
$modversion['templates'][4]['file'] = 'legacy_comment_edit.html';
$modversion['templates'][4]['description'] = '';
$modversion['templates'][5]['file'] = 'legacy_xoops_result.html';
$modversion['templates'][5]['description'] = '';
$modversion['templates'][6]['file'] = 'legacy_xoops_error.html';
$modversion['templates'][6]['description'] = '';
$modversion['templates'][7]['file'] = 'legacy_xoops_confirm.html';
$modversion['templates'][7]['description'] = '';
$modversion['templates'][8]['file'] = 'legacy_comment_navi.html';
$modversion['templates'][8]['description'] = '';
$modversion['templates'][9]['file'] = 'legacy_comment.html';
$modversion['templates'][9]['description'] = '';
$modversion['templates'][10]['file'] = 'legacy_comments_flat.html';
$modversion['templates'][10]['description'] = '';
$modversion['templates'][11]['file'] = 'legacy_comments_nest.html';
$modversion['templates'][11]['description'] = '';
$modversion['templates'][12]['file'] = 'legacy_comments_thread.html';
$modversion['templates'][12]['description'] = '';
$modversion['templates'][13]['file'] = 'legacy_notification_select.html';
$modversion['templates'][13]['description'] = '';
$modversion['templates'][14]['file'] = 'legacy_dummy.html';
$modversion['templates'][14]['description'] = '';
$modversion['templates'][15]['file'] = 'legacy_redirect.html';
$modversion['templates'][15]['description'] = 'Template Redirect';
$modversion['templates'][16]['file'] = 'legacy_image_list.html';
$modversion['templates'][16]['description'] = '';
$modversion['templates'][17]['file'] = 'legacy_image_upload.html';
$modversion['templates'][17]['description'] = '';
$modversion['templates'][18]['file'] = 'legacy_rss.html';
$modversion['templates'][18]['description'] = '';
$modversion['templates'][19]['file'] = 'legacy_search_results.html';
$modversion['templates'][19]['description'] = 'Template Search Results';
$modversion['templates'][20]['file'] = 'legacy_search_showall.html';
$modversion['templates'][20]['description'] = '';
$modversion['templates'][21]['file'] = 'legacy_search_showallbyuser.html';
$modversion['templates'][21]['description'] = '';
$modversion['templates'][22]['file'] = 'legacy_notification_list.html';
$modversion['templates'][22]['description'] = '';
$modversion['templates'][23]['file'] = 'legacy_notification_delete.html';
$modversion['templates'][23]['description'] = '';
$modversion['templates'][24]['file'] = 'legacy_notification_select_form.html';
$modversion['templates'][24]['description'] = '';
$modversion['templates'][25]['file'] = 'legacy_misc_friend.html';
$modversion['templates'][25]['description'] = '';
$modversion['templates'][26]['file'] = 'legacy_misc_friend_success.html';
$modversion['templates'][26]['description'] = '';
$modversion['templates'][27]['file'] = 'legacy_misc_friend_error.html';
$modversion['templates'][27]['description'] = '';
$modversion['templates'][28]['file'] = 'legacy_xoopsform_checkbox.html';
$modversion['templates'][28]['description'] = 'The embedded template for checkbox.';
$modversion['templates'][29]['file'] = 'legacy_xoopsform_color.html';
$modversion['templates'][29]['description'] = 'The embedded template for color.';
$modversion['templates'][30]['file'] = 'legacy_xoopsform_button.html';
$modversion['templates'][30]['description'] = 'The embedded template for button.';
$modversion['templates'][31]['file'] = 'legacy_xoopsform_text.html';
$modversion['templates'][31]['description'] = 'The embedded template for text.';
$modversion['templates'][32]['file'] = 'legacy_xoopsform_select.html';
$modversion['templates'][32]['description'] = 'The embedded template for select.';
$modversion['templates'][33]['file'] = 'legacy_xoopsform_file.html';
$modversion['templates'][33]['description'] = 'The embedded template for file.';
$modversion['templates'][34]['file'] = 'legacy_xoopsform_hidden.html';
$modversion['templates'][34]['description'] = 'The embedded template for hidden.';
$modversion['templates'][35]['file'] = 'legacy_xoopsform_radio.html';
$modversion['templates'][35]['description'] = 'The embedded template for radio.';
$modversion['templates'][36]['file'] = 'legacy_xoopsform_label.html';
$modversion['templates'][36]['description'] = 'The embedded template for label.';
$modversion['templates'][37]['file'] = 'legacy_xoopsform_password.html';
$modversion['templates'][37]['description'] = 'The embedded template for password.';
$modversion['templates'][38]['file'] = 'legacy_xoopsform_textarea.html';
$modversion['templates'][38]['description'] = 'The embedded template for textarea.';
$modversion['templates'][39]['file'] = 'legacy_xoopsform_simpleform.html';
$modversion['templates'][39]['description'] = 'The embedded template for the simple form.';
$modversion['templates'][40]['file'] = 'legacy_xoopsform_tableform.html';
$modversion['templates'][40]['description'] = 'The embedded template for the table form.';
$modversion['templates'][41]['file'] = 'legacy_xoopsform_themeform.html';
$modversion['templates'][41]['description'] = 'The embedded template for the theme form.';
$modversion['templates'][42]['file'] = 'legacy_xoopsform_elementtray.html';
$modversion['templates'][42]['description'] = 'The embedded template for the element tray.';
$modversion['templates'][43]['file'] = 'legacy_xoopsform_textdateselect.html';
$modversion['templates'][43]['description'] = 'The embedded template for the text date select.';
$modversion['templates'][44]['file'] = 'legacy_xoopsform_dhtmltextarea.html';
$modversion['templates'][44]['description'] = 'The embedded template for the dhtml textarea.';
$modversion['templates'][45]['file'] = 'legacy_xoopsform_opt_smileys.html';
$modversion['templates'][45]['description'] = 'The embedded template for the smiles list of dhtml textarea.';
$modversion['templates'][46]['file'] = 'legacy_xoopsform_opt_validationjs.html';
$modversion['templates'][46]['description'] = 'The embedded template for the javascriot validation of input value.';
$modversion['templates'][47]['file'] = 'legacy_xoopsform_grouppermform.html';
$modversion['templates'][47]['description'] = 'The embedded template for the groupperm form.';
$modversion['templates'][48]['file'] = 'legacy_inc_tree.html';
$modversion['templates'][48]['description'] = 'legacy_tree default template';
$modversion['templates'][49]['file'] = 'legacy_inc_tag_select.html';
$modversion['templates'][49]['description'] = 'legacy_tag_select default template';
$modversion['templates'][50]['file'] = 'legacy_inc_tag_cloud.html';
$modversion['templates'][50]['description'] = 'legacy_tag_cloud default template';
$modversion['templates'][51]['file'] = 'legacy_redirect_function.html';
$modversion['templates'][51]['description'] = 'Replace direct Xoops2 system';
$modversion['templates'][52]['file'] = 'legacy_redirect_front_function.html';
$modversion['templates'][52]['description'] = 'Redirect top page if module content is not selected.';
$modversion['templates'][52]['file'] = 'legacy_app_start_page.html';
$modversion['templates'][52]['description'] = 'App start page if module is not selected.';
$modversion['templates'][53]['file'] = 'legacy_site_closed.html';
$modversion['templates'][53]['description'] = 'Used when the site is closed. Maintenance mode, coming soon, etc.';

// Menu
$modversion['hasMain'] = 0;

// Blocks
$modversion['blocks'][1]['func_num'] = 1;
$modversion['blocks'][1]['file'] = 'legacy_usermenu.php';
$modversion['blocks'][1]['name'] = _MI_LEGACY_BLOCK_USERMENU_NAME;
$modversion['blocks'][1]['description'] = _MI_LEGACY_BLOCK_USERMENU_DESC;
$modversion['blocks'][1]['show_func'] = 'b_legacy_usermenu_show';
$modversion['blocks'][1]['template'] = 'legacy_block_usermenu.html';
$modversion['blocks'][1]['visible_any'] = true;
$modversion['blocks'][1]['show_all_module'] = true;

$modversion['blocks'][2]['func_num'] = 2;
$modversion['blocks'][2]['file'] = 'legacy_mainmenu.php';
$modversion['blocks'][2]['name'] = _MI_LEGACY_BLOCK_MAINMENU_NAME;
$modversion['blocks'][2]['description'] = _MI_LEGACY_BLOCK_MAINMENU_DESC;
$modversion['blocks'][2]['show_func'] = 'b_legacy_mainmenu_show';
$modversion['blocks'][2]['edit_func'] = 'b_legacy_mainmenu_edit';
$modversion['blocks'][2]['template'] = 'legacy_block_mainmenu.html';
$modversion['blocks'][2]['visible_any'] = true;
$modversion['blocks'][2]['show_all_module'] = true;
$modversion['blocks'][2]['options'] = '	0|1';

$modversion['blocks'][3]['func_num'] = 3;
$modversion['blocks'][3]['file'] = 'legacy_search.php';
$modversion['blocks'][3]['name'] = _MI_LEGACY_BLOCK_SEARCH_NAME;
$modversion['blocks'][3]['description'] = _MI_LEGACY_BLOCK_SEARCH_DESC;
$modversion['blocks'][3]['show_func'] = 'b_legacy_search_show';
$modversion['blocks'][3]['template'] = 'legacy_block_search.html';
$modversion['blocks'][3]['show_all_module'] = true;

$modversion['blocks'][4]['func_num'] = 4;
$modversion['blocks'][4]['file'] = 'legacy_waiting.php';
$modversion['blocks'][4]['name'] = _MI_LEGACY_BLOCK_WAITING_NAME;
$modversion['blocks'][4]['description'] = _MI_LEGACY_BLOCK_WAITING_DESC;
$modversion['blocks'][4]['show_func'] = 'b_legacy_waiting_show';
$modversion['blocks'][4]['template'] = 'legacy_block_waiting.html';

$modversion['blocks'][5]['func_num'] = 5;
$modversion['blocks'][5]['file'] = 'legacy_siteinfo.php';
$modversion['blocks'][5]['name'] = _MI_LEGACY_BLOCK_SITEINFO_NAME;
$modversion['blocks'][5]['description'] = _MI_LEGACY_BLOCK_SITEINFO_DESC;
$modversion['blocks'][5]['show_func'] = 'b_legacy_siteinfo_show';
$modversion['blocks'][5]['edit_func'] = 'b_legacy_siteinfo_edit';
$modversion['blocks'][5]['options'] = '320|270|s_poweredby.png|1';
$modversion['blocks'][5]['template'] = 'legacy_block_siteinfo.html';
$modversion['blocks'][5]['show_all_module'] = true;

$modversion['blocks'][6]['func_num'] = 6;
$modversion['blocks'][6]['file'] = 'legacy_comments.php';
$modversion['blocks'][6]['name'] = _MI_LEGACY_BLOCK_COMMENTS_NAME;
$modversion['blocks'][6]['description'] = _MI_LEGACY_BLOCK_COMMENTS_DESC;
$modversion['blocks'][6]['show_func'] = 'b_legacy_comments_show';
$modversion['blocks'][6]['options'] = '10';
$modversion['blocks'][6]['edit_func'] = 'b_legacy_comments_edit';
$modversion['blocks'][6]['template'] = 'legacy_block_comments.html';
$modversion['blocks'][6]['show_all_module'] = true;

$modversion['blocks'][7]['func_num'] = 7;
$modversion['blocks'][7]['file'] = 'legacy_notification.php';
$modversion['blocks'][7]['name'] = _MI_LEGACY_BLOCK_NOTIFICATION_NAME;
$modversion['blocks'][7]['description'] = _MI_LEGACY_BLOCK_NOTIFICATION_DESC;
$modversion['blocks'][7]['show_func'] = 'b_legacy_notification_show';
$modversion['blocks'][7]['template'] = 'legacy_block_notification.html';

$modversion['blocks'][8]['func_num'] = 8;
$modversion['blocks'][8]['file'] = 'legacy_themes.php';
$modversion['blocks'][8]['name'] = _MI_LEGACY_BLOCK_THEMES_NAME;
$modversion['blocks'][8]['description'] = _MI_LEGACY_BLOCK_THEMES_DESC;
$modversion['blocks'][8]['show_func'] = 'b_legacy_themes_show';
$modversion['blocks'][8]['options'] = '1|240';
$modversion['blocks'][8]['edit_func'] = 'b_legacy_themes_edit';
$modversion['blocks'][8]['template'] = 'legacy_block_themes.html';
$modversion['blocks'][8]['visible_any'] = true;
$modversion['blocks'][8]['show_all_module'] = true;
