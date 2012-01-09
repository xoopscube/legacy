<?php
// $Id: search.php,v 1.1 2004/09/09 05:14:50 onokazu Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

$xoopsOption['pagetype'] = "search";

include '../../mainfile.php';

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( __FILE__ ) ) ;

$config_handler =& xoops_gethandler('config');
$xoopsConfigSearch =& $config_handler->getConfigsByCat(XOOPS_CONF_SEARCH);

if ($xoopsConfigSearch['enable_search'] != 1) {
	header('Location: '.XOOPS_URL.'/index.php');
	exit();
}
$request = array();
if (! empty($_GET)) $request = $_GET;
if (! empty($_POST)) $request = array_merge($_POST, $request);
$myts =& MyTextSanitizer::getInstance();
$action	= isset($request['action']) 	? $myts->stripSlashesGPC($request['action']) 	: "search";
$query	= isset($request['query']) 	? $myts->stripSlashesGPC($request['query']) 	: "";
$andor	= isset($request['andor']) 	? $myts->stripSlashesGPC($request['andor']) 	: "AND";
$mid 	= isset($request['mid']) 	? intval($request['mid']) 	: 0;
$uid 	= isset($request['uid']) 	? intval($request['uid']) 	: 0;
$start 	= isset($request['start']) 	? intval($request['start']) 	: 0;
$sug 	= isset($request['sug']) 	? intval($request['sug']) 	: 0;
$showcontext= isset($request['showcontext']) 	? intval($request['showcontext']) 	: 1 ;
$mids_p	= isset($request['mids'])  	? $request['mids']	 	: "";
$mids = array();
if( is_array($mids_p) ) { foreach($mids_p as $e){  $mids[] = intval($e); } }

//by domifara add rawurldecode for firefox
if(function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding')){
	$from_encode = mb_detect_encoding($query);
	if ($from_encode && $from_encode !== _CHARSET){
		$query = mb_convert_encoding($query , _CHARSET , $from_encode);
	}
}

if ($andor != 'exact') {
	$query	= mb_ereg_replace(_MD_NBSP, " ", $query);
}

$queries = array();
$mb_suggest = array();
$mb_suggest_w = array();
if ( $action == "results" && $query == "" ) {
	redirect_header("index.php",1,_MD_PLZENTER);
	exit();
}

if ( $action == "showall" && ($query == "" || empty($mid)) ) {
	redirect_header("index.php",1,_MD_PLZENTER);
	exit();
}

if ($action == "showallbyuser" && (empty($mid) || empty($uid))) {
	redirect_header("index.php",1,_MD_PLZENTER);
	exit();
}

$groups = ( $xoopsUser ) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
$gperm_handler = & xoops_gethandler( 'groupperm' );
$available_modules = $gperm_handler->getItemIds('module_read', $groups);
include XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/include/function.php';

if ($action == 'search') {
	include XOOPS_ROOT_PATH.'/header.php';
	$xoopsOption['template_main'] = 'search_index.html';
	include XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/include/searchform.php';
	$search_form  = $search_form->render();
	//Do not remove follows
	//$search_form .= '<p><a href="http://suin.asia" target="_blank">search</a>(<a href="http://xoopscube.jp/" target="_blank">original</a>)</p>';
	$xoopsTpl->assign('search_form', $search_form);
	include XOOPS_ROOT_PATH.'/footer.php';
	exit();
}

if ( $andor != "OR" && $andor != "exact" && $andor != "AND" ) {
	$andor = "AND";
}

$strlen_func = (function_exists('mb_strlen'))? 'mb_strlen' : 'strlen';
if ($action != 'showallbyuser') {
	if ( $andor != "exact" ) {
		$ignored_queries = array(); // holds kewords that are shorter than allowed minimum length
		$temp_queries = array_unique(preg_split('/"([^"]+)"|\'([^\']+)\'|[\s]+/', $query, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY));
		foreach ($temp_queries as $q) {
			$q = trim($q);
			if ($q && $strlen_func($q) >= $xoopsConfigSearch['keyword_min']) {
				$queries[] = addSlashes($q);
				//for Japanese
				if(function_exists('mb_convert_kana')){
					//Zenkaku Eisu
					$_mbq = mb_convert_kana($q, 'a');
					if ($q !== $_mbq) {
						$mb_suggest[] = $_mbq._MD_HANKAKU_EISU;
						$mb_suggest_w[] = addSlashes($_mbq);
					}
					//Hankaku Eisu
					$_mbq = mb_convert_kana($q, 'A');
					if ($q !== $_mbq) {
						$mb_suggest[] = $_mbq._MD_HANKAKU_EISU;
						$mb_suggest_w[] = addSlashes($_mbq);
					}
					//Zenkaku Katakana
					$_mbq = mb_convert_kana($q, 'k');
					if ($q !== $_mbq) {
						$mb_suggest[] = $_mbq._MD_HANKAKU_EISU;
						$mb_suggest_w[] = addSlashes($_mbq);
					}
					//Hankaku Katakana
					$_mbq = mb_convert_kana($q, 'KV');
					if ($q !== $_mbq) {
						$mb_suggest[] = $_mbq._MD_HANKAKU_EISU;
						$mb_suggest_w[] = addSlashes($_mbq);
					}
				}
			} else if ($q) {
				$ignored_queries[] = $q;
			}
		}
 		if (count($queries) == 0) {
			redirect_header('index.php', 2, sprintf(_MD_KEYTOOSHORT, $xoopsConfigSearch['keyword_min'], ceil($xoopsConfigSearch['keyword_min']/2) ));
			exit();
		}
	} else {
		$query = trim($query);
		if ($strlen_func($query) < $xoopsConfigSearch['keyword_min']) {
			redirect_header('index.php', 2, sprintf(_MD_KEYTOOSHORT, $xoopsConfigSearch['keyword_min'], ceil($xoopsConfigSearch['keyword_min']/2) ));
 			exit();
		}
		$queries = array(addSlashes($query));
	}
}
switch ($action) {
case "results":
	$module_handler =& xoops_gethandler('module');
	$criteria = new CriteriaCompo(new Criteria('hassearch', 1));
	$criteria->add(new Criteria('isactive', 1));
	$criteria->add(new Criteria('mid', "(".implode(',', $available_modules).")", 'IN'));
	$db =& Database::getInstance();
	$result = $db->query("SELECT mid FROM ".$db->prefix("search")." WHERE notshow!=0");
    	while (list($badmid) = $db->fetchRow($result)) {
		$criteria->add(new Criteria('mid', $badmid, '!='));
	}
	$modules =& $module_handler->getObjects($criteria, true);
	if(count($modules)==0){
		redirect_header("index.php",3,_MD_UNABLE_TO_SEARCH);
		exit();
	}
	if (empty($mids) || !is_array($mids)) {
		unset($mids);
		$mids = array_keys($modules);
	}
	include XOOPS_ROOT_PATH . '/header.php';
	$xoopsTpl->assign('xoops_module_header', $xoopsTpl->get_template_vars('xoops_module_header') . '<link rel="stylesheet" type="text/css" media="screen" href="'.XOOPS_URL.'/modules/'.$mydirname.'/include/search.css" />');
	$xoopsOption['template_main'] = 'search_result.html';
	$xoopsTpl->assign('lang_search_results', _MD_SEARCHRESULTS);
	$xoopsTpl->assign('lang_keyword', _MD_KEYWORDS);
	if ($andor != 'exact') {
		foreach ($queries as $q) {
			$keywords = array();
			$keywords['key'] = htmlspecialchars(stripslashes($q));
			$xoopsTpl->append('keywords', $keywords);
 		}
 		if (!empty($ignored_queries)) {
			$xoopsTpl->assign('lang_ignoredwors', sprintf(_MD_IGNOREDWORDS, $xoopsConfigSearch['keyword_min']) );
			foreach ($ignored_queries as $q) {
				$badkeywords = array();
				$badkeywords['key'] = htmlspecialchars(stripslashes($q));
				$xoopsTpl->append('badkeywords', $badkeywords);
			}
		}
	} else {
		$keywords = array();
		$keywords['key'] = '"'.htmlspecialchars(stripslashes($queries[0])).'"';
		$xoopsTpl->append('keywords', $keywords);
	}
	if(count($mb_suggest)>0 && $sug!=1){
		$xoopsTpl->assign('lang_sugwords', _MD_KEY_WORD_SUG );
		$sug_url  = XOOPS_URL."/modules/".$mydirname."/index.php";
		$sug_url .= "?andor=".$andor;
		foreach ($mids as $m) {
			$sug_url .= "&mids%5B%5D=".$m;
		}
		$sug_url .= "&action=".$action;
		$sug_url .= "&sug=1";
		$xoopsTpl->assign('sug_url', $sug_url );
		foreach ($mb_suggest as $k=>$m) {
			$sug_keys = array();
			$sug_keys['key'] = htmlspecialchars($m);
			$sug_keys['url'] = $sug_url."&query=".urlencode(stripslashes($mb_suggest_w[$k]));
			$xoopsTpl->append('sug_keys', $sug_keys);
		}
	}
	$no_matches = array();
	foreach ($mids as $mid) {
		$mid = intval($mid);
		if ( in_array($mid, $available_modules) ) {
 			$module =& $modules[$mid];
			if (!is_object($module)) continue;
			$this_mod_dir = $module->getVar('dirname');
			$use_context = false;
			$GLOBALS['md_search_flg_zenhan_support'] = false;
			if( file_exists( XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/plugin/'.$this_mod_dir.'/'.$this_mod_dir.'.php' ) && $xoopsModuleConfig['search_display_text']==1 ){
				include_once XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/plugin/'.$this_mod_dir.'/'.$this_mod_dir.'.php';
				$func = 'b_search_'.$this_mod_dir;
				$results = context_search($func, $queries, $andor, 5, 0);
				$use_context = true;
			}else{
				$results = $module->search($queries, $andor, 5, 0);
			}
			if (empty($results)) $results = array();
			if(! $GLOBALS['md_search_flg_zenhan_support'] && count($mb_suggest_w) > 0){
				if($use_context){
					$results2 = context_search($func, $mb_suggest_w, $andor, 5, 0);
				}else{
					$results2 = $module->search($mb_suggest_w, $andor, 5, 0);
				}
				if ($results2) {
					$results = array_map('unserialize', array_unique(array_map('serialize', array_merge($results,$results2))));
				}
			}
			if (! $results) {
				$no_matches[] = $module->getVar('name');
			} else {
				$count = count($results);
				usort($results, 'sort_by_date');
				if ( $count > 5 ) {
					$results  = array_slice($results, 0, 5);
					$count = 5;
				}
				for ($i = 0; $i < $count; $i++) {
					if (isset($results[$i]['image']) && $results[$i]['image'] != '') {
						$results[$i]['image'] = '/modules/'.$this_mod_dir.'/'.$results[$i]['image'];
					} else {
						$results[$i]['image'] = '/modules/'.$mydirname.'/images/posticon.gif';
					}
					$results[$i]['title'] = $myts->htmlSpecialChars($results[$i]['title']);
					$results[$i]['link'] = '/modules/'.$module->getVar('dirname').'/'.$results[$i]['link'];
					$results[$i]['time'] = !empty($results[$i]['time']) ? formatTimestamp($results[$i]['time']) : "";
					$results[$i]['uid'] = !empty($results[$i]['uid']) ? intval($results[$i]['uid']) : "" ;
					if ( !empty($results[$i]['uid']) ) {
						$results[$i]['uname'] = XoopsUser::getUnameFromId($results[$i]['uid']);
					}
				}
				if ( $count == 5 ) {
					$search_url  = XOOPS_URL.'/modules/'.$mydirname.'/index.php?query='.urlencode(stripslashes(implode(' ', $queries)));
					$search_url .= "&amp;mid=$mid&amp;action=showall&amp;andor=$andor&amp;showcontext=$showcontext";
					$showall_link = '<a href="'.$search_url.'">'._MD_SHOWALLR.'</a>';
				} else {
					$showall_link = '';
				}
				$xoopsTpl->append('modules', array('name' => $module->getVar('name'), 'results' => $results, 'showall_link' => $showall_link));
			}
		}
		unset($results1);
		unset($results2);
		unset($results);
		unset($module);
	}
	if ($no_matches) {
		$xoopsTpl->assign('no_matches', $no_matches);
		$xoopsTpl->assign('no_match', _MD_NOMATCH);
	}
	include "include/searchform.php";
	$search_form  = $search_form->render();
	//Do not remove follows
	$search_form .= '<p><a href="http://suin.asia" target="_blank">search</a>(<a href="http://xoopscube.jp/" target="_blank">original</a>)</p>';
	$xoopsTpl->assign('search_form', $search_form);

	if (defined('LEGACY_MODULE_VERSION') && version_compare(LEGACY_MODULE_VERSION, '2.2', '>=')) {
		// For XCL >= 2.2
		$xclRoot =& XCube_Root::getSingleton();
		$xclRoot->mContext->setAttribute('legacy_pagetitle', Legacy_Utils::formatPagetitle($xoopsModule->getVar('name'), htmlspecialchars(join(' ', $queries)), $andor));
	}

	break;

case "showall":
case "showallbyuser":
	include XOOPS_ROOT_PATH."/header.php";
	$xoopsTpl->assign("xoops_module_header",'<link rel="stylesheet" type="text/css" media="screen" href="'.XOOPS_URL.'/modules/'.$mydirname.'/include/search.css" />');
	$db =& Database::getInstance();
	$result = $db->query("SELECT mid FROM ".$db->prefix("search")." WHERE notshow!=0");
	$undisplayable = array();
    	while (list($badmid) = $db->fetchRow($result)) {
		$undisplayable[] = $badmid;
	}
	if( in_array($mid,$undisplayable) || !in_array($mid, $available_modules) ){
		redirect_header("index.php",1,_NOPERM);
		exit();
	}
	$xoopsOption['template_main'] = 'search_result_all.html';
	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->get($mid);
	$this_mod_dir = $module->getVar('dirname');
	$use_context = false;
	$GLOBALS['md_search_flg_zenhan_support'] = false;
	if( file_exists( XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/plugin/'.$this_mod_dir.'/'.$this_mod_dir.'.php' )  && $xoopsModuleConfig['search_display_text']==1 ){
		include_once XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/plugin/'.$this_mod_dir.'/'.$this_mod_dir.'.php';
		$func = 'b_search_'.$this_mod_dir;
		$results1 =& context_search($func, $queries, $andor, 20, $start, $uid);
		$use_context = true;
	}else{
		$results1 =& $module->search($queries, $andor, 20, $start, $uid);
	}
	if(!$GLOBALS['md_search_flg_zenhan_support'] && count($mb_suggest_w)>0){
		if($use_context){
			$results2 =& context_search($func, $mb_suggest_w, $andor, 20, $start, $uid);
		}else{
			$results2 =& $module->search($mb_suggest_w, $andor, 20, $start, $uid);
		}
	}else{
		$results2 = array();
	}
	$results  = array_merge($results1,$results2);
	usort($results, 'sort_by_date');
	$count = count($results);
	if (is_array($results) && $count > 0) {
		$next_results =& $module->search($queries, $andor, 1, $start + 20, $uid);
		$next_count = count($next_results);
		$has_next = false;
		if (is_array($next_results) && $next_count == 1) {
			$has_next = true;
		}
		$xoopsTpl->assign('lang_search_results', _MD_SEARCHRESULTS);
		if ($action == 'showall') {
			$xoopsTpl->assign('lang_keyword', _MD_KEYWORDS);
			if ($andor != 'exact') {
				foreach ($queries as $q) {
					$keywords = array();
					$keywords['key'] = htmlspecialchars(stripslashes($q));
					$xoopsTpl->append('keywords', $keywords);
				}
 			} else {
				$keywords = array();
				$keywords['key'] = '"'.htmlspecialchars(stripslashes($queries[0])).'"';
				$xoopsTpl->append('keywords', $keywords);
			}
		}
		$xoopsTpl->assign('showing', sprintf(_MD_SHOWING, $start+1, $start + $count));
		$xoopsTpl->assign('module_name', $myts->makeTboxData4Show($module->getVar('name')));
		for ($i = 0; $i < $count; $i++) {
			if (isset($results[$i]['image']) && $results[$i]['image'] != '') {
				$results['image'] = '/modules/'.$module->getVar('dirname').'/'.$results[$i]['image'];
			} else {
				$results['image'] = '/modules/'.$mydirname.'/images/posticon.gif';
			}
			$results['title'] = $myts->makeTboxData4Show($results[$i]['title']);
			$results['link'] = '/modules/'.$module->getVar('dirname').'/'.$results[$i]['link'];
			$results['time'] = $results[$i]['time'] ? formatTimestamp($results[$i]['time']) : '';
			$results['uid'] = intval($results[$i]['uid']);
			$results['context'] = !empty($results[$i]['context']) ? $results[$i]['context'] : "" ;
			if ( !empty($results[$i]['uid']) ) {
				$results['uname'] = XoopsUser::getUnameFromId($results[$i]['uid']);
			}
			$xoopsTpl->append('results', $results);
		}
		$navi = '<table><tr>';
		$search_url = XOOPS_URL.'/modules/'.$mydirname.'/index.php?query='.urlencode(stripslashes(implode(' ', $queries)));
		$search_url .= "&mid=$mid&action=$action&andor=$andor&showcontext=$showcontext";
		if ($action=='showallbyuser') {
			$search_url .= "&uid=$uid";
		}
		if ( $start > 0 ) {
			$prev = $start - 20;
			$navi .= "\n".'<td align="left">';
			$search_url_prev = $search_url."&start=$prev";
			$navi .= "\n".'<a href="'.htmlspecialchars($search_url_prev).'">'._MD_PREVIOUS.'</a></td>';
		}
		$navi .= "\n".'<td>&nbsp;&nbsp;</td>';
		if (false != $has_next) {
			$next = $start + 20;
			$search_url_next = $search_url."&start=$next";
			$navi .= "\n".'<td align="right"><a href="'.htmlspecialchars($search_url_next).'">'._MD_NEXT.'</a></td>';
		}
		$navi .= "\n".'</tr></table>';
		$xoopsTpl->assign('navi', $navi);
	} else {
		$xoopsTpl->assign('no_match', _MD_NOMATCH);
	}
	include "include/searchform.php";
	$search_form  = $search_form->render();
	//Do not remove follows
	$search_form .= '<p><a href="http://suin.asia" target="_blank">search</a>(<a href="http://xoopscube.jp/" target="_blank">original</a>)</p>';
	$xoopsTpl->assign('search_form', $search_form);
	break;
}
include XOOPS_ROOT_PATH."/footer.php";
?>