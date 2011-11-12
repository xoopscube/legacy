<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_comments_show($options){
	return _b_comments_show($options) ;
}
function b_'.$mydirname.'_comments_edit($options){
	return _b_comments_edit($options) ;
}
' ) ;


if( ! defined( 'XPRESS_COMMENTS_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_COMMENTS_BLOCK_INCLUDED' , 1 ) ;
	function _b_comments_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_recent_comments_block.html' : trim( $options[1] );
		$disp_count = empty( $options[2] ) ? '10' : $options[2] ;
		$disp_length = empty( $options[3] ) ? '30' : $options[3] ;
		$date_format = empty( $options[4] ) ? '' : $options[4] ;
		$time_format = empty( $options[5] ) ? '' : $options[5] ;
		$com_select = empty( $options[6] ) ? '0' : $options[6] ;

		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;

		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');
		
		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= _MB_XP2_COUNT .": <input type='text' size='3' name='options[2]' value='" . $disp_count . "' /><br />\n";
		$form .= _MB_XP2_LENGTH .": <input type='text' size='3' name='options[3]' value='" . $disp_length . "' /><br />\n";
		$form .= _MB_XP2_DATE_FORMAT .": <input type='text' name='options[4]' value='" . $date_format . "' /><br />\n";
		$form .= _MB_XP2_TIME_FORMAT .": <input type='text' name='options[5]' value='" . $time_format . "' /><br />\n";
	    $form .= "<br />\n";
	    $form .= comment_type_select('options[6]' , $com_select);


		return $form;
	}
	
	function _b_comments_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
}
?>