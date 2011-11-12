<?php

require_once dirname(dirname(__FILE__)).'/D3pipesReassignAbstract.class.php' ;

class D3pipesReassignTruncate extends D3pipesReassignAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		$options = array_map( 'intval' , explode( '|' , $this->option ) ) ;

		foreach( array_keys( $entries ) as $i ) {
			$entry =& $entries[ $i ] ;
			// trimmark = '...' then minimum 3byte
			if( $options[0] > 3 ) $entry['headline'] = xoops_substr( @$entry['headline'] , 0 , $options[0] ) ;
			if( $options[1] > 3 ) {
				$entry['content_encoded'] = xoops_substr( @$entry['content_encoded'] , 0 , $options[1] ) ;
				$description_tmp = xoops_substr( @$entry['description'] , 0 , $options[1] ) ;
				if( $description_tmp != @$entry['description'] ) {
					$entry['allow_html'] = false ;
				}
	
				$entry['description'] = $description_tmp ;
			}
		}

		return $entries ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		$options = explode( '|' , $current_value ) ;

		// options[0]  (bytes for headline)
		$options[0] = intval( @$options[0] ) ;
		$ret_0 = _MD_D3PIPES_TH_HEADLINE.':<input type="text" name="joint_options['.$index.'][0]" value="'.@$options[0].'" size="4" style="text-align:right;" />bytes' ;

		// options[1]  (bytes for description)
		$options[1] = intval( @$options[1] ) ;
		$ret_1 = _MD_D3PIPES_TH_DESCRIPTION.':<input type="text" name="joint_options['.$index.'][1]" value="'.@$options[1].'" size="4" style="text-align:right;" />bytes' ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.'<br />'.$ret_1 ;
	}
}

?>