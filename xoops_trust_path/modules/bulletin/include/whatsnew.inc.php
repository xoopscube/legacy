<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

eval( '

function '.$mydirname.'_new( $limit=0, $offset=0 )
{
	return bulletin_whatsnew_base( "'.$mydirname.'", $limit, $offset, "'.$category_option.'" ) ;
}

' );

if ( ! function_exists('bulletin_whatsnew_base') ) {
	function bulletin_whatsnew_base( $mydirname, $limit=0, $offset=0, $category_option='' )
	{
		$db =& Database::getInstance() ;
		$categories = empty($category_option) ? 0 : array_map( 'intval' , explode( ',' , $category_option ) ) ;//(0=show all)

		$myts =& MyTextSanitizer::getInstance();

		require_once dirname(dirname(__FILE__)).'/class/bulletingp.php' ;

		$ret = array();

//ver3.0 can_read access
		$gperm =& BulletinGP::getInstance($mydirname) ;
		$can_read_topic_ids = $gperm->makeOnTopics('can_read');
		if (empty($can_read_topic_ids)){
			return $ret;
		}

		// DB table name
		$sql = "SELECT s.*, t.topic_pid, t.topic_imgurl, t.topic_title, t.topic_created, t.topic_modified";
		$sql .= ' FROM ' . $db->prefix($mydirname.'_stories') . ' s, ' . $db->prefix($mydirname.'_topics') . ' t';
		$sql .= ' WHERE s.type > 0 AND s.published < '.time().' AND s.published > 0 AND (s.expired = 0 OR s.expired > '.time().') AND s.topicid = t.topic_id AND s.block = 1';
		if (!empty($categories)){
			$sql .= ' AND s.topicid IN ('.implode(',',$categories).')';
		}
		$sql .= ' AND s.topicid IN ('.implode(',',$can_read_topic_ids).')';
		$sql .= ' ORDER BY published DESC';

		$result = $db->query($sql, $limit, $offset);

		$URL_MOD = XOOPS_URL."/modules/".$mydirname;

		$i = 0;

		while( $row = $db->fetchArray($result))
		{
			$id     = $row['storyid'];
			$catid  = $row['topicid'];

	// bulletin 2.02
			$ret[$i]['link']     = $URL_MOD."/index.php?page=article&storyid=".$id;
		    $ret[$i]['pda']      = $URL_MOD."/index.php?page=print&storyid=".$id;

			$ret[$i]['cat_link'] = $URL_MOD."/index.php?storytopic=".$catid;
			$ret[$i]['title']    = $row['title'];
			$ret[$i]['cat_name'] = $row['topic_title'];
			$ret[$i]['time'] = $row['published'];
			$ret[$i]['uid']  = $row['uid'];
			$ret[$i]['hits'] = $row['counter'];
			$ret[$i]['id']   = $id;

	// description
			$html   = $row['html'];
			$smiley = $row['smiley'];
			$xcode  = $row['xcode'];
			$image  = 1;
			$br     = $row['br'];

			$desc = $row['hometext'];
			$desc = $myts->displayTarea($desc, $html, $smiley, $xcode, $image, $br);
			$ret[$i]['description'] = $desc;

			$i++;
		}

		return $ret;
	}
}

?>