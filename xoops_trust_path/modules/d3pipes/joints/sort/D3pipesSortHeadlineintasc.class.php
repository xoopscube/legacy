<?php

require_once dirname(dirname(__FILE__)).'/D3pipesSortAbstract.class.php' ;
require_once dirname(dirname(dirname(__FILE__))).'/include/common_functions.php' ;

class D3pipesSortHeadlineintasc extends D3pipesSortAbstract {

	// $max_entires : max entries aggregated
	function execute( $entries , $max_entries = 10 )
	{
		// sort by pubtime DESC
		usort( $entries , array( $this , 'compare' ) ) ;

		// for latter sorting
		foreach( array_keys( $entries ) as $i ) {
			$entries[$i]['pubtime'] += 0.999 - 0.001 * $i ;
		}

		return $entries ;
	}

	function compare( $a , $b )
	{
		return intval( @$a['headline'] ) < intval( @$b['headline'] ) ? -1 : 1 ;
	}
}

?>