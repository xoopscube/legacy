<?php

require_once dirname(__FILE__).'/PicoControllerAbstract.class.php' ;
require_once dirname(__FILE__).'/PicoControllerEditContent.class.php' ;
require_once dirname(__FILE__).'/PicoModelCategory.class.php' ;
require_once dirname(__FILE__).'/PicoModelContent.class.php' ;
require_once dirname(__FILE__).'/gtickets.php' ;
require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/history_functions.php' ;

class PicoControllerPreviewContent extends PicoControllerEditContent {

//var $mydirname = '' ;
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

function processPreview( $request )
{
	// Ticket Check
	if ( ! $GLOBALS['xoopsGTicket']->check( true , 'pico' ) ) {
		redirect_header(XOOPS_URL.'/',3,$GLOBALS['xoopsGTicket']->getErrors());
	}

	// initialize
	$cat_data = $this->currentCategoryObj->getData() ;
	$myts =& PicoTextSanitizer::getInstance() ;

	// assigning other than preview/request
	// parent::execute( $request ) ;
	// permission check (can_edit) done

	// request
	$request = pico_get_requests4content( $this->mydirname , $errors = array() , $cat_data['post_auto_approved'] , $cat_data['isadminormod'] , $this->assign['content']['id'] ) ;
	$request['body_raw'] = $request['body'] ;
	$request['subject_raw'] = $request['subject'] ;
	$request4assign = array_map( 'htmlspecialchars_ent' , $request ) ;
	$this->assign['request'] = $request4assign ;

	// override content data for edit
	$this->assign['content'] = $request4assign + $this->assign['content'] ;
	$this->assign['content']['filter_infos'] = pico_main_get_filter_infos( $request['filters'] , $cat_data['isadminormod'] ) ;
	$this->assign['content']['body_raw'] = $request['body'] ;
	$this->assign['content']['extra_fields'] = $request['extra_fields'] ;
	$this->assign['content']['ef'] = pico_common_unserialize( $request['extra_fields'] ) ;

	// temporary $contentObj
	$tmpContentObj = new PicoContent( $this->mydirname , 0 , $this->currentCategoryObj , true ) ;

	// preview
	$this->assign['preview'] = array(
		'errors' => $errors ,
		'htmlheader' => $request['htmlheader'] , // remove it?
		'subject' => $myts->makeTboxData4Show( $request['subject'] , 1 , 1 ) ,
		'body' => $tmpContentObj->filterBody( $this->assign['content'] ) ,
	) ;
}


}

?>