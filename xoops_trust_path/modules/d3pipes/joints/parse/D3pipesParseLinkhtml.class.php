<?php

require_once dirname(dirname(__FILE__)).'/D3pipesParseAbstract.class.php' ;

class D3pipesParseLinkhtml extends D3pipesParseAbstract {

	function execute( $html_source , $max_entries = '' )
	{
		$items = array() ;

		$result = preg_match_all( $this->option , $html_source , $matches , PREG_SET_ORDER ) ;
		if( $result === false ) {
			$this->errors[] = 'Invalid pattern for this Parser'."\n($this->pipe_id)" ;
		}
		foreach( $matches as $match ) {
			if( preg_match( '#[0-9]{2,4}[/.-][0-9]{1,2}[/.-][0-9]{1,2}#' , $match[1] ) ) {
				$pubtime = strtotime( $match[1] ) ;
				$link = $match[2] ;
				$headline = $match[3] ;
			} else if( preg_match( '#[0-9]{2,4}[/.-][0-9]{1,2}[/.-][0-9]{1,2}#' , $match[3] ) ) {
				$pubtime = strtotime( $match[3] ) ;
				$link = $match[1] ;
				$headline = $match[2] ;
			} else {
				$pubtime = time() ;
				$link = $match[1] ;
				$headline = $match[2] ;
			}

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
		$index = intval( $index ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="40" /><br />'._MD_D3PIPES_N4J_WRITEPREG ;
	}

}

?>