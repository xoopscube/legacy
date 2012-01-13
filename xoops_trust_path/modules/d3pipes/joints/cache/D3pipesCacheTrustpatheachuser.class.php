<?php

require_once dirname(__FILE__).'/D3pipesCacheTrustpath.class.php' ;

class D3pipesCacheTrustpatheachuser extends D3pipesCacheTrustpath {

	function getCachePath()
	{
		global $xoopsUser;
		if( is_object( $xoopsUser ) ) {
			$uid = $xoopsUser->getVar('uid');
		} else {
			$uid = 0;
		}
		$base = d3pipes_common_cache_path_base( $this->mydirname ) ;
		return $base . sprintf( '%05d_%02d_uid_%d_cache' , $this->pipe_id , $this->stage, $uid ) ;
	}

}
