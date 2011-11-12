<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesUtf8fromAbstract extends D3pipesJointAbstract {

	var $src_encoding = 'auto' ;

	// constructor
	function D3pipesUtf8fromAbstract( $mydirname , $pipe_id , $option )
	{
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		$this->src_encoding = $option ;
	}
	
	function execute( $string , $max_entries = '' ) {
		// force encoding="(other than UTF8)" to encoding="UTF-8"
		if( is_string( $string ) ) {
			list( $first_line , $rest_string ) = explode( "\n" , $string , 2 ) ;
			$first_line = preg_replace( '/encoding\=(["\']?)[0-9a-zA-Z_-]+\\1/i' , 'encoding="UTF-8"' , $first_line ) ;
			$string = $first_line . "\n" . $rest_string ;
		}

		return $this->convert( $string ) ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		$current_value = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $current_value ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="12" /><br />'._MD_D3PIPES_N4J_UTF8FROM ;
	}
}


?>