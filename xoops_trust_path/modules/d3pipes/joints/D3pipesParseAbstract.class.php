<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesParseAbstract extends D3pipesJointAbstract {

	var $option ;

	// constructor
	function D3pipesParseAbstract( $mydirname , $pipe_id , $option )
	{
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		$this->option = $option ;
	}

	// virtual
	function execute( $xml_source , $max_entries = '' ) {}

	// utility
	function dateToUnix( $date_formatted )
	{
		if( preg_match( '/^[a-zA-Z]/' , trim( $date_formatted ) ) ) {
			$localunixtime = strtotime( $date_formatted ) ;
			if( $localunixtime != -1 ) return $localunixtime ;
		}
	
		// It must be like a W3C Format
		$w3cDT = strtoupper( $date_formatted ) ;

		// for wrong format like dd-mm-yyyy hh:mm:ss
		if( preg_match( '/^(\d{1,2})[-\/\.](\d{1,2})[-\/\.](\d{4})(.*)$/' , $w3cDT , $regs ) ) {
			$w3cDT = "{$regs[3]}-{$regs[2]}-{$regs[1]}{$regs[4]}" ;
		}

		// for too detailed format like ss.ssss
		$w3cDT = preg_replace( '/T(\d{2})\:(\d{2})\:(\d{2})\.\d+/' , 'T$1:$2:$3' , $w3cDT ) ;

		// get the timezone
		$tzoffset = date( 'Z' ) ;
		if( $pos = strrpos( $w3cDT , 'Z' ) ) {
			// GMT
			$localdatetime = substr( $w3cDT , 0 , $pos ) ;
		} else if( ( $pos = strrpos( $w3cDT , '+' ) ) > 0 ) {
			$tzoffset -= $this->parseTimezoneOffset( substr( $w3cDT , $pos + 1 ) ) ;
			$localdatetime = substr( $w3cDT , 0 , $pos ) ;
			$this->previous_tzoffset = $tzoffset ;
		} else if( ( $pos = strrpos( $w3cDT , '-' ) ) > 7 ) {
			$tzoffset += $this->parseTimezoneOffset( substr( $w3cDT , $pos + 1 ) ) ;
			$localdatetime = substr( $w3cDT , 0 , $pos ) ;
			$this->previous_tzoffset = $tzoffset ;
		} else {
			// no timezone (use previous tzoffset if exists)
			$localdatetime = $w3cDT ;
			if( isset( $this->previous_tzoffset ) ) $tzoffset = $this->previous_tzoffset ;
		}

		$localunixtime = strtotime( str_replace( 'T' , ' ' , $localdatetime ) ) ;
		if( $localunixtime == -1 ) return time() ;
		else return $localunixtime + $tzoffset ;
	}

	// utility
	function parseTimezoneOffset( $text )
	{
		if( strstr( $text , ':' ) ) {
			list( $hour , $min ) = explode( ':' , $text ) ;
		} else {
			$hour = substr( $text , 0 , 2 ) ;
			$min = substr( $text , 2 , 2 ) ;
		}

		return @$hour * 3600 + @$min * 60 ;
	}

}


?>