<?php
/**
 * The Footer widget areas.
 *
 */
	$side_bar_num = get_option('xpress_footer_sidebars_count');
	if (empty($side_bar_num)) return;
//	echo "<div id=\"footer-widget-area\" role=\"complementary\">\n";
	echo "<div id=\"footer-widget-area\">\n";
	for($i=1;$i <= $side_bar_num;$i++){
		$sidebar_id = 'footer-widget-area-'.$i;
		echo "\t<div class=\"widget-area-type{$side_bar_num}\">\n";
		echo "\t\t<div id=\"{$sidebar_id}\" class=\"widget-area\">\n";
		echo "\t\t\t<ul class=\"xoxo\">\n";
		dynamic_sidebar( $sidebar_id );
		echo "\t\t\t\n</ul>\n";
		echo "\t\t</div><!-- #{$sidebar_id} -->\n";
		echo "\t</div><!-- .widget-area-type{$side_bar_num} -->\n";
	}
	echo "</div><!-- #footer-widget-area -->\n";
?>
