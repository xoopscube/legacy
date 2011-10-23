<?php

require_once XOOPS_TRUST_PATH.'/modules/d3forum/include/comment_functions.php' ;
require_once XOOPS_TRUST_PATH.'/modules/d3forum/include/main_functions.php' ;
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3NotificationHandler.class.php' ;

// abstract class for d3forum comment integration
class D3commentAbstract {

var $d3forum_dirname = '' ;
var $mydirname = '' ;
var $module = null ;
var $mytrustdirname = '' ;
var $mod_config = array() ;
var $smarty = null ;

public function __construct( $d3forum_dirname , $target_dirname , $target_trustdirname = '' )
{
	$this->mydirname = $target_dirname ;
	$this->mytrustdirname = $target_trustdirname ;
	$this->d3forum_dirname = $d3forum_dirname ;

	// set $this->mod_config as config of target_module
	if( $this->mydirname ) {
		$module_hanlder =& xoops_gethandler( 'module' ) ;
		$config_handler =& xoops_gethandler( 'config' ) ;
		$this->module =& $module_hanlder->getByDirname( $this->mydirname ) ;
		if( is_object( $this->module ) ) {
			$this->mod_config =& $config_handler->getConfigsByCat( 0 , $this->module->getVar( 'mid' ) ) ;
		}
	}

	if( empty( $d3forum_dirname ) ) $this->setD3forumDirname() ;
}


// set smarty
function setSmarty( &$smarty )
{
	$this->smarty =& $smarty ;
}


// abstract (override it)
// set d3forum_dirname from parameter or config
function setD3forumDirname( $d3forum_dirname = '' )
{
	if( $d3forum_dirname ) {
		$this->d3forum_dirname = $d3forum_dirname ;
	} else if( ! empty( $this->mod_config['comment_dirname'] ) ) {
		$this->d3forum_dirname = $this->mod_config['comment_dirname'] ;
	} else {
		$this->d3forum_dirname = 'd3forum' ;
	}
}


// get forum_id from $params or config
// override it if necessary
function getForumId( $params )
{
	if( ! empty( $params['forum_id'] ) ) {
		return intval( $params['forum_id'] ) ;
	} else if( ! empty( $this->mod_config['comment_forum_id'] ) ) {
		return $this->mod_config['comment_forum_id'] ;
	} else {
		return 1 ;
	}
}


// get view from $params or config
// override it if necessary
function getView( $params )
{
	if( ! empty( $params['view'] ) ) {
		return $params['view'] ;
	} else if( ! empty( $this->mod_config['comment_view'] ) ) {
		return $this->mod_config['comment_view'] ;
	} else {
		return 'listposts' ;
	}
}


// get view from $params or config
// override it if necessary
function getOrder( $params )
{
	if( ! empty( $params['order'] ) ) {
		return strtolower( $params['order'] ) ;
	} else if( ! empty( $this->mod_config['comment_order'] ) ) {
		return $this->mod_config['comment_order'] ;
	} else {
		return 'desc' ;
	}
}


// get number of posts will be displayed from $params or config
// override it if necessary
function getPostsNum( $params )
{
	if( ! empty( $params['posts_num'] ) ) {
		return $params['posts_num'] ;
	} else if( ! empty( $this->mod_config['comment_posts_num'] ) ) {
		return $this->mod_config['comment_posts_num'] ;
	} else {
		return 10 ;
	}
}


// abstract (override it)
// get reference description as string
function fetchDescription( $link_id )
{
	return false ;
}


// abstract (override it)
// get reference information as array
function fetchSummary( $link_id )
{
	return array( 'module_name' => '' , 'subject' => '' , 'uri' => '' , 'summary' => '' ) ;
	// all values should be HTML escaped.
}


// get external_link_id from $params
// override it if necessary
function external_link_id( $params )
{
	return @$params['id'] ;
}


// get subject not escaped
// override it if necessary
function getSubjectRaw( $params )
{
	return empty( $params['subject_escaped'] ) ? @$params['subject'] : $this->unhtmlspecialchars( @$params['subject'] ) ;
}


// public
function displayCommentsInline( $params )
{
	$new_params = $this->restructParams( $params ) ;

	d3forum_render_comments( $this->d3forum_dirname , $new_params['forum_id'] , $new_params , $this->smarty ) ;
}


// public
function displayCommentsCount( $params )
{
	$comments_count = $this->countComments( $this->restructParams( $params ) ) ;

	if( empty( $params['var'] ) ) {
		// display
		echo $comments_count ;
	} else {
		// assign as "var"
		$this->smarty->assign( $params['var'] , $comments_count ) ;
	}
}


// protected
function restructParams( $params )
{
	return array(
		'class' => $params['class'] ,
		'view' => $this->getView( $params ) ,
		'order' => $this->getOrder( $params ) ,
		'posts_num' => $this->getPostsNum( $params ) ,
		'subject_raw' => $this->getSubjectRaw( $params ) ,
		'forum_id' => $this->getForumId( $params ) ,
		'forum_dirname' => $this->d3forum_dirname ,
		'external_link_id' => $this->external_link_id( $params ) ,
		'external_dirname' => $this->mydirname ,
		'external_trustdirname' => $this->mytrustdirname ,
	) ;
}


// minimum check
// if you want to allow "string id", override it
function validate_id( $link_id )
{
	$ret = intval( $link_id ) ;
	if( $ret <= 0 ) return false ;
	return $ret ;
}


// callback on newtopic/edit/reply/delete
// abstract
function onUpdate( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 )
{
	return true ;
}


// can vote
// override it if necessary
function canVote( $link_id , $original_flag , $post_id )
{
	return $original_flag ;
}


// can post
// override it if necessary
function canPost( $link_id , $original_flag )
{
	return $original_flag ;
}


// can reply
// override it if necessary
function canReply( $link_id , $original_flag , $post_id )
{
	return $original_flag ;
}


// can edit
// override it if necessary
function canEdit( $link_id , $original_flag , $post_id )
{
	return $original_flag ;
}


// can delete
// override it if necessary
function canDelete( $link_id , $original_flag , $post_id )
{
	return $original_flag ;
}


// can delete
// override it if necessary
function needApprove( $link_id , $original_flag )
{
	return $original_flag ;
}


// processing xoops notification for 'comment'
// override it if necessary
function processCommentNotifications( $mode , $link_id , $forum_id , $topic_id , $post_id )
{
	// non-module integration returns false quickly
	if( ! is_object( $this->module ) ) return false ;

	$not_module =& $this->module ;
	$not_modid = $this->module->getVar('mid') ;
	$not_catinfo =& notificationCommentCategoryInfo( $not_modid ) ;

	// module without 'comment' notification
	if( empty( $not_catinfo ) ) return false ;

	$not_category = $not_catinfo['name'] ;
	$not_itemid = $link_id ;
	$not_event = 'comment' ; // 'comment_submit'?

	$comment_tags = array( 'X_COMMENT_URL' => XOOPS_URL.'/modules/'.$this->d3forum_dirname.'/index.php?post_id='.intval($post_id) ) ;

	$users2notify = d3forum_get_users_can_read_forum( $this->d3forum_dirname , $forum_id ) ;
	if( empty( $users2notify ) ) $users2notify = array( 0 ) ;

	$not_handler =& D3NotificationHandler::getInstance() ;
	$not_handler->triggerEvent( $this->mydirname , $this->mytrustdirname , $not_category , $not_itemid , $not_event , $comment_tags , $users2notify ) ;
}


// returns comment count
// override it if necessary
function countComments( $params )
{
	$db =& Database::getInstance() ;

	$forum_id = $params['forum_id'] ;
	$mydirname = $params['forum_dirname'] ;

	// check the d3forum exists and is active
	$module_hanlder =& xoops_gethandler( 'module' ) ;
	$module =& $module_hanlder->getByDirname( $mydirname ) ;
	if( ! is_object( $module ) || ! $module->getVar('isactive') ) {
		return 0 ;
	}

	// does not check the permission of "module_read" about the d3forum

	// query it
	$select = $params['view'] == 'listtopics' ? 'COUNT(t.topic_id)' : 'SUM(t.topic_posts_count)' ;
	$sql = "SELECT $select FROM ".$db->prefix($mydirname."_topics")." t WHERE t.forum_id=$forum_id AND ! t.topic_invisible AND topic_external_link_id='".addslashes($params['external_link_id'])."'" ;
	if( ! $trs = $db->query( $sql ) ) die( 'd3forum_comment_error in '.__LINE__ ) ;
	list( $count ) = $db->fetchRow( $trs ) ;
	
	return $count ;
}


// returns posts count (does not check the permissions)
function getPostsCount( $forum_id , $link_id )
{
	$db =& Database::getInstance() ;

	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($this->d3forum_dirname."_posts")." p LEFT JOIN ".$db->prefix($this->d3forum_dirname."_topics")." t ON t.topic_id=p.topic_id WHERE t.forum_id=$forum_id AND t.topic_external_link_id='$link_id'" ) ) ;

	return intval( $count ) ;
}


// returns topics count (does not check the permissions)
function getTopicsCount( $forum_id , $link_id )
{
	$db =& Database::getInstance() ;

	list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($this->d3forum_dirname."_topics")." t WHERE t.forum_id=$forum_id AND t.topic_external_link_id='$link_id'" ) ) ;

	return intval( $count ) ;
}


// unhtmlspecialchars (utility)
function unhtmlspecialchars( $text , $quotes = ENT_QUOTES )
{
	return strtr( $text , array_flip( get_html_translation_table( HTML_SPECIALCHARS , $quotes ) ) + array( '&#039;' => "'" ) ) ;
}

}

?>