<?php

require_once dirname(__FILE__).'/PicoControllerAbstract.class.php' ;
require_once dirname(__FILE__).'/PicoModelCategory.class.php' ;
require_once dirname(__FILE__).'/PicoModelContent.class.php' ;

class PicoControllerVoteContent extends PicoControllerAbstract {

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
	parent::execute( $request ) ;

	$this->contentObj = new PicoContent( $this->mydirname , $request['content_id'] , $this->currentCategoryObj ) ;

	// check existence
	if( $this->contentObj->isError() ) {
		redirect_header( XOOPS_URL."/modules/$this->mydirname/index.php" , 2 , _MD_PICO_ERR_READCONTENT ) ;
		exit ;
	}

	$content_data = $this->contentObj->getData() ;

	// permission check
	if( empty( $content_data['can_read'] ) ) {
		if( $this->uid > 0 ) {
			redirect_header( XOOPS_URL.'/' , 2 , _MD_PICO_ERR_PERMREADFULL ) ;
		} else {
			redirect_header( XOOPS_URL.'/user.php' , 2 , _MD_PICO_ERR_LOGINTOREADFULL ) ;
		}
		exit ;
	}

	// check if "use_vote" is on
	if( empty( $this->mod_config['use_vote'] ) ) {
		redirect_header( XOOPS_URL."/modules/$mydirname/".pico_common_make_content_link4html( $this->config , $content4assign ) , 0 , _MD_PICO_MSG_VOTEDISABLED ) ;	exit ;
	}

	// special check for vote_to_post
	if( ! $this->uid && empty( $this->mod_config['guest_vote_interval'] ) ) {
		redirect_header( XOOPS_URL."/modules/$mydirname/".pico_common_make_content_link4html( $this->config , $content4assign ) , 0 , _MD_PICO_ERR_VOTEPERM ) ;
		exit ;
	}

	// get POINT and validation
	$point = intval( @$_GET['point'] ) ;
	if( $point < 0 || $point > 10 ) die( _MD_PICO_ERR_VOTEINVALID.__LINE__ ) ;

	$this->contentObj->vote( $this->uid , @$_SERVER['REMOTE_ADDR'] , $point ) ;

	// view
	$this->is_need_header_footer = false ;
}

function render()
{
	redirect_header( XOOPS_URL."/modules/$this->mydirname/".pico_common_make_content_link4html( $this->mod_config , $this->contentObj->getData() ) , 0 , _MD_PICO_MSG_VOTEACCEPTED ) ;
	exit ;
}


}

?>