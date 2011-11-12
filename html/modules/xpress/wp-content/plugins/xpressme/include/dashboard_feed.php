<?php

// Register Dashboard Widget
add_action('wp_dashboard_setup', 'xpress_register_dashboard_widget');

function xpress_register_dashboard_widget() {
	global $wp_registered_widgets, $wp_registered_widget_controls, $wp_dashboard_control_callbacks;
	global $xpress_config;

	$widget_options = get_option( 'xpress_dashboard_widget_options' );
	if ( !$widget_options || !is_array($widget_options) )
		$widget_options = array();
	
	if ( !isset( $widget_options['xpress_dashboard_primary'] ) ) {
		$update = true;
		$widget_options['xpress_dashboard_primary'] = array(
			'link' => 'http://ja.xpressme.info/blog/' ,
			'url' => 'http://ja.xpressme.info/feed/',
			'title' =>  __( 'XPressME Integration Kit Blog' , 'xpressme') ,
			'items' => 2,
			'show_summary' => 1,
			'show_author' => 0,
			'show_date' => 1
		);
	}

	if ( !isset( $widget_options['xpress_dashboard_secondary'] ) ) {
		$update = true;
		$widget_options['xpress_dashboard_secondary'] = array(
			'link' => 'http://forum.xpressme.info/' ,
			'url' => 'http://forum.xpressme.info/rss.php?topics=1',
			'title' =>  __( 'XPressME Integration Kit Folum' , 'xpressme') ,
			'items' => 5,
			'show_summary' => 0,
			'show_author' => 0,
			'show_date' => 0
		);
	}
	update_option( 'xpress_dashboard_widget_options', $widget_options );
	if ($xpress_config->is_dashboard_blog_disp){
		wp_add_dashboard_widget( 'xpress_dashboard_primary', $widget_options['xpress_dashboard_primary']['title'], 'xpress_dashboard_primary', 'xpress_dashboard_primary_control' );
	}
	if ($xpress_config->is_dashboard_forum_disp){
		wp_add_dashboard_widget( 'xpress_dashboard_secondary', $widget_options['xpress_dashboard_secondary']['title'], 'xpress_dashboard_secondary', 'xpress_dashboard_secondary_control');
	}
}



function xpress_dashboard_primary() {
	xpress_dashboard_rss_output('xpress_dashboard_primary');
}

function xpress_dashboard_primary_control() {
	xpress_dashboard_rss_control( 'xpress_dashboard_primary' );
}

function xpress_dashboard_secondary() {
	xpress_dashboard_rss_output('xpress_dashboard_secondary');
}

function xpress_dashboard_secondary_control() {
	xpress_dashboard_rss_control( 'xpress_dashboard_secondary' );
}

function xpress_dashboard_rss_output( $widget_id ) {
	$widgets = get_option( 'xpress_dashboard_widget_options' );
	echo "<div class='rss-widget'>";
	wp_widget_rss_output( $widgets[$widget_id] );
	echo "</div>";
}

function xpress_dashboard_rss_control( $widget_id, $form_inputs = array() ) {
	if ( !$widget_options = get_option( 'xpress_dashboard_widget_options' ) )
		$widget_options = array();

	if ( !isset($widget_options[$widget_id]) )
		$widget_options[$widget_id] = array();

	$number = 1; // Hack to use wp_widget_rss_form()
	$widget_options[$widget_id]['number'] = $number;

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-rss'][$number]) ) {
		$_POST['widget-rss'][$number] = stripslashes_deep( $_POST['widget-rss'][$number] );
		$widget_options[$widget_id] = wp_widget_rss_process( $_POST['widget-rss'][$number] );
		// title is optional.  If black, fill it if possible
		if ( !$widget_options[$widget_id]['title'] && isset($_POST['widget-rss'][$number]['title']) ) {
			$rss = fetch_feed($widget_options[$widget_id]['url']);
			if ( ! is_wp_error($rss) )
				$widget_options[$widget_id]['title'] = htmlentities(strip_tags($rss->get_title()));
			else
				$widget_options[$widget_id]['title'] = htmlentities(__('Unknown Feed'));
		}
		update_option( 'xpress_dashboard_widget_options', $widget_options );
	}

	wp_widget_rss_form( $widget_options[$widget_id], $form_inputs );
}

?>