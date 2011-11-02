<?php
// Block Version: 1.0
function archives_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_archives_block.html' : trim( $options[1] );
	$type = empty( $options[2] ) ? 'monthly' : $options[2] ;
	$limit  = !is_numeric( $options[3] ) ? 0 : $options[3] ;
	$show_post_count = empty( $options[4] ) ? false : true ;		
	$drop_down = empty( $options[5] ) ? false : true ;	
	
	switch($type){
		case 'yearly':
			$select_str = __('Select Yearly', 'xpress');
			break;
		case 'monthly':
			$select_str = __('Select Monthly', 'xpress');
			break;
		case 'weekly':
			$select_str = __('Select Weekly', 'xpress');
			break;
		case 'daily':
			$select_str = __('Select Daily', 'xpress');
			break;
		case 'postbypost':
			$select_str = __('Select Post', 'xpress');
			break;
		default:
			$select_str = __('Select Monthly', 'xpress');
	}
	
	if ($drop_down) $format = 'option'; else $format = 'html';
	if ($limit == 0 ) $limit_str = ''; else $limit_str = $limit;
	
	$param = array(
		'type' => $type, 
		'limit' => $limit_str, 
		'format' => $format, 
		'before' => '', 
		'after' => '',
		'show_post_count' => $show_post_count
	);
	
	ob_start();
		wp_get_archives($param);
		$get_archives_output = ob_get_contents();
	ob_end_clean();
	

	if($drop_down){
		

		$output = '<form id="archiveform" action="">';
		$output .= '<select name="archive_chrono" onchange="window.location = (document.forms.archiveform.archive_chrono[document.forms.archiveform.archive_chrono.selectedIndex].value);">';
		$output .= "<option value=''>" . $select_str . "</option>\n";
		$output .=  $get_archives_output;
		$output .= '</select>';
		$output .= '</form>';
		
		$output  = '<select name="archive-dropdown"' . "onChange='document.location.href=this.options[this.selectedIndex].value;'>\n";
  		$output .= '<option value="">'.attribute_escape($select_str) . "</option>\n";
  		$output .=	$get_archives_output;
		$output .=	"</select>\n";
		
	} else {
		$output  = "<ul>\n";
		$output .=  $get_archives_output;
		$output  .= "</ul>\n";
	}
	$block['archive'] = $output;
	return $block ;	
}

?>