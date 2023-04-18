<?php
// $Id: newbb.php,v 1.1 2005/04/07 09:23:42 gij Exp $
// FILE		::	newbb.php
// AUTHOR	::	Ryuji AMANO <info@ryus.co.jp>
// WEB		::	Ryu's Planning <http://ryus.co.jp/>

// NewBBversion/newbb2 plugin: D.J., http://xoops.org.cn

function b_sitemap_newbb(){
    global $sitemap_configs;
    $sitemap = [];

    // Get the NewBB version Info
	$module_handler = &xoops_gethandler('module');
	$newbb = $module_handler->getByDirname('newbb');
	$newbb_version = (int)$newbb->getInfo('version');

	// For NewBB 2. If the syntax is wrong, correct me plz.
	if($newbb_version>=2){
		// Get All Forums with access permission
		$forum_handler =& xoops_getmodulehandler('forum', 'newbb');
		$_forums = $forum_handler->getForums(0, 'access');

		// Some transformation; Might ugly but works :=)
		$forums = [];
		$forums_sub = [];
		foreach ($_forums as $forumid => $forum) {
			if($forum->isSubForum()) {
                $forums_sub[$forum->getVar('parent_forum')][$forumid] = [
                    'id' => $forumid,
                    'url' => "viewforum.php?forum=" . $forumid,
                    'title' => $forum->getVar('forum_name')
                ];
            }
			else {
                $forums[$forumid] = [
                    'id' => $forumid,
                    'cid' => $forum->getVar('cat_id'),
                    'url' => "viewforum.php?forum=" . $forumid,
                    'title' => $forum->getVar('forum_name')
                ];
            }
		}
		unset($_forums);
		foreach ($forums as $id => $forum) {
			if(!empty($forums_sub[$id])) {
                $forums[$id]['fchild'] =& $forums_sub[$id];
            }
		}

		// Why not enable subcategory?
		if($sitemap_configs["show_subcategoris"]){
			$category_handler =& xoops_getmodulehandler('category', 'newbb');
			$categories = $category_handler->getAllCats('access');
		    if((is_countable($categories) ? count($categories) : 0)>0) {
                foreach ($categories as $key => $category) {
                    $cat_id = $category->getVar("cat_id");
                    $i = $cat_id;
                    $sitemap['parent'][$i]['id'] = $cat_id;
                    $sitemap['parent'][$i]['title'] = $category->getVar("cat_title");
                    $sitemap['parent'][$i]['url'] = "index.php?cat=" . $cat_id;
                }
            }
		    if(count($forums)>0) {
                foreach ($forums as $id => $forum) {
                    $cid = $forum['cid'];
                    $sitemap['parent'][$cid]['child'][$id] = $forum;
                    $sitemap['parent'][$cid]['child'][$id]['image'] = 2;
                    if (!empty($forum['fchild'])) {
                        foreach ($forum['fchild'] as $_id => $_forum) {
                            $sitemap['parent'][$cid]['child'][$_id] = $_forum;
                            unset($_forum);
                            $sitemap['parent'][$cid]['child'][$_id]['image'] = 3;
                        }
                    }
                    unset($forum);
                }
            }
		// In case not enable subcategory, do the following; Sorry, I have not tested with this mode yet. If any bugs, feel free to fix.
		}else{
		    if(count($forums)>0) {
                foreach ($forums as $id => $forum) {
                    $sitemap['parent'][$id] = $forum;
                    if (!empty($forum['fchild'])) {
                        foreach ($forum['fchild'] as $_id => $_forum) {
                            $sitemap['parent'][$id]['child'][$_id] = $_forum;
                            $sitemap['parent'][$cid]['child'][$id]['image'] = 2;
                            unset($_forum);
                        }
                    }
                    unset($forum);
                }
            }
		}
		return $sitemap;
	}
	/*
	 * My part ends, D.J. :=)
	 */

    $db =& Database::getInstance();
    $myts =& MyTextSanitizer::getInstance();
    if($sitemap_configs["show_subcategoris"]){ // ���֥���ɽ���ΤȤ��Τ߼¹� by Ryuji
        // ���ƥ��������
        $sql = 'SELECT DISTINCT c.* FROM '.$db->prefix('bb_categories').' c, '.$db->prefix("bb_forums").' f WHERE f.cat_id=c.cat_id GROUP BY c.cat_id, c.cat_title, c.cat_order ORDER BY c.cat_order';
        $result = $db->query($sql);
        $categories = [];
        while ( $cat_row = $db->fetchArray($result) ) {
            $i = $cat_row["cat_id"];
            $sitemap['parent'][$i]['id'] = $cat_row["cat_id"];
            $sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show($cat_row["cat_title"]);
            $sitemap['parent'][$i]['url'] = "index.php?cat=".$cat_row["cat_id"];
            $categories[] = $cat_row["cat_id"];
        }
    }

    // �ե������������
    $sql = "SELECT f.* FROM ".$db->prefix("bb_forums")." f LEFT JOIN ".$db->prefix("bb_categories")." c ON f.cat_id=c.cat_id ORDER BY f.forum_id";
    $result = $db->query($sql);
    //$forums = array();
    $i=0;
    while($forum_row = $db->fetchArray($result)){
        //if(in_array($forum_row["cat_id"], $categories)){
            if($sitemap_configs["show_subcategoris"]){ // ���֥���ɽ���ΤȤ��Τ߼¹� by Ryuji
                $j = $forum_row["cat_id"];
    			$sitemap['parent'][$j]['child'][$i]['id'] = $forum_row["forum_id"];
    			$sitemap['parent'][$j]['child'][$i]['title'] = $myts->makeTboxData4Show($forum_row["forum_name"]);
    			$sitemap['parent'][$j]['child'][$i]['image'] = 2;
    			$sitemap['parent'][$j]['child'][$i]['url'] = "viewforum.php?forum=".$forum_row['forum_id'];
            }else{
                // ���֥�����ɽ���ʤ�ե�������롼�Ȥˤ���
                $sitemap['parent'][$i]['id'] = $forum_row["forum_id"];
                $sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show($forum_row["forum_name"]);
                $sitemap['parent'][$i]['url'] = "viewforum.php?forum=".$forum_row['forum_id'];
            }
        $i++;
        //}
    }
    //print_r($categories);
    return $sitemap;
}
