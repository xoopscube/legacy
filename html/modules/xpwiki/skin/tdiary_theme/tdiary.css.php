<?php
$xoopsOption['nocommon'] = true;
require '../../../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;
include XOOPS_TRUST_PATH."/modules/xpwiki/skin/".basename(__FILE__); 
?>