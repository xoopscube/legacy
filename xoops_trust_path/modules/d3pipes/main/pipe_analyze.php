<?php

require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

if( ! is_object( $xoopsUser ) || ! $xoopsUser->isAdmin() ) {
	die( 'Only administrator can use this feature.' ) ;
}

// fetch pipe_row
$pipe_id = intval( @$_GET['pipe_id'] ) ;
$pipe4assign = d3pipes_common_get_pipe4assign( $mydirname , $pipe_id ) ;

// fetch step
$step = intval( @$_GET['step'] ) ;

// cut joints after the step
$pipe4assign['joints'] = array_slice( $pipe4assign['joints'] , 0 , $step + 1 ) ;

// force to remove all cache about the pipe
if( $pipe4assign['joints'][0]['joint'] == 'fetch' ) {
	d3pipes_common_delete_all_cache( $mydirname , $pipe_id ) ;
}

// fetch entries
$entries = d3pipes_common_fetch_entries( $mydirname , $pipe4assign , 0x7fff /* No limit */ , $errors , $xoopsModuleConfig ) ;

ob_start() ;
var_dump( $entries ) ;
echo "--- errors ---\n" ;
var_dump( $errors ) ;
$body = ob_get_contents() ;
ob_end_clean() ;

if( strstr( @$_SERVER['HTTP_USER_AGENT'] , 'MSIE' ) ) {
	// for the idiot browser :-)
	@ini_set( 'default_charset' , 'UTF-8' ) ;
	header( 'Content-type: text/html;' ) ;
	echo '<pre>'.htmlspecialchars( $body ).'</pre>' ;
} else {
	@ini_set( 'default_charset' , '' ) ;
	header( 'Content-type: text/plain;' ) ;
	echo $body ;
}
exit ;


?>