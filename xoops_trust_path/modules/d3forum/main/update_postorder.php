<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.php' ;

// get cookie path
$xoops_cookie_path = defined('XOOPS_COOKIE_PATH') ? XOOPS_COOKIE_PATH : preg_replace( '?http://[^/]+(/.*)$?' , "$1" , XOOPS_URL ) ;
if( $xoops_cookie_path == XOOPS_URL ) $xoops_cookie_path = '/' ;

// update cookie
setcookie( $mydirname.'_postorder' , intval( $_GET['postorder'] ) , time() + 86400 * 30 , $xoops_cookie_path ) ;

$allowed_identifiers = array( 'post_id' , 'topic_id' , 'forum_id' ) ;

if( in_array( $_GET['ret_name'] , $allowed_identifiers ) ) {
	$ret_request = $_GET['ret_name'] . '=' . intval( $_GET['ret_val'] ) ;
} else {
	$ret_request = "topic_id=$topic_id" ;
}

header( "Location: ".XOOPS_URL."/modules/$mydirname/index.php?$ret_request" ) ;
exit ;

?>