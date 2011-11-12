<?php
// used wptouch plugin & iPhone access check (thx uemu)
function is_iphone_with_wptouch(){
	global $wptouch_plugin;
	if (is_object($wptouch_plugin)) return $wptouch_plugin->applemobile;
	return false;
}

function ob_end_flush_child($now_ob_level){
	// The flash does the output buffer of WP before doing rendering. 
	// Example: comment_quicktags plugin does not flash buffer specifically.
	while(ob_get_level() > $now_ob_level) ob_end_flush();
	$ob_level = ob_get_level();
}
function safe_site_url(){
	global $xoops_config,$blog_id,$blogname;
	
	if (is_xpress_index_page_call()){
		
		if (!empty($_POST['submit_url_change'])) {
			if (!empty($_POST['site_url_set'])) {
				update_option('siteurl' , $_POST['site_url_set']);
			}
			if (!empty($_POST['home_url_set'])) {
				update_option('home' , $_POST['home_url_set']);
			}
		}
		if (!empty($_POST['submit_redirect'])) {
			$url = get_option('home') . '/wp-admin/options-permalink.php';
//			echo $url;
			wp_redirect($url);
			exit();
		}

		$siteurl = get_option('siteurl');
		$home = get_option('home');
		$module_url = $xoops_config->module_url;
		$module_name = $xoops_config->module_name;
		$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
		$guess_url = preg_replace('|/' . $module_name . '/.*|i', '/' . $module_name, $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$blog_sub_path = '';
		if(function_exists('is_multisite') && is_multisite()){
			if ($blog_id > 0 && $blog_id != BLOG_ID_CURRENT_SITE){
				$blog_sub_path = '/' .$blogname;
			}
		}
		$guess_url .= $blog_sub_path;
		$xoops_module_url = $module_url.$blog_sub_path;
		
		$site_url_error =  false;
		if(strcmp($siteurl,$guess_url) !== 0) {
			if (!@fclose(@fopen($siteurl . '/xoops_version.php', "r"))){
				$site_url_error = true;
			}
		}
		$home_url_error = false;
		if(strcmp($guess_url,$home) !== 0) {
			$home_url_error = true;
		}
		if ($site_url_error || $home_url_error){
			include $xoops_config->xoops_root_path ."/header.php";
			$form = '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">'."\n";
			$form .= '<table cellspacing="1" cellpadding="1" border="0">';
			$form .= '<tbody>';
			if ($site_url_error){
				$site_url_error_log = __('Can not access WordPress address (URL).','xpressme');
				$form .= '<tr><td colspan="3"><font color="red"><b>' . $site_url_error_log . '</b></font></td></tr>';
				$form .= '<tr>';
				$form .= '<td width="16">&nbsp;</td>';
				$form .= '<td width="64">'.__('WordPress address (URL)').'</td>';
				$form .= '<td>';
				$form .= $siteurl . '<br />';
				$form .= '&emsp;to<br />';
				$form .= '<input name="site_url_set" type="text" size="64" maxlength="200" value="'  . $guess_url . '" /></td>';
				$form .= '</tr>';

			}
			if ($home_url_error){
				if ($site_url_error) $form .= '<tr><td colspan="3">&nbsp;</td></tr>';;
				$home_url_error_log = __('WordPress Blog address (URL) is different from access URL.','xpressme');
				$form .= '<tr><td colspan="3"><font color="red"><b>' . $home_url_error_log . '</b></font></td></tr>';
				$form .= '<tr>';
				$form .= '<td width="16">&nbsp;</td>';
				$form .= '<td width="144">'. __('Blog address (URL)') .'</td>';
				$form .= '<td>';
				$form .= $home . '<br />';
				$form .= '&emsp;to<br />';
				$form .= '<input name="home_url_set" type="text" size="64" maxlength="200" value="'  . $guess_url . '" /></td>';
				$form .= '</tr>';
			}
			$form .= '</tbody>';
			$form .= '</table><br />';
			$form .= '<input type="submit" value= "'.__('Save Changes').'" name="submit_url_change" />' ."\n";
			$form .= '</form>' ."\n";
			echo $form;
			include $xoops_config->xoops_root_path . '/footer.php';
			exit();
		}
		if (!empty($_POST['home_url_set'])) {
			$url = get_option('home') . '/wp-admin/options-permalink.php';
			$form = '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">'."\n";
			$form .= '<p><font color="blue"><b>'. __('After Blog address (URL) is set, it is necessary to set the permalink again.','xpressme').'</b></font></p>';
			$form .= '<input type="submit" value= "'.__('Permalink Settings').'" name="submit_redirect" />' ."\n";
			$form .= '</form>' ."\n";
			include $xoops_config->xoops_root_path ."/header.php";
			echo $form;
			include $xoops_config->xoops_root_path . '/footer.php';
			exit();
		}

	}
}

require_once( dirname( __FILE__ ).'/request_url.php');
require_once( dirname( __FILE__ ).'/memory_limit.php');

$global_session = $_SESSION;

if (is_xpress_index_page_call()){
	//$_SERVER['REQUEST_METHOD'] = 'POST' is
	//When notifying by a private message, Notification_reserve_send();
	//it is evaded that the data base becomes read-only as a result of the check on the referrer and the method. 
	$request_method =  (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : '';
	$_SERVER['REQUEST_METHOD'] = 'POST';
	require_once $xoops_config->xoops_mainfile_path; //It is necessary to execute it for the user attestation before wp-settings.php. 
	$_SERVER['REQUEST_METHOD'] = $request_method;
	xpress_set_memory_limmit(); // Set memory limmit.(Limmit Value from XPressMe modele config.)
	unset($offset);		//This Trap is provides the case where $offset is defined on the XOOPS side.
	
	require_once(ABSPATH.'wp-settings.php');
	
	//When it is not possible to connect it correctly at site home URL on the WordPress side, 
	//URL is corrected based on accessed URL. 

	global $xoopsUserIsAdmin;
	if ($xoopsUserIsAdmin) safe_site_url();

	if (!is_object($xoopsUser)){	// before login auth cookie clear
		wp_logout();
	}
	wp();
	//for Event notification update on single post to which link is changed by permalink
	if ( is_404() ) {
		if (!empty($_POST['not_redirect'])) {
			include '../../mainfile.php';
			require_once XOOPS_ROOT_PATH.'/include/notification_update.php';
			exit();
		}
	}
	
	if (!function_exists('is_wordpress_style')){	// When the XPressME plug-in is invalid
		require_once dirname( __FILE__ ).'/xpress_active_plugin.php' ;
		xpress_pulugin_activation('xpressme/xpressme.php');
		// reloaded 
		header('Location: ' . $xoops_config->module_url . '/');
		
		$err_str = "The activation of the XPressME plugin was executed.<br />\n";
		$err_str .= "Because the XPressME plugin was invalid.<br />\n";
		$err_str .= "Please do the rereading seeing on the page.\n";			
		die($err_str);
	}
	
	Notification_reserve_send();
	ob_start();
		$now_ob_level = ob_get_level();
		if (version_compare($xoops_config->wp_version,'2.2', '<'))
			require_once dirname( __FILE__ ).'/old_template-loader.php' ;
		else
			require_once( ABSPATH . WPINC . '/template-loader.php' );
		ob_end_flush_child($now_ob_level);
		$wp_output = ob_get_contents();
	ob_end_clean();
	
	// insert credit
	$pattern = '<body';
	$replace = "\n<!-- credit " . xpress_credit('echo=0&no_link=1') . " -->\n<body";
	$wp_output = preg_replace("/" . $pattern . "/s", $replace, $wp_output);
			
	//Rendering Select
	if(
		is_wordpress_style()		// When the display mode is WordPress style
		|| is_feed()				// It judges it here because it does in is_index_page() through feed to which the permalink is set.
		|| is_iphone_with_wptouch()	// When iPhone access & used wptouch plugin (thx uemu)
	){

			echo $wp_output;
	} else {
			require_once( dirname( __FILE__ ).'/xpress_render.php' );
			xpress_render($wp_output);
	}

	//When there is no block cache, and an optional block is different, cache is refreshed. 
	//When adding, and changing and deleting Post & Comment, block cache is refreshed by add_action at any time. 
	// This Function in xpressme plugin
	require_once( dirname( __FILE__ ).'/xpress_block_render.php' );	
	xpress_unnecessary_block_cache_delete($xoops_config->module_name);
	if (is_home()) {
		xpress_block_cache_refresh($xoops_config->module_name);
		require_once( dirname( __FILE__ ).'/xpress_block_header.php' );	
		set_xpress_block_header($xoops_config->module_name);
	}
	if ( ini_get( 'register_globals' ) )
		$_SESSION = $global_session; //restore a session erased by wp_unregister_GLOBALS
	
	exit();		// The return to wp-blog-header.php is stolen here
}
if (is_admin_post_call()) require_once $xoops_config->xoops_mainfile_path;		// for Notification_triggerEvent
if (is_xpress_comments_post_call()) require_once $xoops_config->xoops_mainfile_path;	// for Notification_triggerEvent
xpress_set_memory_limmit(); // Set memory limmit.(Limmit Value from XPressMe modele config.)
require_once(ABSPATH.'wp-settings.php');
if ( ini_get( 'register_globals' ) )
	$_SESSION = $global_session; //restore a session erased by wp_unregister_GLOBALS
?>