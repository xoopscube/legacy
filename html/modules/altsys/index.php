<?php

$xoopsOption['nocommon'] = 1 ;
define('_LEGACY_PREVENT_LOAD_CORE_',true) ;
require '../../mainfile.php' ;

header( 'Location: '.XOOPS_URL.'/user.php' ) ;

?>