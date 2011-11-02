<?php
// Block Version: 1.0
function calender_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_calender_block.html' : trim( $options[1] );
	$sun_color = empty( $options[2] ) ? '#DB0000' : $options[2] ;
	$sat_color = empty( $options[3] ) ? '#004D99' : $options[3] ;
//	$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
	$mydirpath = get_xpress_dir_path();

	$block['calender'] = xpress_get_calendar('sun_color=' . $sun_color . '&sat_color=' .$sat_color);
	return $block ;	
}
?>