<?php

$storyid   = isset($_GET['storyid']) ? intval($_GET['storyid']) : 0 ;
$storypage = isset($_GET['storypage']) ? intval($_GET['storypage']) : 0 ;

// If there are no articles
if( empty($storyid) || !Bulletin::isPublishedExists( $mydirname , $storyid) ){
	redirect_header($mydirurl.'/index.php',2,_MD_NOSTORY);
	exit();
}

//Template
$xoopsOption['template_main'] = "{$mydirname}_article.html";

require_once XOOPS_ROOT_PATH.'/header.php';

$article = new Bulletin( $mydirname , $storyid);

$gperm =& BulletinGP::getInstance($mydirname) ;
if( ! $gperm->proceed4topic('can_read',$article->getVar('topicid')) ){
	redirect_header($mydirurl.'/index.php',2,_NOPERM);
	exit();
}

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

	// [pagebreak]If the content is configured in [pagebreak] multi-page articles
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

	$topic_perm = $gperm->get_viewtopic_perm_of_current_user($story['topicid'] , $article->getVar('uid'));
	$story = array_merge($story,$topic_perm);
	$story['type'] = $article->getVar('type');

	// Assign a number of comments
	$ccount = $article->getVar('comments');
	if( $ccount == 0 ){
		$story['comentstotal'] = _MD_COMMENTS;
	}elseif( $ccount == 1 ) {
		$story['comentstotal'] = _MD_ONECOMMENT;
	}else{
		$story['comentstotal'] = sprintf(_MD_NUMCOMMENTS, $ccount);
	}

//Assign the user information
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

// Related article
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

// Recent Posts from Category
if($bulletin_disp_list_of_cat && $bulletin_stories_of_cat > 0){
	$category_storeis = Bulletin::getAllPublished( $mydirname , $bulletin_stories_of_cat, 0, $article->getVar('topicid'), 0, true, false, true);//ver3.0 changed
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

// If you are using Tell A Frined module
if($bulletin_use_tell_a_frined){
	$mail_link = XOOPS_URL.'/modules/tellafriend/index.php?target_uri='.rawurlencode( "$mydirurl/index.php?page=article&storyid=$storyid" ).'&amp;subject='.rawurlencode(sprintf(_MD_INTARTFOUND,$xoopsConfig['sitename'])) ;
}else{
//	$mail_link = 'mailto:?subject='.rawurlencode(sprintf(_MD_INTARTICLE,$xoopsConfig['sitename'])).'&amp;body='.rawurlencode(sprintf(_MD_INTARTFOUND, $xoopsConfig['sitename']).':  '.$mydirurl.'/index.php?page=article&storyid='.$storyid);
	$mail_subject = sprintf(_MD_INTARTICLE,$xoopsConfig['sitename']);
	$mail_body = sprintf(_MD_INTARTFOUND, $xoopsConfig['sitename']).':  '.$mydirurl.'/index.php?page=article&storyid='.$storyid;
	if (defined('_MD_MAILTO_ENCODING')){
		if ( strcasecmp(_MD_MAILTO_ENCODING,_CHARSET) && function_exists('mb_convert_encoding') && @mb_internal_encoding(_CHARSET) ) {
			$mail_subject =mb_convert_encoding( $mail_subject  , _MD_MAILTO_ENCODING , _CHARSET) ;
			$mail_body =mb_convert_encoding( $mail_body  , _MD_MAILTO_ENCODING , _CHARSET) ;
		}
	}
	$mail_link = 'mailto:?subject='.rawurlencode($mail_subject).'&amp;body='.rawurlencode( $mail_body );
}

$xoopsTpl->assign('story', $story);
$xoopsTpl->assign('mail_link', $mail_link);
$xoopsTpl->assign('disp_print_icon', $bulletin_disp_print_icon);
$xoopsTpl->assign('disp_tell_icon', $bulletin_disp_tell_icon );
// Breadcrumbs
if($bulletin_use_pankuzu) $xoopsTpl->assign('pankuzu', $article->topics->makePankuzuForHTML($article->getVar('topicid')) );

if( $bulletin_titile_as_sitename ) $xoopsTpl->assign('xoops_pagetitle', $article->getVar('title'));

require_once XOOPS_ROOT_PATH.'/include/comment_view.php';

if($bulletin_assing_rssurl_head){
	$xoopsTpl->assign('xoops_module_header', $rss_feed . $xoopsTpl->get_template_vars( "xoops_module_header" ));
}
$xoopsTpl->assign($assing_array);

// Count up the number of views
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

//meta description
$description = $myts->htmlSpecialChars(xoops_substr(strip_tags($story['text']),0,255),ENT_QUOTES);
if (defined('LEGACY_MODULE_VERSION') && version_compare(LEGACY_MODULE_VERSION, '2.2', '>=')) {
	// For XCL 2.2
	$xclRoot =& XCube_Root::getSingleton();
	$headerScript = $xclRoot->mContext->getAttribute('headerScript');
	$headerScript->addMeta('description', $description);
} elseif (isset($xoTheme) && is_object($xoTheme)) {
	// For XOOPS 2.3 or higher & Impress CMS.
	$xoTheme->addMeta('meta', 'description', $description);
}

require_once XOOPS_ROOT_PATH.'/footer.php';
?>