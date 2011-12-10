<?php

function b_bulletin_category_new_allowed_order()
{
	return array(
		_MB_BULLETIN_DATE . ' DESC' => 'published DESC' ,
		_MB_BULLETIN_HITS . ' DESC' => 'counter DESC' ,
		_MB_BULLETIN_DATE . ' ASC' => 'published ASC' ,
		_MB_BULLETIN_HITS . ' ASC' => 'counter ASC' ,
		'title' => 'title' ,
		'created DESC' => 'created DESC' ,
		'created ASC' => 'created ASC' ,
		'expired DESC' => 'expired DESC' ,
		'expired ASC' => 'expired ASC' ,
		'comments DESC' => 'comments DESC' ,
	) ;
}

function b_bulletin_category_new_show($options) {

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$selected_order = empty( $options[1] ) || ! in_array( $options[1] , b_bulletin_category_new_allowed_order() ) ? 'published DESC' : $options[1] ;
	$display_count = empty($options[2]) ? 0 :intval($options[2]);//Number display for each category
	$Length_title = empty($options[3]) ? 255 :intval($options[3]);//Length of the title
	$categories = empty($options[4]) ? 0 : array_map( 'intval' , explode( ',' , $options[4] ) ) ;//(0=show all)
	$show_body = empty($options[5]) ? 0 :intval($options[5]);//Number of articles showing body for each category
	$show_category_icon = empty($options[6]) ? 0 :intval($options[6]);//(yes or no) ,Display category icon
//ver3.0 fix
	if ($show_body > $display_count){
		$show_body = $display_count;
	}

	require dirname( dirname( __FILE__ ) ).'/include/configs.inc.php';
	require_once XOOPS_ROOT_PATH.'/class/xoopstree.php';
	require_once dirname(dirname(__FILE__)).'/class/bulletingp.php' ;

	$mytree = new XoopsTree($table_topics,'topic_id','topic_pid');

	$arr = array();
//ver3.0 can_read access
	$gperm =& BulletinGP::getInstance($mydirname) ;
	$can_read_topic_ids = $gperm->makeOnTopics('can_read');
	if (empty($can_read_topic_ids)){
		return false;
	}

	// Query to get the root category
		$sql_topics = "SELECT topic_id, topic_title, topic_imgurl";
		$sql_topics .= " FROM ".$xoopsDB->prefix($mydirname."_topics") ;
	if( empty($categories) ){
		// Get categories all from route
		$sql_topics .= " WHERE topic_pid=0";
		$sql_topics .= " AND topic_id IN (".implode(",",$can_read_topic_ids).")";
		$sql_topics .= " ORDER BY topic_title";
	}else{
		// when category
		$sql_topics .= " WHERE topic_id IN (".implode(",",$categories).")";
		$sql_topics .= " AND topic_id IN (".implode(",",$can_read_topic_ids).")";
	}
	$result = $xoopsDB->query($sql_topics);
	$topics_count = $xoopsDB->getRowsNum($result);
	if (empty($topics_count)){
		return false;
	}

	$block = array();

	while( list($topic_id, $topic_title, $topic_imgurl) = $xoopsDB->fetchRow($result) ){
		$topic = array();
		$topic['title'] = $myts->makeTboxData4Show($topic_title);
		$topic['id'] = $topic_id;

		// Set the image topic
		if ($topic_imgurl != '' && file_exists($bulletin_topicon_path.$topic_imgurl) && $show_category_icon) {
			$topic['topic_url'] = str_replace(XOOPS_ROOT_PATH,XOOPS_URL,$bulletin_topicon_path).$topic_imgurl;
			$topic['show_category_icon'] = 1;
		}else{
			$topic['show_category_icon'] = 0;
		}

		$where = sprintf("s.published < %u AND s.published > 0 AND (s.expired = 0 OR s.expired > %1\$u) AND s.block = 1 AND (s.topicid=%u", time(), $topic_id);
		if (!$gperm->group_perm(2)){
			$where .= " AND s.type > 0";
		}

		// View the directory to include children categorys
		$arr = $mytree->getAllChildId($topic_id);
		$size = count($arr);
		for($i=0;$i<$size;$i++){
			$where .= " OR s.topicid=".$arr[$i];
		}
		$where .= ")";
//ver3.0 can_read access
		$where .= " AND s.topicid IN (".implode(',',$can_read_topic_ids).")";

		// see more... for topics
		$sql = sprintf("SELECT COUNT(*) FROM %s s WHERE %s", $xoopsDB->prefix($mydirname."_stories"), $where);
		list($count) = $xoopsDB->fetchRow($xoopsDB->query($sql));
		if($count>$display_count){
			$topic['morelink'] = 1;
		}
		$topic['lang_morelink'] = _MORE;

//ver3.0
		$sql = "SELECT s.*, t.topic_pid, t.topic_imgurl, t.topic_title, t.topic_created, t.topic_modified";
		$sql .= " FROM " . $xoopsDB->prefix($mydirname."_stories") . " s, " . $xoopsDB->prefix($mydirname."_topics") . " t";
		$sql .= " WHERE " . $where . " AND s.topicid = t.topic_id";
		$sql .= " ORDER BY $selected_order";

		// Show body
		if($show_body > 0){

//ver2.0	$sql  = sprintf('SELECT s.storyid, s.topicid, s.title, s.hometext, s.bodytext, s.published, s.expired, s.counter, s.comments, s.uid, s.topicimg, s.html, s.smiley, s.br, s.xcode, t.topic_title, t. topic_imgurl FROM %s s, %s t WHERE %s AND s.topicid = t.topic_id %s', $table_stories, $table_topics, $where, $order);

			$result2 = $xoopsDB->query($sql,$show_body,0);

			while ( $myrow = $xoopsDB->fetchArray($result2) ) {
				$fullstory = array() ; // GIJ
				$fullstory['id']       = $myrow['storyid'];
				$fullstory['posttime'] = formatTimestamp($myrow['published'], $bulletin_date_format);
				$fullstory['date']     = formatTimestamp($myrow['published'], $bulletin_date_format);
				$fullstory['published'] = intval($myrow['published']);
				$fullstory['topicid']  = $myrow['topicid'];
				$fullstory['topic']    = $myts->makeTboxData4Show($myrow['topic_title']);
				$fullstory['title']    = $myts->makeTboxData4Show($myrow['title']);
				$fullstory['text']     = $myts->displayTarea($myrow['hometext'],$myrow['html'],$myrow['smiley'],$myrow['xcode'],1,$myrow['br']);
				$fullstory['hits']     = $myrow['counter'];
				$fullstory['title_link'] = true;
				//Assign the user information
				$fullstory['uid']      = $myrow['uid'];
				$fullstory['uname']    = XoopsUser::getUnameFromId($myrow['uid']);
				$fullstory['realname'] = XoopsUser::getUnameFromId($myrow['uid'], 1);
				$fullstory['morelink'] = '';

				// Length counting process
				if ( myStrlenText($myrow['bodytext']) > 1 ) {
					$fullstory['bytes']    = sprintf(_MB_BULLETIN_BYTESMORE, myStrlenText($myrow['bodytext']));
					$fullstory['readmore'] = true;
				}else{
					$fullstory['bytes']    = 0;
					$fullstory['readmore'] = false;
				}

				// Assign a number of comments
				$ccount = $myrow['comments'];
				if( $ccount == 0 ){
					$fullstory['comentstotal'] = _MB_BULLETIN_COMMENTS;
				}elseif( $ccount == 1 ) {
					$fullstory['comentstotal'] = _MB_BULLETIN_ONECOMMENT;
				}else{
					$fullstory['comentstotal'] = sprintf(_MB_BULLETIN_NUMCOMMENTS, $ccount);
				}

				// Links for administrato //old verssion
				$fullstory['adminlink'] = 0;

				// Image Topic
				if ( $myrow['topicimg'] ) {
					$fullstory['topic_url'] = makeTopicImgURL($bulletin_topicon_path, $myrow['topic_imgurl']);
					$fullstory['align']     = topicImgAlign($myrow['topicimg']);
				}
//ver3.0
				$topic_perm = $gperm->get_viewtopic_perm_of_current_user($myrow['topicid'] , $myrow['uid']);
				$fullstory = array_merge($fullstory,$topic_perm);

				$fullstory['type']     = $myrow['type'];
				$fullstory['raw_data'] = $myrow;

				$topic['fullstories'][] = $fullstory;

			}
		}

		if( $display_count - $show_body > 0 ){

//ver2.0	$sql  = sprintf('SELECT s.storyid, s.title, s.published, s.expired, s.counter, s.uid FROM %s s WHERE %s %s', $table_stories, $where, $order);

			$result3 = $xoopsDB->query($sql,$display_count-$show_body,$show_body);

			while ( $myrow = $xoopsDB->fetchArray($result3) ) {

				// Also supports multi-byte
				$story['title']    = $myts->makeTboxData4Show(xoops_substr($myrow['title'], 0 ,intval($Length_title) + 3, '...'));
				$story['id']       = $myrow['storyid'];
				$story['date']     = formatTimestamp($myrow['published'], $bulletin_date_format);
				$story['published'] = intval($myrow['published']);
				$story['hits']     = $myrow['counter'];
				$story['uid']      = $myrow['uid'];
				$story['uname']    = XoopsUser::getUnameFromId($myrow['uid']);
				$story['realname'] = XoopsUser::getUnameFromId($myrow['uid'], 1);

//ver3.0
				$topic_perm = $gperm->get_viewtopic_perm_of_current_user($myrow['topicid'] , $myrow['uid']);
				$story = array_merge($story,$topic_perm);
				$story['type']     = $myrow['type'];
				$story['raw_data'] = $myrow;

				$topic['stories'][] = $story;
			}

		}

		$block['topics'][] = $topic;
	}

	if( ! empty( $block ) ) {
		$block['mod_config'] = @$bulletin_configs ;
		$block['lang_postedby'] = _POSTEDBY;
		$block['lang_on']       = _ON;
		$block['lang_reads']    = _READS;
		$block['lang_readmore'] = _MB_BULLETIN_READMORE;
		$block['type']  = $selected_order;
		$block['mydirurl'] = XOOPS_URL.'/modules/'.$mydirname;;
		$block['mydirname'] = $mydirname;
	}

	return $block;
}

function b_bulletin_category_new_edit($options) {

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$selected_order = empty( $options[1] ) || ! in_array( $options[1] , b_bulletin_category_new_allowed_order() ) ? 'published DESC' : $options[1] ;

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname ,
		'options' => $options ,
		'order_options' => array_flip( b_bulletin_category_new_allowed_order() ) ,
		'selected_order' => $selected_order ,
		'yn_options' => array( 0 => _NO , 1 => _YES ) ,
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_category_new.html' ) ;
}
?>