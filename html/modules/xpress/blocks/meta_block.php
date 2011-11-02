<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_meta_show($options){
	return _b_meta_show($options) ;
}
function b_'.$mydirname.'_meta_edit($options){
	return _b_meta_edit($options) ;
}
' ) ;		
	
if( ! defined( 'XPRESS_META_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_META_BLOCK_INCLUDED' , 1 ) ;

	function _b_meta_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_meta_block.html' : trim( $options[1] );
		$wp_link = empty( $options[2] ) ? false : true ;
		$xoops_link = empty( $options[3] ) ? false : true ;
		$post_rss = empty( $options[4] ) ? false : true ;
		$comment_rss = empty( $options[5] ) ? false : true ;
		$post_new = empty( $options[6] ) ? false : true ;
		$admin_edit = empty( $options[7] ) ? false : true ;
		$readme = empty( $options[8] ) ? false : true ;
		$ch_style = empty( $options[9] ) ? false : true ;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');	

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= yes_no_radio_option('options[2]', _MB_XP2_META_WP_LINK , $wp_link) . "<br />\n";
		$form .= yes_no_radio_option('options[3]', _MB_XP2_META_XOOPS_LINK , $xoops_link) . "<br />\n";
		$form .= yes_no_radio_option('options[4]', _MB_XP2_META_POST_RSS , $post_rss) . "<br />\n";
		$form .= yes_no_radio_option('options[5]', _MB_XP2_META_COMMENT_RSS , $comment_rss) . "<br />\n";
		$form .= yes_no_radio_option('options[6]', _MB_XP2_META_POST_NEW , $post_new) . "<br />\n";
		$form .= yes_no_radio_option('options[7]', _MB_XP2_META_ADMIN , $admin_edit) . "<br />\n";
		$form .= yes_no_radio_option('options[8]', _MB_XP2_META_README , $readme) . "<br />\n";
		$form .= yes_no_radio_option('options[9]', _MB_XP2_META_CH_STYLE , $ch_style) . "\n";
	    
		return $form;
	}
	
	function _b_meta_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
}

?>