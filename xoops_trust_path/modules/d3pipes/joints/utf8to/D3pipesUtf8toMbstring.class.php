<?php

require_once dirname(dirname(__FILE__)).'/D3pipesUtf8toAbstract.class.php' ;

class D3pipesUtf8toMbstring extends D3pipesUtf8toAbstract {

	function execute( $data , $max_entries = 10 )
	{
		if( empty( $this->dest_encoding ) ) $this->dest_encoding = mb_internal_encoding() ;

		if( is_array( $data ) ) {
			return array_map( array( $this , 'execute' ) , $data ) ;
		} else {
			return mb_convert_encoding( $data , $this->dest_encoding , 'UTF-8' ) ;
		}
	}

}

?>