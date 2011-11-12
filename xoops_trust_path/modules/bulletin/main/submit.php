<?php
// Get Ctrl
$op = isset($_POST['op']) ? trim($_POST['op']) : 'form';
$op = isset($_GET['op']) && $_GET['op'] == 'delete' ? 'delete' : $op;
// error log
$errors = array();
// Ticket function loading
require_once dirname(dirname(__FILE__))."/include/gtickets.php";
if ( !empty($_POST['preview']) ) {
	if ( ! $xoopsGTicket->check() ) {
		$errors['ticket'] = 'Ticket Error';
		$op = 'form';
	} else {
		$op = 'preview';
	}
}elseif( !empty($_POST['post']) ) {
	if ( ! $xoopsGTicket->check() ) {
		$errors['ticket'] = 'Ticket Error';
		$op = 'form';
	} else {
		$op = 'post';
	}
}
$storyid = isset( $_GET['storyid'] ) ? intval($_GET['storyid']) : 0 ;
$storyid = isset( $_POST['storyid'] ) ? intval($_POST['storyid']) : $storyid ;
$return = isset( $_GET['return'] ) ? intval($_GET['return']) : 0 ;
$return = isset( $_POST['return'] ) ? intval($_POST['return']) : $return ;
// In Case of Topic id
$topicid = isset($_GET['topicid']) ? intval($_GET['topicid']) : 0;
$topicid = isset($_POST['topicid']) ? intval($_POST['topicid']) : $topicid;
/*
 * Data loading section
 */
$story = new Bulletin( $mydirname , $storyid );
if ( $storyid ){
	$topicid = $story->getVar('topicid');
}
if( $topicid ){
	$story->setVar('topicid', $topicid);
}
// In case of No Topic
$BTopic = new BulletinTopic( $mydirname, $topicid );
if( !$BTopic->topicExists() ){
	die(_MD_NO_TOPICS);
	exit;
}
$xoopsTpl->assign('topic_title', $BTopic->topic_title());

// Chanege the WSYWIG editor
if( ! empty( $_REQUEST['using_fck'] ) ) {
	$_POST['text'] = $_POST['text_fck'] ;
}

$str_arr = array('title','text');
$int_arr = array('topicid','type','topicimg','published','expired');
$bai_arr = array('html','smiley','br','xcode','autodate','autoexpdate','notifypub','block','ihome','approve');
foreach( $str_arr as $k ){
	if( isset($_POST[$k]) ) $story->setVar($k, $_POST[$k]);
}
foreach( $int_arr as $k ){
	if( isset($_POST[$k]) ) $story->setVar($k, $_POST[$k]);
}
foreach( $bai_arr as $k ){
	if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
		$_POST[$k] = isset( $_POST[$k] ) ? 1 : 0 ;
		$story->setVar($k, $_POST[$k]);
	}
}

// relation
if( $gperm->group_perm(7) && $bulletin_use_relations ){
	if( isset($_POST['storyidRH']) && isset($_POST['dirnameR']) && is_array($_POST['storyidRH']) && is_array($_POST['dirnameR']) ){
		foreach( $_POST['storyidRH'] as $k => $v ){
			$relations[$k]['dirname'] = $_POST['dirnameR'][$k];
			$relations[$k]['linkedid'] = intval($v);
		}
	}elseif( $storyid > 0 && is_array($relation_arr = $story->relation->getRelations($storyid)) && $op == 'form' ){
		$relations = $relation_arr;
	}else{
		$relations = array();
	}
}

if( empty( $storyid ) ){
	// 権限がない場合HTMLをOFF
	if( !$gperm->group_perm(4) ){
		$story->setVar('html', 0);
		$story->setVar('br', 1);
	}
	if( !$gperm->group_perm(2) ){
		$story->setVar('approve', 0);
	}
}

if( $op == 'post' ){
	if(!$gperm->proceed4topic("can_post",$topicid)){ die(_NOPERM); exit(); }
	$time = time();
	$auto['year']  = isset( $_POST['auto']['year'] )  ? intval( $_POST['auto']['year'] )  : formatTimestamp($time, 'Y');
	$auto['month'] = isset( $_POST['auto']['month'] ) ? intval( $_POST['auto']['month'] ) : formatTimestamp($time, 'n');
	$auto['day']   = isset( $_POST['auto']['day'] )   ? intval( $_POST['auto']['day'] )   : formatTimestamp($time, 'd');
	$auto['hour']  = isset( $_POST['auto']['hour'] )  ? intval( $_POST['auto']['hour'] )  : formatTimestamp($time, 'H');
	$auto['min']   = isset( $_POST['auto']['min'] )   ? intval( $_POST['auto']['min'] )   : formatTimestamp($time, 'i');
	$auto['sec']   = isset( $_POST['auto']['sec'] )   ? intval( $_POST['auto']['sec'] )   : date('s');
	$autoexp['year']  = isset( $_POST['autoexp']['year'] )  ? intval( $_POST['autoexp']['year'] )  : formatTimestamp($time, 'Y');
	$autoexp['month'] = isset( $_POST['autoexp']['month'] ) ? intval( $_POST['autoexp']['month'] ) : formatTimestamp($time, 'n');
	$autoexp['day']   = isset( $_POST['autoexp']['day'] )   ? intval( $_POST['autoexp']['day'] )   : formatTimestamp($time, 'd');
	$autoexp['hour']  = isset( $_POST['autoexp']['hour'] )  ? intval( $_POST['autoexp']['hour'] )  : formatTimestamp($time, 'H');
	$autoexp['min']   = isset( $_POST['autoexp']['min'] )   ? intval( $_POST['autoexp']['min'] )   : formatTimestamp($time, 'i');
	$autoexp['sec']   = isset( $_POST['autoexp']['sec'] )   ? intval( $_POST['autoexp']['sec'] )   : date('s');
	// new post
	if ( empty( $storyid ) ){
		$story->setVar('hostname', xoops_getenv('REMOTE_ADDR'));
		$story->setVar('uid', $my_uid);
		$story->devideHomeTextAndBodyText();

		// 自動承認かどうか
		if( $gperm->group_perm(2) ){
			$story->setVar('type', $story->getVar('approve') ); // GIJ
			if(!$gperm->proceed4topic("post_auto_approved",$topicid)){ $story->setVar('type', 0); }
		}else{
			$story->setVar('type', 0);
		}

		// 掲載予定日設定のルーチン
		if ( $story->getVar('autodate') == 1 && $gperm->group_perm(3) ){
			$pubdate = mktime( $auto['hour'], $auto['min'], $auto['sec'], $auto['month'], $auto['day'], $auto['year'] );
			$offset  = $xoopsUser->timezone() - $xoopsConfig['server_TZ'];
			$pubdate = $pubdate - ( $offset * 3600 );
			$story->setVar('published', $pubdate);
		}else{
			$story->setVar('published', time());
		}
		// 掲載終了日設定のルーティン
		if ( $story->getVar('autoexpdate') == 1 && $gperm->group_perm(3) ){
			$expdate = mktime( $autoexp['hour'], $autoexp['min'], $autoexp['sec'], $autoexp['month'], $autoexp['day'], $autoexp['year'] );
			$offset = $xoopsUser -> timezone() - $xoopsConfig['server_TZ'];
			$expdate = $expdate - ( $offset * 3600 );
			$story->setVar('expired', $expdate);
		}else{
			$story->setVar('expired', 0);
		}
		$is_new = true;
	}else{
		// edited post
		
		if(!$gperm->proceed4topic("can_edit",$topicid)){ die(_NOPERM); exit(); }
		$story->devideHomeTextAndBodyText();

		// approve this article
		$approved = 0;
		if ( $story->getVar('approve') == 1 ){
			if( $story->getVar('type') == 0) $approved = 1;
			$story->setVar('type', 1);
		}else{
			$story->setVar('type', 0);
		}

		// 掲載予定日設定のルーチン
		if ( $story->getVar('autodate') == 1 ){
			$pubdate = mktime( $auto['hour'], $auto['min'], $auto['sec'], $auto['month'], $auto['day'], $auto['year'] );
			$offset = $xoopsUser -> timezone();
			$offset = $offset - $xoopsConfig['server_TZ'];
			$pubdate = $pubdate - ( $offset * 3600 );
			$story->setVar('published', $pubdate);
		} else {
			$story->setVar('published', $time);
		}

		// 掲載終了日設定のルーティン
		if ( $story->getVar('autoexpdate') == 1 ){
			$expdate = mktime( $autoexp['hour'], $autoexp['min'], $autoexp['sec'], $autoexp['month'], $autoexp['day'], $autoexp['year'] );
			if ( !empty( $autoexpdate ) ) $offset = $xoopsUser -> timezone() - $xoopsConfig['server_TZ'];
			$expdate = $expdate - ( $offset * 3600 );
			$story->setVar('expired', $expdate);
		} else {
			$story->setVar('expired', 0);
		}
		$is_new = false;
	}
	//DB挿入時にエラーが発生したら
	if(!$story->store()) {
		die(_MD_THANKS_BUT_ERROR);
	}

	// relation
	if( $gperm->group_perm(7) && $bulletin_use_relations ){
		$story->relation->storyid = $story->getVar('storyid');
		$story->relation->cleanup();
		$story->relation->store($relations);
	}

	if( $is_new ){
		// イベント通知処理
		$notification_handler =& xoops_gethandler('notification');
		$tags = array();
		$tags['STORY_NAME'] = $myts->stripSlashesGPC($story->getVar('title', 'n'));
		$tags['STORY_URL']  = $mydirurl.'/index.php?page=article&storyid=' . $story->getVar('storyid');
		if($gperm->group_perm(2)){
			$notification_handler->triggerEvent('global', 0, 'new_story', $tags);
		} else {
			// admin only
			$tags['WAITINGSTORIES_URL'] = $mydirurl.'/index.php?mode=admin&op=newarticle';
			$notification_handler->triggerEvent('global', 0, 'story_submit', $tags, $gperm->getAdminUsers());
		}
		// 承認したときに通知を受け取る
		if ($story->getVar('notifypub') == 1 && !$gperm->group_perm(2)) {
			require_once XOOPS_ROOT_PATH.'/include/notification_constants.php';
			$notification_handler->subscribe('story', $story->getVar('storyid'), 'approve', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
		}
		//投稿数加算処理
		if ($gperm->group_perm(2) && is_object($xoopsUser) && $bulletin_plus_posts == 1) {
			$xoopsUser->incrementPost();
		}
		// 自動承認のときはメッセージを変える
		if($gperm->group_perm(2)){
			redirect_header($mydirurl.'/index.php', 2, _MD_THANKS_AUTOAPPROVE);
			exit;
		}
		redirect_header($mydirurl.'/index.php', 3, _MD_THANKS);
		exit;
	}else{
		// イベント通知処理
		$notification_handler =& xoops_gethandler('notification');
		$tags = array();
		$tags['STORY_NAME'] = $myts->stripSlashesGPC($story->getVar('title', 'n'));
		$tags['STORY_URL']  = $mydirurl.'/index.php?page=article&storyid=' . $story->getVar('storyid');
		// 承認の通知
		if ( $approved == 1 ){
			$notification_handler->triggerEvent( 'story', $story->getVar('storyid'), 'approve', $tags );
			$notification_handler->triggerEvent('global', 0, 'new_story', $tags);
		}
		if ( $return == 1 || $story->getVar('published') > time() ){
			redirect_header($mydirurl.'/index.php?mode=admin&op=list', 3, _MD_DBPUDATED);
		}else{
			redirect_header($mydirurl.'/index.php?page=article&storyid='.$story->getVar('storyid'), 3, _MD_DBPUDATED);
		}
		exit;
	}
}
if( $op == 'preview' ){
	require_once XOOPS_ROOT_PATH.'/header.php';

	// remove extra slashes only for preview
	if( get_magic_quotes_gpc() ) {
		$story->setVar( 'title' , stripslashes( $story->getVar( 'title' , 'n' ) ) ) ;
		$story->setVar( 'text' , stripslashes( $story->getVar( 'text' , 'n' ) ) ) ;
	}
	$p_title = $myts->makeTboxData4Show($story->getVar('title', 'n'), 0, 0);
	$p_hometext = str_replace('[pagebreak]', '<br style="page-break-after:always;" />', $myts->displayTarea($story->getVar('text', 'n'), $story->getVar('html'), $story->getVar('smiley'), $story->getVar('xcode'), 1, $story->getVar('br')));
//	themecenterposts($p_title, $p_hometext);
//	echo '<br />';
	$xoopsTpl->assign('preview', array('title' => $p_title, 'hometext' => $p_hometext));
	$op = 'form';
}
if( $op == 'form' ){
	// for edit
	$topics = $gperm->makeOnTopics("can_post");
	if ($topicid==0) $topicid = $topics[0];
	$proceed = $gperm->proceed4topic("can_post",$topicid);
	if (!$topics || !$proceed){ die(_NOPERM); exit(); }
	
	$xoopsTpl->assign('topic_selbox', $BTopic->makeMyTopicList($topicid,$topics) );

	if( $storyid  && $story->getVar('text', 'n') == '' ){
		$story->unifyHomeTextAndBodyText();
	}

	// 掲載日時
	if( isset($_POST['auto']) && is_array($_POST['auto']) ){
		$auto = mktime( $_POST['auto']['hour'], $_POST['auto']['min'], @$_POST['auto']['sec'], $_POST['auto']['month'], $_POST['auto']['day'], $_POST['auto']['year'] );
	} elseif ( $story->getVar('published') > 0 ) {
		$auto = $story->getVar('published');
		$story->setVar('autodate', 1);
	} else {
		$auto = time();
	}

	// 掲載終了日時
	if( isset($_POST['auto']) && is_array($_POST['autoexp']) ){
		$autoexp = mktime( $_POST['autoexp']['hour'], $_POST['autoexp']['min'], @$_POST['autoexp']['sec'], $_POST['autoexp']['month'], $_POST['autoexp']['day'], $_POST['autoexp']['year'] );
	} elseif ( $story->getVar('expired') > 0 ) {
		$autoexp = $story->getVar('expired');
		$story->setVar('autoexpdate', 1);
	} else {
		$autoexp = time();
	}

	$xoopsOption['template_main'] = "{$mydirname}_submit.html";
//	require_once sprintf('%s/modules/legacy/language/%s/main.php' ,XOOPS_ROOT_PATH, $xoopsConfig['language']);

	require_once XOOPS_ROOT_PATH.'/header.php';
	if( !empty($errors) ) xoops_error($errors);
	// require dirname(dirname(__FILE__)).'/include/storyform.inc.php';
	require dirname(dirname(__FILE__)).'/include/storyform_templatevars.inc.php';
	$xoopsTpl->assign( 'xoops_breadcrumbs' , array(
		array( 'name' => $xoopsModule->getVar('name') , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/' ) ,
		array( 'name' => _MD_SUBMITNEWS ) ,
	) ) ; // GIJ
	$xoopsTpl->assign( 'mod_config' , $xoopsModuleConfig ) ;

	require_once XOOPS_ROOT_PATH.'/footer.php';
}

if( $op == 'delete' ){
	if(!$gperm->proceed4topic("can_delete",$topicid)){ die(_NOPERM); exit(); }
	if(!$isadmin){
		die(_NOPERM);
		exit();
	}
	$storyid = isset( $_GET['storyid'] ) ? intval( $_GET['storyid'] ) : 0 ;
	
	if ( !empty( $_POST['ok'] ) ){

		//check ticket
		if ( ! $xoopsGTicket->check() ) {
			die('Ticket Error');
			exit();
		}

		$storyid = isset( $_POST['storyid'] ) ? intval( $_POST['storyid'] ) : 0 ;

		if ( empty($storyid) ){
			die( _MD_EMPTYNODELETE );
			exit();
		}
		$story = new Bulletin( $mydirname , $storyid );
		if (!$story){
			die( _MD_EMPTYNODELETE );
			exit();
		}
		// 関連記事削除
		$story->relation->queryUnlinkById($storyid);
		$story->relation->queryDelete(1);
		$story -> delete();
		xoops_comment_delete( $xoopsModule->getVar('mid'), $storyid );
		xoops_notification_deletebyitem( $xoopsModule->getVar('mid'), 'story', $storyid );
		if( $return == 1){
			redirect_header( $mydirurl.'/index.php?mode=admin&op=list', 1, _MD_DBPUDATED );
		}else{
			redirect_header( $mydirurl.'/index.php', 1, _MD_DBPUDATED );
		}
		exit();
	}else{
		require_once XOOPS_ROOT_PATH.'/header.php';
		xoops_confirm( array( 'op' => 'delete', 'storyid' => $storyid, 'ok' => 1, 'return' => $return, 'XOOPS_G_TICKET'=>$xoopsGTicket->issue( __LINE__ ) ), 'index.php?page=submit', _MD_RUSUREDEL );
		require_once XOOPS_ROOT_PATH.'/footer.php';
	}
}
?>