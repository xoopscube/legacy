<?php

//< style >< script >< link > tag is pulled out from the header of html contents. 
function get_mod_header($contents)
{
	global $xpress_config;
	
	$pattern = "<body[^>]*?>(.*)<\/body>";
	$body_cut = preg_replace("/".$pattern."/s" , '' , $contents);


	$pattern = "<head[^>]*?>(.*)<\/head>";
	$head_str = '';
	if (preg_match("/".$pattern."/s",  $body_cut, $head_matches)){
		$head_str = $head_matches[1];
	}
	$pattern = '<head[^>]*?>';
	$head_str = preg_replace("/".$pattern."/s" , '' , $head_str);
	$pattern = '<\/head>';
	$head_str = preg_replace("/".$pattern."/s" , '' , $head_str);
	$pattern = '<\s*html\s+xmlns[^>]*?>';
	$head_str = preg_replace("/".$pattern."/s" , '' , $head_str);
	$pattern = '<\s*head\s+profile[^>]*?>';
	$head_str = preg_replace("/".$pattern."/s" , '' , $head_str);
	$pattern = '<\s*meta\s+http-equiv[^>]*?>';
	$head_str = preg_replace("/".$pattern."/s" , '' , $head_str);
	$pattern = '<title[^>]*?>(.*)<\s*\/\s*title\s*>';
	$head_str = preg_replace("/".$pattern."/s" , '' , $head_str);

	$head_str = meta_name_cut('robots',$head_str);
	$head_str = meta_name_cut('keywords',$head_str);
	$head_str = meta_name_cut('description',$head_str);
	$head_str = meta_name_cut('rating',$head_str);
	$head_str = meta_name_cut('author',$head_str);
	$head_str = meta_name_cut('copyright',$head_str);
	$head_str = meta_name_cut('generator',$head_str);

	$head_str = preg_replace("/^(\s)*(\r|\n|\r\n)/m", "", $head_str);	
	$pattern = "^";
	$head_str = preg_replace("/".$pattern."/m" , "\t" , $head_str);

	return $head_str;
}

function meta_name_cut($name = '', $head_str)
{
	$pattern = '<\s*meta\s+name\s*=\s*["\']' . $name . '["\'][^>]*?>';
	$head_str = preg_replace("/".$pattern."/i" , '' , $head_str);
	return $head_str;
}

// for title reprace plugin (all in one seo pack)
function get_xpress_title($contents)
{
	$pattern = '<title[^>]*?>(.*)<\s*\/\s*title\s*>';
	$title_str = '';
	if (preg_match("/".$pattern."/i",  $contents, $head_matches)){
		$title_str = $head_matches[1];
	}
	return $title_str;
}

function get_xpress_meta_name($name = '',$contents)
{
	$pattern = '<\s*meta\s+name\s*=\s*["\']' . $name . '["\']\s*content\s*=\s*[\'"](.*)[\'"]\s*\/\s*>';
	$meta = '';
	if (preg_match("/".$pattern."/i",  $contents, $head_matches)){
		$meta = @$head_matches[1];
	}
	return $meta;
}

// get sidebar rendaring 
function get_sidebar_rander($name = null)
{
	$templates = array();
	if ( isset($name) )
		$templates[] = "sidebar-{$name}.php";

	$templates[] = "sidebar.php";

	$link= locate_template($templates, false);
	if ('' == $link){
//		$link =  get_theme_root() . '/default/sidebar.php';
		return '';
	} else {
		ob_start();
			require($link);
			$sidebar = ob_get_contents();
		ob_end_clean();
		return $sidebar;
	}
}

// < body > tag is pulled out from the header of html contents. 
function get_body($contents)
{
	global $xpess_config;
	if (!is_object($xpess_config)) $xpess_config = new XPressME_Class();
	$pattern = "<body[^>]*?>(.*)<\/body>";
	$body = '';
	if(preg_match("/".$pattern."/s",  $contents, $body_matches)){
		$body = $body_matches[1];
	}
	
	if ($xpess_config->is_theme_sidebar_disp){
		$xpress_class = 'xpress-body';
	} else {
		$xpress_class = 'xpress-body onecolumn';
	}
	
	$pattern = '<body\s*([^>]*)>';
	$body_class = 'class="' . $xpress_class . '"';
	if(preg_match("/".$pattern."/s",  $contents, $body_matches)){
		$body_tag_option = $body_matches[1];

		$pattern = 'class\s*=\s*[\'|"]([^\'|^"]*)[\'|"]';		
		if(preg_match("/".$pattern."/",  $body_tag_option, $class_matches)){
			$class_value = $class_matches[1];
			$reprace = $xpress_class . ' '. $class_value;
			$body_class = preg_replace("/".$class_value."/",  $reprace, $body_tag_option);
		} else {
			$body_class = 'class="' . $xpress_class . '" ' . $body_tag_option;
		}
	}

	if (!$xpess_config->is_theme_sidebar_disp){
		$side_panel = get_sidebar_rander();
			$body = str_replace($side_panel,'',$body);
	}
	$body = "\n<div " . $body_class . "> <!-- Substitution of wordpress <body > -->\n" . $body . "\n</div> <!-- Substitution of wordpress </body > -->\n";
	return $body;
}

//Making of module header
function get_xpress_module_header($contents)
{
	global $xoopsTpl;
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$preload_make_module_header = $xoopsTpl->get_template_vars('xoops_module_header');
	} else {
		$preload_make_module_header = '';
	}
	
	if (! empty($preload_make_module_header)){
	$preload_make_module_header = '<!-- preload added module header -->
' . $preload_make_module_header . '
';
	}
	
	$wp_module_header = '<!-- wordpress  added module header -->
' . get_mod_header($contents) . '
<!-- end of wordpress  added module header -->';
	$wp_module_header .= "\n<!-- credit " . xpress_credit('echo=0&no_link=1') . " -->\n";

	return $preload_make_module_header . $wp_module_header;
}

//PHP_SELF and GET are remake for the XOOPS event notification.
function xpress_remake_global_for_permlink(){
	global $wp_db,$wp_query;
	$php_self = $_SERVER['PHP_SELF'];
	$get = $_GET;

	if (preg_match('/\/$/',$php_self) && !preg_match('/index.php/',$php_self)) {
		$php_self = $php_self . 'index.php';
		$_SERVER['PHP_SELF'] = $php_self;
	}
	if (empty($_GET)){
		$query_vars = $wp_query->query_vars;
		$post = $wp_query->post;
		if ($wp_query->is_single) {
			$_GET = array('p'=>$post->ID);
		} else if($wp_query->is_category){
			$_GET = array('cat'=>$query_vars['cat']);
		} else if($wp_query->is_author){
			$_GET = array('author'=>$query_vars['author']);
		}
	}
}

//rendering for the module header and the body
function xpress_render($contents){
	global $xoops_config;
	global $xoopsUser , $xoopsTpl,$xpress_config , $xoopsModule , $xoopsLogger, $xoopsConfig ; //for XOOPS
	
	require_once( ABSPATH .'/include/xpress_breadcrumbs.php' );
	$xoops_breadcrumbs = get_breadcrumbs();

	xpress_remake_global_for_permlink();
	$mydirname = basename(dirname(dirname(__FILE__)));
	include $xoops_config->xoops_root_path ."/header.php";
	$xoopsTpl->assign('xoops_breadcrumbs', $xoops_breadcrumbs);
	$xoopsTpl->assign('xoops_module_header', get_xpress_module_header($contents));
	$page_title = $GLOBALS["xoopsModule"]->getVar("name"). ' &raquo;'. get_xpress_title($contents);
	$xoopsTpl->assign('xoops_pagetitle', $page_title);
	
	$xoops_keywords = $xoopsTpl->get_template_vars('xoops_meta_keywords');
	$wp_keyword = get_xpress_meta_name('keywords',$contents);
	switch ($xpress_config->meta_keyword_type){
		case 'xoops':
			break;
		case 'wordpress':
			if (!empty($wp_keyword))
				$xoopsTpl->assign('xoops_meta_keywords', $wp_keyword);
			break;
		case 'wordpress_xoops':
			if (!empty($wp_keyword)){
				if (!empty($xoops_keywords)){
					$keywords = $wp_keyword . ', ' . $xoops_keywords;
				} else {
					$keywords = $wp_keyword;
				}
				$xoopsTpl->assign('xoops_meta_keywords', $keywords);
			} 
			break;
		default :
	}

	$xoops_description = $xoopsTpl->get_template_vars('xoops_meta_description');
	$wp_description = get_xpress_meta_name('description',$contents);
	switch ($xpress_config->meta_description_type){
		case 'xoops':
			break;
		case 'wordpress':
			if (!empty($wp_description))
				$xoopsTpl->assign('xoops_meta_description', $wp_description);
			break;
		case 'wordpress_xoops':
			if (!empty($wp_description)){
				if (!empty($xoops_description)){
					$description = $wp_description . ' ' . $xoops_description;
				} else {
					$description = $wp_description;
				}
				$xoopsTpl->assign('xoops_meta_description', $description);
			} 
			break;
		default :
	}

	$wp_robots = get_xpress_meta_name('robots',$contents);
	switch ($xpress_config->meta_robot_type){
		case 'xoops':
			break;
		case 'wordpress':
			if (!empty($wp_robots))
				$xoopsTpl->assign('xoops_meta_robots', $wp_robots);
			break;
		default :
	}
	if (empty($contents)){
		$template_name = get_option('template');
		$xpress_data['body_contents'] = "<p>Themes \"$template_name\" is broken or doesn't exist. </p><p>Please choose the right theme from the admin page of wordpress.</p>";
	} else {
		$xpress_data['body_contents'] = get_body($contents);
	}
	// used $GLOBALS. becose xpress_left_arrow_post_link() and xpress_right_arrow_post_link() is other loop in this position
	$xpress_data['left_post_link'] = @$GLOBALS['left_arrow_post_link'];
	$xpress_data['right_post_link'] = @$GLOBALS['right_arrow_post_link'];
	$xpress_data['left_posts_link'] =  str_replace('&laquo;','',xpress_left_arrow_posts_link('echo=0'));
	$xpress_data['right_posts_link'] = str_replace('&raquo;','',xpress_right_arrow_posts_link('echo=0'));
	$xpress_data['now_user_level'] = xpress_now_user_level('echo=0');

	//If notification_select.php is not executed in CMS other than XCL, the selector of in-line is not displayed. 
	if (is_object($xoopsModule) && $xoopsModule->getVar('hasnotification') == 1 && is_object($xoopsUser)) {
		require_once $xoops_config->xoops_root_path . '/include/notification_select.php';
	}
	
	$xoopsTpl->assign('xpress', $xpress_data);
	$templates_file = 'db:'.$mydirname. '_index.html';
	echo $xoopsTpl->fetch( $templates_file ) ;
	include $xoops_config->xoops_root_path . '/footer.php';
}

?>