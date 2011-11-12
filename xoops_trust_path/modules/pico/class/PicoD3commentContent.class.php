<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once XOOPS_TRUST_PATH.'/modules/d3forum/class/D3commentAbstract.class.php' ;

// a class for d3forum comment integration
class PicoD3commentContent extends D3commentAbstract {

function fetchSummary( $external_link_id )
{
	$myts =& MyTextsanitizer::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $this->mydirname ) ;
	$config_handler =& xoops_gethandler('config');
	$configs = $config_handler->getConfigList( $module->mid() ) ;

	// external_link_id means $content_id
	$content_id = intval( $external_link_id ) ;
	$mydirname = $this->mydirname ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// get categoryObject and contentObject
	list( $categoryObj , $contentObj ) = pico_common_get_objects_from_content_id( $mydirname , $content_id ) ;

	// existence check
	if( ! is_object( $categoryObj ) || ! is_object( $contentObj ) ) return '' ;

	// permission check
	$content_data = $contentObj->getData() ;
	if( empty( $content_data['can_read'] ) ) return '' ;

	// dare to convert it irregularly
	$summary = str_replace( '&amp;' , '&' , htmlspecialchars( xoops_substr( strip_tags( $content_data['body_cached'] ) , 0 , 255 ) , ENT_QUOTES ) ) ;

	return array(
		'dirname' => $mydirname ,
		'module_name' => $module->getVar( 'name' ) ,
		'subject' => $myts->makeTboxData4Show( $content_data['subject_raw'] , 1 , 1 ) ,
		'uri' => XOOPS_URL.'/modules/'.$mydirname.'/'.pico_common_make_content_link4html( $configs , $content_data ) ,
		'summary' => $summary ,
	) ;
}


function validate_id( $link_id )
{
	// assume that link_id as content_id
	$content_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;

	// get categoryObject and contentObject
	list( $categoryObj , $contentObj ) = pico_common_get_objects_from_content_id( $mydirname , $content_id ) ;

	// existence check
	if( ! is_object( $categoryObj ) || ! is_object( $contentObj ) ) return false ;
	// permission check
	$content_data = $contentObj->getData() ;
	if( empty( $content_data['can_read'] ) ) return false ;

	return $content_id ;
}


function onUpdate( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 )
{
	$content_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;

	$db =& Database::getInstance() ;

	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($this->d3forum_dirname."_posts")." p LEFT JOIN ".$db->prefix($this->d3forum_dirname."_topics")." t ON t.topic_id=p.topic_id WHERE t.forum_id=$forum_id AND t.topic_external_link_id='$content_id'" ) ) ;
	$db->queryF( "UPDATE ".$db->prefix($mydirname."_contents")." SET comments_count=$count WHERE content_id=$content_id" ) ;

	return true ;
}


// get id from <{$content.id}>
function external_link_id( $params )
{
	$content = $this->smarty->get_template_vars( 'content' ) ;
	return intval( $content['id'] ) ;
}


// get escaped subject from <{$content.subject}>
function getSubjectRaw( $params )
{
	$content = $this->smarty->get_template_vars( 'content' ) ;
	return $this->unhtmlspecialchars( $content['subject'] , ENT_QUOTES ) ;
}



}

?>