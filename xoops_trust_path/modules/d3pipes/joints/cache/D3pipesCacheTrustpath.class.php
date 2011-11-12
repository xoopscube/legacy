<?php

require_once dirname(dirname(__FILE__)).'/D3pipesCacheAbstract.class.php' ;

class D3pipesCacheTrustpath extends D3pipesCacheAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		if( $this->is_cached ) {
			// cached
			return $this->cached_body ;
		} else if( empty( $entries ) ) {
			// fetch error
			if( ! empty( $this->cached_body ) ) $this->touchCache() ;
			return $this->cached_body ;
		} else {
			// fetch success (cache expired)
			$this->storeCache( $entries ) ;
			return $entries ;
		}
	}

}


?>