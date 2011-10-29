<?php
function b_bulletin_recent_comments_show($options) {

	global $xoopsDB;

	$mydirname = $options[0] ;

	$rs = $xoopsDB->query( "SELECT mid FROM ".$xoopsDB->prefix('modules')." WHERE dirname='$mydirname'" ) ;
	list( $bulletin_mid ) = $xoopsDB->fetchRow( $rs ) ;

	require_once XOOPS_ROOT_PATH.'/include/comment_constants.php';

	$block = array();

	$comment_handler =& xoops_gethandler('comment');
	$member_handler  =& xoops_gethandler('member');

	$criteria = new CriteriaCompo(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
	$criteria->add(new Criteria('com_modid', $bulletin_mid));
	$criteria->setLimit(10);
	$criteria->setSort('com_created');
	$criteria->setOrder('DESC');
	$comments =& $comment_handler->getObjects($criteria, true);

	foreach (array_keys($comments) as $i) {
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
	
	return $block;
}


function b_bulletin_recent_comments_edit($options) {
    $form  = '<table>';

    $form .= '</table>';
    return $form;
}

?>