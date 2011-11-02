<?php
// Block Version: 1.1
function widget_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_widget.html' : trim( $options[1] );
	$title_show = empty( $options[2] ) ? false : true ;
	$widget_select = empty( $options[3] ) ? '' : $options[3] ;

	$selected = explode(',' , $widget_select);

	$output = '';
	foreach($selected as $select){
		$ex = explode('::',$select);
		$sidebar_id = $ex[0];
		$widget_id = $ex[1] ;
	
		ob_start();
			render_widget($sidebar_id,$widget_id);
			$output .= ob_get_contents();
		ob_end_clean();	
	}
	if (count($selected) > 1){
		$output = "<ul>\n" . $output . "\n</ul>\n";
	} else {
		if(!$title_show){
			$del_pattern = '<[^>]*class\s*=\s*[\'|"]widgettitle[\'|"]\s*>[^<]*<\/[^>]*>';
			$output = preg_replace('/' . $del_pattern . '/', '', $output);
		}
		if (preg_match('/^<li[^>]*>.*<\/li>$/s',$output)){
			$output = preg_replace('/^<li[^>]*>/', '', $output);
			$output = preg_replace('/<\/li>$/', '', $output);
		}
	}
	$block['widget'] = $output;
	if (empty($output)) $block['err_message'] = 'Selected Widget is not active. ';
	return $block ;	
}

function render_widget($index = 1, $widget_id) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	if ( is_int($index) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title($index);
		foreach ( (array) $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title($value['name']) == $index ) {
				$index = $key;
				break;
			}
		}
	}
	if (!function_exists('wp_get_sidebars_widgets')) {
		echo 'Not support sidebar widget';
		return;
	}
	$sidebars_widgets = wp_get_sidebars_widgets();
	$registered_sidebars = $wp_registered_sidebars[$index];
	$key_exists = array_key_exists($index, $sidebars_widgets);
	$is_array = is_array($sidebars_widgets[$index]);
	

	
	if ( empty($registered_sidebars) || !$key_exists || !$is_array || empty($sidebars_widgets[$index]) )
		return false;

	$sidebar = $wp_registered_sidebars[$index];

	$did_one = false;
	foreach ( (array) $sidebars_widgets[$index] as $id ) {
		if ($id != $widget_id) continue;
		$params = array_merge(
			array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
			(array) $wp_registered_widgets[$id]['params']
		);

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		}
		$classname_ = ltrim($classname_, '_');
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

		$params = apply_filters( 'dynamic_sidebar_params', $params );

		$callback = $wp_registered_widgets[$id]['callback'];

		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	return $did_one;
}
?>