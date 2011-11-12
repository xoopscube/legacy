<?php

require_once dirname(dirname(__FILE__)).'/D3pipesClipAbstract.class.php' ;

class D3pipesClipModuledb extends D3pipesClipAbstract {

	// store
	function execute( $entries , $max_entries = 10 )
	{
		// delete expired clippings
		$this->removeExpired() ;

		$db =& Database::getInstance() ;
		$clip_table = $db->prefix( $this->mydirname.'_clippings' ) ;

		// count entries of current feed(return this number of entries instead of max_entries)
		$current_entry_size = sizeof( $entries ) ;

		// entries may be sorted by putime desc ...
		$entries = array_reverse( $entries ) ;

		foreach( $entries as $i => $entry ) {
			$fingerprint4sql = mysql_real_escape_string( @$entry['fingerprint'] ) ;
			if( empty( $fingerprint4sql ) ) continue ;
			list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM $clip_table WHERE fingerprint='$fingerprint4sql' AND pipe_id=$this->pipe_id" ) ) ;
			if( $count > 0 ) continue ;

			$pubtime4sql = empty( $entry['pubtime'] ) ? time() : intval( $entry['pubtime'] ) ;
			$link4sql = empty( $entry['link'] ) ? '' : mysql_real_escape_string( $entry['link'] ) ;
			$headline4sql = empty( $entry['headline'] ) ? '(no title)' : mysql_real_escape_string( $entry['headline'] ) ;

			$db->queryF( "INSERT INTO $clip_table (pipe_id,fingerprint,pubtime,link,headline,data,fetched_time) VALUES ($this->pipe_id,'$fingerprint4sql',$pubtime4sql,'$link4sql','$headline4sql','".mysql_real_escape_string(serialize($entry))."',UNIX_TIMESTAMP())" ) ;
		}

		return $this->getLatestClippings( $max_entries ) ;
	}

	// fetch multiple entries
	function getLatestClippings( $max_entries )
	{
		$db =& Database::getInstance() ;

		$clip_table = $db->prefix( $this->mydirname.'_clippings' ) ;

		$result = $db->query( "SELECT clipping_id,highlight,weight,comments_count ,fetched_time,can_search,link,fingerprint,data FROM $clip_table WHERE pipe_id=$this->pipe_id AND can_search ORDER BY pubtime DESC,clipping_id DESC LIMIT ".max($this->entries_from_clip,$max_entries) ) ;

		$entries = array() ;
		while( list( $clipping_id , $highlight , $weight , $comments_count , $fetched_time , $visible , $link , $fingerprint , $entry_serialized ) = $db->fetchRow( $result ) ) {
			$entry_row = d3pipes_common_unserialize( $entry_serialized ) ;
			if( ! is_array( $entry_row ) ) $entry_row = array() ;
			$entries[] = array(
				'link' => $link ,
				'fingerprint' => $fingerprint ,
				'clipping_id' => $clipping_id ,
				'pipe_id' => $this->pipe_id ,
				'clipping_highlight' => $highlight ,
				'clipping_weight' => $weight ,
				'clipping_fetched_time' => $fetched_time ,
				'comments_count' => $comments_count ,
				'visible' => $visible ,
			) + $entry_row ;
		}

		return $entries ;
	}


	// fetch single entry
	function getClipping( $clipping_id )
	{
		$db =& Database::getInstance() ;

		$clip_table = $db->prefix( $this->mydirname.'_clippings' ) ;

		$clipping_id = intval( $clipping_id ) ;
		list( $pipe_id , $highlight , $weight , $comments_count , $fetched_time , $visible , $link , $fingerprint , $data_serialized ) = $db->fetchRow( $db->query( "SELECT pipe_id,highlight,weight,comments_count,fetched_time,can_search,link,fingerprint,data FROM $clip_table WHERE clipping_id=$clipping_id" ) ) ;

		if( empty( $pipe_id ) ) return false ;

		$clipping = array(
			'link' => $link ,
			'fingerprint' => $fingerprint ,
			'clipping_id' => intval( $clipping_id ) ,
			'pipe_id' => intval( $pipe_id ) ,
			'clipping_highlight' => $highlight ,
			'clipping_weight' => $weight ,
			'clipping_fetched_time' => $fetched_time ,
			'comments_count' => $comments_count ,
			'visible' => $visible ,
		) ;

		if( empty( $data_serialized ) || empty( $visible ) ) return $clipping ;
		else return $clipping + d3pipes_common_unserialize( $data_serialized ) ;
	}


	// fetch entries in the range
	function getClippings( $pipe_id , $num , $pos = 0 )
	{
		$db =& Database::getInstance() ;

		$clip_table = $db->prefix( $this->mydirname.'_clippings' ) ;

		$entries = array() ;

		$pipe_id = intval( $pipe_id ) ;
		$num = intval( $num ) ;
		$pos = intval( $pos ) ;
		$result = $db->query( "SELECT clipping_id FROM $clip_table WHERE pipe_id=$pipe_id AND can_search ORDER BY pubtime DESC LIMIT $pos,$num" ) ;
		while( list( $clipping_id ) = $db->fetchRow( $result ) ) {
			$entries[] = $this->getClipping( $clipping_id ) ;
		}

		return $entries ;
	}


	// get entries count of the pipe
	function getClippingCount( $pipe_id )
	{
		$db =& Database::getInstance() ;

		$clip_table = $db->prefix( $this->mydirname.'_clippings' ) ;

		$pipe_id = intval( $pipe_id ) ;
		list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM $clip_table WHERE pipe_id=$pipe_id" ) ) ;

		return $count ;
	}


	function removeExpired()
	{
		$clip_life_time = isset( $this->clip_life_time ) ? intval( $this->clip_life_time ) : $this->mod_configs['removeclips_by_fetched'] * 86400 ;

		if( empty( $clip_life_time ) ) return ;

		$db =& Database::getInstance() ;

		$clip_table = $db->prefix( $this->mydirname.'_clippings' ) ;

		// d3forum integration
		$d3comment_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $this->mod_configs['comment_dirname'] ) ;
		$d3comment_forum_id = intval( $this->mod_configs['comment_forum_id'] ) ;
		if( ! file_exists( XOOPS_ROOT_PATH.'/modules/'.$d3comment_dirname.'/mytrustdirname.php' ) ) $d3comment_forum_id = 0 ;
		if( $d3comment_forum_id > 0 ) {
			$d3comment_join4sql = "LEFT JOIN ".$db->prefix($d3comment_dirname."_topics")." t ON t.forum_id=$d3comment_forum_id AND t.topic_external_link_id=c.clipping_id" ;
			$whr_d3comment = 't.topic_id IS NULL' ;
		} else {
			$d3comment_join4sql = '' ;
			$whr_d3comment = '1' ;
		}

		$whr = 'c.fetched_time < UNIX_TIMESTAMP() - '.$clip_life_time ;
		$result = $db->query( "SELECT c.clipping_id FROM $clip_table c $d3comment_join4sql WHERE $whr AND ! highlight AND ($whr_d3comment)" ) ;
		while( list( $clipping_id ) = $db->fetchRow( $result ) ) {
			$db->queryF( "DELETE FROM $clip_table WHERE clipping_id=$clipping_id" ) ;
		}

	}
}


?>
