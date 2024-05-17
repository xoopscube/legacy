<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class D3forumAntispamAbstract {

	public $errors = [];

	public function getErrors4Html() {
		$ret = '';

		foreach ( $this->errors as $error ) {
			$ret .= '<span style="color:#f00;">' . htmlspecialchars( $error ) . '</span><br>';
		}

		return $ret;
	}

	public function getHtml4Assign() {
		return [
			'html_in_form'            => '',
			'js_global'               => '',
			'js_in_validate_function' => '',
		];
	}

	public function checkValidate() {
		return true;
	}

	public function isMobile() {
		if ( class_exists( 'Wizin_User' ) ) {
			// WizMobile (gusagi)
			$user = Wizin_User::getSingleton();

			return $user->bIsMobile;
		}

		if ( defined( 'HYP_K_TAI_RENDER' ) && HYP_K_TAI_RENDER && HYP_K_TAI_RENDER != 2 ) {
			// hyp_common ktai-renderer (nao-pon)
			return true;
		}

		return false;
	}

	public function setVar( $key, $val ) {
		if ( property_exists( $this, $key ) ) {
			$this->$key = $val;

			return true;
		}

		return false;
	}

	public function getVar( $key ) {
		if ( property_exists( $this, $key ) ) {
			return $this->$key;
		}

		return null;
	}
}
