<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

// variable check (and default)
$smiley         = $smiley ?? 1;
$xcode          = $xcode ?? 1;
$br             = $br ?? 1;
$number_entity  = $number_entity ?? 1; // default on
$special_entity = $special_entity ?? 0; // default off
$solved         = $solved ?? 1;
$pid            = empty( $pid ) ? 0 : (int) $pid;
$post_id        = empty( $post_id ) ? 0 : (int) $post_id;
$topic_id       = empty( $topic_id ) ? 0 : (int) $topic_id;
$forum_id       = empty( $forum_id ) ? 0 : (int) $forum_id;
$formTitle      = empty( $formTitle ) ? '' : $formTitle;
$mode           = ! in_array( @$mode, [ 'newtopic', 'edit', 'reply', 'preview' ] ) ? 'newtopic' : $mode;
$allow_html     = $xoopsModuleConfig['allow_html'];
$html           = isset( $html ) ? (int) $html : 0;

if ( $uid > 0 ) {

	$allow_sig = $xoopsModuleConfig['allow_sig'];

	$attachsig = isset( $attachsig ) ? (int) $attachsig : $xoopsUser->getVar( 'attachsig' );

	// notification (what a buggy functions ... :-x
	if ( ! empty( $xoopsModuleConfig['notification_enabled'] ) && in_array( 'topic-newpost', @$xoopsModuleConfig['notification_events'], true ) ) {
		$allow_notify = true;
		if ( isset( $notify ) ) {
			$notify = (int) $notify;
		} else {
			$notification_handler = xoops_gethandler( 'notification' );
			if ( ! empty( $topic_id ) && $notification_handler->isSubscribed( 'topic', $topic_id, 'newpost', $xoopsModule->getVar( 'mid' ), $uid ) ) {
				$notify = 1;
			} else {
				$notify = 0;
			}
		}
	} else {
		$allow_notify = false;
		$notify       = 0;
	}
} else {
	$allow_sig    = false;
	$attachsig    = 0;
	$allow_notify = false;
	$notify       = 0;
}

// solved changeable?
if ( ! empty( $xoopsModuleConfig['use_solved'] ) && $isadminormod ) {
	$can_change_solved = true;
} else {
	$can_change_solved = false;
}

// form elements or javascript for anti-SPAM
if ( d3forum_common_is_necessary_antispam( $xoopsUser, $xoopsModuleConfig ) ) {
	$antispam_obj    = d3forum_common_get_antispam_object( $xoopsModuleConfig );
	$antispam4assign = $antispam_obj->getHtml4Assign();
} else {
	$antispam4assign = [];
}

// WYSIWYG (some editor needs global scope ... orz)
$d3forum_wysiwygs = [ 'name' => 'message', 'value' => d3forum_common_unhtmlspecialchars( $message4html ) ];

// !Fix Undefined variable by adding global,
// although this isn't a particularly elegant approach
global $d3forum_wysiwyg_body, $d3forum_wysiwyg_header;

include dirname( __DIR__ ) . '/include/wysiwyg_editors.inc.php';

// naao from
if ( is_object( $xoopsUser ) ) {
	if ( 1 === $xoopsModuleConfig['use_name'] && $xoopsUser->getVar( 'name' ) ) {
		$poster_uname4disp = $xoopsUser->getVar( 'name' );
	} else {
		$poster_uname4disp = $xoopsUser->getVar( 'uname' );
	}
}
// naao to


// dare to set 'template_main' after header.php (for disabling cache)
include XOOPS_ROOT_PATH . '/header.php';

$xoopsOption['template_main'] = $mydirname . '_main_post_form.html';

$xoopsTpl->assign( [
		'mydirname'                => $mydirname,
		'mod_url'                  => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'             => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'               => $xoopsModuleConfig,
		'mode'                     => $mode,
		'ispreview'                => (int) @$ispreview,
		'formtitle'                => $formTitle,
		'uid'                      => $uid,
		//'uname' => $uid ? $xoopsUser->getVar('uname') : @$guest_name4html ,
		'uname'                    => $uid ? $poster_uname4disp : @$guest_name4html,
		'guest_name'               => @$guest_name4html,
		'guest_email'              => @$guest_email4html,
		'guest_url'                => @$guest_url4html,
		'guest_pass'               => @$guest_pass4html,
		'subject'                  => @$subject4html,
		'message'                  => @$message4html,
		'reference_quote'          => @$quote4html,
		'reference_subject'        => @$reference_subject4html,
		'reference_message'        => @$reference_message4html,
		'reference_name'           => @$reference_name4html,
		'reference_time'           => @$reference_time,
		'reference_time_formatted' => formatTimestamp( @$reference_time, 'm' ),
		'preview_subject'          => @$preview_subject4html,
		'preview_message'          => @$preview_message4html,
		'icon_options'             => $d3forum_icon_meanings,
		'icon_selected'            => (int) @$icon,
		'pid'                      => $pid,
		'post_id'                  => $post_id,
		'topic_id'                 => $topic_id,
		'forum_id'                 => $forum_id,
		'external_link_id'         => @$external_link_id,
		'can_change_solved'        => $can_change_solved,
		'solved'                   => $solved,
		'solved_checked'           => $solved ? 'checked="checked"' : '',
		'allow_mark'               => @$xoopsModuleConfig['allow_mark'],
		'u2t_marked'               => (int) @$u2t_marked,
		'u2t_marked_checked'       => @$u2t_marked ? 'checked="checked"' : '',
		'allow_hideuid'            => @$xoopsModuleConfig['allow_hideuid'] && $uid,
		'hide_uid'                 => (int) @$hide_uid,
		'hide_uid_checked'         => @$hide_uid ? 'checked="checked"' : '',
		'invisible'                => (int) @$invisible,
		'invisible_checked'        => @$invisible ? 'checked="checked"' : '',
		'approval'                 => (int) @$approval,
		'approval_checked'         => @$approval ? 'checked="checked"' : '',
		'smiley'                   => $smiley,
		'smiley_checked'           => $smiley ? 'checked="checked"' : '',
		'xcode'                    => $xcode,
		'xcode_checked'            => $xcode ? 'checked="checked"' : '',
		'br'                       => $br,
		'br_checked'               => $br ? 'checked="checked"' : '',
		'number_entity'            => $number_entity,
		'number_entity_checked'    => $number_entity ? 'checked="checked"' : '',
		'special_entity'           => $special_entity,
		'special_entity_checked'   => $special_entity ? 'checked="checked"' : '',
		'allow_sig'                => $allow_sig,
		'attachsig'                => $attachsig,
		'attachsig_checked'        => $attachsig ? 'checked="checked"' : '',
		'allow_notify'             => $allow_notify,
		'notify'                   => $notify,
		'notify_checked'           => $notify ? 'checked="checked"' : '',
		'allow_html'               => $allow_html,
		'html'                     => $html,
		'html_checked'             => $html ? 'checked="checked"' : '',
		'category'                 => $category4assign,
		'forum'                    => $forum4assign,
		'topic'                    => @$topic4assign,
		'post'                     => 'edit' === $mode ? @$post4assign : [],
		'body_wysiwyg'             => $d3forum_wysiwyg_body,
		'antispam'                 => $antispam4assign,
		'xoops_module_header'      => '<link rel="stylesheet" type="text/css" media="all" href="' . str_replace( '{mod_url}', XOOPS_URL . '/modules/' . $mydirname, $xoopsModuleConfig['css_uri'] ) . '">' . $xoopsTpl->get_template_vars( 'xoops_module_header' ) . "\n" . $d3forum_wysiwyg_header,
		'xoops_pagetitle'          => $formTitle,
		'xoops_breadcrumbs'        => array_merge( $xoops_breadcrumbs, [ [ 'name' => $formTitle ] ] ),
	]
);

include XOOPS_ROOT_PATH . '/footer.php';
