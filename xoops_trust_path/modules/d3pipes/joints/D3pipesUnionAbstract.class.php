<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesUnionAbstract extends D3pipesJointAbstract {

	var $union_ids = array() ;
	var $default_num = 10 ;
	var $keep_pipe_info = 0 ;

	// constructor
	function D3pipesUnionAbstract( $mydirname , $pipe_id , $option = '' )
	{
		$options = explode( '|' , $option ) ;

		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		if( trim( $options[0] ) == '' ) $union_idnums = array() ;
		else $union_idnums = array_map( 'trim' , explode( ',' , $options[0] ) ) ;
		if( empty( $options[1] ) ) $options[1] = 10 ;
		$this->keep_pipe_info = empty( $options[2] ) ? 0 : intval( @$options[2] ) ;

		foreach( $union_idnums as $idnum ) {
			@list( $pipe_id , $num ) = explode( ':' , $idnum ) ;
			if( intval( @$pipe_id ) > 0 ) {
				$this->union_ids[] = array(
					'pipe_id' => intval( $pipe_id ) ,
					'num' => intval( @$num ) > 0 ? intval( $num ) : intval( $options[1] ) ,
				) ;
			}
		}
	}

	// virtual
	function execute( $entries , $max_entries = 10 ) {}

	// append pipe_info into entries
	function appendPipeInfoIntoEntries( $entries , $pipe4assign )
	{
		foreach( array_keys( $entries ) as $i ) {
			if( empty( $entries[ $i ][ 'pipe' ] ) ) {
				$entries[ $i ][ 'initial_pipe' ] = $pipe4assign ;
				$entries[ $i ][ 'pipe' ] = $pipe4assign ;
			} else if( ! $this->keep_pipe_info ) {
				$entries[ $i ][ 'pipe' ] = $pipe4assign ;
			}
		}

		return $entries ;
	}

	// rendering common options
	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		$options = explode( '|' , $current_value ) ;

		// options[0]  (entries)
		$options[0] = preg_replace( '/[^0-9,:]/' , '' , @$options[0] ) ;
		$ret_0 = '<label>'._MD_D3PIPES_N4J_UNION.'<input type="text" name="joint_options['.$index.'][0]" value="'.htmlspecialchars($options[0],ENT_QUOTES).'" size="40" /></label>' ;

		// options[1]  (default num)
		$options[1] = empty( $options[1] ) ? 10 : intval( @$options[1] ) ;
		$ret_1 = '<label>'._MD_D3PIPES_N4J_EACHENTRIES.':<input type="text" name="joint_options['.$index.'][1]" value="'.@$options[1].'" size="3" style="text-align:right;" /></label>' ;

		// options[2]  (default false)
		$options[2] = empty( $options[2] ) ? 0 : intval( @$options[2] ) ;
		$ret_2 = '<label>'._MD_D3PIPES_N4J_KEEPPIPEINFO.':<input type="checkbox" name="joint_options['.$index.'][2]" value="1" '.($options[2]?'checked="checked"':'').' /></label>' ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.'<br />'.$ret_1.' &nbsp; '.$ret_2 ;
	}
}


?>