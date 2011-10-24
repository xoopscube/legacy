<?php

require_once dirname(dirname(__FILE__)).'/D3pipesReassignAbstract.class.php' ;

class D3pipesReassignPico_ef_except extends D3pipesReassignAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		foreach( array_keys( $entries ) as $i ) {
			$entry =& $entries[ $i ] ;
			foreach( array_keys( $entry['ef'] ) as $field_name ) {
				if( preg_match( $this->option , $field_name ) ) {
					unset( $entry['ef'][ $field_name ] ) ;
				}
			}
		}
	
		return $entries ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="40" /><br />'._MD_D3PIPES_N4J_WRITEPREG ;
	}
}

?>