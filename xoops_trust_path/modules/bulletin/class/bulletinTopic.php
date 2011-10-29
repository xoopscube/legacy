<?php

require_once XOOPS_ROOT_PATH."/class/xoopstopic.php";

class BulletinTopic extends XoopsTopic{
	
	function BulletinTopic( $mydirname , $topicid=0 )
	{
		$this->db =& Database::getInstance();
		$this->mydirname = $mydirname ;
		$this->table = $this->db->prefix( "{$mydirname}_topics" );
		$this->ts =& MyTextSanitizer::getInstance();
		
		if ( is_array($topicid) ) {
			$this->makeTopic($topicid);
		} elseif ( $topicid != 0 ) {
			$this->getTopic(intval($topicid));
		} else {
			$this->topic_id = $topicid;
		}
	}

	function topicExists() 
	{
		$sql = "SELECT COUNT(*) from ".$this->table;
		$result = $this->db->query($sql);
		list($count) = $this->db->fetchRow($result);
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	function makePankuzu($topic_id=0, $ret = array())
	{
		$result = ( "SELECT `topic_pid`, `topic_title` FROM ".$this->table." WHERE `topic_id` = ".intval($topic_id) );
		$result = $this->db->query($result);
		list($topic_pid, $topic_title) = $this->db->fetchRow($result);
		$ret[] = array('topic_id' => $topic_id, 'topic_title' => $topic_title);
		if($topic_pid > 0){
			$ret = $this->makePankuzu($topic_pid, $ret);
		}else{
			$ret = array_reverse($ret);
		}

		return $ret;

	}

	function makePankuzuForHTML($topic_id=0)
	{
		$pankuzu = $this->makePankuzu($topic_id);
		foreach($pankuzu as $k => $v){
			$pankuzu[$k]['topic_title'] = $this->ts->htmlSpecialChars($pankuzu[$k]['topic_title']);
		}
		return $pankuzu;
	}


	// GIJ
	function getAllChildId( $topic_ids = null )
	{
		$db =& $this->db ;
		
		if( empty( $topic_ids ) ) $topic_ids = array( $this->topic_id ) ;
		$result = $db->query( "SELECT distinct topic_id FROM ".$this->table." WHERE topic_pid IN (".implode(',',$topic_ids).")" ) ;
		$children = array() ;
		while( list( $child_id ) = $db->fetchRow( $result ) ) $children[] = $child_id ;
		if( empty( $children ) ) return array() ;
		else {
			return array_merge( $children , $this->getAllChildId( $children ) ) ;
		}
	}


	// GIJ
	function makeTopicSelBox( $none = false , $seltopic = null , $selname = '', $onchange = '' )
	{
		$seltopic = is_null( $seltopic ) ? intval( $this->topic_id ) : intval( $seltopic ) ;

		ob_start();

		$tree = new XoopsTree( $this->table , "topic_id" , "topic_pid" ) ;
		$tree->makeMySelBox( "topic_title" , "topic_title" , $seltopic , $none , $selname , $onchange ) ;

		$ret = ob_get_contents();
		ob_end_clean();	

		//$ret = str_replace('topic_id','topicid', $ret); // non-sense code?
		return $ret;
	}
	// Bluemoon
	function makeMyTopicList($preset_id=0, $row=NULL){
		$this->id = "topic_id";
		$this->pid = "topic_pid";
		$title = "topic_title";
		$order = "topic_title";
		$onchange = "";
		$none = count($row);
		$myts =& MyTextSanitizer::getInstance();
		$ret = "<select name='topicid'";
		if ( $onchange != "" ) {
			$ret .= " onchange='".$onchange."'";
		}
		$ret .= ">\n";
		$sql = "SELECT ".$this->id.",".$title." FROM ".$this->table." WHERE ".$this->pid."=0";
		if ( $order != "" ) {
			$sql .= " ORDER BY $order";
		}
		$result = $this->db->query($sql);
		if ( $none ) {
			$ret .= "<option value='0'>----</option>\n";
		}
		while ( list($catid, $name) = $this->db->fetchRow($result) ) {
			if (is_array($row) && !in_array($catid,$row)) continue;
			$sel = "";
			if ( $catid == $preset_id ) {
				$sel = " selected='selected'";
			}
			$ret .= "<option value='$catid'$sel>$name</option>\n";
			$sel = "";
		}
		$ret .= "</select>\n";
		return $ret;
	}
	

	// override
	function store()
	{
		$mode = empty( $this->topic_id ) ? 'insert' : 'udpate' ;
		parent::store() ;
		if( $mode == 'insert' ) {
			$this->topic_id = $this->db->getInsertId() ;
			$this->db->query( "UPDATE ".$this->table." SET topic_created=UNIX_TIMESTAMP(),topic_modified=UNIX_TIMESTAMP() WHERE topic_id=".$this->topic_id ) ;
		} else {
			$this->db->query( "UPDATE ".$this->table." SET topic_modified=UNIX_TIMESTAMP() WHERE topic_id=".$this->topic_id ) ;
		}
	}


}
?>