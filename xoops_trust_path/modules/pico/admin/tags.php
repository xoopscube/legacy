<?php

require_once dirname(dirname(__FILE__)).'/include/main_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/import_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/history_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/pico.textsanitizer.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
require_once XOOPS_ROOT_PATH.'/class/pagenav.php' ;
$myts =& PicoTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

$allowed_orders = array( 'count ASC' , 'count DESC' , 'weight ASC' , 'weight DESC' , 'label ASC' , 'label DESC' ) ;

//
// transaction stage
//

// tags update
if( ! empty( $_POST['tags_update'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	foreach( array_keys( $_POST['labels'] ) as $old_label ) {
		if( empty( $_POST['labels'][ $old_label ] ) ) continue ;
		$new_label = $myts->stripSlashesGPC( $_POST['labels'][ $old_label ] ) ;
		$weight = intval( $_POST['weights'][ $old_label ] ) ;
		$db->query( "UPDATE ".$db->prefix($mydirname."_tags")." SET label='".mysql_real_escape_string($new_label)."',weight='$weight' WHERE label='".mysql_real_escape_string($old_label)."'" ) ;

		if( $new_label != $old_label ) {
			// update tags field in contents table
			$result = $db->query( "SELECT content_id,tags FROM ".$db->prefix($mydirname."_contents WHERE tags LIKE '%".mysql_real_escape_string($old_label)."%'") ) ;
			while( list( $content_id , $tags ) = $db->fetchRow( $result ) ) {
				$tags_array = array_flip( explode( ' ' , $tags ) ) ;
				if( isset( $tags_array[ $old_label ] ) ) {
					$tags_array[ $new_label ] = $tags_array[ $old_label ] ;
					unset( $tags_array[ $old_label ] ) ;
					$new_tags = implode( ' ' , array_flip( $tags_array ) ) ;
					$db->query( "UPDATE ".$db->prefix($mydirname."_contents")." SET tags='".mysql_real_escape_string($new_tags)."' WHERE content_id=$content_id" ) ;
				}
			}
		}
	}
	pico_sync_tags( $mydirname ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=tags" , 3 , _MD_PICO_MSG_UPDATED ) ;
	exit ;
}


// tags delete
if( ! empty( $_POST['tags_delete'] ) && ! empty( $_POST['action_selects'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	foreach( $_POST['action_selects'] as $label => $value ) {
		if( empty( $value ) ) continue ;
		$label = $myts->stripSlashesGPC( $label ) ;
		$db->query( "DELETE FROM ".$db->prefix($mydirname."_tags")." WHERE label='".mysql_real_escape_string($label)."'" ) ;

		// update tags field in contents table
		$result = $db->query( "SELECT content_id,tags FROM ".$db->prefix($mydirname."_contents WHERE tags LIKE '%".mysql_real_escape_string($label)."%'") ) ;
		while( list( $content_id , $tags ) = $db->fetchRow( $result ) ) {
			$tags_array = array_flip( explode( ' ' , $tags ) ) ;
			if( isset( $tags_array[ $label ] ) ) {
				unset( $tags_array[ $label ] ) ;
				$new_tags = implode( ' ' , array_flip( $tags_array ) ) ;
				$db->query( "UPDATE ".$db->prefix($mydirname."_contents")." SET tags='".mysql_real_escape_string($new_tags)."' WHERE content_id=$content_id" ) ;
			}
		}
	}
	pico_sync_tags( $mydirname ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=tags" , 3 , _MD_A_PICO_MSG_DELETED ) ;
	exit ;
}


//
// form stage
//

// requests for form
$pos = empty( $_GET['pos'] ) ? 0 : intval( $_GET['pos'] ) ;
$num = empty( $_GET['num'] ) ? 30 : intval( $_GET['num'] ) ;
$order = in_array( @$_GET['order'] , $allowed_orders ) ? $_GET['order'] : $allowed_orders[0] ;

// pre query
list( $hit ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_tags") ) ) ;

// pagenav
$pagenav = '' ;
$pagenav_obj = new XoopsPageNav( $hit , $num , $pos , 'pos', "page=tags&amp;num=$num&amp;order=".urlencode($order) ) ;
$pagenav = $pagenav_obj->renderNav() ;

// main query
$trs = $db->query( "SELECT * FROM ".$db->prefix($mydirname."_tags")." ORDER BY $order LIMIT $pos,$num" ) ;

$tags4assign = array() ;
while( $tag_row = $db->fetchArray( $trs ) ) {
	// get contents
	$contents4assign = array() ;
	$ors = $db->query( "SELECT content_id,vpath,subject FROM ".$db->prefix($mydirname."_contents")." WHERE content_id IN (".$tag_row['content_ids'].") LIMIT 10" ) ;
	while( $content_row = $db->fetchArray( $ors ) ) {
		$contents4assign[] = array(
			'id' => intval( $content_row['content_id'] ) ,
			'link' => pico_common_make_content_link4html( $xoopsModuleConfig , $content_row ) ,
			'subject' => $myts->makeTboxData4Show( $content_row['subject'] , 1 , 1 ) ,
		) + $content_row ;
	}

	$tag4assign = array(
		'label_raw' => $tag_row['label'] ,
		'label' => htmlspecialchars( $tag_row['label'] , ENT_QUOTES ) ,
		'contents' => $contents4assign ,
	) ;
	$tags4assign[] = $tag4assign + $tag_row ;
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
	'tags' => $tags4assign ,
	'num' => $num ,
	'order' => $order ,
	'allowed_orders' => $allowed_orders ,
	'pagenav' => $pagenav ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'pico_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_tags.html' ) ;
xoops_cp_footer();

?>