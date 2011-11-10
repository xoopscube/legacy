<?php

if( ! function_exists( 'yes_no_radio_option' ) ) :
function yes_no_radio_option($option_name,$label,$value,$yes = '',$no= ''){
	if (empty( $yes ))  $yes = _YES ;
	if (empty( $no ))  $no = _NO ;
	$form = $label.' : ';
	if ($value){
		$form .= "<input type='radio' name='". $option_name . "' value='1' checked='checked' />" . $yes. "; " ;
		$form .= "<input type='radio' name='". $option_name . "' value='0' />". $no ;
	}else{
		$form .= "<input type='radio' name='". $option_name . "' value='1' />" . $yes. "; " ;
		$form .= "<input type='radio' name='". $option_name . "' value='0' checked='checked' />". $no ;
	}		
    return $form;
	
}
endif;

if(!function_exists("categorie_select")):
function categorie_select($option_name = '',$value='',$row_num=0 ,$sort_column = 'ID', $sort_order = 'asc')
{
    $mydirpath = dirname(dirname(__FILE__));
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	$wp_prefix = preg_replace('/wordpress/','wp',$mydirname) . '_';
	$xoopsDB =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
    $selected = explode(',' , $value);
	$isAll = (count($selected)==0||empty($selected[0]))?true:false;
    $sort_column = 'cat_'.$sort_column;
    if (empty($row_num)) $size = ''; else $size = 'size="' . $row_num . '"';
	include $mydirpath.'/wp-includes/version.php';
	if ($wp_db_version < 6124) {
		$db_xpress_categories = $xoopsDB->prefix($wp_prefix . 'categories');
		$query = "
	    	SELECT cat_ID, cat_name, category_nicename,category_parent 
	    	FROM $db_xpress_categories 
	    	WHERE cat_ID > 0 
	        ";
		$query .= " ORDER BY $sort_column $sort_order";

    } else {
    	$db_xpress_terms = $xoopsDB->prefix($wp_prefix . 'terms');
    	$db_xpress_term_taxonomy = $xoopsDB->prefix($wp_prefix . 'term_taxonomy');
    	$query = "
			SELECT $db_xpress_terms.term_id as cat_ID , $db_xpress_terms.name as cat_name , $db_xpress_term_taxonomy.taxonomy 
			FROM $db_xpress_terms LEFT JOIN $db_xpress_term_taxonomy ON $db_xpress_terms.term_id = $db_xpress_term_taxonomy.term_id 
			WHERE $db_xpress_term_taxonomy.taxonomy = 'category' 
        ";
		$query .= " ORDER BY $sort_column $sort_order";
    }
	$res =  $xoopsDB->query($query, 0, 0);
	$option = "\t<option value=\"0\" ";
	if ($isAll) $option .= " selected=\"selected\"";
	$option .= ">"._MB_XP2_ALL ."</option>\n";

    if ($res !== false){
 		while($row = $xoopsDB->fetchArray($res)){
            $cat_name = $row['cat_name'];
            $cat_ID = $row['cat_ID'];
            $option .= "\t<option value=\"".$cat_ID."\"";
            if (in_array($cat_ID, $selected))
                $option .= ' selected="selected"';
            $option .= '>';
            $option .= $myts->htmlspecialchars($cat_name);
            $option .= "</option>\n";
        }
    }
    $output = _MB_XP2_CATS_SELECT ."<br />\n";
    $output .= '&nbsp;&nbsp;<select name="categorie" id="cat_sel" '.$size.' multiple="multiple" onclick="CatSelect()">' ."\n";
    $output .= $option;
    $output .= '</select>';
    $output .= 	'&emsp;' .  _MB_XP2_CATS_DIRECT_SELECT . " <input type='text' name='$option_name' id='cat_csv' value='$value' /><br />\n";
    $output .= '
<script type="text/javascript">
    function CatSelect(){
        var idx=new Array();
        var sel = document.getElementById("cat_sel").options;
        for(var i=0, n=0; i<sel.length; i++){
            if(sel[i].selected){ idx[n++]=sel[i].value; }
        }
        if(idx.length>0){
        	document.getElementById("cat_csv").value = idx;
		}
    }
</script>
';
    
    return $output;

}
endif;
if(!function_exists("blog_select")):
function blog_select($option_name = '',$value='',$exclusion=false ,$row_num=0)
{
    $mydirpath = dirname(dirname(__FILE__));
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	$wp_prefix = preg_replace('/wordpress/','wp',$mydirname) . '_';
	$xoopsDB =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
    $selected = explode(',' , $value);
	$isAll = (count($selected)==0||empty($selected[0]))?true:false;
    
    if (empty($row_num)) $size = ''; else $size = 'size="' . $row_num . '"';
	include $mydirpath.'/wp-includes/version.php';
	
	$option = "\t<option value=\"0\" ";
	if ($isAll) $option .= " selected=\"selected\"";
	if ($exclusion){
		$option .= ">"._MB_XP2_NONE ."</option>\n";
    } else {
	 	$option .= ">"._MB_XP2_ALL ."</option>\n";
	}

	if ($wp_db_version > 6124) {
		$db_xpress_blogs = $xoopsDB->prefix($wp_prefix . 'blogs');
		$query = "
	    	SELECT blog_id 
	    	FROM $db_xpress_blogs 
	    	ORDER BY blog_id
	        ";
		
	    if ($res =  $xoopsDB->query($query, 0, 0)){
	 		while($row = $xoopsDB->fetchArray($res)){
	 			
	            $blog_id = $row['blog_id'];
	            if ($blog_id == 1) {
	            	$blog_selector = '';
	            } else {
	            	$blog_selector = $blog_id . '_';
	            }
		 		$db_xpress_options = $xoopsDB->prefix($wp_prefix . $blog_selector . 'options');
	            $options_query = "
	    			SELECT option_value 
	    			FROM $db_xpress_options 
	    			WHERE option_name = 'blogname'
	        		";
	    		if ($options_res =  $xoopsDB->query($options_query, 0, 0)){
	    			$options_row = $xoopsDB->fetchArray($options_res);
	    			$blog_name = $options_row['option_value'];
	    		} else {
	    			$blog_name = 'Blog_' . $blog_id ;
	    		}
	    		
	            $option .= "\t<option value=\"".$blog_id."\"";
	            if (in_array($blog_id, $selected))
				$option .= ' selected="selected"';
				$option .= '>';
				$option .= $myts->htmlspecialchars($blog_name);
				$option .= "</option>\n";
	        }
	    }
    }
	if ($exclusion){
	 	$output = _MB_XP2_EXCLUSION_BLOGS_SELECT ."<br />\n";
    } else {
		$output = _MB_XP2_SHOW_BLOGS_SELECT ."<br />\n";
	}
    $output .= '&nbsp;&nbsp;<select name="blogs" id="blog_sel" '.$size.' multiple="multiple" onclick="BlogSelect()">' ."\n";
    $output .= $option;
    $output .= '</select>';
    $output .= 	'&emsp;' .  _MB_XP2_BLOGS_DIRECT_SELECT . " <input type='text' name='$option_name' id='blog_csv' value='$value' /><br />\n";
    $output .= '
<script type="text/javascript">
    function BlogSelect(){
        var idx=new Array();
        var sel = document.getElementById("blog_sel").options;
        for(var i=0, n=0; i<sel.length; i++){
            if(sel[i].selected){ idx[n++]=sel[i].value; }
        }
        if(idx.length>0){
        	document.getElementById("blog_csv").value = idx;
		}
    }
</script>
';
    
    return $output;

}
endif;

if(!function_exists("comment_type_select")):
function comment_type_select($option_name = '',$value='')
{
    $selected = explode(',' , $value);
	$isAll = (count($selected)==0||empty($selected[0]))?true:false;

    $option = "<option value=\"0\" ";
    if ($isAll) $option .= " selected=\"selected\"";
    $option .= ">"._MB_XP2_ALL ."</option>";

    $option .= "<option value=\"1\" ";
    if (in_array(1, $selected))
		$option .= " selected=\"selected\"";
    $option .= ">"._MB_XP2_COMMENT ."</option>";

    $option .= "<option value=\"2\" ";
    if (in_array(2, $selected))
		$option .= " selected=\"selected\"";
    $option .= ">"._MB_XP2_TRUCKBACK ."</option>";

    $option .= "<option value=\"3\" ";
    if (in_array(3, $selected))
		$option .= " selected=\"selected\"";
    $option .= ">"._MB_XP2_PINGBACK ."</option>";

    $output = _MB_XP2_COM_TYPE . "<br />\n";
    $output .= 	"<input type='hidden' name='$option_name' id='com_hidden' value='$value' />\n";
    $output .= '&nbsp;&nbsp;<select name="com_type" id="com_type" multiple="multiple" onclick="ComTypeSelect()">' ."\n";
    $output .= $option;
    $output .= '</select><br />';
    $output .= '
<script type="text/javascript">
    function ComTypeSelect(){
        var idx=new Array();
        var sel=document.getElementById("com_type").options;
        for(var i=0, n=0; i<sel.length; i++){
            if(sel[i].selected){ idx[n++]=sel[i].value; }
        }
        if(idx.length>0){
	       	document.getElementById("com_hidden").value = idx;
		}
    }
</script>
';
    
    return $output;

}
endif;

if(!function_exists("block_template_setting")):
function block_template_setting($mydirname,$option_name = '',$value='')
{
	$temp_parm = explode(':' , $value);
	if (empty($temp_parm[1])) {
		$filename=$temp_parm[0];
		$temp_type = 'db';
	} else  {
		$filename=$temp_parm[1];
		$temp_type = $temp_parm[0];
	}

	$none_prefix_filename = '';
	$pattern = '^' . $mydirname . '_(.*).html';
	if (preg_match('/' . $pattern . '/' , $filename, $match)){ // file prefix check
		$none_prefix_filename = $match[1];
	}
	
	$output = _MB_XP2_THISTEMPLATE . "\n";
	$output .= 	'<input type="hidden" size="50" name="' . $option_name . '" id="template_hide" value="' . $value .'"/>' . "\n";
    $output .= '&nbsp;<select name="template_type" id="template_type" onclick="Template_Make()">' ."\n";
	switch ($temp_type){
		case 'db':
		case 'DB':
			$output .=  '<option value="0" selected="selected">db</option>';
			$output .=  '<option value="1">file</option>';
			break;
		default:
			$output .=  '<option value="0">db</option>';
			$output .=  '<option value="1" selected="selected">file</option>';
	}
	$output .= '</select>';
	$output .= '<b>:'.$mydirname . '_</b>';
	$output .= '<input type="text" size="30" name="none_prefix_file" id="none_prefix_file" value="'. $none_prefix_filename. '"  onChange="Template_Make()"/><b>.html</b><br />';
    $output .= '
<script type="text/javascript">
	function Template_Make(){
		var type_element = document.getElementById("template_type");
		var name_element = document.getElementById("none_prefix_file");
		var real_element = document.getElementById("template_hide");

		var file_name = "' . $mydirname . '_" + name_element.value + ".html";
		if (type_element.value ==0) var tmp_type = "db:"; else var tmp_type = "file:";
		real_element.value = tmp_type + file_name;
	}
</script>
';
    
    return $output;

}
endif;
if(!function_exists("javascript_check")):
function javascript_check()
{
	$out  = '<div id="JSNG"><p style="color: red"><b>' . _MB_XP2_NO_JSCRIPT . '</b></p><br /></div>';
	$out .= '<script>  document.getElementById("JSNG").style.display = "none";</script>';
	return $out;
}
endif;

?>