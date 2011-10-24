<?php

require_once dirname(dirname(__FILE__)).'/D3pipesUtf8fromAbstract.class.php' ;

class D3pipesUtf8fromIconv extends D3pipesUtf8fromAbstract {

	function convert( $data )
	{
		if( empty( $this->src_encoding ) ) $this->src_encoding = 'ISO-8859-1' ; // TODO?

		if( is_array( $data ) ) {
			return array_map( array( $this , 'convert' ) , $data ) ;
		} else {
			return iconv( $this->src_encoding , 'UTF-8' , $data ) ;
		}
	}

}

?>