<?php
// Block Version: 1.0
function global_recent_posts_list_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_global_recent_posts_list_block.html' : trim( $options[1] );
	$disp_count = empty( $options[2] ) ? '10' : $options[2] ;
	$disp_red = empty( $options[3] ) ? '1' : $options[3] ;
	$disp_green = empty( $options[4] ) ? '7' : $options[4] ;
	$date_format = empty( $options[5] ) ? '' : $options[5] ;
	$time_format = empty( $options[6] ) ? '' : $options[6] ;
	$shown_for_each_blog = empty( $options[7] ) ? false : true ;		
	$exclusion_blog = empty( $options[8] ) ? '0' : $options[8] ;
	
	$mydirpath = get_xpress_dir_path();
	
	if (xpress_is_multiblog() && function_exists('get_blog_list')){
		if (empty($date_format)) $date_format = get_settings('date_format');
		if (empty($time_format)) $time_format = get_settings('time_format');
		if(empty($tag_select)) $tag_where = ''; else $tag_where = "tag='$tag_select'&";
		
		global $wpdb,$wp_query;
		$block = array();
		
		$data_array = xpress_grobal_recent_posts($disp_count,$exclusion_blog,$shown_for_each_blog);
		$item_no = 0;
		$red_sec = $disp_red *60*60*24;
		$green_sec = $disp_green *60*60*24;
		foreach($data_array as $data){
			$elapse = time() - $data->post_unix_time;
			$new_mark = '';
			if ($elapse < $red_sec ) {
				$new_mark = '<em style="color: red; font-size: small;">New! </em>';

			} else if ($elapse < $green_sec) {
				$new_mark = '<em style="color: green; font-size: small;">New! </em>';
			}
			$data->new_mark = $new_mark;

			$row_data = get_object_vars($data);
				
			$block['contents']['item'.$item_no] = $row_data;
			$item_no++;
		}
		$block['data_count'] = $item_no;  //xml unserialise error
		$block['shown_for_each_blog'] = $shown_for_each_blog;
	} else {
		$block['err_message'] = __('This blog is not set to the multi blog.', 'xpress');
	}
		
	return $block ;
}
?>