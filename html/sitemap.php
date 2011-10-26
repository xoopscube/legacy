<?php

// dirname
$dirname = empty( $_GET['dirname'] ) ? 'd3pipes' : preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $_GET['dirname'] ) ;
if( ! file_exists( './modules/' . $dirname . '/mytrustdirname.php' ) ) die( 'invalid dirname (1)' ) ;
require dirname(__FILE__).'/modules/'.$dirname.'/mytrustdirname.php' ;
if( $mytrustdirname != 'd3pipes' ) die( 'invalid dirname (2)' ) ;
define( 'D3PIPES_SITEMAP_DIRNAME' , $dirname ) ; // set dirname (string)
unset( $dirname , $mytrustdirname ) ;

// set $_GET
$_GET = array( 'page' => 'xml' , 'style' => 'sitemap' , 'pipe_id' => intval( @$_GET['pipe_id'] ) ) ;

// set $_SERVER
$_SERVER['QUERY_STRING'] = '' ;
foreach( $_GET as $key => $val ) {
	$_SERVER['QUERY_STRING'] .= $key.'='.urlencode($val).'&' ;
}
unset( $key , $val ) ;
if( empty( $_SERVER['REQUEST_URI'] ) ) {
	$_SERVER['REQUEST_URI'] = @$_SERVER['PHP_SELF'] ;
}
$_SERVER['REQUEST_URI'] = str_replace( basename( __FILE__ ) , 'modules/'.D3PIPES_SITEMAP_DIRNAME.'/index.php?'.$_SERVER['QUERY_STRING'] , $_SERVER['REQUEST_URI'] ) ;
$_SERVER['PHP_SELF'] = $_SERVER['REQUEST_URI'] ;

chdir( './modules/'.D3PIPES_SITEMAP_DIRNAME.'/' ) ;
require dirname(__FILE__).'/modules/'.D3PIPES_SITEMAP_DIRNAME.'/index.php' ;

?>