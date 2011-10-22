<?php
// $Id: xoopsstory.php,v 1.1 2007/05/15 02:34:21 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
include_once XOOPS_ROOT_PATH."/class/xoopstopic.php";
include_once XOOPS_ROOT_PATH."/class/xoopsuser.php";

class XoopsStory
{
    var $table;
	var $storyid;
	var $topicid;
	var $uid;
	var $title;
	var $hometext;
	var $bodytext="";
	var $counter;
	var $created;
	var $published;
	var $expired;
	var $hostname;
	var $nohtml=0;
	var $nosmiley=0;
	var $ihome=0;
	var $notifypub=0;
	var $type;
	var $approved;
	var $topicdisplay;
	var $topicalign;
	var $db;
	var $topicstable;
	var $comments;

	function Story($storyid=-1)
	{
		$this->db =& Database::getInstance();
		$this->table = "";
		$this->topicstable = "";
		if ( is_array($storyid) ) {
			$this->makeStory($storyid);
		} elseif ( $storyid != -1 ) {
			$this->getStory(intval($storyid));
		}
	}

	function setStoryId($value)
	{
		$this->storyid = intval($value);
	}

	function setTopicId($value)
	{
		$this->topicid = intval($value);
	}

	function setUid($value)
	{
		$this->uid = intval($value);
	}

	function setTitle($value)
	{
		$this->title = $value;
	}

	function setHometext($value)
	{
		$this->hometext = $value;
	}

	function setBodytext($value)
	{
		$this->bodytext = $value;
	}

	function setPublished($value)
	{
		$this->published = intval($value);
	}

	function setExpired($value)
	{
		$this->expired = intval($value);
	}

	function setHostname($value)
	{
		$this->hostname = $value;
	}

	function setNohtml($value=0)
	{
		$this->nohtml = $value;
	}

	function setNosmiley($value=0)
	{
		$this->nosmiley = $value;
	}

	function setIhome($value)
	{
		$this->ihome = $value;
	}

	function setNotifyPub($value)
	{
		$this->notifypub = $value;
	}

	function setType($value)
	{
		$this->type = $value;
	}

	function setApproved($value)
	{
		$this->approved = intval($value);
	}

	function setTopicdisplay($value)
	{
		$this->topicdisplay = $value;
	}

	function setTopicalign($value)
	{
		$this->topicalign = $value;
	}

	function setComments($value)
	{
		$this->comments = intval($value);
	}

	function store($approved=false)
	{
		//$newpost = 0;
		$myts =& MyTextSanitizer::getInstance();
		$title =$myts->censorString($this->title);
		$hometext =$myts->censorString($this->hometext);
		$bodytext =$myts->censorString($this->bodytext);
		$title = $myts->makeTboxData4Save($title);
		$hometext = $myts->makeTareaData4Save($hometext);
		$bodytext = $myts->makeTareaData4Save($bodytext);
		if ( !isset($this->nohtml) || $this->nohtml != 1 ) {
			$this->nohtml = 0;
		}
		if ( !isset($this->nosmiley) || $this->nosmiley != 1 ) {
			$this->nosmiley = 0;
		}
		if ( !isset($this->notifypub) || $this->notifypub != 1 ) {
			$this->notifypub = 0;
		}
		if( !isset($this->topicdisplay) || $this->topicdisplay != 0 ) {
			$this->topicdisplay = 1;
		}
		$expired = !empty($this->expired) ? $this->expired : 0;
		if ( !isset($this->storyid) ) {
			//$newpost = 1;
			$newstoryid = $this->db->genId($this->table."_storyid_seq");
			$created = time();
			$published = ( $this->approved ) ? $this->published : 0;

			$sql = sprintf("INSERT INTO %s (storyid, uid, title, created, published, expired, hostname, nohtml, nosmiley, hometext, bodytext, counter, topicid, ihome, notifypub, story_type, topicdisplay, topicalign, comments) VALUES (%u, %u, '%s', %u, %u, %u, '%s', %u, %u, '%s', '%s', %u, %u, %u, %u, '%s', %u, '%s', %u)", $this->table, $newstoryid, $this->uid, $title, $created, $published, $expired, $this->hostname, $this->nohtml, $this->nosmiley, $hometext, $bodytext, 0, $this->topicid, $this->ihome, $this->notifypub, $this->type, $this->topicdisplay, $this->topicalign, $this->comments);
		} else {
			if ( $this->approved ) {
				$sql = sprintf("UPDATE %s SET title = '%s', published = %u, expired = %u, nohtml = %u, nosmiley = %u, hometext = '%s', bodytext = '%s', topicid = %u, ihome = %u, topicdisplay = %u, topicalign = '%s', comments = %u WHERE storyid = %u", $this->table, $title, $this->published, $expired, $this->nohtml, $this->nosmiley, $hometext, $bodytext, $this->topicid, $this->ihome, $this->topicdisplay, $this->topicalign, $this->comments, $this->storyid);
			} else {
				$sql = sprintf("UPDATE %s SET title = '%s', expired = %u, nohtml = %u, nosmiley = %u, hometext = '%s', bodytext = '%s', topicid = %u, ihome = %u, topicdisplay = %u, topicalign = '%s', comments = %u WHERE storyid = %u", $this->table, $title, $expired, $this->nohtml, $this->nosmiley, $hometext, $bodytext, $this->topicid, $this->ihome, $this->topicdisplay, $this->topicalign, $this->comments, $this->storyid);
			}
			$newstoryid = $this->storyid;
		}
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		if ( empty($newstoryid) ) {
			$newstoryid = $this->db->getInsertId();
			$this->storyid = $newstoryid;
		}
		return $newstoryid;
	}

	function getStory($storyid)
	{
		$sql = "SELECT * FROM ".$this->table." WHERE storyid=".$storyid."";
		$array = $this->db->fetchArray($this->db->query($sql));
		$this->makeStory($array);
	}

	function makeStory($array)
	{
		foreach ( $array as $key=>$value ){
			$this->$key = $value;
		}
	}

	function delete()
	{
		$sql = sprintf("DELETE FROM %s WHERE storyid = %u", $this->table, $this->storyid);
		if( !$result = $this->db->query($sql) ) {
			return false;
		}
		return true;
	}

	function updateCounter()
	{
		$sql = sprintf("UPDATE %s SET counter = counter+1 WHERE storyid = %u", $this->table, $this->storyid);
		if ( !$result = $this->db->queryF($sql) ) {
			return false;
		}
		return true;
	}

	function updateComments($total)
	{
		$sql = sprintf("UPDATE %s SET comments = %u WHERE storyid = %u", $this->table, $total, $this->storyid);
		if ( !$result = $this->db->queryF($sql) ) {
			return false;
		}
		return true;
	}

	function topicid()
	{
		return $this->topicid;
	}

	function topic()
	{
		return new XoopsTopic($this->topicstable, $this->topicid);
	}

	function uid()
	{
		return $this->uid;
	}

	function uname()
	{
		return XoopsUser::getUnameFromId($this->uid);
	}

	function title($format="Show")
	{
		$myts =& MyTextSanitizer::getInstance();
		$smiley = 1;
		if ( $this->nosmiley() ) {
			$smiley = 0;
		}
		switch ( $format ) {
		case "Show":
			$title = $myts->makeTboxData4Show($this->title,$smiley);
			break;
		case "Edit":
			$title = $myts->makeTboxData4Edit($this->title);
			break;
		case "Preview":
			$title = $myts->makeTboxData4Preview($this->title,$smiley);
			break;
		case "InForm":
			$title = $myts->makeTboxData4PreviewInForm($this->title);
			break;
		}
		return $title;
	}

	function hometext($format="Show")
	{
		$myts =& MyTextSanitizer::getInstance();
		$html = 1;
		$smiley = 1;
		$xcodes = 1;
		if ( $this->nohtml() ) {
			$html = 0;
		}
		if ( $this->nosmiley() ) {
			$smiley = 0;
		}
		switch ( $format ) {
		case "Show":
			$hometext = $myts->makeTareaData4Show($this->hometext,$html,$smiley,$xcodes);
			break;
		case "Edit":
			$hometext = $myts->makeTareaData4Edit($this->hometext);
			break;
		case "Preview":
			$hometext = $myts->makeTareaData4Preview($this->hometext,$html,$smiley,$xcodes);
			break;
		case "InForm":
			$hometext = $myts->makeTareaData4PreviewInForm($this->hometext);
			break;
		}
		return $hometext;
	}

	function bodytext($format="Show")
	{
		$myts =& MyTextSanitizer::getInstance();
		$html = 1;
		$smiley = 1;
		$xcodes = 1;
		if ( $this->nohtml() ) {
			$html = 0;
		}
		if ( $this->nosmiley() ) {
			$smiley = 0;
		}
		switch ( $format ) {
		case "Show":
			$bodytext = $myts->makeTareaData4Show($this->bodytext,$html,$smiley,$xcodes);
			break;
		case "Edit":
			$bodytext = $myts->makeTareaData4Edit($this->bodytext);
			break;
		case "Preview":
			$bodytext = $myts->makeTareaData4Preview($this->bodytext,$html,$smiley, $xcodes);
			break;
		case "InForm":
			$bodytext = $myts->makeTareaData4PreviewInForm($this->bodytext);
			break;
		}
		return $bodytext;
	}

	function counter()
	{
		return $this->counter;
	}

	function created()
	{
		return $this->created;
	}

	function published()
	{
		return $this->published;
	}

	function expired()
	{
		return $this->expired;
	}

	function hostname()
	{
		return $this->hostname;
	}

	function storyid()
	{
		return $this->storyid;
	}

	function nohtml()
	{
		return $this->nohtml;
	}

	function nosmiley()
	{
		return $this->nosmiley;
	}

	function notifypub()
	{
		return $this->notifypub;
	}

	function type()
	{
		return $this->type;
	}

	function ihome()
	{
		return $this->ihome;
	}

	function topicdisplay()
	{
		return $this->topicdisplay;
	}

	function topicalign($astext=true)
	{
		if ( $astext ) {
			if ( $this->topicalign == "R" ) {
				$ret = "right";
			} else {
				$ret = "left";
			}
			return $ret;
		}
		return $this->topicalign;
	}

	function comments()
	{
		return $this->comments;
	}
}
?>
