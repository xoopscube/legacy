<?php
class CustomBB extends XCube_ActionFilter
{
    function preBlockFilter() {
        $this->mController->mRoot->mDelegateManager->add("MyTextSanitizer.XoopsCodePre",array(&$this,"BBCodePre"));
    }
    /*
     * Add Short code into Delegate BBCode 
     */
    function BBCodePre(&$patterns, &$replacements, $allowimage) {

    	// Replacement rules for [xoops_imageurl] tag
        $patterns[] = '/\[xoops_imageurl\]/es';
        $replacements[] = "CustomBB::xoops_imageurl();";  	
    	// Replacement rules for [mod_jump]
        $patterns[] = '/\[mod_jump (.*?)\]/es';
        $replacements[] = "CustomBB::mod_jump('\\1');";
    	// Replacement rules for [iine_bulletintopic_count] tag
        $patterns[] = '/\[iine_bulletintopic_count (.*?)\]/es';
        $replacements[] = "CustomBB::iine_bulletintopic_count('\\1');";
    	// Replacement rules for [xoops_theme] tag
    	$patterns[] = '/\[xoops_theme\]/es';
        $replacements[] = "CustomBB::xoops_theme();";
    	// Replacement rules for [pm_count] tag
        $patterns[] = '/\[pm_count (.*?)\]/es';
        $replacements[] = "CustomBB::pm_count('\\1');";
        $patterns[] = '/\[pm_count\]/es';
        $replacements[] = "CustomBB::pm_count();";
        // Replacement rules for [d3comment_count] tag
        $patterns[] = '/\[d3comment_count (.*?)\]/es';
        $replacements[] = "CustomBB::d3comment_count('\\1');";
        // Replacement rules for [d3comment_unread] tag
        $patterns[] = '/\[d3comment_unread (.*?)\]/es';
        $replacements[] = "CustomBB::d3comment_unread('\\1');";
    }
    /*
     * Make Shrot code return strings
     */
    function xoops_theme($args='') {
		return XOOPS_THEME_URL;
    }
    function xoops_imageurl($args='') {
        global $xoopsConfig;
        return XOOPS_THEME_URL."/".$xoopsConfig['theme_set']."/";
//      return $GLOBALS['xoopsTpl']->get_template_vars('xoops_imageurl');
    }
    function mod_jump($args='') {
		// Short code parser start
		$args = html_entity_decode(stripslashes($args));
		$args = preg_replace('/(")/',"",$args);
		$keyval = explode(" ",$args);
		preg_match('/^url=(.*)/',$keyval[0],$matches);
		// Short code parser end
		$ret = "";
		if ($matches){
			if( preg_match("/formmakex/",$matches[1])){
				$querylist = explode("&",$_SERVER['QUERY_STRING']);
				
				foreach($querylist as $inboxparam ){
					if(preg_match("/inbox/",$inboxparam)){
						$inboxparam = "&". str_replace("inbox","inbox_id",$inboxparam);
					}
				}
			}
			$ret = '<script type="text/javascript">window.location.href="';
			$ret .= XOOPS_URL . "/modules/" . $matches[1] . $inboxparam;
			$ret .= '";</script>';
			return $ret;
		}
    }
    function pm_count($args='') {
		global $xoopsDB;

		// Short code parser start
		$args = preg_replace('/(&quot;|")/',"",stripslashes($args));
		$keyval = explode(" ",$args);
		foreach($keyval as $k){
			preg_match("/(.*)=(.*)/",$k,$matches);
			$params[$matches[1]] = $matches[2];
		}		
		// Short code parser end
		
		$sql = "SELECT count(*) FROM " . $xoopsDB->prefix("message_inbox");
		switch ($params['type']){
			case 'unread':
				$sql .= " WHERE is_read=0";
				if ($params['from_uid']) $sql .= " AND from_uid=".$params['from_uid'];
		}
		$res = $xoopsDB->query($sql);
		list($cnt) = $xoopsDB->fetchRow($res);
        return $cnt;
    }
    function d3comment_count($args='') {
		global $xoopsDB;

		// Short code parser start
		$args = preg_replace('/(&quot;|")/',"",stripslashes($args));
		$keyval = explode(" ",$args);
		foreach($keyval as $k){
			preg_match("/(.*)=(.*)/",$k,$matches);
			$params[$matches[1]] = $matches[2];
		}		
		// Short code parser end
		$mydirname = "d3forum";
		$sql = "SELECT topic_posts_count FROM " . $xoopsDB->prefix($mydirname."_topics");
		if ($params['link_id']) $sql .= " WHERE topic_external_link_id=".$params['link_id'];
		$res = $xoopsDB->query($sql);
		list($cnt) = $xoopsDB->fetchRow($res);
        return $cnt;
    }
    function d3comment_unread($args='') {
		global $xoopsDB,$xoopsUser;

		// Short code parser start
		$args = preg_replace('/(&quot;|")/',"",stripslashes($args));
		$keyval = explode(" ",$args);
		foreach($keyval as $k){
			preg_match("/(.*)=(.*)/",$k,$matches);
			$params[$matches[1]] = $matches[2];
		}		
		// Short code parser end
		$mydirname = "d3forum";
		$sql = "SELECT t.topic_last_post_time, u2t.u2t_time FROM ".$xoopsDB->prefix($mydirname."_topics").
			" t LEFT JOIN ".$xoopsDB->prefix($mydirname."_users2topics")." u2t ON t.topic_id=u2t.topic_id AND u2t.uid="
			. $xoopsUser->uid();
		if ($params['link_id']) $sql .= " WHERE topic_external_link_id=".$params['link_id'];
		$res = $xoopsDB->query($sql);
		list($pt,$ut) = $xoopsDB->fetchRow($res);
		return $pt > $ut ? 1 : 0;
    }
    function iine_bulletintopic_count($args='') {
        global $xoopsDB;

        // Short code parser start
        $args = preg_replace('/(&quot;|")/',"",stripslashes($args));
        $keyval = explode(" ",$args);
        foreach($keyval as $k){
        	preg_match("/(.*)=(.*)/",$k,$matches);
            $params[$matches[1]] = $matches[2];
        }
        // Short code parser end
        $mydirname = "bulletin";
        $sql = "SELECT count(id) FROM " . $xoopsDB->prefix("iine_votes") ." where dirname = '" . $mydirname. "' and content_id in (select storyid from ".$xoopsDB->prefix("bulletin_stories")." where topicid = ".$params['topic_id'] .")";
        //print($sql);
        $res = $xoopsDB->query($sql);
        list($cnt) = $xoopsDB->fetchRow($res);
        return $cnt;
    }
}
?>