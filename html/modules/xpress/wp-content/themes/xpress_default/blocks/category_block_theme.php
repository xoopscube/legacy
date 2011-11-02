<?php
// Block Version: 1.0
function category_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_category.html' : trim( $options[1] );
	$show_option_all = empty( $options[2] ) ? '' : $options[2] ;
	$orderby = empty( $options[3] ) ? 'name' : $options[3] ;
	$order = empty( $options[4] ) ? 'ASC' : $options[4] ;
	$show_last_updated = empty( $options[5] ) ? false : true ;
	$show_count = empty( $options[6] ) ? false : true ;
	$hide_empty = empty( $options[7] ) ? false : true ;
	$use_desc_for_title = empty( $options[8] ) ? false : true ;
	$exclude = empty( $options[9] ) ? '' : $options[9] ;
	$includes = empty( $options[10] ) ? '' : $options[10] ;
	$hierarchical = empty( $options[11] ) ? false : true ;
	$depth  = !is_numeric( $options[12] ) ? 0 : $options[12] ;
	
	if (function_exists('wp_list_categories')){
		$param = array(
			'show_option_all' => $show_option_all, 
			'orderby' => $orderby, 
			'order' => $order, 
			'show_last_update' => $show_last_updated, 
			'style' => 'list',
			'show_count' => $show_count, 
			'hide_empty' => $hide_empty, 
			'use_desc_for_title' => $use_desc_for_title, 
			'child_of' => 0, 
			'feed' => '', 
			'feed_image' => '', 
			'exclude' => $exclude, 
			'include' => $includes, 
			'hierarchical' => $hierarchical, 
			'title_li' => '',
			'number' => '',
			'echo' => 0,
			'depth' => $depth
		);
		if ( xpress_is_wp_version('>=','2.3') ) {
			$block['categories'] = wp_list_categories($param);
		} else {	// not suport echo flag
			ob_start();
			wp_list_categories($param);
			$block['categories'] = ob_get_contents();
			ob_end_clean();
		}
	} else {
		if (empty($show_option_all))
			$optionall = 0;
		else
			$optionall = 1;
		$param = array(
			'optionall' => $optionall, 
			'all' => $show_option_all,
			'sort_column' => $orderby, 
			'sort_order' => $order, 
			'show_last_update' => $show_last_updated, 
			'optioncount' => $show_count, 
			'hide_empty' => $hide_empty, 
			'use_desc_for_title' => $use_desc_for_title, 
			'child_of' => 0, 
			'feed' => '', 
			'feed_image' => '', 
			'exclude' => $exclude, 
			'hierarchical' => $hierarchical, 
			'recurse' => 1,
		);
		ob_start();
			wp_list_cats($param);
			$block['categories'] = ob_get_contents();
		ob_end_clean();	
	}
	return $block ;	
}

?>