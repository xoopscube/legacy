<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesPingAbstract extends D3pipesJointAbstract {

	var $minimum_interval = 1800 ; // 1800sec = 30min

	// constructor
	function D3pipesPingAbstract( $mydirname , $pipe_id , $option ) {
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
	}

	// abstract
	function execute( $entries , $max_entries = 10 ) {}

	function isMatured()
	{
		$md5_file = $this->getCachePath() ;
		if( ! file_exists( $md5_file ) ) return true ;
		if( filemtime( $md5_file ) < time() - $this->minimum_interval ) return true ;
		else return false ;
	}

	function fetchPrevMd5()
	{
		$md5_file = $this->getCachePath() ;
		if( ! file_exists( $md5_file ) ) return '' ;
		return file_get_contents( $md5_file ) ;
	}

	function storeMd5( $entries )
	{
		$md5_file = $this->getCachePath() ;
		@unlink( $md5_file ) ;
		$fp = @fopen( $md5_file , 'wb' ) ;
		if( ! $fp ) return false ;
		fwrite( $fp , md5( serialize( $entries ) ) ) ;
		fclose( $fp ) ;
		return true ;
	}

	function touchCache()
	{
		$md5_file = $this->getCachePath() ;
		@touch( $md5_file ) ;
		return true ;
	}

	function getCachePath()
	{
		$base = d3pipes_common_cache_path_base( $this->mydirname ) ;
		return $base . sprintf( '%05d_%02d_ping' , $this->pipe_id , $this->stage ) ;
	}

	// almost this class have no options
	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" />' ;
	}

}


?>