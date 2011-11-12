<?php

$xoopsOption['nocommon'] = 1 ;
define( '_LEGACY_PREVENT_LOAD_CORE_' , 1 ) ;
define( 'PROTECTOR_SKIP_DOS_CHECK' , 1 ) ;

require '../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirname = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/nocommon.php' ;

?>