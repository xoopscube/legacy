<?php

define('_MD_PICO_FILTERS_EVALINITWEIGHT',0);
define('_MD_PICO_FILTERS_EVALISINSECURE',1); // only admins/moderators can use it

function pico_eval( $mydirname , $text , $content4assign )
{
	ob_start() ;
	eval( $text ) ;
	$ret = ob_get_contents() ;
	ob_end_clean() ;
	return $ret ;
}

?>