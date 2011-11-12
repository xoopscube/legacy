<?php

require_once dirname(dirname(__FILE__)).'/D3pipesReassignAbstract.class.php' ;

class D3pipesReassignDefaultlink extends D3pipesReassignAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		foreach( array_keys( $entries ) as $i ) {
			$entry =& $entries[ $i ] ;
			if( empty( $entry['link'] ) ) {
				$entry['link'] = $this->option ;
			}
		}

		return $entries ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="40" /><br />'._MD_D3PIPES_N4J_WRITEURL ;
	}
}

?>