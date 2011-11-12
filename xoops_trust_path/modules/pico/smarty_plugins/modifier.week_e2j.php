<?php

function smarty_modifier_week_e2j( $text )
{
	return str_replace( array('Mon','Tue','Wue','Thu','Fri','Sat','Sun') , array('月','火','水','木','金','土','日') , $text ) ;
}

?>