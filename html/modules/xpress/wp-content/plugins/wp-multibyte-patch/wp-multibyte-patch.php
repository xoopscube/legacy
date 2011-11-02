<?php
/*
Plugin Name: WP Multibyte Patch
Plugin URI: http://eastcoder.com/code/wp-multibyte-patch/
Description: Enhances multibyte string I/O functionality of WordPress.
Author: Kuraishi (tenpura)
Version: 1.5
Author URI: http://eastcoder.com/
Text Domain: wp-multibyte-patch
Domain Path: /languages
*/

/*
    Copyright (C) 2011 Kuraishi (Email: 210pura at gmail dot com), Tinybit Inc.
           This program is licensed under the GNU GPL Version 2.
*/

class multibyte_patch {

	// Do not edit this section. Use wpmp-config.php instead.

	var $conf = array(
		'excerpt_length' => 55,
		'excerpt_mblength' => 110,
		'excerpt_more' => ' [...]',
		'comment_excerpt_length' => 20,
		'comment_excerpt_mblength' => 40,
		'ascii_threshold' => 90,
		'patch_wp_mail' => true,
		'patch_incoming_trackback' => true,
		'patch_incoming_pingback' => true,
		'patch_wp_trim_excerpt' => true,
		'patch_get_comment_excerpt' => true,
		'patch_process_search_terms' => true,
		'patch_admin_custom_css' => true,
		'patch_wplink_js' => true,
		'patch_word_count_js' => true,
		'patch_sanitize_file_name' => true,
		'patch_bp_create_excerpt' => false,
		'bp_excerpt_mblength' => 110,
		'bp_excerpt_more' => ' [...]'
	);

	var $blog_encoding;
	var $has_mbfunctions;
	var $textdomain = 'wp-multibyte-patch';
	var $lang_dir = 'languages';
	var $required_version = '3.2';
	var $query_based_vars = array();

	function guess_encoding($string, $encoding = '') {
		$blog_encoding = $this->blog_encoding;

		if(!$encoding && seems_utf8($string))
			return 'UTF-8';
		elseif(!$encoding)
			return $blog_encoding;
		else
			return $encoding;
	}

	function convenc($string, $to_encoding, $from_encoding = '') {
		$blog_encoding = $this->blog_encoding;

		if('' == $from_encoding)
			$from_encoding = $blog_encoding;

		if(strtoupper($to_encoding) == strtoupper($from_encoding))
			return $string;
		else
			return mb_convert_encoding($string, $to_encoding, $from_encoding);
	}

	function incoming_trackback($commentdata) {
		global $wpdb;

		if('trackback' != $commentdata['comment_type'])
			return $commentdata;

		if(false === $this->conf['patch_incoming_trackback'])
			return $commentdata;

		$title = isset($_POST['title']) ? stripslashes($_POST['title']) : '';
		$excerpt = isset($_POST['excerpt']) ? stripslashes($_POST['excerpt']) : '';
		$blog_name = isset($_POST['blog_name']) ? stripslashes($_POST['blog_name']) : '';
		$blog_encoding = $this->blog_encoding;

		$from_encoding = isset($_POST['charset']) ? $_POST['charset'] : '';

		if(!$from_encoding)
			$from_encoding = (preg_match("/^.*charset=([a-zA-Z0-9\-_]+).*$/i", $_SERVER['CONTENT_TYPE'], $matched)) ? $matched[1] : '';

		$from_encoding = str_replace(array(',', ' '), '', strtoupper(trim($from_encoding)));
		$from_encoding = $this->guess_encoding($excerpt . $title . $blog_name, $from_encoding);

		$title = $this->convenc($title, $blog_encoding, $from_encoding);
		$blog_name = $this->convenc($blog_name, $blog_encoding, $from_encoding);
		$excerpt = $this->convenc($excerpt, $blog_encoding, $from_encoding);

		$title = strip_tags($title);
		$excerpt = strip_tags($excerpt);

		$title = (strlen($title) > 250) ? mb_strcut($title, 0, 250, $blog_encoding) . '...' : $title;
		$excerpt = (strlen($excerpt) > 255) ? mb_strcut($excerpt, 0, 252, $blog_encoding) . '...' : $excerpt;

		$commentdata['comment_author'] = $wpdb->escape($blog_name);
		$commentdata['comment_content'] = $wpdb->escape("<strong>$title</strong>\n\n$excerpt");

		return $commentdata;
	}

	function pre_remote_source($linea, $pagelinkedto) {
		$this->pingback_ping_linea = $linea;
		$this->pingback_ping_pagelinkedto = $pagelinkedto;
		return $linea;
	}

	function incoming_pingback($commentdata) {
		if('pingback' != $commentdata['comment_type'])
			return $commentdata;

		if(false === $this->conf['patch_incoming_pingback'])
			return $commentdata;

		$pagelinkedto = $this->pingback_ping_pagelinkedto;
		$linea = $this->pingback_ping_linea;

		$linea = preg_replace("/" . preg_quote('<!DOC', '/') . "/i", '<DOC', $linea);
		$linea = preg_replace("/[\r\n\t ]+/", ' ', $linea);
		$linea = preg_replace("/ <(h1|h2|h3|h4|h5|h6|p|th|td|li|dt|dd|pre|caption|input|textarea|button|body)[^>]*>/i", "\n\n", $linea);

		preg_match("/<meta[^<>]+charset=\"*([a-zA-Z0-9\-_]+)\"*[^<>]*>/i", $linea, $matches);
		$charset = isset($matches[1]) ? $matches[1] : '';
		$from_encoding = $this->guess_encoding(strip_tags($linea), $charset);
		$blog_encoding = $this->blog_encoding;

		$linea = strip_tags($linea, '<a>');
		$linea = $this->convenc($linea, $blog_encoding, $from_encoding);
		$p = explode("\n\n", $linea);

		foreach ($p as $para) {
			if(strpos($para, $pagelinkedto) !== false && preg_match("/^([^<>]*)(\<a[^<>]+[\"']" . preg_quote($pagelinkedto, '/') . "[\"'][^<>]*\>)([^<>]+)(\<\/a\>)(.*)$/i", $para, $context))
				break;
		}

		if(!$context)
			return $commentdata;

		$context[1] = strip_tags($context[1]);
		$context[5] = strip_tags($context[5]);
		$len_max = 250;
		$len_c3 = strlen($context[3]);

		if($len_c3 > $len_max) {
			$excerpt = mb_strcut($context[3], 0, 250, $blog_encoding);
		} else {
			$len_c1 = strlen($context[1]);
			$len_c5 = strlen($context[5]);
			$len_left = $len_max - $len_c3;
			$len_left_even = ceil($len_left / 2);

			if($len_left_even > $len_c1) {
				$context[5] = mb_strcut($context[5], 0, $len_left - $len_c1, $blog_encoding);
			}
			elseif($len_left_even > $len_c5) {
				$context[1] .= "\t\t\t\t\t\t";
				$context[1] = mb_strcut($context[1], $len_c1 - ($len_left - $len_c5), $len_c1 + 6, $blog_encoding);
				$context[1] = preg_replace("/\t*$/", '', $context[1]);
			}
			else {
				$context[1] .= "\t\t\t\t\t\t";
				$context[1] = mb_strcut($context[1], $len_c1 - $len_left_even, $len_c1 + 6, $blog_encoding);
				$context[1] = preg_replace("/\t*$/", '', $context[1]);
				$context[5] = mb_strcut($context[5], 0, $len_left_even, $blog_encoding);
			}

			$excerpt = $context[1] . $context[3] . $context[5];
		}

		$commentdata['comment_content'] = '[...] ' . esc_html($excerpt) . ' [...]';
		$commentdata['comment_content'] = addslashes($commentdata['comment_content']);
		$commentdata['comment_author'] = stripslashes($commentdata['comment_author']);
		$commentdata['comment_author'] = $this->convenc($commentdata['comment_author'], $blog_encoding, $from_encoding);
		$commentdata['comment_author'] = addslashes($commentdata['comment_author']);

		return $commentdata;
	}

	function preprocess_comment($commentdata) {
		if($commentdata['comment_type'] == 'trackback')
			return $this->incoming_trackback($commentdata);
		elseif($commentdata['comment_type'] == 'pingback')
			return $this->incoming_pingback($commentdata);
		else
			return $commentdata;
	}

	function is_almost_ascii($string, $encoding) {
		if(100 === $this->conf['ascii_threshold'])
			return false;

		return ($this->conf['ascii_threshold'] < round(@(mb_strlen($string, $encoding) / strlen($string)) * 100)) ? true : false;
	}

	function wp_trim_excerpt($text) {
		$raw_excerpt = $text;

		$blog_encoding = $this->blog_encoding;

		if('' == $text) {
			$text = get_the_content('');

			$text = strip_shortcodes( $text );

			$text = apply_filters('the_content', $text);
			$text = str_replace(']]>', ']]&gt;', $text);
			$text = strip_tags($text);
			$excerpt_length = apply_filters('excerpt_length', $this->conf['excerpt_length']);
			$excerpt_mblength = apply_filters('excerpt_mblength', $this->conf['excerpt_mblength']);
			$excerpt_more = apply_filters('excerpt_more', $this->conf['excerpt_more']);

			if($this->is_almost_ascii($text, $blog_encoding)) {
				$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);

				if ( count($words) > $excerpt_length ) {
					array_pop($words);
					$text = implode(' ', $words);
					$text = $text . $excerpt_more;
				} else {
					$text = implode(' ', $words);
				}
			}
			else {
				$text = trim(preg_replace("/[\n\r\t ]+/", ' ', $text), ' ');

				if(mb_strlen($text, $blog_encoding) > $excerpt_mblength)
					$text = mb_substr($text, 0, $excerpt_mblength, $blog_encoding) . $excerpt_more;
			}
		}

		return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
	}

	function trim_multibyte_excerpt($text = '', $length = 110, $more = ' [...]', $encoding = 'UTF-8') {
		$text = strip_shortcodes($text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$text = trim(preg_replace("/[\n\r\t ]+/", ' ', $text), ' ');

		if(mb_strlen($text, $encoding) > $length)
			$text = mb_substr($text, 0, $length, $encoding) . $more;

		return $text;
	}

	function bp_create_excerpt($text = '') {
		if($this->is_almost_ascii($text, $this->blog_encoding))
			return $text;
		else
			return $this->trim_multibyte_excerpt($text, $this->conf['bp_excerpt_mblength'], $this->conf['bp_excerpt_more'], $this->blog_encoding);
	}

	function bp_get_activity_content_body($content = '') {
		return preg_replace("/<a [^<>]+>([^<>]+)<\/a>(" . preg_quote($this->conf['bp_excerpt_more'], '/') . "<\/p>)$/", "$1$2", $content);
	}

	// param $excerpt could already be truncated to 20 words or less by the original get_comment_excerpt() function.
	function get_comment_excerpt($excerpt = '') {
		$excerpt = preg_replace("/\.\.\.$/", '', $excerpt);
		$blog_encoding = $this->blog_encoding;

		if($this->is_almost_ascii($excerpt, $blog_encoding)) {
			$words = explode(' ', $excerpt, $this->conf['comment_excerpt_length'] + 1);

			if(count($words) > $this->conf['comment_excerpt_length']) {
				array_pop($words);
				$excerpt = implode(' ', $words) . '...';
			}
		}
		elseif(mb_strlen($excerpt, $blog_encoding) > $this->conf['comment_excerpt_mblength']) {
			$excerpt = mb_substr($excerpt, 0, $this->conf['comment_excerpt_mblength'], $blog_encoding) . '...';
		}

		return $excerpt;
	}

	function sanitize_file_name($name) {
		$info = pathinfo($name);
		$ext = !empty($info['extension']) ? '.' . $info['extension'] : '';
		$name = str_replace($ext, '', $name);
		$name_enc = rawurlencode($name);
		$name = ($name == $name_enc) ? $name . $ext : md5($name) . $ext;
		return $name;
	}

	function excerpt_mblength($length) {
		if(isset($this->query_based_vars['excerpt_mblength']) && (int) $this->query_based_vars['excerpt_mblength'])
			return $this->query_based_vars['excerpt_mblength'];
		else
			return (int) $length;
	}

	function excerpt_more($more) {
		if(isset($this->query_based_vars['excerpt_more']))
			return $this->query_based_vars['excerpt_more'];
		else
			return $more;
	}

	function query_based_settings() {
		$is_query_funcs = array('is_feed', 'is_404', 'is_search', 'is_tax', 'is_front_page', 'is_home', 'is_attachment', 'is_single', 'is_page', 'is_category', 'is_tag', 'is_author', 'is_date', 'is_archive', 'is_paged');

		foreach($is_query_funcs as $func) {
			if(isset($this->conf['excerpt_mblength.' . $func]) && !isset($this->query_based_vars['excerpt_mblength']) && $func())
				$this->query_based_vars['excerpt_mblength'] = $this->conf['excerpt_mblength.' . $func];

			if(isset($this->conf['excerpt_more.' . $func]) && !isset($this->query_based_vars['excerpt_more']) && $func())
				$this->query_based_vars['excerpt_more'] = $this->conf['excerpt_more.' . $func];
		}
	}

	function import_l10n_entry($text, $from_domain, $to_domain = 'default') {
		global $l10n;

		if(isset($l10n[$to_domain]->entries) && isset($l10n[$from_domain]->entries[$text]))
			$l10n[$to_domain]->entries[$text] = $l10n[$from_domain]->entries[$text];
	}

	function filters() {
		// remove filter
		if(false !== $this->conf['patch_wp_trim_excerpt'])
			remove_filter('get_the_excerpt', 'wp_trim_excerpt');

		// add filter
		add_filter('preprocess_comment', array(&$this, 'preprocess_comment'), 99);
		add_filter('excerpt_mblength', array(&$this, 'excerpt_mblength'), 9);
		add_filter('excerpt_more', array(&$this, 'excerpt_more'), 9);

		if(false !== $this->conf['patch_incoming_pingback'])
			add_filter('pre_remote_source', array(&$this, 'pre_remote_source'), 10, 2);

		if(false !== $this->conf['patch_wp_trim_excerpt'])
			add_filter('get_the_excerpt', array(&$this, 'wp_trim_excerpt'));

		if(false !== $this->conf['patch_get_comment_excerpt'])
			add_filter('get_comment_excerpt', array(&$this, 'get_comment_excerpt'));

		if(false !== $this->conf['patch_sanitize_file_name'])
			add_filter('sanitize_file_name', array(&$this, 'sanitize_file_name'));

		if(false !== $this->conf['patch_bp_create_excerpt']) {
			add_filter('bp_create_excerpt', array(&$this, 'bp_create_excerpt'), 99);
			add_filter('bp_get_activity_content_body', array(&$this, 'bp_get_activity_content_body'), 99);
			}

		// add action
		add_action('wp', array(&$this, 'query_based_settings'));

		if(method_exists($this, 'process_search_terms') && false !== $this->conf['patch_process_search_terms'])
			add_action('sanitize_comment_cookies', array(&$this, 'process_search_terms'));

		if(method_exists($this, 'wp_mail') && false !== $this->conf['patch_wp_mail'])
			add_action('phpmailer_init', array(&$this, 'wp_mail'));

		if(method_exists($this, 'admin_custom_css') && false !== $this->conf['patch_admin_custom_css'])
			add_action('admin_head' , array(&$this, 'admin_custom_css'), 99);

		if(method_exists($this, 'wplink_js') && false !== $this->conf['patch_wplink_js'])
			add_action('wp_default_scripts' , array(&$this, 'wplink_js'), 9);

		if(method_exists($this, 'word_count_js') && false !== $this->conf['patch_word_count_js'])
			add_action('wp_default_scripts' , array(&$this, 'word_count_js'), 9);
	}

	function mbfunctions_exist() {
		return (
			function_exists('mb_convert_encoding') &&
			function_exists('mb_convert_kana') &&
			function_exists('mb_detect_encoding') &&
			function_exists('mb_strcut') &&
			function_exists('mb_strlen') &&
			function_exists('mb_substr')
		) ? true : false;
	}

	function activation_check() {
		global $wp_version;
		$required_version = $this->required_version;

		if(version_compare(substr($wp_version, 0, strlen($required_version)), $required_version, '<')) {
			deactivate_plugins(__FILE__);
			wp_die(sprintf(__('Sorry, WP Multibyte Patch requires WordPress %s or later.', 'wp-multibyte-patch'), $required_version));
		}
		elseif(!$this->has_mbfunctions) {
			deactivate_plugins(__FILE__);
			wp_die(__('Sorry, WP Multibyte Patch requires mbstring functions.', 'wp-multibyte-patch'));
		}
	}

	function load_conf() {
		$wpmp_conf = array();

		if(file_exists(WP_CONTENT_DIR . '/wpmp-config.php'))
			require_once(WP_CONTENT_DIR . '/wpmp-config.php');

		if(is_multisite()) {
			$blog_id = get_current_blog_id();
			if(file_exists(WP_CONTENT_DIR . '/wpmp-config-blog-' . $blog_id . '.php'))
				require_once(WP_CONTENT_DIR . '/wpmp-config-blog-' . $blog_id . '.php');
		}

		$this->conf = array_merge($this->conf, $wpmp_conf);
	}

	function __construct() {
		$this->load_conf();

		$this->blog_encoding = get_option('blog_charset');
		$this->has_mbfunctions = $this->mbfunctions_exist();

		load_textdomain($this->textdomain, plugin_dir_path(__FILE__) . $this->lang_dir . '/' . $this->textdomain . '-' . get_locale() . '.mo');
		register_activation_hook(__FILE__, array(&$this, 'activation_check'));

		$this->filters();
	}
}

if(defined('WP_PLUGIN_URL')) {
	global $wpmp;

	if(file_exists(dirname(__FILE__) . '/ext/' . get_locale() . '/class.php')) {
		require_once(dirname(__FILE__) . '/ext/' . get_locale() . '/class.php');
		$wpmp = new multibyte_patch_ext();
	}
	elseif(file_exists(dirname(__FILE__) . '/ext/default/class.php')) {
		require_once(dirname(__FILE__) . '/ext/default/class.php');
		$wpmp = new multibyte_patch_ext();
	}
	else
		$wpmp = new multibyte_patch();
}

?>