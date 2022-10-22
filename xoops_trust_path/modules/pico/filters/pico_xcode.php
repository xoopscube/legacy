<?php

define( '_MD_PICO_FILTERS_XCODEINITWEIGHT', 25 );

define( '_MD_PICO_FILTERS_XCODEEDITOR', 'bbcode' );

define( '_MD_PICO_FILTERS_XCODECSSCLASS', 'xcode' );

define( '_MD_PICO_FILTERS_XCODEUSEHTMLATNEW', true );

define( '_MD_PICO_FILTERS_XCODEDISABLEONHTML', true );

function pico_xcode( $mydirname, $text, $content4assign ) {
	require_once dirname( __DIR__ ) . '/class/PicoTextSanitizer.class.php';

	$myts = &PicoTextSanitizer::sGetInstance();

	// html=on, smiley=0, xcode=1, $image=1, $br=0
	$text = $myts->displayTarea( $text, 1, 0, 1, 1, 0 );

	$text = $myts->pageBreak( $mydirname, $text, $content4assign );

	return $text;
}
