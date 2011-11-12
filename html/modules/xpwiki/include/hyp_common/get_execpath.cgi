#!/usr/bin/env php
<?php
define('_LEGACY_PREVENT_LOAD_CORE_', TRUE);
include '../../../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;
require XOOPS_TRUST_PATH.'/class/hyp_common/get_execpath.cgi';
?>