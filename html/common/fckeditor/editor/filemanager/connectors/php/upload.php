<?php 

define( 'FCK_IS_UPLOAD_CONNECTOR' , 1 ) ;

// for XOOPS
require '../../../../../../mainfile.php' ;

require_once dirname(__FILE__).'/functions.php' ;
@include dirname(__FILE__).'/config_and_auth.inc.php' ;
if( ! defined( 'FCK_UPLOAD_PATH' ) ) {
	require dirname(__FILE__).'/config_and_auth.inc.dist.php' ;
}

FileUpload( '/' ) ;

?>