<?php

require_once dirname(dirname(__FILE__)).'/D3pipesFetchAbstract.class.php' ;

class D3pipesFetchFopen extends D3pipesFetchAbstract {

	function execute( $dummy = '' , $max_entries = '' )
	{
		$xml_source = '' ;

		if( trim( $this->url ) == '' ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDURIINFETCH."\n($this->pipe_id)" ;
			return '' ;
		}

		$cache_result = $this->fetchCache() ;
		if( $cache_result !== false ) {
			list( $cached_time , $xml_source ) = $cache_result ;
			if( $cached_time + $this->cache_life_time > time() && $xml_source ) {
				return $xml_source ;
			}
		}

		$fp = @fopen( $this->url , 'rb' ) ;
		if( ! $fp ) {
			// fetch error
			$this->touchCache() ;
			if( ! @ini_get( 'allow_url_fopen' ) ) {
				$this->errors[] = _MD_D3PIPES_ERR_URLFOPENINFETCH ;
			} else {
				$this->errors[] = _MD_D3PIPES_ERR_CANNOTCONNECTINFETCH."\n($this->pipe_id)" ;
			}
			return $xml_source ;
		}

		$xml_source = '' ;
		while( ! feof( $fp ) ) {
			$xml_source .= fgets( $fp , 65536 ) ;
		}
		fclose( $fp ) ;

		if( ! $this->storeCache( $xml_source ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_CACHEFOLDERNOTWRITABLE."\nXOOPS_TRUST_PATH/cache ($this->pipe_id)" ;
			return '' ;
		}

		return $xml_source ;
	}

}

?>