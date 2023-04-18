<?php

/**
 * Simple elFinder driver for MySQL.
 *
 * @author Dmitry (dio) Levashov
 **/
class elFinderVolumeXoopsMyalbum extends elFinderVolumeDriver {

	/**
	 * Driver id
	 * Must be started from letter and contains [a-z0-9]
	 * Used as part of volume id
	 *
	 * @var string
	 **/
	protected $driverId = 'xm';

	protected $mydirname = '';

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
	protected $tbc = '';

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

		$this->options['path'] = '_';
		$this->options['separator'] = '/';
		$this->options['mydirname'] = 'myalbum';
		$this->options['checkSubfolders'] = true;
		$this->options['tmbPath'] = XOOPS_MODULE_PATH . '/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb/';
		$this->options['tmbURL'] = _MD_XELFINDER_MODULE_URL . '/'._MD_ELFINDER_MYDIRNAME.'/cache/tmb/';

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

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		if (! is_object($this->db)) return false;

		xoops_elFinder::dbSetCharset('utf8');

		$this->mydirname = $this->options['mydirname'];

		$this->tbc = $this->db->prefix($this->mydirname) . '_cat';
		$this->tbf = $this->db->prefix($this->mydirname) . '_photos';

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
		return $debug;
	}

	/**
	 * Perform sql query and return result.
	 * Increase sqlCnt and save error if occured
	 *
	 * @param  string  $sql  query
	 * @return misc
	 * @author Dmitry (dio) Levashov
	 **/
	protected function query($sql) {
		$this->sqlCnt++;
		$res = $this->db->query($sql);
		if (!$res) {
			$this->dbError = $this->db->error();
		}
		return $res;
	}

	/*********************************************************************/
	/*                               FS API                              */
	/*********************************************************************/

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
		if ($stat && $stat['mime'] !== 'directory') $stat['_localpath'] = str_replace(XOOPS_ROOT_PATH, 'R', realpath($this->options['filePath'])  . DIRECTORY_SEPARATOR . str_replace($this->options['URL'], '', $stat['url']) );
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
		$this->dirsCache[$path] = [];

		if ($path === '_') {
			$cid = 0;
		} else {
			[$cid] = explode('_', substr($path, 1), 2);
		}

		$row_def = ['size' => 0, 'ts' => 0, 'mime' => '', 'dirs' => 0, 'read' => true, 'write' => false, 'locked' => true, 'hidden' => false, 'url'    => null];

		$_mtime = [];
		$_size = [];

		// cat (dirctory)
		$sql = 'SELECT c.pid, c.cid, c.title as name, max(f.`date`) as ts, s.pid as dirs ' .
				'FROM '.$this->tbc.' as c ' .
				'LEFT JOIN '.$this->tbc.' AS s ON c.cid=s.pid ' .
				'LEFT JOIN '.$this->tbf.' AS f ON c.cid=f.cid ' .
				'WHERE c.pid="'.$cid.'" ' .
				'GROUP BY c.cid';

		$res = $this->query($sql);
		if ($res) {
			while ($row = $this->db->fetchArray($res)) {
				$row = array_merge($row_def, $row);
				$row['mime'] = 'directory';
				$row['dirs'] = ($row['dirs'])? 1 : 0;
				if (! $row['pid']) {
					$row['phash'] = $this->encode('_');
				} else {
					$row['phash'] = $this->encode('_'.$row['pid'].'_');
				}
				$id = '_'.$row['cid'].'_';
				unset($row['cid'], $row['pid']);
				if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
					$this->dirsCache[$path][] = $id;
				}
			}
		}

		if ($cid) {
			// photos
			$sql = 'SELECT submitter as uid, lid, concat( lid, ".", ext ) AS id, res_x AS width, res_y AS height, `date` AS ts, concat( title, ".", ext ) AS name
					FROM '.$this->tbf.'
					WHERE cid="'.$cid.'" AND status>0';

			$res = $this->query($sql);
			if ($res) {
				while ($row = $this->db->fetchArray($res)) {
					$row = array_merge($row_def, $row);
					if (! $cid) {
						$row['phash'] = $this->encode('_');
					} else {
						$row['phash'] = $this->encode('_'.$cid.'_');
					}
					$id = '_'.$cid.'_'.$row['id'];
					$row['url'] = $this->options['URL'].$row['id'];
					$realpath = realpath($this->options['filePath'].$row['id']);
					if (is_file($realpath)) {
						$row['dim'] = $row['width'].'x'.$row['height'];
						$row['size'] = filesize($realpath);
						$row['mime'] = static::mimetypeInternalDetect($row['id']);
						$row['simg'] = trim($this->options['smallImg'], '/');
						$row['owner'] = xoops_elFinder::getUnameByUid($row['uid']);
						$row['tooltip'] = 'Owner: ' . $row['owner'];
						unset($row['uid'], $row['pid'], $row['lid'], $row['id']);
						if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
							$this->dirsCache[$path][] = $id;
						}
					}
				}
			}
		}
	}

	/**
	 * Return array of parents paths (ids)
	 *
	 * @param  int   $path  file path (id)
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function getParents($path) {
		$parents = [];

		while ($path) {
			if ($file = $this->stat($path)) {
				array_unshift($parents, $path);
				$path = $file['phash'] ? $this->decode($file['phash']) : false;
			}
		}

		if (count($parents)) {
			array_pop($parents);
		}
		return $parents;
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
		if ($path === '_') {
			return '';
		} else {
			[$cid, $name] = explode('_', substr($path, 1), 2);
			return $name;
		}
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
		if ($dir === '_') {
			$cid = 0;
		} else {
			[$cid] = explode('_', substr($dir, 1), 2);
		}
		[$lid] = explode('.', $name);
		$sql = 'SELECT lid, cid FROM '.$this->tbf.' WHERE cid="'.(int)$cid.'" AND lid="'.(int)$lid.'"';
		if (($res = $this->query($sql)) && ($r = $this->db->fetchArray($res))) {
			$id = '_'.$r['cid'].'_'.$r['lid'];
			$this->updateCache($id, $this->_stat($id));
			return $id;
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
		if (! preg_match('/^_\d+_?(?:\d+(?:\.[a-zA-Z0-9.]+)?)?$/', $path)) {
			$path = '_'; // root
		}
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
	protected function _stat($path) {
		$stat = [];
  if ($path === '_') {
			$cid = $lid = 0;
		} else {
			[$cid, $lid] = explode('_', substr($path, 1), 2);
			[$lid] = explode('.', $lid);
		}
		$stat_def = ['size' => 0, 'ts' => 0, 'mime' => '', 'dirs' => 0, 'read' => true, 'write' => false, 'locked' => true, 'hidden' => false, 'url'    => null];

		if (! $cid) {
			$stat['name'] = (! empty($this->options['alias'])? $this->options['alias'] : 'untitle');
			$stat['mime'] = 'directory';
			$stat['dirs'] = true;
			$stat = array_merge($stat_def, $stat);
			return $stat;
		} elseif (! $lid) {
			// cat (dirctory)
			$sql = 'SELECT c.pid, c.cid, c.title as name , s.pid as dirs ' .
					'FROM '.$this->tbc.' AS c ' .
					'LEFT JOIN '.$this->tbc.' AS s ON c.cid=s.pid ' .
					'WHERE c.cid="'.$cid.'" LIMIT 1';
			$res = $this->query($sql);
			if ($res) {
				$stat = $this->db->fetchArray($res);
				$stat = array_merge($stat_def, $stat);
				$stat['mime'] = 'directory';
				$stat['dirs'] = $stat['dirs']? 1 : 0;
				if (! $stat['pid']) {
					$stat['phash'] = $this->encode('_');
				} else {
					$stat['phash'] = $this->encode('_'.$stat['pid'].'_');
				}
				unset($stat['cid'], $stat['pid']);
				return $stat;
			}
		} elseif ($cid) {
			// photos
			$sql = 'SELECT submitter as uid, lid, cid, concat( lid, ".", ext ) AS id, res_x AS width, res_y AS height, `date` AS ts, concat( title, ".", ext ) AS name
					FROM '.$this->tbf.'
					WHERE lid="'.$lid.'" AND status>0 LIMIT 1';
			$res = $this->query($sql);
			if ($res) {
				$stat = $this->db->fetchArray($res);
				$stat = array_merge($stat_def, $stat);
				$stat['phash'] = $this->encode('_'.$cid.'_');
				$stat['url'] = $this->options['URL'].$stat['id'];
				$realpath = realpath($this->options['filePath'].$stat['id']);
				$stat['size'] = filesize($realpath);
				$stat['mime'] = static::mimetypeInternalDetect($stat['id']);
				$stat['simg'] = trim($this->options['smallImg'], '/');
				$stat['owner'] = xoops_elFinder::getUnameByUid($stat['uid']);
				$stat['tooltip'] = 'Owner: ' . $stat['owner'];
				unset($stat['uid'], $stat['lid'], $stat['cid'], $stat['id']);
				return $stat;
			}
		}

		return [];
	}

	/**
	 * Return true if path is dir and has at least one childs directory
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _subdirs($path) {
		return ($stat = $this->stat($path)) ? $stat['dirs'] : false;
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
		return ($stat = $this->stat($path)) && $stat['width'] && $stat['height'] ? $stat['width'].'x'.$stat['height'] : '';
	}

	/******************** file/dir content *********************/

	/**
	 * Return symlink target file
	 *
	 * @param  string  $path  link path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function readlink($path) {
		if ($path !== '_') {
			[, $name] = explode('_', substr($path, 1), 2);
			if ($name) {
				return realpath($this->options['filePath'] . $name);
			}
		}
		return false;
	}

	/**
	 * Return files list in directory.
	 *
	 * @param  string  $path  dir path
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _scandir($path) {
		if (!isset($this->dirsCache[$path])) {
			$this->cacheDir($path);
		}
		return $this->dirsCache[$path];
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
		return false;
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
		return false;
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
		$res = false;
		return $res;
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
		return false;
	}

	/**
	 * Remove file
	 *
	 * @param  string  $path  file path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _unlink($path) {
		return false;
	}

	/**
	 * Remove dir
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _rmdir($path) {
		return false;
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
			if (is_file($local) && $contents = file_get_contents($local)) {
				return $contents;
			}
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
		//if ($local = $this->readlink($path)) {
		//	return file_put_contents($local, $content);
		//}
		return false;
	}

	/**
	 * Detect available archivers
	 *
	 * @return void
	 **/
	protected function _checkArchivers() {
		// die('Not yet implemented. (_checkArchivers)');
		return [];
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
	 * Unpack archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return true
	 * @return void
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
	protected function _unpack($path, $arc) {
		die('Not yet implemented. (_unpack)');
		return false;
	}

	/**
	 * Recursive symlinks search
	 *
	 * @param  string  $path  file/dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _findSymlinks($path) {
		die('Not yet implemented. (_findSymlinks)');
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
	protected function _extract($path, $arc) {
		die('Not yet implemented. (_extract)');
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
		die('Not yet implemented. (_archive)');
		return false;
	}

} // END class
