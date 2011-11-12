<?php

require_once XOOPS_TRUST_PATH.'/modules/d3forum/class/D3commentAbstract.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/d3forum/include/comment_functions.php' ;


// a class for d3forum comment integration
class BulletinD3commentStory extends D3commentAbstract {

function fetchSummary( $external_link_id )
{
	$db =& Database::getInstance() ;
	$myts =& MyTextsanitizer::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $this->mydirname ) ;

	$storyid = intval( $external_link_id ) ;
	$mydirname = $this->mydirname ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$mytrustdirpath = dirname(dirname(__FILE__)) ;
	require_once dirname(dirname(__FILE__)).'/class/bulletin.php' ;

	if( Bulletin::isPublishedExists( $mydirname , $storyid ) ){

		$article = new Bulletin( $mydirname , $storyid ) ;
		$subject4assign = $article->getVar( 'title' ) ;
		$summary = $article->getVar('hometext') ;

		if( function_exists( 'easiestml' ) ) {
			$summary = easiestml( $summary ) ;
		}
		$summary4assign = htmlspecialchars( xoops_substr( $this->unhtmlspecialchars( strip_tags( $summary ) ) , 0 , 255 ) , ENT_QUOTES ) ;

	} else {

		$subject4assign = '' ;
		$summary4assign = '' ;

	}

	return array(
		'dirname' => $mydirname ,
		'module_name' => $module->getVar( 'name' ) ,
		'subject' => $subject4assign ,
		'uri' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=article&amp;storyid='.$storyid ,
		'summary' => $summary4assign ,
	) ;


}


// get id from <{$story.id}>
function external_link_id( $params )
{
	$story = $this->smarty->get_template_vars( 'story' ) ;
	return intval( $story['id'] ) ;
}


// get escaped subject from <{$story.title}>
function getSubjectRaw( $params )
{
	$story = $this->smarty->get_template_vars( 'story' ) ;
	return $this->unhtmlspecialchars( $story['title'] , ENT_QUOTES ) ;
}



}

?>