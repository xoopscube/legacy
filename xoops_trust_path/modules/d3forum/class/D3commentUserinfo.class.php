<?php

// sample class for d3forum comment integration
class D3commentUserinfo extends D3commentAbstract {


// get reference description as string
function fetchDescription( $link_id )
{
	$user_handler =& xoops_gethandler( 'user' ) ;
	$user =& $user_handler->get( $link_id ) ;
	if( is_object( $user ) ) {
		return '
			<table class="outer">
				<tr>
					<td style="width:20%;" class="head">'._MD_D3FORUM_LINK_COMMENTSOURCE.'
					<td class="even"><a href="'.XOOPS_URL.'/userinfo.php?uid='.$link_id.'">'.$user->getVar('uname').'</a></td>
				</tr>
			</table>
		' ;
	} else {
		return '' ;
	}

	return false ;
}

// get reference information as array
function fetchSummary( $link_id )
{
	$user_handler =& xoops_gethandler( 'user' ) ;
	$user =& $user_handler->get( $link_id ) ;
	if( is_object( $user ) ) {
		return array( 'module_name' => '' , 'subject' => $user->getVar('uname') , 'uri' => XOOPS_URL.'/userinfo.php?uid='.$link_id , 'summary' => '' ) ;
	} else {
		return 'invalid uid' ;
	}
	// all values should be HTML escaped.
}


function external_link_id( $params )
{
	return intval( @$_GET[ $params['itemname'] ] ) ;
}


// check by user handler
function validate_id( $link_id )
{
	$link_id = intval( $link_id ) ;

	$user_handler =& xoops_gethandler( 'user' ) ;
	$user =& $user_handler->get( $link_id ) ;
	if( is_object( $user ) ) {
		return $link_id ;
	} else {
		return false ;
	}
}


// callback on newtopic/edit/reply/delete
// abstract
function onUpdate( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 )
{
	return true ;
}


}

?>