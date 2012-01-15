<?php
/*
 * Created on 2007/12/20 by nao-pon http://hypweb.net/
 * $Id: stand_alone_functions.php,v 1.3 2012/01/14 03:38:10 nao-pon Exp $
 */

if (! function_exists('xpwiki_saf_build_function')) {

// Build a function
function xpwiki_saf_build_function ($mydirname, $funcname) {
	if (! function_exists($mydirname . '_saf_' . $funcname)) {
	eval( '

	function '.$mydirname . '_saf_' . $funcname . '( $options )
	{
		return xpwiki_saf_' . $funcname . '_base( $options ) ;
	}

	' ) ;
	}
}

function xpwiki_saf_getRecentPages_base( $options ) {

	$mydirname = preg_replace( '/[^0-9a-zA-Z_-]/', '', $options[0]);

	$base = (empty($options[1]))? '' : strval($options[1]);

	$count = (empty($options[2]))? 10 : max(1, intval($options[2]));

	// Load need files.
	include_once dirname(dirname(__FILE__)).'/include.php';

	// Make XpWiki object.
	$xpwiki = new XpWiki($mydirname);
	$xpwiki->init('#RenderMode');
	$rss_plugin =& $xpwiki->func->get_plugin_instance('rss');

	if (! empty($options[3])) {
		$xpwiki->root->userinfo['admin'] = FALSE;
		$xpwiki->root->userinfo['uid'] = 0;
		$xpwiki->root->userinfo['uname'] = '';
		$xpwiki->root->userinfo['uname_s'] = '';
		$xpwiki->root->userinfo['gids'] = array();
	}

	$getbody = (! empty($options[4]));

	$lines = $xpwiki->func->get_existpages(FALSE, ($base ? $base . '/' : ''), array('limit' => $count, 'order' => ' ORDER BY editedtime DESC', 'nolisting' => TRUE, 'withtime' =>TRUE));

	$ret = array();

	if ($base) {
		$ret['base'] = array(
			'name' => $base,
			'link' => $xpwiki->func->get_page_uri($base, true),
			'child_counts' => $xpwiki->func->get_child_counts($base),
		);
	}

	foreach ($lines as $line) {
		list($time, $page) = explode("\t", rtrim($line));

		$title = $xpwiki->root->pagename_num2str ? preg_replace('/\/(?:[0-9\-]+|[B0-9][A-Z0-9]{9})$/','/'.$xpwiki->func->get_heading($page),$page) : $page;
		if ($base) $title = substr($title, (strlen($base) + 1));

		list($description, $html, $pginfo, $tags) = $rss_plugin->get_content($page, $getbody);

		$entry = array(
			'id'          => $xpwiki->func->get_pgid_by_name($page),
			'pagename'    => htmlspecialchars($page),
			'views'       => $xpwiki->func->get_page_views($page),
			'replies'     => $xpwiki->func->count_page_comments($page),
			'pubtime'     => ($time + date('Z')),
			'link'        => $xpwiki->func->get_page_uri($page, true),
			'headline'    => htmlspecialchars($title),
			'description' => $description,
			'pginfo'      => $pginfo,
		);
		if (!is_null($html)) $entry['content'] = $html;

		$ret['entries'][] = $entry;
	}

	$xpwiki = null;

	// As for all the values, it is not escaped in HTML.
	return $ret;
}

}
?>