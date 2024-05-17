<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class D3forumMessageValidator {

	public $errors = [];

	public function get_errors4html() {
		$ret = '';
		foreach ( $this->errors as $error ) {
			$ret .= '<span style="color:#f00;">' . htmlspecialchars( $error ) . '</span><br>';
		}

		return $ret;
	}

	public function validate_by_rendered( $html ) {
		$fragments = explode( '<div', $html );

		$nest_level = 0;

		foreach ( $fragments as $fragment ) {
			$nest_level -= substr_count( $fragment, '</div>' );
			if ( $nest_level < 0 ) {
				$this->errors[] = _MD_D3FORUM_ERR_TOOMANYDIVEND;

				return false;
			}
			$nest_level ++;
		}
		if ( 1 !== $nest_level ) {
			$this->errors[] = _MD_D3FORUM_ERR_TOOMANYDIVBEGIN;

			return false;
		}

		return true;
	}

}
