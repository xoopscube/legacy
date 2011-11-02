<?php
// Block Version: 1.0
function meta_block($options)
{
	$mydirname = empty( $options[0] ) ? 'xpress' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_meta.html' : trim( $options[1] );
	$wp_link = empty( $options[2] ) ? false : true ;
	$xoops_link = empty( $options[3] ) ? false : true ;
	$post_rss = empty( $options[4] ) ? false : true ;
	$comment_rss = empty( $options[5] ) ? false : true ;
	$post_new = empty( $options[6] ) ? false : true ;
	$admin_edit = empty( $options[7] ) ? false : true ;
	$readme = empty( $options[8] ) ? false : true ;
	$ch_style = empty( $options[9] ) ? false : true ;
	
	$output ="<ul>\n";
	
	if ($wp_link){
		$output .= '<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform." target="_blank">WordPress</a></li>';
	}
	if ($xoops_link){
		$output .= '<li><a href="http://xoopscube.sourceforge.net/" title="Powered by XOOPS Cube, state-of-the-art Content Management Portal." target="_blank">XOOPS cube</a></li>';
	}
	if ($post_rss){	
		$output .= '<li><a href="'.get_bloginfo('rss2_url').'" title="'. __('Posts RSS', 'xpress') .'">'. __('Posts RSS', 'xpress') .'</a></li>';
	}
	if ($comment_rss){	
		$output .= '<li><a href="'.get_bloginfo('comments_rss2_url').'" title="'. __('Comments RSS', 'xpress') .'">'. __('Comments RSS', 'xpress') .'</a></li>';
	}

	if (is_user_logged_in()) { 
		global $current_user;

		$Now_user_level = $current_user->user_level;
		
		if ($post_new){
			if($Now_user_level > 0){
				if (xpress_is_wp_version('<','2.1') ){
					$output .=
					'<li>'.
					'<a href="'.get_settings('siteurl').'/wp-admin/post.php" title="'. __('Add New', 'xpress') .'">'. __('Add New', 'xpress') .'</a>'.
					'</li>';
				} else {
					$output .=
					'<li>'.
					'<a href="'.get_settings('siteurl').'/wp-admin/post-new.php" title="'. __('Add New', 'xpress') .'">'. __('Add New', 'xpress') .'</a>'.
					'</li>';
				}
			}
		}
		if ($admin_edit){
			if($Now_user_level > 7){
				$output .=	
				'<li>'.
				'<a href="'.get_settings('siteurl').'/wp-admin/" title="'. __('Site Admin', 'xpress') .'">'. __('Site Admin', 'xpress') .'</a>'.
				'</li>';
			}
		}
		$output .=
				'<li>'.
				'<a href="'.get_settings('siteurl').'/wp-admin/profile.php" title="' . __('User Profile', 'xpress') .'">'. __('User Profile', 'xpress') .'</a>'.
				'</li>';				
		

		if (defined('S2VERSION')){
			if($Now_user_level > 7){
					$output .=
					'<li>'.
					'<a href="'.get_settings('siteurl').'/wp-admin/users.php?page=subscribe2/subscribe2.php" title="'. __('Subscription management', 'xpress') .'">'. __('Subscription management', 'xpress') .'</a>'.
					'</li>';
			} else {
					$output .=
					'<li>'.
					'<a href="'.get_settings('siteurl').'/wp-admin/profile.php?page=subscribe2/subscribe2.php" title="'. __('Subscription management', 'xpress') .'">'. __('Subscription management', 'xpress') .'</a>'.
					'</li>';
			}
		}
			
	}
	
	if ($readme){
		$output .='<li>'.'<a href="'.get_settings('siteurl').'/readme.html" title="' .  __('ReadMe', 'xpress') . '">' . __('ReadMe', 'xpress') . '</a>'.'</li>';
	}
	$output .= disp_mode_set();
	if (function_exists('wp_theme_switcher') ) {
		ob_start();
			echo '<li>' . __('Themes') . ':';
			wp_theme_switcher('dropdown');
			echo '</li>';
			$output .= ob_get_contents();
		ob_end_clean();
	}
	$output .= '</ul>';
	$block['meta_info'] = $output;
	return $block ;	
}
?>