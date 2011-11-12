<?php

// comment callback functions

require_once dirname(dirname(__FILE__)).'/class/bulletin.php';
function bulletin_com_update($story_id, $total_num){
	$mydirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $GLOBALS['mydirname'] ) ;
	$article = new Bulletin( $mydirname , $story_id);
	
	if (!$article->updateComments($total_num)) {
		return false;
	}
	return true;
}

function bulletin_com_approve(&$comment){
	// notification mail here
}
?>