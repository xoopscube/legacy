<?php

require_once dirname(dirname(__FILE__)).'/D3pipesFilterAbstract.class.php' ;

class D3pipesFilterFuture_except extends D3pipesFilterAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		$ret = array() ;
		foreach( $entries as $entry ) {
			if( $entry['pubtime'] < time() ) $ret[] = $entry ;
		}
	
		return $ret ;
	}

	function renderOptions( $index , $current_value = null )
	{
		return '' ;
	}

}

?>