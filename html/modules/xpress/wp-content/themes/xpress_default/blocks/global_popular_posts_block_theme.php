<?php
// Block Version: 1.0
function global_popular_posts_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_grobal_popular_posts_block.html' : trim( $options[1] );
	$disp_count = empty( $options[2] ) ? '10' : $options[2] ;
	$show_month_range = empty( $options[3] ) ? '0' : $options[3] ;
	$date_format = empty( $options[4] ) ? '' : $options[4] ;
	$time_format = empty( $options[5] ) ? '' : $options[5] ;
	
	$selected = explode(',' , $cat_select);

	$mydirpath = get_xpress_dir_path();
	
	if (empty($date_format)) $date_format = get_settings('date_format');
	if (empty($time_format)) $time_format = get_settings('time_format');
	
	$cat_select;
	$block = array();
	$item_no = 0;	
	
	$selected_author_id = xpress_selected_author_id('echo=0');	
	
	$data_array = array();

	global $wpdb,$wp_query,$xoops_db,$wp_rewrite;
	
	if (xpress_is_multiblog()){
		$blogs = get_blog_list(0,'all');
		foreach ($blogs AS $blog) {
			switch_to_blog($blog['blog_id']);
			$wp_rewrite->init();
			
			$blog_title = get_bloginfo('name');
			$blog_link = get_bloginfo('url');
			$blog_title_link = '<a href="' . $blog_link . '">' . $blog_title . '</a>' ;

			$db_prefix = get_wp_prefix();
			
			$post_tb = $wpdb->posts;
			$view_tb = $db_prefix . 'views';
			$user_tb = $db_prefix . 'users';
			
			$term_relationships_tb = $wpdb->term_relationships;	// upper 2.3
			$term_taxonomy = $wpdb->term_taxonomy;				// upper 2.3
			$terms_tb = $wpdb->terms;							// upper 2.3

			$post2cat_tb = $wpdb->post2cat; 					//under 2.3
			$categories_tb = $wpdb->categories; 				//under 2.3
			
			include ($mydirpath . '/wp-includes/version.php');
				
			$select = "SELECT $view_tb.post_views, $post_tb.ID, $post_tb.post_title, $post_tb.post_date";				
			if ($wp_db_version >= 6124){
		 		$from  = " FROM (((";
		 		$from .= " $post_tb LEFT JOIN $view_tb ON $post_tb.ID = $view_tb.post_id)";
		 		$from .= " INNER JOIN $term_relationships_tb ON $post_tb.ID = $term_relationships_tb.object_id)";
		 		$from .= " INNER JOIN $term_taxonomy ON $term_relationships_tb.term_taxonomy_id = $term_taxonomy.term_taxonomy_id)";
		 		$from .= " INNER JOIN $terms_tb ON $term_taxonomy.term_id = $terms_tb.term_id ";
		 		
			 	$where = " WHERE $post_tb.post_type = 'post' AND $post_tb.post_status = 'publish'";
			} else {
				$from  = " FROM ((";
				$from .= " $post_tb LEFT JOIN $view_tb ON $post_tb.ID = $view_tb.post_id)";
				$from .= " LEFT JOIN $post2cat_tb ON $post_tb.ID = $post2cat_tb.post_id)";
				$from .= " INNER JOIN $user_tb ON $post_tb.post_author = $user_tb.ID";
				
				$where = " WHERE ($post_tb.post_status = 'publish') AND  (UNIX_TIMESTAMP($post_tb.post_date) <= UNIX_TIMESTAMP())" ;
				
			}
				

			if ($show_month_range > 0) {
			 		$where .= " AND (UNIX_TIMESTAMP($post_tb.post_date) >= UNIX_TIMESTAMP(DATE_ADD(CURRENT_DATE, INTERVAL -$show_month_range month)))";
			}
			$order_limmit = " GROUP BY $post_tb.ID ORDER BY $view_tb.post_views DESC LIMIT 0, $disp_count";
			$sql = $select . $from . $where . $order_limmit;

			$populars = $wpdb->get_results($sql);
			
			foreach ($populars as $popular){
				$wp_query->in_the_loop = true;		//for use the_tags() in multi lopp 
				$r = new WP_Query("p=$popular->ID");
				if($r->have_posts()){
					$r->the_post();
					ob_start();
						the_ID();
						$post_id = ob_get_contents();
					ob_end_clean();
					
					ob_start();
						the_title();
						$title = ob_get_contents();
					ob_end_clean();
					
					ob_start();
						the_permalink();
						$permalink = ob_get_contents();
					ob_end_clean();					
					
					ob_start();
						the_author_posts_link();
						$author = ob_get_contents();
					ob_end_clean();
					
					ob_start();
						the_category(' &bull; ');
						$category = ob_get_contents();
					ob_end_clean();	
					
					if (function_exists('the_tags')){
						ob_start();
							the_tags(__('Tags:', 'xpress') . ' ',' &bull; ','');
							$tags = ob_get_contents();
						ob_end_clean();	
					} else {
						$tags = '';
					}
					
					ob_start();
						the_modified_date($date_format);
						$post_modified_date = ob_get_contents();
					ob_end_clean();
						
					ob_start();
						the_modified_date($time_format);
						$post_modified_time = ob_get_contents();
					ob_end_clean();
					
					ob_start();
						the_time($date_format);
						$post_date = ob_get_contents();
					ob_end_clean();
					
					ob_start();
						the_time($time_format);
						$post_time = ob_get_contents();
					ob_end_clean();
					
					
					ob_start();
						comments_popup_link(__('Comments (0)'), __('Comments (1)'), __('Comments (%)'));
						$comments_popup_link = ob_get_contents();
					ob_end_clean();
					

					$post_title = '<a href="' . $permalink . '">' . $title . '</a>';
					$post_date_time = $post_date . ' ' . $post_time ;
					$post_modified_date_time = $post_modified_date . ' ' . $post_modified_time ;
					$trackback_url = trackback_url(false);
					$post_views = xpress_post_views_count('post_id=' . $post_id . '&format=' . __('Views :%d', 'xpress'). '&echo=0');
					$post_views_num = (int) xpress_post_views_count("post_id={$post_id}&format=%d&echo=0");
					$row_data = array(
						'blog_title' => $blog_title ,
						'blog_link' => $blog_link ,
						'blog_title_link' => $blog_title_link ,
						'post_id'		=> $post_id ,
						'post_title'	=> $post_title ,
						'post_date' => $post_date ,
						'post_time' => $post_time ,
						'post_date_time' => $post_date_time ,
						'post_modified_date' => $post_modified_date ,
						'post_modified_time' => $post_modified_time ,
						'post_modified_date_time' => $post_modified_date_time ,
						'post_author' 	=> $author ,
						'post_category' 	=> $category ,	
						'post_tags' 		=> $tags,
						'post_views' 		=> $post_views,
						'post_views_num'	=> $post_views_num,
						'comment_link' 	=> $comments_popup_link ,
						'trackback_url' => $trackback_url
					);
					$data_array[] = $row_data;
				}
			}  // end of foreach
			restore_current_blog();
			$wp_rewrite->init();
		}
		usort($data_array, "post_views_cmp");
		if (!empty($disp_count)){
			$data_array = array_slice($data_array,0,$disp_count);
		}

		$item_no = 0;
		foreach ($data_array as $data) {
			$block['contents']['item'.$item_no] = $data;
			$item_no++;
		}
		$block['data_count'] = $item_no;
	} else {
		$block['err_message'] = __('This blog is not set to the multi blog.', 'xpress');
	}
	return $block ;
}
function post_views_cmp($a, $b)
{
    return $b['post_views_num'] - $a['post_views_num'];
}
?>