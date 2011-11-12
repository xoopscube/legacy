<?php
function  find_xpress_update( $version, $locale ){
		$latest_version = get_option('xpressme_latest_version');
		return (object)$latest_version;
}

function list_xpress_update( $update=null ) {
	global $xoops_config,$xpress_config;
	
	$xpress_version = $xoops_config->module_version . $xoops_config->module_codename;
	$lang = WPLANG;
	
	$automatically_enable = file_exists(ABSPATH .'wp-admin/includes/class-wp-upgrader.php');

	$check_url = "http://ja.xpressme.info/version_check/index.php?version=$xpress_version&lang=$lang";

	echo	'<div class="wrap">'."\n";
	echo		'<div id="icon-options-general" class="icon32"><br /></div>'."\n";
	echo		'<h2>' . __('XPressME Upgrade', 'xpressme') . "</h2><br>\n";
	if( get_xpress_latest_version($check_url)){
		$latest = get_option('xpressme_latest_version');
		if ($latest) {
			$site_url=$latest['url'];
			$package=$latest['package'];
			$latest_version=$latest['latest_version'];
			$check_time=$latest['check_time'];
		}

		$show_buttons = false;
		if (version_compare($xpress_version, $latest_version, '>')){ 
				echo '<h3 class="response">';
				printf(__('You are using a XPressME Integration Kit development version (%s). Cool! Please stay updated.', 'xpressme') , $xpress_version);
				echo '</h3>';
		} else if (version_compare($xpress_version, $latest_version, '<')) {
			echo '<h3 class="response">';
			_e( 'An updated version of XPressME Integration Kit is available.', 'xpressme' );
			echo '</h3>';

			if ($latest['diff_response'] == 'diff_exists'){
				$download_diff  ='<a class="button" href="' . $latest['diff_package'] . '">';
				$download_diff .=	sprintf(__('Download differential file for %s', 'xpressme') , $xpress_version);
				$download_diff .='</a>';
			}
			if($automatically_enable){
				$message = 	sprintf(__('You can update to XPressME Integration Kit Ver %s</a> automatically or download the package and install it manually:', 'xpressme'), $latest_version);
			} else {
				$message = 	sprintf(__('You can upgrade to version %s download the package and install it manually:', 'xpressme'),$latest_version);
			}
			$submit = __('Update Automatically', 'xpressme');
			$form_action = 'admin.php?page=upgrade_page&action=do-xpress-upgrade';
			$download = sprintf(__('Download %s', 'xpressme') , $latest_version);
			$show_buttons = true;

		} else {
			echo '<h3>';
			printf(__('You have the latest version of XPressME Integration Kit Ver.%s.', 'xpressme'),$xpress_version);
			echo '</h3>';

			$message = __('You have the latest version of XPressME Integration Kit. You do not need to upgrade', 'xpressme');
			$submit = __('Re-install Automatically', 'xpressme');
			$form_action = 'update-core.php?action=do-core-reinstall';

		}
		
		// develop
		$develop_show = false;
		if ($latest['develop_response'] == 'development_exists'
			&& !empty($latest['develop_package'])
			)
		{
			$develop_latest_version=$latest['develop_latest_version'];
			$develop_form_action = 'admin.php?page=upgrade_page&action=do-xpress-develop_upgrade';
			if($automatically_enable){
				$develop_message = 	sprintf(__('You can update to  XPressME Integration Kit development version %s automatically or download the package and install it manually:', 'xpressme'), $develop_latest_version);
			} else {
				$develop_message = 	sprintf(__('You can use the development version %s download the package and install it manually:', 'xpressme'),$develop_latest_version);
			}
			$develop_show = true;
			$develop_package=$latest['develop_package'];
			$develop_submit = __('Update Automatically', 'xpressme');
			$develop_download = sprintf(__('Download %s', 'xpressme') , $develop_latest_version);
			$develop_download_diff = '';
			// develop diff
			if ($latest['diff_develop_response'] == 'diff_develop_exists'
				&& !empty($latest['diff_develop_package'])
				)
			{
					$develop_download_diff  = '<a class="button" href="' . $latest['diff_develop_package'] . '">';
					$develop_download_difff .= sprintf(__('Download differential file for %s', 'xpressme') , $latest['diff_develop_latest_version']);
					$develop_download_diff .= '</a>';
			}
		}

		echo '<p>';
		echo $message;
		echo '</p>';
		echo '<form method="post" action="' . $form_action . '" name="upgrade" class="upgrade">';
	//	wp_nonce_field('upgrade-core');
		echo '<p>';
		echo '<input name="version" value="'. esc_attr($update->current) .'" type="hidden"/>';
		echo '<input name="locale" value="'. esc_attr($update->locale) .'" type="hidden"/>';
		if ( $show_buttons ) {
			if($automatically_enable){
				echo '<input id="upgrade" class="button" type="submit" value="' . esc_attr($submit) . '" name="upgrade" />&nbsp;';
			}
			echo '<a href="' . esc_url($package) . '" class="button">' . $download . '</a>&nbsp;';
			echo $download_diff;
		}
		echo '</form>';
		
		if ($develop_show){
			echo '<p>';
			echo $develop_message;
			echo '</p>';
			echo '<form method="post" action="' . $develop_form_action . '" name="develop_upgrade" class="develop_upgrade">';
			echo '<p>';
			echo '<input name="version" value="'. esc_attr($update->current) .'" type="hidden"/>';
			echo '<input name="locale" value="'. esc_attr($update->locale) .'" type="hidden"/>';
				if($automatically_enable){
					echo '<input id="upgrade" class="button" type="submit" value="' . esc_attr($develop_submit) . '" name="develop_upgrade" />&nbsp;';
				}
				echo '<a href="' . esc_url($develop_package) . '" class="button">' . $develop_download . '</a>&nbsp;';
				echo $develop_download_diff;
			echo '</form>';
		}
	} else {
		echo '<h3 class="response">';
		printf(__('There is no response from <a href="%s">version check API</a> now. sorry, please confirm it after.', 'xpressme'),$check_url);
		echo	"</div>\n";
	}
}


function xpress_update_core($current) {
	include_once ABSPATH . 'wp-content/plugins/xpressme/include/class-xpress-upgrader.php';
	$upgrader = new Xpress_Upgrader();
	return $upgrader->upgrade($current);
}

function do_xpress_upgrade( $develop = false,$reinstall = false ) {
	global $wp_filesystem,$xoops_config;

	if ( $reinstall )
		$url = 'admin.php?page=upgrade_page&action=do-xpress-reinstall';
	else {
		if(!$develop)
			$url = 'admin.php?page=upgrade_page&action=do-xpress-upgrade';
		else
			$url = 'admin.php?page=upgrade_page&action=do-xpress-develop_upgrade';
		$url = wp_nonce_url($url, 'upgrade-xpress');
	}
	if ( false === ($credentials = request_filesystem_credentials($url, '', false, ABSPATH)) )
		return;

	$version = isset( $_POST['version'] )? $_POST['version'] : false;
	$locale = isset( $_POST['locale'] )? $_POST['locale'] : 'en_US';
	$update = find_xpress_update( $version, $locale );
//	$update = true;
	if ( !$update )
		return;
	if ( $develop )
		$update->package = $update->develop_package;

	if ( ! WP_Filesystem($credentials, ABSPATH) ) {
		request_filesystem_credentials($url, '', true, ABSPATH); //Failed to connect, Error and request again
		return;
	}
?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Update XPressME Integration Kit', 'xpressme'); ?></h2>
<?php
	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		echo '</div>';
		return;
	}

	if ( $reinstall )
		$update->response = 'reinstall';

	$result = xpress_update_core($update, 'show_message');

	if ( is_wp_error($result) ) {
		show_message($result);
		if ('up_to_date' != $result->get_error_code() )
			show_message( __('Installation Failed', 'xpressme') );
	} else {
		show_message( __('XPressME Integration Kit files updated successfully', 'xpressme') );
		show_message( __('Please update the module. ', 'xpressme') );
		
		$update_url = $xoops_config->module_url .'/admin/update.php';
		show_message( '<strong>' . __('Actions:', 'xpressme') . '</strong> <a href="' . esc_url( $update_url ) . '">' . __('Go to Module Update', 'xpressme') . '</a>' );
	}
	echo '</div>';
}




function upgrade_page()
{
	$action = isset($_GET['action']) ? $_GET['action'] : 'upgrade-xpress';
	$upgrade_error = false;
	if ( 'upgrade-xpress' == $action ) {
		list_xpress_update();
	} elseif ( 'do-xpress-upgrade' == $action) {
		$update_develop = false;
		do_xpress_upgrade($update_develop);
	} else if  ( 'do-xpress-develop_upgrade' == $action) {
		$update_develop = true;
		do_xpress_upgrade($update_develop);
	}
}

function xp_remote_get($url, $headers = ""){
	global $xoops_config;
	$xpress_version = $xoops_config->module_version . $xoops_config->module_codename;

	require_once( $xoops_config->module_path . '/wp-includes/class-snoopy.php');

	// Snoopy is an HTTP client in PHP
	$client = new Snoopy();
	$client->agent = 'XPressME/' . $xpress_version;
	$client->read_timeout = 2;
	if (is_array($headers) ) {
		$client->rawheaders = $headers;
	}

	@$client->fetch($url);
	$response['response']['code'] = $client->status;
	$response['body'] = $client->results;
	return $response;
	return $client;

}

function get_xpress_latest_version($check_url=null){
	global $wp_version, $wpdb, $wp_local_package;
	global $xoops_config;
	
	$xpress_version = $xoops_config->module_version . $xoops_config->module_codename;
	$lang = WPLANG;
	if(is_null($check_url))
		$check_url = "http://ja.xpressme.info/version_check/index.php?version=$xpress_version&lang=$lang";
	$request_options = array(
	'timeout' => 3,
	'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);

	if (! function_exists('wp_remote_get')) {
		$response = xp_remote_get($check_url);
		
		if (empty($response['body'])) return false;
	} else {
	
		$response = wp_remote_get($check_url, $request_options);
		
		if ( is_wp_error( $response ) )
			return false;
	}
	if ( 200 != $response['response']['code'] )
		return false;
	$body = trim( $response['body'] );
	$body = str_replace(array("\r\n", "\r"), "\n", $body);
	$returns = explode("\n", $body);

	if ( isset( $returns[0] ) ) $response = $returns[0]; else $response = '';
	if ( isset( $returns[1] ) ) $url = clean_url( $returns[1] ); else $url = '';
	if ( isset( $returns[2] ) ) $package = clean_url( $returns[2] ); else $package = '';
	if ( isset( $returns[3] ) ) $latest_version = $returns[3]; else  $latest_version = '';
	if ( isset( $returns[4] ) ) $lang = $returns[4]; else $lang = '';
	
	// diff 
	if ( isset( $returns[6] ) ) $diff_response = $returns[6]; else $diff_response = '';
	if ( isset( $returns[7] ) ) $diff_url = clean_url( $returns[7] ); else $diff_url = '';
	if ( isset( $returns[8] ) ) $diff_package = clean_url( $returns[8] ); else $diff_package = '';
	if ( isset( $returns[9] ) ) $diff_latest_version = $returns[9]; else  $diff_latest_version = '';
	if ( isset( $returns[10] ) ) $diff_lang = $returns[10]; else $diff_lang = '';

	// developer 
	if ( isset( $returns[12] ) ) $develop_response = $returns[12]; else $develop_response = '';
	if ( isset( $returns[13] ) ) $develop_url = clean_url( $returns[13] ); else $develop_url = '';
	if ( isset( $returns[14] ) ) $develop_package = clean_url( $returns[14] ); else $develop_package = '';
	if ( isset( $returns[15] ) ) $develop_latest_version = $returns[15]; else  $develop_latest_version = '';
	if ( isset( $returns[16] ) ) $develop_lang = $returns[16]; else $develop_lang = '';
	
	// developer diff
	if ( isset( $returns[18] ) ) $diff_develop_response = $returns[18]; else $diff_develop_response = '';
	if ( isset( $returns[19] ) ) $diff_develop_url = clean_url( $returns[19] ); else $diff_develop_url = '';
	if ( isset( $returns[20] ) ) $diff_develop_package = clean_url( $returns[20] ); else $diff_develop_package = '';
	if ( isset( $returns[21] ) ) $diff_develop_latest_version = $returns[21]; else  $diff_develop_latest_version = '';
	if ( isset( $returns[22] ) ) $diff_develop_lang = $returns[22]; else $diff_develop_lang = '';

	$write_options = array (
		'response' => $response ,
		'url' => $url ,
		'package' => $package ,
		'latest_version' => $latest_version ,
		'lang' => $lang ,
		'diff_response' => $diff_response ,
		'diff_url' => $diff_url ,
		'diff_package' => $diff_package ,
		'diff_latest_version' => $diff_latest_version ,
		'diff_lang' => $diff_lang ,
		'develop_response' => $develop_response ,
		'develop_url' => $develop_url ,
		'develop_package' => $develop_package ,
		'develop_latest_version' => $develop_latest_version ,
		'develop_lang' => $develop_lang ,
		'diff_develop_response' => $diff_develop_response ,
		'diff_develop_url' => $diff_develop_url ,
		'diff_develop_package' => $diff_develop_package ,
		'diff_develop_latest_version' => $diff_develop_latest_version ,
		'diff_develop_lang' => $diff_develop_lang ,
		'check_time' => time()
	);
	
	$latest_version = get_option('xpressme_latest_version');
	if (!$latest_version) {
		add_option('xpressme_latest_version', $write_options);
	} else {
		update_option('xpressme_latest_version', $write_options);
	}
	return true;
}

function xpress_update_check() {
	if ( defined('WP_INSTALLING') )
		return;
	global $pagenow;

	$php_query_string = $_SERVER['QUERY_STRING'];

	if ( 'admin.php' == $pagenow && 'page=upgrade_page' == $php_query_string)
		return;

	global $wp_version, $wpdb, $wp_local_package;
	global $xoops_config;

	$php_query_string = $_SERVER['QUERY_STRING'];
	$xpress_version = $xoops_config->module_version . $xoops_config->module_codename;

	$latest = get_option('xpressme_latest_version');
	if (!$latest ) {
		get_xpress_latest_version();
		$latest = get_option('xpressme_latest_version');
	}

	if ($latest) {
		$next_check = $latest['check_time'] + (60*60*24);
		$now_time = time();
		if ($next_check < $now_time ){
			get_xpress_latest_version();
			$latest = get_option('xpressme_latest_version');
		}
	}

	if ($latest) {
		$url=$latest['url'];
		$package=$latest['package'];
		$latest_version=$latest['latest_version'];
		$check_time=$latest['check_time'];
		$upgrade_page = $xoops_config->module_url . "/wp-admin/admin.php?page=upgrade_page";

		if (version_compare($xpress_version, $latest_version, '<')) {
			if ( current_user_can('manage_options') ){
				$msg = sprintf( __('XPressME Integration Kit Version %1$s is available! <a href="%2$s">Please update now</a>.', 'xpressme'), $latest_version, $upgrade_page );
			} else {
				$msg = sprintf( __('XPressME Integration Kit Version %1$s is available! Please notify the site administrator.', 'xpressme'), $latest_version );
			}
			echo "<div id='update-nag'>$msg </div>";
		}
	}
}
?>