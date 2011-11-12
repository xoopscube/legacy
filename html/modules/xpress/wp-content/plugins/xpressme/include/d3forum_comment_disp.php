<?php
		global $xoops_db,$xpress_config;
		$xoops_trust_path = get_xoops_trust_path();
		$xoops_root_path = get_xoops_root_path();
		$inc_path = $xoops_trust_path . '/modules/d3forum/include/comment_functions.php';
		include_once( $inc_path);
		$xpress_dir = get_xpress_dir_path();
		$xpress_dirname = get_xpress_dir_name();
		
		include_once($xpress_dir . '/class/xpressD3commentContent.class.php');
		
		$dir_name = $xpress_config->d3forum_module_dir;
		$forum_id = $xpress_config->d3forum_forum_id;
		$external_link_format = $xpress_config->d3forum_external_link_format;
		if ($xpress_config->is_d3forum_desc){
			$order = 'desc';
		} else {
			$order = 'asc';
		}
		if ($xpress_config->is_d3forum_desc){
			$order = 'desc';
		} else {
			$order = 'asc';
		}

		if ($xpress_config->is_d3forum_flat){
			$view = 'listposts_flat';
		} else {
			$view = 'listtopics';
		}
		$posts_num = $xpress_config->d3forum_views_num;
		
	// force UPDATE forums.forum_external_link_format "(dirname)::(classname)::(trustdirname)"
		$xoops_db->query( "UPDATE ".get_xoops_prefix() . $dir_name."_forums SET forum_external_link_format='".$external_link_format."' WHERE forum_id= $forum_id" ) ;
		
		$d3comment =& new xpressD3commentContent( $dir_name , $xpress_dirname ) ;

		$post_title = get_the_title();
        if (function_exists('get_the_ID')){        // upper wordpress 2.1
            $post_id=get_the_ID();
        } else {        // lower wordpress 2.1
            ob_start();
                the_ID();
                $post_id=ob_get_contents();
            ob_end_clean();
        }
        if (empty($_GET['p'])){
            $_GET['p']= $post_id;
        } 	
//		$params = array("dirname" => $dir_name, "forum_id" => $forum_id, "itemname" => "p", "id" => $post_id , "subject" => $post_title);
		$params = array("forum_id" => $forum_id,  "id" => $post_id , "subject" => $post_title , "order" => $order , "view" => $view , "posts_num" => $posts_num);
		if(file_exists($xoops_trust_path .'/modules/d3forum/')) {
			if(file_exists($xoops_root_path .'/modules/' . $dir_name . '/')) {
				$d3comment->displayCommentsInline($params ) ;
			} else {
				echo ('<h3>' . $dir_name . 'is not found </h3>') ;
			}
		} else {
		echo '<h3> d3forum is not install </h3>';
		}

?>