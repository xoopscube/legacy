<?php
// Block Version: 1.0
function tag_cloud_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_meta.html' : trim( $options[1]);
	$smallest = !is_numeric( $options[2] ) ? 8 : $options[2] ;
	$largest = !is_numeric( $options[3] ) ? 22 : $options[3] ;
	$unit = empty( $options[4] ) ? 'pt' : $options[4] ;
	$number = !is_numeric( $options[5] ) ? 45 : $options[5] ;
	$format = empty( $options[6] ) ? 'flat' : $options[6] ;
	$orderby = empty( $options[7] ) ? 'name' : $options[7] ;
	$order = empty( $options[8] ) ? 'ASC' : $options[8] ;
	$exclude = is_null( $options[9] ) ? '' : $options[9] ;
	$wp_include = is_null( $options[10] ) ? '' : $options[10] ;
	
	$param=array(
		'smallest' => $smallest,
		'largest' => $largest,
		'unit' => $unit,
		'number' => $number,
		'format' => $format,
		'orderby' => $orderby,
		'order' => $order,
		'exclude' => $exclude,
		'include' => $wp_include
	);
	if (function_exists('wp_tag_cloud')) {
		ob_start();
			wp_tag_cloud($param);
			$output = ob_get_contents();
		ob_end_clean();	
	} else {
		$output = 'not function wp_tag_cloud()';
	}
	$block['tag_cloud'] = $output;								
	return $block ;	
}
?>