<?php
// Block Version: 1.0
function page_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_page_block.html' : trim( $options[1] );
	$sort_column = empty( $options[2] ) ? 'post_title' : $options[2] ;
	$sort_order = empty( $options[3] ) ? 'asc' : $options[3] ;
	$exclude = empty( $options[4] ) ? '' : $options[4] ;
	$exclude_tree = empty( $options[5] ) ? '' : $options[5] ;
	$includes = empty( $options[6] ) ? '' : $options[6] ;
	$depth  = !is_numeric( $options[7] ) ? 0 : $options[7] ;
	$child_of  = !is_numeric( $options[8] ) ? 0 : $options[8] ;
	$show_date = empty( $options[9] ) ? 'none' : $options[9] ;
	$date_format = empty( $options[10] ) ? '' : $options[10] ;
	$hierarchical = empty( $options[11] ) ? false : true ;
	$meta_key = empty( $options[12] ) ? '' : $options[12] ;
	$meta_value = empty( $options[13] ) ? '' : $options[13] ;

	if (empty($date_format)) $date_format = get_option('date_format');
	if ($exclude_tree == 0 ) $exclude_tree = '';
	if ($show_date == 'none' ) $show_date = '';
	
	if (xpress_is_wp_version('>=','2.2')){
		$parm = array(
	    	'sort_column'	=> $sort_column, 
	    	'sort_order'	=> $sort_order, 
	    	'exclude'		=> $exclude,
	    	'exclude_tree'	=> $exclude_tree,
	    	'include'		=> $includes,
	    	'depth'			=> $depth, 
	    	'child_of'		=> $child_of,
	    	'show_date'		=> $show_date,
	    	'date_format'	=> $date_format,
	    	'title_li'		=> '',
	    	'echo'			=> 0,
	    	'hierarchical'	=> $hierarchical,
	    	'meta_key'		=> $meta_key,
	    	'meta_value'	=> $meta_value
	    );
		$output = "<ul>\n" . wp_list_pages($parm) . "\n</ul>\n";
	} else {
		$output = "<ul>\n";
		ob_start();
			wp_list_pages($parm);
			$output .= ob_get_contents();
		ob_end_clean();
		$output .="\n</ul>\n";
	}
	$block['list_pages'] = $output;
	return $block ;	
}

?>