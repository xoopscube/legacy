<?php

require_once dirname(__FILE__).'/D3forumAntispamDefault.class.php' ;

class D3forumAntispamDefaultmobile extends D3forumAntispamDefault {

function checkValidate()
{
	if( $this->isMobile() ) {
		return true ;
	} else {
		return parent::checkValidate() ;
	}
}

}

?>