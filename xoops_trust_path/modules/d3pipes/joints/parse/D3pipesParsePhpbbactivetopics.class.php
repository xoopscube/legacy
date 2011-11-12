<?php

require_once dirname(dirname(__FILE__)).'/D3pipesParseAbstract.class.php' ;

class D3pipesParsePhpbbactivetopics extends D3pipesParseAbstract {

	function execute( $html_source , $max_entries = '' )
	{
		$items = array() ;

		$pipe4assign = d3pipes_common_get_pipe4assign( $this->mydirname , $this->pipe_id ) ;
		$base_url = substr( $pipe4assign['url'] , 0 , strrpos( $pipe4assign['url'] , '/' ) ) ;

		$result = preg_match_all( '#class\=\"topictitle\"\>([^<]+)\<\/a\>.*viewtopic.php\?f\=(\d+)\&amp\;t\=(\d+)\&[^"]+p=(\d+)\D+.*\<br \/\>([^<]+)\<br \/\>#sU' , $html_source , $matches , PREG_SET_ORDER ) ;
		if( $result === false ) {
			$this->errors[] = _MD_D3PIPES_ERR_PARSETYPEMISMATCH."\n($this->pipe_id)" ;
		}
		foreach( $matches as $match ) {
			$pubtime = strtotime( $match[5] . ' (UTC)' ) ; // phpbb uses UTC
			$link = $base_url.'/viewtopic.php?f='.$match[2].'&t='.$match[3].'&p='.$match[4].'#p'.$match[4] ;
			$headline = $match[1] ;

			$items[] = array(
				'headline' => $headline ,
				'pubtime' => $pubtime ,
				'link' => $link ,
				'fingerprint' => $link ,
			) ;
		}

		return $items ;
	}


	function renderOptions( $index , $current_value = null )
	{
		return '' ;
	}
}

?>