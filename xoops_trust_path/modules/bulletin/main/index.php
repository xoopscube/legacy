<?php
require XOOPS_ROOT_PATH.'/header.php';

$storytopic = isset($_GET['storytopic']) ? intval($_GET['storytopic']) : 0 ;
$storynum   = isset($_GET['storynum'])   ? intval($_GET['storynum'])   : $bulletin_storyhome;
$start      = isset($_GET['start'])      ? intval($_GET['start'])      : 0 ;
$caldate    = isset($_GET['caldate'])    ? $_GET['caldate']            : '' ;
$storynum   = ($storynum > 30)           ? $bulletin_storyhome         : $storynum ;

/*
 * comment list for storytopic
 */
$op = isset($_GET['op']) ? $_GET['op'] : "" ;
// By yoshis op=comments is d3forum comment
if ($op == "comments" && !empty($xoopsModuleConfig['comment_dirname']) && !empty($xoopsModuleConfig['comment_forum_id']) ){
//ver3.0 can_read access
	$gperm =& BulletinGP::getInstance($mydirname) ;
	$can_read_topic_ids = $gperm->makeOnTopics('can_read');
	if (empty($can_read_topic_ids)){
		die(_NOPERM);
	}

	$cmttbl = $xoopsModuleConfig['comment_dirname'];
	$whr_forum = "t.forum_id=" . $xoopsModuleConfig['comment_forum_id'];
	$odr = $xoopsModuleConfig['comment_order'];
	$posttbl = $commenttbl . "_posts";
//TODO when over count display
	if($storytopic){
		$comments = array();
		$sql = "SELECT b.topic_id,b.topic_title,s.storyid,p.uid,u.uname,p.post_time,p.post_text FROM "
		.$xoopsDB->prefix($mydirname."_topics")." b LEFT JOIN "
		.$xoopsDB->prefix($mydirname."_stories")." s ON b.topic_id=s.topicid LEFT JOIN "
		.$xoopsDB->prefix($cmttbl."_topics")." t ON s.storyid=t.topic_external_link_id LEFT JOIN "
		.$xoopsDB->prefix($cmttbl."_posts")." p ON t.topic_last_post_id=p.post_id LEFT JOIN "
		.$xoopsDB->prefix("users")." u ON p.uid=u.uid "
		."WHERE ! t.topic_invisible AND (".$whr_forum." AND b.topic_id=".$storytopic.")"
		." AND b.topic_id IN (".implode(",",$can_read_topic_ids).")"
		." ORDER BY p.post_time ".$odr ;
		$ret=$xoopsDB->query($sql,50,0);//TODO Limit display
		while($myrow=$xoopsDB->fetchArray($ret)){
			$topic_id = $myrow['topic_id'];
			$topic_title = $myrow['topic_title'];
			$comments[]=$myrow;
		}
		$xoopsTpl->assign('topic_id', $topic_id);
		$xoopsTpl->assign('topic_title', $topic_title);
		$xoopsTpl->assign('comments', $comments);
		$xoopsOption['template_main'] = "{$mydirname}_comments.html";
	}else{
		$topicInfo = array();
		//TODO
		$sql = "SELECT b.topic_id,b.topic_title,count(p.post_id) as comment FROM "
		.$xoopsDB->prefix($mydirname."_topics")." b LEFT JOIN "
		.$xoopsDB->prefix($mydirname."_stories")." s ON b.topic_id=s.topicid LEFT JOIN "
		.$xoopsDB->prefix($cmttbl."_topics")." t ON s.storyid=t.topic_external_link_id LEFT JOIN "
		.$xoopsDB->prefix($cmttbl."_posts")." p ON t.topic_last_post_id=p.post_id "
		."WHERE ! t.topic_invisible AND (".$whr_forum." )"
		." AND b.topic_id IN (".implode(",",$can_read_topic_ids).")"
		." GROUP BY b.topic_id";
		$ret=$xoopsDB->query($sql);
		while($myrow=$xoopsDB->fetchArray($ret)){
			$topicInfo[$myrow['topic_id']]['topic_id']=$myrow['topic_id'];
			$topicInfo[$myrow['topic_id']]['title']=$myrow['topic_title'];
			$topicInfo[$myrow['topic_id']]['comment']=$myrow['comment'];
		}
/*TODO iine_vote
		$sql = "SELECT b.topic_id,b.topic_title,count(i.content_id) as iine FROM "
		.$xoopsDB->prefix($mydirname."_topics")." b LEFT JOIN "
		.$xoopsDB->prefix($mydirname."_stories")." s ON b.topic_id=s.topicid LEFT JOIN "
		.$xoopsDB->prefix("iine_votes")." i ON s.storyid=i.content_id WHERE i.dirname='".$mydirname."' "
		." AND b.topic_id IN (".implode(",",$can_read_topic_ids).")"
		."GROUP BY b.topic_id";
		$ret=$xoopsDB->query($sql);
		while($myrow=$xoopsDB->fetchArray($ret)){
			$topicInfo[$myrow['topic_id']]['topic_id']=$myrow['topic_id'];
			$topicInfo[$myrow['topic_id']]['title']=$myrow['topic_title'];
			$topicInfo[$myrow['topic_id']]['iine']=$myrow['iine'];
		}
*/
		if (defined('LEGACY_MODULE_VERSION') && version_compare(LEGACY_MODULE_VERSION, '2.2', '>=')) {
		// For XCL 2.2
			$sql = "SELECT b.topic_id,b.topic_title,count(m.inbox_id) as message FROM "
			.$xoopsDB->prefix($mydirname."_topics")." b LEFT JOIN "
			.$xoopsDB->prefix($mydirname."_stories")." s ON b.topic_id=s.topicid LEFT JOIN "
			.$xoopsDB->prefix("message_inbox")." m ON m.from_uid=s.uid "
			."WHERE m.uid=" . $xoopsUser->uid()
			." AND b.topic_id IN (".implode(",",$can_read_topic_ids).")"
			." GROUP BY b.topic_id";
			$ret=$xoopsDB->query($sql);
			while($myrow=$xoopsDB->fetchArray($ret)){
				$topicInfo[$myrow['topic_id']]['topic_id']=$myrow['topic_id'];
				$topicInfo[$myrow['topic_id']]['title']=$myrow['topic_title'];
				$topicInfo[$myrow['topic_id']]['message']=$myrow['message'];
			}
		}
		//TODO Limit diplay count
//		if (!empty($topicInfo)){
//			$topicInfo = array_slice($topicInfo, 0, 50);
//		}
		$xoopsTpl->assign('topicinfo', $topicInfo);
		$xoopsOption['template_main'] = "{$mydirname}_topicinfo.html";
	}
	require_once XOOPS_ROOT_PATH.'/footer.php';
	exit;
}
// Navigator
if ( $bulletin_displaynav == 1 ) {

	// Use the navigation declared
	$xoopsTpl->assign('displaynav', true);

	// Assign Selector
	$bt = new BulletinTopic( $mydirname ) ;
	$xoopsTpl->assign('topic_select', $bt->makeTopicSelBox( true , $storytopic , 'storytopic' ) ) ;

/*	// Option to assign
	for ( $i = 5; $i <= 30; $i = $i + 5 ) {
		$option = array();
		$option['sel']    = ($i == $storynum) ? ' selected="selected"' : '' ;
		$option['option'] = $i ;
		$xoopsTpl->append('option', $option);
	}*/

} else {
	$xoopsTpl->assign('displaynav', false);
}

// Links from the calendar (if there is a date)
if( !empty($caldate) && preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $caldate, $datearr) ){
	$articles = Bulletin::getAllToday( $mydirname , $storynum, $start, $caldate, true , true);
	$xoopsTpl->assign('displaynav', false);
}else{
// If the normal display
	$articles = Bulletin::getAllPublished( $mydirname , $storynum, $start, $storytopic, 1, true, true, true);//ver3.0 changed
}

$scount = count($articles);
$gperm =& BulletinGP::getInstance($mydirname) ;

// Loop of the article
for ( $i = 0; $i < $scount; $i++ ) {
	$story = array();

	$story['id']         = $articles[$i]->getVar('storyid');
	$story['posttime']   = formatTimestamp($articles[$i]->getVar('published'), $bulletin_date_format);
	$story['text']       = $articles[$i]->getVar('hometext');
	$story['topicid']    = $articles[$i]->getVar('topicid');
	$story['topic']      = $articles[$i]->topic_title();
	$story['title']      = $articles[$i]->getVar('title');
	$story['hits']       = $articles[$i]->getVar('counter');
	$story['title_link'] = true;

	$topic_perm = $gperm->get_viewtopic_perm_of_current_user($story['topicid'] , $articles[$i]->getVar('uid'));
	$story = array_merge($story,$topic_perm);
	$story['type']        = $articles[$i]->getVar('type');

	//Assign the user information
	$story['uid']        = $articles[$i]->getVar('uid');
	$story['uname']      = $articles[$i]->getUname();
	$story['realname']   = $articles[$i]->getRealname();

	// Length counting process
	if ( $articles[$i]->strlenBodytext() > 1 ) {
		$story['bytes']    = sprintf(_MD_BYTESMORE, $articles[$i]->strlenBodytext());
		$story['readmore'] = true;
	}

	// Assign a number of comments
	$ccount = $articles[$i]->getVar('comments');
	if( $ccount == 0 ){
		$story['comentstotal'] = _MD_COMMENTS;
	}elseif( $ccount == 1 ) {
		$story['comentstotal'] = _MD_ONECOMMENT;
	}else{
		$story['comentstotal'] = sprintf(_MD_NUMCOMMENTS, $ccount);
	}

	// Administrative links
	$story['adminlink'] = 0;
	if ( $xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid()) ) {
		$story['adminlink'] = 1;
	}

	// Icon image
	if ( $articles[$i]->showTopicimg() ) {
		$story['topic_url'] = $articles[$i]->imglink($bulletin_topicon_path);
		$story['align']     = $articles[$i]->getTopicalign();
	}

	$xoopsTpl->append('stories', $story);
}

// Page Navigation
if( !empty($caldate) && preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $caldate, $datearr) ){
	$totalcount = Bulletin::countPublishedByDate( $mydirname , $caldate , true);
	$query      = 'caldate='.$caldate;
}else{
	$totalcount = Bulletin::countPublished( $mydirname , $storytopic , true ,true);
	$query      = 'storytopic='.$storytopic;
}
if ( $totalcount > $scount ) {
	require_once XOOPS_ROOT_PATH.'/class/pagenav.php';
	$pagenav = new XoopsPageNav($totalcount, $storynum, $start, 'start', $query);
	$xoopsTpl->assign('pagenav', $pagenav->renderNav());

} else {
	$xoopsTpl->assign('pagenav', '');
}

$xoopsTpl->assign($assing_array);

if($bulletin_assing_rssurl_head){
	$xoopsTpl->assign('xoops_module_header', $rss_feed . $xoopsTpl->get_template_vars( "xoops_module_header" ));
}

// GIJ
$breadcrumbs = array( array( 'name' => $xoopsModule->getVar('name') , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/' ) ) ;
$topic = new BulletinTopic( $mydirname , $storytopic ) ;
if( $storytopic ) {
	$pankuzu4assign = $topic->makePankuzuForHTML( $storytopic ) ;
	foreach( $pankuzu4assign as $p4a ) {
		if( $p4a['topic_id'] == $storytopic ) $breadcrumbs[] = array( 'name' => $p4a['topic_title'] ) ;
		else $breadcrumbs[] = array( 'name' => $p4a['topic_title'] , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?storytopic='.$p4a['topic_id'] ) ;
	}
}
$xoopsTpl->assign( 'xoops_breadcrumbs' , $breadcrumbs ) ;
$xoopsTpl->assign( 'mod_config' , $xoopsModuleConfig ) ;
//Template
$xoopsOption['template_main'] = "{$mydirname}_index.html";

require_once XOOPS_ROOT_PATH.'/footer.php';
?>