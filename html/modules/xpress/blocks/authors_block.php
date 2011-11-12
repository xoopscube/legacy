<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_authors_show( $options){
	return _b_authors_show($options) ;
}
function b_'.$mydirname.'_authors_edit( $options){
	return _b_authors_edit($options) ;
}
' ) ;


if( ! defined( 'XPRESS_AUTHORS_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_AUTHORS_BLOCK_INCLUDED' , 1 ) ;
	
	function _b_authors_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_authors_block.html' : trim( $options[1] );
		$optioncount = empty( $options[2] ) ? false : true ;
		$exclude_admin = empty( $options[3] ) ? false : true ;
		$show_fullname = empty( $options[4] ) ? false : true ;
		$hide_empty = empty( $options[5] ) ? false : true ;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');
			
		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= yes_no_radio_option('options[2]', _MB_XP2_SHOW_NUM_OF_POST , $optioncount);
		$form .= "<br />" . yes_no_radio_option('options[3]', _MB_XP2_EXCLUEDEADMIN , $exclude_admin);
		$form .= "<br />" . yes_no_radio_option('options[4]', _MB_XP2_SHOW_FULLNAME , $show_fullname);
				$form .= "<br />" . yes_no_radio_option('options[5]', _MB_XP2_HIDE_EMPTY , $hide_empty);
//	    $form .="<br /><input type='text' size='60' name='options[4]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />";
	    
		return $form;
	}

	function _b_authors_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}


}
?>