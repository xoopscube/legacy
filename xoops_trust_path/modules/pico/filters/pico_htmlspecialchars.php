<?php

define('_MD_PICO_FILTERS_HTMLSPECIALCHARSINITWEIGHT',5);

function pico_htmlspecialchars( $mydirname , $text , $content4assign )
{
	return htmlspecialchars( $text , ENT_QUOTES ) ;
}

?>