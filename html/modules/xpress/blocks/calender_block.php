<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_calender_show($options){
	return _b_calender_show($options) ;
}
function b_'.$mydirname.'_calender_edit($options){
	return _b_calender_edit($options) ;
}
' ) ;
	
if( ! defined( 'XPRESS_CALENDAR_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_CALENDAR_BLOCK_INCLUDED' , 1 ) ;

	function _b_calender_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_calender_block.html' : trim( $options[1] );
		$sun_color = empty( $options[2] ) ? '#DB0000' : $options[2] ;
		$sat_color = empty( $options[3] ) ? '#004D99' : $options[3] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= _MB_XP2_SUN_COLOR .": <input type='text' name='options[2]' value='" . $sun_color . "' /><br />\n";
		$form .= _MB_XP2_SAT_COLOR .": <input type='text' name='options[3]' value='" . $sat_color . "' /><br />\n";

		return $form;
	}

	function _b_calender_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);

	}
	
}

?>