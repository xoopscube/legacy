<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

require_once XOOPS_TRUST_PATH . '/modules/pico/class/FormProcessByHtml.class.php';
require_once XOOPS_TRUST_PATH . '/modules/pico/class/PicoFormProcessBySmartyBase.class.php';

function smarty_function_formmail4fleamarket( $params, &$smarty ) {
	$controller = new PicoFormProcessBySmartyFormmail4fleamarket();

	$controller->parseParameters( $params );

	// add a toEmail from xoopsUser
	$content = $smarty->get_template_vars( 'content' );

	$poster_uid = (int) $content['poster_uid'];

	$user_handler = &xoops_gethandler( 'user' );

	$poster = $user_handler->get( $poster_uid );

	if ( is_object( $poster ) ) {
		$controller->toEmails[] = $poster->getVar( 'email', 'n' );
	}

	if ( $controller->countValidToEmails() <= 0 ) {
		die( 'Set a valid email address by adding to="(email)" inside &lt;{' . $controller->mypluginname . '}&gt;' );
	}
	$controller->execute( $params, $smarty );
}

class PicoFormProcessBySmartyFormmail4fleamarket extends PicoFormProcessBySmartyBase {
	public function __construct() {
		$this->mypluginname = 'formmail4fleamarket';
	}

	public function executeLast() {
		$this->sendMail();
		$this->storeDB();
	}
}
