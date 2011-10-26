<?php

require_once dirname(dirname(__FILE__)).'/D3pipesReplaceAbstract.class.php' ;

class D3pipesReplaceMbregex extends D3pipesReplaceAbstract {

	function execute( $data , $max_entries = '' )
	{
		if( is_array( $data ) ) {
			return array_map( array( $this , 'execute' ) , $data ) ;
		} else {
			return mbereg_replace( $this->pattern , $this->replacement , $data ) ;
		}
	}

}

?>