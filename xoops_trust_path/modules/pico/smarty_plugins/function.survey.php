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

function smarty_function_survey( $params, &$smarty ) {
	$controller               = new PicoFormProcessBySmartySurvey();
	$controller->canPostAgain = false; // default false for survey
	$controller->parseParameters( $params );
	$controller->execute( $params, $smarty );
}

class PicoFormProcessBySmartySurvey extends PicoFormProcessBySmartyBase {
	public function __construct() {
		$this->mypluginname = 'survey';
	}

	public function executeLast() {
		$this->sendMail();
		$this->storeDB();
	}
}
