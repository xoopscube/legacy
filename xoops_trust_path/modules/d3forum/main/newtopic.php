<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

include dirname( __DIR__ ) . '/include/common_prepend.php';

$forum_id = (int) @$_GET['forum_id'];

$external_link_id = @$_GET['external_link_id'];

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if ( ! include dirname( __DIR__ ) . '/include/process_this_forum.inc.php' ) {
	die( _MD_D3FORUM_ERR_READFORUM );
}

// get&check this category ($category4assign, $category_row), override options
if ( ! include dirname( __DIR__ ) . '/include/process_this_category.inc.php' ) {
	die( _MD_D3FORUM_ERR_READCATEGORY );
}

// check post permission
if ( empty( $can_post ) ) {
	die( _MD_D3FORUM_ERR_POSTFORUM );
}
if ( ! empty( $forum_row['forum_external_link_format'] ) && empty( $external_link_id ) ) {
	die( _MD_D3FORUM_ERR_FORUMASCOMMENT );
}

// get external ID and validate it
if ( $external_link_id ) {

	$d3com = d3forum_main_get_comment_object( $mydirname, $forum_row['forum_external_link_format'], $forum_id );

	if ( false === ( $external_link_id = $d3com->validate_id( $external_link_id ) ) ) {
		die( _MD_D3FORUM_ERR_INVALIDEXTERNALLINKID );
	}
}

// specific variables for newtopic
$pid          = 0;
$post_id      = 0;
$subject4html = htmlspecialchars( $myts->stripslashesGPC( @$_GET['subject'] ), ENT_QUOTES );
$message4html = '';
$topic_id     = 0;
$invisible    = 0;
$approval     = 1;

$post_default_options = array_map( 'trim', explode( ',', strtolower( @$xoopsModuleConfig['default_options'] ) ) );

foreach (
	[
		'smiley',
		'xcode',
		'br',
		'number_entity',
		'special_entity',
		'html',
		'attachsig',
		'hide_uid',
		'notify',
		'u2t_marked'
	] as $key
) {
	$$key = in_array( $key, $post_default_options ) ? 1 : 0;
}

if ( is_object( @$GLOBALS['xoopsUser'] ) ) {
	$attachsig |= $GLOBALS['xoopsUser']->getVar( 'attachsig' );
}

$formTitle = $external_link_id ? _MD_D3FORUM_POSTASCOMMENTTOP : _MD_D3FORUM_POSTASNEWTOPIC;

$mode = 'newtopic';

include dirname( __DIR__ ) . '/include/display_post_form.inc.php';
