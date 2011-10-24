<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once XOOPS_TRUST_PATH.'/modules/d3forum/class/D3commentAbstract.class.php' ;

// a class for d3forum comment integration
class D3pipesD3commentContent extends D3commentAbstract {

function fetchSummary( $external_link_id )
{
	$db =& Database::getInstance() ;
	$myts =& MyTextsanitizer::getInstance() ;

	$mydirname = $this->mydirname ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $mydirname ) ;
	$config_handler =& xoops_gethandler('config');
	$configs = $config_handler->getConfigList( $module->mid() ) ;

	$clipping_id = intval( $external_link_id ) ;
	$clipping = d3pipes_common_get_clipping( $mydirname , $clipping_id ) ;
	if( $clipping === false ) return array() ;

	return array(
		'dirname' => $mydirname ,
		'module_name' => $module->getVar( 'name' ) ,
		'subject' => $myts->makeTboxData4Show( $clipping['headline'] ) ,
		'uri' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=clipping&amp;clipping_id='.$clipping_id ,
		'summary' => htmlspecialchars( @$clipping['link'] , ENT_QUOTES ) ,
	) ;
}


function validate_id( $link_id )
{
	$clipping_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;

	$db =& Database::getInstance() ;

	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_clippings")." WHERE clipping_id=$clipping_id" ) ) ;
	if( $count <= 0 ) return false ;
	else return $clipping_id ;
}


function onUpdate( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 )
{
	$clipping_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;

	$db =& Database::getInstance() ;

	// $count = $this->getPostsCount( $forum_id , $link_id ) ;
	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($this->d3forum_dirname."_posts")." p LEFT JOIN ".$db->prefix($this->d3forum_dirname."_topics")." t ON t.topic_id=p.topic_id WHERE t.forum_id=$forum_id AND t.topic_external_link_id='$clipping_id'" ) ) ; // should be replaced

	$db->queryF( "UPDATE ".$db->prefix($mydirname."_clippings")." SET comments_count=$count WHERE clipping_id=$clipping_id" ) ;

	// remove all cache of the pipe
	list( $pipe_id ) = $db->fetchRow( $db->query( "SELECT pipe_id FROM ".$db->prefix($mydirname."_clippings")." WHERE clipping_id=$clipping_id" ) ) ;
	d3pipes_common_delete_all_cache( $mydirname , $pipe_id , false , false ) ;

	return true ;
}


// get id from <{$clipping_id}>
function external_link_id( $params )
{
	$clipping_id = $this->smarty->get_template_vars( 'clipping_id' ) ;
	return intval( $clipping_id ) ;
}


// get escaped subject from <{$entry.headline}>
function getSubjectRaw( $params )
{
	$entry = $this->smarty->get_template_vars( 'entry' ) ;
	return $this->unhtmlspecialchars( $entry['headline'] , ENT_QUOTES ) ;
}





}

?>