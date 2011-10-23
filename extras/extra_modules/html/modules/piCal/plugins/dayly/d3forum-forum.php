<?php

	// a plugin for d3forum

	if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

	/*
		$db : db instance
		$myts : MyTextSanitizer instance
		$this->year : year
		$this->month : month
		$this->date : date
		$this->week_start : sunday:0 monday:1
		$this->user_TZ : user's timezone (+1.5 etc)
		$this->server_TZ : server's timezone (-2.5 etc)
		$tzoffset_s2u : the offset from server to user
		$now : the result of time()
		$plugin = array('dirname'=>'dirname','name'=>'name','dotgif'=>'*.gif','options'=>'options')
		
		$plugin_returns[ DATE ][]
	*/

	// set range (added 86400 second margin "begin" & "end")
	$range_start_s = mktime(0,0,0,$this->month,$this->date-1,$this->year) ;
	$range_end_s = mktime(0,0,0,$this->month,$this->date+2,$this->year) ;

	// options
	$options = explode( '|' , $plugin['options'] ) ;
	// options[0] : category extract
	if( ! empty( $options[0] ) ) {
		$cat_ids = array_map( 'intval' , explode( ',' , $options[0] ) ) ;
		$whr_cat = 'f.cat_id IN (' . implode( ',' , $cat_ids ) . ')' ;
	} else {
		$whr_cat = '1' ;
	}

	// forums can be read by current viewer (check by forum_access)
	require_once XOOPS_TRUST_PATH.'/modules/d3forum/include/common_functions.php' ;
	$whr_forum = "f.forum_id IN (".implode(",",d3forum_get_forums_can_read( $plugin['dirname'] )).")" ;

	// query (added 86400 second margin "begin" & "end")
	$result = $db->query( "SELECT f.forum_title,f.forum_id,f.forum_last_post_time FROM ".$db->prefix($plugin['dirname']."_forums")." f WHERE ($whr_forum) AND ($whr_cat) AND f.forum_last_post_time >= $range_start_s AND f.forum_last_post_time < $range_end_s" ) ;

	while( list( $title , $id , $server_time ) = $db->fetchRow( $result ) ) {
		// sub query for the latest topic
		$result2 = $db->query( "SELECT t.topic_id,t.topic_title FROM ".$db->prefix($plugin['dirname']."_topics")." t WHERE topic_id=$id AND ! t.topic_invisible" ) ;		list( $topic_id , $topic_title ) = $db->fetchRow( $result2 ) ;
	
		$user_time = $server_time + $tzoffset_s2u ;
		if( date( 'j' , $user_time ) != $this->date ) continue ;
		$target_date = date('j',$user_time) ;
		$tmp_array = array(
			'dotgif' => $plugin['dotgif'] ,
			'dirname' => $plugin['dirname'] ,
			'link' => XOOPS_URL."/modules/{$plugin['dirname']}/index.php?forum_id=$id" , // &amp;caldate={$this->year}-{$this->month}-$target_date" ,
			'id' => $id ,
			'server_time' => $server_time ,
			'user_time' => $user_time ,
			'name' => 'forum_id' ,
			'title' => $myts->makeTboxData4Show( $title ) ,
			'description' => empty( $topic_title ) ? '' : $myts->makeTboxData4Show( $topic_title ) ,
		) ;

		// multiple gifs allowed per a plugin & per a day
		$plugin_returns[ $target_date ][] = $tmp_array ;
	}


?>