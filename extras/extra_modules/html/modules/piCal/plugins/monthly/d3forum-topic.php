<?php

	// a plugin for d3forum

	if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

	/*
		$db : db instance
		$myts : MyTextSanitizer instance
		$this->year : year
		$this->month : month
		$this->user_TZ : user's timezone (+1.5 etc)
		$this->server_TZ : server's timezone (-2.5 etc)
		$tzoffset_s2u : the offset from server to user
		$now : the result of time()
		$plugin = array('dirname'=>'dirname','name'=>'name','dotgif'=>'*.gif','options'=>'options')
		$just1gif : 0 or 1
		
		$plugin_returns[ DATE ][]
	*/

	// set range (added 86400 second margin "begin" & "end")
	$range_start_s = mktime(0,0,0,$this->month,0,$this->year) ;
	$range_end_s = mktime(0,0,0,$this->month+1,1,$this->year) ;

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
	$whr_forum = "t.forum_id IN (".implode(",",d3forum_get_forums_can_read( $plugin['dirname'] )).")" ;

	// query (added 86400 second margin "begin" & "end")
	$sql = "SELECT t.topic_title,t.topic_id,t.topic_last_post_time,t.topic_external_link_id,f.forum_id,f.forum_external_link_format FROM ".$db->prefix($plugin['dirname']."_topics")." t LEFT JOIN ".$db->prefix($plugin['dirname']."_forums")." f ON f.forum_id=t.forum_id WHERE ! t.topic_invisible AND ($whr_forum) AND ($whr_cat) AND t.topic_last_post_time >= $range_start_s AND t.topic_last_post_time < $range_end_s" ;
	$result = $db->query( $sql ) ;

	while( list( $title , $id , $server_time , $topic_external_link_id , $forum_id , $forum_external_link_format ) = $db->fetchRow( $result ) ) {

		$canread_topic = true ;
		if ( $forum_external_link_format ) {
			require_once XOOPS_TRUST_PATH.'/modules/d3forum/include/main_functions.php' ;
			$d3com =& d3forum_main_get_comment_object( $plugin['dirname'] , $forum_external_link_format);
			$canread_topic = $d3com->validate_id($topic_external_link_id);
		}

		if ( $canread_topic ) {
			$user_time = $server_time + $tzoffset_s2u ;
			if( date( 'n' , $user_time ) != $this->month ) continue ;
			$target_date = date('j',$user_time) ;
			$tmp_array = array(
				'dotgif' => $plugin['dotgif'] ,
				'dirname' => $plugin['dirname'] ,
				'link' => XOOPS_URL."/modules/{$plugin['dirname']}/index.php?topic_id=$id" , // &amp;caldate={$this->year}-{$this->month}-$target_date" ,
				'id' => $id ,
				'server_time' => $server_time ,
				'user_time' => $user_time ,
				'name' => 'topic_id' ,
				'title' => $myts->makeTboxData4Show( $title )
			) ;
			if( $just1gif ) {
				// just 1 gif per a plugin & per a day
				$plugin_returns[ $target_date ][ $plugin['dirname'] ] = $tmp_array ;
			} else {
				// multiple gifs allowed per a plugin & per a day
				$plugin_returns[ $target_date ][] = $tmp_array ;
			}
		}
	}

?>