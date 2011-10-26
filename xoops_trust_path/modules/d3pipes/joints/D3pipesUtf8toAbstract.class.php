<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesUtf8toAbstract extends D3pipesJointAbstract {

	var $dest_encoding ;

	// constructor
	function D3pipesUtf8toAbstract( $mydirname , $pipe_id , $option )
	{
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		$this->dest_encoding = $option ;
	}
	
	function execute( $string , $max_entries = '' ) {}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		$current_value = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $current_value ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="12" /><br />'._MD_D3PIPES_N4J_UTF8TO ;
	}
}


?>