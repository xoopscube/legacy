<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/D3forumAntispamJapanese.class.php';

class D3forumAntispamJapanesemobilesmart extends D3forumAntispamJapanese {

	public function checkValidate() {
		if ( $this->isMobile() ) {
			return true;
		}

		return parent::checkValidate();
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

}
