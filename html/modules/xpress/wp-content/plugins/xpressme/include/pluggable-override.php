<?php
/**
 * XPress - WordPress for XOOPS
 *
 * Adding multi-author features to XPressME
 *
 * @copyright	The XPressME project
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		toemon
 * @since		2.05
 * @version		$Id$
 * @package		module::xpress
 */

// ***********************************  Start Pluggable Function Edit (wp-include/pluggable.php) ************************************

if ( !function_exists('get_currentuserinfo') ) :
function get_currentuserinfo() {
	global $current_user;
	global $xoopsModule,$xoopsUser,$xoopsUserIsAdmin;


	if ($xoopsModule){
		if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )
			return false;

		if (is_object($xoopsUser)){			// When the user is logging in xoops
			if ( ! empty($current_user) ){
				$xoops_user = $xoopsUser->getVar("uname");
				if ($current_user->user_login == $xoops_user){	// If xoops login user and wordpress current user are the same
					return;
				}
			}
			if (check_xpress_auth_cookie()){	//The cookie is login user's or it checks it
				if (function_exists('wp_validate_auth_cookie')){
					if ( $user = wp_validate_auth_cookie() ) {
						// When the user meta prefix is different according to the change in the xoops data base prefix, it restores it. 
						if (!check_user_meta_prefix($user)){
							repair_user_meta_prefix();
						}
						wp_set_current_user($user);
						return ;
					}
				} else { // for WP2.0					
					if ( !empty($_COOKIE[USER_COOKIE]) && !empty($_COOKIE[PASS_COOKIE])){
						if(wp_login($_COOKIE[USER_COOKIE], $_COOKIE[PASS_COOKIE], true) ) {
							$user_login = $_COOKIE[USER_COOKIE];
							wp_set_current_user(0, $user_login);
							return;
						}
					}
				}
			}				
			return xpress_login();	
		} else {							// For the xoops guest
			if ( ! empty($current_user) ){	// When a current user of wordpress is set, a current user is cleared. 
				wp_set_current_user(0);
				wp_logout();
				wp_clear_auth_cookie();
			}
			return false;
		}
	} else {
		// WP original
		if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )
			return false;

		if ( ! empty($current_user) )
			return;

		if (function_exists('wp_validate_auth_cookie')){
			if ( ! $user = wp_validate_auth_cookie() ) {
				 if ( empty($_COOKIE[LOGGED_IN_COOKIE]) || !$user = wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in') ) {
				 	wp_set_current_user(0);
				 	return false;
				 }
			}
			wp_set_current_user($user);
		} else { // for WP2.0
			if ( empty($_COOKIE[USER_COOKIE]) || empty($_COOKIE[PASS_COOKIE]) || 
				!wp_login($_COOKIE[USER_COOKIE], $_COOKIE[PASS_COOKIE], true) ) {
				wp_set_current_user(0);
				return false;
			}
			$user_login = $_COOKIE[USER_COOKIE];
			wp_set_current_user(0, $user_login);
		}
	}
}
endif;

if ( !function_exists('xpress_login') ) :
function xpress_login(){
	global $current_user;
	global $xoopsModule,$xoopsUser,$xoopsUserIsAdmin;
	
	if(is_object($xoopsUser)){
		$u_name = $xoopsUser->getVar("uname");
		$u_pass_md5 = $xoopsUser->getVar("pass");
		if ( ! empty($u_name) && ! empty($u_pass_md5) ) {
			include_once dirname( __FILE__ ).'/user_sync_xoops.php';
			repair_user_meta_prefix();  //Repair when data base prefix is changed on XOOPS side
			$messege = '';
			$ret = user_sync_to_wordpress($xoopsUser->getVar("uid"),$messege);
			if ($ret){
				$user = new WP_User(0, $u_name);
				if ( wp_login($u_name, $u_pass_md5) ) {
					wp_setcookie($u_name, $u_pass_md5, true, '', '', false);
					do_action('wp_login', $u_name);
					wp_set_current_user($user->ID);
					return  true;
				}
			}			
		}
	}
	if ( ! empty($current_user) ){
		wp_set_current_user(0);
		wp_logout();
		wp_clear_auth_cookie();
	}
	return false;
}
endif;

if ( !function_exists('check_xpress_auth_cookie') ) :
function check_xpress_auth_cookie() {		// for wp2.5
	if ( empty($_COOKIE[AUTH_COOKIE]) ){
		return false;
	}
	$cookie = $_COOKIE[AUTH_COOKIE];

	$cookie_elements = explode('|', $cookie);
	if ( count($cookie_elements) != 3 ){
			return false;
	}
					
	if(is_object($GLOBALS["xoopsModule"])){
//		&& WP_BLOG_DIRNAME == $GLOBALS["xoopsModule"]->getVar("dirname")){
		if(is_object($GLOBALS["xoopsUser"])){
			$u_name = $GLOBALS["xoopsUser"]->getVar("uname");
			list($username, $expiration, $hmac) = $cookie_elements;
			if ($u_name == $username) {
				return true;
			}
		}
	} else {
		$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
		$org_url = $_SERVER['REQUEST_URI'];
		$needle = '/modules/' . $mydirname . '/wp-admin/';
		if (strstr($org_url , $needle)){
			return true;				
		}
	}
	return false;
}
endif;

if ( !function_exists('wp_check_password') ) :
// for wordpress2.5
function wp_check_password($password, $hash, $user_id = '') {
	global $wp_hasher;
	global $xoops_config,$xoops_db;

	// For attestation when password has been sent as hash value. (When having logged it in from Xoops and ImpressCMS)
	if ($hash == $password){ 
		return apply_filters('check_password', true, $password, $hash, $user_id);
	}
	
	// Password authentication for Xoops 
	if ( strlen($hash) <= 32 ) {
		$check = ( $hash == md5($password) );
		return apply_filters('check_password', $check, $password, $hash, $user_id);	
	}
	
	// Password authentication for ImpressCMS 
	if($xoops_config->is_impress && function_exists('hash')){
		$mainSalt = $xoops_config->xoops_db_salt;
		// get user salt
		$xpress_user_db = $xoops_config->module_db_prefix . 'users';
		$xoops_user_db = $xoops_config->xoops_db_prefix . '_users';
		$login_name = $xoops_db->get_var("SELECT user_login FROM $xpress_user_db WHERE ID = $user_id");
		$user_salt = $xoops_db->get_var("SELECT salt FROM $xoops_user_db WHERE uname = '$login_name'");
		$enc_type = intval( $xoops_db->get_var("SELECT enc_type FROM $xoops_user_db WHERE uname = '$login_name'") );
		
		// Make Impress hash 
		if($enc_type == 0) {$impress_hash = md5($password);} // no salt used for compatibility with external scripts such as ipb/phpbb etc.
		elseif($enc_type == 1) {$impress_hash = hash('sha256', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 2) {$impress_hash = hash('sha384', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 3) {$impress_hash = hash('sha512', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 4) {$impress_hash = hash('ripemd128', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 5) {$impress_hash = hash('ripemd160', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 6) {$impress_hash = hash('whirlpool', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 7) {$impress_hash = hash('haval128,4', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 8) {$impress_hash = hash('haval160,4', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 9) {$impress_hash = hash('haval192,4', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 10) {$impress_hash = hash('haval224,4', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 11) {$impress_hash = hash('haval256,4', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 12) {$impress_hash = hash('haval128,5', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 13) {$impress_hash = hash('haval160,5', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 14) {$impress_hash = hash('haval192,5', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 15) {$impress_hash = hash('haval224,5', $user_salt.md5($password).$mainSalt);}
		elseif($enc_type == 16) {$impress_hash = hash('haval256,5', $user_salt.md5($password).$mainSalt);}

		if ($hash == $impress_hash){
			return apply_filters('check_password', true, $password, $hash, $user_id);
		}
	}	

	// If the hash is still md5...
	if ( strlen($hash) <= 32 ) {
		$check = ( $hash == md5($password) );	
/* A new hash is not used because it differs from the hash on the XOOPS password. 
 *		if ( $check && $user_id ) {
 *			// Rehash using new hash.
 *			wp_set_password($password, $user_id);
 *			$hash = wp_hash_password($password);
 *		}
 */
		return apply_filters('check_password', $check, $password, $hash, $user_id);
	}

	// If the stored hash is longer than an MD5, presume the
	// new style phpass portable hash.
	if ( empty($wp_hasher) ) {
		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		// By default, use the portable hash from phpass
		$wp_hasher = new PasswordHash(8, TRUE);
	}

	$check = $wp_hasher->CheckPassword($password, $hash);

	return apply_filters('check_password', $check, $password, $hash, $user_id);
}
endif;

if ( !function_exists('wp_redirect') ) :
function wp_redirect($location, $status = 302) {
	global $is_IIS,$xoops_config,$action;
	
	if ($location == 'wp-login.php?loggedout=true') $location = $xoops_config->xoops_url.'/user.php?op=logout'; //xoops logout at wp logout
	if ($location == 'wp-login.php?action=register') $location = $xoops_config->xoops_url."/register.php";  //wp-register to xoops register
	if ($action == 'logout') $location = $xoops_config->xoops_url.'/user.php?op=logout'; //xoops logout at comment logout

	$location = apply_filters('wp_redirect', $location, $status);
	$status = apply_filters('wp_redirect_status', $status, $location);

	if ( !$location ) // allows the wp_redirect filter to cancel a redirect
		return false;

	$location = wp_sanitize_redirect($location);

	if (!headers_sent()) {
		ob_end_clean();
		if ( $is_IIS ) {
			header("Refresh: 0;url=$location");
		} else {
			if ( php_sapi_name() != 'cgi-fcgi' )
				status_header($status); // This causes problems on IIS and some FastCGI setups
			header("Location: $location");
		}
	} else {  // force redirect 
		echo ("<HTML>");
		echo("<META http-equiv=\"Refresh\" content=\"0;url=$location\">");
		echo ("<BODY onload=\"try {self.location.href='$location' } catch(e) {}\">");
		printf(__("If the page does not automatically reload, please click <a href='%s'>here</a>","xpressme"),$location);
		echo ("</BODY>");
		echo ("</HTML>");
	}
}
endif;

if ( !function_exists('wp_hash_password') ) :
function wp_hash_password($password) {
	global $wp_hasher;
	return md5($password); // A new hash is not used because it differs from the hash on the XOOPS password.
/*
	if ( empty($wp_hasher) ) {
		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		// By default, use the portable hash from phpass
		$wp_hasher = new PasswordHash(8, TRUE);
	}

	return $wp_hasher->HashPassword($password);
*/
}
endif;

if ( !function_exists('wp_clear_auth_cookie') ) :
/**
 * Removes all of the cookies associated with authentication.
 *
 * @since 2.5
 */
function wp_clear_auth_cookie() {
	do_action('clear_auth_cookie');

	@setcookie(AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
	@setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, COOKIE_DOMAIN);
	@setcookie(AUTH_COOKIE, ' ', time() - 31536000, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN);
	@setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN);
	@setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	@setcookie(LOGGED_IN_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);

	// Old cookies
	@setcookie(AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	@setcookie(AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	@setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	@setcookie(SECURE_AUTH_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);

	// Even older cookies
	@setcookie(USER_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	@setcookie(PASS_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	@setcookie(USER_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	@setcookie(PASS_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);

}
endif;






// ***********************************  End Of Pluggable Function Edit (wp-include/pluggable.php) ************************************

?>