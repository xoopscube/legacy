<?php

try {
	//debug($_REQUEST);
	// for keep alive
	if (! empty($_GET['keepalive']) && $_SERVER['REQUEST_METHOD'] !== 'OPTIONS') exit(0);

	@ set_time_limit(120); // just in case it too long, not recommended for production

	// needed for case insensitive search to work, due to broken UTF-8 support in PHP
	ini_set('default_charset', 'UTF-8');
	if (version_compare(PHP_VERSION, '5.6', '<')) {
		ini_set('mbstring.internal_encoding', 'UTF-8');
		ini_set('mbstring.http_input', 'pass');
		ini_set('mbstring.http_output', 'pass');
	} else {
		@ini_set('mbstring.internal_encoding', '');
		@ini_set('mbstring.http_input', '');
		@ini_set('mbstring.http_output', '');
	}

	//error_reporting(E_ALL | E_STRICT); // Set E_ALL for debuging

	// Add PEAR Dirctory into include path
	$incPath = get_include_path();
	$addPath = XOOPS_TRUST_PATH . '/PEAR';
	if (strpos($incPath, $addPath) === FALSE) {
		//set_include_path( $incPath . PATH_SEPARATOR . $addPath );
		set_include_path( $addPath . PATH_SEPARATOR . $incPath );
	}
	define('ELFINDER_DROPBOX_USE_CURL_PUT', true);

	// load compat functions
	require_once dirname(__FILE__) . '/include/compat.php';

	$php54 = version_compare(PHP_VERSION, '5.4', '>=');
	$php55 = version_compare(PHP_VERSION, '5.5', '>=');
	
	// load composer auto loader
	if ($php54 && is_file(__DIR__ . '/plugins/vendor/autoload.php')) {
		require_once __DIR__ . '/plugins/vendor/autoload.php';
	}

	// convert PATH_INFO to GET query for netmount
	if (! empty($_SERVER['PATH_INFO'])) {
		$_ps = explode('/', trim($_SERVER['PATH_INFO'], '/'));
		if (! isset($_GET['cmd'])) {
			$_cmd = $_ps[0];
			if ($_cmd === 'netmount') {
				$_GET['cmd'] = $_cmd;
				$_i = 1;
				foreach(array('protocol', 'host') as $_k) {
					if (isset($_ps[$_i])) {
						if (! isset($_GET[$_k])) {
							$_GET[$_k] = $_ps[$_i];
						}
					} else {
						break;
					}
				}
			}
		}
	}

	// load elFinder auto loader
	define('_MD_ELFINDER_LIB_PATH', XOOPS_TRUST_PATH . '/libs/elfinder');
	require _MD_ELFINDER_LIB_PATH . '/php/autoload.php';

	//////////////////////////////////////////////////////
	// for XOOPS
	$config = $xoopsModuleConfig;

	$debug = (! empty($config['debug']));
	if ($debug) {
		if (defined('E_STRICT')) {
			error_reporting(E_ALL ^ E_STRICT);
		} else {
			error_reporting(E_ALL);
		}
		// set elFinder error level
		define('ELFINDER_DEBUG_ERRORLEVEL', error_reporting());
	} else {
		error_reporting(0);
	}

	if (! empty($config['enable_imagemagick_ps'])) {
		define('ELFINDER_IMAGEMAGICK_PS', true);
	}

	if (! empty($config['ffmpeg_path'])) {
		define('ELFINDER_FFMPEG_PATH', $config['ffmpeg_path']);
	}

	define('_MD_XELFINDER_NETVOLUME_SESSION_KEY', 'xel_'.$mydirname.'_NetVolumes');

	if (! defined('XOOPS_MODULE_PATH')) define('XOOPS_MODULE_PATH', XOOPS_ROOT_PATH . '/modules');
	if (! defined('XOOPS_MODULE_URL')) define('XOOPS_MODULE_URL', XOOPS_URL . '/modules');

	define('_MD_ELFINDER_MYDIRNAME', $mydirname);
	define('_MD_XELFINDER_PROXY_TOKEN_KEY', $mydirname.'_ptoken');

	// load xoops_elFinder
	require_once dirname(__FILE__).'/class/xoops_elFinder.class.php';
	$xoops_elFinder = new xoops_elFinder($mydirname);
	$xoops_elFinder->setConfig($config);
	$xoops_elFinder->setLogfile($debug? XOOPS_TRUST_PATH . '/cache/elfinder.log.txt' : '');

	// HTTP request header origin
	$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
	if ($origin && $origin === $xoops_elFinder->getMyOrigin()) {
		$origin = '';
	}

	$allowOrigins = array_map('trim', preg_split('/\s+/', $config['allow_origins']));
	$allowOrigins[] = preg_replace('#(^https?://[^/]+).*#i', '$1', XOOPS_URL);
	$allowOrigins = array_flip($allowOrigins);

	// Check cToken for protect from CSRF
	if (! isset($_SESSION['XELFINDER_CTOKEN'])
	|| ! isset($_REQUEST['ctoken'])
	|| $_SESSION['XELFINDER_CTOKEN'] !== $_REQUEST['ctoken']) {
		if (($origin && isset($allowOrigins[$origin]))
			|| isset($_GET['logout'])
			|| (isset($_GET['cmd']) && ($_GET['cmd'] === 'callback' || $_GET['cmd'] === 'netmount'))
			|| (isset($_REQUEST['cmd']) && $_REQUEST['cmd'] === 'file')
			|| !empty($_REQUEST[_MD_XELFINDER_PROXY_TOKEN_KEY])
		) {
			if ($origin && $_REQUEST['ctoken'] && ! isset($_SESSION['XELFINDER_CTOKEN'])) {
				$_SESSION['XELFINDER_CTOKEN'] = $_REQUEST['ctoken'];
			}
		} else {
			header('HTTP', true, 403);
			exit(json_encode(array('error' => 'errPleaseReload')));
		}
	}

	if (empty($_REQUEST['xoopsUrl']) && !$origin) {
		define('_MD_XELFINDER_SITEURL', XOOPS_URL);
		define('_MD_XELFINDER_MODULE_URL', XOOPS_MODULE_URL);
	} else {
		if (($origin && !isset($allowOrigins[$origin]))
		 || (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])
		 		 && !in_array(strtoupper($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']), array('POST', 'GET', 'OPTIOINS')))
		) {
			exit(json_encode(array('error' => 'errAccess')));
		}
		define('_MD_XELFINDER_SITEURL', empty($_REQUEST['xoopsUrl'])? XOOPS_URL : $_REQUEST['xoopsUrl']);
		define('_MD_XELFINDER_MODULE_URL', str_replace(XOOPS_URL, _MD_XELFINDER_SITEURL, XOOPS_MODULE_URL));
		if ($origin) {
			header('Access-Control-Allow-Origin: ' . $origin);
			!isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])
			 || header('Access-Control-Allow-Methods: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']);
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 1000');
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
				header('Access-Control-Allow-Headers: '
						. $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
			} else {
				header('Access-Control-Allow-Headers: *');
			}
			header('Access-Control-Expose-Headers: Content-Length');
		}

		if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS' || ! empty($_GET['keepalive'])) exit(0);
	}

	define('ELFINDER_IMG_PARENT_URL', XOOPS_URL . '/common/elfinder/');

	require dirname(__FILE__) . '/class/xelFinder.class.php';
	require dirname(__FILE__) . '/class/xelFinderVolumeFTP.class.php';

	$extras = array();
	if (strtoupper(_CHARSET) !== 'UTF-8') {
		mb_convert_variables('UTF-8', _CHARSET, $config);
	}
	$config_MD5 = md5(json_encode($config));

	// box
	if (!empty($config['boxapi_id']) && !empty($config['boxapi_secret'])) {
		elFinder::$netDrivers['box'] = 'Box';
		define('ELFINDER_BOX_CLIENTID',     $config['boxapi_id']);
		define('ELFINDER_BOX_CLIENTSECRET', $config['boxapi_secret']);
	}

	// dropbox
	if ($php55 && !empty($config['dropbox_token']) && !empty($config['dropbox_seckey']) && class_exists('\Kunnu\Dropbox\DropboxApp')) {
		elFinder::$netDrivers['dropbox2'] = 'Dropbox2';
		define('ELFINDER_DROPBOX_APPKEY',    $config['dropbox_token']);
		define('ELFINDER_DROPBOX_APPSECRET', $config['dropbox_seckey']);
	}

	// google drive
	if ($php54 && !empty($config['googleapi_id']) && !empty($config['googleapi_secret']) && class_exists('\Google_Client')) {
		elFinder::$netDrivers['googledrive'] = 'GoogleDrive';
		define('ELFINDER_GOOGLEDRIVE_CLIENTID',     $config['googleapi_id']);
		define('ELFINDER_GOOGLEDRIVE_CLIENTSECRET', $config['googleapi_secret']);
	}

	// one drive
	if (!empty($config['onedriveapi_id']) && !empty($config['onedriveapi_secret'])) {
		elFinder::$netDrivers['onedrive'] = 'OneDrive';
		define('ELFINDER_ONEDRIVE_CLIENTID',     $config['onedriveapi_id']);
		define('ELFINDER_ONEDRIVE_CLIENTSECRET', $config['onedriveapi_secret']);
	}

	// zoho office editor
	if (!empty($config['zoho_apikey'])) {
		// https://www.zoho.com/docs/help/office-apis.html
		define('ELFINDER_ZOHO_OFFICE_APIKEY', $config['zoho_apikey']);
	}

	// ONLINE-CONVERT.COM API
	if (!empty($config['online_convert_apikey'])) {
		// https://apiv2.online-convert.com/docs/getting_started/api_key.html
		define('ELFINDER_ONLINE_CONVERT_APIKEY', $config['online_convert_apikey']);
	}

	/*// load xoops_elFinder
	require_once dirname(__FILE__).'/class/xoops_elFinder.class.php';
	$xoops_elFinder = new xoops_elFinder($mydirname);
	$xoops_elFinder->setConfig($config);
	$xoops_elFinder->setLogfile($debug? XOOPS_TRUST_PATH . '/cache/elfinder.log.txt' : '');*/

	// Access control
	require_once dirname(__FILE__).'/class/xelFinderAccess.class.php';
	// custom session handler
	require_once _MD_ELFINDER_LIB_PATH . '/php/elFinderSession.php';
	
	// make sesstion handler
	$session = new elFinderSession(array(
		'base64encode' => $xoops_elFinder->base64encodeSessionData,
		'keys' => array(
			'default'   => 'xel_'.$mydirname.'_Caches',
			'netvolume' => _MD_XELFINDER_NETVOLUME_SESSION_KEY
		)
	));
	
	// for XOOPS uid of current session
	$uidSessionKey = 'xel_'.$mydirname.'_Uid';

	// Check command login/logout/status
	$xoops_elFinder->checkLogin($session);
	
	// get user roll
	$userRoll = $xoops_elFinder->getUserRoll();
	$isAdmin = $userRoll['isAdmin'];

	// set netmount data to session
	$netVolumeData = array();
	if ($userRoll['uid'] && $userRoll['uid'] !== $session->get($uidSessionKey)) {
		$sessNetVols = $session->get('netvolume', $netVolumeData);
		$netVolumeData = array_merge($xoops_elFinder->getNetmountData(), $sessNetVols);
		if (count($netVolumeData)) {
			$session->set('netvolume', $netVolumeData);
			if (count($sessNetVols)) {
				$xoops_elFinder->saveNetmoutData($session);
			}
		}
	}

	// set current XOOPS uid to session
	$session->set($uidSessionKey, $userRoll['uid']);
	
	// Get volumes
	if (isset($_SESSION['XELFINDER_RF_'.$mydirname]) && $_SESSION['XELFINDER_CFG_HASH_'.$mydirname] === $config_MD5) {
		$rootConfig = unserialize(base64_decode($_SESSION['XELFINDER_RF_'.$mydirname]));
	} else {
		$memberUid = $userRoll['uid'];
		$memberGroups = $userRoll['mygids'];
		$inSpecialGroup = $userRoll['inSpecialGroup'];
		
		// set umask
		foreach(array('default', 'users_dir', 'guest_dir', 'group_dir') as $_key) {
			$config[$_key.'_umask'] = strval(dechex(0xfff - intval(strval($config[$_key.'_item_perm']), 16)));
		}
		
		// set uploadAllow
		if ($isAdmin) {
			$config['uploadAllow'] = @$config['upload_allow_admin'];
			$config['autoResize'] = @$config['auto_resize_admin'];
			$config['uploadMaxSize'] = @$config['upload_max_admin'];
		} elseif ($inSpecialGroup) {
			$config['uploadAllow'] = @$config['upload_allow_spgroups'];
			$config['autoResize'] = @$config['auto_resize_spgroups'];
			$config['uploadMaxSize'] = @$config['upload_max_spgroups'];
		} elseif ($memberUid) {
			$config['uploadAllow'] = @$config['upload_allow_user'];
			$config['autoResize'] = @$config['auto_resize_user'];
			$config['uploadMaxSize'] = @$config['upload_max_user'];
		} else {
			$config['uploadAllow'] = @$config['upload_allow_guest'];
			$config['autoResize'] = @$config['auto_resize_guest'];
			$config['uploadMaxSize'] = @$config['upload_max_guest'];
		}
		
		$config['uploadAllow'] = trim($config['uploadAllow']);
		if (! $config['uploadAllow'] || $config['uploadAllow'] === 'none') {
			$config['uploadAllow'] = array();
		} else {
			$config['uploadAllow'] = explode(' ', $config['uploadAllow']);
			$config['uploadAllow'] = array_map('trim', $config['uploadAllow']);
		}
		$config['autoResize'] = (int)$config['autoResize'];
		
		if (! empty($xoopsConfig['cool_uri'])) {
			$config['URL'] = _MD_XELFINDER_SITEURL . '/' . $mydirname . '/view/';
		} else if (empty($config['disable_pathinfo'])) {
			$config['URL'] = _MD_XELFINDER_MODULE_URL . '/' . $mydirname . '/index.php/view/';
		} else {
			$config['URL'] = _MD_XELFINDER_MODULE_URL . '/' . $mydirname . '/index.php?page=view&file=';
		}
		
		if (! isset($extras[$mydirname.':xelfinder_db'])) {
			$extras[$mydirname.':xelfinder_db'] = array();
		}
		foreach (
				array('default_umask', 'use_users_dir', 'users_dir_perm', 'users_dir_umask', 'use_guest_dir', 'guest_dir_perm', 'guest_dir_umask',
						'use_group_dir', 'group_dir_parent', 'group_dir_perm', 'group_dir_umask', 'uploadAllow', 'uploadMaxSize', 'URL', 'unzip_lang_value')
				as $_extra
		) {
			$extras[$mydirname.':xelfinder_db'][$_extra] = empty($config[$_extra])? '' : $config[$_extra];
		}
		if (! empty($config['autoResize'])) {
			$extras[$mydirname.':xelfinder_db']['plugin']['AutoResize'] = array(
				'enable' => true,
				'maxHeight' => $config['autoResize'],
				'maxWidth' => $config['autoResize'],
				'offDropWith' => ($isAdmin || $inSpecialGroup)? 4 : null // Disable with Ctrl key 
			);
		}
		
		$rootConfig = $xoops_elFinder->getRootVolumeConfigs($config['volume_setting'], $extras);
		
		// Add net(FTP) volume
		if ($isAdmin && !empty($config['ftp_host']) && !empty($config['ftp_port']) && !empty($config['ftp_user']) && !empty($config['ftp_pass'])) {
			$ftp = array(
				'driver'  => 'FTPx',
				'id'      => 'ad',
				'alias'   => $config['ftp_name'],
				'host'    => $config['ftp_host'],
				'port'    => $config['ftp_port'],
				'path'    => $config['ftp_path'],
				'user'    => $config['ftp_user'],
				'pass'    => $config['ftp_pass'],
				'disabled'=> !empty($config['ftp_search'])? array() : array('search'),
				'statOwner' => true,
				'allowChmodReadOnly' => true,
				'is_local'=> true,
				'tmpPath' => XOOPS_MODULE_PATH . '/'.$mydirname.'/cache',
				'utf8fix' => true,
				'defaults' => array('read' => true, 'write' => true, 'hidden' => false, 'locked' => false),
				'attributes' => array(
					array(
						'pattern' => '~/\.~',
						'read' => false,
						'write' => false,
						'hidden' => true,
						'locked' => false
					),
				)
			);
			$rootConfig[] = array('raw' => $ftp);
		}
		if (defined('ELFINDER_DROPBOX_APPKEY') && $config['dropbox_path'] && $config['dropbox_acc_token']) {
			if ($config['dropbox_acc_seckey']) {
				if ($token2 = elFinderVolumeDropbox2::getTokenFromOauth1(ELFINDER_DROPBOX_APPKEY, ELFINDER_DROPBOX_APPSECRET, $config['dropbox_acc_token'], $config['dropbox_acc_seckey'])) {
					$config['dropbox_acc_token'] = $token2;
				}
			}
			if (empty($config['dropbox_acc_seckey']) || $token2) {
				$dropbox_access = null;
				$dropboxIsInGroup = (array_intersect($memberGroups, ( isset($config['dropbox_writable_groups'])? $config['dropbox_writable_groups'] : array() )));
				if (!$isAdmin) {
					$dropbox_access = new xelFinderAccess();
					if (isset($config['dropbox_hidden_ext']))
						$dropbox_access->setHiddenExtention($config['dropbox_hidden_ext']);
					if (isset($config['dropbox_write_ext']))
						$dropbox_access->setWriteExtention($dropboxIsInGroup? $config['dropbox_write_ext'] : '');
					if (isset($config['dropbox_unlock_ext']))
						$dropbox_access->setUnlockExtention($dropboxIsInGroup? $config['dropbox_unlock_ext'] : '');
				}
				$dropbox = array(
					'driver'        => 'Dropbox2',
					'id'            => 'sh',
					'app_key'       => ELFINDER_DROPBOX_APPKEY,
					'app_secret'    => ELFINDER_DROPBOX_APPSECRET,
					'alias'         => trim($config['dropbox_name']),
					'access_token'  => trim($config['dropbox_acc_token']),
					'path'          => '/'.trim($config['dropbox_path'], ' /'),
					'defaults'      => array('read' => true, 'write' => ($dropboxIsInGroup? true : false), 'hidden' => false, 'locked' => false),
					'accessControl' => is_object($dropbox_access)? array($dropbox_access, 'access') : null,
					'uploadDeny'    => (!$isAdmin && !empty($config['dropbox_upload_mime']))? array('all') : array(),
					'uploadAllow'   => (!$isAdmin && !empty($config['dropbox_upload_mime']))? array_map('trim', explode(',', $config['dropbox_upload_mime'])) : array(),
					'uploadOrder'   => array('deny', 'allow'),
					'tmpPath'       => XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache',
					'tmbPath'       => XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb',
					'tmbURL'        => _MD_XELFINDER_MODULE_URL.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb',
				);
				$rootConfig[] = array('raw' => $dropbox);
			}
		}
		
		try {
			if ($serVar = @serialize($rootConfig)) {
				$_SESSION['XELFINDER_RF_'.$mydirname] = base64_encode($serVar);
				$_SESSION['XELFINDER_CFG_HASH_'.$mydirname] = $config_MD5;
			}
		} catch (Exception $e) {}
	}

	$rootVolumes = $xoops_elFinder->buildRootVolumes($rootConfig);
	foreach($rootVolumes as $rootVolume) {
		if (isset($rootVolume['driverSrc'])) {
			require_once $rootVolume['driverSrc'];
		}
	}
	//var_dump($rootVolumes);exit;

	$optionsNetVolumes = array(
		'*' => array(
			'tmpPath' => XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache',
			'tmbPath' => XOOPS_MODULE_PATH.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb',
			'tmbURL'  => _MD_XELFINDER_MODULE_URL.'/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb',
			'tsPlSleep' => 15,
			'syncMinMs' => 30000,
			'plugin' => array(
				'AutoResize' => array(
					'enable' => false
				),
				'Watermark' => array(
					'enable' => false
				),
				'Normalizer' => array(
					'enable' => false
				),
				'Sanitizer' => array(
					'enable' => false
				)
			)
		)
	);

	// End for XOOPS
	//////////////////////////////////////////////////////

	$opts = array(
		'isAdmin' => $isAdmin, // for class xelFinder
		'locale' => 'ja_JP.UTF-8',
		'optionsNetVolumes' => $optionsNetVolumes,
		'session' => $session,
		'bind'   => array(
			//'*' => array($xoops_elFinder, 'log'),
			'netmount.pre' => array($xoops_elFinder, 'netmountPreCallback'),
			'netmount rename' => array($xoops_elFinder, 'netmountCallback'),
			'mkdir mkfile put upload extract' => array($xoops_elFinder, 'notifyMail'),
			'upload.pre mkdir.pre mkfile.pre rename.pre archive.pre ls.pre' => array(
				'Plugin.Sanitizer.cmdPreprocess',
				'Plugin.Normalizer.cmdPreprocess'
			),
			'upload.presave' => array(
				'Plugin.Sanitizer.onUpLoadPreSave',
				'Plugin.Normalizer.onUpLoadPreSave',
				array($xoops_elFinder, 'autoRotateOnUpLoadPreSave'),
				'Plugin.AutoResize.onUpLoadPreSave',
				'Plugin.Watermark.onUpLoadPreSave'
			),
			'editor.pre' => array($xoops_elFinder, 'editorPreCallback'),
		),
		'plugin' => array(
			//'Sanitizer' => array(
			//	'enable' => true,
			//),
			'AutoResize' => array(
				'enable' => false
			),
			'Watermark' => array(
				'enable' => false
			),
		),
		'debug' => $debug,
		'uploadTempPath' => XOOPS_TRUST_PATH . '/cache',
		'commonTempPath' => XOOPS_TRUST_PATH . '/cache',
		'tmpLinkPath' => XOOPS_MODULE_PATH . '/'.$mydirname.'/cache',
		'roots' => $rootVolumes,
		'callbackWindowURL' => !empty($_REQUEST['myUrl'])? ($_REQUEST['myUrl'] . 'connector.php?cmd=callback') : ''
	);

	// clear output buffer
	while( ob_get_level() ) {
		if (! @ ob_end_clean()) break;
	}

	$elfinder = new xelFinder($opts);
	$connector = new elFinderConnector($elfinder, $debug);
	
	// check netVolumeData
	if ($netVolumeData) {
		if (count($netVolumeData) !== count($session->get('netvolume', array()))) {
			// save user data if found invalid netvolume data
			$_result = array('sync' => true); //dummy data
			$xoops_elFinder->netmountCallback(null, $_result, null, $elfinder);
		}
	}
	
	$connector->run();
} catch (Exception $e) {
	exit(json_encode(array('error' => $e->getMessage())));
}


function debug() {
	$args = func_get_args();
	ob_start();
	foreach($args as $arg) {
		//debug_print_backtrace();
		var_dump($arg);
	}
	$str = ob_get_clean();
	file_put_contents(dirname(__FILE__) . '/debug.txt', $str . "\n", FILE_APPEND);
}

