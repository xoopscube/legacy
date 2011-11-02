<?php
// Block Version: 1.0
function sidebar_block($options)
{
	$templates = array();
	$templates[] = "sidebar.php";
	$sidebar_path = locate_template($templates, false);
	if (empty($sidebar_path)){ // ktai_style plugin is empty
		$sidebar_path = dirname(dirname(__FILE__)) . "/sidebar.php";
	}
	ob_start();
		require($sidebar_path);
		$output = ob_get_contents();
	ob_end_clean();
	$pattern = '<div\s+id\s*=\s*["\']xpress_sidebar["\']\s*>';
	$output = preg_replace("/".$pattern."/s" , '<div class="xpress_sidebar_block">' , $output);

	$block['sidebar'] = $output;								
	return $block ;	

}

?>