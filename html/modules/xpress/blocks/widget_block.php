<?php
if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( '
function b_'.$mydirname.'_widget_show($options){
	return _b_widget_show($options) ;
}
function b_'.$mydirname.'_widget_edit($options){
	return _b_widget_edit($options) ;
}
' ) ;

if( ! defined( 'XPRESS_WIDGET_BLOCK_INCLUDED' ) ) {
	define( 'XPRESS_WIDGET_BLOCK_INCLUDED' , 1 ) ;
	
	function _b_widget_edit($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_widget_block.html' : trim( $options[1] );
		$title_show = empty( $options[2] ) ? false : true ;
		$widget_select = empty( $options[3] ) ? '' : $options[3] ;

		$selected = explode(',' , $widget_select);
		$wp_prefix = preg_replace('/wordpress/','wp',$mydirname) . '_';
		$xoopsDB =& Database::getInstance();
		$myts =& MyTextSanitizer::getInstance();

		$db_xpress_options = $xoopsDB->prefix($wp_prefix . 'options');
		$query = "SELECT option_value FROM $db_xpress_options WHERE option_name = 'sidebars_widgets' LIMIT 1";
		$res =  $xoopsDB->query($query, 0, 0);
	    if ($res !== false){
	 		$row = $xoopsDB->fetchArray($res);
	 		$sidebars_widgets = @unserialize( $row['option_value'] );
	 	}
	 	if ( !isset($sidebars_widgets['array_version']) )
			$sidebars_widgets['array_version'] = 1;
		
		
		require_once(XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/blocks/block_common.php');

		$form  = javascript_check();
		$form .= "MyDirectory <input type='text' name='options[0]' value='" . $mydirname . "' /><br />\n";
		$form .= block_template_setting($mydirname,'options[1]',htmlspecialchars($this_template,ENT_QUOTES));
		$form .= "<br />" . yes_no_radio_option('options[2]', _MB_XP2_WIDGET_TITLE_SHOW , $title_show);
		$form .= "<br />";
		$form .= _MB_XP2_SELECT_WIDGET .":<br />\n";
    	$form .= "<input type='hidden' name='options[3]' value='$widget_select' />\n";

		$select = "&nbsp;&nbsp;&nbsp;<select name='widget_sel' multiple=\"multiple\" onclick=\"WidgetSelect()\">\n";
		$found = false;
		foreach ( (array) $sidebars_widgets as $index => $sidebar ){
			if ( is_array($sidebar) ){
				$sidebar_id = $index;
				foreach ( (array) $sidebar as $i => $name ) {
					$found = true;
					$widget = strtolower($name);
					$widget_str = $sidebar_id . '::' . $widget;
					if (in_array($widget_str, $selected))
						$select .=  "<option value='" .  $widget_str . "' selected='selected'>" . $widget_str;
					else
						$select .=  "<option value='" . $widget_str . "'>" . $widget_str;
					
				}
			}
		}

		$select .=  "</select><br/>\n";
    $select .= '
<script type="text/javascript">
    function WidgetSelect(){
        var idx=new Array();
        var sel=document.forms["blockform"].elements["widget_sel"].options;
        for(var i=0, n=0; i<sel.length; i++){
            if(sel[i].selected){ idx[n++]=sel[i].value; }
        }
        if(idx.length>0){
        	document.forms["blockform"].elements["options[3]"].value = idx;
		}
    }
</script>
';
		
		if ($found){
			$form = $form . $select;
		} else {
			$form = $form . "&nbsp;&nbsp;&nbsp;" . _MB_XP2_NO_WIDGET;
		}
		
		return $form;
	}
	
	function _b_widget_show($options)
	{
		$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
		$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
		$block_function_name = basename( __FILE__ );
		
		require_once $mydirpath.'/include/xpress_block_render.php';
		return xpress_block_render($mydirname,$block_function_name,$options);
	}
}
?>