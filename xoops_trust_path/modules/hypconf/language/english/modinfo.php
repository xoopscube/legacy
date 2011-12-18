<?php
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'hypconf' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define($constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref.'_NAME', 'HypCommon conf');

// A brief description of this module
define($constpref.'_DESC', 'Configure of HypCommonFunc.');

define($constpref.'_MSG_SAVED' , 'Config was saved correctly.');
define($constpref.'_COUSTOM_BLOCK' , 'Custom block');

// admin menus
define($constpref.'_ADMENU_CONTENTSADMIN' , 'Configuration Verify');
define($constpref.'_ADMENU_MAIN_SWITCH' , 'Main Switch');
define($constpref.'_ADMENU_K_TAI_CONF' , 'Setup for mobile');
define($constpref.'_ADMENU_MYBLOCKSADMIN' , 'Permissions Setting');
define($constpref.'_ADMENU_XPWIKI_RENDER', 'xpWiki renderer');
define($constpref.'_ADMENU_SPAM_BLOCK', 'SPAM blocker');

// notice error
define($constpref.'_MAIN_SWITCH_NOT_ENABLE', '"<b>$1</b>" is invalid with the main switch. In order to operate a setup here, please validate "<b>$1</b>" with a main switch.');

// main_switch
define($constpref.'_USE_SET_QUERY_WORDS', 'Set to a constant search words.');
define($constpref.'_USE_SET_QUERY_WORDS_DESC', '');
define($constpref.'_USE_WORDS_HIGHLIGHT', 'Highlight search words.');
define($constpref.'_USE_WORDS_HIGHLIGHT_DESC', 'It becomes effective when "Set to a constant search words" is "yes".<br />A highlight list is inserted directly under a &lt;body&gt; tag. It will be inserted in the portion to insert in arbitrary places if "&lt;!--HIGHLIGHT_SEARCH_WORD--&gt;" is described in a theme. ');
define($constpref.'_USE_PROXY_CHECK', 'Check that the proxy when posting.');
define($constpref.'_USE_PROXY_CHECK_DESC', '');
define($constpref.'_INPUT_FILTER_STRENGTH', 'GET, POST control character filter intensity');
define($constpref.'_INPUT_FILTER_STRENGTH_DESC', '');
define($constpref.'_USE_DEPENDENCE_FILTER', 'Environment-dependent character filter.');
define($constpref.'_USE_DEPENDENCE_FILTER_DESC', 'This is a feature of the Japanese environment.');
define($constpref.'_USE_POST_SPAM_FILTER', 'SPAM Filter.');
define($constpref.'_USE_POST_SPAM_FILTER_DESC', '');
define($constpref.'_POST_SPAM_TRAP_SET', 'Honeypots (traps for Bot) to automatically insert.');
define($constpref.'_POST_SPAM_TRAP_SET_DESC', '');
define($constpref.'_USE_K_TAI_RENDER', 'To enable the feature on mobile phones.');
define($constpref.'_USE_K_TAI_RENDER_DESC', '');
define($constpref.'_USE_SMART_REDIRECT', 'To enable smart redirection.');
define($constpref.'_USE_SMART_REDIRECT_DESC', '');
// main_switch value
define($constpref.'_INPUT_FILTER_STRENGTH_0', 'Controls allowed in a non-NULL');
define($constpref.'_INPUT_FILTER_STRENGTH_1', '\t, \r, \n and EMOJI of SoftBank are allowed in controls');
define($constpref.'_INPUT_FILTER_STRENGTH_2', '\t, \r and \n are allowed in controls');

// k_tai_render
define($constpref.'_UA_REGEX', 'User agent');
define($constpref.'_UA_REGEX_DESC', 'User agent to handle the mobile component. PCRE (compatible Perl) Regular Expressions.');
define($constpref.'_JQM_PROFILES', 'jQuery Mobile');
define($constpref.'_JQM_PROFILES_DESC', 'Profile name to apply jQuery Mobile. Them separated by comma. If the profile name defined in the renderer to mobile phones, "docomo, au, softbank, willcom, android, iphone, ipod, ipad, and windows mobile" you can use.');
define($constpref.'_JQM_THEME', 'jqm Theme');
define($constpref.'_JQM_THEME_DESC', 'JQuery Mobile theme of the entire page. In normal condition "a, b, c, d, e" is valid.');
define($constpref.'_JQM_THEME_CONTENT', 'Main section');
define($constpref.'_JQM_THEME_CONTENT_DESC', 'jQuery Mobile theme applied to the main contents.');
define($constpref.'_JQM_THEME_BLOCK', 'Block section');
define($constpref.'_JQM_THEME_BLOCK_DESC', 'JQuery Mobile theme applied to the block.');
define($constpref.'_JQM_CSS', 'jqm Add CSS');
define($constpref.'_JQM_CSS_DESC', 'Creation of CSS for themes is easy if <a href="http://jquerymobile.com/themeroller/" target="_blank">ThemeRoller | jQuery Mobile</a> or <a href="http://as001.productscape.com/themeroller.cfm" target="_blank">jQuery Mobile Themeroller</a> is used.');
define($constpref.'_JQM_REMOVE_FLASH' , 'Remove Flash (jqm)');
define($constpref.'_JQM_REMOVE_FLASH_DESC' , 'Profile name to remove Flash. Them separated by comma. If the profile name defined in the renderer to mobile phones, "docomo, au, softbank, willcom, android, iphone, ipod, ipad, and windows mobile" you can use.');
define($constpref.'_JQM_RESOLVE_TABLE' , 'Expand nested table (jqm)');
define($constpref.'_JQM_RESOLVE_TABLE_DESC' , 'Expand the table that is nested when applying jQuery Mobile.');
define($constpref.'_JQM_IMAGE_CONVERT' , 'IMG width max [px] (jqm)');
define($constpref.'_JQM_IMAGE_CONVERT_DESC' , 'Shrink Image to specification size(width) at the time of jQuery Mobile application. "0" is disable.');
define($constpref.'_DISABLEDBLOCKIDS', 'Disable Block');
define($constpref.'_DISABLEDBLOCKIDS_DESC', 'Disable the selected block when the mobile access.');
define($constpref.'_LIMITEDBLOCKIDS', 'Alive Block');
define($constpref.'_LIMITEDBLOCKIDS_DESC', 'Enables the selected block when the mobile access. If you select a block, the block is not selected is disabled. If you do not specify any filtering is not.');
define($constpref.'_SHOWBLOCKIDS', 'Expand Block');
define($constpref.'_SHOWBLOCKIDS_DESC', 'Block mobile access to view every time. <br />When using jQuery Mobile will initially be deployed collapse. <br />In a conventional mobile phone selected block is displayed, the non-selected block is the link to view the block.');

// xpwiki_render
define($constpref.'_XPWIKI_RENDER_NONE', 'Do not use');
define($constpref.'_XPWIKI_RENDER_DIRNAME', 'xpWiki renderer');
define($constpref.'_XPWIKI_RENDER_DIRNAME_DESC', 'Please select a "xpWiki" to be used as xpWiki renderer in the site-wide.<br />By using the site-wide xpWiki renderer, can be use xpWiki (PukiWiki) text formatter.');
define($constpref.'_XPWIKI_RENDER_USE_WIKIHELPER', 'Site-wide Wiki Helper');
define($constpref.'_XPWIKI_RENDER_USE_WIKIHELPER_DESC', 'If "Yes" is chosen, will be able to uses Wiki helper & Rich editor at site-wide.');
define($constpref.'_XPWIKI_RENDER_NOTUSE_WIKIHELPER_MODULES', 'Disabled Wiki Helper');
define($constpref.'_XPWIKI_RENDER_NOTUSE_WIKIHELPER_MODULES_DESC', 'Please choose the module which sets a site wide Wiki helper to disabled.');
define($constpref.'_REQUERE_XCL', 'This setting is only available in XOOPS Cube Legacy system.');
define($constpref.'_XCL_REQUERE_2_2_1', 'This feature will be available since XOOPS Cube Legacy 2.2.1 .However,  If you have a edited "class/module.textsanitizer.php" for this feature already. Please ignore this message.');
define($constpref.'_TEXTFILTER_ALREADY_EXISTS', 'There is a "SetupHyp_TextFilter.class.php" in "preload" directory so this setting will be disabled.');

// spam_block
define($constpref.'_USE_MAIL_NOTIFY', 'POST SPAM mail notification 0: No 1: determination only SPAM, 2: all');
define($constpref.'_USE_MAIL_NOTIFY_DESC', '');
define($constpref.'_SEND_MAIL_INTERVAL', 'Digest interval in minutes (0: from time to time send)');
define($constpref.'_SEND_MAIL_INTERVAL_DESC', '');
define($constpref.'_POST_SPAM_A', 'Points per tag &lt;a&gt;');
define($constpref.'_POST_SPAM_A_DESC', '');
define($constpref.'_POST_SPAM_BB', 'Points per BBcode link');
define($constpref.'_POST_SPAM_BB_DESC', '');
define($constpref.'_POST_SPAM_URL', 'Points per URL');
define($constpref.'_POST_SPAM_URL_DESC', '');
define($constpref.'_POST_SPAM_UNHOST', 'Addition point of unknown Host');
define($constpref.'_POST_SPAM_UNHOST_DESC', '');
define($constpref.'_POST_SPAM_HOST', 'Addition point of Spam Host');
define($constpref.'_POST_SPAM_HOST_DESC', '');
define($constpref.'_POST_SPAM_WORD', 'Addition point of Spam Word');
define($constpref.'_POST_SPAM_WORD_DESC', '');
define($constpref.'_POST_SPAM_FILED', 'Addition point of Honeypot input');
define($constpref.'_POST_SPAM_FILED_DESC', '');
define($constpref.'_POST_SPAM_TRAP', 'Filed name of Honeypot');
define($constpref.'_POST_SPAM_TRAP_DESC', '');
define($constpref.'_POST_SPAM_USER', 'Threshold of Spam judging: Login user');
define($constpref.'_POST_SPAM_USER_DESC', '');
define($constpref.'_POST_SPAM_GUEST', 'Threshold of Spam judging: Guest');
define($constpref.'_POST_SPAM_GUEST_DESC', '');
define($constpref.'_POST_SPAM_BADIP', 'Threshold value registered to an access refusal list');
define($constpref.'_POST_SPAM_BADIP_DESC', '');
define($constpref.'_POST_SPAM_BADIP_TTL', '<b>Protector cooperation</b>: Denial of Access Denied duration [s] (0: unlimited, null: unused Protector)');
define($constpref.'_POST_SPAM_BADIP_TTL_DESC', '');
define($constpref.'_POST_SPAM_BADIP_FOREVER', '<b>Protector cooperation</b>: Access denied permanent threshold');
define($constpref.'_POST_SPAM_BADIP_FOREVER_DESC', '');
define($constpref.'_POST_SPAM_BADIP_TTL0', '<b>Protector cooperation</b>: Indefinite duration access denied [s] (0: indeed indefinitely)');
define($constpref.'_POST_SPAM_BADIP_TTL0_DESC', '');

}
