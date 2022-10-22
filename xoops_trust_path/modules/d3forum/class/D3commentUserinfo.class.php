<?php
/**
 * D3Forum module for XCL
 * Sample class for d3forum comment integration
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */


class D3commentUserinfo extends D3commentAbstract {
	// get reference description as string
	public function fetchDescription( $link_id ) {
		$user_handler =& xoops_gethandler( 'user' );
		$user         =& $user_handler->get( $link_id );
		if ( is_object( $user ) ) {
			return '
			<table class="outer">
				<tr>
					<td style="width:20%;" class="head">' . _MD_D3FORUM_LINK_COMMENTSOURCE . '
					<td class="even"><a href="' . XOOPS_URL . '/userinfo.php?uid=' . $link_id . '">' . $user->getVar( 'uname' ) . '</a></td>
				</tr>
			</table>
		';
		} else {
			return '';
		}

		//!Fix Todo
		return false;
	}

	// get reference information as array
	public function fetchSummary( $link_id ) {
		$user_handler =& xoops_gethandler( 'user' );
		$user         =& $user_handler->get( $link_id );
		if ( is_object( $user ) ) {
			return [
				'module_name' => '',
				'subject'     => $user->getVar( 'uname' ),
				'uri'         => XOOPS_URL . '/userinfo.php?uid=' . $link_id,
				'summary'     => ''
			];
		}

		return 'invalid uid';
		// all values should be HTML escaped.
	}

	public function external_link_id( $params ) {
		return (int) @$_GET[ $params['itemname'] ];
	}

	// check by user handler
	public function validate_id( $link_id ) {
		$link_id = (int) $link_id;

		$user_handler =& xoops_gethandler( 'user' );
		$user         =& $user_handler->get( $link_id );
		if ( is_object( $user ) ) {
			return $link_id;
		}

		return false;
	}

	// callback on newtopic/edit/reply/delete
	// abstract
	public function onUpdate( $mode, $link_id, $forum_id, $topic_id, $post_id = 0 ) {
		return true;
	}
}
