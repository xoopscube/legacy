<?php

require_once dirname(dirname(__FILE__)).'/D3pipesPingAbstract.class.php' ;

class D3pipesPingXmlrpc2 extends D3pipesPingAbstract {

	function execute( $entries , $max_entries = 10 )
	{
		if( $this->isMatured() ) {
			if( empty( $entries ) ) {
				$this->touchCache() ;
			} else {
				// check if entries are updated by compairing md5 hash
				if( $this->fetchPrevMd5() == md5( serialize( $entries ) ) ) {
					$this->touchCache() ;
				} else {
					// send ping
					$this->sendPings() ;
					$this->storeMd5( $entries ) ;
				}
			}
		}
		return $entries ;
	}


	function sendPings()
	{
		$ping_bodies = $this->getPingBodies() ;

		$lines = array_map( 'trim' , explode( "\n" , $this->mod_configs['update_ping_servers'] ) ) ;
		foreach( $lines as $line ) {
			if( substr( $line , 0 , 4 ) != 'http' ) continue ;
			if( substr( $line , -2 ) == ' E' ) {
				// extended ping
				$this->postEachPing( trim( substr( $line , 0 , -2 ) ) , $ping_bodies[1] ) ;
			} else {
				// normal ping
				$this->postEachPing( $line , $ping_bodies[0] ) ;
			}
		}
	}


	function getPingBodies()
	{
		$tpl = new XoopsTpl() ;
		$tpl->assign( array(
			'site_name' => d3pipes_common_convert_encoding_ietoutf8( $this->mydirname , $GLOBALS['xoopsConfig']['sitename'] ) ,
			'site_url' => XOOPS_URL.'/' ,
			'page_url' => XOOPS_URL.'/modules/'.$this->mydirname.'/index.php?page=eachpipe&amp;pipe_id='.$this->pipe_id ,
			'rss_url' => XOOPS_URL.'/modules/'.$this->mydirname.'/index.php?page=xml&amp;style=rss20&amp;pipe_id='.$this->pipe_id ,
			) ) ;
		$ping_body = $tpl->fetch( 'db:'.$this->mydirname.'_main_xmlrpc2ping.html' ) ;
		$extended_ping_body = $tpl->fetch( 'db:'.$this->mydirname.'_main_xmlrpc2extendedping.html' ) ;
		return array( $ping_body , $extended_ping_body ) ;
	}


	function postEachPing( $url , $body )
	{
		$content_length = strlen( $body ) ;
		$URI_PARTS = parse_url( $url ) ;
		if( empty( $URI_PARTS['host'] ) || empty( $URI_PARTS['path'] ) ) return false ;

		$headers = '' ;
		$headers .= "POST ".$URI_PARTS['path']." HTTP/1.0\r\n" ;
		$headers .= "User-Agent: request\r\n" ;
		$headers .= "Host: ".$URI_PARTS['host']."\r\n" ;
		$headers .= "Content-Type: text/xml\r\n" ;
		$headers .= "Content-length: $content_length\r\n" ;
		$headers .= "\r\n" ;

		$fp = fsockopen( $URI_PARTS['host'] , 80 , $errno , $errstr , 10 ) ;
		if( ! $fp ) return false ;
		fwrite( $fp , $headers . $body , strlen( $headers . $body ) ) ;

		$response = fread( $fp , 65536 ) ;
		fclose( $fp ) ;

		// DEBUG
		//error_log( $headers . $body . "\n" , 3 , '/tmp/error_log' ) ;
		//error_log( $response . "\n" , 3 , '/tmp/error_log' ) ;

	}
}


?>