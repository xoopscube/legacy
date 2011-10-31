<?php
/*
 * Created on 2009/05/08 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: piCal.php,v 1.3 2011/06/01 06:27:52 nao-pon Exp $
 */

require_once XOOPS_TRUST_PATH.'/modules/xpwiki/include.php' ;
$xpwiki =& XpWiki::getInitedSingleton($plugin['dirname']);
if ($xpwiki->isXpWiki) {

	// options
	$options = array(
		'base'    => '',
		'nochild' => FALSE,
		'include' => '',
		'exclude' => '',
	);
	
	//$args = explode( '#' , trim($plugin['options']) ) ;
	$args = $xpwiki->func->csv_explode(',' , trim($plugin['options']));
	
	$dummy =& $xpwiki->func->get_plugin_instance('dummy');
	$dummy->fetch_options($options, $args, array('base'));
	
	$base = '';
	if ($options['base']) {
		// Base name
		$base = rtrim($options['base'], '/') . '/';
	}
	
	$include = array();
	if ($options['include']) {
		foreach(explode('#', $options['include']) as $_page) {
			$include[] = '`name` LIKE \''.addslashes($_page).'\'';
		}
		$include = 'AND ' . join(' AND ', $include);
	} else {
		$include = '';
	}
	
	$exclude = array();
	if ($options['exclude']) {
		foreach(explode('#', $options['exclude']) as $_page) {
			$exclude[] = '`name` NOT LIKE \''.addslashes($_page).'\'';
		}
		$exclude = 'AND ' . join(' AND ', $exclude);
	} else {
		$exclude = '';
	}

	// to GMT
	$range_start_s -= $xpwiki->cont['LOCALZONE'];
	$range_end_s -= $xpwiki->cont['LOCALZONE'];
	
	$queryOptions = array(
		'where'    => " editedtime >= $range_start_s AND editedtime < $range_end_s " . $include . $exclude,
		'nochild'  => $options['nochild'],
		'exclude'  => $options['exclude'],
		'withtime' => TRUE,
		'order'    => ' ORDER BY `name`',
	);
	$pages = $xpwiki->func->get_existpages(FALSE, $base, $queryOptions);
	
	$monthly = (($range_end_s - $range_start_s) > 864000);
	foreach($pages as $page) {
		list( $server_time, $page ) = explode("\t", $page);
		$server_time += $xpwiki->cont['LOCALZONE'];
		$user_time = $server_time + $tzoffset_s2u ;
		if( $monthly && date( 'n' , $user_time ) != $this->month ) continue ;
		$target_date = date('j',$user_time) ;
		$title = ($xpwiki->root->pagename_num2str) ? preg_replace('/\/(?:[0-9\-]+|[B0-9][A-Z0-9]{9})$/','/'.$xpwiki->func->get_heading($page),$page) : $page;
		if ($base && strpos($title, $base) === 0) {
			$title = substr($title, strlen($base));
		}
		$tmp_array = array(
			'dotgif' => $plugin['dotgif'] ,
			'dirname' => $plugin['dirname'] ,
			'link' => $xpwiki->func->get_page_uri($page, true) ,
			'id' =>  $xpwiki->func->get_pgid_by_name($page) ,
			'server_time' => $server_time ,
			'user_time' => $user_time ,
			'name' => 'pgid' ,
			'title' => $myts->makeTboxData4Show($title)
		) ;
		if( $just1gif ) {
			// just 1 gif per a plugin & per a day
			$plugin_returns[ $target_date ][ $plugin['dirname'] ] = $tmp_array ;
		} else {
			// multiple gifs allowed per a plugin & per a day
			$plugin_returns[ $target_date ][] = $tmp_array ;
		}
	}
}
