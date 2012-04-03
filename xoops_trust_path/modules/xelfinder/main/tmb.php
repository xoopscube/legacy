<?php

require_once dirname(dirname(__FILE__)) . '/class/xelFinderMisc.class.php';
$xelFinderMisc = new xelFinderMisc();
$xelFinderMisc->myConfig = $xoopsModuleConfig;
$xelFinderMisc->db = $xoopsDB;
$xelFinderMisc->mydirname = $mydirname;

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

$query = 'SELECT `width`, `height`, `mime`, `size`, `mtime`, `perm`, `uid` FROM `' . $xoopsDB->prefix($mydirname) . '_file`' . ' WHERE file_id = ' . $file_id . ' LIMIT 1';
if ($file_id && ($res = $xoopsDB->query($query)) && $xoopsDB->getRowsNum($res)) {
	list($width, $height, $mime, $size, $mtime, $perm, $uid) = $xoopsDB->fetchRow($res);
	if ($xelFinderMisc->readAuth($perm, $uid)) {
		
		@include_once XOOPS_TRUST_PATH . '/class/hyp_common/hyp_common_func.php';
		
		$out = $file = XOOPS_TRUST_PATH . '/uploads/xelfinder/'. rawurlencode(substr(XOOPS_URL, strpos(XOOPS_URL, '://') + 3)) . '_' . $mydirname . '_' . $file_id;
		
		if (! is_file($file)) {
			$xelFinderMisc->exitOut(404);
		}
		
		$check = max($width, $height);
		if ($s < $check && function_exists('XC_CLASS_EXISTS') && XC_CLASS_EXISTS('HypCommonFunc')) {
			$s_file = $file . '_' . intval($s / $check * 100) . '.tmb';
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
