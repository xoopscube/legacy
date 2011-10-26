<?php

require_once dirname(dirname(__FILE__)).'/D3pipesUtf8fromAbstract.class.php' ;

class D3pipesUtf8fromIso88591 extends D3pipesUtf8fromAbstract {

	function convert( $data )
	{
		// ignore encodign specified by option
		//if( empty( $this->src_encoding ) ) $this->src_encoding = 'auto' ;

		if( is_array( $data ) ) {
			return array_map( array( $this , 'convert' ) , $data ) ;
		} else {
			return utf8_encode( $data ) ;
		}
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" />' ;
	}

}

?>