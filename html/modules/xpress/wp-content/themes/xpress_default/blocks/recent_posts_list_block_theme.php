<?php
// Block Version: 1.0
function recent_posts_list_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_recent_posts_list_block.html' : trim( $options[1] );
	$disp_count = empty( $options[2] ) ? '10' : $options[2] ;
	$disp_red = empty( $options[3] ) ? '1' : $options[3] ;
	$disp_green = empty( $options[4] ) ? '7' : $options[4] ;
	$date_format = empty( $options[5] ) ? '' : $options[5] ;
	$time_format = empty( $options[6] ) ? '' : $options[6] ;
	$tag_select = $options[7] ;
	$cat_select = empty( $options[8] ) ? '0' : $options[8] ;
	
	$selected = explode(',' , $cat_select);

	$mydirpath = get_xpress_dir_path();

	if (empty($date_format)) $date_format = get_settings('date_format');
	if (empty($time_format)) $time_format = get_settings('time_format');
	if(empty($tag_select)) $tag_where = ''; else $tag_where = "tag='$tag_select'&";
	if (array_search(0,$selected)===0) $cat_select = 0;

	$selected_author_id = xpress_selected_author_id('echo=0');	
	if (!empty($selected_author_id)){
		$author_where ="author=$selected_author_id&";
	} else {
		$author_where = '';
	}
	
	global $wpdb,$wp_query;
	$block = array();
	$item_no = 0;	
	if (!is_null($wpdb)){
		$wp_query->in_the_loop = true;		//for use the_tags() in multi lopp 
		if ($cat_select) {
			$r = new WP_Query($author_where . $tag_where . "cat=$cat_select&showposts=$disp_count&what_to_show=posts&nopaging=0&post_status=publish");
		} else {
			$r = new WP_Query($author_where . $tag_where ."showposts=$disp_count&what_to_show=posts&nopaging=0&post_status=publish");
		}
		while($r->have_posts()){			
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
			
			$red_sec = $disp_red *60*60*24;
			$green_sec = $disp_green *60*60*24;
			ob_start();
				the_time('U');
				$check_time = ob_get_contents();
			ob_end_clean();
			$elapse = time() - $check_time;
			$new_mark = '';
			if ($elapse < $red_sec ) {
				$new_mark = '<em style="color: red; font-size: small;">New! </em>';

			} else if ($elapse < $green_sec) {
				$new_mark = '<em style="color: green; font-size: small;">New! </em>';
			}
			
			$post_title = '<a href="' . $permalink . '">' . $title . '</a>';
			$post_date_time = $post_date . ' ' . $post_time ;
			$post_modified_date_time = $post_modified_date . ' ' . $post_modified_time ;
			$trackback_url = trackback_url(false);
			$post_viwes = xpress_post_views_count('post_id=' . $post_id . '&format=' . __('Views :%d', 'xpress'). '&echo=0');

//			if (empty($tags)) $tags = __('Not Tag');

			$row_data = array(
				'post_id'		=> $post_id ,
				'new_mark'		=> $new_mark ,
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
				'post_views' 		=> $post_viwes,
				'comment_link' 	=> $comments_popup_link ,
				'trackback_url' => $trackback_url
			);
			
			$block['contents']['item'.$item_no] = $row_data;
			$item_no++;
		}
		$block['data_count'] = $item_no;  //xml unserialise error
	}
	return $block ;
}
?>