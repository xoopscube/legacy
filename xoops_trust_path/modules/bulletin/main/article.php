<?php

$storyid   = isset($_GET['storyid']) ? intval($_GET['storyid']) : 0 ;
$storypage = isset($_GET['storypage']) ? intval($_GET['storypage']) : 0 ;

// 記事が存在しない場合
if( !Bulletin::isPublishedExists( $mydirname , $storyid) ){
	redirect_header($mydirurl.'/index.php',2,_MD_NOSTORY);
	exit();
}

//テンプレート
$xoopsOption['template_main'] = "{$mydirname}_article.html";

require_once XOOPS_ROOT_PATH.'/header.php';

$article = new Bulletin( $mydirname , $storyid);

$story['id']       = $storyid;
$story['posttime'] = formatTimestamp($article->getVar('published'), $bulletin_date_format);
$story['topicid']  = $article->getVar('topicid');
$story['topic']    = $article->topic_title();
$story['title']    = $article->getVar('title');
$story['text']     = $article->getVar('hometext');
$story['hits']     = $article->getVar('counter') + 1; // To disp real view
$bodytext = $article->getVar('bodytext');

if ( $bodytext != '' ) {
	$articletext = explode('[pagebreak]', $bodytext);
	$story_pages = count($articletext);
	$storypage   = ( $story_pages - 1 >= $storypage ) ? $storypage : 0 ;

	// [pagebreak]で複数ページのコンテンツが構成されている場合
	if ($story_pages > 1 ) {
		require_once XOOPS_ROOT_PATH.'/class/pagenav.php';
		$pagenav = new XoopsPageNav($story_pages, 1, $storypage, 'storypage', 'page=article&storyid='.$storyid);
		$xoopsTpl->assign('pagenav', $pagenav->renderNav());

		if ($storypage == 0) {
			$story['text'] = $story['text'].'<br /><br />'.$articletext[$storypage];
		} else {
			$story['text'] = $articletext[$storypage];
		}
	} else {
		$story['text'] = $story['text'].'<br /><br />'.$bodytext;
    }
}

//ユーザ情報をアサイン
$story['uid']      = $article->getVar('uid');
$story['uname']    = $article->getUname();
$story['realname'] = $article->getRealname();
$story['morelink'] = '';

$story['adminlink'] = 0;
if ( $xoopsUser && $xoopsUser->isAdmin($xoopsModule->getVar('mid')) ) {
	$story['adminlink'] = 1;
}

if ( $article->showTopicimg()  ) {
	$story['topic_url'] = $article->imglink($bulletin_topicon_path);
	$story['align']     = $article->getTopicalign();
}

// 関連記事
if($bulletin_use_relations){
	$relations = $article->getRelated();
	foreach($relations as $relation){
		$relation_asign = array();
		$relation_asign['storyid'] = $relation->getVar('storyid');
		$relation_asign['title'] = $relation->getVar('title');
		$relation_asign['date'] = formatTimestamp($relation->getVar('published'), $bulletin_date_format);
		$relation_asign['uid'] = $relation->getVar('uid');
		$relation_asign['uname'] = $relation->getUname();
		$relation_asign['realname'] = $relation->getRealname();
		$relation_asign['topicid'] = $relation->getVar('topicid');
		$relation_asign['topic'] = $relation->topic_title();
		$relation_asign['counter'] = $relation->getVar('counter');
		$relation_asign['comments'] = $relation->getVar('comments');
		$relation_asign['dirname'] = $relation->getVar('dirname');
		$relation_asign['url'] = XOOPS_URL.'/modules/'.$relation->getVar('dirname');
		$xoopsTpl->append('relations', $relation_asign);
	}
}

// カテゴリの最新記事
if($bulletin_disp_list_of_cat && $bulletin_stories_of_cat > 0){
	$category_storeis = Bulletin::getAllPublished( $mydirname , $bulletin_stories_of_cat, 0, $article->getVar('topicid'), 0);
	foreach($category_storeis as $category_story){
		$category_story_asign['storyid']  = $category_story->getVar('storyid');
		$category_story_asign['title']    = $category_story->getVar('title');
		$category_story_asign['date']     = formatTimestamp($category_story->getVar('published'), $bulletin_date_format);
		$category_story_asign['uid']      = $category_story->getVar('uid');
		$category_story_asign['uname']    = $category_story->getUname();
		$category_story_asign['realname'] = $category_story->getRealname();
		$category_story_asign['counter']  = $category_story->getVar('counter');
		$category_story_asign['comments'] = $category_story->getVar('comments');
		$xoopsTpl->append('category_storeis', $category_story_asign);
	}
}

// Tell A Frinedを使う場合
if($bulletin_use_tell_a_frined){
	$mail_link = XOOPS_URL.'/modules/tellafriend/index.php?target_uri='.rawurlencode( "$mydirurl/index.php?page=article&storyid=$storyid" ).'&amp;subject='.rawurlencode(sprintf(_MD_INTARTFOUND,$xoopsConfig['sitename'])) ;
}else{
	$mail_link = 'mailto:?subject='.rawurlencode(sprintf(_MD_INTARTICLE,$xoopsConfig['sitename'])).'&amp;body='.rawurlencode(sprintf(_MD_INTARTFOUND, $xoopsConfig['sitename']).':  '.$mydirurl.'/index.php?page=article&storyid='.$storyid);
}

$xoopsTpl->assign('story', $story);
$xoopsTpl->assign('mail_link', $mail_link);
$xoopsTpl->assign('disp_print_icon', $bulletin_disp_print_icon);
$xoopsTpl->assign('disp_tell_icon', $bulletin_disp_tell_icon );
// パンくずリスト
if($bulletin_use_pankuzu) $xoopsTpl->assign('pankuzu', $article->topics->makePankuzuForHTML($article->getVar('topicid')) );

if( $bulletin_titile_as_sitename ) $xoopsTpl->assign('xoops_pagetitle', $article->getVar('title'));

require_once XOOPS_ROOT_PATH.'/include/comment_view.php';

if($bulletin_assing_rssurl_head){
	$xoopsTpl->assign('xoops_module_header', $rss_feed . $xoopsTpl->get_template_vars( "xoops_module_header" ));
}
$xoopsTpl->assign($assing_array);

// 閲覧数をカウントアップする
if (empty($_GET['com_id']) && !isset($_GET['storypage'])) {
	$article->updateCounter();
}

// GIJ
$breadcrumbs = array( array( 'name' => $xoopsModule->getVar('name') , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/' ) ) ;
$pankuzu4assign = $article->topics->makePankuzuForHTML($article->getVar('topicid')) ;
foreach( $pankuzu4assign as $p4a ) {
	$breadcrumbs[] = array( 'name' => $p4a['topic_title'] , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?storytopic='.$p4a['topic_id'] ) ;
}
$breadcrumbs[] = array( 'name' => $article->getVar('title') ) ;
$xoopsTpl->assign( 'xoops_breadcrumbs' , $breadcrumbs ) ;
$xoopsTpl->assign( 'mod_config' , $xoopsModuleConfig ) ;

require_once XOOPS_ROOT_PATH.'/footer.php';
?>