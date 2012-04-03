<?php

require_once dirname(dirname(__FILE__)) . '/class/xelFinderMisc.class.php';
$xelFinderMisc = new xelFinderMisc();
$xelFinderMisc->myConfig = $xoopsModuleConfig;
$xelFinderMisc->db = $xoopsDB;
$xelFinderMisc->mydirname = $mydirname;

$xelFinderMisc->mode = 'view';

$file_id = 0;
if (isset($path_info)) {
	list(,$file_id) = explode('/', $path_info);
} elseif (isset($_GET['file'])) {
	list($file_id) = explode('/', $_GET['file']);
}
$file_id = (int)$file_id;

while( ob_get_level() ) {
	if (! @ ob_end_clean()) {
		break;
	}
}

$query = 'SELECT `mime`, `size`, `mtime`, `perm`, `uid` FROM `' . $xoopsDB->prefix($mydirname) . '_file`' . ' WHERE file_id = ' . $file_id . ' LIMIT 1';
if ($file_id && ($res = $xoopsDB->query($query)) && $xoopsDB->getRowsNum($res)) {
	
	list($mime, $size, $mtime, $perm, $uid) = $xoopsDB->fetchRow($res);
	if ($xelFinderMisc->readAuth($perm, $uid, $file_id)) {
		$file = XOOPS_TRUST_PATH . '/uploads/xelfinder/'. rawurlencode(substr(XOOPS_URL, strpos(XOOPS_URL, '://') + 3)) . '_' . $mydirname . '_' . $file_id;
		
		if (! is_file($file)) {
			$xelFinderMisc->exitOut(404);
		}
		
		$xelFinderMisc->output($file, $mime, $size, $mtime);
 	} else {
		$xelFinderMisc->exitOut(403);
	}
} else {
	$xelFinderMisc->exitOut(404);
}

