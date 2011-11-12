<?php

$storyid = isset($_GET['storyid']) ? intval($_GET['storyid']) : 0;

// 記事が存在しない場合
if( !Bulletin::isPublishedExists( $mydirname , $storyid) ){
	redirect_header($mydirurl.'/index.php',2,_MD_NOSTORY);
	exit();
}

require_once XOOPS_ROOT_PATH.'/class/template.php';

$article = new Bulletin( $mydirname , $storyid);

$datetime = formatTimestamp($article->getVar('published'), $bulletin_date_format);

$tpl = new XoopsTpl();
$tpl->xoops_setTemplateDir(XOOPS_ROOT_PATH.'/themes');
$tpl->xoops_setCaching(2);
$tpl->xoops_setCacheTime(0);

$tpl->assign('charset', _CHARSET);
$tpl->assign('sitename', htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
$tpl->assign('sitename', htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
$tpl->assign('slogan', htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES));
$tpl->assign('xoops_version', htmlspecialchars(XOOPS_VERSION, ENT_QUOTES));
$tpl->assign('site_image', htmlspecialchars($bulletin_imgurl_on_print, ENT_QUOTES));
$tpl->assign('story_title', $article->getVar('title'));
$tpl->assign('story_date', $datetime);
$tpl->assign('story_topic', $article->topic_title());
$tpl->assign('story_hometext', $article->getVar('hometext'));
$tpl->assign('story_id', $storyid);
$tpl->assign('story_bodytext', $article->getDividedBodytext());
$tpl->assign('this_comes_from', sprintf(_MD_THISCOMESFROM,htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES)));
$tpl->assign($assing_array);

$tpl->display( "db:{$mydirname}_print.html" ) ;

?>