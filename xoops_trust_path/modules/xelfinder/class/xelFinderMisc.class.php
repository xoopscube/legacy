<?php

class xelFinderMisc {
	
	var $myConfig;
	var $db;
	var $mydirname;
	var $mode;
	
	function readAuth($perm, $f_uid, $file_id) {
		global $xoopsUser, $xoopsModule;
		if (is_object($xoopsUser)) {
			$uid = $xoopsUser->getVar('uid');
			$groups = $xoopsUser->getGroups();
			$isAdmin = $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
		} else {
			$uid = 0;
			$groups = array(XOOPS_GROUP_ANONYMOUS);
			$isAdmin = false;
		}
	
		$isOwner = ($isAdmin || ($f_uid && $f_uid == $uid));
		$inGroup = (array_intersect($this->getGroupsByUid($f_uid), $groups));
	
		$perm = strval($perm);
		$own = intval($perm[0], 16);
		$grp = intval($perm[1], 16);
		$gus = intval($perm[2], 16);
	
		if ($readable = (($isOwner && (4 & $own) === 4) || ($inGroup && (4 & $grp) === 4) || (4 & $gus) === 4)) {
			if ($this->mode === 'view' && ! empty($this->myConfig['edit_disable_linked'])) {
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
	
	function getGroupsByUid($uid) {
		if ($uid) {
			$user_handler =& xoops_gethandler('user');
			$user =& $user_handler->get( $uid );
			$groups = $user->getGroups();
		} else {
			$groups = array( XOOPS_GROUP_ANONYMOUS );
		}
		return $groups;
	}
	
	function output($file, $mime, $size, $mtime) {
		
		$this->check_304($mtime);
		
		header('Content-Length: '.$size);
		header('Content-Type: '.$mime);
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
	
	function check_304($time) {
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
	
	function if_modified_since() {
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
	
	function exitOut($code) {
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
