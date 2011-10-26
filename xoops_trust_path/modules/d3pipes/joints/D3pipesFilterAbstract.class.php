<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesFilterAbstract extends D3pipesJointAbstract {

	var $pattern ;

	// constructor
	function D3pipesFilterAbstract( $mydirname , $pipe_id , $option )
	{
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		$this->pattern = $option ;
	}
	
	function execute( $entries , $max_entries = 10 ) {}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="40" /><br />'._MD_D3PIPES_N4J_WRITEPREG ;
	}
}

?>