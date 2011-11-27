<?php

function b_bulletin_category_new_allowed_order()
{
	return array(
		_MB_BULLETIN_DATE . ' DESC' => 'published DESC' ,
		_MB_BULLETIN_HITS . ' DESC' => 'counter DESC' ,
		_MB_BULLETIN_DATE . ' ASC' => 'published ASC' ,
		_MB_BULLETIN_HITS . ' ASC' => 'counter ASC' ,
		'title' => 'title' ,
		'created DESC' => 'created DESC' ,
		'created ASC' => 'created ASC' ,
		'expired DESC' => 'expired DESC' ,
		'expired ASC' => 'expired ASC' ,
		'comments DESC' => 'comments DESC' ,
	) ;
}

function b_bulletin_category_new_show($options) {

	$mydirname = $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$selected_order = empty( $options[1] ) || ! in_array( $options[1] , b_bulletin_category_new_allowed_order() ) ? 'published DESC' : $options[1] ;

	require dirname( dirname( __FILE__ ) ).'/include/configs.inc.php';
	require_once XOOPS_ROOT_PATH.'/class/xoopstree.php';
	
	$mytree = new XoopsTree($table_topics,'topic_id','topic_pid');

	$arr = array();
	// ルートカテゴリを得るクエリ
	if( empty($options[4]) ){
		// 全ルートカテゴリを得る
		$result = $xoopsDB->query("SELECT topic_id, topic_title, topic_imgurl FROM $table_topics WHERE topic_pid=0 ORDER BY topic_title");
	}else{
		// 指定カテゴリのみ
		$result = $xoopsDB->query("SELECT topic_id, topic_title, topic_imgurl FROM $table_topics WHERE topic_id=".$options[4]);
	}

	$block = array();

	while( list($topic_id, $topic_title, $topic_imgurl) = $xoopsDB->fetchRow($result) ){
		$topic = array();
		$topic['title'] = $myts->makeTboxData4Show($topic_title);
		$topic['id'] = $topic_id;

		// トピック画像をセット
		if ($topic_imgurl != '' && file_exists($bulletin_topicon_path.$topic_imgurl) && $options[6]) {
			$topic['topic_url'] = str_replace(XOOPS_ROOT_PATH,XOOPS_URL,$bulletin_topicon_path).$topic_imgurl;
		}

		$where = sprintf("s.type > 0 AND s.published < %u AND s.published > 0 AND (s.expired = 0 OR s.expired > %1\$u) AND s.block = 1 AND (s.topicid=%u", time(), $topic_id);

		// 子ディレクトリを対象に含める
		$arr = $mytree->getAllChildId($topic_id);
		$size = count($arr);
		for($i=0;$i<$size;$i++){
			$where .= " OR s.topicid=".$arr[$i];
		}

		$where .= ")";
		$order = "ORDER BY $selected_order";

		// more をセット
		$sql = sprintf('SELECT COUNT(*) FROM %s s WHERE %s', $table_stories, $where);
		list($count) = $xoopsDB->fetchRow($xoopsDB->query($sql));
		if($count>$options[2]){
			$topic['morelink'] = 1;
		}

		// 本文を表示する
		if($options[5] > 0){

			$sql  = sprintf('SELECT s.storyid, s.topicid, s.title, s.hometext, s.bodytext, s.published, s.expired, s.counter, s.comments, s.uid, s.topicimg, s.html, s.smiley, s.br, s.xcode, t.topic_title, t. topic_imgurl FROM %s s, %s t WHERE %s AND s.topicid = t.topic_id %s', $table_stories, $table_topics, $where, $order);

			$result2 = $xoopsDB->query($sql,$options[5],0);

			while ( $myrow = $xoopsDB->fetchArray($result2) ) {
				$fullstory = array() ; // GIJ
				$fullstory['id']       = $myrow['storyid'];
				$fullstory['posttime'] = formatTimestamp($myrow['published'], $bulletin_date_format);
				$fullstory['date']     = formatTimestamp($myrow['published'], $bulletin_date_format);
				$fullstory['published'] = intval($myrow['published']);
				$fullstory['topicid']  = $myrow['topicid'];
				$fullstory['topic']    = $myts->makeTboxData4Show($myrow['topic_title']);
				$fullstory['title']    = $myts->makeTboxData4Show($myrow['title']);
				$fullstory['text']     = $myts->displayTarea($myrow['hometext'],$myrow['html'],$myrow['smiley'],$myrow['xcode'],1,$myrow['br']);
				$fullstory['hits']     = $myrow['counter'];
				$fullstory['title_link'] = true;
				//ユーザ情報をアサイン
				$fullstory['uid']      = $myrow['uid'];
				$fullstory['uname']    = XoopsUser::getUnameFromId($myrow['uid']);
				$fullstory['realname'] = XoopsUser::getUnameFromId($myrow['uid'], 1);
				$fullstory['morelink'] = '';

				// 文字数カウント処理
				if ( myStrlenText($myrow['bodytext']) > 1 ) {
					$fullstory['bytes']    = sprintf(_MB_BULLETIN_BYTESMORE, myStrlenText($myrow['bodytext']));
					$fullstory['readmore'] = true;
				}else{
					$fullstory['bytes']    = 0;
					$fullstory['readmore'] = false;
				}

				// コメントの数をアサイン
				$ccount = $myrow['comments'];
				if( $ccount == 0 ){
					$fullstory['comentstotal'] = _MB_BULLETIN_COMMENTS;
				}elseif( $ccount == 1 ) {
					$fullstory['comentstotal'] = _MB_BULLETIN_ONECOMMENT;
				}else{
					$fullstory['comentstotal'] = sprintf(_MB_BULLETIN_NUMCOMMENTS, $ccount);
				}

				// 管理者用のリンク
				$fullstory['adminlink'] = 0;

				// トピック画像
				if ( $myrow['topicimg'] ) {
					$fullstory['topic_url'] = makeTopicImgURL($bulletin_topicon_path, $myrow['topic_imgurl']);
					$fullstory['align']     = topicImgAlign($myrow['topicimg']);
				}

				$topic['fullstories'][] = $fullstory;

			}
		}

		if( $options[2] - $options[5] > 0 ){

			$sql  = sprintf('SELECT s.storyid, s.title, s.published, s.expired, s.counter, s.uid FROM %s s WHERE %s %s', $table_stories, $where, $order);

			$result3 = $xoopsDB->query($sql,$options[2]-$options[5],$options[5]);

			while ( $myrow = $xoopsDB->fetchArray($result3) ) {

				// マルチバイト環境に対応
				$story['title']    = $myts->makeTboxData4Show(xoops_substr($myrow['title'], 0 ,$options[3] + 3, '...'));
				$story['id']       = $myrow['storyid'];
				$story['date']     = formatTimestamp($myrow['published'], $bulletin_date_format);
				$story['published'] = intval($myrow['published']);
				$story['hits']     = $myrow['counter'];
				$story['uid']      = $myrow['uid'];
				$story['uname']    = XoopsUser::getUnameFromId($myrow['uid']);
				$story['realname'] = XoopsUser::getUnameFromId($myrow['uid'], 1);

				$topic['stories'][] = $story;
			}

		}

		$block['topics'][] = $topic;
	}

	if( ! empty( $block ) ) {
		$block['mod_config'] = @$bulletin_configs ;
		$block['lang_postedby'] = _POSTEDBY;
		$block['lang_on']       = _ON;
		$block['lang_reads']    = _READS;
		$block['lang_readmore'] = _MB_BULLETIN_READMORE;
		$block['type']  = $selected_order;
		$block['mydirurl'] = XOOPS_URL.'/modules/'.$mydirname;;
		$block['mydirname'] = $mydirname;
	}

	return $block;
}

function b_bulletin_category_new_edit($options) {

	$mydirname = $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	$selected_order = empty( $options[1] ) || ! in_array( $options[1] , b_bulletin_category_new_allowed_order() ) ? 'published DESC' : $options[1] ;

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl =& new XoopsTpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname ,
		'options' => $options ,
		'order_options' => array_flip( b_bulletin_category_new_allowed_order() ) ,
		'selected_order' => $selected_order ,
		'yn_options' => array( 0 => _NO , 1 => _YES ) ,
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_category_new.html' ) ;
}
?>