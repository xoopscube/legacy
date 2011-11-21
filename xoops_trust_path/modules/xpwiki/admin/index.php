<?php

include_once XOOPS_TRUST_PATH."/modules/xpwiki/include.php";
$xw =& XpWiki::getInitedSingleton($mydirname);

if (!$xw->func->get_pgid_by_name($xw->root->defaultpage)) {
	$xw->func->send_location('', '', $xw->cont['HOME_URL'] . '?cmd=dbsync');
}

$check_dir = array(
	$xw->cont['UPLOAD_DIR']      ,
	$xw->cont['UPLOAD_DIR'].'s'  ,
	$xw->cont['BACKUP_DIR']      ,
	$xw->cont['CACHE_DIR']       ,
	$xw->cont['CACHE_DIR'].'page',
	$xw->cont['CACHE_DIR'].'plugin',
	$xw->cont['DIFF_DIR']        ,
	//$xw->cont['COUNTER_DIR']   ,
	$xw->cont['TRACKBACK_DIR']   ,
	$xw->cont['RENDER_CACHE_DIR'],
	$xw->cont['DATA_DIR']
);

$check_dir = array_unique($check_dir);
sort($check_dir);

$dir_res = array();

foreach($check_dir as $dir){
	$dir = rtrim($dir, '/');
	if (is_writable($dir)) {
		$dir .= ' (<span style="color:green;font-weight:bold;">OK</span>)';
	} else {
		$dir .= ' (<span style="color:red;font-weight:bold;">NG</span>)';
	}
	$dir_res[] = $dir;
}

$dir_res = '<ul><li>'.join('</li><li>', $dir_res).'</li></ul>';

// output
xoops_cp_header() ;

include dirname(__FILE__).'/mymenu.php' ;

echo <<<EOD

<h3>Writable check results</h3>
$dir_res

EOD;

xoops_cp_footer() ;

?>