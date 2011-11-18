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

// main_switch
define($constpref.'_USE_SET_QUERY_WORDS', 'Set to a constant search words.');
define($constpref.'_USE_SET_QUERY_WORDS_DESC', '');
define($constpref.'_USE_WORDS_HIGHLIGHT', 'Highlight search words.');
define($constpref.'_USE_WORDS_HIGHLIGHT_DESC', '');
define($constpref.'_USE_PROXY_CHECK', 'Check that the proxy when posting.');
define($constpref.'_USE_PROXY_CHECK_DESC', '');
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

// k_tai_render
define($constpref.'_UA_REGEX', 'User agent');
define($constpref.'_UA_REGEX_DESC', 'User agent to handle the mobile component. PCRE (compatible Perl) Regular Expressions.');
define($constpref.'_JQUERY_PROFILES', 'jQuery Mobile');
define($constpref.'_JQUERY_PROFILES_DESC', 'Profile name to apply jQuery Mobile. Them separated by comma. If the profile name defined in the renderer to mobile phones, "docomo, au, softbank, willcom, android, iphone, ipod, ipad, and windows mobile" you can use.');
define($constpref.'_JQUERY_THEME', 'jqm Theme');
define($constpref.'_JQUERY_THEME_DESC', 'JQuery Mobile theme of the entire page. In normal condition "a, b, c, d, e" is valid.');
define($constpref.'_JQUERY_THEME_CONTENT', 'Main section');
define($constpref.'_JQUERY_THEME_CONTENT_DESC', 'jQuery Mobile theme applied to the main contents.');
define($constpref.'_JQUERY_THEME_BLOCK', 'Block section');
define($constpref.'_JQUERY_THEME_BLOCK_DESC', 'JQuery Mobile theme applied to the block.');
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

}
