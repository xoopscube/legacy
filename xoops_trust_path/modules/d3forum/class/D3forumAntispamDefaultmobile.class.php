<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/D3forumAntispamDefault.class.php';

class D3forumAntispamDefaultmobile extends D3forumAntispamDefault {

	public function checkValidate() {
		if ( $this->isMobile() ) {
			return true;
		}

		return parent::checkValidate();
	}

}
