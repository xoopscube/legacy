<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesClipAbstract extends D3pipesJointAbstract {

	var $max_entries_from_clip = 10000 ; // private
	var $entries_from_clip ; // public
	var $clip_life_time ; // public

	// constructor
	function D3pipesClipAbstract( $mydirname , $pipe_id , $option ) {
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		$options = explode( '|' , $option ) ;
		$this->entries_from_clip = $options[0] > $this->max_entries_from_clip ? $this->max_entries_from_clip : intval( $options[0] ) ;
		$this->clip_life_time = isset( $options[1] ) && is_numeric( $options[1] ) ? intval( $options[1] * 86400 ) : null ;
	}

	function execute( $entries , $max_entries = 10 ) {}

	function getClipping( $clipping_id ) {}

	function getClippings( $pipe_id , $num , $pos = 0 ) {}

	function getClippingCount( $pipe_id ) {}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		$options = explode( '|' , $current_value ) ;

		// options[0]  (entries_from_clip)
		$options[0] = $options[0] > $this->max_entries_from_clip ? $this->max_entries_from_clip : intval( $options[0] ) ;
		$ret_0 = _MD_D3PIPES_N4J_ENTRIESFROMCLIP.'<input type="text" name="joint_options['.$index.'][0]" value="'.@$options[0].'" size="4" style="text-align:right;" />' ;

		// options[1]  (clip_life_time)
		$options[1] = isset( $options[1] ) && is_numeric( $options[1] ) ? intval( $options[1] ) : '' ;
		$ret_1 = _MD_D3PIPES_N4J_CLIPLIFETIME.'<input type="text" name="joint_options['.$index.'][1]" value="'.$options[1].'" size="4" style="text-align:right;" />' ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.'<br />'.$ret_1 ;
	}

}


?>