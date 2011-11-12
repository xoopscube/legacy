<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_sidebar_show($options){
	return _b_sidebar_show($options) ;
}
function b_'.$mydirname.'_sidebar_edit($options){
	return _b_sidebar_edit($options) ;
}
' ) ;		
	
if( ! defined( 'XPRESS_SIDEBAR_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_SIDEBAR_BLOCK_INCLUDED' , 1 ) ;
	
	function _b_sidebar_show($options)
	{
		$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}

}

?>