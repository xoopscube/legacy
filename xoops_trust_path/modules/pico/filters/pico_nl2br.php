<?php

define( '_MD_PICO_FILTERS_NL2BRINITWEIGHT', 30 );

define( '_MD_PICO_FILTERS_NL2BREDITOR', '' );

define( '_MD_PICO_FILTERS_NL2BRCSSCLASS', '' );

define( '_MD_PICO_FILTERS_NL2BRUSEHTMLATNEW', false );

define( '_MD_PICO_FILTERS_NL2BRDISABLEONHTML', true );

function pico_nl2br( $mydirname, $text, $content4assign ) {
	return nl2br( $text );
}
