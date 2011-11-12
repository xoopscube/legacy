<?php

$fromyear  = (isset($_GET['year']))  ? intval($_GET['year'])  : 0 ;
$frommonth = (isset($_GET['month'])) ? intval($_GET['month']) : 0 ;

//テンプレート
$xoopsOption['template_main'] = "{$mydirname}_archive.html";

require_once XOOPS_ROOT_PATH.'/header.php';

$result = Bulletin::getPublishedDays( $mydirname ) ;

if (!$result) {
	redirect_header($mydirurl.'/index.php',3,_MD_NO_ARCIVES);
}

// 月名のリスト
$months_arr = array(1=>_MD_JANUARY, 2=>_MD_FEBRUARY, 3=>_MD_MARCH, 4=>_MD_APRIL, 5=>_MD_MAY, 6=>_MD_JUNE, 7=>_MD_JULY, 8=>_MD_AUGUST, 9=>_MD_SEPTEMBER, 10=>_MD_OCTOBER, 11=>_MD_NOVEMBER, 12=>_MD_DECEMBER);

// タイムゾーン
$useroffset = $xoopsConfig['default_TZ'];
if($xoopsUser){
	$timezone = $xoopsUser->timezone();
	if(isset($timezone)){
		$useroffset = $xoopsUser->timezone();
	}
}

// 記事が存在する期間のカレンダー配列を作る
$start_year = formatTimestamp(reset($result), "Y", $useroffset);
$end_year   = formatTimestamp(end($result),   "Y", $useroffset);
$year_arr = array();
for($i=$start_year;$i<=$end_year;$i++){
	$year_arr[$i] = array(1=>1,2,3,4,5,6,7,8,9,10,11,12);
}

// 記事が存在する月のリスト配列を作る
$exist_arr = array();
$total = count($result);
for($i=0;$i<$total;$i++){
	$this_year  = intval(formatTimestamp($result[$i], "Y", $useroffset));
	$this_month = intval(formatTimestamp($result[$i], "n", $useroffset));
	$exist_arr[$this_year][$this_month] = $this_month;
}

// カレンダーのassing
$i = 0;
$years  = array();
foreach($year_arr as $caly => $calms){

	$months = array();
	foreach($calms as $calm){
		$months[$calm]['string'] = $months_arr[$calm];
		$months[$calm]['number'] = $calm;
		if( isset($exist_arr[$caly][$calm]) ){
			$months[$calm]['link'] = true;
		}
		if( $fromyear == $caly && $frommonth == $calm ){
			$months[$calm]['current'] = true;
		}
	}

	$years[$i]['number'] = $caly;
	$years[$i]['string'] = sprintf(_MD_YEAR_X ,$caly);
	$years[$i]['months'] = $months;
	$i++;
}
$xoopsTpl->assign('years', $years);

// 記事のリストassign
if ($fromyear != 0 && $frommonth != 0) {
	$xoopsTpl->assign('show_articles', true);
	$xoopsTpl->assign('currentmonth', $months_arr[$frommonth]);
	$xoopsTpl->assign('currentyear', $fromyear);

	// must adjust the selected time to server timestamp
	$timeoffset = $useroffset - $xoopsConfig['server_TZ'];
	$monthstart = mktime(0 - $timeoffset, 0, 0, $frommonth, 1, $fromyear);
	$monthend   = mktime(23 - $timeoffset, 59, 59, $frommonth + 1, 0, $fromyear);
	$monthend   = ($monthend > time()) ? time() : $monthend;
	
	$article = Bulletin::getArchives( $mydirname , $monthstart,$monthend);
	$scount = count($article);

	for ( $i = 0; $i < $scount; $i++ ) {
		$story = array();
	    	$story['id']         = $article[$i]->getVar('storyid');
	    	$story['topic']      = $article[$i]->topic_title();
	    	$story['topicid']    = $article[$i]->getVar('topicid');
	    	$story['title']      = $article[$i]->getVar('title');
		$story['counter']    = $article[$i]->getVar('counter');
		$story['date']       = formatTimestamp($article[$i]->getVar('published'), $bulletin_date_format, $useroffset);
		$story['print_link'] = 'index.php?page=print&amp;storyid='.$article[$i]->getVar('storyid');
		
		// Tell A Frined使用する場合
		if($bulletin_use_tell_a_frined){
			$story['mail_link'] = XOOPS_URL.'/modules/tellafriend/index.php?target_uri='.rawurlencode( "$mydirurl/index.php?page=article&amp;storyid=".$article[$i]->getVar('storyid') ).'&amp;subject='.rawurlencode(sprintf(_MD_INTARTFOUND,$xoopsConfig['sitename'])) ;
		}else{
			$story['mail_link'] = 'mailto:?subject='.sprintf(_MD_INTARTICLE, $xoopsConfig['sitename']).'&amp;body='.sprintf(_MD_INTARTFOUND, $xoopsConfig['sitename']).':  '.$mydirurl.'/index.php?page=article&amp;storyid='.$article[$i]->getVar('storyid');
		}
		
		$xoopsTpl->append('stories', $story);
	}

	$xoopsTpl->assign('lang_storytotal', sprintf(_MD_THEREAREINTOTAL, $scount));
} else {
	$xoopsTpl->assign('show_articles', false);
}

$xoopsTpl->assign('disp_print_icon', $bulletin_disp_print_icon);
$xoopsTpl->assign('disp_tell_icon', $bulletin_disp_tell_icon );

$xoopsTpl->assign($assing_array);
if($bulletin_assing_rssurl_head){
	$xoopsTpl->assign('xoops_module_header', $rss_feed . $xoopsTpl->get_template_vars( "xoops_module_header" ));
}

$xoopsTpl->assign( 'xoops_breadcrumbs' , array(
	array( 'name' => $xoopsModule->getVar('name') , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/' ) ,
	array( 'name' => _MD_NEWSARCHIVES ) ,
) ) ; // GIJ
$xoopsTpl->assign( 'mod_config' , $xoopsModuleConfig ) ;

require_once XOOPS_ROOT_PATH.'/footer.php';
?>