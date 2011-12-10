<?php
function b_bulletin_recent_comments_show($options) {

	global $xoopsDB;

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	if (!isset($options[1])){
		$options[1] = 0 ;//(0=show all for d3pipes)
	}
	$categories = empty($options[1]) ? 0 : array_map( 'intval' , explode( ',' , $options[1] ) ) ;//(0=show all)

	$rs = $xoopsDB->query( "SELECT mid FROM ".$xoopsDB->prefix('modules')." WHERE dirname='$mydirname'" ) ;
	list( $bulletin_mid ) = $xoopsDB->fetchRow( $rs ) ;

	require_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
	require_once dirname(dirname(__FILE__)).'/class/bulletingp.php' ;

	$block = array();
//ver3.0 can_read access
	$gperm =& BulletinGP::getInstance($mydirname) ;
	$can_read_topic_ids = $gperm->makeOnTopics('can_read');
	if (empty($can_read_topic_ids)){
		return false;
	}

	$comment_handler =& xoops_gethandler('comment');
	$member_handler  =& xoops_gethandler('member');

	$criteria = new CriteriaCompo(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
	$criteria->add(new Criteria('com_modid', $bulletin_mid));
	$criteria->setLimit(10);
	$criteria->setSort('com_created');
	$criteria->setOrder('DESC');
	$comments =& $comment_handler->getObjects($criteria, true);
//ver3.0
	$storyids = array();
	foreach (array_keys($comments) as $i) {
		$storyids[] = $comments[$i]->getVar('com_itemid');
	}
	if (empty($storyids)){
		return false;
	}

	$can_read_storyids = array();
	$sql = "SELECT s.storyid";
	$sql .= ' FROM ' . $xoopsDB->prefix($mydirname.'_stories') . ' s, ' . $xoopsDB->prefix($mydirname.'_topics') . ' t';
//TODO published only
//	$sql .= ' WHERE s.type > 0 AND s.published < '.time().' AND s.published > 0 AND (s.expired = 0 OR s.expired > '.time().') AND s.topicid = t.topic_id AND s.block = 1';
//	$sql .= ' AND s.storyid IN ('.implode(',',$storyids).')';
	$sql .= ' WHERE s.storyid IN ('.implode(',',$storyids).')';
	if (!empty($categories)){
		$sql .= ' AND s.topicid IN ('.implode(',',$categories).')';
	}
	$sql .= ' AND s.topicid IN ('.implode(',',$can_read_topic_ids).')';
	$result = $xoopsDB->query($sql);
	while ( $myrow = $xoopsDB->fetchArray($result) ) {
		$can_read_storyids[]= $myrow['storyid'];
	}
	if (empty($can_read_storyids)){
		return false;
	}

	foreach (array_keys($comments) as $i) {
		if (! in_array($comments[$i]->getVar('com_itemid') , $can_read_storyids)){
			continue;
		}
		$mid = $comments[$i]->getVar('com_modid');

		$com['id']     = $i;
		$com['title']  = $comments[$i]->getVar('com_title');
		$com['time']   = formatTimestamp($comments[$i]->getVar('com_created'),'m');
		$com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
		$com['uid']    = $comments[$i]->getVar('com_uid');
		$com['itemid'] = $comments[$i]->getVar('com_itemid');
		$com['rootid'] = $comments[$i]->getVar('com_rootid');
		$com['url']    = XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=article&amp;storyid='.$com['itemid'].'&amp;com_id='.$i.'&amp;com_rootid='.$com['rootid'].'#comment'.$i;
		if ($comments[$i]->getVar('com_uid') > 0) {
			$poster =& $member_handler->getUser($comments[$i]->getVar('com_uid'));
			if (is_object($poster)) {
				$com['poster'] = $poster->getVar('uname');
			}
		}

		$block['comments'][] =& $com;
		unset($com);
	}

	if (empty($block)){
		return false;
	}
	return $block;
}


function b_bulletin_recent_comments_edit($options) {

	$mydirname = empty( $options[0] ) ? basename( dirname( dirname( __FILE__ ) ) ) : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname,
		'options' => $options
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_comment.html' ) ;
	}

?>