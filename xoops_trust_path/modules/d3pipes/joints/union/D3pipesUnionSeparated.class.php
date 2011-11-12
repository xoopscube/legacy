<?php

require_once dirname(dirname(__FILE__)).'/D3pipesUnionAbstract.class.php' ;
require_once dirname(dirname(dirname(__FILE__))).'/include/common_functions.php' ;

class D3pipesUnionSeparated extends D3pipesUnionAbstract {

	var $_pipes_entries = array() ; // private

	// $max_entires : max entries per an pipe
	function execute( $entries , $max_entries = 10 )
	{
		$this->_pipes_entries = empty( $entries[0]['pipe'] ) ? array() : array( $entries[0]['pipe'] + array( 'entries' => $entries ) ) ;
	
		foreach( $this->union_ids as $union_ids ) {
			$pipe4assign = d3pipes_common_get_pipe4assign( $this->mydirname , $union_ids['pipe_id'] ) ;
			if( empty( $pipe4assign ) ) continue ;
			$entries_tmp = d3pipes_common_fetch_entries( $this->mydirname , $pipe4assign , min( $union_ids['num'] , $max_entries ) , $errors , $this->mod_configs ) ;
			$this->errors = array_merge( $this->errors , $errors ) ;
			$this->_pipes_entries[] = $pipe4assign + array( 'entries' => $entries_tmp ) ;
			$entries_tmp = $this->appendPipeInfoIntoEntries( $entries_tmp , $pipe4assign ) ;
			$entries = is_array( $entries ) ? array_merge( $entries , $entries_tmp ) : $entries_tmp ;
		}

		// not sorted

		return $entries ;
	}


	function pubtime_sort( $a , $b )
	{
		return @$a['pubtime'] > @$b['pubtime'] ? -1 : 1 ;
	}

	function getPipesEntries()
	{
		return $this->_pipes_entries ;
	}
}

?>