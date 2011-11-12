<?php
/*
 * XPressME Menu Widget 
 * XPressME MENU widget using the the WordPress 2.8 widget API. This is meant strictly as a means of showing the new API using the <a href="http://jessealtman.com/2009/06/08/tutorial-wordpress-28-widget-api/">tutorial</a>.
 */
class XPress_Menu_Widget extends WP_Widget
{
	/**
	* Declares the XPress_Menu_Widget class.
	*
	*/
	function XPress_Menu_Widget(){
		$widget_ops = array('classname' => 'widget_xpress', 'description' => __( "XPressME User Menu Widget") );
		$control_ops = array('width' => 600, 'height' => 300);
		$this->WP_Widget('XPress_Menu', __('XPressME MENU'), $widget_ops, $control_ops);
	}

	/**
	* Displays the Widget
	*
	*/
	function widget($args, $instance){
		global $xpress_config,$xoops_config;
		global $current_user;

		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);

		# Before the widget
		echo $before_widget;

		# The title
		if ( $title )
		echo $before_title . $title . $after_title;

		# Make the XPressME MENU widget
		$menu = array();
		for($i = 0; $i < 10; $i++) {
			$menu[$i]['Type'] = $instance['Type_' . $i];
			$menu[$i]['Title'] = $instance['Title_' . $i];
			$menu[$i]['URL'] = $instance['URL_' . $i];
			$menu[$i]['Visible'] = $instance['Visible_' . $i];
			$menu[$i]['Weight'] = $instance['Weight_' . $i];
		}			
		echo '<ul>';
		for($i = 0; $i < 10; $i++) {
			$type = $menu[$i]['Type'];
			if ($menu[$i]['Visible'] && !empty($menu[$i]['Title']) ){
				switch($type){
					case 0:
					case 1:
						echo '<li><a href="' . $menu[$i]['URL'] . '">' . $menu[$i]['Title'] . '</a></li>';
						break;
					case 2:	// Add New
						if (is_user_logged_in()){
							if ($current_user->user_level > 0){
								if (xpress_is_wp_version('<','2.1') ){
									echo '<li><a href="'.get_settings('siteurl').'/wp-admin/post.php" title="'. $menu[$i]['Title'] .'">'. $menu[$i]['Title'] .'</a></li>';
								} else {
									echo '<li><a href="'.get_settings('siteurl').'/wp-admin/post-new.php" title="'. $menu[$i]['Title'] .'">'. $menu[$i]['Title'] .'</a></li>';
								}
							}
						}
						break;
					case 3: // User Profile
						if (is_user_logged_in()) { 
							echo '<li><a href="'.get_settings('siteurl').'/wp-admin/profile.php" title="' . $menu[$i]['Title'] .'">'. $menu[$i]['Title'] .'</a></li>';
						}
						break;
					case 4: // WordPress Admin
						if (is_user_logged_in()){
							if ($current_user->user_level > 7){
								echo '<li><a href="'.get_settings('siteurl').'/wp-admin/" title="'. $menu[$i]['Title'] .'">'. $menu[$i]['Title'] .'</a></li>';
							}
						}
						break;
					case 5: // Module Admin
						if($GLOBALS["xoopsUserIsAdmin"]){
							echo '<li><a href="'.get_settings('siteurl').'/admin/index.php"  title="'. $menu[$i]['Title'] .'">'. $menu[$i]['Title'] .'</a></li>';
						}
						break;
					case 6: // XPressME Setting
						if (is_user_logged_in()){
							if ($current_user->user_level > 7){
								echo '<li><a href="'.get_settings('siteurl').'/wp-admin/admin.php?page=xpressme\\xpressme.php" title="'. $menu[$i]['Title'] .'">'. $menu[$i]['Title'] .'</a></li>';
							}
						}
						break;
					case 7: // Display Mode Select
						if ($xpress_config->viewer_type == 'user_select'){
							echo disp_mode_set();
						}
						break;
					default:
				}
			}
		}

		echo '</ul>';
		# After the widget
		echo $after_widget;
	}

	/**
	* Saves the widgets settings.
	*
	*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		
		for($i = 0; $i < 10; $i++) {
			$instance['Type_'. $i] = strip_tags(stripslashes($new_instance['Type_'. $i]));
			$instance['Title_' . $i] = strip_tags(stripslashes($new_instance['Title_'. $i]));
			if ($instance['Type_'. $i] < 2){
				$instance['URL_' . $i] = strip_tags(stripslashes($new_instance['URL_'. $i]));
			} else {
				$instance['URL_' . $i] = '';
			}
			$instance['Visible_' . $i] = strip_tags(stripslashes($new_instance['Visible_'. $i]));
		}

		return $instance;
	}

	/**
	* Creates the edit form for the widget.
	*
	*/
	function form($instance){
		global $xpress_config,$xoops_config;
		
		if (xpress_is_wp_version('<','2.1') ){
			$addnew = get_settings('siteurl').'/wp-admin/post.php';
		} else {
			$addnew = get_settings('siteurl').'/wp-admin/post-new.php';
		}
		$type = array();
		$type[0] = __('Link', 'xpressme');
		$type[1] = __('Site Home', 'xpressme');
		$type[2] = __('Add New', 'xpressme');
		$type[3] = __('User Profile', 'xpressme');
		$type[4] = __('WordPress Admin', 'xpressme');
		$type[5] = __('Module Admin', 'xpressme');
		$type[6] = __('XPressME Setting', 'xpressme');
		$type[7] = __('Display Mode Select', 'xpressme');
		
		$auto_setting = __('Auto Setting', 'xpressme');
		
		//Defaults
		$instance = wp_parse_args( (array) $instance, 
		array(
			'title'=> __('User Menu', 'xpressme'),
			
			'Type_0' =>1 , 
			'Title_0' => __('Site Home', 'xpressme'),
			'URL_0' => get_xoops_url(),
			'Visible_0' => 1,
			
			'Type_1' =>2 , 
			'Title_1' => __('Add New', 'xpressme'),
			'URL_1' => $auto_setting,
			'Visible_1' => 1,
				
			'Type_2' =>3 , 
			'Title_2' => __('User Profile', 'xpressme'),
			'URL_2' => __('Auto Setting', 'xpressme'),
			'Visible_2' => 1,
				
			'Weight_2' => 3,
			'Type_3' =>4 , 
			'Title_3' => __('WordPress Admin', 'xpressme'),
			'URL_3' => $auto_setting,
			'Visible_3' => 1,
				
			'Type_4' =>5 , 
			'Title_4' => __('Module Admin', 'xpressme'),
			'URL_4' => $auto_setting,
			'Visible_4' => 1,
				
			'Type_5' =>6 , 
			'Title_5' => __('XPressME Setting', 'xpressme'),
			'URL_5' => $auto_setting,
			'Visible_5' => 1,
				
			'Type_6' =>7 , 
			'Title_6' => $auto_setting,
			'URL_6' => $auto_setting,
			'Visible_6' => 1,
			'Type_7' =>0 , 
			'Title_7' => __('Link', 'xpressme'),
			'URL_7' => '',
			'Visible_7' => 0,
				
			'Type_8' =>0 , 
			'Title_8' => __('Link', 'xpressme'),
			'URL_8' => '',
			'Visible_8' => 0,
				
			'Type_9' =>0 , 
			'Title_9' => __('Link', 'xpressme'),
			'URL_9' => '',
			'Visible_9' => 0,
		) );
		
		echo '
		<script type="text/javascript">
		    function TypeSelect(type_id,title_id,url_id){
				var type=document.getElementById(type_id);
				var title=document.getElementById(title_id);
				var link_url=document.getElementById(url_id);
				var auto_set = \''. $auto_setting .'\';
				title.value = type[type.value].text;
				if(type.value > 1){
					link_url.value = auto_set;
					link_url.disabled = true;
					link_url.style.backgroundColor = \'transparent\';
		        } else {
		        	if (link_url.value == auto_set) link_url.value = \'\';
					link_url.disabled = false;
					link_url.style.backgroundColor = \'#FFFFEE\';
				}
		        if(type.value == 1){
					link_url.value = \''.get_xoops_url() . '\';
		        }
				if(type.value == 7){
					title.value = auto_set;
					title.disabled = true;
					title.style.backgroundColor = \'transparent\';
		        } else {
		        	if (title.value == auto_set) title.value = \'\';
					title.disabled = false;
					title.style.backgroundColor = \'#FFFFEE\';
				}

		    }
		</script>';

		// Output the options
		echo '<p><label for="' . $this->get_field_name('title') . '">'. "\n";
		echo __('Title:') . '<input style="width: 200px;" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $instance['title'] . '" /></label></p>'. "\n";
		echo "
		    <table width='100%' class='outer' cellpadding='4' cellspacing='1' border=\"1\" bordercolor=\"#888888\">
		    <tr valign='middle' align='center' style=\"background-color:#2E323B;color:#FFFFFF\">
		    <th width='10%'>Type</th>
		    <th width='15%'>Title</th>
		    <th width='10%'>URL</th>
		    <th width='10px'>Visible</th>
			</tr>
		";
		for($i = 0; $i < 10; $i++) {
			$even = $i % 2;
			if ($even) {
				$back_color = ' style="background-color:#E3E3E3"';
			} else {
				$back_color = ' style="background-color:#F5F5F5"';
			}
			$text_back_color = ' style="background-color:#FFFFEE"';
			echo "<tr $back_color>";

			$select_arg = "'" . $this->get_field_id('Type_' . $i) . "','" . $this->get_field_id('Title_' . $i) . "','" . $this->get_field_id('URL_' . $i) . "'";
			echo '<th><select id="' . $this->get_field_id('Type_' . $i) . '" name="' . $this->get_field_name('Type_' . $i) . '" ' .$back_color . 'onchange="TypeSelect(' . $select_arg . ')">';
			for ($j = 0; $j < 8; $j++) {
				if ($instance['Type_'. $i] == $j) $select = ' selected="selected"'; else $select = '';
				echo '<option ' . $select . 'value="'. $j . '">' . $type[$j] . '</option>';
			}
			echo '</select></th>';
			
			if ($instance['Type_'. $i] == 7) {
				$title_disible = 'disabled=disabled';
				$title_back_color = $back_color;
				$title_value = $auto_setting;

			} else {
				$title_disible = '';
				$title_back_color = $text_back_color;
				$title_value = $instance['Title_'. $i];
			}
			echo '<th style="padding:2px"><input size="24" id="' . $this->get_field_id('Title_' . $i) . '" name="' . $this->get_field_name('Title_' . $i) . '" type="text" value="' . $title_value . '" ' .$title_back_color . $title_disible .  '/></th>'. "\n";
			if ($instance['Type_'. $i] > 1) {
				$url_disible = 'disabled=disabled';
				$url_back_color = $back_color;
				$url_value = $auto_setting;
			} else {
				$url_disible = '';
				$url_back_color = $text_back_color;
				$url_value = $instance['URL_'. $i];
			}
			echo '<th style="padding:2px"><input size="40" id="' . $this->get_field_id('URL_' . $i) . '" name="' . $this->get_field_name('URL_' . $i) . '" type="text" value="' . $url_value . '" ' .$url_back_color . $url_disible . '/></th>'. "\n";
			if ($instance['Visible_'. $i]) $check = ' checked="checked"'; else $check = '';
			echo '<th><input size="4" id="' . $this->get_field_id('Visible_' . $i) . '" name="' . $this->get_field_name('Visible_' . $i) . '" type="checkbox" value="1"' . $check . ' /></th>'. "\n";
			echo '</tr>';
		}
		echo 	'</table>';
	}

}// END class

/**
* Register Hello World widget.
*
* Calls 'widgets_init' action after the Hello World widget has been registered.
*/
function XPress_MenuInit() {
	register_widget('XPress_Menu_Widget');
}
add_action('widgets_init', 'XPress_MenuInit');
?>
