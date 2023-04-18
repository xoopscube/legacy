<?php
/*
 * @author     Nobuhiro YASUTOMI, PHP8
 * Created on 2012/01/20 by nao-pon http://xoops.hypweb.net/
 * $Id: xoops_elFinder.class.php,v 1.1 2012/01/20 13:32:02 nao-pon Exp $
 */

class xoops_elFinder {

	protected $db;
	
	protected $xoopsUser;
	protected $xoopsModule;
	protected $mydirname;
	protected $isAdmin;
	
	protected $config;
	protected $mygids;
	protected $uid;
	protected $inSpecialGroup;
	protected $myOrigin;
	protected $tokeDataPrefix;

	public $base64encodeSessionData;
	
	protected static $dbCharset = '';
	
	/**
	* Log file path
	*
	* @var string
	**/
	protected $file = '';
	
	protected $defaultVolumeOptions = ['dateFormat' => 'y/m/d H:i', 'mimeDetect' => 'auto', 'tmbSize'	 => 48, 'tmbCrop'	 => true, 'defaults' => ['read' => true, 'write' => false, 'hidden' => false, 'locked' => false]];
	
	protected $writeCmds = ['archive', 'chmod', 'cut', 'duplicate', 'edit', 'empty', 'extract', 'mkdir', 'mkfile', 'paste', 'perm', 'put', 'rename', 'resize', 'rm', 'upload'];
	
	public function __construct($mydirname, $opt = []) {
		global $xoopsUser, $xoopsModule;
		
		if (!is_object($xoopsModule)) {
			$module_handler = xoops_getHandler('module');
			$mModule = $module_handler->getByDirname($mydirname);
		} else {
			$mModule = $xoopsModule;
		}
		
		$this->xoopsModule = $mModule;
		$this->setXoopsUser($xoopsUser);
		$this->mydirname = $mydirname;
		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		$this->defaultVolumeOptions = array_merge($this->defaultVolumeOptions, $opt);
		$this->base64encodeSessionData = ((!defined('_CHARSET') || _CHARSET !== 'UTF-8') && substr($this->getSessionTableType(), -4) !== 'blob');
		$https = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off');
		$this->myOrigin = ($https? 'https://' : 'http://')
			.$_SERVER['SERVER_NAME'] // host
			.(((! $https && $_SERVER['SERVER_PORT'] == 80) || ($https && $_SERVER['SERVER_PORT'] == 443)) ? '' : (':' . $_SERVER['SERVER_PORT']));  // port
		$this->tokeDataPrefix = XOOPS_MODULE_PATH.'/'.$mydirname.'/cache/tokendata_';
	}
	
	public function getMyOrigin() {
		return $this->myOrigin;
	}
	
	public function getUserRoll() {
		$res = ['isAdmin'        => (bool)$this->isAdmin, 'uid'            => (int)$this->uid, 'mygids'         => (array)$this->mygids, 'inSpecialGroup' => (bool)$this->inSpecialGroup];
		return $res;
	}
	
	public function getUid() {
		return $this->uid;
	}
	
	public function getUname($mode = 'n') {
		$uname = '';
		if ($this->uid) {
			$uname = $this->xoopsUser->getVar('uname', $mode);
			if (strtoupper(_CHARSET) !== 'UTF-8') {
				$uname = mb_convert_encoding($uname, 'UTF-8', _CHARSET);
			}
		}
		return $uname;
	}
	
	public function checkLogin($session) {
		$urlInfo = [];
  // login/logout/status
		if (isset($_GET['login']) || isset($_GET['logout']) || isset($_GET['status'])) {
			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', false);
			header('Pragma: no-cache');
			if (isset($_GET['status'])) {
				$uname = $this->getUname();
				echo json_encode(['uname' => $uname], JSON_THROW_ON_ERROR);
			} else {
				$config_handler = xoops_getHandler('config');
				$xoopsConfig = $config_handler->getConfigsByCat(XOOPS_CONF);
				$session->start();
				$data = ['uname' => ''];
				if (isset($_GET['logout'])) {
					$session->set('netvolume', []);
					$this->destroySessionVar();
					$this->setXoopsUser();
				} else {
					$data = [];
					if ($this->login($session, $xoopsConfig)) {
						$data['uname'] = $this->getUname();
					} else {
						$data['error'] = 'loginFaild';
					}
				}
				if (empty($data['error'])) {
					session_regenerate_id();
					$data['xoopsUid'] = $this->getUid();
					$data['autoSyncSec'] = $this->getAutoSyncSec();
					$data['cToken'] = $this->getCToken();
					// care to old xoops
					if ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] && session_name() !== $xoopsConfig['session_name']) {
						setcookie($xoopsConfig['session_name'], session_id(), ['expires' => time()+(60 * $xoopsConfig['session_expire']), 'path' => rtrim($urlInfo['path'], '/').'/', 'domain' => '', 'secure' => 0]);
					}
				}
				$session->close();
				echo json_encode($data, JSON_THROW_ON_ERROR);
			}
			exit();
		} else if (! empty($_GET[_MD_XELFINDER_PROXY_TOKEN_KEY])) {
			$this->tokenDataGC();
			// check token
			$token = preg_replace('/[^0-9a-f]/', '', $_GET[_MD_XELFINDER_PROXY_TOKEN_KEY]);
			$file = $this->tokeDataPrefix . $token . '.dat';
			if (file_exists($file) && ($data = unserialize(file_get_contents($file)))) {
				if (!empty($data['require']) && !empty($data['uid'])) {
					$args = $_GET;
					if (! empty($_POST)) {
						$args = array_merge($_POST, $args);
					}
					$ok = true;
					foreach($data['require'] as $key => $val) {
						if (! isset($args[$key]) || $args[$key] != $val) {
							$ok = false;
							break;
						}
					}
					if ($ok) {
						$member_handler = xoops_getHandler('member');
						$this->setXoopsUser($member_handler->getUser($data['uid']));
						if ($this->xoopsUser) {
							global $xoopsUser;
							$xoopsUser = $this->xoopsUser;
						}
					} else {
						header('HTTP', true, 403);
						exit();
					}
				}
			}
		}
	}
	
	public function getCToken() {
		$cToken = md5(session_id() . XOOPS_ROOT_PATH . (defined(XOOPS_SALT)? XOOPS_SALT : XOOPS_DB_PASS));
		$_SESSION['XELFINDER_CTOKEN'] = $cToken;
		return $cToken;
	}
	
	public function getNetmountData() {
		$data = [];
		if ($this->uid) {
			$table = $this->db->prefix($this->mydirname.'_userdat');
			$sql = 'SELECT `data` FROM `'.$table.'` WHERE `key`=\'netVolumes\' AND `uid`='.$this->uid.' LIMIT 1';
			if ($res = $this->db->query($sql)) {
				if ($this->db->getRowsNum($res) > 0) {
					[$data] = $this->db->fetchRow($res);
					$data = @unserialize($data);
					if (! $data && ! is_array($data)) {
						$data = [];
						$sql = 'DELETE FROM `'.$table.'` WHERE `key`=\'netVolumes\' AND `uid`='.$this->uid;
						$this->db->queryF($sql);
					}
				}
			}
		}
		return $data;
	}
	
	public function getDisablesCmds($useAdmin = true) {
		$disabledCmds = [];
		if (!$useAdmin || !$this->isAdmin) {
			if (!empty($this->config['disable_writes_' . (is_object($this->xoopsUser)? 'user' : 'guest')])) {
				$disabledCmds = $this->writeCmds;
			} 
			if (!empty($this->config['disabled_cmds_by_gids'])) {
				$_parts = array_map('trim', explode(':', $this->config['disabled_cmds_by_gids']));
				foreach($_parts as $_part) {
					[$_gid, $_cmds] = explode('=', $_part, 2);
					$_gid = intval($_gid);
					$_cmds = trim($_cmds);
					if (! $_gid || ! $_cmds) continue;
					if (in_array($_gid, $this->mygids)) {
						$_cmds = array_map('trim', explode(',', $_cmds));
						$disabledCmds = array_merge($disabledCmds, $_cmds);
					}
				}
				$disabledCmds = array_unique($disabledCmds);
			}
		}
		return $disabledCmds;
	}
	
	public function getRootVolumeConfigs($config, $extras = []) {
		$pluginPath = dirname(__FILE__, 2) . '/plugins/';
		$configs = explode("\n", $config);
		$files = [];
		
		$ids = [];
		foreach($configs as $_conf) {
			$_conf = trim($_conf);
			if (! $_conf || $_conf[0] === '#') continue;
			$_confs = explode(':', $_conf, 5);
			$_confs = array_map('trim', $_confs);
			[$mydirname, $plugin, $path, $title, $options] = array_pad($_confs, 5, '');
			
			if (! $this->moduleCheckRight($mydirname)) continue;
			
			$extOptions = [];
			$extOptKeys = ['uploadmaxsize' => 'uploadMaxSize', 'id'            => 'id', 'encoding'      => 'encoding', 'locale'        => 'locale', 'chmod'         => ['statOwner', 'allowChmodReadOnly']];
			$defaults = null;
			if ($options) {
				$options = str_getcsv($options, '|');
				if (is_array($options[0])) {
					$options = $options[0];
				}
				foreach($options as $_op) {
					if (strpos($_op, 'gid=') === 0) {
						$_gids = array_map('intval', explode(',', substr($_op, 4)));
						if ($_gids && $this->mygids) {
							if (! array_intersect($this->mygids, $_gids)) {
								continue 2;
							}
						}
					} else if (strpos($_op, 'defaults=') === 0) {
						[, $_tmp] = explode('=', $_op, 2);
						$defaults = $this->defaultVolumeOptions['defaults'];
						$_tmp = strtolower($_tmp);
						foreach($defaults as $_p) {
							if (strpos($_tmp, (string) $_p[0]) !== false) {
								$defaults[$_p] = true;
							}
						}
					} else if (strpos($_op, 'plugin.') === 0) {
						[$_p, $_tmp] = explode('=', substr($_op, 7), 2);
						if (! isset($extOptions['plugin'])) {
							$extOptions['plugin'] = [];
						}
						$_opts = [];
						$_p = trim($_p);
						$_parts = str_getcsv($_tmp);
						if ($_parts) {
							if (is_array($_parts[0])) {
								$_parts = $_parts[0];
							}
							foreach($_parts as $_part) {
								[$_k, $_v] = explode(':', trim($_part), 2);
								$_v = trim($_v);
								switch(strtolower($_v)) {
									case 'true':
										$_v = true;
										break;
									case 'false':
										$_v = false;
										break;
									default:
										$_fc = $_v[0];
										$_lc = substr($_v, -1);
										if ($_fc === '`' && $_lc === '`') {
											try {
												eval('$_v = '. trim($_v, '`') . ' ;');
											} catch (Exception $e) { continue 2; }
										} else if ($_fc === '(' && $_lc === ')') {
											try {
												eval('$_v = array'. $_v . ' ;');
												if (! is_array($_v)) {
													continue 2;
												}
											} catch (Exception $e) { continue 2; }
										} else {
											is_numeric($_v) && ($_v = strpos($_v, '.')? (float)$_v : (int)$_v);
										}
								}
								$_opts[trim($_k)] = $_v;
							}
						}
						if ($_opts) {
							$extOptions['plugin'][$_p] = $_opts;
						}
					} else {
						[$key, $value] = explode('=', $_op);
						$key = trim($key);
						$lKey = strtolower($key);
						if (isset($extOptKeys[$lKey])) {
							if (is_array($extOptKeys[$lKey])) {
								foreach($extOptKeys[$lKey] as $_key) {
									$extOptions[$_key] = trim($value);
								}
							} else {
								$extOptions[$extOptKeys[$lKey]] = trim($value);
							}
						}
						if (substr($key, 0, 3) === 'ext') {
							$extOptions[$key] = trim($value);
						}
					}
				}
			}
			if (is_array($defaults)) {
				$extOptions['defaults'] = $defaults;
			}
			
			if ($title === '') $title = $mydirname;
			$path = trim($path, '/');
			$path = ($path === '')? '/' : '/' . $path . '/';
			$src = $pluginPath . $plugin . '/volume.php';
			if (is_file($src)) {
				$extra = $extras[$mydirname.':'.$plugin] ?? [];
				
				//reset value
				$isAdmin = $this->isAdmin;
				$mConfig = $this->config;
				$mDirname = $this->mydirname;
				
				$files[] = compact('src', 'mydirname', 'title', 'path', 'extra', 'extOptions', 'isAdmin', 'mConfig', 'mDirname');
			}
		}
		$files['disabledCmds'] = $this->getDisablesCmds();
		return $files;
	}
	
	public function buildRootVolumes($configs) {
		$roots = [];
		$disabledCmds = $configs['disabledCmds'];
		unset($configs['disabledCmds']);
		foreach($configs as $config) {
			$raw = null;
			extract($config);
			if ($raw) {
				$roots[] = $raw;
				continue;
			}
			
			$volumeOptions = [];
			if (@include $src) {
				if ($volumeOptions) {
					!isset($volumeOptions['disabled']) && ($volumeOptions['disabled'] = []);
					!isset($volumeOptions['id']) && ($volumeOptions['id'] = '_' . $mydirname);
					if (!empty($volumeOptions['readonly'])) {
						$volumeOptions['disabled'] = array_merge($this->writeCmds, is_array($volumeOptions['disabled'])? $volumeOptions['disabled'] : []);
					}
					$volumeOptions = array_replace_recursive($this->defaultVolumeOptions, $volumeOptions, $extra, $extOptions);
					if ($disabledCmds) {
						if (!isset($volumeOptions['disabled']) || !is_array($volumeOptions['disabled'])) {
							$volumeOptions['disabled'] = [];
						}
						$volumeOptions['disabled'] = array_merge($volumeOptions['disabled'], $disabledCmds);
					}
					if (isset($ids[$volumeOptions['id']])) {
						$i = 1;
						while(isset($ids[$volumeOptions['id']])){
							$volumeOptions['id'] = preg_replace('/\d+$/', '', $volumeOptions['id']);
							$volumeOptions['id'] .= $i++;
						}
					}
					$ids[$volumeOptions['id']] = true;
					$roots[] = $volumeOptions;
				}
			}
		}
		return $roots;
	}
	
	public function getAutoSyncSec() {
		if (isset($this->config['autosync_sec_admin'])) {
			if ($this->isAdmin) {
				return intval($this->config['autosync_sec_admin']);
			} else if ($this->inSpecialGroup) {
				return intval($this->config['autosync_sec_spgroups']);
			} else if ($this->uid > 0) {
				return intval($this->config['autosync_sec_user']);
			} else {
				return intval($this->config['autosync_sec_guest']);
			}
		}
		return 0;
	}
	
	private function moduleCheckRight($dirname) {
		static $module_handler = null;
	
		$ret = false;
	
		if (is_null($module_handler)) {
			$module_handler = xoops_getHandler('module');
		}
	
		if ($XoopsModule = $module_handler->getByDirname($dirname)) {
			$moduleperm_handler = xoops_getHandler('groupperm');
			$ret = ($moduleperm_handler->checkRight('module_read', $XoopsModule->getVar('mid'), (is_object($this->xoopsUser)? $this->xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS)));
		}
	
		return $ret;
	}
	
	private function getAdminGroups($dirname = '') {
		$aGroups = [];
		if ($dirname === '') {
			$dirname = $this->mydirname;
			$module_handler = xoops_getHandler('module');
			$XoopsModule = $module_handler->getByDirname($dirname);
		} else {
			$XoopsModule = $this->xoopsModule;
		}
		if ($XoopsModule) {
			$mid = $XoopsModule->getVar('mid');
			$hGroupperm = xoops_getHandler('groupperm');
			$hGroup = xoops_getHandler('group');
			$groups = $hGroup->getObjects(null, true);
			foreach($groups as $gid => $group) {
				if ($hGroupperm->checkRight('module_admin', $mid, $gid)) {
					$aGroups[] = $group;
				}
			}
		}
		return $aGroups;
	}
	
	public function setConfig($config) {
		$this->config = $config;
		$this->inSpecialGroup = (array_intersect($this->mygids, ( $config['special_groups'] ?? [] )));
	}
	
	public function setLogfile($path = '') {
		if ($path) {
			$this->file = $path;
			$dir = dirname($path);
			if (!is_dir($dir)) {
				mkdir($dir);
			}
		}
	}
	
	public function netmountPreCallback() {
		// check session table type
		if (strlen(serialize($_SESSION)) > 64000) {
			// expand session table size, change type to "MEDIUMBLOB"
			$stype = $this->getSessionTableType();
			if ($stype !== 'mediumblob' && $stype !== 'longblob') {
				$this->db->queryF('ALTER TABLE `'.$this->db->prefix('session').'` CHANGE `sess_data` `sess_data` MEDIUMBLOB NOT NULL');
			}
		}
	}
	
	public function netmountCallback($cmd, $result, $args, $elfinder) {
		if (is_object($this->xoopsUser) && (!empty($result['sync']) || !empty($result['added']) || !empty($result['removed']) || !empty($result['changed']))) {
			if ($cmd === 'rename') {
				if (empty($result['changed']) || empty($result['changed'][0]['isroot']) || empty($result['changed'][0]['options']['netkey'])) {
					return;
				}
			}
			$this->saveNetmoutData($elfinder->getSession());
		}
	}
	
	public function saveNetmoutData($session) {
		$userRoll = $this->getUserRoll();
		if ($uid = $userRoll['uid']) {
			$table = $this->db->prefix($this->mydirname.'_userdat');
			$netVolumes = $this->db->quoteString(serialize($session->get('netvolume', [])));
			$sql = 'SELECT `id` FROM `'.$table.'` WHERE `key`=\'netVolumes\' AND `uid`='.$uid;
			if ($res = $this->db->query($sql)) {
				if ($this->db->getRowsNum($res) > 0) {
					$sql = 'UPDATE `'.$table.'` SET `data`='.$netVolumes.', `mtime`='.time().' WHERE `key`=\'netVolumes\' AND `uid`='.$uid;
				} else {
					$sql = 'INSERT `'.$table.'` SET `key`=\'netVolumes\', `uid` = '.$uid.', `data`='.$netVolumes.', `mtime`='.time();
				}
				$this->db->queryF($sql);
			}
		}
	}

	protected function tokenDataGC() {
		$files = glob($this->tokeDataPrefix . '*', GLOB_NOSORT);
		if ($files) {
			$now = time();
			foreach($files as $_f) {
				if (filemtime($_f) < $now) {
					unlink($_f);
				}
			}
		}
	}

	public function editorPreCallback($cmd, &$args, $elfinder, $volume) {
		if ($args['name'] === 'ZohoOffice') {
			if (! empty($args['method']) && $args['method'] === 'init') {
				$token = $this->setTokenData(['require' => ['cmd' => 'editor', 'id' => $args['args']['target'], 'name' => 'ZohoOffice', 'method' => 'save'], 'uid' => $this->uid]);
				$cdata = empty($args['args']['cdata']) ? '' : $args['args']['cdata'];
				$args['args']['cdata'] = $cdata . '&' . _MD_XELFINDER_PROXY_TOKEN_KEY . '=' . $token;
			}
		}
	}
	
	public function setTokenData($data, $expires = 43200/* 12h */) {
		$this->tokenDataGC();
		$serData = serialize($data);
		$token = md5(_MD_XELFINDER_PROXY_TOKEN_KEY . $serData);
		$file = $this->tokeDataPrefix . $token . '.dat';
		file_put_contents($file, $serData);
		touch($file, time() + $expires);
		return $token;
	}

	public function notifyMail($cmd, $result, $args, $elfinder) {
		if (!empty($result['added'])) {
			$mail = false;
			if (is_object($this->xoopsUser)) {
				if ($this->isAdmin) {
					$mail = in_array(XOOPS_GROUP_ADMIN, $this->config['mail_notify_group']);
				} else {
					$mail = (array_intersect($this->config['mail_notify_group'], $this->mygids));
				}
			} else {
				$mail = ($this->config['mail_notify_guest']);
			}
			
			if ($mail) {
				$config_handler = xoops_getHandler('config');
				$xoopsConfig = $config_handler->getConfigsByCat(XOOPS_CONF);
				
				$sep = "\n".str_repeat('-', 40)."\n";
				$self = XOOPS_MODULE_URL . '/' . $this->mydirname . '/connector.php';
				if (is_object($this->xoopsUser)) {
					$uname = $this->xoopsUser->uname('n');
					$uid = $this->xoopsUser->uid();
				} else {
					$uname = $xoopsConfig['anonymous'];
					$uid = 0;
				}
				$date = date('c');
				
				$head = <<<EOD
USER: $uname
UID: $uid
IP: {$_SERVER['REMOTE_ADDR']}
CMD: $cmd
DATE: $date
EOD;
				$msg = [];
				
				foreach ($result['added'] as $file) {
					
					$url = 'unknown';
					if (!empty($file['url'])) {
						$url = ($file['url'] !=  1)? $file['url'] : 'ondemand';
					} else {
						$url = $self . '?cmd=file&target='.$file['hash'];
					}
					$dl = $self . '?cmd=file&download=1&target='.$file['hash'];
					$hash = $file['hash'];
					$path = $elfinder->realpath($file['hash']);
					$name = $file['name'];
					$manager = XOOPS_MODULE_URL . '/' . $this->mydirname . '/manager.php?admin=1#elf_' . $file['phash'];
					$msg[] = <<<EOD
HASH: $hash
PATH: $path
NAME: $name
URL: $url
DOWNLOAD: $dl
MANAGER: $manager
EOD;
				}
			
				$sitename = $xoopsConfig['sitename'];
				$modname = $this->xoopsModule->getVar('name');
				$subject = '[' . $modname . '] Cmd: "'.$cmd.'" Report';
				$message = join($sep, $msg);
				if (strtoupper(_CHARSET) !== 'UTF-8') {
					ini_set('default_charset', _CHARSET);
					if (version_compare(PHP_VERSION, '5.6', '<')) {
						ini_set('mbstring.internal_encoding', _CHARSET);
					} else if (ini_get('mbstring.internal_encoding')) {
						@ini_set('mbstring.internal_encoding', '');
					}
					$message = mb_convert_encoding($message, _CHARSET, 'UTF-8');
				}
				
				$xoopsMailer = getMailer();
				$xoopsMailer->useMail();
				$xoopsMailer->setFromName($sitename.':'.$modname);
				$xoopsMailer->setSubject($subject);
				$xoopsMailer->setBody($head.$sep.$message);
				$xoopsMailer->setToGroups($this->getAdminGroups());
				$xoopsMailer->send();
				$xoopsMailer->reset();
			
				if (strtoupper(_CHARSET) !== 'UTF-8') {
					ini_set('default_charset', 'UTF-8');
					if (version_compare(PHP_VERSION, '5.6', '<')) {
						ini_set('mbstring.internal_encoding', 'UTF-8');
					} else if (ini_get('mbstring.internal_encoding')) {
						@ini_set('mbstring.internal_encoding', '');
					}
				}
			}
		}
	}
	
	public function changeAddParent($cmd, &$result, $args, $elfinder) {
		if (! empty($result['changed'])) {
			if (($target = $result['changed'][0]['phash'])
					&& ($volume = $elfinder->getVolume($target))){
				if ($parents = $volume->parents($target, true)) {
					$exist = [];
					foreach($result['changed'] as $changed) {
						$exist[$changed['hash']] = true;
					}
					foreach($parents as $changed) {
						if (! isset($exist[$changed['hash']])) {
							$result['changed'][] = $changed;
						}
					}
				}
			}
		}
	}
	
	/**
	 * Create log record
	 *
	 * @param  string   $cmd       command name
	 * @param  array    $result    command result
	 * @param  array    $args      command arguments from client
	 * @param  elFinder $elfinder  elFinder instance
	 * @return void|true
	 * @author Dmitry (dio) Levashov
	 **/
	public function log($cmd, $result, $args, $elfinder) {
		$log = $cmd.' ['.date('d.m H:s')."]\n";
		
		if (!empty($result['error'])) {
			$log .= "\tERROR: ".implode(' ', $result['error'])."\n";
		}
	
		if (!empty($result['warning'])) {
			$log .= "\tWARNING: ".implode(' ', $result['warning'])."\n";
		}
	
		if (!empty($result['removed'])) {
			foreach ($result['removed'] as $file) {
				// removed file contain additional field "realpath"
				$log .= "\tREMOVED: ".$elfinder->realpath($file['hash'])."\n";
			}
		}
	
		if (!empty($result['added'])) {
			foreach ($result['added'] as $file) {
				$log .= "\tADDED: ".$elfinder->realpath($file['hash'])."\n";
			}
		}
	
		if (!empty($result['changed'])) {
			foreach ($result['changed'] as $file) {
				$log .= "\tCHANGED: ".$elfinder->realpath($file['hash'])."\n";
			}
		}
		
		$this->write($log);
	}
	
	/**
	 * Write log into file
	 *
	 * @param  string  $log  log record
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function write($log) {
	
		if ($this->file && ($fp = @fopen($this->file, 'a'))) {
			fwrite($fp, $log."\n");
			fclose($fp);
		}
	}
	
	/**
	 * JPEG image auto rotation by EXIF info for OnUpLoadPreSave callback
	 * 
	 * @param string $path
	 * @param string $name
	 * @param string $src
	 * @param object $elfinder
	 * @param object $volume
	 * @return boolean
	 */
	public function autoRotateOnUpLoadPreSave(&$path, &$name, $src, $elfinder, $volume) {
		if (! class_exists('HypCommonFunc') || version_compare(HypCommonFunc::get_version(), '20150515', '<')) {
			return false;
		}
		$srcImgInfo = @getimagesize($src);
		if ($srcImgInfo === false) {
			return false;
		}
		if (! in_array($srcImgInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_JPEG2000])) {
			return false;
		}
		$ret = HypCommonFunc::rotateImage($src, 0, 95, $srcImgInfo);
		// remove exif gps info
		HypCommonFunc::removeExifGps($src, $srcImgInfo);
		return ($ret);
	}
	
	/**
	 * Get DB session table data type
	 * 
	 * @return string
	 */
	public function getSessionTableType() {
		$db = $this->db;
		$sql = 'SHOW COLUMNS FROM `'. $db->prefix('session') .'` WHERE Field = \'sess_data\'';
		if ($res = $db->queryF($sql)) {
			if ($row = $db->fetchArray($res)) {
				return strtolower($row['Type']);
			}
		}
		return '';
	}
	
	/**
	 * Get uname by uid
	 * @param int $uid
	 * @return string
	 */
	public static function getUnameByUid($uid){
		$uname = null;
  static $unames = [];
		static $db = null;
	
		$uid = (int)$uid;
		if (isset($unames[$uid])) {
			return $unames[$uid];
		}
		
		if (is_null($db)) {
			$db = XoopsDatabaseFactory::getDatabaseConnection();
		}
		
		if ($uid === 0) {
			$config_handler = xoops_getHandler('config');
			$xoopsConfig = $config_handler->getConfigsByCat(XOOPS_CONF);
			$uname = $xoopsConfig['anonymous'];
			if (self::$dbCharset === 'utf8' && strtoupper(_CHARSET) !== 'UTF-8') {
				$uname = mb_convert_encoding($uname, 'UTF-8', _CHARSET);
			}
		} else {
			$query = 'SELECT `uname` FROM `'.$db->prefix('users').'` WHERE uid=' . $uid . ' LIMIT 1';
			if ($result = $db->query($query)) {
				[$uname] = $db->fetchRow($result);
			}
			if ((string)$uname === '') {
				return self::getUnameByUid(0);
			}
		}
		if (self::$dbCharset !== 'utf8' && strtoupper(_CHARSET) !== 'UTF-8') {
			$uname = mb_convert_encoding($uname, 'UTF-8', _CHARSET);
		}
		return $unames[$uid] = $uname;
	}
	
	/**
	 * Sets the default client character set
	 * 
	 * @param string $charset
	 * @return bool
	 */
	public static function dbSetCharset($charset = 'utf8') {
		static $link = null;
		if (is_null($link)) {
			$db = XoopsDatabaseFactory::getDatabaseConnection();
			$link = (is_object($db->conn) && get_class($db->conn) === 'mysqli')? $db->conn : false;
		}
		self::$dbCharset = $charset;
		if ($link) {
			return mysqli_set_charset($link, $charset);
		} else {
			return mysql_set_charset($charset);
		}
	}
	
	/**
	 * Get admin group ids by module directory name
	 * 
	 * @param string $dirname
	 * @return  array
	 */
	public static function getAdminGroupIds($dirname) {
		static $res = [];
		if (isset($res[$dirname])) {
			return $res[$dirname];
		}
		$ids = [XOOPS_GROUP_ADMIN];
		$module_handler = xoops_getHandler('module');
		$XoopsModule = $module_handler->getByDirname($dirname);
		if ($XoopsModule) {
			$mid = $XoopsModule->getVar('mid');
			$hGroupperm = xoops_getHandler('groupperm');
			$hGroup = xoops_getHandler('group');
			$groups = $hGroup->getObjects(null, true);
			foreach($groups as $gid => $group) {
				if ($hGroupperm->checkRight('module_admin', $mid, $gid)) {
					$ids[] = $gid;
				}
			}
		}
		$res[$dirname] = $ids;
		return $ids;
	}
	
	protected function destroySessionVar() {
		unset(
			$_SESSION['xoopsUserId'],
			$_SESSION['xoopsUserGroups'],
			$_SESSION['xoopsUserTheme'],
			$_SESSION['XELFINDER_RF_'.$this->mydirname]
		);
	}
	
	protected function login($session, $xoopsConfig) {
		$uname = $_POST['uname'] ?? '';
		$pass = $_POST['pass'] ?? '';
		if (strtoupper(_CHARSET) !== 'UTF-8') {
			$uname = mb_convert_encoding($uname, _CHARSET, 'UTF-8');
		}

		$member_handler = xoops_getHandler('member');
		$myts = method_exists('MyTextsanitizer', 'sGetInstance')? MyTextsanitizer::sGetInstance() : MyTextsanitizer::getInstance();
		$user = $member_handler->loginUser(addslashes($myts->stripSlashesGPC($uname)), addslashes($myts->stripSlashesGPC($pass)));
		if ($user) {
			// check site current status
			if (! $user->getVar('level')) {
				return false;
			}
			$groups = $user->getGroups();
			if ($xoopsConfig['closesite'] == 1) {
				$allowed = false;
				foreach ($groups as $group) {
					if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
						$allowed = true;
						break;
					}
				}
				if (!$allowed) {
					return false;
				}
			}
			// reset session
			$this->destroySessionVar();
			// set login status
			$user->setVar('last_login', time());
			$member_handler->insertUser($user, true);
			$_SESSION['xoopsUserId'] = $user->getVar('uid');
			$_SESSION['xoopsUserGroups'] = $groups;
			$user_theme = $user->getVar('theme');
			if (in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
				$_SESSION['xoopsUserTheme'] = $user_theme;
			}
			// RMV-NOTIFY
			// Perform some maintenance of notification records
			$notification_handler = xoops_getHandler('notification');
			$notification_handler->doLoginMaintenance($user->getVar('uid'));
		} else {
			return false;
		}
		
		$this->setXoopsUser($user);
		
		return true;
	}

	protected function setXoopsUser($user = null) {
		if (is_object($user)) {
			$this->xoopsUser = $user;
			$this->isAdmin = (is_object($user) && $user->isAdmin($this->xoopsModule->getVar('mid')));
			$this->mygids = is_object($user)? $user->getGroups() : [XOOPS_GROUP_ANONYMOUS];
			$this->uid = is_object($user)? intval($user->getVar('uid')) : 0;
			if ($this->config) {
				$this->inSpecialGroup = (array_intersect($this->mygids, ( $this->config['special_groups'] ?? [] )));
			}
		} else {
			$this->xoopsUser = null;
			$this->isAdmin = false;
			$this->mygids = [XOOPS_GROUP_ANONYMOUS];
			$this->uid = 0;
			$this->inSpecialGroup = false;
		}
	}
}
