<?php
// wp_login override for wp2.3 under
if ( !function_exists('wp_login') && version_compare($xoops_config->wp_version,'2.3', '<')) :
	function wp_login($username, $password, $already_md5 = false) {
		global $wpdb, $error;


		if(is_object($GLOBALS["xoopsModule"]) && WP_BLOG_DIRNAME == $GLOBALS["xoopsModule"]->getVar("dirname")){
			if(!is_object($GLOBALS["xoopsUser"])){
				wp_clearcookie();
				return false;
			}
		}			

		$username = sanitize_user($username);

		if ( '' == $username )
			return false;

		if ( '' == $password ) {
			$error = __('<strong>ERROR</strong>: The password field is empty.');
			return false;
		}

		$login = get_userdatabylogin($username);
		//$login = $wpdb->get_row("SELECT ID, user_login, user_pass FROM $wpdb->users WHERE user_login = '$username'");

		if (!$login) {
			$error = __('<strong>ERROR</strong>: Invalid username.');
			return false;
		} else {
			if ($login->user_login == $username) {
					if ($login->user_pass == $password) return true;
					if ($login->user_pass == md5($password)) return true;
			}

			$error = __('<strong>ERROR</strong>: Incorrect password.');
			$pwd = '';
			return false;
		}
	}
endif;

// Under WP2.1
if (version_compare($xoops_config->wp_version,'2.1', '<')) :
	// ADD WP 2.1.0
	function the_modified_date($d = '') {
		echo apply_filters('the_modified_date', get_the_modified_date($d), $d);
	}

	// ADD WP 2.1.0
	function get_the_modified_date($d = '') {
		if ( '' == $d )
			$the_time = get_post_modified_time(get_option('date_format'));
		else
			$the_time = get_post_modified_time($d);
		return apply_filters('get_the_modified_date', $the_time, $d);
	}
endif;	// Under WP2.1

// Under WP2.2
if (version_compare($xoops_config->wp_version,'2.2', '<')):
	// Added WP2.2 wp_parse_args()
	function wp_parse_args( $args, $defaults = '' ) {
		if ( is_object( $args ) )
			$r = get_object_vars( $args );
		elseif ( is_array( $args ) )
			$r =& $args;
		else
			wp_parse_str( $args, $r );

		if ( is_array( $defaults ) )
			return array_merge( $defaults, $r );
		return $r;
	}
	// Added WP2.2 translate()
	function translate($text, $domain = 'default') {
		global $l10n;

		if (isset($l10n[$domain]))
			return apply_filters('gettext', $l10n[$domain]->translate($text), $text, $domain);
		else
			return apply_filters('gettext', $text, $text, $domain);
	}

	// Added WP2.2 translate_with_context()
	function before_last_bar( $string ) {
		$last_bar = strrpos( $string, '|' );
		if ( false == $last_bar )
			return $string;
		else
			return substr( $string, 0, $last_bar );
	}
endif;	// Under WP2.2

// Under WP2.2.1
if (version_compare($xoops_config->wp_version,'2.2.1', '<')) :
	// Added WP2.2.1 wp_parse_str()
	function wp_parse_str( $string, &$array ) {
		parse_str( $string, $array );
		if ( get_magic_quotes_gpc() )
			$array = stripslashes_deep( $array );
		$array = apply_filters( 'wp_parse_str', $array );
	}

endif;	// Under WP2.2.1

// Under WP2.3
if (version_compare($xoops_config->wp_version,'2.3', '<')) :
	if ( !function_exists('wp_sanitize_redirect') ) :
	/**
	 * Sanitizes a URL for use in a redirect.
	 *
	 * @since 2.3
	 *
	 * @return string redirect-sanitized URL
	 **/
	function wp_sanitize_redirect($location) {
		$location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%]|i', '', $location);
		$location = wp_kses_no_null($location);

		// remove %0d and %0a from location
		$strip = array('%0d', '%0a');
		$found = true;
		while($found) {
			$found = false;
			foreach( (array) $strip as $val ) {
				while(strpos($location, $val) !== false) {
					$found = true;
					$location = str_replace($val, '', $location);
				}
			}
		}
		return $location;
	}
	endif;
	
	if ( !function_exists('wp_sanitize_redirect') ) :
	/**
	 * Sanitizes a URL for use in a redirect.
	 *
	 * @since 2.3
	 *
	 * @return string redirect-sanitized URL
	 **/
	function wp_sanitize_redirect($location) {
		$location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%]|i', '', $location);
		$location = wp_kses_no_null($location);

		// remove %0d and %0a from location
		$strip = array('%0d', '%0a');
		$found = true;
		while($found) {
			$found = false;
			foreach( (array) $strip as $val ) {
				while(strpos($location, $val) !== false) {
					$found = true;
					$location = str_replace($val, '', $location);
				}
			}
		}
		return $location;
	}
	endif;

endif;	// Under WP2.3

// Under WP2.5
if (version_compare($xoops_config->wp_version,'2.5', '<')) :
	// Added WP2.5 absint()
	function absint( $maybeint ) {
		return abs( intval( $maybeint ) );
	}
	// Added WP2.5 translate_with_context()
	function translate_with_context( $text, $domain = 'default' ) {
		return before_last_bar(translate( $text, $domain ) );
	}
	/**
	 * @ignore
	 */
	function _c() {}
	
	if ( !function_exists('wp_logout') ) {
		function wp_logout() {
			wp_clear_auth_cookie();
			do_action('wp_logout');
		}
	}


endif;	// Under WP2.5

// Under WP2.7
if (version_compare($xoops_config->wp_version,'2.6', '<')) :
	/**
	 * Guess the URL for the site.
	 *
	 * Will remove wp-admin links to retrieve only return URLs not in the wp-admin
	 * directory.
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	function wp_guess_url() {
	if ( defined('WP_SITEURL') && '' != WP_SITEURL ) {
		$url = WP_SITEURL;
	} else {
		$schema = ( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
		$url = preg_replace('|/wp-admin/.*|i', '', $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	}
	return $url;
}
endif;	// Under WP2.6

// Under WP2.7
if (version_compare($xoops_config->wp_version,'2.7', '<')) :
	// Added WP2.7 separate_comments()
	function &separate_comments(&$comments) {
		$comments_by_type = array('comment' => array(), 'trackback' => array(), 'pingback' => array(), 'pings' => array());
		$count = count($comments);
		for ( $i = 0; $i < $count; $i++ ) {
			$type = $comments[$i]->comment_type;
			if ( empty($type) )
				$type = 'comment';
			$comments_by_type[$type][] = &$comments[$i];
			if ( 'trackback' == $type || 'pingback' == $type )
				$comments_by_type['pings'][] = &$comments[$i];
		}

		return $comments_by_type;
	}

	// Added WP2.7 get_comments()
	function get_comments( $args = '' ) {
		global $wpdb;

		$defaults = array('status' => '', 'orderby' => 'comment_date_gmt', 'order' => 'DESC', 'number' => '', 'offset' => '', 'post_id' => 0);

		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

		// $args can be whatever, only use the args defined in defaults to compute the key
		$key = md5( serialize( compact(array_keys($defaults)) )  );
		$last_changed = wp_cache_get('last_changed', 'comment');
		if ( !$last_changed ) {
			$last_changed = time();
			wp_cache_set('last_changed', $last_changed, 'comment');
		}
		$cache_key = "get_comments:$key:$last_changed";

		if ( $cache = wp_cache_get( $cache_key, 'comment' ) ) {
			return $cache;
		}

		$post_id = absint($post_id);

		if ( 'hold' == $status )
			$approved = "comment_approved = '0'";
		elseif ( 'approve' == $status )
			$approved = "comment_approved = '1'";
		elseif ( 'spam' == $status )
			$approved = "comment_approved = 'spam'";
		else
			$approved = "( comment_approved = '0' OR comment_approved = '1' )";

		$order = ( 'ASC' == $order ) ? 'ASC' : 'DESC';

		$orderby = 'comment_date_gmt';  // Hard code for now

		$number = absint($number);
		$offset = absint($offset);

		if ( !empty($number) ) {
			if ( $offset )
				$number = 'LIMIT ' . $offset . ',' . $number;
			else
				$number = 'LIMIT ' . $number;

		} else {
			$number = '';
		}

		if ( ! empty($post_id) )
			$post_where = "comment_post_ID = $post_id AND" ;
		else
			$post_where = '';

		$comments = $wpdb->get_results( "SELECT * FROM $wpdb->comments WHERE $post_where $approved ORDER BY $orderby $order $number" );
		wp_cache_add( $cache_key, $comments, 'comment' );

		return $comments;
	}

	// Added WP2.7 absint()
	function locate_template($template_names, $load = false) {
		if (!is_array($template_names))
			return '';

		$located = '';
		foreach($template_names as $template_name) {
			if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
				$located = STYLESHEETPATH . '/' . $template_name;
				break;
			} else if ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
				$located = TEMPLATEPATH . '/' . $template_name;
				break;
			}
		}

		if ($load && '' != $located)
			load_template($located);

		return $located;
	}
	// Added WP2.7 post_password_required()
	function post_password_required( $post = null ) {
		$post = get_post($post);

		if ( empty($post->post_password) )
			return false;

		if ( !isset($_COOKIE['wp-postpass_' . COOKIEHASH]) )
			return true;

		if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password )
			return true;

		return false;
	}
	// Added WP2.7 comment_form_title()
	function comment_form_title( $noreplytext = 'Leave a Reply', $replytext = 'Leave a Reply to %s', $linktoparent = TRUE ) {
		global $comment;

		$replytoid = isset($_GET['replytocom']) ? (int) $_GET['replytocom'] : 0;

		if ( 0 == $replytoid )
			echo $noreplytext;
		else {
			$comment = get_comment($replytoid);
			$author = ( $linktoparent ) ? '<a href="#comment-' . get_comment_ID() . '">' . get_comment_author() . '</a>' : get_comment_author();
			printf( $replytext, $author );
		}
	}
endif;	// Under WP2.7

// Under WP2.8
if (version_compare($xoops_config->wp_version,'2.8', '<')) :
	function _wp_specialchars( $string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false ) {
		$string = (string) $string;

		if ( 0 === strlen( $string ) ) {
			return '';
		}

		// Don't bother if there are no specialchars - saves some processing
		if ( !preg_match( '/[&<>"\']/', $string ) ) {
			return $string;
		}

		// Account for the previous behaviour of the function when the $quote_style is not an accepted value
		if ( empty( $quote_style ) ) {
			$quote_style = ENT_NOQUOTES;
		} elseif ( !in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) ) {
			$quote_style = ENT_QUOTES;
		}

		// Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
		if ( !$charset ) {
			static $_charset;
			if ( !isset( $_charset ) ) {
				$alloptions = wp_load_alloptions();
				$_charset = isset( $alloptions['blog_charset'] ) ? $alloptions['blog_charset'] : '';
			}
			$charset = $_charset;
		}
		if ( in_array( $charset, array( 'utf8', 'utf-8', 'UTF8' ) ) ) {
			$charset = 'UTF-8';
		}

		$_quote_style = $quote_style;

		if ( $quote_style === 'double' ) {
			$quote_style = ENT_COMPAT;
			$_quote_style = ENT_COMPAT;
		} elseif ( $quote_style === 'single' ) {
			$quote_style = ENT_NOQUOTES;
		}

		// Handle double encoding ourselves
		if ( !$double_encode ) {
			$string = wp_specialchars_decode( $string, $_quote_style );

			/* Critical */
			// The previous line decodes &amp;phrase; into &phrase;  We must guarantee that &phrase; is valid before proceeding.
			$string = wp_kses_normalize_entities($string);

			// Now proceed with custom double-encoding silliness
			$string = preg_replace( '/&(#?x?[0-9a-z]+);/i', '|wp_entity|$1|/wp_entity|', $string );
		}

		$string = @htmlspecialchars( $string, $quote_style, $charset );

		// Handle double encoding ourselves
		if ( !$double_encode ) {
			$string = str_replace( array( '|wp_entity|', '|/wp_entity|' ), array( '&', ';' ), $string );
		}

		// Backwards compatibility
		if ( 'single' === $_quote_style ) {
			$string = str_replace( "'", '&#039;', $string );
		}

		return $string;
	}
	
	/**
	 * Checks for invalid UTF8 in a string.
	 *
	 * @since 2.8
	 *
	 * @param string $string The text which is to be checked.
	 * @param boolean $strip Optional. Whether to attempt to strip out invalid UTF8. Default is false.
	 * @return string The checked text.
	 */
	function wp_check_invalid_utf8( $string, $strip = false ) {
		$string = (string) $string;

		if ( 0 === strlen( $string ) ) {
			return '';
		}

		// Store the site charset as a static to avoid multiple calls to get_option()
		static $is_utf8;
		if ( !isset( $is_utf8 ) ) {
			$is_utf8 = in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) );
		}
		if ( !$is_utf8 ) {
			return $string;
		}

		// Check for support for utf8 in the installed PCRE library once and store the result in a static
		static $utf8_pcre;
		if ( !isset( $utf8_pcre ) ) {
			$utf8_pcre = @preg_match( '/^./u', 'a' );
		}
		// We can't demand utf8 in the PCRE installation, so just return the string in those cases
		if ( !$utf8_pcre ) {
			return $string;
		}

		// preg_match fails when it encounters invalid UTF8 in $string
		if ( 1 === @preg_match( '/^./us', $string ) ) {
			return $string;
		}

		// Attempt to strip the bad chars if requested (not recommended)
		if ( $strip && function_exists( 'iconv' ) ) {
			return iconv( 'utf-8', 'utf-8', $string );
		}

		return '';
	}

	/**
	 * Perform a deep string replace operation to ensure the values in $search are no longer present
	 *
	 * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
	 * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
	 * str_replace would return
	 *
	 * @since 2.8.1
	 * @access private
	 *
	 * @param string|array $search
	 * @param string $subject
	 * @return string The processed string
	 */
	function _deep_replace( $search, $subject ) {
		$found = true;
		$subject = (string) $subject;
		while ( $found ) {
			$found = false;
			foreach ( (array) $search as $val ) {
				while ( strpos( $subject, $val ) !== false ) {
					$found = true;
					$subject = str_replace( $val, '', $subject );
				}
			}
		}

		return $subject;
	}

	
	/* @since 2.8.0
	 * @uses wp_kses_bad_protocol() To only permit protocols in the URL set
	 *		via $protocols or the common ones set in the function.
	 *
	 * @param string $url The URL to be cleaned.
	 * @param array $protocols Optional. An array of acceptable protocols.
	 *		Defaults to 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' if not set.
	 * @param string $_context Private. Use esc_url_raw() for database usage.
	 * @return string The cleaned $url after the 'clean_url' filter is applied.
	 */
	function esc_url( $url, $protocols = null, $_context = 'display' ) {
		$original_url = $url;

		if ( '' == $url )
			return $url;
		$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = _deep_replace($strip, $url);
		$url = str_replace(';//', '://', $url);
		/* If the URL doesn't appear to contain a scheme, we
		 * presume it needs http:// appended (unless a relative
		 * link starting with / or a php file).
		 */
		if ( strpos($url, ':') === false &&
			substr( $url, 0, 1 ) != '/' && substr( $url, 0, 1 ) != '#' && !preg_match('/^[a-z0-9-]+?\.php/i', $url) )
			$url = 'http://' . $url;

		// Replace ampersands and single quotes only when displaying.
		if ( 'display' == $_context ) {
			$url = wp_kses_normalize_entities( $url );
			$url = str_replace( '&amp;', '&#038;', $url );
			$url = str_replace( "'", '&#039;', $url );
		}

		if ( !is_array($protocols) )
			$protocols = array ('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn');
		if ( wp_kses_bad_protocol( $url, $protocols ) != $url )
			return '';

		return apply_filters('clean_url', $url, $original_url, $_context);
	}
	/**
	 * Escaping for HTML attributes.
	 *
	 * @since 2.8.0
	 *
	 * @param string $text
	 * @return string
	 */
	function esc_attr( $text ) {
		$safe_text = wp_check_invalid_utf8( $text );
		$safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
		return apply_filters( 'attribute_escape', $safe_text, $text );
	}

endif;	// Under WP2.8
?>