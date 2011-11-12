<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: pukiwiki.css.php,v 1.32 2011/07/29 07:14:26 nao-pon Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Default CSS

// Default charset
$charset = isset($_GET['charset']) ? $_GET['charset']  : '';
switch ($charset) {
	case 'Shift_JIS': break; /* this @charset is for Mozilla's bug */
	default: $charset ='iso-8859-1';
}

// Media
$media   = isset($_GET['media'])   ? $_GET['media']    : '';
if ($media != 'print') $media = 'screen';

// Base
$dir   = isset($_GET['base'])   ? "_".preg_replace("/[^\w-]+/","",$_GET['base'])    : '';
$class = "div.xpwiki".$dir;

// Pre Width
$pre_width = isset($_GET['pw']) ? $_GET['pw'] : 'auto';

// Over write
$overwrite = (empty($overwrite))? '' : $overwrite;

// Etag
$filetime = ($media === 'print')? filemtime(dirname(__FILE__) . '/css/main_print.css') :
									filemtime(dirname(__FILE__) . '/css/main.css');
$etag = md5($dir.$charset.$media.$overwrite.$pre_width.$filetime);

// Not Modified?
if ($etag === @$_SERVER["HTTP_IF_NONE_MATCH"]) {
	header( "HTTP/1.1 304 Not Modified" );
	header( "Etag: ". $etag );
	exit();
}

// Output buffering start
ob_start();

// Output CSS ----
if ($media === 'print') {
	include (dirname(__FILE__) . '/css/main_print.css');
} else {
	include (dirname(__FILE__) . '/css/main.css');
}

// Over write
echo $overwrite;

$out = str_replace(array('$class', '$dir', '$pre_width', '$charset'),
					array($class, $dir, $pre_width, $charset),
					ob_get_contents());

while( ob_get_level() ) {
	if (! ob_end_clean()) {
		break;
	}
}

// Send header
header('Content-Type: text/css');
$matches = array();
if(ini_get('zlib.output_compression') && preg_match('/\b(gzip|deflate)\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'], $matches)) {
	header('Content-Encoding: ' . $matches[1]);
	header('Vary: Accept-Encoding');
}
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s", filemtime(__FILE__) ) . " GMT" );
header( "Etag: ". $etag );
header( "Content-length: ". strlen($out) );
echo $out;
?>