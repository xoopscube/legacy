<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_blog_list_show($options){
	return _b_blog_list_show($options) ;
}
function b_'.$mydirname.'_blog_list_edit($options){
	return _b_blog_list_edit($options) ;
}
' ) ;		
	
if( ! defined( 'XPRESS_BLOG_LIST_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_BLOG_LIST_BLOCK_INCLUDED' , 1 ) ;

	function _b_blog_list_edit($options)
	{

		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_blog_list_block.html' : trim( $options[1] );
		$orderby = empty( $options[2] ) ? 'name' : $options[2] ;
		$order = empty( $options[3] ) ? 'ASC' : $options[3] ;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');	

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= _MB_XP2_BLOG_ORDERBY .": ";
		$form .=  "<select name='options[2]'>";
		if ($orderby =='name')
			$form .=  "<option value='name' selected='selected'>". _MB_XP2_BLOG_NAME;
		else
			$form .=  "<option value='name'>". _MB_XP2_BLOG_NAME;
		if ($orderby =='count')
			$form .=  "<option value='count' selected='selected'>". _MB_XP2_BLOG_COUNT;
		else
			$form .=  "<option value='count'>". _MB_XP2_BLOG_COUNT;
		if ($orderby =='ID')
			$form .=  "<option value='ID' selected='selected'>". _MB_XP2_BLOG_ID;
		else
			$form .=  "<option value='ID'>". _MB_XP2_BLOG_ID;
		$form .=  "</select><br/>";
		
		$form .= _MB_XP2_SORT_ORDER .": ";
		$form .=  "<select name='options[3]'>";
		if ($order =='ASC')
			$form .=  "<option value='ASC' selected='selected'>" . _MB_XP2_SORT_ASC;
		else
			$form .=  "<option value='ASC'>" . _MB_XP2_SORT_ASC;
		if ($order =='DESC')
			$form .=  "<option value='DESC' selected='selected'>" . _MB_XP2_SORT_DESC;
		else
			$form .=  "<option value='DESC'>" . _MB_XP2_SORT_DESC;
		$form .=  "</select><br/>";
		
		return $form;
	}
	
	function _b_Blog_list_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
	
}

?>