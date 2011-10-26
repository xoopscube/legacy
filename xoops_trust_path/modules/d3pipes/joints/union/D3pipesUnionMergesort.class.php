<?php

require_once dirname(dirname(__FILE__)).'/D3pipesUnionAbstract.class.php' ;
require_once dirname(dirname(dirname(__FILE__))).'/include/common_functions.php' ;

class D3pipesUnionMergesort extends D3pipesUnionAbstract {

	// $max_entires : max entries aggregated
	function execute( $entries , $max_entries = 10 )
	{
		foreach( $this->union_ids as $union_ids ) {
			$pipe4assign = d3pipes_common_get_pipe4assign( $this->mydirname , $union_ids['pipe_id'] ) ;
			$entries_tmp = d3pipes_common_fetch_entries( $this->mydirname , $pipe4assign , $union_ids['num'] , $errors , $this->mod_configs ) ;
			$this->errors = array_merge( $this->errors , $errors ) ;
			$entries_tmp = $this->appendPipeInfoIntoEntries( $entries_tmp , $pipe4assign ) ;
			$entries = is_array( $entries ) ? array_merge( $entries , $entries_tmp ) : $entries_tmp ;
		}

		// sort by pubtime DESC
		usort( $entries , array( $this , 'pubtime_sort' ) ) ;

		return array_slice( $entries , 0 , $max_entries ) ;
	}


	function pubtime_sort( $a , $b )
	{
		if( @$a['pubtime'] == @$b['pubtime'] ) return intval( @$a['headline'] ) < intval( @$b['headline'] ) ? -1 : 1 ;
		else return @$a['pubtime'] > @$b['pubtime'] ? -1 : 1 ;
	}

}

?>