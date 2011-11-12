<?php

$GLOBALS['pico_tables'] = array(
	'category_permissions' => array(
		'cat_id' ,
		'uid' ,
		'groupid' ,
		'permissions' ,
	) ,
	'categories' => array(
		'cat_id' ,
		'cat_permission_id' ,
		'cat_vpath' ,
		'pid' ,
		'cat_title' ,
		'cat_desc' ,
		'cat_depth_in_tree' ,
		'cat_order_in_tree' ,
		'cat_path_in_tree' ,
		'cat_unique_path' ,
		'cat_weight' ,
		'cat_options' ,
		'cat_created_time' ,
		'cat_modified_time' ,
		'cat_vpath_mtime' ,
		'cat_redundants' ,
	) ,
	'contents' => array(
		'content_id' ,
		'permission_id' ,
		'vpath' ,
		'cat_id' ,
		'weight' ,
		'created_time' ,
		'modified_time' ,
		'expiring_time' ,
		'last_cached_time' ,
		'poster_uid' ,
		'poster_ip' ,
		'modifier_uid' ,
		'modifier_ip' ,
		'subject' ,
		'subject_waiting' ,
		'locked' ,
		'visible' ,
		'approval' ,
		'use_cache' ,
		'allow_comment' ,
		'show_in_navi' ,
		'show_in_menu' ,
		'viewed' ,
		'votes_sum' ,
		'votes_count' ,
		'comments_count' ,
		'htmlheader' ,
		'htmlheader_waiting' ,
		'body' ,
		'body_waiting' ,
		'body_cached' ,
		'filters' ,
		'tags' ,
		'extra_fields' ,
		'redundants' ,
		'for_search' ,
	) ,
	'content_votes' => array(
		'vote_id' ,
		'content_id' ,
		'uid' ,
		'vote_point' ,
		'vote_time' ,
		'vote_ip' ,
	) ,
	'content_histories' => array(
		'content_history_id' ,
		'content_id' ,
		'vpath' ,
		'cat_id' ,
		'created_time' ,
		'modified_time' ,
		'poster_uid' ,
		'poster_ip' ,
		'modifier_uid' ,
		'modifier_ip' ,
		'subject' ,
		'htmlheader' ,
		'body' ,
		'filters' ,
	) ,
	'content_extras' => array(
		'content_extra_id' ,
		'content_id' ,
		'extra_type' ,
		'created_time' ,
		'modified_time' ,
		'data' ,
	) ,
	'tags' => array(
		'label' ,
		'weight' ,
		'count' ,
		'content_ids' ,
		'created_time' ,
		'modified_time' ,
	) ,
) ;


function pico_import_errordie()
{
	$db =& Database::getInstance() ;

	echo _MD_A_PICO_ERR_SQLONIMPORT ;
	echo $db->logger->dumpQueries() ;
	exit ;
}



function pico_import_from_smartsection( $mydirname , $import_mid )
{
	$db =& Database::getInstance() ;
	$import_mid = intval( $import_mid ) ;

	// get name of `contents` table 
	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->get( $import_mid ) ;
	$from_tables = $module->getInfo('tables') ;
	if( sizeof( $from_tables ) != 5 ) pico_import_errordie() ;
	$target_dirname = $module->getVar('dirname') ;

	// categories
	$to_table = $db->prefix( $mydirname.'_categories' ) ;
	$from_table = $db->prefix( $from_tables[0] ) ;
	$db->query( "DELETE FROM `$to_table`" ) ;
	$db->query( "INSERT INTO `$to_table` (cat_id,pid,cat_title) VALUES (0,0xffff,'TOP')" ) ;
	$irs = $db->query( "INSERT INTO `$to_table` (cat_id,pid,cat_title,cat_desc,cat_weight,cat_created_time,cat_modified_time) SELECT categoryid,parentid,name,description,weight,created,created FROM `$from_table`" ) ;
	if( ! $irs ) pico_import_errordie() ;

	// category_permissions
	$to_table = $db->prefix( $mydirname.'_category_permissions' ) ;
	$from_table = $db->prefix( 'group_permission' ) ;
	$db->query( "DELETE FROM `$to_table` WHERE cat_id>0" ) ;
	$rs = $db->query( "SELECT * FROM `$from_table` WHERE gperm_modid=$import_mid AND gperm_name='category_read'" ) ;
	while( $row = $db->fetchArray( $rs ) ) {
		$permissions4sql = addslashes( serialize(array('can_read'=>1,'can_readfull'=>1,'can_post'=>0,'can_edit'=>0,'can_delete'=>0,'post_auto_approved'=>0,'is_moderator'=>0,'can_makesubcategory'=>0)) ) ;
		$db->query( "INSERT INTO `$to_table` SET cat_id={$row['gperm_itemid']},uid=NULL,groupid={$row['gperm_groupid']},permissions='$permissions4sql'" ) ;
	}
	// groupid=1
	$permissions4sql = addslashes( serialize(array('can_read'=>1,'can_readfull'=>1,'can_post'=>1,'can_edit'=>1,'can_delete'=>1,'post_auto_approved'=>1,'is_moderator'=>1,'can_makesubcategory'=>1)) ) ;
	$db->query( "INSERT INTO `$to_table` (cat_id,uid,groupid,permissions) SELECT categoryid,NULL,1,'$permissions4sql' FROM ".$db->prefix( $from_tables[0] ) ) ;

	// content_votes (delete all)
	$db->query( "DELETE FROM ".$db->prefix($mydirname."_content_votes") ) ;

	// contents (temporary body_waiting,body_cached for reconstruct filters)
	$to_table = $db->prefix( $mydirname.'_contents' ) ;
	$from_table = $db->prefix( $from_tables[1] ) ;
	$db->query( "DELETE FROM `$to_table`" ) ;
	$irs = $db->query( "INSERT INTO `$to_table` (content_id,cat_id,weight,created_time,modified_time,subject,visible,approval,allow_comment,show_in_navi,show_in_menu,htmlheader,body,poster_uid,modifier_uid,viewed,/*1*/body_waiting,body_cached,subject_waiting,htmlheader_waiting) SELECT itemid,categoryid,weight,datesub,datesub,title,status=2,status<>1,cancomment,1,1,'',CONCAT(summary,body),uid,uid,counter,/*1*/dohtml,dosmiley,doxcode,dobr FROM `$from_table`" ) ;
	if( ! $irs ) pico_import_errordie() ;

	// update filters for DB contents
	$db->query( "UPDATE `$to_table` SET filters=CONCAT(filters,'|htmlspecialchars') WHERE ! body_waiting" ) ;
	$db->query( "UPDATE `$to_table` SET filters=CONCAT(filters,'|xcode') WHERE subject_waiting" ) ;
	$db->query( "UPDATE `$to_table` SET filters=CONCAT(filters,'|smiley') WHERE body_cached" ) ;
	$db->query( "UPDATE `$to_table` SET filters=CONCAT(filters,'|nl2br') WHERE htmlheader_waiting" ) ;
	$db->query( "UPDATE `$to_table` SET body_waiting='',body_cached='',subject_waiting='',htmlheader_waiting=''" ) ;
}

function pico_import_from_tinyd( $mydirname , $import_mid )
{
	$db =& Database::getInstance() ;

	// get name of `contents` table 
	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->get( $import_mid ) ;
	list( $from_table_base ) = $module->getInfo('tables') ;
	if( empty( $from_table_base ) ) pico_import_errordie() ;
	$target_dirname = $module->getVar('dirname') ;

	// categories
	// skip all

	// category_permissions
	// skip all

	// content_votes (delete all)
	$db->query( "DELETE FROM ".$db->prefix($mydirname."_content_votes") ) ;

	// contents (temporary body_waiting,body_cached for reconstruct filters)
	$to_table = $db->prefix( $mydirname.'_contents' ) ;
	$from_table = $db->prefix( $from_table_base ) ;
	$db->query( "DELETE FROM `$to_table`" ) ;
	$irs = $db->query( "INSERT INTO `$to_table` (content_id,cat_id,weight,created_time,modified_time,subject,visible,allow_comment,show_in_navi,show_in_menu,htmlheader,body,filters,body_waiting,body_cached,redundants,htmlheader_waiting) SELECT storyid,0,blockid,UNIX_TIMESTAMP(created),UNIX_TIMESTAMP(last_modified),title,visible,!nocomments,1,submenu,html_header,`text`,nohtml,nosmiley,nobreaks,address,'' FROM `$from_table`" ) ;
	if( ! $irs ) pico_import_errordie() ;

	// update filters for DB contents
	$db->query( "UPDATE `$to_table` SET filters='textwiki' WHERE filters='16'" ) ;
	$db->query( "UPDATE `$to_table` SET filters='textwiki|xcode' WHERE filters='18'" ) ;
	$db->query( "UPDATE `$to_table` SET filters='eval' WHERE filters='8'" ) ;
	$db->query( "UPDATE `$to_table` SET filters='eval|xcode' WHERE filters='10'" ) ;
	$db->query( "UPDATE `$to_table` SET filters='htmlspecialchars' WHERE filters='3'" ) ;
	$db->query( "UPDATE `$to_table` SET filters='' WHERE filters='2'" ) ;
	$db->query( "UPDATE `$to_table` SET filters='htmlspecialchars|xcode' WHERE filters='1'" ) ;
	$db->query( "UPDATE `$to_table` SET filters='xcode' WHERE filters='0'" ) ;
	$db->query( "UPDATE `$to_table` SET filters=CONCAT(filters,'|smiley') WHERE body_waiting='0'" ) ;
	$db->query( "UPDATE `$to_table` SET filters=CONCAT(filters,'|nl2br') WHERE body_cached='0'" ) ;
	$db->query( "UPDATE `$to_table` SET body_waiting='',body_cached=''" ) ;

	// update fileters for WRAP contents (using redundants as a temporary)
	$db->query( "UPDATE `$to_table` SET redundants='' WHERE redundants=NULL" ) ;
	$db->query( "UPDATE `$to_table` SET filters='wraps',vpath=CONCAT('/',redundants) WHERE LENGTH(redundants)>0" ) ;
	$db->query( "UPDATE `$to_table` SET redundants=NULL" ) ;

}

function pico_import_from_pico( $mydirname , $import_mid )
{
	$db =& Database::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$from_module =& $module_handler->get( $import_mid ) ;

	foreach( $GLOBALS['pico_tables'] as $table_name => $columns ) {
		$to_table = $db->prefix( $mydirname.'_'.$table_name ) ;
		$from_table = $db->prefix( $from_module->getVar('dirname').'_'.$table_name ) ;
		$columns4sql = implode( ',' , $columns ) ;
		$db->query( "DELETE FROM `$to_table`" ) ;
		$irs = $db->query( "INSERT INTO `$to_table` ($columns4sql) SELECT $columns4sql FROM `$from_table`" ) ;
		if( ! $irs ) pico_import_errordie() ;
	}
}


// just import a content (contents and content_votes only)
function pico_import_a_content_from_pico( $mydirname , $import_mid , $content_id )
{
	$db =& Database::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$from_module =& $module_handler->get( $import_mid ) ;

	// contents table
	$to_table = $db->prefix( $mydirname.'_contents' ) ;
	$from_table = $db->prefix( $from_module->getVar('dirname').'_contents' ) ;
	$columns4sql = implode( ',' , array_diff( $GLOBALS['pico_tables']['contents'] , array( 'content_id' , 'cat_id') ) ) ;
	$irs = $db->query( "INSERT INTO `$to_table` ($columns4sql,cat_id) SELECT $columns4sql,0 FROM `$from_table` WHERE content_id=".intval($content_id) ) ;
	if( ! $irs ) pico_import_errordie() ;

	// content_votes table
	$new_content_id = $db->getInsertId() ;
	$to_table = $db->prefix( $mydirname.'_content_votes' ) ;
	$from_table = $db->prefix( $from_module->getVar('dirname').'_content_votes' ) ;
	$columns4sql = implode( ',' , array_diff( $GLOBALS['pico_tables']['content_votes'] , array( 'vote_id' , 'content_id' ) ) ) ;
	$irs = $db->query( "INSERT INTO `$to_table` ($columns4sql,content_id) SELECT $columns4sql,$new_content_id FROM `$from_table` WHERE content_id=".intval($content_id) ) ;
	if( ! $irs ) pico_import_errordie() ;
}


?>