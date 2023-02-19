<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

include dirname( __DIR__ ) . '/include/common_prepend.php';

require_once dirname( __DIR__ ) . '/class/gtickets.php';

$topic_id = (int) @$_GET['topic_id'];

// get&check this topic ($topic4assign, $topic_row, $forum_id), count topic_view up, get $prev_topic, $next_topic
include dirname( __DIR__ ) . '/include/process_this_topic.inc.php';

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if ( ! include dirname( __DIR__ ) . '/include/process_this_forum.inc.php' ) {
	die( _MD_D3FORUM_ERR_READFORUM );
}

// get&check this category ($category4assign, $category_row), override options
if ( ! include dirname( __DIR__ ) . '/include/process_this_category.inc.php' ) {
	die( _MD_D3FORUM_ERR_READCATEGORY );
}

// special permission check for topicmanager
if ( ! $isadminormod ) {
	die( _MD_D3FORUM_ERR_MODERATEFORUM );
}


// get all of d3forum module instances
$module_handler = xoops_gethandler( 'module' );

$modules = $module_handler->getObjects();

$exportable_modules = [ 0 => '----' ];

foreach ( $modules as $module ) {

	$mid            = $module->getVar( 'mid' );
	$dirname        = $module->getVar( 'dirname' );
	$dirpath        = XOOPS_ROOT_PATH . '/modules/' . $dirname;
	$mytrustdirname = '';

	if ( file_exists( $dirpath . '/mytrustdirname.php' ) ) {
		include $dirpath . '/mytrustdirname.php';
	}

	if ( 'd3forum' === $mytrustdirname && $dirname !== $mydirname ) {
		// d3forum
		$exportable_modules[ $mid ]       = 'd3forum:' . $module->getVar( 'name' ) . "($dirname)";
		$dist_forum_permissions           = d3forum_get_forum_permissions_of_current_user( $dirname );
		$exportable_module_forums[ $mid ] = d3forum_make_jumpbox_options( $dirname, '1', 'f.`forum_id` IN (' . implode( ',', array_keys( $dist_forum_permissions ) ) . ')', 0 );
	}
}


// TRANSACTION PART
require_once dirname( __DIR__ ) . '/include/transact_functions.php';

if ( ! empty( $_POST['topicman_post'] ) ) {

	if ( ! $xoopsGTicket->check( true, 'd3forum' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	d3forum_update_topic_from_post( $mydirname, $topic_id, $forum_id, $forum_permissions, $isadmin );

	redirect_header( XOOPS_URL . "/modules/$mydirname/index.php?topic_id=$topic_id", 2, _MD_D3FORUM_TOPICMANAGERDONE );
	exit;
}

if ( ! empty( $_POST['topicman_sync'] ) ) {

	if ( ! $xoopsGTicket->check( true, 'd3forum' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	// clear unique_paths for rebuilding them.
	$prs = $db->query( 'UPDATE ' . $db->prefix( $mydirname . '_posts' ) . " SET unique_path='' WHERE topic_id=$topic_id" );

	// sync posts from post_votes
	$prs = $db->query( 'SELECT post_id FROM ' . $db->prefix( $mydirname . '_posts' ) . " WHERE topic_id=$topic_id" );

	while ( list( $post_id ) = $db->fetchRow( $prs ) ) {
		d3forum_sync_post_votes( $mydirname, $post_id, false );
	}

	d3forum_sync_topic_votes( $mydirname, $topic_id, false );

	d3forum_sync_topic( $mydirname, $topic_id, false );

	redirect_header( XOOPS_URL . "/modules/$mydirname/index.php?topic_id=$topic_id", 2, _MD_D3FORUM_TOPICMANAGERDONE );
	exit;
}

if ( ! empty( $_POST['topicman_export_copy'] ) || ! empty( $_POST['topicman_export_move'] ) ) {

	require_once dirname( __DIR__ ) . '/include/import_functions.php';

	if ( ! $xoopsGTicket->check( true, 'd3forum' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	$export_mid = (int) @$_POST['export_mid'];

	$export_forum_id = (int) @$_POST['export_forum_id'][ $export_mid ];

	if ( ! empty( $exportable_modules[ $export_mid ] ) && $export_forum_id > 0 ) {

		d3forum_export_topic_to_d3forum( $mydirname, $export_mid, $export_forum_id, $forum_id, $topic_id, ! empty( $_POST['topicman_export_move'] ) );

		redirect_header( XOOPS_URL . "/modules/$mydirname/index.php?forum_id=$forum_id", 2, _MD_D3FORUM_TOPICMANAGERDONE );
		exit;
	}
}


// get target forums
$jump_box_forums = [];

foreach ( $forum_permissions as $forum_id => $perms ) {
	if ( $perms['is_moderator'] ) {
		$jump_box_forums[] = $forum_id;
	}
}

$whr4forum_jump_box = empty( $jump_box_forums ) ? '0' : 'f.forum_id IN (' . implode( ',', $jump_box_forums ) . ')';


// FORM PART

$xoopsOption['template_main'] = $mydirname . '_main_topicmanager.html';

include XOOPS_ROOT_PATH . '/header.php';

// make edit data (special patch)
$topic4assign['title4edit'] = htmlspecialchars( $topic_row['topic_title'], ENT_QUOTES );

$xoopsTpl->assign( [
		'mydirname'                => $mydirname,
		'mod_url'                  => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'             => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'               => $xoopsModuleConfig,
		'category'                 => $category4assign,
		'forum'                    => $forum4assign,
		'topic'                    => $topic4assign,
		'forum_jumpbox_options'    => d3forum_make_jumpbox_options( $mydirname, '1', $isadmin ? '1' : $whr4forum_jump_box, $topic_row['forum_id'] ),
		'export_to_module_options' => $exportable_modules,
		'export_to_forum_options'  => $exportable_module_forums,
		'gticket_hidden'           => $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'd3forum' ),
		'xoops_module_header'      => '<link rel="stylesheet" type="text/css" media="all" href="' . str_replace( '{mod_url}', XOOPS_URL . '/modules/' . $mydirname, $xoopsModuleConfig['css_uri'] ) . '">' . $xoopsTpl->get_template_vars( 'xoops_module_header' ),
		'xoops_pagetitle'          => _MD_D3FORUM_TOPICMANAGER,
		'xoops_breadcrumbs'        => array_merge( $xoops_breadcrumbs, [ [ 'name' => _MD_D3FORUM_TOPICMANAGER ] ] ),
	]
);

include XOOPS_ROOT_PATH . '/footer.php';
