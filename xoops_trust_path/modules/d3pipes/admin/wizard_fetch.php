<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/admin_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

//
// request stage
//

$allowed_requests = array( 'name' , 'url' , 'rssurl' , 'rssencoding' , 'clip' , 'allowhtml' ) ;
$requests = array() ;
$lacked_requests = array() ;
$error_messages = array() ;
foreach( $allowed_requests as $allowed_request ) {
	if( ! isset( $_POST[ $allowed_request ] ) || $_POST[ $allowed_request ] === '' ) {
		$lacked_requests[ $allowed_request ] = true ;
	} else {
		$requests[ $allowed_request ] = $myts->stripSlashesGPC( $_POST[ $allowed_request ] ) ;
	}
}

//
// form stage
//

// fetch the RSS/Atom
if( ! empty( $requests['rssurl'] ) ) {
	$pipe_row_tmp = array( 'pipe_id' => 0 , 'joints' => array( array( 'joint' => 'fetch' , 'joint_class' => 'snoopy' , 'option' => $requests['rssurl'] ) ) ) ;
	$xml_source = d3pipes_common_fetch_entries( $mydirname , $pipe_row_tmp , 1 , $errors , $xoopsModuleConfig ) ;
	if( preg_match( '/\<\?xml[^>]+\?\>/isU' , $xml_source , $regs ) ) {
		$xml_declaration = $regs[0] ;
	} else {
		$xml_declaration = '' ;
		$error_messages[] = _MD_A_D3PIPES_TH_WIZ_WARN_RSSURL ;
	}
}

// determine the encoding
if( empty( $requests['rssencoding'] ) ) {
	unset( $lacked_requests['rssencoding'] ) ;
	if( ! empty( $xml_declaration ) ) {
		if( preg_match( '/encoding\=([\'\"])?([^\'\"]+)\\1/' , $xml_declaration , $regs ) ) {
			$requests['rssencoding'] = $regs[2] ;
		} else {
			$requests['rssencoding'] = 'UTF-8' ;
		}
	} else {
		$requests['rssencoding'] = '' ;
	}
}

// create form for pipe admin
$post_hiddens = array() ;
if( empty( $lacked_requests ) ) {

	// base (fetch & parse)
	$post_hiddens = array(
		'name' => $requests['name'] ,
		'url' => $requests['url'] ,
		'joint_weights[0]' => 0 ,
		'joint_classes[0]' => 'fetch::' ,
		'joint_option[0]' => $requests['rssurl'] ,
		'joint_weights[2]' => 20 ,
		'joint_classes[2]' => 'parse::' ,
		'joint_option[2]' => '' ,
		'joint_weights[9]' => 90 ,
		'joint_classes[9]' => 'cache::' ,
		'joint_option[9]' => '3600' ,
	) ;

	// xml's encoding to UTF-8 if necessary
	if( strtolower( $requests['rssencoding'] ) != 'utf-8' ) {
		$post_hiddens += array(
			'joint_weights[1]' => 10 ,
			'joint_classes[1]' => 'utf8from::' ,
			'joint_option[1]' => $requests['rssencoding'] ,
		) ;
	}

	// UTF8 to internal_encoding for entries if necessary
	if( strtolower( $xoopsModuleConfig['internal_encoding'] ) != 'utf-8' ) {
		$post_hiddens += array(
			'joint_weights[3]' => 30 ,
			'joint_classes[3]' => 'utf8to::' ,
			'joint_option[3]' => $xoopsModuleConfig['internal_encoding'] ,
		) ;
	}

	// allowhtml -> reassign::contentencoded
	if( $requests['allowhtml'] ) {
		$post_hiddens += array(
			'joint_weights[4]' => 40 ,
			'joint_classes[4]' => 'reassign::allowhtml' ,
		) ;
	}

	// clip or cache
	if( $requests['clip'] ) {
		$post_hiddens += array(
			'joint_weights[5]' => 50 ,
			'joint_classes[5]' => 'clip::' ,
			'joint_option[5]' => '3600' ,
		) ;
	}
}


//
// display stage
//

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl = new D3Tpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'yesno_options' => array( 1 => _YES , 0 => _NO ) ,
	'requests_raw' => $requests ,
	'lacked_requests' => $lacked_requests ,
	'error_messages' => $error_messages ,
	'post_hiddens' => $post_hiddens ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3pipes_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_wizard_fetch.html' ) ;
xoops_cp_footer();

?>