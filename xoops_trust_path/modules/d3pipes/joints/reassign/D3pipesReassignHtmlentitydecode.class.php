<?php

require_once dirname(dirname(__FILE__)).'/D3pipesReassignAbstract.class.php' ;

class D3pipesReassignHtmlentitydecode extends D3pipesReassignAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		foreach( array_keys( $entries ) as $i ) {
			$entry =& $entries[ $i ] ;
			$entry['headline'] = html_entity_decode( @$entry['headline'] , ENT_QUOTES ) ;
			$entry['link'] = html_entity_decode( @$entry['link'] , ENT_QUOTES ) ;
			$entry['description'] = html_entity_decode( @$entry['description'] , ENT_QUOTES ) ;
			$entry['content_encoded'] = html_entity_decode( @$entry['content_encoded'] , ENT_QUOTES ) ;
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