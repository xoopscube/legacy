<?php

define( '_MD_PICO_FILTERS_XOOPSTSINITWEIGHT', 25 );

define( '_MD_PICO_FILTERS_XOOPSTSEDITOR', 'bbcode' );

define( '_MD_PICO_FILTERS_XOOPSTSCSSCLASS', '' );

define( '_MD_PICO_FILTERS_XOOPSTSUSEHTMLATNEW', false );

define( '_MD_PICO_FILTERS_XOOPSTSDISABLEONHTML', true );

function pico_xoopsts( $mydirname, $text, $content4assign ) {
	$myts = null;
 include_once( XOOPS_ROOT_PATH . '/class/module.textsanitizer.php' );

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &MyTextSanitizer::getInstance();

	// html=0, smiley=1, xcode=1, $image=1, $br=1
	$text = $myts->displayTarea( $text, 0, 1, 1, 1, 1 );

	return $text;
}
