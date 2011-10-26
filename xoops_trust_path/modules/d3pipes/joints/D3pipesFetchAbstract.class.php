<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesFetchAbstract extends D3pipesJointAbstract {

	var $cache_life_time ;
	var $url ;
	
	// constructor
	function D3pipesFetchAbstract( $mydirname , $pipe_id , $option )
	{
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		$this->cache_life_time = 300 ; // minimum
		$this->url = $option ;
	}

	function execute( $dummy = '' , $max_entries = '' ) {}

	function fetchCache()
	{
		$cache_file = $this->getCachePath() ;
		if( ! file_exists( $cache_file ) ) return false ;
		else return array( filemtime( $cache_file ) , file_get_contents( $cache_file ) ) ;
	}

	function fetch_cache() { return $this->fetchCache() ; }


	function storeCache( $xml_source )
	{
		$cache_file = $this->getCachePath() ;
		@unlink( $cache_file ) ;
		$fp = @fopen( $cache_file , 'wb' ) ;
		if( ! $fp ) return false ;
		fwrite( $fp , $xml_source ) ;
		fclose( $fp ) ;

		// update lastfetch_time
		$db =& Database::getInstance() ;
		$db->queryF( "UPDATE ".$db->prefix($this->mydirname."_pipes")." SET lastfetch_time=UNIX_TIMESTAMP() WHERE pipe_id=$this->pipe_id" ) ;

		return true ;
	}

	function store_cache( $xml_source ) { return $this->storeCache( $xml_source ) ; }


	function touchCache()
	{
		$cache_file = $this->getCachePath() ;
		@touch( $cache_file ) ;
		return true ;
	}

	function touch_cache() { return $this->touchCache() ; }


	function getCachePath()
	{
		$base = d3pipes_common_cache_path_base( $this->mydirname ) ;
		return $base . sprintf( '%05d_%02d_fetch' , $this->pipe_id , $this->stage ) ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		if( substr( $current_value , 0 , 4 ) != 'http' ) {
			$current_value = 'http://' ;
		}

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="40" /><br />'._MD_D3PIPES_N4J_FETCH ;
	}

}


?>