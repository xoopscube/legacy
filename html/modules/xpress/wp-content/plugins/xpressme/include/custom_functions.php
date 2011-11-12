<?php
function xpress_user_access_level(){
	global $current_user;
	
	$level = @$current_user->user_level;
	$role = @$current_user->roles[0];
	switch ($role){
		case 'administrator':
			$role_level = 10;
			break;
		case 'editor':
			$role_level = 7;
			break;
		case 'author':
			$role_level = 2;
			break;		
		case 'contributor':
			$role_level = 1;
			break;
		default:
			$role_level = 0;
	}
	
	if ($level > $role_level){
		return $level;
	} else {
		return $role_level;
	}
}

function xpress_is_contributor()
{
	global $current_user;
	get_currentuserinfo();
	if (xpress_user_access_level() > 3)
		return true;
	else
		return false;
}

function xpress_is_multiblog() {
	global $xoops_config;
	
	if (function_exists('is_multisite') && is_multisite()) return true;
	return false;
}

function xpress_is_multiblog_root() {
	global $blog_id;
	if ( xpress_is_multiblog() && $blog_id == BLOG_ID_CURRENT_SITE){
		return true;
	} else {
		return false;
	}
}

function xpress_is_wp_version($operator='==',$comp_version){
	global $xoops_config;
 	return version_compare($xoops_config->wp_version, $comp_version, $operator);
}

function xpress_is_theme_sidebar_disp(){
	global $xpress_config;
	if (is_wordpress_style()) return true;
	return $xpress_config->is_theme_sidebar_disp;
}	

function xpress_is_author_view_count(){
	global $xpress_config;
	return $xpress_config->is_author_view_count;
}

function xpress_is_multi_user(){
	global $xpress_config;
	return $xpress_config->is_multi_user;
}


function xpress_the_title($args = '')
{
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	$output = '<div class ="xpress-post-header">' . "\n";
	
	if (function_exists('hotDates')) {
		ob_start();
			hotDates();
			$output .= ob_get_contents();
		ob_end_clean();
	}
	$output .= '<div class ="xpress-post-title">' . "\n";
	$output .= '<h2><a href="';
	ob_start();
		the_permalink();
		$output .= ob_get_contents();
	ob_end_clean();
	
	if(function_exists('the_title_attribute')){
		$title = the_title_attribute('echo=0');	
	} else {
		ob_start();
			the_title();
			$title = ob_get_contents();
		ob_end_clean();
	}
						
	$output .= '" rel="bookmark" title="';
	$output .= sprintf(__('Permanent Link to %s', 'xpress'), $title);
	$output .= '">';
	$output .= $title;
	$output .= '</a></h2>' . "\n";
	$output .= '</div>' . "\n";
	$output .= '</div>' . "\n";
	
	if ($echo)
		echo $output;
	else
		return $output;

}

function xpress_selected_author($args ='' ) {
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	$output = '';
  	$author_cookie = 'select_' . get_xpress_dir_name() . "_author" ;
  	if (!empty($_COOKIE[$author_cookie])){
  		$uid = intval($_COOKIE[$author_cookie]);
  		$user_info = get_userdata($uid);
  		$output = $user_info->display_name;
  	}
	if ($echo)
		echo $output;
	else
		return $output;
		
}
function xpress_selected_author_id($args ='' ) {
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	$output = '';
  	$author_cookie = 'select_' . get_xpress_dir_name() . "_author" ;
  	if (!empty($_COOKIE[$author_cookie])){
  		$output = intval($_COOKIE[$author_cookie]);
  	} else {
  		$output = '';
  	}
	if ($echo)
		echo $output;
	else
		return $output;		
}

function xpress_now_user_level($args ='' ) {
	global $current_user;
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	
	$output = xpress_user_access_level();
	if ($echo)
		echo $output;
	else
		return $output;
}
	
function xpress_credit($args ='')
{
	global $wp_version , $xoops_config;
	if ($xoops_config->is_wpmu) {
		global $wpmu_version;
	}
	
	$defaults = array(
		'echo' => 1,
		'no_link' => 0
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	
	$xpress_version = $xoops_config->module_version;
	$xpress_codename = $xoops_config->module_codename;
	if ($no_link){
		if ($xoops_config->is_wpmu) {
			$output = 'XPressMU Ver.' . sprintf('%.2f %s',$xpress_version,$xpress_codename);
			$output .= '(included WordPress MU ' . $wpmu_version. ')';
		} else {
			$output = 'XPressME Ver.' . sprintf('%.2f %s',$xpress_version,$xpress_codename);
			if (strstr($wp_version,'ME')){
				$output .= '(included WordPress ' . $wp_version . ')';
			} else {
				$output .= '(included WordPress ' . $wp_version . ')';
			}
		}
	} else {
		if ($xoops_config->is_wpmu) {
			$output = '<a href="http://ja.xpressme.info"'. " target='_blank'" . '>XPressMU Ver.' . sprintf('%.2f %s',$xpress_version,$xpress_codename) .'</a>';
			$output .= '(included <a href="http://mu.wordpress.org/" title="Powered by WordPress"'." target='_blank'". '>WordPress MU ' . $wpmu_version . '</a>)';
		} else {
			$output = '<a href="http://ja.xpressme.info"'. " target='_blank'" . '>XPressME Ver.' . sprintf('%.2f %s',$xpress_version,$xpress_codename) .'</a>';
			if (strstr($wp_version,'ME')){
				$output .= '(included <a href="http://wpme.sourceforge.jp/" title="Powered by WordPress"'." target='_blank'". '>WordPress ' . $wp_version . '</a>)';
			} else {
				$output .= '(included <a href="http://wordpress.org/" title="Powered by WordPress"'." target='_blank'". '>WordPress ' . $wp_version . '</a>)';
			}
		}
	}		
	if ($echo)
		echo $output;
	else
		return $output;
}

function xpress_convert_time($args ='')
{
	$defaults = array(
		'echo' => 1,
		'format' => '(%.3f sec.)'		
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	
	$output =  sprintf($format,timer_stop(0));
	if ($echo)
		echo $output;
	else
		return $output;
}

function xpress_left_arrow_post_link($args ='')
{
	global $xpress_config;
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	
	$ret = '';
		
	$link_str = '&laquo; %link';

	if($xpress_config->is_left_postnavi_old){
		$link_title = $xpress_config->old_post_link_text;
		ob_start();
		if ($xpress_config->is_postnavi_title_disp)
			previous_post_link($link_str);
		else 
			previous_post_link($link_str,$link_title);
		$ret = ob_get_contents();
		ob_end_clean();
		ob_start();
			previous_post_link('%link',$link_title);
			$GLOBALS['left_arrow_post_link'] = ob_get_contents();
		ob_end_clean();

	} else {
		$link_title = $xpress_config->newer_post_link_text;
		ob_start();
		if ($xpress_config->is_postnavi_title_disp)
			next_post_link($link_str);
		else
			next_post_link($link_str,$link_title);
		$ret = ob_get_contents();
		ob_end_clean();
		ob_start();
			next_post_link('%link',$link_title);
			$GLOBALS['left_arrow_post_link'] = ob_get_contents();
		ob_end_clean();

	}
	
	if ($xpress_config->is_postnavi_title_disp){
		$on_mouse_show = $link_title;
	} else  {
		if($xpress_config->is_left_postnavi_old){
			ob_start();
				previous_post_link('%link');
				$on_mouse_show = ob_get_contents();
			ob_end_clean();
		} else {
			ob_start();
				next_post_link('%link');
				$on_mouse_show = ob_get_contents();
			ob_end_clean();
		}
		$pattern = "<a[^>]*?>(.*)<\/a>";
		preg_match("/".$pattern."/s",  $on_mouse_show, $body_matches);
		$on_mouse_show = $body_matches[1];
	}
	$output = str_replace('">','" title="'.$on_mouse_show . '">' , $ret);

	if (icon_exists($xpress_config->post_left_arrow_image_link)){
		$img_link = str_replace($link_title,"<img src=\"$xpress_config->post_left_arrow_image_link\" alt=\"\" style=\"vertical-align:middle\"/>",$GLOBALS['left_arrow_post_link']);
		$img_link = str_replace('rel=','title="'.$on_mouse_show.'" rel=',$img_link);
		$output = str_replace('&laquo;',$img_link , $output);
	}

	if ($echo)
		echo $output;
	else
		return $output;
}

function xpress_right_arrow_post_link($args ='')
{
	global $xpress_config;
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	$ret = '';
	
	$link_str = '%link &raquo;';
	
	if($xpress_config->is_left_postnavi_old){
		$link_title = $xpress_config->newer_post_link_text;
		ob_start();
		if ($xpress_config->is_postnavi_title_disp)
			next_post_link($link_str);
		else
			next_post_link($link_str,$link_title);
		$ret = ob_get_contents();
		ob_end_clean();
		ob_start();
			next_post_link('%link',$link_title);
			$GLOBALS['right_arrow_post_link'] = ob_get_contents();
		ob_end_clean();

	} else {
		$link_title = $xpress_config->old_post_link_text;
		ob_start();
		if ($xpress_config->is_postnavi_title_disp)
			previous_post_link($link_str);
		else 
			previous_post_link($link_str,$link_title);
		$ret = ob_get_contents();
		ob_end_clean();
		ob_start();
			previous_post_link('%link',$link_title);
			$GLOBALS['right_arrow_post_link'] = ob_get_contents();
		ob_end_clean();

	}
	
	if ($xpress_config->is_postnavi_title_disp){
		$on_mouse_show = $link_title;
	} else  {
		if($xpress_config->is_left_postnavi_old){
			ob_start();
				next_post_link('%link');
				$on_mouse_show = ob_get_contents();
			ob_end_clean();
		} else {
			ob_start();
				previous_post_link('%link');
				$on_mouse_show = ob_get_contents();
			ob_end_clean();
		}
		$pattern = "<a[^>]*?>(.*)<\/a>";
		preg_match("/".$pattern."/s",  $on_mouse_show, $body_matches);
		$on_mouse_show = $body_matches[1];
	}
	$output = str_replace('">','" title="'.$on_mouse_show . '">' , $ret);

	if (icon_exists($xpress_config->post_right_arrow_image_link)){
		$img_link = str_replace($link_title,"<img src=\"$xpress_config->post_right_arrow_image_link\" alt=\"\" style=\"vertical-align:middle\"/>",$GLOBALS['right_arrow_post_link']);
		$img_link = str_replace('rel=','title="'.$on_mouse_show.'" rel=',$img_link);
		$output = str_replace('&raquo;',$img_link , $output);
	}

	if ($echo)
		echo $output;
	else
		return $output;
}
// page link
function xpress_left_arrow_posts_link($args ='')
{
	global $xpress_config;
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	$output = '';
		
	if($xpress_config->is_left_page_navi_old){
		$link_title = $xpress_config->old_page_link_text;
		ob_start();
		next_posts_link("&laquo; $link_title");
		$output = ob_get_contents();
		ob_end_clean();
	} else {
		$link_title = $xpress_config->newer_page_link_text;
		ob_start();
		previous_posts_link("&laquo; $link_title");
		$output = ob_get_contents();
		ob_end_clean();
	}
	
	if (icon_exists($xpress_config->page_left_arrow_image_link)){
		$output = $img_link . str_replace('&laquo;','' , $output);
		$img_link = str_replace($link_title,"<img src=\"$xpress_config->page_left_arrow_image_link\" alt=\"\" style=\"vertical-align:middle\"/>",$output);
		$output = $img_link . $output;
	}

	if ($echo)
		echo $output;
	else
		return $output;
}


function xpress_right_arrow_posts_link($args ='')
{
	global $xpress_config;
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	$output = '';		
	
	if($xpress_config->is_left_page_navi_old){
		$link_title = $xpress_config->newer_page_link_text;
		ob_start();
		previous_posts_link("$link_title &raquo;");
		$output = ob_get_contents();
		ob_end_clean();
	} else {
		$link_title = $xpress_config->old_page_link_text;
		ob_start();
		next_posts_link("$link_title &raquo;");
		$output = ob_get_contents();
		ob_end_clean();
	}
	
	if (icon_exists($xpress_config->page_right_arrow_image_link)){
		$output = $img_link . str_replace('&raquo;','' , $output);
		$img_link = str_replace($link_title,"<img src=\"$xpress_config->page_right_arrow_image_link\" alt=\"\" style=\"vertical-align:middle\"/>",$output);
		$output = $output . $img_link;
	}
	if ($echo)
		echo $output;
	else
		return $output;
}

function xpress_substr($str, $start, $length, $trimmarker = '...')
{
    if (function_exists('mb_substr') && function_exists('mb_strlen')){
        $str2 = mb_substr( $str , $start , $length);
        return $str2 . ( mb_strlen($str)!=mb_strlen($str2) ? $trimmarker : '' );
    } else {
        return ( strlen($str) - $start <= $length ) ? substr( $str, $start, $length ) : substr( $str, $start, $length - strlen($trimmarker) ) . $trimmarker;
    }
}


// views count
// Set and retrieves post views given a post ID or post object. 
// Retrieves post views given a post ID or post object. 
function xpress_post_views_count($args ='') {
	global $xoops_db,$wpdb;
	global $blog_id;
	static $post_cache_views;

	$defaults = array(
		'post_id' => 0,
		'format'=> __('views :%d','xpressme'),
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	if ( empty($post_id) ) {
		if ( isset($GLOBALS['post']) )
			$post_id = $GLOBALS['post']->ID;
	}
	if ( empty($blogid) ) {
		$blogid = $blog_id;
	}

	$post_id = intval($post_id);
	if($post_id==0) return null;
	if(!isset($post_cache_views[$post_id])){
		if (is_null($blogid)){
			$blog_where = '';
		} else {
			$blog_where = ' AND blog_id = '. $blogid;
		}
		$sql = "SELECT post_views FROM " . get_wp_prefix() . "views" . " WHERE post_id=$post_id " .  $blog_where;
       $post_views = $xoops_db->get_var($sql);
        if (!$post_views) {
	        $post_cache_views[$post_id] = 0;
        }else{
	        $post_cache_views[$post_id] = $post_views;
        }
	}
	$v_count = intval($post_cache_views[$post_id]);
	
	if (empty($format)) $format = __('views :%d','xpressme');
	
	$output = sprintf($format,$v_count);

	if ($echo)
		echo $output;
	else
		return $output;
}

function set_post_views_count($content) {
	if ( empty($_GET["feed"]) &&  empty($GLOBALS["feed"]) && empty($GLOBALS["doing_trackback"]) && empty($GLOBALS["doing_rss"]) && empty($_POST) && is_single() ){
		post_views_counting();
	}
	return $content;
}

// Set post views given a post ID or post object. 
function post_views_counting($post_id = 0) {
	global $xoops_db,$wpdb;
	global $table_prefix;
	global $blog_id;
	static $views;
	
	$post_id = intval($post_id);
	if ( empty($post_id) && isset($GLOBALS['post']) ){
		$post_id = $GLOBALS['post']->ID;
	}

	$views_db = get_wp_prefix() . 'views';
	if (is_null($blog_id)) $blog_id = 0;

	if($post_id==0 || !empty($views[$post_id])) return null;
	
	if(!xpress_is_author_view_count()){
		$current_user_id = $GLOBALS['current_user']->ID;
		$post_author_id = $GLOBALS['post']->post_author;
		if ($current_user_id ==$post_author_id) return null;
	}
	if (is_null($blog_id)){
		$blog_where = '';
	} else {
		$blog_where = ' AND blog_id = ' . $blog_id;
	}
	$sql = "SELECT post_views FROM " . $views_db . " WHERE post_id=$post_id" . $blog_where;
	$post_views_found = $xoops_db->get_var($sql);
	if($post_views_found){
        $sql = "UPDATE " . $views_db . " SET post_views=post_views+1 WHERE post_id=$post_id" . $blog_where;
    }else{
    	if (is_null($blog_id)){
			$sql = "INSERT INTO " . $views_db . " (post_id, post_views) VALUES ($post_id, 1)";
		} else {
			$sql = "INSERT INTO " . $views_db . " (blog_id, post_id, post_views) VALUES ($blog_id, $post_id, 1)";
		}
    }
    $xoops_db->query($sql);
	return true;
}

function get_xpress_excerpt_contents($excerpt_length_word,$excerpt_length_character,$more_link_text = '') {
	global $post,$xpress_config;
	
	$blog_encoding = get_option('blog_charset');
	$text = get_the_content('');
	if (function_exists('strip_shortcodes')){ //@since WP2.5
		$text = strip_shortcodes( $text );
	}
	$text = apply_filters('the_content', $text);
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_tags($text);
	if (function_exists('mb_strlen') && function_exists('mb_substr')){
		$is_almost_ascii = ($xpress_config->ascii_judged_rate < round(@(mb_strlen($text, $blog_encoding) / strlen($text)) * 100)) ? true : false;
	} else {
		$is_almost_ascii = true;
	}
	if($is_almost_ascii) {
		$words = explode(' ', $text, $excerpt_length_word + 1);

		if(count($words) > $excerpt_length_word) {
			array_pop($words);
			array_push($words, ' ... ');
			$text = implode(' ', $words);
			if (!empty($more_link_text)) $text .= '<div class="xpress-more-link"><a href="'. get_permalink() . "\">".$more_link_text .'</a></div>';

		}
	}
	elseif(mb_strlen($text, $blog_encoding) > $excerpt_length_character) {
		$text = mb_substr($text, 0, $xpress_config->excerpt_length_character, $blog_encoding) . ' ... ';
		if (!empty($more_link_text)) $text .= '<div class="xpress-more-link"><a href="'. get_permalink() . "\">".$more_link_text .'</a></div>';
	}

	return $text;
}

function xpress_the_content($args ='')
{
	global $post,$xpress_config;
	
	$defaults = array(
		'more_link_text'=> $xpress_config->more_link_text,
		'stripteaser' => 0,
		'more_file' => '',
		'configration_select' => 1,
		'do_excerpt' => 0,
		'excerpt_length_word' => $xpress_config->excerpt_length_word ,
		'excerpt_length_character' => $xpress_config->excerpt_length_character ,
		'excerpt_more_link_text' => $xpress_config->excerpt_more_link_text ,
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	
	if ($configration_select){
		if ($xpress_config->is_content_excerpt)
			$do_excerpt = 1;
		else
			$do_excerpt = 0;
	}
	
	if ($do_excerpt){
		$content = get_xpress_excerpt_contents($excerpt_length_word,$excerpt_length_character,$excerpt_more_link_text);
	} else {
		$content = get_the_content($more_link_text,$stripteaser,$more_file);
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
	}	
	if ($echo)
		echo $content;
	else
		return $content;
}

function xpress_post_new_link($args ='')
{
	global $xoops_config;
	
	$defaults = array(
		'link_title'=> 'Post New',
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	
	if (xpress_is_wp_version('>=','2.1')){
		$output = '<a href="'. get_bloginfo('url') . '/wp-admin/post-new.php' . '">' . $link_title . '</a>';
	} else {
		$output = '<a href="'. get_bloginfo('url') . '/wp-admin/post.php' . '">' . $link_title . '</a>';
	}	
	if ($echo)
		echo $output;
	else
		return $output;
}

function xpress_conditional_title($args ='')
{
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	
	$selected_author = xpress_selected_author('echo=0');
	
	$output = __('Main', 'xpressme');
	$output = '';
	if (is_category())
		$output = sprintf(__('Archive for the &#8216;%s&#8217; Category', 'xpressme'), single_cat_title('', false));
	if (function_exists( 'is_tag' )){
		if (is_tag())
			$output = sprintf(__('Posts Tagged &#8216;%s&#8217;', 'xpressme'), single_tag_title('', false) );
	}
	if (is_day())
		$output = sprintf(__('Archive for %s|Daily archive page', 'xpressme'), get_the_time(__('F jS, Y', 'xpressme')));
	if (is_month())
		$output = sprintf(__('Archive for %s|Monthly archive page', 'xpressme'), get_the_time(__('F, Y', 'xpressme')));
	if (is_year())
		$output = sprintf(__('Archive for %s|Yearly archive page', 'xpressme'), get_the_time(__('Y', 'xpressme')));
	if (is_author()){
		if (empty($selected_author))
			$output = sprintf(__('Archive for the &#8216;%s&#8217; Author', 'xpressme'), get_author_name( get_query_var('author')));
	}
	if (is_search())
		$output = sprintf(__('Search Results of word &#8216;%s&#8217;', 'xpressme'), get_search_query());
	
	if (!empty($selected_author)){
		$selected_id = xpress_selected_author_id('echo=0');
//		$output = get_avatar($selected_id,$size = '32') . sprintf(__('Article of %s', 'xpressme'), $selected_author) . ' - ' . $output;
		if (empty($output))
			$output = sprintf(__('Article of %s', 'xpressme'), $selected_author) ;
		else
			$output = sprintf(__('Article of %s', 'xpressme'), $selected_author) . ' - ' . $output;
	}	
	if ($echo)
		echo $output;
	else
		return $output;
}

// The content of the trackback/pingback to the post is returned by the list. 
function xpress_pings_list($args =''){
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	
	$trackbacks = xpress_get_pings();
	if (! empty($trackbacks)) {
		$output = '<ol id="xpress_pingslist"> ';

		foreach ($trackbacks as $trackback){
			$list = date(get_settings('date_format'),$trackback['date']) . ' <a target="_blank" href="' . $trackback['site_url'] . '" rel="external nofollow">' . sprintf(__('From %1$s on site %2$s','xpressme'),$trackback['title'],$trackback['site_name']) . "</a>\n" ;

			$output .=  '<li>';
			$output .=  $list ;
			$output .=  '</li>';

		}
		$output .= '</ol>' ;
	} else {
		$output = '';
	}
	
	if ($echo)
		echo $output;
	else
		return $output;
}

// The amount of the trackback/pingback to the post is returned.
function xpress_pings_number( $args ='' ) {
	$defaults = array(
		'zero' => __('No Trackback/Pingback', 'xpressme'),
		'one' => __('One Trackback/Pingback', 'xpressme'),
		'more' => __('% TrackBack/Pingback', 'xpressme'),
		'deprecated' => '',
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	$pings = xpress_get_pings();
	if (empty($pings)){
		$number = 0;
	}else {
		$number = count($pings);
	}
	if ( $number > 1 )
		$output = str_replace('%', number_format_i18n($number), $more);
	elseif ( $number == 0 )
		$output = $zero;
	else // must be one
		$output = $one;

	if ($echo)
		echo $output;
	else
		return $output;
}

// xpress_get_pings() is a subfunction used with xpress_pings_number() and xpress_pings_list(). 
function xpress_get_pings()
{
	global $withcomments, $post, $wpdb, $id, $trackback, $user_login, $user_ID, $user_identity;

	if ( ! (is_single() || is_page() || $withcomments) )
		return;

	/** @todo Use API instead of SELECTs. */
	if ( $user_ID) {
		$trackbacks = $wpdb->get_results(sprintf("SELECT * , UNIX_TIMESTAMP(comment_date) AS comment_timestamp ,UNIX_TIMESTAMP(comment_date_gmt) AS comment_timestamp_gmt FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) ) AND ( comment_type = 'trackback' OR comment_type = 'pingback' ) ORDER BY comment_date", $post->ID, $user_ID));
	} else if ( empty($trackback_author) ) {
		$trackbacks = $wpdb->get_results(sprintf("SELECT * , UNIX_TIMESTAMP(comment_date) AS comment_timestamp ,UNIX_TIMESTAMP(comment_date_gmt) AS comment_timestamp_gmt FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1' AND ( comment_type = 'trackback' OR comment_type = 'pingback' ) ORDER BY comment_date", $post->ID));
	} else {
		$trackbacks = $wpdb->get_results(sprintf("SELECT * , UNIX_TIMESTAMP(comment_date) AS comment_timestamp ,UNIX_TIMESTAMP(comment_date_gmt) AS comment_timestamp_gmt FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) AND ( comment_type = 'trackback' OR comment_type = 'pingback' ) ORDER BY comment_date", $post->ID, $trackback_author, $trackback_author_email));
	}

	if ($trackbacks){
		$ret = array();
		foreach ($trackbacks as $trackback){

			$pattern = '<strong>(.*)<\/strong>(.*)';
			if ( preg_match ( "/".$pattern."/i", $trackback->comment_content , $match ) ){
				$title = $match[1];
				$content = $match[2];
			}
			if (empty($title)) $title = $trackback->comment_author;


			$row_data = array(
				'ID'		=> $trackback->comment_ID ,
				'post_ID'	=> $trackback->comment_post_ID ,
				'site_name' => $trackback->comment_author ,
				'site_url' => $trackback->comment_author_url ,
				'title' => $title ,
				'content' => $content ,
				'date'		=> $trackback->comment_timestamp ,
				'date_gmt'		=> $trackback->comment_timestamp_gmt ,
				'agent'		=> $trackback->comment_agent ,
				'type'		=> $trackback->comment_type ,
				'IP'		=> $trackback->comment_author_IP ,
			);
			array_push($ret,$row_data);
		}
		return $ret;
	}
			return false;
}

function xpress_get_calendar($args = '') {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts , $xoops_config;

	$defaults = array(
		sun_color => '#DB0000',
		sat_color => '#004D99',
		initial => true
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	
	ob_start();
		get_calendar(true);
		$calendar = ob_get_contents();
	ob_end_clean();
	$calendar = preg_replace('/<th abbr=/', '<th align="center"  abbr=', $calendar); //week name align center
	$calendar = preg_replace('/<td>/', '<td align="center">', $calendar); //days align center
	$calendar = preg_replace('/<td id="today">/', '<td id="today" align="center">', $calendar); //today align center
	$calendar = preg_replace('/<span style="color:[^>]*>/', '', $calendar); //wp2011 color delete
	$calendar = preg_replace('/<\/span>/', '', $calendar); //wp2011 color delete

	$week_begins = intval(get_option('start_of_week'));
	$head_pattrn = '<thead>\s*<tr>\s*(<th[^>]*>[^<]*<\/th>)\s*(<th[^>]*>[^<]*<\/th>)\s*(<th[^>]*>[^<]*<\/th>)\s*(<th[^>]*>[^<]*<\/th>)\s*(<th[^>]*>[^<]*<\/th>)\s*(<th[^>]*>[^<]*<\/th>)\s*(<th[^>]*>[^<]*<\/th>)\s*<\/tr>\s*<\/thead>';
	if(preg_match('/'. $head_pattrn . '/s' ,$calendar,$head_match)){
		$sun_index = 1 - $week_begins;
		if ($sun_index < 1) $sun_index = $sun_index +7;
		$sat_index = 7 - $week_begins;
		if ($sat_index < 1) $sat_index = $sat_index +7;
		
		$sun_head = $head_match[$sun_index];
		$sat_head = $head_match[$sat_index];
		
		$pattrn = '(<th[^>]*>)(.*)(<\/th>)';
		if(preg_match('/'. $pattrn . '/' ,$sun_head,$sun_match)){
			$sun_head_after = $sun_match[1] . '<span style="color: ' . $sun_color . '">' . $sun_match[2] . '</span>'. $sun_match[3];
			$calendar = str_replace($sun_head,$sun_head_after,$calendar);
		}
		if(preg_match('/'. $pattrn . '/' ,$sat_head,$sat_match)){
			$sat_head_after = $sat_match[1] . '<span style="color: ' . $sat_color . '">' . $sat_match[2] . '</span>'. $sat_match[3];
			$calendar = str_replace($sat_head,$sat_head_after,$calendar);
		}
	}
	return $calendar;
}

function xpress_grobal_recent_posts($num = 10,$exclusion_blog = 0, $shown_for_each_blog = false)
{
	global $wpdb, $wp_rewrite , $switched , $blog_id;
	if (empty($date_format)) $date_format = get_settings('date_format');
	if (empty($time_format)) $time_format = get_settings('time_format');
	$exclusion = explode(',' , $exclusion_blog);


	$first_blogid = $blog_id;
	$num = (int)$num;
//	$wp_query->in_the_loop = true;		//for use the_tags() in multi lopp 
	$data_array = array();
	if (xpress_is_multiblog()){
		$blogs = get_blog_list(0,'all');
		foreach ($blogs AS $blog) {
			if (!in_array(0, $exclusion) && in_array($blog['blog_id'], $exclusion)) continue;
			switch_to_blog($blog['blog_id']);
			$wp_rewrite->init();  // http://core.trac.wordpress.org/ticket/12040 is solved, it is unnecessary.

				if (empty($num)){
					query_posts("post_status=publish");
				} else {
					query_posts("showposts=$num&post_status=publish");
				}
				if (have_posts()){
					while(have_posts()){
						$data = new stdClass();
						
						the_post();
						ob_start();
							the_ID();
							$data->post_id = ob_get_contents();
						ob_end_clean();
						
						$data->blog_id = $blog['blog_id'];
						$data->blog_name = get_bloginfo('name');
						$data->blog_url = get_bloginfo('url');
						$data->blog_link = '<a href="' . $data->blog_url . '">' . $data->blog_name . '</a>' ;


						ob_start();
							the_title();
							$data->title = ob_get_contents();
						ob_end_clean();
						$data->post_permalink = get_blog_permalink($data->brog_id, $data->post_id);
						$data->title_link = '<a href="' . $data->post_permalink . '">' . $data->title . '</a>' ;

						ob_start();
							the_author_posts_link();
							$data->post_author = ob_get_contents();
						ob_end_clean();

						ob_start();
							the_category(' &bull; ');
							$data->post_category = ob_get_contents();
						ob_end_clean();	
						
						if (function_exists('the_tags')){
							ob_start();
								the_tags(__('Tags:', 'xpress') . ' ',' &bull; ','');
								$data->post_tags = ob_get_contents();
							ob_end_clean();	
						} else {
							$data->tags = '';
						}

						$data->the_content = xpress_the_content('echo=0');
						
						ob_start();
							the_content();
							$data->the_full_content = ob_get_contents();
						ob_end_clean();
						
						ob_start();
							the_modified_date($date_format);
							$data->post_modified_date = ob_get_contents();
						ob_end_clean();
							
						ob_start();
							the_modified_date($time_format);
							$data->post_modified_time = ob_get_contents();
						ob_end_clean();
						$data->post_modified_date_time = $data->post_modified_date . ' ' . $data->post_modified_time;
						
						ob_start();
							the_time('U');
							$data->post_unix_time = ob_get_contents();
						ob_end_clean();
						
						ob_start();
							the_time($date_format);
							$data->post_date = ob_get_contents();
						ob_end_clean();
						
						ob_start();
							the_time($time_format);
							$data->post_time = ob_get_contents();
						ob_end_clean();
						
						$data->post_date_time = $data->post_date . ' ' . $data->post_time;

						ob_start();
							comments_popup_link(__('Comments (0)'), __('Comments (1)'), __('Comments (%)'));
							$data->comments_link = ob_get_contents();
						ob_end_clean();
						
						$data->post_views = xpress_post_views_count('post_id=' . $data->post_id . '&blogid=' . $data->brog_id . '&format=' . __('Views :%d', 'xpress'). '&echo=0');
						if (function_exists('the_qf_get_thumb_one')){
							$data->post_thumbnail = the_qf_get_thumb_one("num=0&width=120&tag=1","",$data->the_full_content);
						} else {
							$data->post_thumbnail = get_the_post_thumbnail(null,'thumbnail');
						}
						$data->author_avatar =get_avatar(get_the_author_meta('ID'),$size = '32');

						$data_array[] = $data;
	        		}  // end whilwe
				} // end if
			restore_current_blog();
//			$wp_rewrite->init();
		} // end foreach
//		switch_to_blog($first_blogid);
		$wp_rewrite->init(); // http://core.trac.wordpress.org/ticket/12040 is solved, it is unnecessary.

		restore_current_blog();
	}
	if (!$shown_for_each_blog){
		usort($data_array, "the_time_cmp");
		if (!empty($num)){
			$data_array = array_slice($data_array,0,$num);
		}
	}
	return $data_array;
}
function the_time_cmp($a, $b)
{
    return - strcasecmp($a->post_unix_time, $b->post_unix_time);
}

function xpress_get_blog_option($option_name,$b_id = 1)
{
	global $wpdb;
	$db_prefix = get_wp_prefix();
	
	if (empty($b_id)) $b_id =1;
	$blog_prefix = '';
	if ($b_id >1) $blog_prefix = $b_id . '_';
	$options_tb = $db_prefix . $blog_prefix .'options';

	$sql = "SELECT option_value FROM $options_tb WHERE option_name = $option_name";
	$ret_val = $wpdb->get_var($sql);
	return $ret_val;
}

function xpress_create_new_blog_link($args ='' ) {
	global $xoops_config;

	global $current_user;
	$defaults = array(
		'echo' => 1
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r );
	$result = xpress_create_new_blog();
	if (!empty($result)){
		$output = $result['link'];
	} else {
		$output = '';
	}
	
	if ($echo)
		echo $output;
	else
		return $output;
}

function xpress_create_new_blog() {
	global $xoops_config;
	global $current_user;
	$ret = array();

	if (xpress_is_multiblog() && is_user_logged_in()){
		$primary_blog_id = @$current_user->primary_blog;
		if (!empty($primary_blog_id)) return $ret;
		$active_signup = get_site_option( 'registration' );
		if ( !$active_signup ) $active_signup = 'none';
		switch ($active_signup){
			case 'all':
			case 'blog':
				$ret['url'] = $xoops_config->module_url . '/wp-signup.php';
				$ret['menu_url'] = 'wp-signup.php';
				$ret['title'] = __('Create New Blog','xpressme');
				$ret['link'] = '<a href="' . $ret['url'] . '">' . $ret['title'] . '</a>';
				break;
			case 'user':
			case 'none':
			default:
		}
	}
	return $ret;
}
function xpress_primary_blog_link() {
	global $xoops_config;
	global $current_user;
	global $blog_id;
	$ret = array();

	if (xpress_is_multiblog() && is_user_logged_in()){
		$blog_list = get_blog_list();
		$root_path = get_blog_status(1,'path');
		$primary_blog_id = @$current_user->primary_blog;
		if(empty($primary_blog_id)) return $ret;
		$primary_path = get_blog_status($primary_blog_id,'path');
		$script = str_replace($root_path, "", $primary_path);
		if ($primary_blog_id !== $blog_id){
			$ret['url'] = get_blogaddress_by_id($primary_blog_id);
			$ret['menu_url'] = $script;
			$ret['title'] = __('Your Primary Blog','xpressme');
			$ret['link'] = '<a href="' . $ret['url'] . '">' . $ret['title'] . '</a>';
		}
	}
	return $ret;
}

?>