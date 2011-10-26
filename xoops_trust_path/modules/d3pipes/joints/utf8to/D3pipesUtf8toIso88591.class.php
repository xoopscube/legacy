<?php

require_once dirname(dirname(__FILE__)).'/D3pipesUtf8toAbstract.class.php' ;

class D3pipesUtf8toIso88591 extends D3pipesUtf8toAbstract {

	function execute( $data , $max_entries = 10 )
	{
		// ignore encodign specified by option
		//if( empty( $this->dest_encoding ) ) $this->dest_encoding = mb_internal_encoding() ;

		if( is_array( $data ) ) {
			return array_map( array( $this , 'execute' ) , $data ) ;
		} else {
			return utf8_decode( $data ) ;
		}
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" />' ;
	}
}

?>