<?php

require_once dirname(__FILE__).'/PicoControllerAbstract.class.php' ;
require_once dirname(__FILE__).'/PicoModelCategory.class.php' ;
require_once dirname(__FILE__).'/PicoModelContent.class.php' ;
require_once dirname(__FILE__).'/gtickets.php' ;
require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/history_functions.php' ;

class PicoControllerInsertContent extends PicoControllerAbstract {

//var $mydirname = '' ;
//var $mytrustdirname = '' ;
//var $assign = array() ;
//var $mod_config = array() ;
//var $uid = 0 ;
//var $currentCategoryObj = null ;
//var $permissions = array() ;
//var $is_need_header_footer = true ;
//var $template_name = '' ;
//var $html_header = '' ;
//var $contentObjs = array() ;

var $contentObj ;

function execute( $request )
{
	// Ticket Check
	if ( ! $GLOBALS['xoopsGTicket']->check( true , 'pico' ) ) {
		redirect_header(XOOPS_URL.'/',3,$GLOBALS['xoopsGTicket']->getErrors());
	}

	parent::execute( $request ) ;

	// initialize
	$cat_data = $this->currentCategoryObj->getData() ;
	$picoPermission =& PicoPermission::getInstance() ;

	// permission check
	if( empty( $cat_data['can_post'] ) ) {
		redirect_header( XOOPS_URL.'/' , 2 , _MD_PICO_ERR_MAKECONTENT ) ;
	}

	// insert a content
	$content_id = pico_makecontent( $this->mydirname , $cat_data['post_auto_approved'] , $cat_data['isadminormod'] ) ;
	$content_uri4html = XOOPS_URL."/modules/$this->mydirname/".pico_common_make_content_link4html( $this->mod_config , $content_id , $this->mydirname ) ;

	// get contentObj
	$this->contentObj = new PicoContent( $this->mydirname , $content_id , $this->currentCategoryObj ) ;
	$content_data = $this->contentObj->getData() ;

	// return uri
	if( ! empty( $_GET['ret'] ) && ( $ret_uri = pico_main_parse_ret2uri( $this->mydirname , $_GET['ret'] ) ) ) {
		$ret_uri4html = htmlspecialchars( $ret_uri , ENT_QUOTES ) ;
	} else {
		$ret_uri4html = $content_uri4html ;
	}

	// calling a delegate
	if( class_exists( 'XCube_DelegateUtils' ) ) {
		XCube_DelegateUtils::raiseEvent( 'ModuleClass.Pico.Contentman.InsertSuccess' , $this->mydirname , $content_id , $cat_data , $ret_uri4html ) ;
	}

	// create extra_tags for notifications
	$extra_tags = array(
		'CONTENT_URL' => pico_common_unhtmlspecialchars( $content_uri4html ) ,
		'APPROVAL_URL' => XOOPS_URL."/modules/$this->mydirname/index.php?page=contentmanager&content_id=$content_id" ,
		'CAT_TITLE' => $cat_data['cat_title'] ,
		'CONTENT_SUBJECT' => $content_data['subject_raw'] ,
	) ;

	// users2notify (can_readfull only)
	$users2notify = $picoPermission->getUidsFromCatid( $this->mydirname , $cat_data['id'] , 'can_readfull' ) ;

	if( $cat_data['post_auto_approved'] ) {
		// Notify for new content 'global'
		pico_main_trigger_event( $this->mydirname , 'global' , 0 , 'newcontent' , $extra_tags , $users2notify , 0 ) ;
		// Notify for new content 'category' of all parental categories
		foreach( array_keys( $cat_data['paths_raw'] ) as $cat_id ) {
			pico_main_trigger_event( $this->mydirname , 'category' , $cat_id , 'newcontent' , $extra_tags , $users2notify , 0 ) ;
		}
		// message "registered"
		redirect_header( $ret_uri4html , 2 , _MD_PICO_MSG_CONTENTMADE ) ;
	} else {
		// Notify for new waiting content (only for admin or mod)
		$users2notify = pico_main_get_moderators( $this->mydirname , $cat_data['id'] ) ;
		if( empty( $users2notify ) ) $users2notify = array( 0 ) ;
		pico_main_trigger_event( $this->mydirname , 'global' , 0 , 'waitingcontent' , $extra_tags , $users2notify ) ;
		// message "waiting approval"
		redirect_header( $ret_uri4html , 2 , _MD_PICO_MSG_CONTENTWAITINGREGISTER ) ;
	}

	// view
	$this->is_need_header_footer = false ;
}

function render()
{
	exit ;
}


}

?>