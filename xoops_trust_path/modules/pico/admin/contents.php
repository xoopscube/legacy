<?php

require_once dirname(dirname(__FILE__)).'/include/main_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/import_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/history_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/pico.textsanitizer.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& PicoTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;


define( 'SPECIAL_CAT_ID_ALL' , -1 ) ;
define( 'SPECIAL_CAT_ID_DELETED' , -2 ) ;


// get exportable modules
$module_handler =& xoops_gethandler( 'module' ) ;
$modules =& $module_handler->getObjects() ;
$exportable_modules = array( '0' => '----' ) ;
foreach( $modules as $module ) {
	$mid = $module->getVar('mid') ;
	$dirname = $module->getVar('dirname') ;
	$dirpath = XOOPS_ROOT_PATH.'/modules/'.$dirname ;
	$mytrustdirname = '' ;
	$tables = $module->getInfo('tables') ;
	if( file_exists( $dirpath.'/mytrustdirname.php' ) ) {
		include $dirpath.'/mytrustdirname.php' ;
	}
	if( $mytrustdirname == 'pico' && $dirname != $mydirname ) {
		// pico
		$exportable_modules[$mid] = $module->getVar('name')." ($dirname)" ;
	}
}

// get $cat_id
$cat_id = intval( @$_GET['cat_id'] ) ;
if( $cat_id == SPECIAL_CAT_ID_ALL ) {
	$cat_title = _MD_PICO_ALLCONTENTS ;
} else if( $cat_id == SPECIAL_CAT_ID_DELETED ) {
	$cat_title = _MD_PICO_DELETEDCONTENTS ;
} else {
	list( $cat_id , $cat_title ) = $db->fetchRow( $db->query( "SELECT cat_id,cat_title FROM ".$db->prefix($mydirname."_categories")." WHERE cat_id=$cat_id" ) ) ;
	if( empty( $cat_id ) ) {
		$cat_id = 0 ;
		$cat_title = _MD_PICO_TOP ;
	}
}


//
// transaction stage
//

// contents update
if( ! empty( $_POST['contents_update'] ) && ! empty( $_POST['weights'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$errors = array() ;
	foreach( $_POST['weights'] as $content_id => $weight ) {
		$content_id = intval( $content_id ) ;
		$weight = intval( $weight ) ;
		$subject4sql = "'".mysql_real_escape_string($myts->stripSlashesGPC( @$_POST['subjects'][$content_id] ))."'" ;
		$vpath4sql = empty( $_POST['vpaths'][$content_id] ) ? 'null' : "'".mysql_real_escape_string($myts->stripSlashesGPC($_POST['vpaths'][$content_id]))."'" ;
		$visible = empty( $_POST['visibles'][$content_id] ) ? 0 : 1 ;
		$show_in_navi = empty( $_POST['show_in_navis'][$content_id] ) ? 0 : 1 ;
		$show_in_menu = empty( $_POST['show_in_menus'][$content_id] ) ? 0 : 1 ;
		$allow_comment = empty( $_POST['allow_comments'][$content_id] ) ? 0 : 1 ;
		$result = $db->query( "UPDATE ".$db->prefix($mydirname."_contents")." SET weight=$weight,subject=$subject4sql,vpath=$vpath4sql,visible=$visible,show_in_navi=$show_in_navi,show_in_menu=$show_in_menu,allow_comment=$allow_comment WHERE content_id=$content_id" ) ;
		if( ! $result ) $errors[] = $content_id ;
	}
	pico_sync_all( $mydirname ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=contents&amp;cat_id=$cat_id" , 3 , $errors ? sprintf( _MD_A_PICO_MSG_FMT_DUPLICATEDVPATH , implode( ',' , $errors ) ) : _MD_PICO_MSG_UPDATED ) ;
	exit ;
}

// contents move
if( ! empty( $_POST['contents_move'] ) && ! empty( $_POST['action_selects'] ) && isset( $_POST['dest_cat_id'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	// cat_id check
	$dest_cat_id = intval( $_POST['dest_cat_id'] ) ;
	if( $dest_cat_id !== 0 ) {
		list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_categories")." WHERE cat_id=$dest_cat_id" ) ) ;
		if( empty( $count ) ) die( _MD_PICO_ERR_READCATEGORY ) ;
	}

	foreach( $_POST['action_selects'] as $content_id => $value ) {
		if( empty( $value ) ) continue ;
		$content_id = intval( $content_id ) ;
		$db->query( "UPDATE ".$db->prefix($mydirname."_contents")." SET cat_id=$dest_cat_id WHERE content_id=$content_id" ) ;
	}
	pico_sync_all( $mydirname ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=contents&amp;cat_id=$cat_id" , 3 , _MD_A_PICO_MSG_CONTENTSMOVED ) ;
	exit ;
}

// contents delete
if( ! empty( $_POST['contents_delete'] ) && ! empty( $_POST['action_selects'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	foreach( $_POST['action_selects'] as $content_id => $value ) {
		if( empty( $value ) ) continue ;
		$content_id = intval( $content_id ) ;
		pico_delete_content( $mydirname , $content_id , true ) ;
		/* $db->query( "DELETE FROM ".$db->prefix($mydirname."_contents")." WHERE content_id=$content_id" ) ;
		$db->query( "DELETE FROM ".$db->prefix($mydirname."_content_votes")." WHERE content_id=$content_id" ) ; */
	}
	pico_sync_all( $mydirname ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=contents&amp;cat_id=$cat_id" , 3 , _MD_A_PICO_MSG_CONTENTSDELETED ) ;
	exit ;
}

// contents export
if( ! empty( $_POST['contents_export'] ) && ! empty( $_POST['action_selects'] ) && ! empty( $_POST['export_mid'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$export_mid = intval( @$_POST['export_mid'] ) ;
	if( empty( $exportable_modules[ $export_mid ] ) ) die( _MD_A_PICO_ERR_INVALIDMID ) ;
	$export_module =& $module_handler->get( $export_mid ) ;

	foreach( $_POST['action_selects'] as $content_id => $value ) {
		if( empty( $value ) ) continue ;
		pico_import_a_content_from_pico( $export_module->getVar('dirname') , $xoopsModule->getVar('mid') , $content_id ) ;
	}

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=contents&amp;cat_id=$cat_id" , 3 , _MD_A_PICO_MSG_CONTENTSEXPORTED ) ;
	exit ;
}


//
// form stage
//

// category options as array
$cat_options = pico_common_get_cat_options( $mydirname ) ;

// fetch contents
if( $cat_id == SPECIAL_CAT_ID_DELETED ) {
	$ors = $db->query( "SELECT oh.*,up.uname AS poster_uname,um.uname AS modifier_uname,c.cat_title,c.cat_depth_in_tree,1 AS is_deleted  FROM ".$db->prefix($mydirname."_content_histories")." oh LEFT JOIN ".$db->prefix("users")." up ON oh.poster_uid=up.uid LEFT JOIN ".$db->prefix("users")." um ON oh.modifier_uid=um.uid LEFT JOIN ".$db->prefix($mydirname."_categories")." c ON oh.cat_id=c.cat_id LEFT JOIN ".$db->prefix($mydirname."_contents")." o ON o.content_id=oh.content_id WHERE o.content_id IS NULL GROUP BY oh.content_id ORDER BY oh.modified_time DESC" ) ;
} else {
	$whr_cat_id = $cat_id == SPECIAL_CAT_ID_ALL ? "1" : "o.cat_id=$cat_id" ;
	$ors = $db->query( "SELECT o.*,up.uname AS poster_uname,um.uname AS modifier_uname,c.cat_title,c.cat_depth_in_tree,0 AS is_deleted  FROM ".$db->prefix($mydirname."_contents")." o LEFT JOIN ".$db->prefix("users")." up ON o.poster_uid=up.uid LEFT JOIN ".$db->prefix("users")." um ON o.modifier_uid=um.uid LEFT JOIN ".$db->prefix($mydirname."_categories")." c ON o.cat_id=c.cat_id WHERE ($whr_cat_id) ORDER BY c.cat_depth_in_tree,o.weight,o.content_id" ) ;
}
$contents4assign = array() ;
while( $content_row = $db->fetchArray( $ors ) ) {
	$wrap_full_path = XOOPS_TRUST_PATH._MD_PICO_WRAPBASE.'/'.$mydirname.str_replace('..','',$content_row['vpath']) ;

	$content4assign = array(
		'id' => intval( $content_row['content_id'] ) ,
		'link' => pico_common_make_content_link4html( $xoopsModuleConfig , $content_row ) ,
		'cat_title' => $myts->makeTboxData4Show( $content_row['cat_title'] , 1 , 1 ) ,
		'created_time_formatted' => formatTimestamp( $content_row['created_time'] , 'm' ) ,
		'modified_time_formatted' => formatTimestamp( $content_row['modified_time'] , 'm' ) ,
		'expiring_time_formatted' => formatTimestamp( @$content_row['expiring_time'] , 'm' ) ,
		'poster_uname' => $content_row['poster_uid'] ? $myts->makeTboxData4Show( $content_row['poster_uname'] ) : _MD_PICO_REGISTERED_AUTOMATICALLY ,
		'modifier_uname' => $content_row['modifier_uid'] ? $myts->makeTboxData4Show( $content_row['modifier_uname'] ) : _MD_PICO_REGISTERED_AUTOMATICALLY ,
		'subject' => $myts->makeTboxData4Edit( $content_row['subject'] ) ,
		'vpath' => htmlspecialchars( $content_row['vpath'] ) ,
		'wrap_file' => is_file( $wrap_full_path ) ? array( 'mtime_formatted' => formatTimestamp( filemtime( $wrap_full_path ) , 'm' ) , 'size' => filesize( $wrap_full_path ) ) : false ,
		'histories' => $content_row['is_deleted'] ? pico_get_content_histories4assign( $mydirname , intval( $content_row['content_id'] ) ) : array() ,
		'ef' => pico_common_unserialize( $content_row['extra_fields'] ) ,
	) ;
	$contents4assign[] = $content4assign + $content_row ;
}


//
// display stage
//

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl = new XoopsTpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'cat_id' => $cat_id ,
	'cat_link' => pico_common_make_category_link4html( $xoopsModuleConfig , $cat_id , $mydirname ) ,
	'cat_title' => htmlspecialchars( $cat_title , ENT_QUOTES ) ,
	'cat_options' => $cat_options + array( SPECIAL_CAT_ID_ALL => _MD_PICO_ALLCONTENTS , SPECIAL_CAT_ID_DELETED => _MD_PICO_DELETEDCONTENTS ) ,
	'cat_options4move' => $cat_options ,
	'module_options' => $exportable_modules ,
	'contents' => $contents4assign ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'pico_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_contents.html' ) ;
xoops_cp_footer();

?>