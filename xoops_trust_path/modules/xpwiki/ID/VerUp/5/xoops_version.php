<?php

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' ) ;

$mydirname = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/xoops_version.php' ;

///////////////////////////////////////
// Sub menu in Main menu block.
// You can edit freely add and remove.

if (@$GLOBALS['Xpwiki_'.$mydirname]['is_newable']) {
	$modversion['sub'][] = array(
		'name' => constant($constpref . '_MENU_NEWPAGE') ,
		'url'  => '?cmd=newpage' );
}

$modversion['sub'][] = array(
		'name' => constant($constpref . '_MENU_RECENT') ,
		'url'  => '?RecentChanges' );

$modversion['sub'][] = array(
	'name' => constant($constpref . '_MENU_PAGELIST') ,
	'url'  => '?cmd=list' );

$modversion['sub'][] = array(
	'name' => constant($constpref . '_MENU_HELP') ,
	'url'  => '?Help' );

if (@$GLOBALS['Xpwiki_'.$mydirname]['pgid']) {

	$modversion['sub'][] = array(
		'name' => '&#187; '.constant($constpref . '_MENU_RELAYTED') ,
		'url'  => '?cmd=related&amp;pgid=' . $GLOBALS['Xpwiki_'.$mydirname]['pgid'] . '#'.$mydirname.'_navigator' );

	if (@$GLOBALS['Xpwiki_'.$mydirname]['is_editable']) {
		$modversion['sub'][] = array(
			'name' => '&#187; '.constant($constpref . '_MENU_EDIT') ,
			'url'  => '?cmd=edit&amp;pgid=' . $GLOBALS['Xpwiki_'.$mydirname]['pgid'] . '#'.$mydirname.'navigator' );
	} else {
		$modversion['sub'][] = array(
			'name' => '&#187; '.constant($constpref . '_MENU_SOURCE') ,
			'url'  => '?cmd=backup&amp;action=source&amp;pgid=' . $GLOBALS['Xpwiki_'.$mydirname]['pgid'] . '#'.$mydirname.'navigator' );
	}

	$modversion['sub'][] = array(
		'name' => '&#187; '.constant($constpref . '_MENU_DIFF') ,
		'url'  => '?cmd=backup&amp;action=diff&amp;pgid=' . $GLOBALS['Xpwiki_'.$mydirname]['pgid'] . '#'.$mydirname.'navigator' );

	$modversion['sub'][] = array(
		'name' => '&#187; '.constant($constpref . '_MENU_BACKUPS') ,
		'url'  => '?cmd=backup&amp;pgid=' . $GLOBALS['Xpwiki_'.$mydirname]['pgid'] . '#'.$mydirname.'navigator' );

	$modversion['sub'][] = array(
		'name' => '&#187; '.constant($constpref . '_MENU_ATTACHES') ,
		'url'  => '?plugin=attach&amp;pcmd=list&amp;pgid=' . $GLOBALS['Xpwiki_'.$mydirname]['pgid'] . '#'.$mydirname.'navigator' );

	if (@$GLOBALS['Xpwiki_'.$mydirname]['sw_referer']) {
		$modversion['sub'][] = array(
			'name' => '&#187; '.constant($constpref . '_MENU_REFERER') ,
			'url'  => '?plugin=referer&amp;pgid=' . $GLOBALS['Xpwiki_'.$mydirname]['pgid'] . '#'.$mydirname.'navigator' );
	}
}
