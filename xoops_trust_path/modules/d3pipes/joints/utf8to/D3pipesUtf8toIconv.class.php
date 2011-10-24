<?php

require_once dirname(dirname(__FILE__)).'/D3pipesUtf8toAbstract.class.php' ;

class D3pipesUtf8toIconv extends D3pipesUtf8toAbstract {

	function execute( $data , $max_entries = 10 )
	{
		if( empty( $this->dest_encoding ) ) $this->dest_encoding = _CHARSET ; // TODO?

		if( is_array( $data ) ) {
			return array_map( array( $this , 'execute' ) , $data ) ;
		} else {
			return iconv( 'UTF-8' , $this->dest_encoding , $data ) ;
		}
	}

}

?>