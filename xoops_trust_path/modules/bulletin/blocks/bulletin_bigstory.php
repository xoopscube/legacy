<?php

function b_bulletin_bigstory_show($options) {

	global $xoopsDB;

	$myts =& MyTextSanitizer::getInstance();

	$mydirname = $options[0] ;

	$block = array();
	$tdate = mktime(0,0,0,date("n"),date("j"),date("Y"));
	$result = $xoopsDB->query("SELECT storyid, title FROM ".$xoopsDB->prefix("{$mydirname}_stories")." WHERE published > ".$tdate." AND published < ".time()." AND (expired > ".time()." OR expired = 0) ORDER BY counter DESC",1,0);
	list($fsid, $ftitle) = $xoopsDB->fetchRow($result);
	if ( !$fsid && !$ftitle ) {
		$block['message'] = _MB_BULLETIN_NOTYET;
	} else {
		$block['message'] = _MB_BULLETIN_TMRSI;
		$block['title']   = $myts->makeTboxData4Show($ftitle);
		$block['storyid'] = $fsid;
	}
	$block['mydirname'] = $mydirname;

	return $block;

}

?>