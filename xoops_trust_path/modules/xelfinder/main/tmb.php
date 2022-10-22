<?php

require_once dirname( __DIR__ ) . '/class/xelFinderMisc.class.php';

$xelFinderMisc           = new xelFinderMisc( $mydirname );
$xelFinderMisc->myConfig = $xoopsModuleConfig;
$xelFinderMisc->dbSetCharset( 'utf8' );

$xelFinderMisc->mode = 'tmb';

$file_id = 0;
$s = 0;
if (isset($path_info)) {
	list(, $s, $file_id) = explode('/', $path_info);
} elseif (isset($_GET['s']) && isset($_GET['file'])) {
	$s = $_GET['s'];
	list($file_id) = explode('/', $_GET['file']);
	
}
$file_id = (int)$file_id;
$s = max(16, (int)$s);

while( ob_get_level() ) {
	if (! @ ob_end_clean()) {
		break;
	}
}

$query = 'SELECT `width`, `height`, `mime`, `size`, `mtime`, `perm`, `uid`, `local_path` FROM `' . $xoopsDB->prefix($mydirname) . '_file`' . ' WHERE file_id = ' . $file_id . ' LIMIT 1';
if ($file_id && ($res = $xoopsDB->query($query)) && $xoopsDB->getRowsNum($res)) {
	list($width, $height, $mime, $size, $mtime, $perm, $uid, $file) = $xoopsDB->fetchRow($res);
	if ($xelFinderMisc->readAuth($perm, $uid)) {
		
		@include_once XOOPS_TRUST_PATH . '/class/hyp_common/hyp_common_func.php';
		
		$prefix = defined('XELFINDER_DB_FILENAME_PREFIX')? XELFINDER_DB_FILENAME_PREFIX : substr(XOOPS_URL, strpos(XOOPS_URL, '://') + 3);
		$basepath = XOOPS_TRUST_PATH . '/uploads/xelfinder/'. rawurlencode($prefix) . '_' . $mydirname . '_';
		if (! $file) {
			$tmb = $file = $basepath . $file_id;
		} else {
			$tmb = $basepath . md5($file);
			if (substr($file, 1, 1) === '/') {
				$_head = substr($file, 0, 1);
				if (strpos($file, '%') !== false) {
					$file = dirname($file) . DIRECTORY_SEPARATOR . rawurldecode(basename($file));
				}
				switch($_head) {
					case 'R':
						$file = XOOPS_ROOT_PATH . substr($file, 1);
						break;
					case 'T':
						$file = XOOPS_TRUST_PATH . substr($file, 1);
						break;
				}
			}
		}
		
		$out = $file;
		
		if (! is_file($file)) {
			$xelFinderMisc->exitOut(404);
		}
		
		$check = max($width, $height);
		if ($s < $check && function_exists('XC_CLASS_EXISTS') && XC_CLASS_EXISTS('HypCommonFunc')) {
			$s_file = $tmb . '_' . intval($s / $check * 100) . '.tmb';
			$out = HypCommonFunc::make_thumb($file, $s_file, $s, $s);
			if ($out !== $file) {
				$size = filesize($out);
				$mtime = filemtime($out);
				$mime = 'image';
			}
		}
		
		$xelFinderMisc->output($out, $mime, $size, $mtime);
	} else {
		$xelFinderMisc->exitOut(403);
	}
} else {
	$xelFinderMisc->exitOut(404);
}
