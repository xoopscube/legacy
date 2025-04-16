<?php
/**
 * Protector module for XCL
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster (XCL/PHP8)
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// language file (modinfo.php)
if ( file_exists( __DIR__ . '/language/' . @$GLOBALS['xoopsConfig']['language'] . '/modinfo.php' ) ) {
	include __DIR__ . '/language/' . @$GLOBALS['xoopsConfig']['language'] . '/modinfo.php';
} elseif ( file_exists( __DIR__ . '/language/english/modinfo.php' ) ) {
	include __DIR__ . '/language/english/modinfo.php';
}

$constpref = '_MI_' . strtoupper( $mydirname );

// Manifesto
$modversion['dirname']          = $mydirname;
$modversion['name']             = constant( $constpref . '_NAME' );
$modversion['version']          = '4.00';
$modversion['detailed_version'] = '4.00.0';
$modversion['description']      = constant( $constpref . '_DESC' );
$modversion['author']           = 'GIJ=CHECKMATE, Nuno Luciano aka Gigamaster (XCL/PHP8)';
$modversion['credits']          = 'PEAK Corp.(v.3.54) (https://www.peak.ne.jp/)';
$modversion['license']          = 'GPL';
$modversion['image']            = '/images/module_protector.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['official']         = 0;
$modversion['cube_style']       = true;

// SQL
// Tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false;
$modversion['tables']  = [];

// Admin menu and default admin page
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php?page=dashboard';
$modversion['adminmenu']  = 'admin/admin_menu.php';

// Templates

// Blocks
$modversion['blocks'] = [];

// Menu
$modversion['hasMain'] = 0;

// Search
$modversion['hasSearch'] = 0;

// Comments
$modversion['hasComments'] = 0;

// Notification

$modversion['hasNotification'] = 0;

// onInstall, onUpdate, onUninstall
$modversion['onInstall']   = 'oninstall.php';
$modversion['onUpdate']    = 'onupdate.php';
$modversion['onUninstall'] = 'onuninstall.php';

// Config Settings (only for modules that need config settings generated automatically)
$modversion['config'][1] = [
	'name'        => 'global_disabled',
	'title'       => $constpref . '_GLOBAL_DISBL',
	'description' => $constpref . '_GLOBAL_DISBLDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0',
	'options'     => [],
];
$modversion['config'][]  = [
	'name'        => 'default_lang',
	'title'       => $constpref . '_DEFAULT_LANG',
	'description' => $constpref . '_DEFAULT_LANGDSC',
	'formtype'    => 'text',
	'valuetype'   => 'text',
	'default'     => @$GLOBALS['xoopsConfig']['language'],
	'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'enable_manip_check',
    'title'       => $constpref . '_MANIPUCHECK',
    'description' => $constpref . '_MANIPUCHECKDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'manip_value',
    'title'       => $constpref . '_MANIPUVALUE',
    'description' => $constpref . '_MANIPUVALUEDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '',
    'options'     => [],
];
$modversion['config'][]  = [
	'name'        => 'log_level',
	'title'       => $constpref . '_LOG_LEVEL',
	'description' => '',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 255,
	'options'     => [
		$constpref . '_LOGLEVEL0'   => 0,
		$constpref . '_LOGLEVEL15'  => 15,
		$constpref . '_LOGLEVEL63'  => 63,
		$constpref . '_LOGLEVEL255' => 255
	],
];
$modversion['config'][]  = [
	'name'        => 'banip_time0',
	'title'       => $constpref . '_BANIP_TIME0',
	'description' => '',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => 86400,
	'options'     => [],
];
$modversion['config'][]  = [
	'name'        => 'banip_ipv6prefix',
	'title'       => $constpref . '_BANIP_IPV6PREFIX',
	'description' => $constpref . '_BANIP_IPV6PREFIXDSC',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => 64,
	'options'     => [],
];
$modversion['config'][]  = [
	'name'        => 'reliable_ips',
	'title'       => $constpref . '_RELIABLE_IPS',
	'description' => $constpref . '_RELIABLE_IPSDSC',
	'formtype'    => 'textarea',
	'valuetype'   => 'array',
	'default'     => '^192.168.|127.0.0.1',
	'options'     => [],
];
$modversion['config'][]  = [
	'name'        => 'session_fixed_topbit',
	'title'       => $constpref . '_HIJACK_TOPBIT',
	'description' => $constpref . '_HIJACK_TOPBITDSC',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => 24,
	'options'     => [],
];
$modversion['config'][]  = [
	'name'        => 'session_fixed_topbitv6',
	'title'       => $constpref . '_HIJACK_TOPBITV6',
	'description' => $constpref . '_HIJACK_TOPBITV6DSC',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => 128,
	'options'     => [],
];
$modversion['config'][]  = [
	'name'        => 'groups_denyipmove',
	'title'       => $constpref . '_HIJACK_DENYGP',
	'description' => $constpref . '_HIJACK_DENYGPDSC',
	'formtype'    => 'group_multi',
	'valuetype'   => 'array',
	'default'     => [ 1 ],
	'options'     => [],
];
$modversion['config'][]  = [
	'name'        => 'san_nullbyte',
	'title'       => $constpref . '_SAN_NULLBYTE',
	'description' => $constpref . '_SAN_NULLBYTEDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'die_badext',
	'title'       => $constpref . '_DIE_BADEXT',
	'description' => $constpref . '_DIE_BADEXTDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'contami_action',
	'title'       => $constpref . '_CONTAMI_ACTION',
	'description' => $constpref . '_CONTAMI_ACTIONDS',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 3,
	'options'     => [
		$constpref . '_OPT_NONE'     => 0,
		$constpref . '_OPT_EXIT'     => 3,
		$constpref . '_OPT_BIPTIME0' => 7,
		$constpref . '_OPT_BIP'      => 15
	],
];
$modversion['config'][] = [
	'name'        => 'isocom_action',
	'title'       => $constpref . '_ISOCOM_ACTION',
	'description' => $constpref . '_ISOCOM_ACTIONDSC',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => [
		$constpref . '_OPT_NONE'     => 0,
		$constpref . '_OPT_SAN'      => 1,
		$constpref . '_OPT_EXIT'     => 3,
		$constpref . '_OPT_BIPTIME0' => 7,
		$constpref . '_OPT_BIP'      => 15
	],
];
$modversion['config'][] = [
	'name'        => 'union_action',
	'title'       => $constpref . '_UNION_ACTION',
	'description' => $constpref . '_UNION_ACTIONDSC',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => [
		$constpref . '_OPT_NONE'     => 0,
		$constpref . '_OPT_SAN'      => 1,
		$constpref . '_OPT_EXIT'     => 3,
		$constpref . '_OPT_BIPTIME0' => 7,
		$constpref . '_OPT_BIP'      => 15
	],
];
$modversion['config'][] = [
	'name'        => 'id_forceintval',
	'title'       => $constpref . '_ID_INTVAL',
	'description' => $constpref . '_ID_INTVALDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '0',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'file_dotdot',
	'title'       => $constpref . '_FILE_DOTDOT',
	'description' => $constpref . '_FILE_DOTDOTDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => '1',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'bf_count',
	'title'       => $constpref . '_BF_COUNT',
	'description' => $constpref . '_BF_COUNTDSC',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => '10',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'bwlimit_count',
	'title'       => $constpref . '_BWLIMIT_COUNT',
	'description' => $constpref . '_BWLIMIT_COUNTDSC',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'dos_skipmodules',
	'title'       => $constpref . '_DOS_SKIPMODS',
	'description' => $constpref . '_DOS_SKIPMODSDSC',
	'formtype'    => 'text',
	'valuetype'   => 'text',
	'default'     => '',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'dos_expire',
	'title'       => $constpref . '_DOS_EXPIRE',
	'description' => $constpref . '_DOS_EXPIREDSC',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => '60',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'dos_f5count',
	'title'       => $constpref . '_DOS_F5COUNT',
	'description' => $constpref . '_DOS_F5COUNTDSC',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => '20',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'dos_f5action',
	'title'       => $constpref . '_DOS_F5ACTION',
	'description' => '',
	'formtype'    => 'select',
	'valuetype'   => 'text',
	'default'     => 'exit',
	'options'     => [
		$constpref . '_DOSOPT_NONE'     => 'none',
		$constpref . '_DOSOPT_SLEEP'    => 'sleep',
		$constpref . '_DOSOPT_EXIT'     => 'exit',
		$constpref . '_DOSOPT_BIPTIME0' => 'biptime0',
		$constpref . '_DOSOPT_BIP'      => 'bip',
		$constpref . '_DOSOPT_HTA'      => 'hta'
	],
];
$modversion['config'][] = [
	'name'        => 'dos_crcount',
	'title'       => $constpref . '_DOS_CRCOUNT',
	'description' => $constpref . '_DOS_CRCOUNTDSC',
	'formtype'    => 'text',
	'valuetype'   => 'int',
	'default'     => '40',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'dos_craction',
	'title'       => $constpref . '_DOS_CRACTION',
	'description' => '',
	'formtype'    => 'select',
	'valuetype'   => 'text',
	'default'     => 'exit',
	'options'     => [
		$constpref . '_DOSOPT_NONE'     => 'none',
		$constpref . '_DOSOPT_SLEEP'    => 'sleep',
		$constpref . '_DOSOPT_EXIT'     => 'exit',
		$constpref . '_DOSOPT_BIPTIME0' => 'biptime0',
		$constpref . '_DOSOPT_BIP'      => 'bip',
		$constpref . '_DOSOPT_HTA'      => 'hta'
	],
];
$modversion['config'][] = [
	'name'        => 'dos_crsafe',
	'title'       => $constpref . '_DOS_CRSAFE',
	'description' => $constpref . '_DOS_CRSAFEDSC',
	'formtype'    => 'text',
	'valuetype'   => 'text',
	'default'     => '/(msnbot|Googlebot|Yahoo! Slurp)/i',
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'bip_except',
	'title'       => $constpref . '_BIP_EXCEPT',
	'description' => $constpref . '_BIP_EXCEPTDSC',
	'formtype'    => 'group_multi',
	'valuetype'   => 'array',
	'default'     => [ 1 ],
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'disable_features',
	'title'       => $constpref . '_DISABLES',
	'description' => '',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => [ 'xmlrpc' => 1, 'xmlrpc + 2.0.9.2 bugs' => 1025, '_NONE' => 0 ],
];
$modversion['config'][] = [
	'name'        => 'enable_dblayertrap',
	'title'       => $constpref . '_DBLAYERTRAP',
	'description' => $constpref . '_DBLAYERTRAPDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'dblayertrap_wo_server',
	'title'       => $constpref . '_DBTRAPWOSRV',
	'description' => $constpref . '_DBTRAPWOSRVDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'enable_bigumbrella',
	'title'       => $constpref . '_BIGUMBRELLA',
	'description' => $constpref . '_BIGUMBRELLADSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'spamcount_uri4user',
	'title'       => $constpref . '_SPAMURI4U',
	'description' => $constpref . '_SPAMURI4UDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'spamcount_uri4guest',
	'title'       => $constpref . '_SPAMURI4G',
	'description' => $constpref . '_SPAMURI4GDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 5,
	'options'     => [],
];
$modversion['config'][] = [
	'name'        => 'filters',
	'title'       => $constpref . '_FILTERS',
	'description' => $constpref . '_FILTERSDSC',
	'formtype'    => 'textarea',
	'valuetype'   => 'text',
	'default'     => '',
	'options'     => [],
];
// Threat Intelligence configs
$modversion['config'][] = [
    'name'        => 'httpbl_enabled',
    'title'       => $constpref . '_HTTPBL_ENABLED',
    'description' => $constpref . '_HTTPBL_ENABLED_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'httpbl_key',
    'title'       => $constpref . '_HTTPBL_KEY',
    'description' => $constpref . '_HTTPBL_KEY_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'httpbl_threat_threshold',
    'title'       => $constpref . '_HTTPBL_THREAT_THRESHOLD',
    'description' => $constpref . '_HTTPBL_THREAT_THRESHOLD_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 25,
];

$modversion['config'][] = [
    'name'        => 'ti_feed_urls',
    'title'       => $constpref . '_FEED_URLS',
    'description' => $constpref . '_FEED_URLS_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'ti_check_login',
    'title'       => $constpref . '_CHECK_LOGIN',
    'description' => $constpref . '_CHECK_LOGIN_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'ti_check_register',
    'title'       => $constpref . '_CHECK_REGISTER',
    'description' => $constpref . '_CHECK_REGISTER_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'ti_check_forms',
    'title'       => $constpref . '_CHECK_FORMS',
    'description' => $constpref . '_CHECK_FORMS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'ti_check_admin',
    'title'       => $constpref . '_CHECK_ADMIN',
    'description' => $constpref . '_CHECK_ADMIN_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'ti_cache_duration',
    'title'       => $constpref . '_CACHE_DURATION',
    'description' => $constpref . '_CACHE_DURATION_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 3600,
    'options'     => [
        $constpref . '_CACHE_1HOUR' => 3600,
        $constpref . '_CACHE_6HOURS' => 21600,
        $constpref . '_CACHE_1DAY' => 86400,
        $constpref . '_CACHE_1WEEK' => 604800,
    ],
];
// Add proxy configuration options
$modversion['config'][] = [
    'name'        => 'proxy_enabled',
    'title'       => '_MI_PROTECTOR_PROXY_ENABLED',
    'description' => '_MI_PROTECTOR_PROXY_ENABLED_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0
];

$modversion['config'][] = [
    'name'        => 'proxy_allowed_domains',
    'title'       => '_MI_PROTECTOR_PROXY_ALLOWED_DOMAINS',
    'description' => '_MI_PROTECTOR_PROXY_ALLOWED_DOMAINS_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => ''
];

$modversion['config'][] = [
    'name'        => 'proxy_blocked_domains',
    'title'       => '_MI_PROTECTOR_PROXY_BLOCKED_DOMAINS',
    'description' => '_MI_PROTECTOR_PROXY_BLOCKED_DOMAINS_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => ''
];

$modversion['config'][] = [
    'name'        => 'proxy_cache_enabled',
    'title'       => '_MI_PROTECTOR_PROXY_CACHE_ENABLED',
    'description' => '_MI_PROTECTOR_PROXY_CACHE_ENABLED_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1
];

$modversion['config'][] = [
    'name'        => 'proxy_cache_time',
    'title'       => '_MI_PROTECTOR_PROXY_CACHE_TIME',
    'description' => '_MI_PROTECTOR_PROXY_CACHE_TIME_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 3600
];

$modversion['config'][] = [
    'name'        => 'proxy_log_requests',
    'title'       => '_MI_PROTECTOR_PROXY_LOG_REQUESTS',
    'description' => '_MI_PROTECTOR_PROXY_LOG_REQUESTS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1
];

$modversion['config'][] = [
    'name'        => 'proxy_strip_js',
    'title'       => '_MI_PROTECTOR_PROXY_STRIP_JS',
    'description' => '_MI_PROTECTOR_PROXY_STRIP_JS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0
];

$modversion['config'][] = [
    'name'        => 'proxy_strip_cookies',
    'title'       => '_MI_PROTECTOR_PROXY_STRIP_COOKIES',
    'description' => '_MI_PROTECTOR_PROXY_STRIP_COOKIES_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0
];

$modversion['config'][] = [
    'name'        => 'proxy_user_agent',
    'title'       => '_MI_PROTECTOR_PROXY_USER_AGENT',
    'description' => '_MI_PROTECTOR_PROXY_USER_AGENT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => ''
];

$modversion['config'][] = [
    'name'        => 'proxy_plugins_enabled',
    'title'       => '_MI_PROTECTOR_PROXY_PLUGINS_ENABLED',
    'description' => '_MI_PROTECTOR_PROXY_PLUGINS_ENABLED_DESC',
    'formtype'    => 'select_multi',
    'valuetype'   => 'array',
    'default'     => [],
    'options'     => [] // This will be populated dynamically
];

$modversion['config'][] = [
    'name'        => 'contami_action',
    'title'       => $constpref . '_CONTAMI_ACTION',
    'description' => $constpref . '_CONTAMI_ACTIONDS',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 3,
    'options'     => [
        $constpref . '_OPT_NONE'     => 0,
        $constpref . '_OPT_EXIT'     => 3,
        $constpref . '_OPT_BIPTIME0' => 7,
        $constpref . '_OPT_BIP'      => 15
    ],
];
$modversion['config'][] = [
    'name'        => 'isocom_action',
    'title'       => $constpref . '_ISOCOM_ACTION',
    'description' => $constpref . '_ISOCOM_ACTIONDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [
        $constpref . '_OPT_NONE'     => 0,
        $constpref . '_OPT_SAN'      => 1,
        $constpref . '_OPT_EXIT'     => 3,
        $constpref . '_OPT_BIPTIME0' => 7,
        $constpref . '_OPT_BIP'      => 15
    ],
];
$modversion['config'][] = [
    'name'        => 'union_action',
    'title'       => $constpref . '_UNION_ACTION',
    'description' => $constpref . '_UNION_ACTIONDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [
        $constpref . '_OPT_NONE'     => 0,
        $constpref . '_OPT_SAN'      => 1,
        $constpref . '_OPT_EXIT'     => 3,
        $constpref . '_OPT_BIPTIME0' => 7,
        $constpref . '_OPT_BIP'      => 15
    ],
];
$modversion['config'][] = [
    'name'        => 'id_forceintval',
    'title'       => $constpref . '_ID_INTVAL',
    'description' => $constpref . '_ID_INTVALDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'file_dotdot',
    'title'       => $constpref . '_FILE_DOTDOT',
    'description' => $constpref . '_FILE_DOTDOTDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'bf_count',
    'title'       => $constpref . '_BF_COUNT',
    'description' => $constpref . '_BF_COUNTDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '10',
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'bwlimit_count',
    'title'       => $constpref . '_BWLIMIT_COUNT',
    'description' => $constpref . '_BWLIMIT_COUNTDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'dos_skipmodules',
    'title'       => $constpref . '_DOS_SKIPMODS',
    'description' => $constpref . '_DOS_SKIPMODSDSC',
    'formtype'    => 'text',
    'valuetype'   => 'text',
    'default'     => '',
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'dos_expire',
    'title'       => $constpref . '_DOS_EXPIRE',
    'description' => $constpref . '_DOS_EXPIREDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '60',
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'dos_f5count',
    'title'       => $constpref . '_DOS_F5COUNT',
    'description' => $constpref . '_DOS_F5COUNTDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '20',
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'dos_f5action',
    'title'       => $constpref . '_DOS_F5ACTION',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'exit',
    'options'     => [
        $constpref . '_DOSOPT_NONE'     => 'none',
        $constpref . '_DOSOPT_SLEEP'    => 'sleep',
        $constpref . '_DOSOPT_EXIT'     => 'exit',
        $constpref . '_DOSOPT_BIPTIME0' => 'biptime0',
        $constpref . '_DOSOPT_BIP'      => 'bip',
        $constpref . '_DOSOPT_HTA'      => 'hta'
    ],
];
$modversion['config'][] = [
    'name'        => 'dos_crcount',
    'title'       => $constpref . '_DOS_CRCOUNT',
    'description' => $constpref . '_DOS_CRCOUNTDSC',
    'formtype'    => 'text',
    'valuetype'   => 'int',
    'default'     => '40',
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'dos_craction',
    'title'       => $constpref . '_DOS_CRACTION',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'exit',
    'options'     => [
        $constpref . '_DOSOPT_NONE'     => 'none',
        $constpref . '_DOSOPT_SLEEP'    => 'sleep',
        $constpref . '_DOSOPT_EXIT'     => 'exit',
        $constpref . '_DOSOPT_BIPTIME0' => 'biptime0',
        $constpref . '_DOSOPT_BIP'      => 'bip',
        $constpref . '_DOSOPT_HTA'      => 'hta'
    ],
];
$modversion['config'][] = [
    'name'        => 'dos_crsafe',
    'title'       => $constpref . '_DOS_CRSAFE',
    'description' => $constpref . '_DOS_CRSAFEDSC',
    'formtype'    => 'text',
    'valuetype'   => 'text',
    'default'     => '/(msnbot|Googlebot|Yahoo! Slurp)/i',
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'bip_except',
    'title'       => $constpref . '_BIP_EXCEPT',
    'description' => $constpref . '_BIP_EXCEPTDSC',
    'formtype'    => 'group_multi',
    'valuetype'   => 'array',
    'default'     => [ 1 ],
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'disable_features',
    'title'       => $constpref . '_DISABLES',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [ 'xmlrpc' => 1, 'xmlrpc + 2.0.9.2 bugs' => 1025, '_NONE' => 0 ],
];
$modversion['config'][] = [
    'name'        => 'enable_dblayertrap',
    'title'       => $constpref . '_DBLAYERTRAP',
    'description' => $constpref . '_DBLAYERTRAPDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'dblayertrap_wo_server',
    'title'       => $constpref . '_DBTRAPWOSRV',
    'description' => $constpref . '_DBTRAPWOSRVDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'enable_bigumbrella',
    'title'       => $constpref . '_BIGUMBRELLA',
    'description' => $constpref . '_BIGUMBRELLADSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'spamcount_uri4user',
    'title'       => $constpref . '_SPAMURI4U',
    'description' => $constpref . '_SPAMURI4UDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'spamcount_uri4guest',
    'title'       => $constpref . '_SPAMURI4G',
    'description' => $constpref . '_SPAMURI4GDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 5,
    'options'     => [],
];
$modversion['config'][] = [
    'name'        => 'filters',
    'title'       => $constpref . '_FILTERS',
    'description' => $constpref . '_FILTERSDSC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
    'options'     => [],
];
// Threat Intelligence configs
$modversion['config'][] = [
    'name'        => 'httpbl_enabled',
    'title'       => $constpref . '_HTTPBL_ENABLED',
    'description' => $constpref . '_HTTPBL_ENABLED_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'httpbl_key',
    'title'       => $constpref . '_HTTPBL_KEY',
    'description' => $constpref . '_HTTPBL_KEY_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'httpbl_threat_threshold',
    'title'       => $constpref . '_HTTPBL_THREAT_THRESHOLD',
    'description' => $constpref . '_HTTPBL_THREAT_THRESHOLD_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 25,
];

$modversion['config'][] = [
    'name'        => 'ti_feed_urls',
    'title'       => $constpref . '_FEED_URLS',
    'description' => $constpref . '_FEED_URLS_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'ti_check_login',
    'title'       => $constpref . '_CHECK_LOGIN',
    'description' => $constpref . '_CHECK_LOGIN_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'ti_check_register',
    'title'       => $constpref . '_CHECK_REGISTER',
    'description' => $constpref . '_CHECK_REGISTER_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'ti_check_forms',
    'title'       => $constpref . '_CHECK_FORMS',
    'description' => $constpref . '_CHECK_FORMS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'ti_check_admin',
    'title'       => $constpref . '_CHECK_ADMIN',
    'description' => $constpref . '_CHECK_ADMIN_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'ti_cache_duration',
    'title'       => $constpref . '_CACHE_DURATION',
    'description' => $constpref . '_CACHE_DURATION_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 3600,
    'options'     => [
        $constpref . '_CACHE_1HOUR' => 3600,
        $constpref . '_CACHE_6HOURS' => 21600,
        $constpref . '_CACHE_1DAY' => 86400,
        $constpref . '_CACHE_1WEEK' => 604800,
    ],
];
