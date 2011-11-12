<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_search_show($options){
	return _b_search_show($options) ;
}
function b_'.$mydirname.'_search_edit($options){
	return _b_search_edit($options) ;
}
' ) ;	

if( ! defined( 'XPRESS_SEARCH_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_SEARCH_BLOCK_INCLUDED' , 1 ) ;

	function _b_search_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_search_block.html' : trim( $options[1] );
		$disp_count = empty( $options[2] ) ? '18' : $options[2] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname ;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php') ;
		
		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";		
		$form .= _MB_XP2_SEARCH_LENGTH .": <input type='text' size='3' name='options[2]' value='" . $disp_count . "' /><br />";
//	    $form .="<br /><input type='text' size='60' name='options[2]' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />";

		return $form;
	}

	function _b_search_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
}
?>