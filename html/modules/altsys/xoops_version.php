<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * @package    Altsys
 * @version    2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c)) 2005-2025 Gijoe (https://peak.ne.jp/)
 * @license    GPL 2.0
 */

if (! defined('XOOPS_TRUST_PATH')) {
    die('set XOOPS_TRUST_PATH into mainfile.php') ;
}

$mydirname = basename(__DIR__) ;
$mydirpath = __DIR__;

$mytrustdirname = 'altsys' ;

require XOOPS_TRUST_PATH.'/libs/'.$mytrustdirname.'/xoops_version.php' ;
