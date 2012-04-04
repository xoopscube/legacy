<?php
require_once dirname(dirname(__FILE__)) . '/myalbum/driver.class.php';

/**
 * Simple elFinder driver for MySQL.
 *
 * @author Dmitry (dio) Levashov
 **/
class elFinderVolumeXoopsGnavi extends elFinderVolumeXoopsMyalbum {

	/**
	 * Driver id
	 * Must be started from letter and contains [a-z0-9]
	 * Used as part of volume id
	 *
	 * @var string
	 **/
	protected $driverId = 'xg';

	/**
	 * Cache dir contents
	 *
	 * @param  string  $path  dir path
	 * @return void
	 * @author Dmitry Levashov
	 **/
	protected function cacheDir($path) {
		$this->dirsCache[$path] = array();

		if ($path === '_') {
			$cid = 0;
		} else {
			list($cid) = explode('_', substr($path, 1), 2);
		}

		$row_def = array(
			'size' => 0,
			'ts' => 0,
			'mime' => '',
			'dirs' => 0,
			'read' => true,
			'write' => false,
			'locked' => true,
			'hidden' => false
		);

		$_mtime = array();
		$_size = array();

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
			$sql = 'SELECT lid,
					concat( lid, ".", ext ) AS id, res_x AS width, res_y AS height, concat( IF(caption != "", caption, title), ".", ext ) AS name,
					concat( lid, "_1.", ext1 ) AS id1, res_x1 AS width1, res_y1 AS height1, concat( IF(caption1 != "", caption1, concat(title, "_1")), ".", ext1 ) AS name1,
					concat( lid, "_2.", ext2 ) AS id2, res_x2 AS width2, res_y2 AS height2, concat( IF(caption2 != "", caption2, concat(title, "_2")), ".", ext2 ) AS name2,
					`date` AS ts
					FROM '.$this->tbf.'
					WHERE (cid="'.$cid.'" OR cid1="'.$cid.'" OR cid2="'.$cid.'" OR cid3="'.$cid.'" OR cid4="'.$cid.'") AND status>0';

			$res = $this->query($sql);
			if ($res) {
				while ($row = $this->db->fetchArray($res)) {
					$row = array_merge($row_def, $row);
					if (! $cid) {
						$row['phash'] = $this->encode('_');
					} else {
						$row['phash'] = $this->encode('_'.$cid.'_');
					}
					$ids = $width = $height = $name = array();
					$ids[0] = $row['id'];
					$ids[1] = $row['id1'];
					$ids[2] = $row['id2'];
					$width[0] = $row['width'];
					$width[1] = $row['width1'];
					$width[2] = $row['width2'];
					$height[0] = $row['height'];
					$height[1] = $row['height1'];
					$height[2] = $row['height2'];
					$name[0] = $row['name'];
					$name[1] = $row['name1'];
					$name[2] = $row['name2'];
					unset($row['pid'], $row['lid'], $row['cid'],
						$row['id'], $row['width'], $row['height'], $row['name'],
						$row['id1'], $row['width1'], $row['height1'], $row['name1'],
						$row['id2'], $row['width2'], $row['height2'], $row['name2']);
					for($_cnt = 0; $_cnt < 3; $_cnt++) {
						if (substr($ids[$_cnt], -1) === '.') continue;
						$id = '_'.$cid.'_'.$ids[$_cnt];
						$realpath = realpath($this->options['filePath'].$ids[$_cnt]);
						if (is_file($realpath)) {
							$row['width'] = $width[$_cnt];
							$row['height'] = $height[$_cnt];
							$row['name'] = $name[$_cnt];
							$row['url'] = $this->options['URL'].$ids[$_cnt];
							$row['dim'] = $row['width'].'x'.$row['height'];
							$row['size'] = filesize($realpath);
							$row['mime'] = $this->mimetypeInternalDetect($ids[$_cnt]);
							$row['simg'] = trim($this->options['smallImg'], '/');
							if (($stat = $this->updateCache($id, $row)) && empty($stat['hidden'])) {
								$this->dirsCache[$path][] = $id;
							}
						}
					}
				}
			}
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
			list($cid) = explode('_', substr($dir, 1), 2);
		}
		list($lid) = explode('.', $name);
		list($lid) = explode('.', $lid);
		list($lid, $cnt) = array_pad(explode('_', $lid), 2, '');
		if ($cnt) {
			$cnt = '_' . (int)$cnt;
		}
		
		$sql = 'SELECT lid, cid FROM '.$this->tbf.' WHERE cid="'.(int)$cid.'" AND lid="'.(int)$lid.'"';
		if (($res = $this->query($sql)) && ($r = $this->db->fetchArray($res))) {
			$id = '_'.$r['cid'].'_'.$r['lid'].$cnt;
			$this->updateCache($id, $this->_stat($id));
			return $id;
		}
		return -1;
	}

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
		if ($path === '_') {
			$cid = $lid = 0;
		} else {
			list($cid, $lid) = explode('_', substr($path, 1), 2);
			list($lid) = explode('.', $lid);
			list($lid, $cnt) = array_pad(explode('_', $lid), 2, '');
			if ($cnt) {
				$cnt = (int)$cnt;
			}
		}
		$stat_def = array(
			'size' => 0,
			'ts' => 0,
			'mime' => '',
			'dirs' => 0,
			'read' => true,
			'write' => false,
			'locked' => true,
			'hidden' => false
		);

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
			$sql = 'SELECT lid, concat( lid, "'.($cnt? ('_' . $cnt) : '').'.", ext'.$cnt.' ) AS id, res_x'.$cnt.' AS width, res_y'.$cnt.' AS height, `date` AS ts, concat( title, "'.($cnt? ('_' . $cnt) : '').'.", ext'.$cnt.' ) AS name
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
				$stat['mime'] = $this->mimetypeInternalDetect($stat['id']);
				$stat['simg'] = trim($this->options['smallImg'], '/');
				unset($stat['lid'], $stat['id']);
				return $stat;
			}
		}

		return array();
	}
} // END class
