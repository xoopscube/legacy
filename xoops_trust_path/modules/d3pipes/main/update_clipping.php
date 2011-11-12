<?php

require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

// get clipping (raw data)
$clipping_id = intval( @$_POST['clipping_id'] ) ;
$clipping = d3pipes_common_get_clipping( $mydirname , $clipping_id ) ;
if( $clipping === false ) {
	die( _MD_D3PIPES_ERR_INVALIDCLIPPINGID ) ;
}

// special check of update_clipping
if( ! is_object( $xoopsUser ) || ! $xoopsUser->isAdmin() ) {
	die( _MD_D3PIPES_ERR_PERMISSION ) ;
}

// redirect uri and message
$redirect_uri = XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=clipping&clipping_id='.$clipping_id ;
$redirect_message = _MD_D3PIPES_MSG_CLIPPINGUPDATED ;

// highlight
$highlight = empty( $_POST['highlight_clipping'] ) ? 0 : 1 ;
$result = $db->query( "UPDATE ".$db->prefix($mydirname."_clippings")." SET highlight=$highlight WHERE clipping_id=$clipping_id" ) ;

// visible (soft delete)
$visible = empty( $_POST['visible_clipping'] ) ? 0 : 1 ;
$db->query( "UPDATE ".$db->prefix($mydirname."_clippings")." SET can_search=$visible WHERE clipping_id=$clipping_id" ) ;

// delete (hard delete)
if( ! empty( $_POST['delete_clipping'] ) ) {
	if( $clipping['comments_count'] > 0 ) {
		$redirect_message = _MD_D3PIPES_MSG_CLIPPINGCANNOTDELETED ;
	} else {
		$db->query( "DELETE FROM ".$db->prefix($mydirname."_clippings")." WHERE clipping_id=$clipping_id" ) ;
		$redirect_uri = XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=eachpipe&pipe_id='.$clipping['pipe_id'] ;
		$redirect_message = _MD_D3PIPES_MSG_CLIPPINGDELETED ;
	}
}

redirect_header( $redirect_uri , 3 , $redirect_message ) ;
exit ;

?>