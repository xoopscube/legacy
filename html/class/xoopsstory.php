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

class xoopsstory
{
    public $table;
    public $storyid;
    public $topicid;
    public $uid;
    public $title;
    public $hometext;
    public $bodytext="";
    public $counter;
    public $created;
    public $published;
    public $expired;
    public $hostname;
    public $nohtml=0;
    public $nosmiley=0;
    public $ihome=0;
    public $notifypub=0;
    public $type;
    public $approved;
    public $topicdisplay;
    public $topicalign;
    public $db;
    public $topicstable;
    public $comments;

    public function Story($storyid=-1)
    {
        $this->db =& Database::getInstance();
        $this->table = "";
        $this->topicstable = "";
        if (is_array($storyid)) {
            $this->makeStory($storyid);
        } elseif ($storyid != -1) {
            $this->getStory(intval($storyid));
        }
    }

    public function setStoryId($value)
    {
        $this->storyid = intval($value);
    }

    public function setTopicId($value)
    {
        $this->topicid = intval($value);
    }

    public function setUid($value)
    {
        $this->uid = intval($value);
    }

    public function setTitle($value)
    {
        $this->title = $value;
    }

    public function setHometext($value)
    {
        $this->hometext = $value;
    }

    public function setBodytext($value)
    {
        $this->bodytext = $value;
    }

    public function setPublished($value)
    {
        $this->published = intval($value);
    }

    public function setExpired($value)
    {
        $this->expired = intval($value);
    }

    public function setHostname($value)
    {
        $this->hostname = $value;
    }

    public function setNohtml($value=0)
    {
        $this->nohtml = $value;
    }

    public function setNosmiley($value=0)
    {
        $this->nosmiley = $value;
    }

    public function setIhome($value)
    {
        $this->ihome = $value;
    }

    public function setNotifyPub($value)
    {
        $this->notifypub = $value;
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function setApproved($value)
    {
        $this->approved = intval($value);
    }

    public function setTopicdisplay($value)
    {
        $this->topicdisplay = $value;
    }

    public function setTopicalign($value)
    {
        $this->topicalign = $value;
    }

    public function setComments($value)
    {
        $this->comments = intval($value);
    }

    public function store($approved=false)
    {
        //$newpost = 0;
        $myts =& MyTextSanitizer::sGetInstance();
        $title =$myts->censorString($this->title);
        $hometext =$myts->censorString($this->hometext);
        $bodytext =$myts->censorString($this->bodytext);
        $title = $myts->makeTboxData4Save($title);
        $hometext = $myts->makeTareaData4Save($hometext);
        $bodytext = $myts->makeTareaData4Save($bodytext);
        if (!isset($this->nohtml) || $this->nohtml != 1) {
            $this->nohtml = 0;
        }
        if (!isset($this->nosmiley) || $this->nosmiley != 1) {
            $this->nosmiley = 0;
        }
        if (!isset($this->notifypub) || $this->notifypub != 1) {
            $this->notifypub = 0;
        }
        if (!isset($this->topicdisplay) || $this->topicdisplay != 0) {
            $this->topicdisplay = 1;
        }
        $expired = !empty($this->expired) ? $this->expired : 0;
        if (!isset($this->storyid)) {
            //$newpost = 1;
            $newstoryid = $this->db->genId($this->table."_storyid_seq");
            $created = time();
            $published = ($this->approved) ? $this->published : 0;

            $sql = sprintf("INSERT INTO %s (storyid, uid, title, created, published, expired, hostname, nohtml, nosmiley, hometext, bodytext, counter, topicid, ihome, notifypub, story_type, topicdisplay, topicalign, comments) VALUES (%u, %u, '%s', %u, %u, %u, '%s', %u, %u, '%s', '%s', %u, %u, %u, %u, '%s', %u, '%s', %u)", $this->table, $newstoryid, $this->uid, $title, $created, $published, $expired, $this->hostname, $this->nohtml, $this->nosmiley, $hometext, $bodytext, 0, $this->topicid, $this->ihome, $this->notifypub, $this->type, $this->topicdisplay, $this->topicalign, $this->comments);
        } else {
            if ($this->approved) {
                $sql = sprintf("UPDATE %s SET title = '%s', published = %u, expired = %u, nohtml = %u, nosmiley = %u, hometext = '%s', bodytext = '%s', topicid = %u, ihome = %u, topicdisplay = %u, topicalign = '%s', comments = %u WHERE storyid = %u", $this->table, $title, $this->published, $expired, $this->nohtml, $this->nosmiley, $hometext, $bodytext, $this->topicid, $this->ihome, $this->topicdisplay, $this->topicalign, $this->comments, $this->storyid);
            } else {
                $sql = sprintf("UPDATE %s SET title = '%s', expired = %u, nohtml = %u, nosmiley = %u, hometext = '%s', bodytext = '%s', topicid = %u, ihome = %u, topicdisplay = %u, topicalign = '%s', comments = %u WHERE storyid = %u", $this->table, $title, $expired, $this->nohtml, $this->nosmiley, $hometext, $bodytext, $this->topicid, $this->ihome, $this->topicdisplay, $this->topicalign, $this->comments, $this->storyid);
            }
            $newstoryid = $this->storyid;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($newstoryid)) {
            $newstoryid = $this->db->getInsertId();
            $this->storyid = $newstoryid;
        }
        return $newstoryid;
    }

    public function getStory($storyid)
    {
        $sql = "SELECT * FROM ".$this->table." WHERE storyid=".$storyid."";
        $array = $this->db->fetchArray($this->db->query($sql));
        $this->makeStory($array);
    }

    public function makeStory($array)
    {
        foreach ($array as $key=>$value) {
            $this->$key = $value;
        }
    }

    public function delete()
    {
        $sql = sprintf("DELETE FROM %s WHERE storyid = %u", $this->table, $this->storyid);
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    public function updateCounter()
    {
        $sql = sprintf("UPDATE %s SET counter = counter+1 WHERE storyid = %u", $this->table, $this->storyid);
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    public function updateComments($total)
    {
        $sql = sprintf("UPDATE %s SET comments = %u WHERE storyid = %u", $this->table, $total, $this->storyid);
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    public function topicid()
    {
        return $this->topicid;
    }

    public function topic()
    {
        return new XoopsTopic($this->topicstable, $this->topicid);
    }

    public function uid()
    {
        return $this->uid;
    }

    public function uname()
    {
        return XoopsUser::getUnameFromId($this->uid);
    }

    public function title($format="Show")
    {
        $myts =& MyTextSanitizer::sGetInstance();
        $smiley = 1;
        if ($this->nosmiley()) {
            $smiley = 0;
        }
        switch ($format) {
        case "Show":
            $title = $myts->makeTboxData4Show($this->title, $smiley);
            break;
        case "Edit":
            $title = $myts->makeTboxData4Edit($this->title);
            break;
        case "Preview":
            $title = $myts->makeTboxData4Preview($this->title, $smiley);
            break;
        case "InForm":
            $title = $myts->makeTboxData4PreviewInForm($this->title);
            break;
        }
        return $title;
    }

    public function hometext($format="Show")
    {
        $myts =& MyTextSanitizer::sGetInstance();
        $html = 1;
        $smiley = 1;
        $xcodes = 1;
        if ($this->nohtml()) {
            $html = 0;
        }
        if ($this->nosmiley()) {
            $smiley = 0;
        }
        switch ($format) {
        case "Show":
            $hometext = $myts->makeTareaData4Show($this->hometext, $html, $smiley, $xcodes);
            break;
        case "Edit":
            $hometext = $myts->makeTareaData4Edit($this->hometext);
            break;
        case "Preview":
            $hometext = $myts->makeTareaData4Preview($this->hometext, $html, $smiley, $xcodes);
            break;
        case "InForm":
            $hometext = $myts->makeTareaData4PreviewInForm($this->hometext);
            break;
        }
        return $hometext;
    }

    public function bodytext($format="Show")
    {
        $myts =& MyTextSanitizer::sGetInstance();
        $html = 1;
        $smiley = 1;
        $xcodes = 1;
        if ($this->nohtml()) {
            $html = 0;
        }
        if ($this->nosmiley()) {
            $smiley = 0;
        }
        switch ($format) {
        case "Show":
            $bodytext = $myts->makeTareaData4Show($this->bodytext, $html, $smiley, $xcodes);
            break;
        case "Edit":
            $bodytext = $myts->makeTareaData4Edit($this->bodytext);
            break;
        case "Preview":
            $bodytext = $myts->makeTareaData4Preview($this->bodytext, $html, $smiley, $xcodes);
            break;
        case "InForm":
            $bodytext = $myts->makeTareaData4PreviewInForm($this->bodytext);
            break;
        }
        return $bodytext;
    }

    public function counter()
    {
        return $this->counter;
    }

    public function created()
    {
        return $this->created;
    }

    public function published()
    {
        return $this->published;
    }

    public function expired()
    {
        return $this->expired;
    }

    public function hostname()
    {
        return $this->hostname;
    }

    public function storyid()
    {
        return $this->storyid;
    }

    public function nohtml()
    {
        return $this->nohtml;
    }

    public function nosmiley()
    {
        return $this->nosmiley;
    }

    public function notifypub()
    {
        return $this->notifypub;
    }

    public function type()
    {
        return $this->type;
    }

    public function ihome()
    {
        return $this->ihome;
    }

    public function topicdisplay()
    {
        return $this->topicdisplay;
    }

    public function topicalign($astext=true)
    {
        if ($astext) {
            if ($this->topicalign == "R") {
                $ret = "right";
            } else {
                $ret = "left";
            }
            return $ret;
        }
        return $this->topicalign;
    }

    public function comments()
    {
        return $this->comments;
    }
}
