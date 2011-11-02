<?php
// Block Version: 1.0
function blog_list_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_category.html' : trim( $options[1] );
	$orderby = empty( $options[2] ) ? 'name' : $options[2] ;
	$order = empty( $options[3] ) ? 'ASC' : $options[3] ;

	if (xpress_is_multiblog() && function_exists('get_blog_list')){
		$blogs = get_blog_list(0,'all');
		$data = array();
		foreach ($blogs AS $blog) {
			$url =  get_blog_option($blog['blog_id'],'siteurl');
			$blog_name = get_blog_option( $blog['blog_id'], 'blogname' );
			$blog_link = "<a href=\" $url \"> $blog_name </a>";
			$blog_id = $blog['blog_id'];
			$post_count = $blog['postcount'];
			$last_post_date = '';
			$last_post_time = '';
			$last_post_date_time = '';
				
			$row_data = array(
				'blog_id'		=> $blog_id ,
				'blog_name'	=> $blog_link ,
				'last_post_date' => $last_post_date ,
				'last_post_time' => $last_post_time ,
				'post_date_time' => $last_post_date_time ,
				'last_post_date_time' => $post_modified_date ,
				'post_count' => $post_count
			);
			$data[] = $row_data;
		}
		if (strcmp($order,'ASC') == 0){
			switch($orderby){
				case 'count':
					usort($data, "r_post_count_cmp");
					break;
				case 'ID' :
					usort($data, "r_blog_id_cmp");
					break;
				default :
					usort($data, "r_blog_name_cmp");
			}
		} else {
			switch($orderby){
				case 'count':
					usort($data, "post_count_cmp");
					break;
				case 'ID' :
					usort($data, "blog_id_cmp");
					break;
				default :
					usort($data, "blog_name_cmp");
			}
		}
		
		$block = array();
		$item_no = 0;	
		foreach ($data AS $row) {
			$block['contents']['item'.$item_no] = $row;
			$item_no++;
		}// end of foreach
		$block['data_count'] = $item_no;  //xml unserialise error
	} else {
		$block['err_message'] = __('This blog is not set to the multi blog.', 'xpress');
	}
	return $block ;
}
function blog_name_cmp($a, $b)
{
    return - strcasecmp($a["blog_name"], $b["blog_name"]);
}
function blog_id_cmp($a, $b)
{
    return $b["blog_id"] - $a["blog_id"];
}
function post_count_cmp($a, $b)
{
    return $b["post_count"] - $a["post_count"];
}

function r_blog_name_cmp($a, $b)
{
    return strcasecmp($a["blog_name"], $b["blog_name"]);
}
function r_blog_id_cmp($a, $b)
{
    return $a["blog_id"] - $b["blog_id"];
}
function r_post_count_cmp($a, $b)
{
    return $a["post_count"] - $b["post_count"];
}

?>