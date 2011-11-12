<?php
// Block Version: 1.0
function search_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_search.html' : trim( $options[1] );
	$input_length = empty( $options[2] ) ? '18' : $options[2] ;
	$mydirpath = get_xpress_dir_path();

	$act_url = get_bloginfo('siteurl');
	$output  = '<form method="get" id="searchform" action="' . $act_url . '">' ."\n";
	$output .= '<input type="text" name="s" id="s" size="' . $input_length . '" />' ."\n";
	$output .= '<input type="submit" id="searchsubmit" value="' . __('Search', 'xpress') . '" />' ."\n";
	$output .= "</form>\n";

	$block['search'] = $output;
	return $block ;
}

?>