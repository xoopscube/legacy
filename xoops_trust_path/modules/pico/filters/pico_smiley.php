<?php

define('_MD_PICO_FILTERS_SMILEYINITWEIGHT',30);

function pico_smiley( $mydirname , $text , $content4assign )
{
	$myts =& MyTextSanitizer::getInstance() ;

	// html=on, smiley=0, xcode=1, $image=1, $br=0
	$text = $myts->smiley( $text ) ;
	return $text ;
}

?>