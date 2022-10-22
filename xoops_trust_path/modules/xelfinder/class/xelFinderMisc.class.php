<?php

class xelFinderMisc {
	
	var $myConfig;
	var $db;
	var $mydirname;
	var $mode;
	
	public function __construct($mydirname) {
		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
		$this->mydirname = $mydirname;
	}
	
	private function authPrepare($perm, $f_uid) {
		global $xoopsUser, $xoopsModule;
		
		if (is_object($xoopsUser)) {
			if (empty($xoopsModule) || ! is_object($xoopsModule)) {
				$module_handler = xoops_getHandler('module');
				$xModule = $module_handler->getByDirname($this->mydirname);
			} else {
				$xModule = $xoopsModule;
			}
			$uid = $xoopsUser->getVar('uid');
			$groups = $xoopsUser->getGroups();
			$isAdmin = $xoopsUser->isAdmin($xModule->getVar('mid'));
		} else {
			$uid = 0;
			$groups = array(XOOPS_GROUP_ANONYMOUS);
			$isAdmin = false;
		}
	
		$isOwner = ($isAdmin || ($f_uid && $f_uid == $uid));
		$inGroup = (array_intersect($this->getGroupsByUid($f_uid), $groups))? true : false;
	
		$perm = strval($perm);
		$own = intval($perm[0], 16);
		$grp = intval($perm[1], 16);
		$gus = intval($perm[2], 16);
	
		return array($isOwner, $inGroup, $own, $grp, $gus, $perm);
	}
	
	private function checkAuth($auth, $perm, $f_uid) {
		list($isOwner, $inGroup, $own, $grp, $gus, $perm) = $this->authPrepare($perm, $f_uid);
		//exit(var_dump(array($isOwner, $inGroup, $own, $grp, $gus, $perm)));
		$ret = false;
		if (strpos($auth, 'r') !== false) {
			$ret = (($isOwner && (4 & $own) === 4) || ($inGroup && (4 & $grp) === 4) || (4 & $gus) === 4);
		}
		if ($ret && strpos($auth, 'w') !== false) {
			$ret = (($isOwner && (2 & $own) === 2) || ($inGroup && (2 & $grp) === 2) || (2 & $gus) === 2);
		}
		return $ret;
	}
	
	public function dbSetCharset($charset = 'utf8') {
		if (!$this->db) return false;
		$db = $this->db;
		$link = (is_object($db->conn) && get_class($db->conn) === 'mysqli')? $db->conn : false;
		if ($link) {
			return mysqli_set_charset($link, $charset);
		} else {
			return mysql_set_charset($charset);
		}
	}
	
	public function readAuth($perm, $f_uid, $file_id = null) {
		
		list($isOwner, $inGroup, $own, $grp, $gus, $perm) = $this->authPrepare($perm, $f_uid);
		
		if ($readable = (($isOwner && (4 & $own) === 4) || ($inGroup && (4 & $grp) === 4) || (4 & $gus) === 4)) {
			if ($file_id && $this->mode === 'view' && ! empty($this->myConfig['edit_disable_linked'])) {
				if ((2 & $own) === 2 || (2 & $grp) === 2 || (2 & $gus) === 2 || (1 & $own) === 1 || (1 & $grp) === 1 || (1 & $gus) === 1) {
					$refer = @ $_SERVER['HTTP_REFERER'];
					if (strpos($refer, 'http') === 0 && ! preg_match('#^'.preg_quote(XOOPS_URL).'/[^?]+manager\.php#', $refer)) {
						$perm = dechex($own & ~3).dechex($grp & ~3).dechex($gus & ~3);
						$tbf = $this->db->prefix($this->mydirname) . '_file';
						$sql = sprintf('UPDATE %s SET `perm`="%s" WHERE `file_id` = "%d" LIMIT 1', $tbf, $perm, $file_id);
						$this->db->queryF($sql);
					}
				}
			}
		}
		
		return $readable;
	
	}
	
	public function getGroupsByUid($uid) {
		if ($uid) {
			$user_handler = xoops_getHandler('user');
			$user = $user_handler->get( $uid );
			$groups = $user->getGroups();
		} else {
			$groups = array( XOOPS_GROUP_ANONYMOUS );
		}
		return $groups;
	}
	
	public function getUserHome($auth = 'rw', $uid = null) {
		if (is_null($uid)) {
			global $xoopsUser;
			$uid = is_object($xoopsUser)? $xoopsUser->uid() : 0;
		}
		$tbf = $this->db->prefix($this->mydirname) . '_file';
		$sql = sprintf('SELECT file_id, perm, uid from %s WHERE home_of="%s" LIMIT 1', $tbf, $uid);
		//exit($sql);
		$ret = false;
		//$res = $this->db->query($sql);exit(var_dump($this->db->getRowsNum($res)));
		if (($res = $this->db->query($sql)) && $this->db->getRowsNum($res)) {
			list($id, $perm, $f_uid) = $this->db->fetchRow($res);
			//exit(var_dump(array($id, $perm, $f_uid)));
			if ($this->checkAuth($auth, $perm, $f_uid)) {
				$ret = $id;
			}
		}
		return $ret;
	}
	
	public function getGroupHome($auth = 'rw', $uid = null) {
		if (is_null($uid)) {
			global $xoopsUser;
			$user = $xoopsUser;
		} else if ($uid) {
			$user_handler = xoops_getHandler('user');
			$user = $user_handler->get( $uid );
		} else {
			return false;
		}
		$ret = false;
		$groups = $user->getGroups();
		sort($groups);
		//exit(var_dump($groups));
		if ($groups[0] == XOOPS_GROUP_ANONYMOUS) {
			return isset($groups[1])? $this->getUserHome($auth, '-'.$groups[1]) : false;
		} else {
			return $this->getUserHome($auth, '-'.$groups[0]);
		}
	}
	
	public function getHash($id, $prefix = null) {
		if (is_null($prefix)) {
			$prefix = 'xe_'.$this->mydirname.'_';
		}
		$hash = strtr(base64_encode($id), '+/=', '-_.');
		$hash = rtrim($hash, '.');
		return $prefix.$hash;
	}
	
	public function output($file, $mime, $size, $mtime, $name = '') {
		
		$this->check_304($mtime);
		
		$disp = (isset($_GET['dl']))? 'attachment' : 'inline';
		
		if ($name === '') {
			$filename = '';
		} else {
			$filenameEncoded = rawurlencode($name);
			if (strpos($filenameEncoded, '%') === false) { // ASCII only
				$filename = 'filename="'.$name.'"';
			} else {
				$ua = $_SERVER['HTTP_USER_AGENT'];
				if (preg_match('/MSIE [4-8]/', $ua)) { // IE < 9 do not support RFC 6266 (RFC 2231/RFC 5987)
					$filename = 'filename="'.$filenameEncoded.'"';
				} elseif (strpos($ua, 'Chrome') === false && strpos($ua, 'Safari') !== false && preg_match('#Version/[3-5]#', $ua)) { // Safari < 6
					$filename = 'filename="'.str_replace('"', '', $file['name']).'"';
				} else { // RFC 6266 (RFC 2231/RFC 5987)
					$filename = 'filename*=UTF-8\'\''.$filenameEncoded;
				}
			}
		}
		
		header('Content-Length: '.$size);
		header('Content-Type: '.$mime);
		header('Content-Disposition: '.$disp.'; '.$filename);
		header('Last-Modified: '  . gmdate( "D, d M Y H:i:s", $mtime ) . " GMT" );
		header('Etag: '. $mtime);
		header('Cache-Control: private, max-age=' . XELFINDER_CACHE_TTL );
		header('Expires: ' . gmdate( "D, d M Y H:i:s", XELFINDER_UNIX_TIME + XELFINDER_CACHE_TTL ) . ' GMT');
		header('Pragma:');
		
		if (function_exists('XC_CLASS_EXISTS') && XC_CLASS_EXISTS('HypCommonFunc')) {
			HypCommonFunc::readfile($file);
		} else {
			readfile($file);
		}
	}
	
	public function check_304($time) {
		if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && $time == $_SERVER['HTTP_IF_NONE_MATCH'])
		     || $time <= $this->if_modified_since()) {
			header('HTTP/1.1 304 Not Modified');
			header('Etag: '. $time);
			header('Cache-Control: public, max-age=' . XELFINDER_CACHE_TTL );
			header('Expires: ' . gmdate( "D, d M Y H:i:s", XELFINDER_UNIX_TIME + XELFINDER_CACHE_TTL) . ' GMT');
			header('Pragma:');
			exit;
		}
	}
	
	public function if_modified_since() {
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$str = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
			if (($pos = strpos($str, ';')) !== false) {
				$str = substr($str, 0, $pos);
			}
			if (strpos($str, ',') === false) {
				$str .= ' GMT';
			}
			$time = strtotime($str);
		}
	
		if (isset($time) && is_int($time)) {
			return $time;
		} else {
			return -1;
		}
	}
	
	public function exitOut($code) {
		switch ($code) {
			case 403:
				header('HTTP/1.0 403 Forbidden');
				exit('403 Forbidden');
			case 404:
				header('HTTP/1.0 404 Not Found');
				exit('404 Not Found');
			default:
				exit;
		}
	}

}
