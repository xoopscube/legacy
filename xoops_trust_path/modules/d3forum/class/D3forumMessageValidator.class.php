<?php

class D3forumMessageValidator {

var $errors = array() ;

function get_errors4html()
{
	$ret = '' ;
	foreach( $this->errors as $error ) {
		$ret .= '<span style="color:#f00;">'.htmlspecialchars($error).'</span><br />' ;
	}

	return $ret ;
}


function validate_by_rendered( $html )
{
	$fragments = explode( '<div' , $html ) ;
	$nest_level = 0 ;
	foreach( $fragments as $fragment ) {
		$nest_level -= substr_count( $fragment , '</div>' ) ;
		if( $nest_level < 0 ) {
			$this->errors[] = _MD_D3FORUM_ERR_TOOMANYDIVEND ;
			return false ;
		}
		$nest_level ++ ;
	}
	if( $nest_level != 1 ) {
		$this->errors[] = _MD_D3FORUM_ERR_TOOMANYDIVBEGIN ;
		return false ;
	}

	return true ;
}

}

?>