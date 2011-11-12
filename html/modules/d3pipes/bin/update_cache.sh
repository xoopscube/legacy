#!/usr/local/bin/php
<?php

if( ! empty( $_SERVER['HTTP_HOST'] ) ) die( 'This script cannot be accessed via httpd' ) ;

chdir( dirname( __FILE__ ) ) ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

// dummy variables
$_SERVER['REMOTE_ADDR'] = '192.168.0.1' ;
$_SERVER['REQUEST_URI'] = '/modules/'.$mydirname.'/' ;
$_SERVER['REQUEST_METHOD'] = 'GET' ;

require '../../../mainfile.php' ;
require XOOPS_TRUST_PATH.'/modules/d3pipes/include/update_cache.inc.php' ;

?>