<?php

require_once dirname(dirname(__FILE__)).'/D3pipesFetchAbstract.class.php' ;

class D3pipesFetchSnoopy extends D3pipesFetchAbstract {

	function execute( $dummy = '' , $max_entries = '' )
	{
		$this->cache_life_time = max( $this->cache_life_time , $this->mod_configs['fetch_cache_life_time'] ) ;

		$xml_source = '' ;

		if( ! strstr( $this->url , '://' ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDURIINFETCH."\n($this->pipe_id)" ;
			return '' ;
		}

		$cache_result = $this->fetchCache() ;
		if( $cache_result !== false ) {
			list( $cached_time , $xml_source ) = $cache_result ;
			if( $cached_time + $this->cache_life_time > time() ) {
				return $xml_source ;
			}
		}

		require_once XOOPS_ROOT_PATH.'/class/snoopy.php' ;
		$snoopy = new Snoopy ;
		$snoopy->maxredirs = 0 ;
		$snoopy->offsiteok = true ;

		$snoopy->proxy_host = $this->mod_configs['snoopy_proxy_host'] ;
		$snoopy->proxy_port = $this->mod_configs['snoopy_proxy_port'] ;
		$snoopy->proxy_user = $this->mod_configs['snoopy_proxy_user'] ;
		$snoopy->proxy_pass = $this->mod_configs['snoopy_proxy_pass'] ;
		$snoopy->curl_path = $this->mod_configs['snoopy_curl_path'] ;

		$fetch_result = $snoopy->fetch( $this->url ) ;

		// check redirect
		if( $fetch_result && $snoopy->_redirectaddr && $this->mod_configs['snoopy_maxredirs'] > 0 ) {
			if( ! empty( $this->mod_configs['redirect_warning'] ) ) {
				$this->errors[] = _MD_D3PIPES_ERR_REDIRECTED."\n(".$this->pipe_id.")\n".$this->url." ->\n".$snoopy->_redirectaddr ;
			}
			$snoopy->maxredirs = $this->mod_configs['snoopy_maxredirs'] ;
			$fetch_result = $snoopy->fetch( $this->url ) ;
		}

		// check fetch error
		if( ! $fetch_result || ! ( $xml_source = $snoopy->results ) ) {
			$this->touchCache() ;
			$message = _MD_D3PIPES_ERR_CANTCONNECTINFETCH."\n" ;
			if( ! empty( $snoopy->proxy_host ) ) {
				$message .= _MD_D3PIPES_ERR_DOUBTFULPROXY."\n" ;
			}
			if( substr( $this->url , 0 , 5 ) == 'https' ) {
				$message .= _MD_D3PIPES_ERR_DOUBTFULCURLPATH."\n" ;
			}
			$this->errors[] = $message."($this->pipe_id)" ;
			return '' ;
		}

		// check cache folder is writable
		if( ! $this->storeCache( $xml_source ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_CACHEFOLDERNOTWRITABLE."\nXOOPS_TRUST_PATH/cache ($this->pipe_id)" ;
			return '' ;
		}

		return $xml_source ;
	}

}

?>