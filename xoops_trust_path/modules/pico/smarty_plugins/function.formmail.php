<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

require_once XOOPS_TRUST_PATH . '/modules/pico/class/FormProcessByHtml.class.php';
require_once XOOPS_TRUST_PATH . '/modules/pico/class/PicoFormProcessBySmartyBase.class.php';

function smarty_function_formmail( $params, &$smarty ) {
	$controller = new PicoFormProcessBySmartyFormmail();

	$controller->parseParameters( $params );

	// toEmails from 'adminmail'
	if ( empty( $controller->toEmails ) && '' != trim( $GLOBALS['xoopsConfig']['adminmail'] ) ) {
		$controller->toEmails[] = $GLOBALS['xoopsConfig']['adminmail'];
	}

	if ( $controller->countValidToEmails() <= 0 ) {
		die( 'Set a valid email address by adding to="(email)" inside &lt;{' . $controller->mypluginname . '}&gt;' );
	}
	$controller->execute( $params, $smarty );
}

class PicoFormProcessBySmartyFormmail extends PicoFormProcessBySmartyBase {
	public function __construct() {
		$this->mypluginname = 'formmail';
	}

	public function executeLast() {
		$this->sendMail();
		//$this->storeDB() ;
	}
}
