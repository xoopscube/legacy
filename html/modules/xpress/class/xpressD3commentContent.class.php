<?php
if( ! defined( 'XPRESS_D3FORUM_CLASS_INCLUDED' ) ) {
	define( 'XPRESS_D3FORUM_CLASS_INCLUDED' , 1 ) ;

	// a class for d3forum comment integration
	class xpressD3commentContent extends D3commentAbstract {

		function fetchSummary( $external_link_id )
		{
		//	include_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
			global $forum_id;
			
			$db =& Database::getInstance() ;
			$myts =& MyTextsanitizer::getInstance() ;

			$module_handler =& xoops_gethandler( 'module' ) ;
			$module =& $module_handler->getByDirname( $this->mydirname ) ;
			$config_handler =& xoops_gethandler('config');
			$configs = $config_handler->getConfigList( $module->mid() ) ;

			$post_id = intval( $external_link_id ) ;
			$mydirname = $this->mydirname ;
			if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
			
			$xpress_prefix = preg_replace('/wordpress/','wp',$mydirname);
			$blog_info = $this->get_comment_blog_info($forum_id);
			// query
			$post_row = $db->fetchArray( $db->query( "SELECT * FROM ".$blog_info['mb_table_prefix']."posts WHERE ID=$post_id" ) ) ;
			if( empty( $post_row ) ) return '' ;

			// dare to convert it irregularly
			$summary = str_replace( '&amp;' , '&' , htmlspecialchars( xoops_substr( strip_tags( $post_row['post_content'] ) , 0 , 255 ) , ENT_QUOTES ) ) ;
			$uri = preg_replace('/\/$/','',$blog_info['home']);
			$uri .= '/?p='.$post_row['ID'];
			return array(
				'dirname' => $mydirname. $blog_info['sub_dir'] ,
				'module_name' => $blog_info['blogname'] ,
				'subject' => $post_row['post_title'] ,
				'uri' => $uri ,
				'summary' => $summary ,
			) ;
		}
		// public
		function displayCommentsInline( $params )
		{
			global $xoops_config;
			if ( function_exists('date_default_timezone_set') )
				date_default_timezone_set($xoops_config->xoops_time_zone);
			$new_params = $this->restructParams( $params ) ;
			if (!$this->canAddComment($params['id']) ) {
				$new_params['no_form'] = true;
				echo '<p class="xpress_comment_close">' . __('Sorry, comments are closed for this item.') . '</p>';
				ob_start();
					d3forum_render_comments( $this->d3forum_dirname , $new_params['forum_id'] , $new_params , $this->smarty ) ;
					$d3comment=ob_get_contents();
				ob_end_clean();
				preg_match('/(.*?)<div><a href=(.*?)index.php\?page=newtopic&amp;forum_id=[^>]*?>(.*?)<\/a><\/div>\s?(.*)/s', $d3comment, $elms);
				if (! empty($elms[0])) $d3comment = $elms[1] . $elms[4];
				echo $d3comment;
			} else {
				d3forum_render_comments( $this->d3forum_dirname , $new_params['forum_id'] , $new_params , $this->smarty ) ;
			}
			if ( function_exists('date_default_timezone_set') )
				date_default_timezone_set('UTC');
		}

		//private for XPressME
		function canAddComment($external_link_id)
		{
			global $forum_id;
			global $post;
			
			if (is_object($post)){  // in wordpress 
				if ($post->ID == $external_link_id){
					if($post->comment_status == 'open'){
						return true;
					} else {
						return false;
					}
				}
			}
					$db =& Database::getInstance() ;
					$myts =& MyTextsanitizer::getInstance() ;

					$module_handler =& xoops_gethandler( 'module' ) ;
					$module =& $module_handler->getByDirname( $this->mydirname ) ;
					$config_handler =& xoops_gethandler('config');
					$configs = $config_handler->getConfigList( $module->mid() ) ;

					$post_id = intval( $external_link_id ) ;
					$mydirname = $this->mydirname ;
					if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
					
					$blog_info = $this->get_comment_blog_info($forum_id);
					$xpress_prefix = preg_replace('/wordpress/','wp',$mydirname);

					// query
					$sql = "SELECT * FROM ".$blog_info['mb_table_prefix']."posts WHERE ID=$post_id";
					$post_row = $db->fetchArray( $db->query( $sql ) ) ;
					if( empty( $post_row ) ) return false ;
					if ($post_row['comment_status'] == 'open')
						return true;
					else
						return false;

		}
				
		// abstract (override it)
		// set d3forum_dirname from parameter or config
		function setD3forumDirname( $d3forum_dirname = '' )
		{
			if( ! empty($this->mod_config['d3forum_dir'] ) ) {
		    		$this->d3forum_dirname = $this->mod_config['d3forum_dir'] ;
			} else if( $d3forum_dirname ) {
				$this->d3forum_dirname = $d3forum_dirname ;
			} else if( ! empty( $this->mod_config['comment_dirname'] ) ) {
				$this->d3forum_dirname = $this->mod_config['comment_dirname'] ;
			} else {
				$this->d3forum_dirname = 'd3forum' ;
			}
		}
				
		// get forum_id from $params or config
		// override it if necessary

		function getForumId( $params )
		{
			if( ! empty( $this->mod_config['d3forum_id'] ) ) {
				return $this->mod_config['d3forum_id'] ;
			} else if( ! empty( $params['forum_id'] ) ) {
				return intval( $params['forum_id'] ) ;
			} else if( ! empty( $this->mod_config['comment_forum_id'] ) ) {
				return $this->mod_config['comment_forum_id'] ;
			} else {
				return 1 ;
			}
		}

		// get view from $params or config
		// override it if necessary
		function getView( $params )
		{
			if( ! empty( $params['view'] ) ) {
				return $params['view'] ;
			} else {
				return 'listposts' ;
			}
		}


		// get view from $params or config
		// override it if necessary
		function getOrder( $params )
		{
			global $xpress_config;
			if( ! empty( $params['order'] ) ) {
				return strtolower( $params['order'] ) ;
			} else {
				return 'desc' ;

			}
		}


		// get number of posts will be displayed from $params or config
		// override it if necessary
		function getPostsNum( $params )
		{
			if( ! empty( $params['posts_num'] ) ) {
				return $params['posts_num'] ;
			} else {
				return 10 ;
			}
		}
		
		function validate_id( $link_id )
		{
			global $forum_id;
			$post_id = intval( $link_id ) ;
			$mydirname = $this->mydirname ;
			$xpress_prefix = preg_replace('/wordpress/','wp',$mydirname);
			$blog_info = $this->get_comment_blog_info($forum_id);		
			$db =& Database::getInstance() ;
			
			list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$blog_info['mb_table_prefix'] ."posts WHERE ID=$post_id AND comment_status ='open'" ) ) ;

			if( $count <= 0 ) return false ;
			else return $post_id ;
		}
		
	// callback on newtopic/edit/reply/delete
	// abstract
		function onUpdate( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 )
		{
			global $message;
			
			if ($mode == 'approve'){
				$mode = 'edit';
			}
			return $this->sync_to_wp_comment( $mode , $link_id , $forum_id , $topic_id , $post_id);

		}
		
		// processing xoops notification for 'comment'
		// override it if necessary
		function processCommentNotifications( $mode , $link_id , $forum_id , $topic_id , $post_id )
		{
			$blog_info = $this->get_comment_blog_info($forum_id);
			$wp_full_prefix = $blog_info['mb_table_prefix'];

			$db =& Database::getInstance() ;
			$myts =& MyTextsanitizer::getInstance() ;

			$module_handler =& xoops_gethandler( 'module' ) ;
			$module =& $module_handler->getByDirname( $this->mydirname ) ;
			$config_handler =& xoops_gethandler('config');
			$configs = $config_handler->getConfigList( $module->mid() ) ;

			$mydirname = $this->mydirname ;
			if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
			
			$xpress_prefix = preg_replace('/wordpress/','wp',$mydirname);
			
			$table_term_relationships = $wp_full_prefix ."term_relationships";
			$table_term_taxonomy = $wp_full_prefix."term_taxonomy";
			$table_terms = $wp_full_prefix."terms";
			$table_categories = $db->prefix($xpress_prefix."_categories");
			$table_post2cat = $db->prefix($xpress_prefix."_post2cat");
			$wp_post = $wp_full_prefix."posts";
			$wp_options = $wp_full_prefix."options";
			$wp_users  = $db->prefix($xpress_prefix."_users");
						
			$sql = "SELECT option_value  FROM $wp_options WHERE option_name ='blogname'";
			$blog_row = $db->fetchArray( $db->query( $sql ) ) ;
			if( empty( $blog_row ) ) return false;
			$blog_name = $blog_row['option_value'];
			
			
			// query
			$sql = "SELECT * FROM ".$wp_post." WHERE ID=$link_id ";
			$post_row = $db->fetchArray( $db->query( $sql ) ) ;
			if( empty( $post_row ) ) return false;
			$post_title = $post_row['post_title'];
			$post_author = $post_row['post_author'];
			
			$sql = "SELECT display_name  FROM $wp_users WHERE ID ='$post_author'";
			$blog_row = $db->fetchArray( $db->query( $sql ) ) ;
			if( empty( $blog_row ) ) return false;
			$user_name = $blog_row['display_name'];

			require_once XOOPS_ROOT_PATH . '/include/notification_functions.php' ;

			// non-module integration returns false quickly
			if( ! is_object( $this->module ) ) return false ;

			$not_module =& $this->module ;
			$not_modid = $this->module->getVar('mid') ;

			$comment_tags = array( 'XPRESS_AUTH_NAME' =>$user_name,'XPRESS_BLOG_NAME' =>$blog_name,'XPRESS_POST_TITLE' => $post_title , 'XPRESS_POST_URL' => XOOPS_URL.'/modules/'.$this->d3forum_dirname.'/index.php?post_id='.intval($post_id) ) ;
			$notification_handler =& xoops_gethandler( 'notification' ) ;
			$notification_handler->triggerEvent( 'global' , 0 , 'comment' , $comment_tags , false , $not_modid ) ;
			$notification_handler->triggerEvent( 'author' , $post_author , 'comment' , $comment_tags , false , $not_modid ) ;
			$notification_handler->triggerEvent( 'post' , $link_id , 'comment' , $comment_tags , false , $not_modid ) ;
			
			$post_row = $db->fetchArray( $db->query( "SELECT * FROM ".$db->prefix($this->d3forum_dirname."_posts")." WHERE post_id=$post_id" ) ) ;
			if( !empty( $post_row ) ){
				if ( $post_row['approval'] ==0 ){
							$notification_handler->triggerEvent( 'global' , 0 , 'waiting', $comment_tags , false , $not_modid ) ;
				}
			}
			
		// categorie notification
			include(XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/wp-includes/version.php');
			if ($wp_db_version < 6124){
					$sql2 = "SELECT c.cat_ID, c.cat_name FROM ".$table_categories." c, ".$table_post2cat." p2c WHERE c.cat_ID = p2c.category_id AND p2c.post_id=".$link_id;
			} else {
					$sql2  = "SELECT $table_term_relationships.object_id, $table_terms.term_id AS cat_ID, $table_terms.name AS cat_name ";
					$sql2 .= "FROM $table_term_relationships INNER JOIN ($table_term_taxonomy INNER JOIN $table_terms ON $table_term_taxonomy.term_id = $table_terms.term_id) ON $table_term_relationships.term_taxonomy_id = $table_term_taxonomy.term_taxonomy_id ";
					$sql2 .= "WHERE ($table_term_relationships.object_id =" . $link_id.") AND ($table_term_taxonomy.taxonomy='category')";		
			}
			$res2 = $db->query($sql2);
			while($row2 = $db->fetchArray($res2)){
				$cat_id = $row2['cat_ID'];
				$cat_name = $row2['cat_name'];
				$comment_tags = array( 'XPRESS_AUTH_NAME' =>$user_name,'XPRESS_BLOG_NAME' =>$blog_name,'XPRESS_CAT_TITLE' => $cat_name,'XPRESS_POST_TITLE' => $post_title , 'XPRESS_POST_URL' => XOOPS_URL.'/modules/'.$this->d3forum_dirname.'/index.php?post_id='.intval($post_id) ) ;
				$notification_handler->triggerEvent( 'category' , $cat_id , 'comment' , $comment_tags , false , $not_modid ) ;
			}
		}
		
		//  The content is reflected in the WordPress comment when there is a change in the D3Forum comment. 

		function sync_to_wp_comment( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 ){

			$blog_info = $this->get_comment_blog_info($forum_id);
			$wp_full_prefix = $blog_info['mb_table_prefix'];
			$blog_id = $blog_info['blog_id'];
			$target_db_prefix = $blog_info['mb_table_prefix'];
			$mydirname = $this->mydirname ;
			$xpress_prefix = preg_replace('/wordpress/','wp',$mydirname);
			$d3f_forum_dir  = $this->d3forum_dirname;
			
			$d3f_prefix = $d3f_forum_dir;
			$myts =& MyTextSanitizer::getInstance();
			$xoopsDB =& Database::getInstance();

			$wp_comments = $wp_full_prefix . 'comments';
			$wp_posts = $wp_full_prefix . 'posts';
			$wp_d3forum_link = $xoopsDB->prefix($xpress_prefix . '_d3forum_link');
			$d3f_posts = $xoopsDB->prefix($d3f_prefix . '_posts');
			$d3f_topics = $xoopsDB->prefix($d3f_prefix . '_topics');
			$d3f_users2topics  = $xoopsDB->prefix($d3f_prefix . '_users2topics ');
			$db_xoops_users = $xoopsDB->prefix('users');
			$d3f_post_votes = $xoopsDB->prefix($d3f_prefix . '_post_votes');
			
			$comment_post_ID = $link_id;

			$d3f_sql  =	"SELECT $d3f_posts.guest_name, ";
			$d3f_sql .=	"$d3f_posts.guest_email, $d3f_posts.guest_url, $d3f_posts.poster_ip, $d3f_posts.post_time, ";
			$d3f_sql .=	"$d3f_posts.post_text, $d3f_posts.approval, $d3f_posts.uid ,$d3f_posts.pid ";
			$d3f_sql .=	"FROM $d3f_posts ";
			$d3f_sql .=	"WHERE $d3f_posts.post_id = $post_id";

			$d3f_res = $xoopsDB->query($d3f_sql, 0, 0);
			if ($d3f_res === false){
				die('...Err. OPEN D3Forum Data (' .  $d3f_sql . ')');
			}else {
				$d3f_row = $xoopsDB->fetchArray($d3f_res);
				$uid = $d3f_row['uid'];
				if (!empty($uid)) {
					$xu_sql  = "SELECT uid ,name ,uname ,email , url FROM $db_xoops_users WHERE uid = $uid";
					$xu_res =  $xoopsDB->query($xu_sql, 0, 0);
					if ($xu_res === false){
						$user_display_name = '';
					}else {
						$xu_row = $xoopsDB->fetchArray($xu_res);
						if (empty($xu_row['name'])){
							$user_display_name = $xu_row['uname'];
						} else {
							$user_display_name = $xu_row['name'] ;
						}
						$comment_author_email = "'" . $xu_row['email'] . "'";
						$comment_author_url = "'" . $xu_row['url'] . "'";
					}
					$comment_author = "'" . addSlashes($user_display_name) . "'";
				} else {						
					$comment_author = "'" . addSlashes($d3f_row['guest_name']) . "'";
					$comment_author_email = "'" . $d3f_row['guest_email'] . "'";
					$comment_author_url = "'" . $d3f_row['guest_url'] . "'";
				}
				$comment_author_IP = "'" . $d3f_row['poster_ip'] . "'";
				$comment_date = "'" . date('Y-m-d H:i:s' , $d3f_row['post_time']) . "'";
				$comment_content = "'" . addSlashes($d3f_row['post_text']) . "'";
				$comment_approved = "'" . $d3f_row['approval'] . "'";
				require_once (XOOPS_ROOT_PATH . '/modules/'.$mydirname . '/include/general_functions.php');
				$user_ID = xoops_uid_to_wp_uid($d3f_row['uid'],$mydirname);
				$comment_date_gmt = "'" . gmdate('Y-m-d H:i:s' , $d3f_row['post_time']) . "'";
				$comment_type = '';
				$d3f_pid = $d3f_row['pid'];
				if ($d3f_pid > 0) {
					$comment_parent = $this->get_wp_comment_ID($d3f_pid);
				} else {
					$comment_parent = 0 ;
				}
				
				switch($mode){				
					case 'reply':
					case 'newtopic' :				
						$wp_sql  = "INSERT INTO $wp_comments ";
						$wp_sql .=    "(comment_post_ID , comment_author , comment_author_email , comment_author_url , comment_author_IP , ";
						$wp_sql .=    "comment_date , comment_content , comment_approved , user_id , comment_date_gmt, comment_parent) ";
						$wp_sql .=  "VALUES ";
						$wp_sql .=    "($comment_post_ID, $comment_author, $comment_author_email, $comment_author_url, $comment_author_IP, ";
						$wp_sql .=    "$comment_date, $comment_content, $comment_approved, $user_ID, $comment_date_gmt, $comment_parent)";

						$wp_res = $xoopsDB->queryF($wp_sql, 0, 0);
						if ($wp_res === false){
							die( '...Err. INSERT' . $wp_comments . '(' . $wp_sql . ')');
						} else{
							$comment_ID = $xoopsDB->getInsertId();
							
							$wp_sql  = "UPDATE $wp_posts SET  comment_count = comment_count +1 WHERE ID = $comment_post_ID";
							$wp_res = $xoopsDB->queryF($wp_sql, 0, 0);
						
							$wp_sql  = "INSERT INTO $wp_d3forum_link ";
							$wp_sql .=    "(comment_ID , post_id, forum_id,blog_id) ";
							$wp_sql .=  "VALUES ";
							$wp_sql .=    "($comment_ID, $post_id ,$forum_id,$blog_id)";		
							$wp_res = $xoopsDB->queryF($wp_sql, 0, 0);
						}
						

						if ($comment_approved ==0){
							require_once XOOPS_ROOT_PATH . '/include/notification_functions.php' ;
							$notification_handler =& xoops_gethandler( 'notification' ) ;
							$notification_handler->triggerEvent( 'global' , 0 , 'waiting') ;
						}			

						break;
					case 'edit':
						$wp_sql = "SELECT comment_ID FROM $wp_d3forum_link WHERE post_id = $post_id ";
						$wp_res = $xoopsDB->query($wp_sql, 0, 0);
						if ($wp_res === false){
							die('...Err. EDIT' . $wp_comments . '(' . $wp_sql . ')');
						} else {
							$wp_row = $xoopsDB->fetchArray($wp_res);
							$comment_ID = $wp_row['comment_ID'];
						
						
							$wp_sql  = "UPDATE $wp_comments SET comment_content = $comment_content , comment_date_gmt = $comment_date_gmt WHERE comment_ID = $comment_ID";
							$wp_res = $xoopsDB->queryF($wp_sql, 0, 0);
							if ($wp_res === false){
								die( '...Err. UPDATE' . $wp_comments . '(' . $wp_sql . ')');
							}
						}
						break;
					case 'delete':
						// wordpress comments delete
						$comment_ID = $this->get_wp_comment_ID($post_id);
						if ($comment_ID > 0){
							$sql= "SELECT comment_type FROM $wp_comments WHERE comment_ID = $comment_ID";
							$res= $xoopsDB->query( $sql);
							if ($xoopsDB->getRowsNum($res) > 0 ){
								$row = $xoopsDB->fetchArray($res);
								$comment_type = $row['comment_type'];
								if (!empty($comment_type)) break;
							}
							$wp_sql = "DELETE FROM $wp_comments WHERE comment_ID = $comment_ID";
							$wp_res = $xoopsDB->queryF($wp_sql, 0, 0);
							
							$wp_sql = "DELETE FROM $wp_d3forum_link WHERE post_id = $post_id";
							$wp_res = $xoopsDB->queryF($wp_sql, 0, 0);
							
							$wp_sql  = "UPDATE $wp_posts SET  comment_count = comment_count -1 WHERE ID = $comment_post_ID";
							$wp_res = $xoopsDB->queryF($wp_sql, 0, 0);
						}
						break;
					default :
				}				
			}		
			return true ;
		}
		function get_wp_comment_ID($d3forum_post_ID){
			$xp_prefix = $wpdirname = basename( dirname( dirname( __FILE__ ) ) ) ;
			$xp_prefix = preg_replace('/wordpress/','wp',$xp_prefix);
			
			$xoopsDB =& Database::getInstance();
			$wp_d3forum_link = $xoopsDB->prefix($xp_prefix . '_d3forum_link');
			
			$sql  =	"SELECT * FROM $wp_d3forum_link WHERE post_id = $d3forum_post_ID";
			$res = $xoopsDB->query($sql, 0, 0);
			$ret = 0;
			if ($xoopsDB->getRowsNum($res) > 0 ){
				$row = $xoopsDB->fetchArray($res);
				$ret = $row['comment_ID'];
			}
			return $ret;
		}
		
		function get_comment_blog_info($d3forum_forum_id){
			$xoopsDB =& Database::getInstance();
			$xp_prefix = $wpdirname = basename( dirname( dirname( __FILE__ ) ) ) ;
			$xp_prefix = preg_replace('/wordpress/','wp',$xp_prefix);
			$xp_prefix = $xoopsDB->prefix($xp_prefix);
			$table_name = 'options';
			$options_array = array();


				$sql = "SHOW TABLES LIKE '" . $xp_prefix  . '%' . $table_name . "'";
				if($result = $xoopsDB->queryF($sql)){
					while($row = $xoopsDB->fetchRow($result)){
						$wp_option_table = $row[0];
						$pattern = '/'. $table_name . '/';
						$option['mb_table_prefix'] =  preg_replace($pattern,'',$wp_option_table);
						if (preg_match('/'. $xp_prefix . '_([0-9]*)_/',$option['mb_table_prefix'],$matchs)){
							$option['blog_id'] = $matchs[1];
						} else {
							$option['blog_id'] = 1;
						}
						
						$option_sql = "SELECT option_name, option_value FROM $wp_option_table WHERE option_name IN ('home','siteurl','blogname','xpressme_option')";
						if($option_result =  $xoopsDB->query($option_sql, 0, 0)){
							while($option_row = $xoopsDB->fetchArray($option_result)){
								$name = $option_row['option_name'];
								$value = $option_row['option_value'];
								if ($name == 'xpressme_option'){
									$value =  @unserialize( $value );
								}
								$option[$name] = $value;
							}
							$option['siteurl'] = preg_replace('/\/$/','',$option['siteurl']);
							$option['home'] = preg_replace('/\/$/','',$option['home']);
							
							if ($option['blog_id'] === 1){
								$option['sub_dir'] = '';
							} else {
								$xoops_url = str_replace('/','\\/',XOOPS_URL);
								if (preg_match('/'. $xoops_url . '.*' . $wpdirname . '(.*)/',$option['home'],$matchs)){
									$option['sub_dir'] = $matchs[1];
								} else {
									$option['sub_dir'] = '';
								}
							}
							//if ($option['xpressme_option']['d3forum_forum_id'] == $d3forum_forum_id){
								return $option;
							//}
						}
					}
					return null;
				}
		}
		function repair_d3forum_link($comment_ID, $post_id ,$forum_id,$blog_id,$target_db_prefix){
		}
		
	} // class
}
?>