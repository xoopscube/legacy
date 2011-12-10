<?php

function b_bulletin_bigstory_show($options) {

	global $xoopsDB;

	$myts =& MyTextSanitizer::getInstance();

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	if (!isset($options[1])){
		$options[1] = 0 ;//(0=show all for d3pipes)
	}
	$categories = empty($options[1]) ? 0 : array_map( 'intval' , explode( ',' , $options[1] ) ) ;//(0=show all)

	require dirname( dirname( __FILE__ ) ).'/include/configs.inc.php';
	require_once dirname(dirname(__FILE__)).'/class/bulletingp.php' ;

	$block = array();
//ver3.0 can_read access
	$gperm =& BulletinGP::getInstance($mydirname) ;
	$can_read_topic_ids = $gperm->makeOnTopics('can_read');
	if (empty($can_read_topic_ids)){
		return false;
	}

	$tdate = mktime(0,0,0,date("n"),date("j"),date("Y"));
//ver2.0
//	$result = $xoopsDB->query("SELECT storyid, title FROM ".$xoopsDB->prefix("{$mydirname}_stories")." WHERE published > ".$tdate." AND published < ".time()." AND (expired > ".time()." OR expired = 0) ORDER BY counter DESC",1,0);
//ver3.0
	$sql = "SELECT s.*, t.topic_pid, t.topic_imgurl, t.topic_title, t.topic_created, t.topic_modified";
	$sql .= ' FROM ' . $xoopsDB->prefix($mydirname.'_stories') . ' s, ' . $xoopsDB->prefix($mydirname.'_topics') . ' t';
	$sql .= ' WHERE s.type > 0 AND s.published < '.time().' AND s.published > 0 AND (s.expired = 0 OR s.expired > '.time().') AND s.topicid = t.topic_id AND s.block = 1';
	$sql .= " AND s.published > ".$tdate;
	if (!empty($categories)){
		$sql .= ' AND s.topicid IN ('.implode(',',$categories).')';
	}
	$sql .= ' AND s.topicid IN ('.implode(',',$can_read_topic_ids).')';
	$sql .= " ORDER BY counter DESC";
	$result = $xoopsDB->query($sql,1,0);
	//ver3.0 fix when all no data
	while ( $myrow = $xoopsDB->fetchArray($result) ) {
		$block['message'] = _MB_BULLETIN_TMRSI;
		$block['title']   = $myts->makeTboxData4Show($myrow['title']);//ver3.0 changed
		$block['storyid'] = $myrow['storyid'];//ver3.0 changed
		$block['raw_data'] = $myrow;
	}
	if (empty($block)){
		$block['message'] = _MB_BULLETIN_NOTYET;
	}


	$block['mydirname'] = $mydirname;


	return $block;

}

function b_bulletin_bigstory_edit($options) {

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname,
		'options' => $options
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_bigstory.html' ) ;
}

?>