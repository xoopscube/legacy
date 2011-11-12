<?php
// Block Version: 1.0
function authors_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_authors.html' : trim( $options[1] );
	$optioncount = empty( $options[2] ) ? 0 : 1 ;
	$exclude_admin = empty( $options[3] ) ? 0 : 1 ;
	$show_fullname = empty( $options[4] ) ? 0 : 1 ;
	$hide_empty = empty( $options[5] ) ? 0 : 1 ;		
	$mydirpath = get_xpress_dir_path();
	
	if(xpress_is_wp_version('<','2.3') ){
		$param_str = 'optioncount='. $optioncount . '&exclude_admin=' . $exclude_admin .'&show_fullname='. $show_fullname . '&hide_empty=' . $hide_empty;
		ob_start();
			wp_list_authors($param_str); //WP2011 wp_list_authors() used only parse_str()
			$list_authors = ob_get_contents() ;
		ob_end_clean();
	} else {
		$param = array(
			'optioncount' => $optioncount, 
			'exclude_admin' => $exclude_admin, 
			'show_fullname' => $show_fullname, 
			'hide_empty' => $hide_empty,
			'feed' => '',
			'feed_image' => '',
			'echo' => false
		);
		$list_authors =	wp_list_authors($param);
	}
	if (xpress_is_multi_user()){
		$all_link = '<li>' . '<a href="' . get_bloginfo('url'). '" title="' . __('All Authors','xpress') . '">' .__('All Authors','xpress') . '</a></li>';
	} else {
		$all_link = ''; 
	}
	$output = "<ul>\n" . $all_link . $list_authors . "\n</ul>\n";
	
	$block['authors'] = $output;
	
	return $block ;
}
?>