<?php

require_once dirname(dirname(__FILE__)).'/D3pipesFilterAbstract.class.php' ;

class D3pipesFilterMbregex extends D3pipesFilterAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		// check regex is valid or not
		$pattern = $this->pattern ;
		if( trim( $pattern ) == '' ) return $entries ;
	
		$ret = array() ;
		foreach( $entries as $entry ) {
			if( mb_ereg( $pattern , serialize( $entry ) ) ) {
				$ret[] = $entry ;
			}
		}
	
		return $ret ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="40" /><br />'._MD_D3PIPES_N4J_WRITEPOSIX ;
	}
}

?>