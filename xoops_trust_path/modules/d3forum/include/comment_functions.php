<?php

require_once dirname(dirname(__FILE__)).'/class/D3commentAbstract.class.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;


// old interface
function d3forum_display_comment_topicscount( $mydirname , $forum_id , $params , $mode = 'post' , &$smarty )
{
	global $xoopsUser , $xoopsConfig ;

	$mydirpath = XOOPS_ROOT_PATH.'/modules/'.$mydirname ;
	$mytrustdirpath = dirname( dirname( __FILE__ ) ) ;

	$db =& Database::getInstance() ;

	// external_link_id
	if( ! empty( $params['link_id'] ) ) {
		$external_link_id = $params['link_id'] ;
	} else if( ! empty( $params['itemname'] ) ) {
		$external_link_id = @$_GET[ $params['itemname'] ] ;
	} else {
		echo "set valid link_id or itemname in the template" ;
		return ;
	}
	if( empty( $external_link_id ) ) return ;

	// check the d3forum exists and is active
	$module_hanlder =& xoops_gethandler( 'module' ) ;
	$module =& $module_hanlder->getByDirname( $mydirname ) ;
	if( ! is_object( $module ) || ! $module->getVar('isactive') ) {
		return ;
	}

	// check permission of "module_read"
	$moduleperm_handler =& xoops_gethandler( 'groupperm' ) ;
	$groups = is_object( $xoopsUser ) ? $xoopsUser->getGroups() : array( XOOPS_GROUP_ANONYMOUS ) ;
	if( ! $moduleperm_handler->checkRight( 'module_read' , $module->getVar( 'mid' ) , $groups ) ) {
		return ;
	}

	$select = $mode == 'topic' ? 'COUNT(t.topic_id)' : 'SUM(t.topic_posts_count)' ;

	$sql = "SELECT $select FROM ".$db->prefix($mydirname."_topics")." t WHERE t.forum_id=$forum_id AND ! t.topic_invisible AND topic_external_link_id='".addslashes($external_link_id)."'" ;
	if( ! $trs = $db->query( $sql ) ) die( 'd3forum_comment_error in '.__LINE__ ) ;
	list( $count ) = $db->fetchRow( $trs ) ;

	// return $count as "var" or echo directly
	$var_name = @$params['item'] . @$params['assign'] . @$params['var'] ;
	if( is_object( $smarty ) && ! empty( $var_name ) ) {
		$smarty->assign( $var_name , intval( $count ) ) ;
	} else {
		echo intval( $count ) ;
	}
}


// old interface
function d3forum_display_comment( $mydirname , $forum_id , $params )
{
	global $xoopsUser , $xoopsConfig , $xoopsModule ;

	// check the d3forum exists and is active
	$module_hanlder =& xoops_gethandler( 'module' ) ;
	$module =& $module_hanlder->getByDirname( $mydirname ) ;
	if( ! is_object( $module ) || ! $module->getVar('isactive') ) {
		return ;
	}

	// check permission of "module_read"
	$moduleperm_handler =& xoops_gethandler( 'groupperm' ) ;
	$groups = is_object( $xoopsUser ) ? $xoopsUser->getGroups() : array( XOOPS_GROUP_ANONYMOUS ) ;
	if( ! $moduleperm_handler->checkRight( 'module_read' , $module->getVar( 'mid' ) , $groups ) ) {
		return ;
	}

	// subject_raw
	$params['subject_raw'] = empty( $params['subject_escaped'] ) ? @$params['subject'] : d3forum_common_unhtmlspecialchars( @$params['subject'] ) ;

	// read d3comment class and make the object
	// for using d3forum_comment plugin with d3com class
	if( ! empty( $params['class'] ) ) {
		$class_name = preg_replace( '/[^0-9a-zA-Z_]/' , '' , $params['class'] ) ;
		$external_dirname = @$params['mydirname'] ;
		$external_trustdirname = @$params['mytrustdirname'] ;

		// auto external_dirname
		if( $external_dirname == '' && is_object( $GLOBALS['xoopsModule'] ) ) {
			$external_dirname = $GLOBALS['xoopsModule']->getVar('dirname') ;
		}

		// naao from
		require_once dirname(dirname(__FILE__)).'/class/D3commentObj.class.php' ;

		// search and include the class file
		if( $external_trustdirname && file_exists( XOOPS_TRUST_PATH."/modules/$external_trustdirname/class/{$class_name}.class.php" ) ) {
			require_once XOOPS_TRUST_PATH."/modules/$external_trustdirname/class/{$class_name}.class.php" ;
		} else if( $external_dirname && file_exists( XOOPS_ROOT_PATH."/modules/$external_dirname/class/{$class_name}.class.php" ) ) {
			require_once XOOPS_ROOT_PATH."/modules/$external_dirname/class/{$class_name}.class.php" ;
		} else {
			include_once dirname(dirname(__FILE__))."/class/{$class_name}.class.php" ;
			$external_dirname = '' ;
			$external_trustdirname = '' ;
		}
		
		$m_params['forum_dirname'] = $mydirname ;
		$m_params['external_dirname'] = $external_dirname ;
		$m_params['class_name'] = $class_name ;
		$m_params['external_trustdirname'] = $external_trustdirname  ;

		if( class_exists( $class_name ) ) {
			$obj =& D3commentObj::getInstance ( $m_params ) ;
			$external_link_id = $obj->d3comObj->external_link_id( $params ) ;
		}
		// naao to
	}

	// for conventional module
	if( ! is_object( $obj->d3comObj ) ) {
		if( ! empty( $params['itemname'] ) ) {
			$external_link_id = @$_GET[ $params['itemname'] ] ;
			if( empty( $external_link_id ) ) return ;
		} else {
			echo "set valid itemname or class in <{d3forum_comment}> of the template" ;
			return ;
		}
	}

	$params['external_link_id'] = $external_link_id ;
	$params['external_dirname'] = $external_dirname ;
	$params['external_trustdirname'] = $external_trustdirname ;

	$smarty = null ;
	d3forum_render_comments( $mydirname , $forum_id , $params , $smarty ) ;
}


function d3forum_render_comments( $mydirname , $forum_id , $params , &$smarty )
{
	global $xoopsUser , $xoopsConfig , $xoopsModule ;

	$mydirpath = XOOPS_ROOT_PATH.'/modules/'.$mydirname ;
	$mytrustdirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	$mytrustdirpath = dirname( dirname( __FILE__ ) ) ;

	$db =& Database::getInstance() ;

	// extract $external_* from $parms
	$external_link_id = $params['external_link_id'] ;
	$external_dirname = $params['external_dirname'] ;
	$external_trustdirname = $params['external_trustdirname'] ;

	// language files
	$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
	if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
	require_once( $langmanpath ) ;
	$langman =& D3LanguageManager::getInstance() ;
	$langman->read( 'main.php' , $mydirname , $mytrustdirname ) ;

	// local $xoopsModuleConfig
	$module_hanlder =& xoops_gethandler( 'module' ) ;
	$module =& $module_hanlder->getByDirname( $mydirname ) ;
	$config_handler =& xoops_gethandler( 'config' ) ;
	$xoopsModuleConfig =& $config_handler->getConfigsByCat( 0 , $module->getVar( 'mid' ) ) ;

	include dirname(__FILE__).'/common_prepend.php' ;

	$forum_id = intval( $forum_id ) ;
	if( ! include dirname(__FILE__).'/process_this_forum.inc.php' ) return ;

	if( ! include dirname(__FILE__).'/process_this_category.inc.php' ) return ;

	// get $odr_options, $solved_options, $query4assign
	//$query4nav = "forum_id=$forum_id" ;
	//include dirname(__FILE__).'/process_query4topics.inc.php' ;

	// force UPDATE forums.forum_external_link_format "(dirname)::(classname)::(trustdirname)"
	if( empty( $forum_row['forum_external_link_format'] ) && ! empty( $params['class'] ) ) {
		$db->queryF( "UPDATE ".$db->prefix($mydirname."_forums")." SET forum_external_link_format='".addslashes($external_dirname.'::'.$params['class'].'::'.$external_trustdirname)."' WHERE forum_id=$forum_id" ) ;
	}

	// check $params
	$params['posts_num'] = @$params['posts_num'] > 0 ? intval( $params['posts_num'] ) : 10 ;

	// INVISIBLE
	$whr_invisible = $isadminormod ? '1' : '! t.topic_invisible' ;

	/************ THREADED VIEW ************/
	if( @$params['view'] == 'listtopics' ) {

		$this_template = 'db:'.$mydirname.'_comment_listtopics.html' ;

		// number query
		$sql = "SELECT COUNT(t.topic_id) FROM ".$db->prefix($mydirname."_topics")." t LEFT JOIN ".$db->prefix($mydirname."_users2topics")." u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN ".$db->prefix($mydirname."_posts")." lp ON lp.post_id=t.topic_last_post_id LEFT JOIN ".$db->prefix($mydirname."_posts")." fp ON fp.post_id=t.topic_first_post_id WHERE t.forum_id=$forum_id AND ($whr_invisible) AND topic_external_link_id='".addslashes($external_link_id)."'" ;
		if( ! $trs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		list( $topic_hits ) = $db->fetchRow( $trs ) ;
		$post_hits = 0 ;
		
		// pagenav
		/*
		if( $topic_hits > $num ) {
			require_once XOOPS_ROOT_PATH.'/class/pagenav.php' ;
			$pagenav_obj = new XoopsPageNav( $topic_hits , $num , $pos , 'pos', $query4nav ) ;
			$pagenav = $pagenav_obj->renderNav() ;
		}
		*/

		$sql_order = @$params['order'] == 'asc' ? 'ASC' : 'DESC' ;

		// main query
		$sql = "SELECT t.*, lp.subject AS lp_subject, lp.icon AS lp_icon, lp.number_entity AS lp_number_entity, lp.special_entity AS lp_special_entity, fp.subject AS fp_subject, fp.icon AS fp_icon, fp.number_entity AS fp_number_entity, fp.special_entity AS fp_special_entity, u2t.u2t_time, u2t.u2t_marked, u2t.u2t_rsv FROM ".$db->prefix($mydirname."_topics")." t LEFT JOIN ".$db->prefix($mydirname."_users2topics")." u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN ".$db->prefix($mydirname."_posts")." lp ON lp.post_id=t.topic_last_post_id LEFT JOIN ".$db->prefix($mydirname."_posts")." fp ON fp.post_id=t.topic_first_post_id WHERE t.forum_id=$forum_id AND ($whr_invisible) AND topic_external_link_id='".addslashes($external_link_id)."' ORDER BY t.topic_last_post_time $sql_order" ;
		if( ! $trs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	
		// topics loop
		$topics = array() ;
		while( $topic_row = $db->fetchArray( $trs ) ) {
		
			$topic_id = intval( $topic_row['topic_id'] ) ;
		
			// get last poster's object
			$user_handler =& xoops_gethandler( 'user' ) ;
			$last_poster_obj =& $user_handler->get( intval( $topic_row['topic_last_uid'] ) ) ;
			$last_post_uname4html = is_object( $last_poster_obj ) ? $last_poster_obj->getVar( 'uname' ) : $xoopsConfig['anonymous'] ;
			$first_poster_obj =& $user_handler->get( intval( $topic_row['topic_first_uid'] ) ) ;
			$first_post_uname4html = is_object( $first_poster_obj ) ? $first_poster_obj->getVar( 'uname' ) : $xoopsConfig['anonymous'] ;
		
			// topics array
			$topics[] = array(
				'id' => $topic_row['topic_id'] ,
				'title' => $myts->makeTboxData4Show( $topic_row['topic_title'] , $topic_row['fp_number_entity'] , $topic_row['fp_special_entity'] ) ,
				'replies' => intval( $topic_row['topic_posts_count'] ) - 1 ,
				'views' => intval( $topic_row['topic_views'] ) ,
				'last_post_time' => intval( $topic_row['topic_last_post_time'] ) ,
				'last_post_time_formatted' => formatTimestamp( $topic_row['topic_last_post_time'] , 'm' ) ,
				'last_post_id' => intval( $topic_row['topic_last_post_id'] ) ,
				'last_post_icon' => intval( $topic_row['lp_icon'] ) ,
				'last_post_subject' => $myts->makeTboxData4Show( $topic_row['lp_subject'] , $topic_row['lp_number_entity'] , $topic_row['lp_special_entity'] ) ,
				'last_post_uid' => intval( $topic_row['topic_last_uid'] ) ,
				'last_post_uname' => $last_post_uname4html ,
				'first_post_time' => intval( $topic_row['topic_first_post_time'] ) ,
				'first_post_time_formatted' => formatTimestamp( $topic_row['topic_first_post_time'] , 'm' ) ,
				'first_post_id' => intval( $topic_row['topic_first_post_id'] ) ,
				'first_post_icon' => intval( $topic_row['fp_icon'] ) ,
				'first_post_subject' => $myts->makeTboxData4Show( $topic_row['fp_subject'] , $topic_row['fp_number_entity'] , $topic_row['fp_special_entity'] ) ,
				'first_post_uid' => intval( $topic_row['topic_first_uid'] ) ,
				'first_post_uname' => $first_post_uname4html ,
				'bit_new' => $topic_row['topic_last_post_time'] > @$topic_row['u2t_time'] ? 1 : 0 ,
				'bit_hot' => $topic_row['topic_posts_count'] > $xoopsModuleConfig['hot_threshold'] ? 1 : 0 ,
				'locked' => intval( $topic_row['topic_locked'] ) ,
				'sticky' => intval( $topic_row['topic_sticky'] ) ,
				'solved' => intval( $topic_row['topic_solved'] ) ,
				'invisible' => intval( $topic_row['topic_invisible'] ) ,
				'u2t_time' => intval( @$topic_row['u2t_time'] ) ,
				'u2t_marked' => intval( @$topic_row['u2t_marked'] ) ,
				'votes_count' => intval( $topic_row['topic_votes_count'] ) ,
				'votes_sum' => intval( $topic_row['topic_votes_sum'] ) ,
				'votes_avg' => round( $topic_row['topic_votes_sum'] / ( $topic_row['topic_votes_count'] - 0.0000001 ) , 2 ) ,
			) ;
		}
		$post = array() ;

	/************ DEFAULT VIEW (listposts_flat) ************/
	} else {
		$this_template = 'db:'.$mydirname.'_comment_listposts_flat.html' ;
		$posts = array() ;
		$sql_whr = "t.forum_id=$forum_id AND ($whr_invisible) AND t.topic_external_link_id='".addslashes($external_link_id)."'" ;

		// post order
		$postorder = 0 ;
		$postorder4sql = 'post_time DESC' ;
		if ( isset($params['order'] ) ) {
			if ( $params['order'] == 'asc' ) {
				$postorder = 1 ;
				$postorder4sql = 'post_time ASC' ;
			}
		}

	    // post_hits ~ pagenavi //naao from
		// count post
		$sql = "SELECT COUNT(post_id) FROM ".$db->prefix($mydirname."_posts")." p LEFT JOIN ".$db->prefix($mydirname."_topics")." t ON p.topic_id=t.topic_id WHERE $sql_whr" ;
		if( ! $nrs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		list( $post_hits ) = $db->fetchRow( $nrs ) ;

		// pagenav
		$pagenav = '' ;
		// LIMIT
		$num = $params['posts_num'] < 5 ? 5 : intval( $params['posts_num'] ) ;
		$pos = 0 ;
		if( $post_hits > $num ) {
			// POS
			$pos = isset( $_GET['d3f_pos'] ) ? intval( $_GET['d3f_pos'] ) 
				: (($postorder==1) ? (int)(($post_hits-1) / $num) * $num : 0) ;

	            if( !empty($_SERVER['QUERY_STRING'])) {
 	               if( preg_match("/^d3f_pos=[0-9]+/", $_SERVER['QUERY_STRING']) ) {
	                    $query4nav = "";
	                } else {
	                    $query4nav = preg_replace("/^(.*)\&d3f_pos=[0-9]+/", "$1", $_SERVER['QUERY_STRING']);
	                }
	            } else {
	                $query4nav = "";
	            }
			require_once dirname( dirname(__FILE__) ).'/class/D3forumPagenav.class.php' ;
			$pagenav_obj = new D3forumPagenav( $post_hits , $num , $pos , 'd3f_pos', $query4nav ) ;
			$pagenav = $pagenav_obj->getNav() ;
		}

	    // post_hits ~ pagenavi //naao to

		$sql = "SELECT p.*,t.topic_locked FROM ".$db->prefix($mydirname."_posts")." p LEFT JOIN ".$db->prefix($mydirname."_topics")." t ON p.topic_id=t.topic_id WHERE $sql_whr ORDER BY $postorder4sql LIMIT $pos,$num" ; //naao
		if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

		while( $post_row = $db->fetchArray( $prs ) ) {
		
			// get poster's information ($poster_*), $can_reply, $can_edit, $can_delete
			$topic_row = array( 'topic_locked' => $post_row['topic_locked'] ) ;
			include dirname(__FILE__).'/process_eachpost.inc.php' ;
		
			// posts array
			$posts[] = array(
				'id' => intval( $post_row['post_id'] ) ,
				'subject' => $myts->makeTboxData4Show( $post_row['subject'] , $post_row['number_entity'] , $post_row['special_entity'] ) ,
				'pid' => intval( $post_row['pid'] ),
				'topic_id' => intval( $post_row['topic_id'] ),
				'post_time' => intval( $post_row['post_time'] ) ,
				'post_time_formatted' => formatTimestamp( $post_row['post_time'] , 'm' ) ,
				'modified_time' => intval( $post_row['modified_time'] ) ,
				'modified_time_formatted' => formatTimestamp( $post_row['modified_time'] , 'm' ) ,
				'poster_uid' => intval( $post_row['uid'] ) ,
				'poster_uname' => $poster_uname4disp ,
				'poster_ip' => htmlspecialchars( $post_row['poster_ip'] , ENT_QUOTES ) ,
				'poster_rank_title' => $poster_rank_title4disp ,
				'poster_rank_image' => $poster_rank_image4disp ,
				'poster_is_online' => $poster_is_online ,
				'poster_avatar' => $poster_avatar ,
				'poster_posts_count' => $poster_posts_count ,
				'poster_regdate' => $poster_regdate ,
				'poster_regdate_formatted' => formatTimestamp( $poster_regdate , 's' ) ,
				'poster_from' => $poster_from4disp ,
				'modifier_ip' => htmlspecialchars( $post_row['poster_ip'] , ENT_QUOTES ) ,
				'html' => intval( $post_row['html'] ) ,
				'smiley' => intval( $post_row['smiley'] ) ,
				'br' => intval( $post_row['br'] ) ,
				'xcode' => intval( $post_row['xcode'] ) ,
				'icon' => intval( $post_row['icon'] ) ,
				'attachsig' => intval( $post_row['attachsig'] ) ,
				'signature' => $signature4disp ,
				'invisible' => intval( $post_row['invisible'] ) ,
				'approval' => intval( $post_row['approval'] ) ,
				'uid_hidden' => intval( $post_row['uid_hidden'] ) ,
				'depth_in_tree' => intval( $post_row['depth_in_tree'] ) ,
				'order_in_tree' => intval( $post_row['order_in_tree'] ) ,
				'unique_path' => htmlspecialchars( substr( $post_row['unique_path'] , 1 ) , ENT_QUOTES ) ,
				'votes_count' => intval( $post_row['votes_count'] ) ,
				'votes_sum' => intval( $post_row['votes_sum'] ) ,
				'votes_avg' => round( $post_row['votes_sum'] / ( $post_row['votes_count'] - 0.0000001 ) , 2 ) ,
				'past_vote' => -1 , // TODO
				'guest_name' => $myts->makeTboxData4Show( $post_row['guest_name'] ) ,
				'guest_email' => $myts->makeTboxData4Show( $post_row['guest_email'] ) ,
				'guest_url' => $myts->makeTboxUrl4Show( $post_row['guest_url'] ) ,
				'guest_trip' => $myts->makeTboxData4Show( $post_row['guest_trip'] ) ,
				'post_text' => $myts->displayTarea( $post_row['post_text'] , $post_row['html'] , $post_row['smiley'] , $post_row['xcode'] , $xoopsModuleConfig['allow_textimg'] , $post_row['br'] , 0 , $post_row['number_entity'] , $post_row['special_entity'] ) ,
				'post_text_raw' => $post_row['post_text'] , // caution
				'can_edit' => $can_edit ,
				'can_delete' => $can_delete ,
				'can_reply' => $can_reply ,
				'can_vote' => $can_vote ,
			) ;
		}

		//if( @$params['order'] == 'asc' && !empty($posts)) {
		//	$posts = array_reverse( $posts ) ; // thx naao
		//}

		$topics = array() ;

	// form elements or javascripts for anti-SPAM
	if( d3forum_common_is_necessary_antispam( $xoopsUser , $xoopsModuleConfig ) ) {
		$antispam_obj =& d3forum_common_get_antispam_object( $xoopsModuleConfig ) ;
		$antispam4assign = $antispam_obj->getHtml4Assign() ;
	} else {
		$antispam4assign = array() ;
	}

	// naao from
	if( is_object( $xoopsUser ) ) {
		if ($xoopsModuleConfig['use_name'] == 1 && $xoopsUser->getVar( 'name' ) ) {
			$poster_uname4disp = $xoopsUser->getVar( 'name' ) ;
		} else {
			$poster_uname4disp = $xoopsUser->getVar( 'uname' ) ;
		}
	} else { $poster_uname4disp = '' ;}
	// naao to

	}

$tree = array();
if( $external_link_id >0 ) {

	$sql = "SELECT p.*, t.topic_locked, t.topic_id, t.forum_id, t.topic_last_uid, t.topic_last_post_time 
		FROM ".$db->prefix($mydirname."_topics")." t 
		LEFT JOIN ".$db->prefix($mydirname."_posts")." p ON p.topic_id=t.topic_id 
		WHERE t.forum_id=$forum_id AND ($whr_invisible) AND p.depth_in_tree='0' 
			AND (t.topic_external_link_id='".addslashes($external_link_id)."' ) " ;		//naao
	if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	while( $post_row = $db->fetchArray( $prs ) ) {
		// topics array
		$topic_last_uid = intval( $post_row['topic_last_uid'] ) ;
		$topic_last_post_time = intval( $post_row['topic_last_post_time'] ) ;
		$topic_last_uname = XoopsUser::getUnameFromId( $topic_last_uid , $xoopsModuleConfigs['use_name']) ; //naao usereal=1
		$topic_last_uname = $topic_last_uid > 0 ? $topic_last_uname : $myts->makeTboxData4Show( $post_row['guest_name'] ) ;

		$tree[] = array(
			'id' => intval( $post_row['post_id'] ) ,
			'subject' => $myts->makeTboxData4Show( $post_row['subject'] , $post_row['number_entity'] ,
					 $post_row['special_entity'] ) ,
			'post_time_formatted' => formatTimestamp( $post_row['post_time'] , 'm' ) ,
			'poster_uid' => $topic_last_uid ,
			'poster_uname' => $topic_last_uname ,
			'icon' => intval( $post_row['icon'] ) ,
			'depth_in_tree' => intval( $post_row['depth_in_tree'] ) ,
			'order_in_tree' => intval( $post_row['order_in_tree'] ) ,
			'topic_id' => intval( $post_row['topic_id'] ) ,
			'ul_in' => '<ul><li>' ,
			'ul_out' => '</li></ul>' ,
		);
	}
		$topics_count = count($tree) ;
}
	// naao to

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign(
		array(
			'mydirname' => $mydirname ,
			'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
			'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
			'mod_config' => $xoopsModuleConfig ,
			'uid' => $uid ,
			'uname' => $poster_uname4disp ,
			'subject_raw' => $params['subject_raw'] ,
			'postorder' => $postorder ,
			'icon_meanings' => $d3forum_icon_meanings ,
			'category' => $category4assign ,
			'forum' => $forum4assign ,
			'topics' => $topics ,
			'topic_hits' => intval( @$topic_hits ) ,
			'post_hits' => intval( @$post_hits ) ,
			'posts' => $posts ,
			'odr_options' => @$odr_options ,
			'solved_options' => @$solved_options ,
//			'query' => $query4assign ,
			'pagenav' => $pagenav ,	//naao
			'pos' => $pos ,		//naao
			'external_link_id' => $external_link_id ,
			'page' => 'listtopics' ,
			'plugin_params' => $params ,
			'xoops_pagetitle' => $forum4assign['title'] ,
			'antispam' => $antispam4assign ,
		)
	) ;
	// naao from
	if( @$params['view'] != 'listtopics' && $external_link_id >0) {
		$tpl->assign(
			array(
				'tree' => $tree ,	// naao
				'tree_tp_count' => $topics_count ,	// naao
			)
		) ;
	}
	// naao to
	$tpl->display( $this_template ) ;
}



?>