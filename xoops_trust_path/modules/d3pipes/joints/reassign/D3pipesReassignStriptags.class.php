<?php

require_once dirname(dirname(__FILE__)).'/D3pipesReassignAbstract.class.php' ;

class D3pipesReassignStriptags extends D3pipesReassignAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		foreach( array_keys( $entries ) as $i ) {
			$entries[ $i ]['description'] = strip_tags( @$entries[ $i ]['description'] ) ;
			$entries[ $i ]['content_encoded'] = strip_tags( @$entries[ $i ]['content_encoded'] ) ;
		}
	
		return $entries ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" />' ;
	}
}

?>