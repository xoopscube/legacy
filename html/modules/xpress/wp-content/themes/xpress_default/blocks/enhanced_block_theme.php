<?php
// Block Version: 1.1
function enhanced_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_enhanced_block.html' : trim( $options[1] );
	$include_file = empty( $options[2] ) ? '' : $options[2] ;
	
	$include_path = dirname(__FILE__) . '/my_' . $include_file . '_block.php';

	$file_found = true;
	if (empty($include_file)) {
		$file_found = false;
		$output = __('The include file name to display it is not set.','xpress');
	}
	if (! file_exists($include_path)) {
		$include_path_temp = $include_path;
		$include_path = dirname(dirname(dirname(__FILE__))).'/xpress_default/blocks/my_' . $include_file . '_block.php';
		if (! file_exists($include_path)) {
			$file_found = false;
			$output = sprintf(__('File %s not exist.','xpress'),$include_path_temp);
		}
	}
	if($file_found) {
		ob_start();
			require $include_path;
			$output = ob_get_contents();
		ob_end_clean();
		if (empty($output)) $output = __('Data is Empty','xpress');
	}
	$block['enhanced'] = $output;
	return $block ;	
}
?>