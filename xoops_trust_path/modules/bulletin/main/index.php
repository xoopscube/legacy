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
if ($op == "comments"){
	$cmttbl = $xoopsModuleConfig['comment_dirname'];
	$whr_forum = "t.forum_id=" . $xoopsModuleConfig['comment_forum_id'];
	$odr = $xoopsModuleConfig['comment_order'];
	$posttbl = $commenttbl . "_posts";
	if($storytopic){
		$sql = "SELECT b.topic_id,b.topic_title,s.storyid,p.uid,u.uname,p.post_time,p.post_text FROM "
		.$xoopsDB->prefix("bulletin_topics")." b LEFT JOIN "
		.$xoopsDB->prefix("bulletin_stories")." s ON b.topic_id=s.topicid LEFT JOIN "
		.$xoopsDB->prefix($cmttbl."_topics")." t ON s.storyid=t.topic_external_link_id LEFT JOIN "
		.$xoopsDB->prefix($cmttbl."_posts")." p ON t.topic_last_post_id=p.post_id LEFT JOIN "
		.$xoopsDB->prefix("users")." u ON p.uid=u.uid "
		."WHERE ! t.topic_invisible AND (".$whr_forum." AND b.topic_id=".$storytopic.") ORDER BY p.post_time ".$odr ;
		$ret=$xoopsDB->query($sql);
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
		$sql = "SELECT b.topic_id,b.topic_title,count(p.post_id) as comment FROM "
		.$xoopsDB->prefix("bulletin_topics")." b LEFT JOIN "
		.$xoopsDB->prefix("bulletin_stories")." s ON b.topic_id=s.topicid LEFT JOIN "
		.$xoopsDB->prefix($cmttbl."_topics")." t ON s.storyid=t.topic_external_link_id LEFT JOIN "
		.$xoopsDB->prefix($cmttbl."_posts")." p ON t.topic_last_post_id=p.post_id "
		."WHERE ! t.topic_invisible AND (".$whr_forum." ) GROUP BY b.topic_id";
		$ret=$xoopsDB->query($sql);
		while($myrow=$xoopsDB->fetchArray($ret)){
			$topicInfo[$myrow['topic_id']]['topic_id']=$myrow['topic_id'];
			$topicInfo[$myrow['topic_id']]['title']=$myrow['topic_title'];
			$topicInfo[$myrow['topic_id']]['comment']=$myrow['comment'];
		}
		$sql = "SELECT b.topic_id,b.topic_title,count(i.content_id) as iine FROM "
		.$xoopsDB->prefix("bulletin_topics")." b LEFT JOIN "
		.$xoopsDB->prefix("bulletin_stories")." s ON b.topic_id=s.topicid LEFT JOIN "
		.$xoopsDB->prefix("iine_votes").' i ON s.storyid=i.content_id WHERE i.dirname="bulletin" '
		."GROUP BY b.topic_id";
		$ret=$xoopsDB->query($sql);
		while($myrow=$xoopsDB->fetchArray($ret)){
			$topicInfo[$myrow['topic_id']]['topic_id']=$myrow['topic_id'];
			$topicInfo[$myrow['topic_id']]['title']=$myrow['topic_title'];
			$topicInfo[$myrow['topic_id']]['iine']=$myrow['iine'];
		}
		$sql = "SELECT b.topic_id,b.topic_title,count(m.inbox_id) as message FROM "
		.$xoopsDB->prefix("bulletin_topics")." b LEFT JOIN "
		.$xoopsDB->prefix("bulletin_stories")." s ON b.topic_id=s.topicid LEFT JOIN "
		.$xoopsDB->prefix("message_inbox")." m ON m.from_uid=s.uid "
		."WHERE m.uid=" . $xoopsUser->uid() . " GROUP BY b.topic_id";
		$ret=$xoopsDB->query($sql);
		while($myrow=$xoopsDB->fetchArray($ret)){
			$topicInfo[$myrow['topic_id']]['topic_id']=$myrow['topic_id'];
			$topicInfo[$myrow['topic_id']]['title']=$myrow['topic_title'];
			$topicInfo[$myrow['topic_id']]['message']=$myrow['message'];
		}
		$xoopsTpl->assign('topicinfo', $topicInfo);
		$xoopsOption['template_main'] = "{$mydirname}_topicinfo.html";
	}
	require_once XOOPS_ROOT_PATH.'/footer.php';
	exit;
}
// ナビゲータ
if ( $bulletin_displaynav == 1 ) {

	// ナビを使うと宣言
	$xoopsTpl->assign('displaynav', true);

	// セレクタをアサイン
	$bt = new BulletinTopic( $mydirname ) ;
	$xoopsTpl->assign('topic_select', $bt->makeTopicSelBox( true , $storytopic , 'storytopic' ) ) ;

/*	// オプションをアサイン
	for ( $i = 5; $i <= 30; $i = $i + 5 ) {
		$option = array();
		$option['sel']    = ($i == $storynum) ? ' selected="selected"' : '' ;
		$option['option'] = $i ;
		$xoopsTpl->append('option', $option);
	}*/

} else {
	$xoopsTpl->assign('displaynav', false);
}

// カレンダからのリンク（日付指定が有った場合）
if( !empty($caldate) && preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $caldate, $datearr) ){
	$articles = Bulletin::getAllToday( $mydirname , $storynum, $start, $caldate, true , true);
	$xoopsTpl->assign('displaynav', false);
}else{
// 通常表示の場合
	$articles = Bulletin::getAllPublished( $mydirname , $storynum, $start, $storytopic, 1, true, true, true);
}

$scount = count($articles);
$gperm =& BulletinGP::getInstance() ;

// 記事のループ
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

	$topic_perm = $gperm->getTopicPermission($story['topicid']);
	$story = array_merge($story,$topic_perm);

	//ユーザ情報をアサイン
	$story['uid']        = $articles[$i]->getVar('uid');
	$story['uname']      = $articles[$i]->getUname();
	$story['realname']   = $articles[$i]->getRealname();

	// 文字数カウント処理
	if ( $articles[$i]->strlenBodytext() > 1 ) {
		$story['bytes']    = sprintf(_MD_BYTESMORE, $articles[$i]->strlenBodytext());
		$story['readmore'] = true;
	}

	// コメントの数をアサイン
	$ccount = $articles[$i]->getVar('comments');
	if( $ccount == 0 ){
		$story['comentstotal'] = _MD_COMMENTS;
	}elseif( $ccount == 1 ) {
		$story['comentstotal'] = _MD_ONECOMMENT;
	}else{
		$story['comentstotal'] = sprintf(_MD_NUMCOMMENTS, $ccount);
	}

	// 管理者用リンク
	$story['adminlink'] = 0;
	if ( $xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid()) ) {
		$story['adminlink'] = 1;
	}

	// アイコン画像
	if ( $articles[$i]->showTopicimg() ) {
		$story['topic_url'] = $articles[$i]->imglink($bulletin_topicon_path);
		$story['align']     = $articles[$i]->getTopicalign();
	}

	$xoopsTpl->append('stories', $story);
}

// ページナビ
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
//テンプレート
$xoopsOption['template_main'] = "{$mydirname}_index.html";

require_once XOOPS_ROOT_PATH.'/footer.php';
?>