<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesCacheAbstract extends D3pipesJointAbstract {

	var $minimum_cache_time = 300 ; // 300sec = 5min
	var $cache_time ; // public

	// constructor
	function D3pipesCacheAbstract( $mydirname , $pipe_id , $option ) {
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		$this->cache_time = intval( $option ) ;
		if( $this->cache_time < $this->minimum_cache_time ) $this->cache_time = $this->minimum_cache_time ;
	}

	function execute( $entries , $max_entries = 10 ) {}

	function isCached()
	{
		$cache_result = $this->fetchCache() ;
		if( $cache_result !== false ) {
			list( $cached_time , $this->cached_body ) = $cache_result ;
			if( $cached_time + $this->cache_time > time() ) {
				$this->is_cached = true ;
				return true ;
			}
		}
	}

	function fetchCache()
	{
		$cache_file = $this->getCachePath() ;
		if( ! file_exists( $cache_file ) ) return false ;
		$body_serialized = file_get_contents( $cache_file ) ;
		$body = $body_serialized ? d3pipes_common_unserialize( $body_serialized ) : array() ;
		return array( filemtime( $cache_file ) , $body ) ;
	}

	function storeCache( $entries )
	{
		$cache_file = $this->getCachePath() ;
		@unlink( $cache_file ) ;
		$fp = @fopen( $cache_file , 'wb' ) ;
		if( ! $fp ) return false ;
		fwrite( $fp , serialize( $entries ) ) ;
		fclose( $fp ) ;
		return true ;
	}

	function touchCache()
	{
		$cache_file = $this->getCachePath() ;
		@touch( $cache_file ) ;
		return true ;
	}

	function getCachePath()
	{
		$base = d3pipes_common_cache_path_base( $this->mydirname ) ;
		return $base . sprintf( '%05d_%02d_cache' , $this->pipe_id , $this->stage ) ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		$current_value = intval( $current_value ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="10" style="text-align:right;" /><br />'._MD_D3PIPES_N4J_CACHE ;
	}

}


?>