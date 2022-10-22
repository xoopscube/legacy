<?php

/**
 * Simple elFinder driver for MySQL.
 *
 * @author Dmitry (dio) Levashov,
 * @author Naoki Sawada
 **/
class elFinderVolumeXoopsXelfinder_db extends elFinderVolumeDriver {

	/**
	 * Driver id
	 * Must be started from letter and contains [a-z0-9]
	 * Used as part of volume id
	 *
	 * @var string
	 **/
	protected $driverId = 'xe';

	protected $mydirname = '';

	protected $x_mid;
	protected $x_uid = 0;
	protected $x_uname = '';
	protected $x_groups = array();
	protected $x_isAdmin = false;
	protected $x_adminGroups = array();

	protected $groupHomeId = -999999999;
	protected $makeUmask = '';
	protected $makePerm = '';

	/**
	 * Database object
	 *
	 * @var mysqli
	 **/
	protected $db = null;

	/**
	 * Tables to store files
	 *
	 * @var string
	 **/
	protected $tbf = '';

	/**
	 * Directory for tmp files
	 * If not set driver will try to use tmbDir as tmpDir
	 *
	 * @var string
	 **/
	protected $tmpPath = '';

	/**
	 * Numbers of sql requests (for debug)
	 *
	 * @var int
	 **/
	protected $sqlCnt = 0;

	/**
	 * Last db error message
	 *
	 * @var string
	 **/
	protected $dbError = '';

	/**
	* Debug message
	*
	* @var string
	**/
	protected $debugMsg = '';

	/**
	 * Constructor
	 * Extend options with required fields
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function __construct() {
		$this->options['path'] = '1';
		$this->options['separator'] = '/';
		$this->options['mydirname'] = 'xelfinder';
		$this->options['checkSubfolders'] = true;
		$this->options['tempPath'] = XOOPS_MODULE_PATH . '/'._MD_ELFINDER_MYDIRNAME.'/cache';
		$this->options['tmbPath'] = $this->options['tempPath'].'/tmb/';
		$this->options['tmbURL'] = $this->options['tempPath'].'/tmb/';
		$this->options['default_umask'] = '8bb';
		$this->options['autoResize'] = false;
	}

	public function savePerm($target, $perm, $umask, $gids, $mime_filter, $uid = null) {
		if (!preg_match('/^[0-9a-f]{3}$/', $perm) || ($umask && !preg_match('/^[0-9a-f]{3}$/', $umask))) {
			return $this->setError(elFinder::ERROR_INV_PARAMS);
		}
		$path = $this->decode($target);
		$stat = $this->stat($path);
		if (empty($stat)) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		if (empty($stat['isowner'])) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		if (empty($gids)) {
			$gids = ',';
		} else {
			$gids = join(',', $gids);
		}
		if (is_numeric($uid)) {
			$uid = intval($uid);
		} else {
			$uid = intval($stat['uid']);
		}
		
		$mime_filter = $this->db->quoteString($mime_filter);
		
		if ($umask) {
			$sql = sprintf('UPDATE %s SET `perm`="%s", `uid`=%d, `gids`="%s", `umask`="%s", `mime_filter`=%s WHERE `file_id` = "%d" LIMIT 1', $this->tbf, $perm, $uid, $gids, $umask, $mime_filter, $path);
		} else {
			$sql = sprintf('UPDATE %s SET `perm`="%s", `uid`=%d, `gids`="%s", `mime_filter`=%s WHERE `file_id` = "%d" LIMIT 1', $this->tbf, $perm, $uid, $gids, $mime_filter, $path);
		}
		if ($this->query($sql) && $this->db->getAffectedRows() > 0) {
			unset($this->cache[(int)$path]);
			return $stat = $this->stat($path);
		} else {
			//$this->_debug($sql);
			return $this->setError(elFinder::ERROR_SAVE, $stat['name']);
		}
	}
	
	public function getGroups($target) {
		$groups = array();
		
		$path = $this->decode($target);
		$stat = $this->stat($path);
		if (empty($stat)) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		if (empty($stat['isowner'])) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}		
		
		$gids = $this->getGroupsByUid($stat['uid']);
		if (defined('_CHARSET') && _CHARSET === 'UTF-8') {
			$xoopsMenber = xoops_getHandler('member');
			$list = $xoopsMenber->getGroupList();
		} else {
			$xoopsGroup = xoops_getHandler('group');
			$_groups = $xoopsGroup->getObjects(null, true);
			$list = array();
			foreach (array_keys($_groups) as $i) {
				$list[$i] = htmlspecialchars($_groups[$i]->getVar('name', 'n'), ENT_QUOTES, 'UTF-8');
			}
		}
		$targetGroups = array_map('intval', explode(',', $stat['gids']));
		foreach($gids as $id) {
			$id = (int)$id;
			if (isset($list[$id])) {
				$groups[$id] = array('name' => $list[$id], 'on' => (in_array($id, $targetGroups))? 1 : 0);
			}
		}
		
		$uname = '';
		if ($stat['uid'] == $this->x_uid) {
			$uname = $this->x_uname;
		} else {
			if ($stat['uid']) {
				$module_handler = xoops_getHandler('module');
				$user_handler = xoops_getHandler('user');
				$user = $user_handler->get($stat['uid']);
				if (is_object($user)) {
					$uname = $user->uname('s');
				}
			}
		}
		if ($uname === '') {
			$config_handler = xoops_getHandler('config');
			$xoopsConfig = $config_handler->getConfigsByCat(XOOPS_CONF);
			$uname = $this->strToUTF8($xoopsConfig['anonymous']);
		}
		
		return array('groups' => $groups, 'uname' => $uname);
	}
	
	private function updateDirTimestamp($dir, $mtime, $recursive = true) {
		$sql = 'UPDATE %s SET mtime=%d WHERE file_id=%d AND mtime<%d LIMIT 1';
		$sql = sprintf($sql, $this->tbf, $mtime, $dir, $mtime);
		if ($this->query($sql)) {
			unset($this->cache[(int)$dir]);
		}
		if ($recursive && $dir != $this->root) {
			if ($parent = $this->_dirname($dir)) {
				$this->updateDirTimestamp($parent, $mtime, true);
			}
		}
	}
	
	/*********************************************************************/
	/*                        INIT AND CONFIGURE                         */
	/*********************************************************************/

	/**
	 * Prepare driver before mount volume.
	 * Connect to db, check required tables and fetch root path
	 *
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function init() {

		$this->db = & XoopsDatabaseFactory::getDatabaseConnection();
		if (! $this->db) {
			return false;
		}

		xoops_elFinder::dbSetCharset('utf8');

		$this->mydirname = $this->options['mydirname'];

		$this->tbf = $this->db->prefix($this->mydirname) . '_file';

		$module_handler = xoops_getHandler('module');
		$XoopsModule = $module_handler->getByDirname($this->mydirname);
		$module = $XoopsModule->getInfo();
		$this->x_mid = $XoopsModule->getVar('mid');

		global $xoopsUser;
		if (is_object($xoopsUser)) {
			$this->x_uid = $xoopsUser->getVar('uid');
			$this->x_uname = $this->strToUTF8($xoopsUser->uname('n'));
			$this->x_groups = $xoopsUser->getGroups();
			$this->x_isAdmin = (!empty($_REQUEST['admin']) && $xoopsUser->isAdmin($this->x_mid));
		} else {
			$this->x_uid = 0;
			$this->x_groups = array(XOOPS_GROUP_ANONYMOUS);
		}

		$this->x_adminGroups = xoops_elFinder::getAdminGroupIds($this->mydirname);

		if (is_null($this->options['syncChkAsTs'])) {
			$this->options['syncChkAsTs'] = true;
		}

		return true;
	}

	/**
	 * Close connection
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function umount() {
		//$this->db->close();
	}

	/**
	 * Return debug info for client
	 *
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function debug() {
		$debug = parent::debug();
		$debug['sqlCount'] = $this->sqlCnt;
		if ($this->dbError) {
			$debug['dbError'] = $this->dbError;
		}
		if ($this->debugMsg) {
			$debug['msg'] = $this->debugMsg;
		}
		return $debug;
	}

	protected function _debug($msg) {
		$this->debugMsg[] = $msg;
	}

	/**
	 * Perform sql query and return result.
	 * Increase sqlCnt and save error if occured
	 *
	 * @param  string  $sql  query
	 * @return misc
	 * @author Dmitry (dio) Levashov
	 * @author Naoki Sawada
	 **/
	protected function query($sql) {
		$this->sqlCnt++;
		$res = $this->db->queryF($sql);
		if (!$res) {
			$this->dbError = $this->db->error();
			$this->setError($this->dbError.'.');
		}
		return $res;
	}

	/**
	 * Create empty object with required mimetype
	 *
	 * @param  string  $path  parent dir path
	 * @param  string  $name  object name
	 * @param  string  $mime  mime type
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 * @author Naoki Sawada
	 **/
	protected function make($path, $name, $mime, $home_of = 'NULL') {
		if ($name === '') return false;  // It's insurance 
		
		$time = time();
		$gid = 0;;
		$umask = $this->getUmask($path, $gid);
		if ($home_of !== 'NULL') {
			$home_of = intval($home_of);
			if ($home_of < 0 && $home_of != $this->groupHomeId) {
				$gid = abs($home_of);
			}
		}
		if ($this->makeUmask) {
			$umask = $this->makeUmask;
			$this->makeUmask = '';
		}
		if ($this->makePerm) {
			$perm = $this->makePerm;
			$this->makePerm = '';
		} else {
			$perm = $this->getDefaultPerm($umask);
		}

		$sql = 'INSERT INTO %s (`parent_id`, `name`, `ctime`, `mtime`, `perm`, `umask` , `uid`, `gid`, `home_of`, `mime`) VALUES '
		                    . '( %d,          %s,     %d,      %d,     "%s",   "%s",     "%d",  "%d",   %s,     "%s")';
		$sql = sprintf($sql, $this->tbf, intval($path), $this->db->quoteString($name), $time, $time, $perm, $umask, $this->x_uid, $gid, $home_of, $mime);
		//$this->_debug($sql);
		if ($this->query($sql) && $this->db->getAffectedRows() > 0) {
			if ($mime !== 'directory') {
				$id = $this->db->getInsertId();
				$local = $this->readlink($id, true);
				if ($local) return true;
			} else {
				return true;
			}
		}
		return false;
	}

	protected function getUmask($dir, & $gid) {
		$umask = '';
		if ($dir > 0) {
			$sql = 'SELECT `umask`, `home_of` FROM '.$this->tbf.' WHERE `file_id`='.intval($dir).' LIMIT 1';
			//$this->_debug($sql);
			if ($res = $this->db->query($sql)) {
				list($umask, $home_of) = $this->db->fetchRow($res);
				$gid = ($home_of < 0)? abs($home_of) : 0;
			}
		}
		return $umask? $umask : $this->options['default_umask'];
	}

	protected function getDefaultPerm($umask) {
		$base = 0xfff;
		return strval(dechex($base - intval($umask, 16)));
	}

	protected function getGroupsByUid($uid) {
		static $groups = array();

		if (isset($groups[$uid])) return $groups[$uid];

		if ($uid) {
			$user_handler = xoops_getHandler('user');
			$user = $user_handler->get( $uid );
			$groups[$uid] = $user->getGroups();
			$user = null;
			unset($user);
		} else {
			$groups[$uid] = array( XOOPS_GROUP_ANONYMOUS );
		}

		return $groups[$uid];
	}

	protected function setAuthByPerm(& $dat) {

		$isOwner = ($this->x_isAdmin || ($dat['uid'] && $dat['uid'] == $this->x_uid));
		if ($dat['home_of'] < 0) {
			$inGroup = (in_array(abs($dat['home_of']), $this->x_groups));
		} else if ($dat['gid']) {
			$inGroup = (in_array($dat['gid'], $this->x_groups));
		} else {
			if ($dat['gids'] === '') {
				$dat['gids'] = join(',', $this->getGroupsByUid($dat['uid']));
				$sql = 'UPDATE '.$this->tbf.' SET `gids`=\''.$dat['gids'].'\' WHERE `file_id`='.$dat['file_id'].' LIMIT 1';
				$this->query($sql);
			}
			$inGroup = (array_intersect(explode(',', $dat['gids']), $this->x_groups));
		}
		$perm = strval($dat['perm']);
		$own = isset($perm[0])? intval($perm[0], 16) : 0;
		$grp = isset($perm[1])? intval($perm[1], 16) : 0;
		$gus = isset($perm[2])? intval($perm[2], 16) : 0;

		if ($isOwner) $dat['isowner'] = 1;
		$dat['hidden'] = !(($isOwner && (8 & $own) !== 8) || ($inGroup && (8 & $grp) !== 8) || (8 & $gus) !== 8);
		$dat['read']   =  (($isOwner && (4 & $own) === 4) || ($inGroup && (4 & $grp) === 4) || (4 & $gus) === 4);
		$dat['write']  =  (($isOwner && (2 & $own) === 2) || ($inGroup && (2 & $grp) === 2) || (2 & $gus) === 2);
		$dat['locked'] = !(($isOwner && (1 & $own) === 1) || ($inGroup && (1 & $grp) === 1) || (1 & $gus) === 1);
		
		if ($dat['mime'] !== 'directory' && $dat['gids'] && array_intersect(explode(',', $dat['gids']), $this->x_adminGroups)) {
			if (! isset($dat['options'])) {
				$dat['options'] = array();
			}
			$dat['options']['dispInlineRegex'] = '.*';
		}
	}

	protected function checkHomeDir() {
		if ($this->x_isAdmin) {
			if ($this->options['use_group_dir']) {
				$group_parent = $this->root;
				if ($this->options['group_dir_parent']) {
					$sql = 'SELECT file_id FROM '.$this->tbf.' WHERE home_of = ' . $this->groupHomeId . ' LIMIT 1';
					if (($res = $this->query($sql)) && $this->db->getRowsNum($res)) {
						list($group_parent) = $this->db->fetchRow($res);
					} else {
						$this->make($this->root, $this->options['group_dir_parent'], 'directory', $this->groupHomeId);
						$group_parent = $this->db->getInsertId();
					}
				}

				if ($group_parent) {
					$xoopsGroup = xoops_getHandler('group');
					$groups = $xoopsGroup->getObjects(new Criteria('group_type' , 'Anonymous', '!='), true);
					$sql = 'SELECT gid FROM '.$this->tbf.' WHERE home_of < 0 AND mime = \'directory\'';
					if (($res = $this->query($sql)) && $this->db->getRowsNum($res)) {
						while ($row = $this->db->fetchRow($res)) {
							unset($groups[$row[0]]);
						}
					}
					if ($groups) {
						foreach($groups as $gid => $gobj) {
							$gname = $groups[$gid]->getVar('name', 'n');
							$gid *= -1;
							$this->makeUmask = $this->options['group_dir_umask'];
							$this->makePerm = $this->options['group_dir_perm'];
							$this->make($group_parent, $gname, 'directory', $gid);
						}
					}
				}
			}

			if ($this->options['use_guest_dir']) {
				$sql = 'SELECT file_id FROM '.$this->tbf.' WHERE home_of = 0 AND mime = \'directory\' LIMIT 1';
				if (($res = $this->query($sql)) && $this->db->getRowsNum($res) < 1) {
					$config_handler = xoops_getHandler('config');
					$xoopsConfig = $config_handler->getConfigsByCat(XOOPS_CONF);
					$this->makeUmask = $this->options['guest_dir_umask'];
					$this->makePerm = $this->options['guest_dir_perm'];
					$this->make(1, $this->strToUTF8($xoopsConfig['anonymous']), 'directory', 0);
				}
			}
		}

		if ($this->x_uid && $this->options['use_users_dir']) {
			$sql = 'SELECT file_id FROM '.$this->tbf.' WHERE home_of = '.$this->x_uid .' LIMIT 1';
			if (($res = $this->query($sql)) && $this->db->getRowsNum($res) < 1) {
				$this->makeUmask = $this->options['users_dir_umask'];
				$this->makePerm = $this->options['users_dir_perm'];
				$this->make(1, $this->x_uname, 'directory', $this->x_uid);
			}
		}
	}

	protected function strToUTF8($str) {
		if (strtoupper(_CHARSET) !== 'UTF-8') {
			$str = mb_convert_encoding($str, 'UTF-8', _CHARSET);
		}
		return $str;
	}

	/**
	* Copy files & directories into tempDir as local file
	*
	* @param  string  $mkdir  make dirctory name
	* @param  array   $files  files names list
	* @param  string  $dir    current dirctory name
	* @return array
	* @author Naoki Sawada
	**/
	protected function copyToLocalTemp(& $mkdir, $files, $dir = null) {
		$res = array();
		$tempDir = $this->options['tempPath'].DIRECTORY_SEPARATOR.$mkdir;
		if (! @ mkdir($tempDir)) {
			$tempDir = $this->options['tempPath'];
			$mkdir = '';
		}
		foreach($files as $file) {
			$id = (is_null($dir))? $file : $this->_joinPath($dir, $file);
			$stat = $this->stat($id);
			if ($stat['mime'] === 'directory') {
				if ($mkdir && $cids = $this->_scandir($id)) {
					$_cdir = $mkdir.DIRECTORY_SEPARATOR.$stat['name'];
					if ($this->copyToLocalTemp($_cdir, $cids)) {
						$res[] = $stat['name'];
					}
				}
			} else {
				if ($realpath = $this->readlink($id)) {
					if (@ copy($realpath, $tempDir.DIRECTORY_SEPARATOR.$stat['name'])) {
						$res[] = $stat['name'];
					}
				}
			}
		}
		return $res;
	}

	/**
	 * Save file or dirctory from loacl file system
	 *
	 * @param  string  $localpath          local file (or dirctory) path
	 * @param  string  $dir                directory name to save
	 * @param  string  $check_mime_accept  do check mime accept (upload spec.)
	 * @return string|bool
	 * @author Naoki Sawada
	 **/
	protected function localFileSave($localpath, $dir, $check_mime_accept = false) {
		$path = -1;
		$localpath = rtrim($localpath, DIRECTORY_SEPARATOR);
		$name = basename($localpath);
		if ($this->nameAccepted($name)) {
			$width = $height = 0;
			if (is_dir($localpath)) {
				$test = $this->_joinPath($dir, $name);
				$_stat = '';
				($test > 0) && ($_stat = $this->_stat($test)) && ($_stat = $_stat['mime']);
				$path = ($_stat === 'directory')? $test : $this->_mkdir($dir, $name);
				if ($path > 0) {
					$_ok = false;
					foreach (scandir($localpath) as $c_name) {
						if ($c_name != '.' && $c_name != '..') {
							$_res = $this->localFileSave($localpath.DIRECTORY_SEPARATOR.$c_name, $path, $check_mime_accept);
							if (!$_ok && $_res > 0) $_ok = true;
						}
					}
					if (! $_ok && $test === -1) {
						$path = -1;
						$this->_rmdir($path);
					}
				}
			} else {
				$mime = $this->mimetype($localpath);
				$upload = true; // default to allow
				if ($check_mime_accept) {
					// logic based on http://httpd.apache.org/docs/2.2/mod/mod_authz_host.html#order
					$allow  = $this->mimeAccepted($mime, $this->uploadAllow, null);
					$deny   = $this->mimeAccepted($mime, $this->uploadDeny,  null);
					if (strtolower($this->uploadOrder[0]) == 'allow') {
						// array('allow', 'deny'), default is to 'deny'
						$upload = false; // default is deny
						if (!$deny && ($allow === true)) {
							// match only allow
							$upload = true;
						}// else (both match | no match | match only deny) { deny }
					} else { // array('deny', 'allow'), default is to 'allow' - this is the default rule
						$upload = true; // default is allow
						if (($deny === true) && !$allow) {
							// match only deny
							$upload = false;
						} // else (both match | no match | match only allow) { allow }
					}
				}
				if ($upload) {
					if (strpos($mime, 'image') === 0) {
						if ($size = getimagesize($localpath)) {
							$width = $size[1];
							$height = $size[2];
						}
					}
					if ($fp = fopen($localpath, 'rb')) {
						$stat = array(
							'mime' => $mime,
							'width' => $width,
							'height' => $height );
						if ($id = $this->_save($fp, $dir, $name, $stat)) {
							$path = $id;
						}
						fclose($fp);
						@ unlink($localpath);
					}
				}
			}
		}
		return $path;
	}

	/**
	 * Recursively remove a directory
	 *
	 * @param  string $dir  path to the dirctory
	 * @return bool
	 * @author Naoki Sawada
	 **/
	protected function rrmdir($dir) {
		return $this->rmdirRecursive($dir);
	}
	
	protected function makeStat($stat) {
		if ($stat['parent_id']) {
			$stat['phash'] = $this->encode($stat['parent_id']);
		} else {
			$stat['phash'] = null;
		}
		if ($stat['mime'] == 'directory') {
			unset($stat['width']);
			unset($stat['height']);
		} else {
			unset($stat['dirs']);
		}
		$this->setAuthByPerm($stat);
		$name = $stat['name'];
		if ($stat['mime'] !== 'directory') {
			if (strpos($this->options['URL'], '?') === false) {
				$stat['url'] = $this->options['URL'].$stat['file_id'].'/'.rawurlencode($name); // Use pathinfo "index.php/[id]/[name]
			} else {
				$stat['url'] = $this->options['URL'].$stat['file_id'].'&'.rawurlencode($name);
			}
		} else {
			$stat['url'] = null;
		}
		if (!empty($stat['local_path'])) {
			$stat['_localalias'] = 1;
			$stat['alias'] = $stat['local_path'];
			$stat['write'] = 0;
		}
		if (isset($stat['uid'])) {
			$stat['owner'] = xoops_elFinder::getUnameByUid($stat['uid']);
			$stat['tooltip'] = 'Owner: ' . $stat['owner'];
		}
		
		unset($stat['file_id'], $stat['parent_id'], $stat['gid'], $stat['home_of'], $stat['local_path']);
		if (empty($stat['isowner'])) unset($stat['perm'], $stat['uid'], $stat['gids']);
		if (empty($stat['isowner']) || $stat['mime'] !== 'directory') unset($stat['umask']);
		if ($stat['mime'] !== 'directory') unset($stat['filter']);
		
		return $stat;
	}
	
	/**
	 * Save error message
	 *
	 * @param  array  error
	 * @return false
	 * @author Dmitry(dio) Levashov
	 **/
	protected function setError() {
		if (! is_array($this->error)) {
			$this->error = array();
		}
	
		foreach (func_get_args() as $err) {
			if (is_array($err)) {
				$this->error = array_merge($this->error, $err);
			} else {
				$this->error[] = $err;
			}
		}
	
		// $this->error = is_array($error) ? $error : func_get_args();
		return false;
	}
	
	/*********************************************************************/
	/*                               FS API                              */
	/*********************************************************************/

	/*********************** file stat *********************/
	
	/**
	 * Check file attribute
	 *
	 * @param  string  $path  file path (not use)
	 * @param  string  $name  attribute name (read|write|locked|hidden) (not use)
	 * @param  bool    $val   attribute value of file stat
	 * @return bool
	 * @author Naoki Sawada
	 **/
	protected function attr($path, $name, $val=false, $isDir=null) {
		return $val;
	}
	
	/**
	* Put file stat in cache and return it
	*
	* @param  string  $path   file path
	* @param  array   $stat   file stat
	* @return array
	* @author Dmitry (dio) Levashov
	**/
	protected function updateCache($path, $stat) {
		$path = (int)$path;
		$stat = parent::updateCache($path, $stat);
		if ($stat && !isset($stat['locked'])) $stat['locked'] = 0;
		return $this->cache[$path] = $stat;
	}
	
	/**
	 * Cache dir contents
	 *
	 * @param  string  $path  dir path
	 * @return void
	 * @author Dmitry Levashov
	 **/
	protected function cacheDir($path) {
		$this->dirsCache[$path] = array();

		if ($path == $this->root) {
			$this->checkHomeDir();
		}

		$sql = 'SELECT f.file_id, f.parent_id, f.name, f.size, f.mtime AS ts, f.mime,
				f.perm, f.umask, f.uid, f.gid, f.home_of, f.width, f.height, f.gids, f.mime_filter as filter, f.local_path,
				IF(ch.file_id, 1, 0) AS dirs
				FROM '.$this->tbf.' AS f
				LEFT JOIN '.$this->tbf.' AS ch ON ch.parent_id=f.file_id AND ch.mime="directory"
				WHERE f.parent_id="'.$path.'"
				GROUP BY f.file_id, f.parent_id, f.name, f.size, f.mtime, f.mime, f.perm, f.umask, f.uid, f.gid, f.home_of, f.width, f.height, f.gids, f.mime_filter, f.local_path, ch.file_id';

		$res = $this->query($sql);
		if ($res) {
			while ($row = $this->db->fetchArray($res)) {
				$id = $row['file_id'];
				if ($row['name'] === '') $row['name'] = 'Unknown';
				$row = $this->makeStat($row);
				if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
					$this->dirsCache[$path][] = $id;
				}
			}
		}
		
		$current_stat = $this->stat($path);
		if (! empty($current_stat['filter'])) {
			$filter = $this->db->quoteString($current_stat['filter']);
			$filter = substr($filter, 1, strlen($filter)-2);
			$filter = str_replace('*', '%', $filter);
			$q = array();
			foreach(explode(' ', $filter) as $val) {
				if (strpos($val, '/') === false && strpos($val, '%') === false ) {
					$val .= '%';
				}
				$q[] = '`mime` LIKE \''.$val.'\'';
			}
			$query = join(' OR ', $q);
			
			$sql = 'SELECT f.file_id, f.parent_id, f.name, f.size, f.mtime AS ts, f.mime,
			f.perm, f.umask, f.uid, f.gid, f.home_of, f.width, f.height, f.gids, f.mime_filter as filter, f.local_path
			FROM '.$this->tbf.' AS f
			WHERE '.$query;
			
			$res = $this->query($sql);
			if ($res) {
				$phash = $this->encode($path);
				while ($row = $this->db->fetchArray($res)) {
					//$id = $row['file_id'].'_'.$path;
					$id = $row['file_id'];
					$row['alias']  = $this->_path($row['file_id']);
					$row['target'] = $row['file_id'];
					$row = $this->makeStat($row);
					$row['phash'] = $phash;
					if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
						$this->dirsCache[$path][] = $id;
					}
				}
			}
		}

		return $this->dirsCache[$path];
	}

	/**
	 * Return array of parents paths (ids)
	 *
	 * @param  int   $path  file path (id)
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function getParents($path) {
		$parents = array();

		while ($path) {
			if ($file = $this->stat($path)) {
				array_unshift($parents, $path);
				$path = isset($file['phash']) ? $this->decode($file['phash']) : false;
			} else {
				break;
			}
		}

		if (count($parents)) {
			array_pop($parents);
		}
		return $parents;
	}

	/**
	 * Recursive files search
	 *
	 * @param  string  $path   dir path
	 * @param  string  $q      search string
	 * @param  array   $mimes
	 * @return array
	 * @author Naoki Sawada
	 **/
	protected function doSearch($path, $q, $mimes) {
		
		$filters = $dirs = array();
		if ($path != $this->root) {
			$dirs = $inpath = array($path);
			while($inpath) {
				$in = '('.join(',', $inpath).')';
				$inpath = array();
				$sql = 'SELECT `file_id`, `mime_filter` FROM '.$this->tbf.' WHERE `parent_id` IN '.$in.' AND `mime` = \'directory\'';
				if ($res = $this->query($sql)) {
					$_dir = array();
					while ($dat = $this->db->fetchArray($res)) {
						if ($dat['mime_filter']) {
							$filters[] = $dat['mime_filter'];
						} else {
							$inpath[] = $dat['file_id'];
						}
					}
					$dirs = array_merge($dirs, $inpath);
				}
			}
		}
		
		$result = array();
		
		if ($mimes) {
			$whrs = array();
			foreach($mimes as $mime) {
				if (strpos($mime, '/') === false) {
					$mime = $this->db->quoteString($mime);
					$mime = substr($mime, 1, strlen($mime)-2);
					$whrs[] = sprintf('`mime` LIKE \'%s/%%\'', $mime);
				} else {
					$whrs[] = sprintf('`mime` = %s', $this->db->quoteString($mime));
				}
			}
			$whr = join(' OR ', $whrs);
		} else {
			$q = $this->db->quoteString($q);
			$q = '%'.substr($q, 1, strlen($q)-2).'%';
			$whr = '`name` LIKE \''.$q.'\'';
		}
		
		$filter = '';
		if ($filters) {
			$filter = $this->db->quoteString(join(' ', $filters));
			$filter = substr($filter, 1, strlen($filter)-2);
			$filter = str_replace('*', '%', $filter);
			$_f = array();
			foreach(explode(' ', $filter) as $val) {
				if (strpos($val, '/') === false && strpos($val, '%') === false ) {
					$val .= '%';
				}
				$_f[] = '`mime` LIKE \'' . $val . '\'';
			}
			$filter = ' OR ' . join(' OR ', $_f);
		}
		if ($dirs) {
			$whr = '(' . $whr . ') AND (`parent_id` IN (' . join(',', $dirs) . ')' . $filter . ')';
		}
		
		$sql = 'SELECT `file_id`, `mime`, `uid`, `gid`, `gids`, `perm`, `home_of` FROM '.$this->tbf.' WHERE '.$whr;
		
		$res = $this->query($sql);
		if ($res) {
			while ($stat = $this->db->fetchArray($res)) {
				if (!$this->mimeAccepted($stat['mime'], $mimes)) {
					continue;
				}
				$this->setAuthByPerm($stat);
				if (empty($stat['hidden'])) {
					$stat = $this->stat($stat['file_id']);
					$stat['path'] = $this->path($stat['hash']);
					$result[] = $stat;
				}
			}
		}
		
		return $result;
	}
	
	/*********************** paths/urls *************************/

	/**
	 * Return parent directory path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _dirname($path) {
		if ($path != $this->root) {
			$sql = 'SELECT `parent_id` FROM '.$this->tbf.' WHERE file_id="'.$path.'" LIMIT 1;';
			if (($res = $this->query($sql)) && $this->db->getRowsNum($res) > 0) {
				$r = $this->db->fetchRow($res);
				return $r[0];
			}
		}
		return 1;
	}

	/**
	 * Return file name
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _basename($path) {
		$sql = 'SELECT `name` FROM '.$this->tbf.' WHERE file_id="'.$path.'" LIMIT 1;';
		if (($res = $this->query($sql)) && $this->db->getRowsNum($res) > 0) {
			$r = $this->db->fetchRow($res);
			return $r[0];
		}
		return '';
	}

	/**
	 * Join dir name and file name and return full path
	 *
	 * @param  string  $dir
	 * @param  string  $name
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _joinPath($dir, $name) {
		$sql = 'SELECT `file_id` FROM '.$this->tbf.' WHERE parent_id="'.$dir.'" AND name='.$this->db->quoteString($name);
		if (($res = $this->query($sql)) && $this->db->getRowsNum($res) > 0) {
			$r = $this->db->fetchRow($res);
			return $r[0];
		}
		return -1;
	}

	/**
	 * Return normalized path, this works the same as os.path.normpath() in Python
	 *
	 * @param  string  $path  path
	 * @return string
	 * @author Troex Nevelin
	 **/
	protected function _normpath($path) {
		return $path;
	}

	/**
	 * Return file path related to root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _relpath($path) {
		return $path;
	}

	/**
	 * Convert path related to root dir into real path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _abspath($path) {
		return intval($path);
	}

	/**
	 * Return fake path started from root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _path($path) {
		if (($file = $this->stat($path)) == false) {
			return '';
		}

		$parentsIds = $this->getParents($path);
		$path = '';
		foreach ($parentsIds as $id) {
			$dir = $this->stat($id);
			$path .= $dir['name'].$this->separator;
		}
		return $path.$file['name'];
	}

	/**
	 * Return true if $path is children of $parent
	 *
	 * @param  string  $path    path to check
	 * @param  string  $parent  parent path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _inpath($path, $parent) {
		return $path == $parent
			? true
			: in_array($parent, $this->getParents($path));
	}

	/***************** file stat ********************/
	/**
	 * Return stat for given path.
	 * Stat contains following fields:
	 * - (int)    size    file size in b. required
	 * - (int)    ts      file modification time in unix time. required
	 * - (string) mime    mimetype. required for folders, others - optionally
	 * - (bool)   read    read permissions. required
	 * - (bool)   write   write permissions. required
	 * - (bool)   locked  is object locked. optionally
	 * - (bool)   hidden  is object hidden. optionally
	 * - (string) alias   for symlinks - link target path relative to root path. optionally
	 * - (string) target  for symlinks - link target path. optionally
	 *
	 * If file does not exists - returns empty array or false.
	 *
	 * @param  string  $path    file path
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _stat($path, $rootCheck = true) {
		$sql = 'SELECT f.file_id, f.parent_id, f.name, f.size, f.mtime AS ts, f.mime,
				f.perm, f.umask, f.uid, f.gid, f.home_of, f.width, f.height, f.gids, f.mime_filter as filter, f.local_path,
				IF(ch.file_id, 1, 0) AS dirs
				FROM '.$this->tbf.' AS f
				LEFT JOIN '.$this->tbf.' AS p ON p.file_id=f.parent_id
				LEFT JOIN '.$this->tbf.' AS ch ON ch.parent_id=f.file_id AND ch.mime="directory"
				WHERE f.file_id="'.$path.'"
				GROUP BY f.file_id, f.parent_id, f.name, f.size, f.mtime, f.mime, f.perm, f.umask, f.uid, f.gid, f.home_of, f.width, f.height, f.gids, f.mime_filter, f.local_path, ch.file_id';

		$res = $this->query($sql);

		if ($res && $stat = $this->db->fetchArray($res)) {
			if ($stat['name'] === '') $stat['name'] = 'Unknown';
			return $this->makeStat($stat);
		} else if ($rootCheck && $path == $this->root) {
			$this->_mkdir(0, 'VolumeRoot');
			return $this->_stat($path, false);
		}
		return array();
	}

	/**
	 * Return true if path is dir and has at least one childs directory
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _subdirs($path) {
		return ($stat = $this->stat($path)) && isset($stat['dirs']) ? $stat['dirs'] : false;
	}

	/**
	 * Return object width and height
	 * Usualy used for images, but can be realize for video etc...
	 *
	 * @param  string  $path  file path
	 * @param  string  $mime  file mime type
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _dimensions($path, $mime) {
		return ($stat = $this->stat($path)) && isset($stat['width']) && isset($stat['height']) ? $stat['width'].'x'.$stat['height'] : '';
	}

	/******************** file/dir content *********************/
	
	/**
	* Return symlink target file
	*
	* @param  string  $path  link path
	* @return string
	* @author Dmitry (dio) Levashov
	**/
	protected function readlink($path, $make = false) {
		if (! $path) return false;
		$stat = $this->stat($path);
		if (empty($stat['_localalias'])) {
			$link = $this->options['filePath'] . $path;
		} else {
			$link = $stat['alias'];
			if (substr($link, 1, 1) === '/') {
				$_head = substr($link, 0, 1);
				if (strpos($link, '%') !== false) {
					$link = dirname($link) . DIRECTORY_SEPARATOR . rawurldecode(basename($link));
				}
				switch($_head) {
					case 'R':
						$link = XOOPS_ROOT_PATH . substr($link, 1);
						break;
					case 'T':
						$link = XOOPS_TRUST_PATH . substr($link, 1);
						break;
				}
			}
		}
		if (is_file($link)) {
			return $link;
		} else if ($make) {
			return touch($link)? $link : false;
		} else {
			return false;
		}
	}

	/**
	 * Return files list in directory.
	 *
	 * @param  string  $path  dir path
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _scandir($path) {
		return isset($this->dirsCache[$path])
			? $this->dirsCache[$path]
			: $this->cacheDir($path);
	}

	/**
	 * Open file and return file pointer
	 *
	 * @param  string  $path  file path
	 * @param  bool    $write open file for writing
	 * @return resource|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _fopen($path, $mode='rb') {
		if ($local = $this->readlink($path)) {
			return @fopen($local, $mode);
		}
		return false;
	}

	/**
	 * Close opened file
	 *
	 * @param  resource  $fp  file pointer
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _fclose($fp, $path='') {
		@fclose($fp);
	}

	/********************  file/dir manipulations *************************/

	/**
	 * Create dir and return created dir path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new directory name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _mkdir($path, $name) {
		$res = $this->make($path, $name, 'directory') ? $this->_joinPath($path, $name) : false;
		if ($res) {
			$this->updateDirTimestamp($path, time());
		}
		return $res;
	}

	/**
	 * Create file and return it's path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _mkfile($path, $name) {
		$mime = $this->mimetype($name, true);
		$res = $this->make($path, $name, ($mime === 'unknown')? 'text/plain' : $mime) ? $this->_joinPath($path, $name) : false;
		if ($res) {
			$this->updateDirTimestamp($path, time());
		}
		return $res;
	}

	/**
	 * Create symlink. FTP driver does not support symlinks.
	 *
	 * @param  string  $target  link target
	 * @param  string  $path    symlink path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _symlink($target, $path, $name) {
		return false;
	}

	/**
	 * Copy file into another file
	 *
	 * @param  string  $source     source file path
	 * @param  string  $targetDir  target directory path
	 * @param  string  $name       new file name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _copy($source, $targetDir, $name) {
		$this->clearcache();
		$id = $this->_joinPath($targetDir, $name);
		$gid = 0;
		$umask = $this->getUmask($targetDir, $gid);
		$perm = $this->getDefaultPerm($umask);
		$time = time();
		$sql = $id > 0
			? sprintf('REPLACE INTO %s (`file_id`, `parent_id`, `name`, `size`, `ctime`, `mtime`, `mime`, `width`, `height`, `gids`, `uid`, `gid`, `perm`, `umask`, `mime_filter`)
			                    (SELECT  %d,        %d,         `name`, `size`, `ctime`, `mtime`, `mime`, `width`, `height`, `gids`, `uid`,  %d,   "%s",   "%s"   , `mime_filter`  FROM %s WHERE file_id=%d)',
			                $this->tbf, (int)$id, (int)$this->_dirname($id),                                                                $gid,  $perm,  $umask, $this->tbf, (int)$source)
			: sprintf('INSERT INTO %s (            `parent_id`, `name`, `size`, `ctime`, `mtime`, `mime`, `width`, `height`, `gids`, `uid`, `gid`, `perm`, `umask`, `mime_filter`)
			                    (SELECT             %d,          %s,    `size`,  %d,      %d,     `mime`, `width`, `height`, `gids`, `uid`,  %d,   "%s",   "%s"    ,`mime_filter`  FROM %s WHERE file_id=%d)',
					     $this->tbf, (int)$targetDir, $this->db->quoteString($name), $time, $time,                                          $gid,  $perm,  $umask, $this->tbf, (int)$source);
		if ($this->query($sql)) {
			if ($id < 1) $id = $this->db->getInsertId();
			if ($local = $this->readlink($id, true)) {
				if ($target = @fopen($local, 'wb')) {
					$fp = $this->_fopen($source);
					while (!feof($fp)) {
						fwrite($target, fread($fp, 8192));
					}
					fclose($target);
					$this->_fclose($fp);
					$this->updateDirTimestamp($targetDir, $time);
					return $id;
				}
			}
		}
	}

	/**
	 * Move file into another parent dir.
	 * Return new file path or false.
	 *
	 * @param  string  $source  source file path
	 * @param  string  $target  target dir path
	 * @param  string  $name    file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _move($source, $targetDir, $name) {
		$gid = 0;
		$umask = $this->getUmask($targetDir, $gid);
		$stat = $this->stat($source);
		if (! isset($stat['uid']) || $stat['uid'] != $this->x_uid) {
			$perm = 'perm';
			$sql = 'UPDATE %s SET `parent_id`=%d, `name`=%s, `perm`=`%s`, `umask`="%s", `gid`=%d WHERE `file_id`=%d LIMIT 1';
		} else {
			$perm = $this->getDefaultPerm($umask);
			$sql = 'UPDATE %s SET `parent_id`=%d, `name`=%s, `perm`="%s", `umask`="%s", `gid`=%d WHERE `file_id`=%d LIMIT 1';
		}
		$sql = sprintf($sql, $this->tbf, $targetDir, $this->db->quoteString($name), $perm, $umask, $gid, $source);
		if  ($this->query($sql) && $this->db->getAffectedRows() > 0) {
			unset($this->cache[(int)$source]);
			$this->updateDirTimestamp($targetDir, $stat['ts']);
			return $source;
		} else {
			return false;
		}
	}

	/**
	 * Remove file
	 *
	 * @param  string  $path  file path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _unlink($path) {
		$stat = $this->stat($path);
		if ($this->query(sprintf('DELETE FROM %s WHERE `file_id`=%d AND `mime`!="directory" LIMIT 1', $this->tbf, $path)) && $this->db->getAffectedRows()) {
			$stat['phash'] && $this->updateDirTimestamp($this->decode($stat['phash']), time());
			$is_localalias = ! empty($stat['_localalias']);
			if (! $is_localalias) {
				$file = $this->readlink($path);
				@ unlink($file);
			}
			if ($is_localalias) {
				$file = $this->options['filePath'] . md5($stat['alias']);
			}
			$tmbs = glob($file.'_*.tmb');
			if ($tmbs) {
				foreach ($tmbs as $tmb) {
					@ unlink($tmb);
				}
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Remove dir
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _rmdir($path) {
		$stat = $this->stat($path);
		$res = ($this->query(sprintf('DELETE FROM %s WHERE `file_id`=%d AND `mime`="directory" LIMIT 1', $this->tbf, $path)) && $this->db->getAffectedRows());
		if ($res) {
			$stat['phash'] && $this->updateDirTimestamp($this->decode($stat['phash']), time());
		}
		return $res;
	}

	/**
	 * Create new file and write into it from file pointer.
	 * Return new file path or false on error.
	 *
	 * @param  resource  $fp   file pointer
	 * @param  string    $dir  target dir path
	 * @param  string    $name file name
	 * @param  array     $stat file stat (required by some virtual fs)
	 * @return bool|string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _save($fp, $dir, $name, $stat) {
		
		if ($name === '') return false;
		
		$this->clearcache();

		$id = $this->_joinPath($dir, $name);

		if ($id > 0) $this->rmTmb($stat);
		rewind($fp);
		$fstat = fstat($fp);
		if (is_array($fstat) && isset($fstat['size'])) {
			$size = $fstat['size'];
		} else {
			$size = 0;
		}
		$time = time();
		$gid = 0;
		$uid = (int)$this->x_uid;
		$umask = $this->getUmask($dir, $gid);
		$perm = $this->getDefaultPerm($umask);
		$gids = join(',', $this->getGroupsByUid($uid));
		$cut = ($_SERVER['REQUEST_METHOD'] == 'POST')? !empty($_POST['cut']) : !empty($_GET['cut']);
		$local_path = (! $cut && is_array($stat) && !empty($stat['_localpath']))? $stat['_localpath'] : '';
		
		$mime = $stat['mime'];
		$w = $stat['width'];
		$h = $stat['height'];
		
		$sql = $id > 0
			? 'REPLACE INTO %s (`file_id`, `parent_id`, `name`, `size`, `ctime`, `mtime`, `perm`, `umask`, `uid`, `gid`, `mime`, `width`, `height`, `gids`, `local_path`) VALUES ('.$id.', %d, %s, %d, %d, %d, "%s", "%s", %d, %d, "%s", %d, %d, "%s", %s)'
			: 'INSERT INTO %s (`parent_id`, `name`, `size`, `ctime`, `mtime`, `perm`, `umask`, `uid`, `gid`, `mime`, `width`, `height`, `gids`, `local_path`) VALUES (%d, %s, %d, %d, %d, "%s", "%s", %d, %d, "%s", %d, %d, "%s", %s)';
		$sql = sprintf($sql, $this->tbf, (int)$dir, $this->db->quoteString($name), $size, $time, $time, $perm, $umask, $uid, $gid, $mime, $w, $h, $gids, $this->db->quoteString($local_path));

		if ($this->query($sql)) {
			if ($id < 1) $id = $this->db->getInsertId();
			if ($local_path) return $id;
			if ($local = $this->readlink($id, true)) {
				if ($target = @fopen($local, 'wb')) {
					while (!feof($fp)) {
						fwrite($target, fread($fp, 8192));
					}
					fclose($target);
					if ($this->mimeDetect === 'internal' && strpos($mime, 'image') === 0 && ! getimagesize($local)) {
						$this->_unlink($id);
						return $this->setError(elFinder::ERROR_UPLOAD_FILE_MIME);
					}
					if ($size === 0) {
						clearstatcache($local);
						$size = filesize($local);
						$sql = 'UPDATE %s SET size=%d WHERE file_id=%d LIMIT 1';
						$sql = sprintf($sql, $this->tbf, $size, $id);
						$this->query($sql);
					}
					$this->updateDirTimestamp($dir, $time, true);
					return $id;
				} else {
					$this->_unlink($id);
				}
			}
		}

		return false;
	}

	/**
	 * Get file contents
	 *
	 * @param  string  $path  file path
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _getContents($path) {
		if ($local = $this->readlink($path)) {
			return file_get_contents($local);
		}
		return false;
	}

	/**
	 * Write a string to a file
	 *
	 * @param  string  $path     file path
	 * @param  string  $content  new file content
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _filePutContents($path, $content) {
		if ($local = $this->readlink($path)) {
			if (file_put_contents($local, $content) !== false) {
				clearstatcache();
				$stat = $this->stat($path);
				$mime = $this->mimetype($local, $stat['name']);
				$time = time();
				unset($this->cache[(int)$path]);
				$this->updateDirTimestamp($this->_dirname($path), $time, true);
				return $this->query(sprintf('UPDATE %s SET `size`=%d, `mtime`=%d, `mime`="%s" WHERE `file_id` = "%d" LIMIT 1', $this->tbf, strlen($content), $time, $mime, $path));
			}
		}
		return false;
	}

	/**
	* Detect available archivers
	*
	* @return void
	**/
	protected function _checkArchivers() {
		$this->archivers = $this->getArchivers();
		return;
	}

	/**
	 * chmod implementation
	 *
	 * @return bool
	 **/
	protected function _chmod($path, $mode) {
		return false;
	}

	/**
	 * Recursive symlinks search
	 *
	 * @param  string  $path  file/dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _findSymlinks($realpath) {
		if (is_link($realpath)) {
			return true;
		}
		if (is_dir($realpath)) {
			foreach (scandir($realpath) as $name) {
				if ($name != '.' && $name != '..') {
					$p = $realpath.DIRECTORY_SEPARATOR.$name;
					if (is_link($p) || !$this->nameAccepted($name)) {
						$this->setError(elFinder::ERROR_SAVE, $name);
						return true;
					}
					if (is_dir($p) && $this->_findSymlinks($p)) {
						return true;
					} elseif (is_file($p)) {
						$this->archiveSize += filesize($p);
					}
				}
			}
		} else {
			$this->archiveSize += filesize($realpath);
		}
		return false;
	}

	/**
	 * Extract files from archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return true
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	protected function _extract($id, $arc) {
		
		$localpath = $this->readlink($id);
		$stat = $this->stat($id);
		
		$localdir = XOOPS_TRUST_PATH.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.md5(basename($localpath).mt_rand());
		$archive = $localdir.DIRECTORY_SEPARATOR.basename($localpath);
		if (!@mkdir($localdir)) {
			return false;
		}
		
		// insurance unexpected shutdown
		register_shutdown_function(array($this, 'rmdirRecursive'), realpath($localdir));
		
		chmod($localdir, 0777);
		
		// copy in quarantine
		if (!copy($localpath, $archive)) {
			$this->rmdirRecursive($localdir);
			return false;
		}
		
		// extract in quarantine
		$this->unpackArchive($archive, $arc);
		
		// get files list
		$ls = array();
		foreach (scandir($localdir) as $i => $name) {
			if ($name != '.' && $name != '..') {
				$ls[] = $name;
			}
		}
			
		// no files - extract error ?
		if (empty($ls)) {
			return false;
		}
		
		// find symlinks
		$this->archiveSize = 0;
		if ($this->_findSymlinks($localdir)) {
			// remove arc copy
			$this->rmdirRecursive($localdir);
			return $this->setError(array_merge($this->error, array(elFinder::ERROR_ARC_SYMLINKS)));
		}
		
		// check max files size
		if ($this->options['maxArcFilesSize'] > 0 && $this->options['maxArcFilesSize'] < $this->archiveSize) {
			$this->rmdirRecursive($localdir);
			return $this->setError(elFinder::ERROR_ARC_MAXSIZE);
		}
		
		$dir = $this->decode($stat['phash']);

		$extractTo = $this->extractToNewdir; // 'auto', ture or false
		
		$src = $localdir.DIRECTORY_SEPARATOR.$ls[0];
		$_ok = false;
		if (($extractTo === 'auto' || !$extractTo) && count($ls) === 1 && is_file($src)) {
			$dir = $this->localFileSave($src, $dir, true);
			$_ok = $dir? true : false;
		} else if ($extractTo === 'auto' || $extractTo) {
			// create unique name for directory
			$name = $stat['name'];
			if (preg_match('/\.((tar\.(gz|bz|bz2|z|lzo))|cpio\.gz|ps\.gz|xcf\.(gz|bz2)|[a-z0-9]{1,4})$/i', $name, $m)) {
				$name = substr($name, 0,  strlen($name)-strlen($m[0]));
			}
	
			if ($this->_joinPath($dir, $name) > -1) {
				$name = $this->uniqueName($dir, $name, '-', false);
			}
			$dir = $this->_mkdir($dir, $name);
			
			if ($dir < 1) {
				$this->rmdirRecursive($localdir);
				return false;
			}
			
			foreach (scandir($localdir) as $name) {
				if ($name != '.' && $name != '..') {
					$res = $this->localFileSave($localdir.DIRECTORY_SEPARATOR.$name, $dir, true);
					if (!$_ok && $res > 0) $_ok = true;
				}
			}
		} else {
			$result = array();
			foreach($ls as $name) {
				$src = $localdir.DIRECTORY_SEPARATOR.$name;
				$add = $this->localFileSave($src, $dir, true);
				if ($add > 0) {
					$result[] = $add;
				}
			}
			if ($result) {
				$_ok = true;
				$dir = $result;
			} else {
				$dir = 0;
			}
		}
		
		$this->rmdirRecursive($localdir);
		
		if ($_ok) {
			return $dir;
		} else {
			$dir && $this->_rmdir($dir);
		}

		// no files - extract error ?
		return false;
	}

	/**
	* Create archive and return its path
	*
	* @param  string  $dir    target dir
	* @param  array   $files  files names list
	* @param  string  $name   archive name
	* @param  array   $arc    archiver options
	* @return string|bool
	* @author Dmitry (dio) Levashov,
	* @author Alexey Sukhotin
	**/
	protected function _archive($dir, $files, $name, $arc) {
		if (! chdir($this->options['tempPath'])) return false;

		$mkdir = md5(microtime() . join('_', $files));
		$_tmpfiles = $_files = $this->copyToLocalTemp($mkdir, $files, $dir);
		
		$_dir = rtrim($this->options['tempPath'].DIRECTORY_SEPARATOR.$mkdir, DIRECTORY_SEPARATOR);
		
		$this->makeArchive($_dir, $_tmpfiles, $name, $arc);
		
		$ret = $this->localFileSave($_dir.DIRECTORY_SEPARATOR.$name, $dir);

		if ($mkdir) {
			$this->rmdirRecursive($_dir);
		} else {
			foreach($_tmpfiles as $file) {
				@ unlink($_dir.DIRECTORY_SEPARATOR.$file);
			}
		}
		
		return $ret;
	}

} // END class

?>
