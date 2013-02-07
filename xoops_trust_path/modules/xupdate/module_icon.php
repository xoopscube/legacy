<?php

$icon_cache_limit = 3600 ; // default 3600sec == 1hour

session_cache_limiter('public');
header("Expires: ".date('r',intval(time()/$icon_cache_limit)*$icon_cache_limit+$icon_cache_limit));
header("Cache-Control: public, max-age=$icon_cache_limit");
header("Last-Modified: ".date('r',intval(time()/$icon_cache_limit)*$icon_cache_limit));
header("Content-type: image/png");

	$use_custom_icon = false ;
	$icon_fullpath = dirname(__FILE__).'/module_icon.png' ;

	readfile( $icon_fullpath ) ;

?>