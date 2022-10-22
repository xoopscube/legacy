<?php

define( '_MD_PICO_FILTERS_EVALINITWEIGHT', 0 );
define( '_MD_PICO_FILTERS_EVALISINSECURE', 1 ); // only admins/moderators can use it

define( '_MD_PICO_FILTERS_EVALEDITOR', 'none' );
define( '_MD_PICO_FILTERS_EVALCSSCLASS', 'plain' );
define( '_MD_PICO_FILTERS_EVALUSEHTMLATNEW', false );
define( '_MD_PICO_FILTERS_EVALDISABLEONHTML', true );

function pico_eval( $mydirname, $text, $content4assign ) {
	ob_start();
	eval( $text );
	$ret = ob_get_clean();

	return $ret;
}
