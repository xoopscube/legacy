<?php
/*
 * Created on 2007/07/11 by nao-pon http://hypweb.net/
 * $Id: sitemap.plugin.php,v 1.4 2009/09/01 03:04:41 nao-pon Exp $
 */

function b_sitemap_xpwiki( $mydirname ) {
	
	global $sitemap_configs;
	
	$myts =& MyTextSanitizer::getInstance();
	$ret = array();

	include_once dirname(dirname(__FILE__)).'/include.php';

	$xpwiki =& XpWiki::getInitedSingleton($mydirname);
	
	$result = $xpwiki->func->get_existpages(FALSE, '', array('limit' => 5, 'order' => ' ORDER BY editedtime DESC', 'select' => array('title'), 'nolisting' => TRUE));
	
	$ret = array();
	
	// Recent Changes
	$show_cat = (@$sitemap_configs['show_subcategoris'])? 'child' : 'parent';
	if ($show_cat === 'child') {
		$ret['id'] = 0;
		$ret['title'] = $xpwiki->root->_LANG['skin']['recent'];
		$ret['url'] = '?' . rawurlencode($xpwiki->root->whatsnew);
	}
	foreach($result as $_res) {
		$pgid = $_res['pgid'];
		$page = $_res['name'];
		$title = $_res['title'];
		$title = ($xpwiki->root->pagename_num2str) ? preg_replace('/\/(?:[0-9\-]+|[B0-9][A-Z0-9]{9})$/','/'.$xpwiki->func->get_heading($page),$page) : $page;
		$ret[$show_cat][] = array(
			"id" => intval( $pgid ) ,
			"title" => $myts->makeTboxData4Show($title) ,
			"url" => $xpwiki->func->get_page_uri($page) ,
			'image' => 2,
		) ;
	}
	if ($show_cat === 'child') {
		$ret = array('parent' => array($ret));
	}
	
	// Other menus
	
	$ret['parent'][] = array(
		'id' => 0 ,
		'title' => $xpwiki->root->_LANG['skin']['list'] ,
		'url' => '?cmd=list' ,
	);

	$ret['parent'][] = array(
		'id' => 0 ,	
		'title' => $xpwiki->root->_attach_messages['msg_list'],
		'url' => '?plugin=attach&amp;pcmd=list' ,
	);

	$ret['parent'][] = array(
		'id' => 0 ,	
		'title' => $xpwiki->root->_LANG['skin']['help'],
		'url' => '?Help' ,
	);
	
	return $ret;
}
?>