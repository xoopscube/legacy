<?php
namespace Hypweb\Xelfinder\Plugins\Flygoogledrive;

use \League\Flysystem\Filesystem;
use \League\Flysystem\Adapter\Local;
use \League\Flysystem\Cached\CachedAdapter;
use \League\Flysystem\Cached\Storage\Memcached as MCache;
use \League\Flysystem\Cached\Storage\Adapter as ACache;
use \Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use \Hypweb\Flysystem\Cached\Extra\Hasdir;
use \Hypweb\Flysystem\Cached\Extra\DisableEnsureParentDirectories;
use \Google_Client;
use \Google_Service_Drive;

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
	$_err = false;
	foreach(['ext_token'] as $_key) {
		if (! isset($extOptions[$_key])) {
			$_err = true;
		}
	}
	
	if (! $_err) {
		$_sessionKey = md5('XelfinderGoogleDrive'.$extOptions['ext_token']);
		$_client = new Google_Client();
		$_token = @$_SESSION[$_sessionKey];
		if (is_array($_token) && !empty($_token['access_token'])) {
			$_client->setAccessToken($_token);
		}
		if ($_client->isAccessTokenExpired()) {
			if ($_token = @json_decode($extOptions['ext_token'], true, 512, JSON_THROW_ON_ERROR)) {
				$_client->setClientId($_token['client_id']);
				$_client->setClientSecret($_token['client_secret']);
				$_creds = $_client->refreshToken($_token['refresh_token']);
				if ($_client->getAccessToken()) {
					$_creds = array_merge($_creds, $_token);
					$_SESSION[$_sessionKey] = $_creds;
				} else {
					unset($_SESSION[$_sessionKey]);
					throw new Exception('Root volumes setting error: [flyGoogleDrive] Invalid "refresh_token".');
				}
			} else {
				unset($_SESSION[$_sessionKey]);
				throw new Exception('Root volumes setting error: [flyGoogleDrive] "ext_token" is not a JSON.');
			}
		}
		$path = trim($path, '/');
		
		$service = new Google_Service_Drive($_client);
		$_gdrive = new GoogleDriveAdapter($service, $path, [ 'useHasDir' => true ]);
		
		$_cache = null;
		$_expire = $extOptions['ext_cache_expire'] ?? 300;
		if ($_expire) {
			$_cacheKey = md5(XOOPS_URL . $mDirname . $extOptions['ext_token'] . $path);
			
			if (class_exists('Memcached', false)) {
				if (! class_exists('MyMCache', false)) {
					class MyMCache extends MCache { use Hasdir; use DisableEnsureParentDirectories; }
				}
				$memcached = new \Memcached();
				if ($memcached->addServer(
					empty($extOptions['ext_mcache_host'])? 'localhost' : $extOptions['ext_mcache_host'],
					empty($extOptions['ext_mcache_port'])?  11211      : $extOptions['ext_mcache_port']
					)
				) {
					$_cache = new MyMCache($memcached, $_cacheKey, $_expire);
				}
			}
			
			if (! $_cache && is_writable(XOOPS_TRUST_PATH.'/cache')) {
				if (! class_exists('MyACache', false)) {
					class MyACache extends ACache { use Hasdir; use DisableEnsureParentDirectories; }
				}
				$_cache = new MyACache(new Local(XOOPS_TRUST_PATH.'/cache'), $_cacheKey, $_expire);
			}
		}
		
		if ($_cache) {
			// use storage cache with `ext_cache_expire`
			$_fly = new Filesystem(new CachedAdapter($_gdrive, $_cache));
		} else {
			// not use cache
			$_fly = new Filesystem($_gdrive);
		}
		
		$volumeOptions = ['driver' => 'FlysystemExt', 'filesystem' => $_fly, 'fscache' => $_cache, 'alias' => $title, 'separator' => '/', 'icon' => XOOPS_MODULE_URL . '/' . $mDirname . '/images/volume_icon_googledrive.png', 'tmbPath' => XOOPS_MODULE_PATH . '/' . _MD_ELFINDER_MYDIRNAME . '/cache/tmb/', 'tmbURL' => _MD_XELFINDER_MODULE_URL . '/' . _MD_ELFINDER_MYDIRNAME . '/cache/tmb/'];
	}
}

