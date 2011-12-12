<?php

require_once dirname(__FILE__).'/D3forumAntispamJapanese.class.php' ;

class D3forumAntispamJapanesemobilesmart extends D3forumAntispamJapanese {

function checkValidate()
{
	if( $this->isMobile() ) {
		return true ;
	} else {
		return parent::checkValidate() ;
	}
}

function isMobile()
{
	if( class_exists( 'Wizin_User' ) ) {
		// WizMobile (gusagi)
		$user =& Wizin_User::getSingleton();
		return $user->bIsMobile ;
	} else if( defined( 'HYP_K_TAI_RENDER' ) && HYP_K_TAI_RENDER && HYP_K_TAI_RENDER != 2 ) {
		// hyp_common ktai-renderer (nao-pon)
		return true ;
	} else {
		return false ;
	}
}

}

?>