<?php

// === option begin ===
// $category_option  に表示するカテゴリ番号をカンマ(,)で区切って記入。空欄なら全カテゴリー表示。
// $intree を '1' にすると、配下のサブカテゴリも表示することができます。
$category_option = '' ;

// --- option end ---

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' ) ;

$mydirname = basename( dirname(  dirname( __FILE__ ) ) ) ;
$mydirpath = dirname( dirname( __FILE__ ) ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/include/whatsnew.inc.php' ;

?>