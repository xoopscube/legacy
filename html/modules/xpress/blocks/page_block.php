<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_page_show($options){
	return _b_page_show($options) ;
}
function b_'.$mydirname.'_page_edit($options){
	return _b_page_edit($options) ;
}
' ) ;		
	
if( ! defined( 'XPRESS_PAGE_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_PAGE_BLOCK_INCLUDED' , 1 ) ;

	function _b_page_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_page_block.html' : trim( $options[1] );
		$sort_column = empty( $options[2] ) ? 'post_title' : $options[2] ;
		$sort_order = empty( $options[3] ) ? 'asc' : $options[3] ;
		$exclude = empty( $options[4] ) ? '' : $options[4] ;
		$exclude_tree = empty( $options[5] ) ? '' : $options[5] ;
		$includes = empty( $options[6] ) ? '' : $options[6] ;
		$depth = !is_numeric( $options[7] ) ? 0 : $options[7] ;
		$child_of = !is_numeric( $options[8] ) ? 0 : $options[8] ;
		$show_date = empty( $options[9] ) ? 'none' : $options[9] ;
		$date_format = empty( $options[10] ) ? '' : $options[10] ;
		$hierarchical = empty( $options[11] ) ? false : true ;
		$meta_key = empty( $options[12] ) ? '' : $options[12] ;
		$meta_value = empty( $options[13] ) ? '' : $options[13] ;

		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= _MB_XP2_PAGE_ORDERBY .": ";
		$form .=  "<select name='options[2]'>";
		
		if ($sort_column =='post_title')
			$form .=  "<option value='post_title' selected='selected'>". _MB_XP2_PAGE_TITLE;
		else
			$form .=  "<option value='post_title'>". _MB_XP2_PAGE_TITLE;
		
		if ($sort_column =='menu_order')
			$form .=  "<option value='menu_order' selected='selected'>". _MB_XP2_PAGE_MENU_ORDER;
		else
			$form .=  "<option value='menu_order'>". _MB_XP2_PAGE_MENU_ORDER;
		
		if ($sort_column =='post_date')
			$form .=  "<option value='post_date' selected='selected'>". _MB_XP2_PAGE_POST_DATE;
		else
			$form .=  "<option value='post_date'>". _MB_XP2_PAGE_POST_DATE;
		
		if ($sort_column =='post_modified')
			$form .=  "<option value='post_modified' selected='selected'>". _MB_XP2_PAGE_POST_MODIFY;
		else
			$form .=  "<option value='post_modified'>". _MB_XP2_PAGE_POST_MODIFY;
		
		if ($sort_column =='ID')
			$form .=  "<option value='ID' selected='selected'>". _MB_XP2_PAGE_ID;
		else
			$form .=  "<option value='ID'>". _MB_XP2_PAGE_ID;
		
		if ($sort_column =='post_author')
			$form .=  "<option value='post_author' selected='selected'>". _MB_XP2_PAGE_AUTHOR;
		else
			$form .=  "<option value='post_author'>". _MB_XP2_PAGE_AUTHOR;
		
		if ($sort_column =='post_name')
			$form .=  "<option value='post_name' selected='selected'>". _MB_XP2_PAGE_SLUG;
		else
			$form .=  "<option value='post_name'>". _MB_XP2_PAGE_SLUG;
		
		$form .=  "</select><br/>";
		
		
		$form .= _MB_XP2_SORT_ORDER .": ";
		$form .=  "<select name='options[3]'>";
		if ($sort_order =='asc')
			$form .=  "<option value='asc' selected='selected'>" . _MB_XP2_SORT_ASC;
		else
			$form .=  "<option value='asc'>" . _MB_XP2_SORT_ASC;
		if ($sort_order =='desc')
			$form .=  "<option value='desc' selected='selected'>" . _MB_XP2_SORT_DESC;
		else
			$form .=  "<option value='desc'>" . _MB_XP2_SORT_DESC;
		$form .=  "</select><br/>\n";
		
		$form .= _MB_XP2_PAGE_EXCLUDE . "  <input type='text' name='options[4]' value='" . $exclude . "' size ='60' /><br />\n";
		$form .= _MB_XP2_PAGE_EXCLUDE_TREE . "  <input type='text' name='options[5]' value='" . $exclude_tree . "' size ='60' /><br />\n";
		$form .= _MB_XP2_PAGE_INCLUDE . "  <input type='text' name='options[6]' value='" . $includes . "' size ='60' /><br />\n";
		$form .= _MB_XP2_PAGE_DEPTH . "  <input type='text' name='options[7]' value='" . $depth . "' size ='8' /><br />";
		$form .= _MB_XP2_PAGE_CHILD_OF . "  <input type='text' name='options[8]' value='" . $child_of . "' size ='8' /><br />";

		$form .= _MB_XP2_SHOW_DATE_SELECT .": ";
		$form .=  "<select name='options[9]'>";
		if ($show_date =='none')
			$form .=  "<option value='none' selected='selected'>" . _MB_XP2_SHOW_DATE_NONE;
		else
			$form .=  "<option value='none'>" . _MB_XP2_SHOW_DATE_NONE;

		if ($show_date =='post_date')
			$form .=  "<option value='post_date' selected='selected'>" . _MB_XP2_SHOW_POST_DATE;
		else
			$form .=  "<option value='post_date'>" . _MB_XP2_SHOW_POST_DATE;

		if ($show_date =='modified')
			$form .=  "<option value='modified' selected='selected'>" . _MB_XP2_SHOW_MODIFY_DATE;
		else
			$form .=  "<option value='modified'>" . _MB_XP2_SHOW_MODIFY_DATE;
		
		$form .=  "</select><br/>\n";

		$form .= _MB_XP2_DATE_FORMAT .": <input type='text' name='options[10]' value='" . $date_format . "' /><br />\n";
		$form .= yes_no_radio_option('options[11]', _MB_XP2_PAGE_HIERARCHICAL , $hierarchical) . "<br />" ;
		$form .= _MB_XP2_PAGE_META_KEY . "  <input type='text' name='options[12]' value='" . $meta_key . "' size ='40' /><br />\n";
		$form .= _MB_XP2_PAGE_META_VALUE . "  <input type='text' name='options[13]' value='" . $meta_value . "' size ='40' /><br />\n";
    
		return $form;
	}

	function _b_page_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}

}

?>