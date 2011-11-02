<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_tag_cloud_show($options){
	return _b_tag_cloud_show($options) ;
}
function b_'.$mydirname.'_tag_cloud_edit($options){
	return _b_tag_cloud_edit($options) ;
}
' ) ;		
	
if( ! defined( 'XPRESS_TAG_CLOUD_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_TAG_CLOUD_BLOCK_INCLUDED' , 1 ) ;
	
	function _b_tag_cloud_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_tag_cloud_block.html' : trim( $options[1] );
		$smallest = !is_numeric( $options[2] ) ? 8 : $options[2] ;
		$largest = !is_numeric( $options[3] ) ? 22 : $options[3] ;
		$unit = empty( $options[4] ) ? 'pt' : $options[4] ;
		$number = !is_numeric( $options[5] ) ? 45 : $options[5] ;
		$format = empty( $options[6] ) ? 'flat' : $options[6] ;
		$orderby = empty( $options[7] ) ? 'name' : $options[7] ;
		$order = empty( $options[8] ) ? 'ASC' : $options[8] ;
		$exclude = is_null( $options[9] ) ? '' : $options[9] ;
		$wp_include = is_null( $options[10] ) ? '' : $options[10] ;
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');	

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= _MB_XP2_CLOUD_SMALLEST .": <input type='text' size='4' name='options[2]' value='" . $smallest . "' /><br />";
		$form .= _MB_XP2_CLOUD_LARGEST .": <input type='text' size='4' name='options[3]' value='" . $largest . "' /><br />";
		$form .= _MB_XP2_CLOUD_UNIT .": ";
		$form .=  "<select name='options[4]'>";
		if ($unit =='pt')
			$form .=  "<option value='pt' selected='selected'>pt";
		else
			$form .=  "<option value='pt'>pt";
		if ($unit =='px')
			$form .=  "<option value='px' selected='selected'>px";
		else
			$form .=  "<option value='px'>px";
		if ($unit =='em')
			$form .=  "<option value='em' selected='selected'>em";
		else
			$form .=  "<option value='em'>em";
		if ($unit =='%')
			$form .=  "<option value='%' selected='selected'>%";
		else
			$form .=  "<option value='%'>%";
		$form .=  "</select><br/>";
		
		$form .= _MB_XP2_CLOUD_NUMBER .": <input type='text' size='4' name='options[5]' value='" . $number . "' /><br />";
		
		$form .= _MB_XP2_CLOUD_FORMAT .": ";
		$form .=  "<select name='options[6]'>";
		if ($format =='flat')
			$form .=  "<option value='flat' selected='selected'>" . _MB_XP2_FLAT;
		else
			$form .=  "<option value='flat'>" . _MB_XP2_FLAT;
		if ($format =='list')
			$form .=  "<option value='list' selected='selected'>". _MB_XP2_LIST;
		else
			$form .=  "<option value='list'>". _MB_XP2_LIST;
		$form .=  "</select><br/>";
				
		$form .= _MB_XP2_CLOUD_ORDERBY .": ";
		$form .=  "<select name='options[7]'>";
		if ($orderby =='name')
			$form .=  "<option value='name' selected='selected'>". _MB_XP2_TAG_NAME;
		else
			$form .=  "<option value='name'>". _MB_XP2_TAG_NAME;
		if ($orderby =='count')
			$form .=  "<option value='count' selected='selected'>". _MB_XP2_TAG_COUNT;
		else
			$form .=  "<option value='count'>". _MB_XP2_TAG_COUNT;
		$form .=  "</select><br/>";

		$form .= _MB_XP2_CLOUD_ORDER .": ";
		$form .=  "<select name='options[8]'>";
		if ($order =='ASC')
			$form .=  "<option value='ASC' selected='selected'>" . _MB_XP2_SORT_ASC;
		else
			$form .=  "<option value='ASC'>" . _MB_XP2_SORT_ASC;
		if ($order =='DESC')
			$form .=  "<option value='DESC' selected='selected'>" . _MB_XP2_SORT_DESC;
		else
			$form .=  "<option value='DESC'>" . _MB_XP2_SORT_DESC;
		if ($order =='RAND')
			$form .=  "<option value='RAND' selected='selected'>" . _MB_XP2_RAND;
		else
			$form .=  "<option value='RAND'>" . _MB_XP2_RAND;
		$form .=  "</select><br/>";

		$form .= _MB_XP2_CLOUD_EXCLUDE .": <input type='text' size='25' name='options[9]' value='" . $exclude . "' /><br />";
		$form .= _MB_XP2_CLOUD_INCLUDE .": <input type='text' size='25' name='options[10]' value='" . $wp_include . "' /><br />";


//	    $form .="<br /><input type='text' size='60' name='options[8]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />";
	    
		return $form;
	}
	
	function _b_tag_cloud_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
	
}

?>