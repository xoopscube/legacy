<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_enhanced_show($options){
	return _b_enhanced_show($options) ;
}
function b_'.$mydirname.'_enhanced_edit($options){
	return _b_enhanced_edit($options) ;
}
' ) ;

if( ! defined( 'XPRESS_ENHANCED_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_ENHANCED_BLOCK_INCLUDED' , 1 ) ;
	
	function _b_enhanced_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_enhanced_block.html' : trim( $options[1] );
		$include_file = empty( $options[2] ) ? '' : $options[2] ;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= _MB_XP2_ENHACED_FILE .":<br />\n";
		$form .= '&emsp;' . _MB_XP2_FILE_NAME . ": <b>my_</b><input type='text' name='options[2]' value='" . $include_file . "' /><b>_block.php</b><br>\n";
		$form .= '&emsp;' . _MB_XP2_MAKE_ENHACED_FILE . "<br>\n";
		return $form;
	}
	
	function _b_enhanced_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
	
}

?>