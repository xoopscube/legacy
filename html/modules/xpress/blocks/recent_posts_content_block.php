<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_content_show($options){
	return _b_content_show($options) ;
}
function b_'.$mydirname.'_content_edit($options){
	return _b_content_edit($options) ;
}
' ) ;

if( ! defined( 'XPRESS_CONTENT_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_CONTENT_BLOCK_INCLUDED' , 1 ) ;
	
	function _b_content_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_recent_posts_content_block.html' : trim( $options[1] );
		$disp_count =  ($options[2])?intval($options[2]):10;
		$excerpt = empty( $options[3] ) ? false : true ;
		$excerpt_size =  ($options[4])?intval($options[4]):100;
		$date_format = empty( $options[5] ) ? '' : $options[5] ;
		$time_format = empty( $options[6] ) ? '' : $options[6] ;
		$tag_select = $options[7] ;
		$cat_select = empty( $options[8] ) ? '0' : $options[8] ;
		$day_select = ($options[9])?intval($options[9]):0;
		$day_size = ($options[10])?intval($options[10]):0;

		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;

		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');
		
		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />";
		$form .= _MB_XP2_COUNT .": <input type='text' size='3' name='options[2]' value='" . $disp_count . "' /><br />\n";
		$form .= yes_no_radio_option('options[3]', _MB_XP2_P_EXCERPT , $excerpt) . "<br />\n";
		$form .= _MB_XP2_P_EXCERPT_SIZE .": <input type='text' name='options[4]' value='" . $excerpt_size . "' /><br />\n";
		$form .= _MB_XP2_DATE_FORMAT .": <input type='text' name='options[5]' value='" . $date_format . "' /><br />\n";
		$form .= _MB_XP2_TIME_FORMAT .": <input type='text' name='options[6]' value='" . $time_format . "' /><br />\n";
		
		include $mydirpath .'/wp-includes/version.php' ;
		if (wp_version_compare($wp_version, '>=','2.3')){
			$form .= "<br />\n";
			$form .= _MB_XP2_TAGS_SELECT .": <input type='text' name='options[7]' value='" . $tag_select . "' /><br />\n";
		} else {
			$form .= "<input type='hidden' name='options[7]' value='' /><br />\n";
		}
		
	    $form .= categorie_select('options[8]' , $cat_select);
	    
	    $form .= "<br />";
		$form .= _MB_XP2_DAY_SELECT . ':' . _MB_XP2_DAY_BETWEEN . '<select name="options[9]">';
		switch ($day_select){
		case 1:
			$form .= '<option value="0">' . _MB_XP2_NONE . '</option>';
			$form .= '<option value="1" selected>'. _MB_XP2_TODAY . '</option>';
			$form .= '<option value="2">' . _MB_XP2_LATEST . '</option>';
			break;
		case 2:
			$form .= '<option value="0">' . _MB_XP2_NONE . '</option>';
			$form .= '<option value="1">'. _MB_XP2_TODAY . '</option>';
			$form .= '<option value="2" selected>' . _MB_XP2_LATEST . '</option>';
			break;
		default :
			$form .= '<option value="0" selected>' . _MB_XP2_NONE . '</option>';
			$form .= '<option value="1">'. _MB_XP2_TODAY . '</option>';
			$form .= '<option value="2">' . _MB_XP2_LATEST . '</option>';
		}
		$form .= '</select>';
		
		$form .= ' ' . _MB_XP2_DAYS_AND . " <input type='text' size='2' name='options[10]' value='" . $day_size . "' />" . _MB_XP2_DAYS_AGO . "<br />\n";

		return $form;
	}

	
	function _b_content_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
}
?>