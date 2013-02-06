<?php

// Don't enable this for site using single-byte
// Perhaps, japanese, schinese, tchinese, and korean can use it

class protector_postcommon_post_need_multibyte extends ProtectorFilterAbstract {

	function execute()
	{
		global $xoopsUser ;
	
		if( ! function_exists( 'mb_strlen' ) ) return true ;
	
		// registered users always pass this plugin
		if( is_object( $xoopsUser ) ) return true ;
	
		$lengths = array(
			0 => 100 , // default value
			'message' => 2 ,
			'com_text' => 2 ,
			'excerpt' => 2 ,
		) ;
	
		foreach( $_POST as $key => $data ) {
			// dare to ignore arrays/objects
			if( ! is_string( $data ) ) continue ;
	
			$check_length = isset( $lengths[ $key ] ) ? $lengths[ $key ] : $lengths[ 0 ] ;
			if( strlen( $data ) > $check_length ) {
				if( mb_strlen( $data , 'ISO-8859-1' ) == mb_strlen( $data , _CHARSET ) ) {
					$this->protector->message .= "No multibyte character was found ($data)\n" ;
					$this->protector->output_log( 'Singlebyte SPAM' , 0 , false , 128 ) ;
					die( _MD_PROTECTOR_DENYBYMULTIBYTE ) ;
				}
			}
		}
	
		return true ;
	}

}

?>