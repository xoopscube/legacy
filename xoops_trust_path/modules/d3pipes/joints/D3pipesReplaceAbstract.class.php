<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesReplaceAbstract extends D3pipesJointAbstract {

	var $option ;
	var $pattern ;
	var $replacement ;
	var $separator = '||' ;

	// constructor
	function D3pipesReplaceAbstract( $mydirname , $pipe_id , $option )
	{
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		@list( $this->pattern , $this->replacement ) = explode( $this->separator , $option ) ;
		$this->option = $option ;
	}

	function execute( $xml_source , $max_entries = '' ) {}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		$options = explode( $this->separator , $current_value , 2 ) ;

		// options[0]  (patterns from)
		$ret_0 = _MD_D3PIPES_N4J_REPLACEFROM.'<input type="text" name="joint_options['.$index.'][0]" value="'.htmlspecialchars(@$options[0],ENT_QUOTES).'" size="30" />' ;

		// options[1]  (replacement to)
		$ret_1 = _MD_D3PIPES_N4J_REPLACETO.'<input type="text" name="joint_options['.$index.'][1]" value="'.htmlspecialchars(@$options[1],ENT_QUOTES).'" size="30" />' ;

		return '<input type="hidden" name="joint_option_separator['.$index.']" value="'.$this->separator.'" /><input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.'<br />'.$ret_1 ;
	}

}


?>