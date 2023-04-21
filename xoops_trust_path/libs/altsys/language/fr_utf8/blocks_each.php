<?php
// definitions for displaying blocks
// Since altsys is a singleton module, this file has non-sense.
if (defined('FOR_XOOPS_LANG_CHECKER')) {
    $mydirname = 'altsys' ;
}
$constpref = '_MB_' . strtoupper($mydirname) ;

if (defined('FOR_XOOPS_LANG_CHECKER') || ! defined($constpref.'_LOADED')) {
    define($constpref.'_LOADED', 1) ;
    // none
}
