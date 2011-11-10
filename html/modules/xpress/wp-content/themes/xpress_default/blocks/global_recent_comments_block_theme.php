<?php
// Block Version: 1.0
function global_recent_comments_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_global_recent_comments_block.html' : trim( $options[1] );
	$disp_count = empty( $options[2] ) ? '10' : $options[2] ;
	$disp_length = empty( $options[3] ) ? '30' : $options[3] ;
	$date_format = empty( $options[4] ) ? '' : $options[4] ;
	$time_format = empty( $options[5] ) ? '' : $options[5] ;
	$com_select = empty( $options[6] ) ? '0' : $options[6] ;

	$selected = explode(',' , $com_select);

	$mydirpath = get_xpress_dir_path();
	
	if (empty($date_format)) $date_format = get_settings('date_format');
	if (empty($time_format)) $time_format = get_settings('time_format');
	
	$disp_all = in_array('0',$selected);
	$disp_comment = in_array('1',$selected);
	$disp_trackback = in_array('2',$selected);
	$disp_pingback = in_array('3',$selected);
	
	$type_select = '';
	if (!$disp_all){			
		if ($disp_comment){
			$in_where =  "''";
		}
		if ($disp_trackback){
			if (empty($in_where)) $in_where =  "'trackback' "; else $in_where .=  ",'trackback'";
		}
				
		if ($disp_pingback){
			if (empty($in_where)) $in_where =  "'pingback' "; else $in_where .=  ",'pingback'";
		}
		
		if (! empty($in_where)){
			$type_select = " AND comment_type IN($in_where) ";				
		}
	}
	
	global $wpdb, $wp_rewrite , $switched , $blog_id;
	$block = array();
	$comment_array = array();
	if (xpress_is_multiblog()){
		$blogs = get_blog_list(0,'all');
		foreach ($blogs AS $blog) {
			switch_to_blog($blog['blog_id']);
			$wp_rewrite->init();
			if (!is_null($wpdb)){
				$comment_sql  = "SELECT comment_ID,comment_post_ID,comment_author,comment_author_email,comment_author_url,comment_content, comment_type,UNIX_TIMESTAMP(comment_date) as comment_unix_time ";
				$comment_sql .= "FROM $wpdb->comments LEFT JOIN $wpdb->posts ON  $wpdb->posts.ID = $wpdb->comments.comment_post_ID ";
				if (xpress_is_wp_version('<','2.1')){
					$comment_sql .= "WHERE comment_approved = '1' AND post_status = 'publish' $type_select ";
				} else {
					$comment_sql .= "WHERE comment_approved = '1' AND post_type = 'post'  AND post_status = 'publish' $type_select ";
				}
				$comment_sql .= "ORDER BY comment_date_gmt DESC LIMIT $disp_count";
				$comments = $wpdb->get_results($comment_sql);
				
				if ( $comments ) {
						$blog_title = get_bloginfo('name');
						$blog_link = get_bloginfo('url');
						$blog_title_link = '<a href="' . $blog_link . '">' . $blog_title . '</a>' ;
					foreach ($comments as $comment){
						$comment_content = $comment->comment_content;
						$comment_unix_time = $comment->comment_unix_time;
						$comment_excerpt = ($disp_length>0 ? xpress_substr($comment_content, 0, $disp_length): $comment->comment_content);
						$comment_link = get_comment_link($comment->comment_ID);
						$comment_title = $comment_excerpt;
						$comment_title_link = "<a href='$comment_link' rel='external nofollow' class='url'>$comment_title</a>";

						$post_link = get_permalink($comment->comment_post_ID);
						$post_title = get_the_title($comment->comment_post_ID);
						$post_title_link = '<a href="'. $post_link . '">' . $post_title . '</a>';
						
						$author_link = $comment->comment_author_url;
						$author_name = $comment->comment_author;
						$author_name_link = (( empty( $author_link ) || 'http://' == $author_link ) ? $author_name : "<a href='$author_link' rel='external nofollow' class='url'>$author_name</a>");

						$comment_type = (empty($comment->comment_type) ? 'comment': $comment->comment_type);
						
						$post_title_comment_link = '<a href="'. $comment_link . '">' . $post_title . '</a>';
						$from_auther_to_post = sprintf(__('%1$s on %2$s','xpress'), $author_name_link , $post_title_comment_link );

						$row_data = array(
							'blog_title' => $blog_title ,
							'blog_link' => $blog_link ,
							'blog_title_link' => $blog_title_link ,
							'comment_ID' 		=> $comment->comment_ID ,
							'comment_post_ID'	=> $comment->comment_post_ID ,
							'comment_unix_time' => $comment_unix_time ,
							'comment_date' 		=> date($date_format,$comment->comment_unix_time) ,
							'comment_date_time' => date($date_format . ' ' . $time_format,$comment->comment_unix_time) ,
							'comment_content' 	=> $comment_content ,
							'comment_excerpt' 	=> $comment_excerpt ,
							'comment_link' 		=> $comment_link,
							'comment_title' 	=> $comment_title ,
							'comment_title_link' => $comment_title_link ,
							'post_link' 		=> $post_link,
							'post_title' 		=> $post_title,
							'post_title_link' 	=> $post_title_link,
							'author_link' 		=> $author_link,
							'author_name' 		=> $author_name,
							'author_name_link' 	=> $author_name_link,
							'comment_type' 		=> $comment_type,
							'from_auther_to_post' => $from_auther_to_post
						);
						$comment_array[] = $row_data;
					}
					$block['data_count'] = $item_no;
				}
			}
			restore_current_blog();
			$wp_rewrite->init();
		} // end of foreach
		usort($comment_array, "the_comment_time_cmp");
		if (!empty($disp_count)){
			$comment_array = array_slice($comment_array,0,$disp_count);
		}

		$item_no = 0;
		foreach ($comment_array AS $comment) {
			$block['contents']['item'.$item_no] = $comment;
			$item_no++;
		}
		$block['data_count'] = $item_no;
	} else {
		$block['err_message'] = __('This blog is not set to the multi blog.', 'xpress');
	}

	return $block ;
}
function the_comment_time_cmp($a, $b)
{
    return - strcasecmp($a['comment_unix_time'], $b['comment_unix_time']);
}

?>