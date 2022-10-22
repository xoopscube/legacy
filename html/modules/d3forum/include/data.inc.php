<?php
// d3forum plugin for whatsnew module.
// by nao-pon, http://hypweb.net

include_once XOOPS_TRUST_PATH . '/modules/d3forum/include/rss_functions.php';

$mydirname = basename( dirname (__DIR__) );

eval( 'function '.$mydirname . '_new( $limit=0, $offset=0 ){ return d3forum_whatsnew_base( \'' . $mydirname . '\' , $limit, $offset ) ;}') ;
