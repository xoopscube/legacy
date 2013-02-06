<?php

define( 'PROTECTOR_POSTCOMMON_POST_REGISTER_MORATORIUM' , 60 ) ; // minutes

class protector_postcommon_post_register_moratorium extends ProtectorFilterAbstract {

	function execute()
	{
		global $xoopsUser ;

		if( ! is_object( $xoopsUser ) ) {
			return true ;
		}

		$moratorium_result = intval( ( $xoopsUser->getVar('user_regdate') + PROTECTOR_POSTCOMMON_POST_REGISTER_MORATORIUM * 60 - time() ) / 60 ) ;
		if( $moratorium_result > 0 ) {
			if( preg_match( '#(https?\:|\[\/url\]|www\.)#' , serialize( $_POST ) ) ) {
				$message = sprintf( _MD_PROTECTOR_FMT_REGISTER_MORATORIUM , $moratorium_result ) ;
				$this->protector->message .= $message . '(' . serialize( $_POST ) . ')' ;
				$this->protector->output_log( 'Moratorium' , 0 , false , 128 ) ;
				die( $message ) ;
			}
		}
	}

}

?>