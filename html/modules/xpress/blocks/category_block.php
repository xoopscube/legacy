<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_category_show($options){
	return _b_category_show($options) ;
}
function b_'.$mydirname.'_category_edit($options){
	return _b_category_edit($options) ;
}
' ) ;		
	
if( ! defined( 'XPRESS_CATEGORY_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_CATEGORY_BLOCK_INCLUDED' , 1 ) ;

	function _b_category_edit($options)
	{

		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_category_block.html' : trim( $options[1] );
		$show_option_all = empty( $options[2] ) ? '' : $options[2] ;
		$orderby = empty( $options[3] ) ? 'name' : $options[3] ;
		$order = empty( $options[4] ) ? 'ASC' : $options[4] ;
		$show_last_updated = empty( $options[5] ) ? false : true ;
		$show_count = empty( $options[6] ) ? false : true ;
		$hide_empty = empty( $options[7] ) ? false : true ;
		$use_desc_for_title = empty( $options[8] ) ? false : true ;
		$exclude = empty( $options[9] ) ? '' : $options[9] ;
		$includes = empty( $options[10] ) ? '' : $options[10] ;
		$hierarchical = empty( $options[11] ) ? false : true ;
		$depth  = !is_numeric( $options[12] ) ? 0 : $options[12] ;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');	

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= _MB_XP2_CAT_ALL_STR . "  <input type='text' name='options[2]' value='" . $show_option_all . "' /><br />";
		$form .= _MB_XP2_CAT_ORDERBY .": ";
		$form .=  "<select name='options[3]'>";
		if ($orderby =='name')
			$form .=  "<option value='name' selected='selected'>". _MB_XP2_CAT_NAME;
		else
			$form .=  "<option value='name'>". _MB_XP2_CAT_NAME;
		if ($orderby =='count')
			$form .=  "<option value='count' selected='selected'>". _MB_XP2_CAT_COUNT;
		else
			$form .=  "<option value='count'>". _MB_XP2_CAT_COUNT;
		if ($orderby =='ID')
			$form .=  "<option value='ID' selected='selected'>". _MB_XP2_CAT_ID;
		else
			$form .=  "<option value='ID'>". _MB_XP2_CAT_ID;
		$form .=  "</select><br/>";
		
		$form .= _MB_XP2_SORT_ORDER .": ";
		$form .=  "<select name='options[4]'>";
		if ($order =='ASC')
			$form .=  "<option value='ASC' selected='selected'>" . _MB_XP2_SORT_ASC;
		else
			$form .=  "<option value='ASC'>" . _MB_XP2_SORT_ASC;
		if ($order =='DESC')
			$form .=  "<option value='DESC' selected='selected'>" . _MB_XP2_SORT_DESC;
		else
			$form .=  "<option value='DESC'>" . _MB_XP2_SORT_DESC;
		$form .=  "</select><br/>";
		
		$form .= yes_no_radio_option('options[5]', _MB_XP2_SHOW_LAST_UPDATE , $show_last_updated) . "<br />" ;
		$form .= yes_no_radio_option('options[6]', _MB_XP2_SHOW_NUM_OF_POST , $show_count) . "<br />" ;
		$form .= yes_no_radio_option('options[7]', _MB_XP2_CAT_HIDE_EMPTY , $hide_empty) . "<br />" ;
		$form .= yes_no_radio_option('options[8]', _MB_XP2_DESC_FOR_TITLE , $use_desc_for_title) . "<br />" ;
		$form .= _MB_XP2_CAT_EXCLUDE . "  <input type='text' name='options[9]' value='" . $exclude . "' size ='60' /><br />";
		$form .= _MB_XP2_CAT_INCLUDE . "  <input type='text' name='options[10]' value='" . $includes . "' size ='60' /><br />";
		$form .= yes_no_radio_option('options[11]', _MB_XP2_CAT_HIERARCHICAL , $hierarchical) . "<br />" ;
		$form .= _MB_XP2_CAT_DEPTH . "  <input type='text' name='options[12]' value='" . $depth . "' size ='8' /><br />";
		return $form;
	}
	
	function _b_category_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
	
}

?>