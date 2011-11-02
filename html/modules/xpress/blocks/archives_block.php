<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_archives_show($options){
	return _b_archives_show($options) ;
}
function b_'.$mydirname.'_archives_edit($options){
	return _b_archives_edit($options) ;
}
' ) ;		
	

if( ! defined( 'XPRESS_ARCHIVES_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_ARCHIVES_BLOCK_INCLUDED' , 1 ) ;
	
	function _b_archives_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_archives_block.html' : trim( $options[1] );
		$type = empty( $options[2] ) ? 'monthly' : $options[2] ;
		$limit  = !is_numeric( $options[3] ) ? 0 : $options[3] ;
		$show_post_count = empty( $options[4] ) ? false : true ;		
		$drop_down = empty( $options[5] ) ? false : true ;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$a_month = _MB_XP2_ARC_MONTH ;
		$a_week = _MB_XP2_ARC_WEEK ;		

		$form .= _MB_XP2_ARC_TYPE .": ";
		$form .=  "<select name='options[2]'>";
		if ($type =='yearly')
			$form .=  "<option value='yearly' selected='selected'>". _MB_XP2_ARC_YEAR;
		else
			$form .=  "<option value='yearly'>". _MB_XP2_ARC_YEAR;
		
		if ($type =='monthly')
			$form .=  "<option value='monthly' selected='selected'>". _MB_XP2_ARC_MONTH;
		else
			$form .=  "<option value='monthly'>". _MB_XP2_ARC_MONTH;
		
		if ($type =='weekly')
			$form .=  "<option value='weekly' selected='selected'>". _MB_XP2_ARC_WEEK;
		else
			$form .=  "<option value='weekly'>". _MB_XP2_ARC_WEEK;
		
		if ($type =='daily')
			$form .=  "<option value='daily' selected='selected'>". _MB_XP2_ARC_DAY;
		else
			$form .=  "<option value='daily'>". _MB_XP2_ARC_DAY;

		if ($type =='postbypost')
			$form .=  "<option value='postbypost' selected='selected'>". _MB_XP2_ARC_POST;
		else
			$form .=  "<option value='postbypost'>". _MB_XP2_ARC_POST;

		$form .=  "</select><br/>";
		
		$form .= "<br />" . _MB_XP2_COUNT_ZERO_ALL . "  <input type='text' size='3' name='options[3]' value='" . $limit . "' />";
		$form .= "<br />" . yes_no_radio_option('options[4]', _MB_XP2_SHOW_NUM_OF_POST , $show_post_count);
		$form .= "<br />" . yes_no_radio_option('options[5]', _MB_XP2_SHOW_DROP_DOWN , $drop_down);
//	    $form .="<br /><input type='text' size='60' name='options[5]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />";
	    
		return $form;
	}

	function _b_archives_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}


}

?>