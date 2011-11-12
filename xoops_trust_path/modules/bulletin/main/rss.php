<?php

require_once XOOPS_ROOT_PATH.'/class/template.php';

$myts =& MyTextSanitizer::getInstance();

if (function_exists('mb_http_output')) {
	mb_http_output('pass');
}
$tpl = new XoopsTpl();
$tpl->xoops_setCaching(2);
$tpl->xoops_setCacheTime(0);
if (!$tpl->is_cached("db:{$mydirname}_rss.html")) {
	$articles = Bulletin::getAllPublished( $mydirname , 10 , 0 ) ;
	if (is_array($articles)) {
		$tpl->assign('channel_title', bulletin_utf8_encode(htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES)));
		$tpl->assign('channel_link', XOOPS_URL.'/');
		$tpl->assign('channel_desc', bulletin_utf8_encode(htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES)));
		// $tpl->assign('channel_lastbuild', formatTimestamp(time(), 'rss'));
		$tpl->assign('channel_lastbuild', date( 'r' ) ) ; // GIJ
		$tpl->assign('channel_webmaster', $xoopsConfig['adminmail']);
		$tpl->assign('channel_editor', $xoopsConfig['adminmail']);
		$tpl->assign('channel_category', 'News');
		$tpl->assign('channel_generator', 'XOOPS');
		$tpl->assign('channel_language', _LANGCODE);
		$tpl->assign('image_url', XOOPS_URL.'/images/logo.gif');
		$dimention = getimagesize(XOOPS_ROOT_PATH.'/images/logo.gif');
		if (empty($dimention[0])) {
			$width = 88;
		} else {
			$width = ($dimention[0] > 144) ? 144 : $dimention[0];
		}
		if (empty($dimention[1])) {
			$height = 31;
		} else {
			$height = ($dimention[1] > 400) ? 400 : $dimention[1];
		}
		$tpl->assign('image_width', $width);
		$tpl->assign('image_height', $height);
		$count = $articles;
		foreach ($articles as $article) {
			$content4html = $article->getVar('hometext') . $article->getDividedBodytext() ;
			$hometext = $article->getVar('hometext','n') ;
			if( function_exists( 'easiestml' ) ) {
				$content4html = easiestml( $content4html ) ;
				$hometext = easiestml( $hometext ) ;
			}
			$tpl->append('items', array(
				'title' => htmlspecialchars(bulletin_utf8_encode($article->getVar('title', 'n')), ENT_QUOTES), 
				'category' => htmlspecialchars(bulletin_utf8_encode($article->newstopic->topic_title), ENT_QUOTES), 
				'link' => $mydirurl.'/index.php?page=article&amp;storyid='.$article->getVar('storyid'), 
				'guid' => $mydirurl.'/index.php?page=article&amp;storyid='.$article->getVar('storyid'), 
//				'pubdate' => formatTimestamp($article->getVar('published'), 'rss'), 
				'pubdate' => date( 'r' , $article->getVar('published') ) , // GIJ
				'description' => bulletin_utf8_encode(htmlspecialchars(strip_tags($myts->xoopsCodeDecode($hometext)), ENT_QUOTES)),
				'content' => bulletin_utf8_encode($content4html),
			) ) ;
		}
	}
}

header ('Content-Type:text/xml; charset=utf-8');
$tpl->display("db:{$mydirname}_rss.html");


?>