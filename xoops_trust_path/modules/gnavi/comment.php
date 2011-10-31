<?php

eval( '
	function '.$mydirname.'_comments_update( $lid, $total_num ){
	    return gnavi_comments_update_base( "'.$mydirname.'" , $lid , $total_num ) ;
	}
' ) ;

if( ! function_exists( 'gnavi_comments_update_base' ) ) {

	function gnavi_comments_update_base($mydirname, $lid , $total_num ) {
        $db =& Database::getInstance();
		$ret = $db->query( "UPDATE ".$db->prefix($mydirname.'_photos')." SET comments=$total_num WHERE lid=$lid" ) ;
		return $ret ;
	}

}


eval( '
	function '.$mydirname.'_comments_approve( &$comment ){
	    return gnavi_comments_approve_base( "'.$mydirname.'" , $comment ) ;
	}
' ) ;


if( ! function_exists( 'gnavi_comments_approve_base' ) ) {
    function gnavi_comments_approve_base($mydirname, &$comment){
    	// notification mail here
    }

}

?>