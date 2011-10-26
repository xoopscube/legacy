<?php

require_once dirname(dirname(__FILE__)).'/D3pipesSortAbstract.class.php' ;
require_once dirname(dirname(dirname(__FILE__))).'/include/common_functions.php' ;

class D3pipesSortPubtimedsc extends D3pipesSortAbstract {

	// $max_entires : max entries aggregated
	function execute( $entries , $max_entries = 10 )
	{
		// sort by pubtime DESC
		usort( $entries , array( $this , 'compare' ) ) ;

		return $entries ;
	}

	function compare( $a , $b )
	{
		if( @$a['pubtime'] == @$b['pubtime'] ) return intval( @$a['headline'] ) < intval( @$b['headline'] ) ? -1 : 1 ;
		else return @$a['pubtime'] > @$b['pubtime'] ? -1 : 1 ;
	}
}

?>