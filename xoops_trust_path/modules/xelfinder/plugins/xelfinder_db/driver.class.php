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
		$this->options['tempPath'] = XOOPS_ROOT_PATH . '/modules/'._MD_ELFINDER_MYDIRNAME.'/cache';
		$this->options['tmbPath'] = $this->options['tempPath'].'/tmb/';
		$this->options['tmbURL'] = $this->options['tempPath'].'/tmb/';
		$this->options['default_umask'] = '8bb';
	}

	public function savePerm($target, $perm, $umask, $gids, $mime_filter) {
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
		
		$mime_filter = mysql_real_escape_string($mime_filter);
		
		if ($umask) {
			$sql = sprintf('UPDATE %s SET `perm`="%s", `gids`="%s", `umask`="%s", `mime_filter`="%s" WHERE `file_id` = "%d" LIMIT 1', $this->tbf, $perm, $gids, $umask, $mime_filter, $path);
		} else {
			$sql = sprintf('UPDATE %s SET `perm`="%s", `gids`="%s", `mime_filter`="%s" WHERE `file_id` = "%d" LIMIT 1', $this->tbf, $perm, $gids, $mime_filter, $path);
		}
		if ($this->query($sql) && $this->db->getAffectedRows() > 0) {
			unset($this->cache[$path]);
			return $stat = $this->stat($path);
		} else {
			$this->_debug($sql);
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
		$xoopsMenber =& xoops_gethandler('member');
		$list = $xoopsMenber->getGroupList();
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
				$module_handler =& xoops_gethandler('module');
				$user_handler =& xoops_gethandler('user');
				$user =& $user_handler->get($stat['uid']);
				if (is_object($user)) {
					$uname = $user->uname('s');
				}
			}
		}
		if ($uname === '') {
			$config_handler =& xoops_gethandler('config');
			$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
			$uname = $this->strToUTF8($xoopsConfig['anonymous']);
		}
		
		return array('groups' => $groups, 'uname' => $uname);
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

		mysql_set_charset('utf8');

		$this->mydirname = $this->options['mydirname'];

		$this->tbf = $this->db->prefix($this->mydirname) . '_file';

		$module_handler =& xoops_gethandler('module');
		$XoopsModule =& $module_handler->getByDirname($this->mydirname);
		$module = $XoopsModule->getInfo();
		$this->x_mid = $XoopsModule->getVar('mid');

		global $xoopsUser;
		if (is_object($xoopsUser)) {
			$this->x_uid = $xoopsUser->getVar('uid');
			$this->x_uname = $this->strToUTF8($xoopsUser->uname('n'));
			$this->x_groups = $xoopsUser->getGroups();
			$this->x_isAdmin = (!empty($_GET['admin']) && $xoopsUser->isAdmin($this->x_mid));
		} else {
			$this->x_uid = 0;
			$this->x_groups = array(XOOPS_GROUP_ANONYMOUS);
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
			$this->dbError = $this->db->error;
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
			$perm = $this->getDefaultPerm($mime, $umask);
		}

		$sql = 'INSERT INTO %s (`parent_id`, `name`, `ctime`, `mtime`, `perm`, `umask` , `uid`, `gid`, `home_of`, `mime`) VALUES '
		                    . '( %d,         "%s",    %d,      %d,     "%s",   "%s",     "%d",  "%d",   %s,     "%s")';
		$sql = sprintf($sql, $this->tbf, intval($path), mysql_escape_string($name), $time, $time, $perm, $umask, $this->x_uid, $gid, $home_of, $mime);
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

	/**
	 * Return temporary file path for required file
	 *
	 * @param  string  $path   file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function tmpname($path) {
		return $this->tmpPath.DIRECTORY_SEPARATOR.md5($path);
	}

	/**
	* Resize image
	*
	* @param  string   $hash    image file
	* @param  int      $width   new width
	* @param  int      $height  new height
	* @param  bool     $crop    crop image
	* @return array|false
	* @author Dmitry (dio) Levashov
	* @author Alexey Sukhotin
	* @author Naoki Sawada
	**/
	public function resize($hash, $width, $height, $x, $y, $mode = 'resize', $bg = '', $degree = 0) {
		if ($this->commandDisabled('resize')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}

		if (($file = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}

		if (!$file['write'] || !$file['read']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}

		$path = $this->decode($hash);

		if (!$this->canResize($path, $file)) {
			return $this->setError(elFinder::ERROR_UNSUPPORT_TYPE);
		}

		$local = $this->readlink($path);
		if (! $local) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}

		switch($mode) {

			case 'propresize':
				$result = $this->imgResize($local, $width, $height, true, true);
				break;

			case 'crop':
				$result = $this->imgCrop($local, $width, $height, $x, $y);
				break;

			case 'fitsquare':
				$result = $this->imgSquareFit($local, $width, $height, 'center', 'middle', $bg ? $bg : $this->options['tmbBgColor']);
				break;

			case 'rotate':
				$result = $this->imgRotate($local, $degree, ($bg ? $bg : $this->options['tmbBgColor']));
				break;
			
			default:
				$result = $this->imgResize($local, $width, $height, false, true);
				break;
		}
		
		if ($result) {
			clearstatcache();
			$size = filesize($local);
			list($width, $height) = getimagesize($local);
			
			$this->rmTmb($path);
			$this->clearcache();
			$this->createTmb($path, $file);

			$sql = 'UPDATE %s SET mtime=%d, width=%d, height=%d, size=%d WHERE file_id=%d LIMIT 1';
			$sql = sprintf($sql, $this->tbf, time(), $width, $height, $size, $path);
			$this->query($sql);

			return $this->stat($path);
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

	protected function getDefaultPerm($mime, $umask) {
		if ($mime === 'directory') {
			$base = 0xfff;
		} else {
			$base = 0xfff;
		}
		return strval(dechex($base - intval($umask, 16)));
	}

	protected function getGroupsByUid($uid) {
		static $groups = array();

		if (isset($groups[$uid])) return $groups[$uid];

		if ($uid) {
			$user_handler =& xoops_gethandler('user');
			$user =& $user_handler->get( $uid );
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
		$own = intval($perm[0], 16);
		$grp = intval($perm[1], 16);
		$gus = intval($perm[2], 16);

		if ($isOwner) $dat['isowner'] = 1;
		$dat['hidden'] = !(($isOwner && (8 & $own) !== 8) || ($inGroup && (8 & $grp) !== 8) || (8 & $gus) !== 8);
		$dat['read']   =  (($isOwner && (4 & $own) === 4) || ($inGroup && (4 & $grp) === 4) || (4 & $gus) === 4);
		$dat['write']  =  (($isOwner && (2 & $own) === 2) || ($inGroup && (2 & $grp) === 2) || (2 & $gus) === 2);
		$dat['locked'] = !(($isOwner && (1 & $own) === 1) || ($inGroup && (1 & $grp) === 1) || (1 & $gus) === 1);
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
					$xoopsMenber =& xoops_gethandler('member');
					$groups = $xoopsMenber->getGroupList(new Criteria('group_type' , 'Anonymous', '!='));
					$sql = 'SELECT gid FROM '.$this->tbf.' WHERE home_of < 0';
					if (($res = $this->query($sql)) && $this->db->getRowsNum($res)) {
						while ($row = $this->db->fetchRow($res)) {
							unset($groups[$row[0]]);
						}
					}
					if ($groups) {
						foreach($groups as $gid => $gname) {
							$gid *= -1;
							$this->makeUmask = $this->options['group_dir_umask'];
							$this->makePerm = $this->options['group_dir_perm'];
							$this->make($group_parent, $gname, 'directory', $gid);
						}
					}
				}
			}

			if ($this->options['use_guest_dir']) {
				$sql = 'SELECT file_id FROM '.$this->tbf.' WHERE home_of = 0 LIMIT 1';
				if (($res = $this->query($sql)) && $this->db->getRowsNum($res) < 1) {
					$config_handler =& xoops_gethandler('config');
					$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
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
		//$localpath = mb_convert_encoding($localpath, 'UTF-8', 'AUTO');
		$name = basename($localpath);
		$this->_debug($localpath);
		if ($this->nameAccepted($name)) {
			$width = $height = 0;
			if (is_dir($localpath)) {
				$path = $this->_mkdir($dir, $name);
				if ($path > 0) {
					$_ok = false;
					foreach (scandir($localpath) as $c_name) {
						if ($c_name != '.' && $c_name != '..') {
							$_res = $this->localFileSave($localpath.DIRECTORY_SEPARATOR.$c_name, $path, $check_mime_accept);
							if (!$_ok && $_res > 0) $_ok = true;
						}
					}
					if (! $_ok) {
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
						if ($id = $this->_save($fp, $dir, $name, $mime, $width, $height)) {
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
		foreach (scandir($dir) as $file) {
			if ($file != '.' && $file != '..') {
				$file = $dir.DIRECTORY_SEPARATOR.$file;
				if(is_dir($file)) {
					$this->rrmdir($file);
				} else {
					@ unlink($file);
				}
			}
		}
		return rmdir($dir);
	}
	
	protected function makeStat($stat) {
		if ($stat['parent_id']) {
			$stat['phash'] = $this->encode($stat['parent_id']);
		}
		if ($stat['mime'] == 'directory') {
			unset($stat['width']);
			unset($stat['height']);
		} else {
			unset($stat['dirs']);
		}
		$this->setAuthByPerm($stat);
		$stat['url'] = $this->options['URL'].$stat['file_id'].'/'.rawurlencode($stat['name']); // Use pathinfo "index.php/[id]/[name]
		
		unset($stat['file_id'], $stat['parent_id'], $stat['gid'], $stat['home_of']);
		if (empty($stat['isowner'])) unset($stat['perm'], $stat['uid'], $stat['gids']);
		if (empty($stat['isowner']) || $stat['mime'] !== 'directory') unset($stat['umask']);
		if ($stat['mime'] !== 'directory') unset($stat['filter']);
		
		return $stat;
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
	protected function attr($path, $name, $val=false) {
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
				f.perm, f.umask, f.uid, f.gid, f.home_of, f.width, f.height, f.gids, f.mime_filter as filter,
				IF(ch.file_id, 1, 0) AS dirs
				FROM '.$this->tbf.' AS f
				LEFT JOIN '.$this->tbf.' AS ch ON ch.parent_id=f.file_id AND ch.mime="directory"
				WHERE f.parent_id="'.$path.'"
				GROUP BY f.file_id';

		$res = $this->query($sql);
		if ($res) {
			while ($row = $this->db->fetchArray($res)) {
				$id = $row['file_id'];
				$row = $this->makeStat($row);
				if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
					$this->dirsCache[$path][] = $id;
				}
			}
		}
		
		$current_stat = $this->stat($path);
		if (! empty($current_stat['filter'])) {
			$filter = mysql_real_escape_string($current_stat['filter']);
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
			f.perm, f.umask, f.uid, f.gid, f.home_of, f.width, f.height, f.gids, f.mime_filter as filter
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
		
		if ($path != $this->root) {
			return parent::doSearch($path, $q, $mimes);
		} else {
			$result = array();

			$q = '%'.mysql_real_escape_string($q).'%';
			$sql = 'SELECT `file_id`, `mime`, `uid`, `gid`, `gids`, `perm`, `home_of` FROM '.$this->tbf.' WHERE `name` LIKE \''.$q.'\'';
			
			$res = $this->query($sql);
			if ($res) {
				while ($stat = $this->db->fetchArray($res)) {
					if ($stat['mime'] === 'directory' || !$this->mimeAccepted($stat['mime'], $mimes)) {
						continue;
					}
					$this->setAuthByPerm($stat);
					if (empty($stat['hidden'])) {
						$result[] = $this->stat($stat['file_id']);
					}
				}
			}
			
			return $result;
		}
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
		return ($stat = $this->stat($path)) ? ($stat['phash'] ? $this->decode($stat['phash']) : $this->root) : false;
	}

	/**
	 * Return file name
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _basename($path) {
		return ($stat = $this->stat($path)) ? $stat['name'] : false;
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
		$sql = 'SELECT `file_id` FROM '.$this->tbf.' WHERE parent_id="'.$dir.'" AND name="'.mysql_escape_string($name).'"';
		if (($res = $this->query($sql)) && $this->db->getRowsNum($res) > 0) {
			$r = $this->db->fetchArray($res);
			//$this->updateCache($r['file_id'], $this->_stat($r['file_id']));
			return $r['file_id'];
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
		return $path;
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
				f.perm, f.umask, f.uid, f.gid, f.home_of, f.width, f.height, f.gids, f.mime_filter as filter,
				IF(ch.file_id, 1, 0) AS dirs
				FROM '.$this->tbf.' AS f
				LEFT JOIN '.$this->tbf.' AS p ON p.file_id=f.parent_id
				LEFT JOIN '.$this->tbf.' AS ch ON ch.parent_id=f.file_id AND ch.mime="directory"
				WHERE f.file_id="'.$path.'"
				GROUP BY f.file_id';

		$res = $this->query($sql);

		if ($res && $stat = $this->db->fetchArray($res)) {
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
		$link = $this->options['filePath'] . $path;
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
		return $this->make($path, $name, 'directory') ? $this->_joinPath($path, $name) : false;
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
		return $this->make($path, $name, 'text/plain') ? $this->_joinPath($path, $name) : false;
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
		$perm = $this->getDefaultPerm($mime, $umask);
		$time = time();
		$sql = $id > 0
			? sprintf('REPLACE INTO %s (`file_id`, `parent_id`, `name`, `size`, `ctime`, `mtime`, `mime`, `width`, `height`, `gids`, `uid`, `gid`, `perm`, `umask`, `mime_filter`)
			                    (SELECT  %d,        %d,         `name`, `size`, `ctime`, `mtime`, `mime`, `width`, `height`, `gids`, `uid`,  %d,   "%s",   "%s"   , `mime_filter`  FROM %s WHERE file_id=%d)',
			                $this->tbf, (int)$id, (int)$this->_dirname($id),                                                                $gid,  $perm,  $umask, $this->tbf, (int)$source)
			: sprintf('INSERT INTO %s (            `parent_id`, `name`, `size`, `ctime`, `mtime`, `mime`, `width`, `height`, `gids`, `uid`, `gid`, `perm`, `umask`, `mime_filter`)
			                    (SELECT             %d,         "%s",   `size`,  %d,      %d,     `mime`, `width`, `height`, `gids`, `uid`,  %d,   "%s",   "%s"    ,`mime_filter`  FROM %s WHERE file_id=%d)',
					     $this->tbf, (int)$targetDir, mysql_escape_string($name),$time,   $time,                                            $gid,  $perm,  $umask, $this->tbf, (int)$source);
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
			$sql = 'UPDATE %s SET `parent_id`=%d, `name`="%s", `perm`=`%s`, `umask`="%s", `gid`=%d WHERE `file_id`=%d LIMIT 1';
		} else {
			$perm = $this->getDefaultPerm($mime, $umask);
			$sql = 'UPDATE %s SET `parent_id`=%d, `name`="%s", `perm`="%s", `umask`="%s", `gid`=%d WHERE `file_id`=%d LIMIT 1';
		}
		$sql = sprintf($sql, $this->tbf, $targetDir, mysql_escape_string($name), $perm, $umask, $gid, $source);
		if  ($this->query($sql) && $this->db->getAffectedRows() > 0) {
			unset($this->cache[$source]);
			return true;
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
		$file = $this->readlink($path);
		@ unlink($file);
		foreach (glob($file.'_*.tmb') as $tmb) {
			@ unlink($tmb);
		}
		return ($this->query(sprintf('DELETE FROM %s WHERE `file_id`=%d AND `mime`!="directory" LIMIT 1', $this->tbf, $path)) && $this->db->getAffectedRows());
	}

	/**
	 * Remove dir
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _rmdir($path) {
		return ($this->query(sprintf('DELETE FROM %s WHERE `file_id`=%d AND `mime`="directory" LIMIT 1', $this->tbf, $path)) && $this->db->getAffectedRows());
	}

	/**
	 * Create new file and write into it from file pointer.
	 * Return new file path or false on error.
	 *
	 * @param  resource  $fp   file pointer
	 * @param  string    $dir  target dir path
	 * @param  string    $name file name
	 * @return bool|string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _save($fp, $dir, $name, $mime, $w, $h) {
		
		if ($name === '') return false;
		
		$this->clearcache();

		$id = $this->_joinPath($dir, $name);

		if ($id > 0) $this->rmTmb($id);
		rewind($fp);
		$stat = fstat($fp);
		$size = $stat['size'];
		$time = time();
		$gid = 0;
		$uid = (int)$this->x_uid;
		$umask = $this->getUmask($dir, $gid);
		$perm = $this->getDefaultPerm($mime, $umask);
		$gigs = join(',', $this->getGroupsByUid($uid));
		
		if ($mime === 'application/octet-stream' && substr($name, -3) === '.7z' && fread($fp, 2) === '7z') {
			// @todo need check file contents (it's realy 7z?)
			$mime = 'application/x-7z-compressed';
		}
		rewind($fp);
		
		$sql = $id > 0
			? 'REPLACE INTO %s (`file_id`, `parent_id`, `name`, `size`, `ctime`, `mtime`, `perm`, `umask`, `uid`, `gid`, `mime`, `width`, `height`, `gids`) VALUES ('.$id.', %d, "%s", %d, %d, %d, "%s", "%s", %d, %d, "%s", %d, %d, "%s")'
			: 'INSERT INTO %s (`parent_id`, `name`, `size`, `ctime`, `mtime`, `perm`, `umask`, `uid`, `gid`, `mime`, `width`, `height`, `gids`) VALUES (%d, "%s", %d, %d, %d, "%s", "%s", %d, %d, "%s", %d, %d, "%s")';
		$sql = sprintf($sql, $this->tbf, (int)$dir, mysql_escape_string($name), $size, $time, $time, $perm, $umask, $uid, $gid, $mime, $w, $h, $gids);

		if ($this->query($sql)) {
			if ($id < 1) $id = $this->db->getInsertId();
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
					if ($this->options['autoResize'] && strpos($mime, 'image') === 0 && max($w, $h) > $this->options['autoResize']) {
						if ($this->imgResize($local, $this->options['autoResize'], $this->options['autoResize'], true, true)) {
							clearstatcache();
							$size = filesize($local);
							list($width, $height) = getimagesize($local);
							$sql = 'UPDATE %s SET width=%d, height=%d, size=%d WHERE file_id=%d LIMIT 1';
							$sql = sprintf($sql, $this->tbf, $width, $height, $size, $id);
							$this->query($sql);
						}
					}
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
			if (file_put_contents($local, $content)) {
				return $this->query(sprintf('UPDATE %s SET `size`=%d, `mtime`=%d WHERE `file_id` = "%d" LIMIT 1', $this->tbf, strlen($content), time(), $path));
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
		if (!function_exists('exec')) {
			$this->options['archivers'] = $this->options['archive'] = array();
			return;
		}
		$arcs = array(
			'create'  => array(),
			'extract' => array()
			);
		
		//exec('tar --version', $o, $ctar);
		$this->procExec('tar --version', $o, $ctar);

		if ($ctar == 0) {
			$arcs['create']['application/x-tar']  = array('cmd' => 'tar', 'argc' => '-cf', 'ext' => 'tar');
			$arcs['extract']['application/x-tar'] = array('cmd' => 'tar', 'argc' => '-xf', 'ext' => 'tar');
			//$test = exec('gzip --version', $o, $c);
			unset($o);
			$test = $this->procExec('gzip --version', $o, $c);

			if ($c == 0) {
				$arcs['create']['application/x-gzip']  = array('cmd' => 'tar', 'argc' => '-czf', 'ext' => 'tgz');
				$arcs['extract']['application/x-gzip'] = array('cmd' => 'tar', 'argc' => '-xzf', 'ext' => 'tgz');
			}
			unset($o);
			//$test = exec('bzip2 --version', $o, $c);
			$test = $this->procExec('bzip2 --version', $o, $c);
			if ($c == 0) {
				$arcs['create']['application/x-bzip2']  = array('cmd' => 'tar', 'argc' => '-cjf', 'ext' => 'tbz');
				$arcs['extract']['application/x-bzip2'] = array('cmd' => 'tar', 'argc' => '-xjf', 'ext' => 'tbz');
			}
		}
		unset($o);
		//exec('zip --version', $o, $c);
		$this->procExec('zip -v', $o, $c);
		if ($c == 0) {
			$arcs['create']['application/zip']  = array('cmd' => 'zip', 'argc' => '-r9', 'ext' => 'zip');
		}
		unset($o);
		$this->procExec('unzip --help', $o, $c);
		if ($c == 0) {
			$arcs['extract']['application/zip'] = array('cmd' => ($this->options['unzip_lang_value']? 'LANG='.$this->options['unzip_lang_value'].' ' : '').'unzip', 'argc' => '',  'ext' => 'zip');
		}
		unset($o);
		//exec('rar --version', $o, $c);
		$this->procExec('rar --version', $o, $c);
		if ($c == 0 || $c == 7) {
			$arcs['create']['application/x-rar']  = array('cmd' => 'rar', 'argc' => 'a -inul', 'ext' => 'rar');
			$arcs['extract']['application/x-rar'] = array('cmd' => 'rar', 'argc' => 'x -y',    'ext' => 'rar');
		} else {
			unset($o);
			//$test = exec('unrar', $o, $c);
			$test = $this->procExec('unrar', $o, $c);
			if ($c==0 || $c == 7) {
				$arcs['extract']['application/x-rar'] = array('cmd' => 'unrar', 'argc' => 'x -y', 'ext' => 'rar');
			}
		}
		unset($o);
		//exec('7za --help', $o, $c);
		$this->procExec('7za --help', $o, $c);
		if ($c == 0) {
			$arcs['create']['application/x-7z-compressed']  = array('cmd' => '7za', 'argc' => 'a', 'ext' => '7z');
			$arcs['extract']['application/x-7z-compressed'] = array('cmd' => '7za', 'argc' => 'e -y', 'ext' => '7z');
			
			if (empty($arcs['create']['application/x-gzip'])) {
				$arcs['create']['application/x-gzip'] = array('cmd' => '7za', 'argc' => 'a -tgzip', 'ext' => 'tar.gz');
			}
			if (empty($arcs['extract']['application/x-gzip'])) {
				$arcs['extract']['application/x-gzip'] = array('cmd' => '7za', 'argc' => 'e -tgzip -y', 'ext' => 'tar.gz');
			}
			if (empty($arcs['create']['application/x-bzip2'])) {
				$arcs['create']['application/x-bzip2'] = array('cmd' => '7za', 'argc' => 'a -tbzip2', 'ext' => 'tar.bz');
			}
			if (empty($arcs['extract']['application/x-bzip2'])) {
				$arcs['extract']['application/x-bzip2'] = array('cmd' => '7za', 'argc' => 'a -tbzip2 -y', 'ext' => 'tar.bz');
			}
			if (empty($arcs['create']['application/zip'])) {
				$arcs['create']['application/zip'] = array('cmd' => '7za', 'argc' => 'a -tzip -l', 'ext' => 'zip');
			}
			if (empty($arcs['extract']['application/zip'])) {
				$arcs['extract']['application/zip'] = array('cmd' => '7za', 'argc' => 'e -tzip -y', 'ext' => 'zip');
			}
			if (empty($arcs['create']['application/x-tar'])) {
				$arcs['create']['application/x-tar'] = array('cmd' => '7za', 'argc' => 'a -ttar -l', 'ext' => 'tar');
			}
			if (empty($arcs['extract']['application/x-tar'])) {
				$arcs['extract']['application/x-tar'] = array('cmd' => '7za', 'argc' => 'e -ttar -y', 'ext' => 'tar');
			}
		}
		
		$this->archivers = $arcs;
	}

	/**
	 * Unpack archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return void
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
	protected function _unpack($realpath, $arc) {
		$cwd = getcwd();
		$dir = dirname($realpath);
		chdir($dir);
		$cmd = $arc['cmd'].' '.$arc['argc'].' '.escapeshellarg(basename($realpath));
		$this->procExec($cmd, $o, $c);
		chdir($cwd);
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
					if (is_link($p)) {
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
		
		$localdir = XOOPS_TRUST_PATH.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.str_replace(' ', '_', microtime()).basename($localpath);
		$archive = $localdir.DIRECTORY_SEPARATOR.basename($localpath);
		if (!@mkdir($localdir)) {
			return false;
		}
		
		chmod($localdir, 0777);
		
		// copy in quarantine
		if (!copy($localpath, $archive)) {
			return false;
		}
		
		// extract in quarantine
		$this->_unpack($archive, $arc);
		@unlink($archive);
		
		// find symlinks
		$this->archiveSize = 0;
		if ($this->_findSymlinks($localdir)) {
			// remove arc copy
			$this->rrmdir($localdir);
			return $this->setError(elFinder::ERROR_ARC_SYMLINKS);
		}
		
		// check max files size
		if ($this->options['maxArcFilesSize'] > 0 && $this->options['maxArcFilesSize'] < $this->archiveSize) {
			$this->rrmdir($localdir);
			return $this->setError(elFinder::ERROR_ARC_MAXSIZE);
		}
		
		// get files list
		$ls = array();
		$dir = $this->decode($stat['phash']);

		// create unique name for directory
		$name = $stat['name'];
		if (preg_match('/\.((tar\.(gz|bz|bz2|z|lzo))|cpio\.gz|ps\.gz|xcf\.(gz|bz2)|[a-z0-9]{1,4})$/i', $name, $m)) {
			$name = substr($name, 0,  strlen($name)-strlen($m[0]));
		}

		if ($this->_joinPath($dir, $name) > -1) {
			$name = $this->uniqueName($dir, $name, '-', false);
		}
		$dir = $this->_mkdir($dir, $name);
		
		if ($dir < 1) return false;
		
		$_ok = false;
		foreach (scandir($localdir) as $name) {
			if ($name != '.' && $name != '..') {
				$res = $this->localFileSave($localdir.DIRECTORY_SEPARATOR.$name, $dir, true);
				if (!$_ok && $res > 0) $_ok = true;
			}
		}
		
		$this->rrmdir($localdir);
		
		if ($_ok) {
			return $dir;
		} else {
			$this->_rmdir($dir);
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
		$cwd = getcwd();
		
		if (! chdir($this->options['tempPath'])) return false;

		$mkdir = md5(microtime() . join('_', $files));
		$_tmpfiles = $_files = $this->copyToLocalTemp($mkdir, $files, $dir);
		
		$_dir = rtrim($this->options['tempPath'].DIRECTORY_SEPARATOR.$mkdir, DIRECTORY_SEPARATOR);
		
		$_files = array_map('escapeshellarg', $_files);
		chdir($_dir);
		
		$cmd = $arc['cmd'].' '.$arc['argc'].' '.escapeshellarg($name).' '.implode(' ', $_files).'';
		$this->procExec($cmd, $o, $c);

		chdir($cwd);
		
		$ret = $this->localFileSave($_dir.DIRECTORY_SEPARATOR.$name, $dir);

		if ($mkdir) {
			$this->rrmdir($_dir);
		} else {
			foreach($_tmpfiles as $file) {
				@ unlink($_dir.DIRECTORY_SEPARATOR.$file);
			}
		}
		
		return $ret;
	}

} // END class

?>