<?php

// a class for d3forum comment integration
class GnaviD3commentContent extends D3commentAbstract {

function fetchSummary( $external_link_id )
{

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $this->mydirname ) ;

	$db =& Database::getInstance() ;
	$myts =& MyTextsanitizer::getInstance() ;

	$content_id = intval( $external_link_id ) ;
	$mydirname = $this->mydirname ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// query
	$content_row = $db->fetchArray( $db->query( "SELECT l.title AS title , c.description AS description FROM ".$db->prefix($mydirname."_photos")." l LEFT JOIN ".$db->prefix($mydirname."_text")." c ON l.lid = c.lid WHERE l.lid=$content_id AND l.status > 0 " ) ) ;
	if( empty( $content_row ) ) return '' ;

	// dare to convert it irregularly
	$summary = str_replace( '&amp;' , '&' , htmlspecialchars( xoops_substr( strip_tags( $content_row['description'] ) , 0 , 255 ) , ENT_QUOTES ) ) ;

	return array(
		'dirname' => $mydirname ,
		'module_name' => $module->getVar( 'name' ) ,
		'subject' => $myts->makeTboxData4Show( $content_row['title'] ) ,
		'uri' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?lid='.$content_id ,
		'summary' => $summary ,
	) ;
}


function validate_id( $link_id )
{
	$content_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;

	$db =& Database::getInstance() ;
	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_photos")." WHERE lid = $content_id AND status > 0 " ) ) ;
	if( $count <= 0 ) return false ;
	else return $content_id ;

}


function onUpdate( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 )
{
	$content_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;

	$db =& Database::getInstance() ;

	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($this->d3forum_dirname."_posts")." p LEFT JOIN ".$db->prefix($this->d3forum_dirname."_topics")." t ON t.topic_id=p.topic_id WHERE t.forum_id=$forum_id AND t.topic_external_link_id='$content_id'" ) ) ;
	$db->queryF( "UPDATE ".$db->prefix($mydirname."_photos")." SET comments = $count WHERE lid=$content_id" ) ;

	return true ;
}


}

?>