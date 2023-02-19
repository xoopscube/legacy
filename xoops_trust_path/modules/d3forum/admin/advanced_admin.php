<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

require_once dirname( __DIR__ ) . '/include/main_functions.php';
require_once dirname( __DIR__ ) . '/include/common_functions.php';
require_once dirname( __DIR__ ) . '/include/transact_functions.php';
require_once dirname( __DIR__ ) . '/include/import_functions.php';
require_once dirname( __DIR__ ) . '/class/d3forum.textsanitizer.php';
require_once dirname( __DIR__ ) . '/class/gtickets.php';

$myts = D3forumTextSanitizer::sGetInstance();

$db = XoopsDatabaseFactory::getDatabaseConnection();


$importable_modules = d3forum_import_getimportablemodules( $mydirname );

$module_handler = xoops_gethandler( 'module' );

$modules = $module_handler->getObjects( new Criteria( 'hascomments', 1 ) );

$comment_handler = xoops_gethandler( 'comment' );

$comimportable_modules = [];

foreach ( $modules as $module ) {
	$mid                           = $module->getVar( 'mid' );
	$comment_sum                   = $comment_handler->getCount( new Criteria( 'com_modid', $mid ) );
	$comimportable_modules[ $mid ] = $module->getVar( 'name' ) . " ($comment_sum)";
}


//
// transaction stage
//
if ( ! empty( $_POST['do_synctopics'] ) ) {

	set_time_limit( 0 );

	$synctopics_start = (int) @$_POST['synctopics_start'];

	$synctopics_num = empty( $_POST['synctopics_num'] ) ? 100 : (int) $_POST['synctopics_num'];

	// sync topics
	$trs = $db->query( 'SELECT topic_id FROM ' . $db->prefix( $mydirname . '_topics' ) . " WHERE topic_id>=$synctopics_start AND topic_id<" . ( $synctopics_start + $synctopics_num ) );

	$topic_counter = 0;

	while ( list( $topic_id ) = $db->fetchRow( $trs ) ) {

		$topic_counter ++;

		$topic_id = (int) $topic_id;

		// sync posts from post_votes
		$prs = $db->query( 'SELECT post_id FROM ' . $db->prefix( $mydirname . '_posts' ) . " WHERE topic_id=$topic_id" );

		while ( list( $post_id ) = $db->fetchRow( $prs ) ) {
			d3forum_sync_post_votes( $mydirname, $post_id, false );
		}
		d3forum_sync_topic_votes( $mydirname, $topic_id, false );

		d3forum_sync_topic( $mydirname, $topic_id, false );
	}

	$_SESSION[ $mydirname . '_synctopics_start' ] = $synctopics_start + $synctopics_num;

	$_SESSION[ $mydirname . '_synctopics_num' ] = $synctopics_num;

	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=advanced_admin", 3, sprintf( _MD_A_D3FORUM_FMT_SYNCTOPICSDONE, $topic_counter ) );
	exit;
}


if ( ! empty( $_POST['do_syncforums'] ) ) {

	set_time_limit( 0 );

	// sync all forums
	$result = $db->query( 'SELECT forum_id FROM ' . $db->prefix( $mydirname . '_forums' ) );

	while ( list( $forum_id ) = $db->fetchRow( $result ) ) {
		d3forum_sync_forum( $mydirname, $forum_id, false );
	}

	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=advanced_admin", 3, _MD_A_D3FORUM_MSG_SYNCTABLESDONE );
	exit;
}


if ( ! empty( $_POST['do_synccategories'] ) ) {

	set_time_limit( 0 );

	// rebuild category's tree
	d3forum_sync_cattree( $mydirname );

	// sync all categories
	$result = $db->query( 'SELECT cat_id FROM ' . $db->prefix( $mydirname . '_categories' ) . ' ORDER BY cat_order_in_tree DESC' );

	while ( list( $cat_id ) = $db->fetchRow( $result ) ) {
		d3forum_sync_category( $mydirname, $cat_id );
	}

	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=advanced_admin", 3, _MD_A_D3FORUM_MSG_SYNCTABLESDONE );
	exit;
}


if ( ! empty( $_POST['do_import'] ) && ! empty( $_POST['import_mid'] ) ) {

	set_time_limit( 0 );

	if ( ! $xoopsGTicket->check( true, 'd3forum_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	$import_mid = (int) @$_POST['import_mid'];

	if ( empty( $importable_modules[ $import_mid ] ) ) {
		die( _MD_A_D3FORUM_ERR_INVALIDMID );
	}

	[ $fromtype, ] = explode( ':', $importable_modules[ $import_mid ] );

	switch ( $fromtype ) {
		case 'cbb3' :
			d3forum_import_from_cbb3( $mydirname, $import_mid );
			break;
		case 'newbb1' :
			d3forum_import_from_newbb1( $mydirname, $import_mid );
			break;
		case 'xhnewbb' :
			d3forum_import_from_xhnewbb( $mydirname, $import_mid );
			break;
		case 'd3forum' :
			d3forum_import_from_d3forum( $mydirname, $import_mid );
			break;
	}

	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=advanced_admin", 3, _MD_A_D3FORUM_MSG_IMPORTDONE );
	exit;
}


if ( ! empty( $_POST['do_comimport'] ) && ! empty( $_POST['comimport_mid'] ) && ! empty( $_POST['comimport_forum_id'] ) ) {
	set_time_limit( 0 );

	if ( ! $xoopsGTicket->check( true, 'd3forum_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	$mid = (int) @$_POST['comimport_mid'];

	if ( empty( $comimportable_modules[ $mid ] ) ) {
		die( _MD_A_D3FORUM_ERR_INVALIDMID );
	}

	$forum_id = (int) @$_POST['comimport_forum_id'];

	d3forum_comimport_as_topics( $mydirname, $mid, $forum_id );

	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=advanced_admin", 3, _MD_A_D3FORUM_MSG_COMIMPORTDONE );
	exit;
}


//
// form stage
//
$synctopics_start = (int) @$_SESSION[ $mydirname . '_synctopics_start' ];

$synctopics_num = empty( $_SESSION[ $mydirname . '_synctopics_num' ] ) ? 100 : (int) $_SESSION[ $mydirname . '_synctopics_num' ];

[ $max_topic_id ] = $db->fetchRow( $db->query( 'SELECT MAX(topic_id) FROM ' . $db->prefix( $mydirname . '_topics' ) ) );


// RENDER
xoops_cp_header();
include __DIR__ . '/mymenu.php';
$tpl = new XoopsTpl();
$tpl->assign( [
		'mydirname'              => $mydirname,
		'mod_name'               => $xoopsModule->getVar( 'name' ),
		'mod_url'                => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'           => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'             => $xoopsModuleConfig,
		'max_topic_id'           => $max_topic_id,
		'synctopics_start'       => $synctopics_start,
		'synctopics_num'         => $synctopics_num,
		'import_from_options'    => $importable_modules,
		'comimport_from_options' => $comimportable_modules,
		'comimport_to_options'   => d3forum_make_jumpbox_options( $mydirname, '1', '1', - 1 ),
		'gticket_hidden'         => $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'd3forum_admin' ),
	]
);
$tpl->display( 'db:' . $mydirname . '_admin_advanced_admin.html' );
xoops_cp_footer();
