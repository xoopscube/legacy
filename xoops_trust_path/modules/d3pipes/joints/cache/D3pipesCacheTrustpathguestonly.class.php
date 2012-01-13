<?php

require_once dirname(__FILE__).'/D3pipesCacheTrustpatheachuser.class.php' ;

class D3pipesCacheTrustpathguestonly extends D3pipesCacheTrustpatheachuser {

	function execute( $entries , $max_entries = 10 )
	{
		global $xoopsUser;
		if( is_object( $xoopsUser ) ) {
			return $entries ;
		} else {
			return parent::execute( $entries , $max_entries );
		}
	}

}
