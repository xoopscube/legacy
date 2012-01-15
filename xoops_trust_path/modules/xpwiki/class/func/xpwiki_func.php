<?php
//
// Created on 2006/10/02 by nao-pon http://hypweb.net/
// $Id: xpwiki_func.php,v 1.250 2012/01/14 11:56:35 nao-pon Exp $
//
class XpWikiFunc extends XpWikiXoopsWrapper {

	// xpWiki functions.
	// These are functions that need not be overwrited.

	var $xpwiki;
	var $root;
	var $cont;
	var $pid;

	function XpWikiFunc (& $xpwiki) {
		$this->xpwiki = & $xpwiki;
		$this->root  = & $xpwiki->root;
		$this->cont = & $xpwiki->cont;
		$this->pid = $xpwiki->pid;
	}

	function load_ini() {
		$root = & $this->root;
		$const = & $this->cont;

		/////////////////////////////////////////////////
		// Require INI_FILE

		$const['INI_FILE'] = $const['DATA_HOME'] . 'private/ini/pukiwiki.ini.php';
		$die = '';
		if (! is_file($const['INI_FILE']) || ! is_readable($const['INI_FILE'])) {
			$die .= 'File is not found. (INI_FILE)' . "\n";
		} else {
			require($const['INI_FILE']);
			if (is_file($const['CACHE_DIR'] . 'pukiwiki.ini.php')) {
				include ($const['CACHE_DIR'] . 'pukiwiki.ini.php');
			}
		}

		if ($die) $this->die_message(nl2br("\n\n" . $die));

	}

	function init() {

		include(dirname(dirname(__FILE__))."/include/init.php");
	}

	function & get_plugin_instance ($name) {
		static $instance = array();

		if (is_null($name) && ! empty($instance[$this->xpwiki->pid])) {
			$keys = array_keys($instance[$this->xpwiki->pid]);
			foreach($keys as $key) {
				unset($instance[$this->xpwiki->pid][$key]);
			}
			$ret = NULL;
			return $ret;
		}

		if (!isset($instance[$this->xpwiki->pid][$name])) {
			if ($class = $this->exist_plugin($name)) {
				$instance[$this->xpwiki->pid][$name] =& new $class($this);
				$instance[$this->xpwiki->pid][$name]->name = $name;
				if ($this->plugin_init($name, $instance[$this->xpwiki->pid][$name]) === FALSE) {
					$this->die_message('Plugin init failed: ' . $name);
				}
			} else {
				$instance[$this->xpwiki->pid][$name] = false;
			}
		}

		return $instance[$this->xpwiki->pid][$name];
	}

	function get_plugin_filename ($name) {

		$files = array();
		if (is_file($this->root->mydirpath . '/private/plugin/' . $name . '.inc.php')) {
			$files['user'] = $this->root->mydirpath . '/private/plugin/' . $name . '.inc.php';
		}
		if (is_file($this->root->mytrustdirpath . '/plugin/' . $name . '.inc.php')) {
			$files['system'] = $this->root->mytrustdirpath . '/plugin/' . $name . '.inc.php';
		}
		return $files;
	}

	// Set global variables for plugins
	function set_plugin_messages($messages) {
		foreach ($messages as $name=>$val)
			if (! isset($this->root->$name))
				$this->root->$name = $val;
	}

	// Check plugin '$name' is here
	function exist_plugin($name) {
		//	global $vars;
		static $exist = array(), $count = array();

		$name = strtolower($name);
		if(isset($exist[$this->xpwiki->pid][$name])) {
			if (++$count[$this->xpwiki->pid][$name] > $this->cont['PKWK_PLUGIN_CALL_TIME_LIMIT'])
				die('Alert: plugin "' . htmlspecialchars($name) .
				'" was called over ' . $this->cont['PKWK_PLUGIN_CALL_TIME_LIMIT'] .
				' times. SPAM or someting?<br />' . "\n" .
				'<a href="' . $this->cont['HOME_URL'] . '?cmd=edit&amp;page='.
				rawurlencode($this->root->vars['page']) . '">Try to edit this page</a><br />' . "\n" .
				'<a href="' . $this->cont['HOME_URL'] . '">Return to frontpage</a>');
			return $exist[$this->xpwiki->pid][$name];
		}

		$plugin_files = $this->get_plugin_filename ($name);

		$exist[$this->xpwiki->pid][$name] = FALSE;
		$count[$this->xpwiki->pid][$name] = 1;
		$ret = FALSE;

		if (preg_match('/^\w{1,64}$/', $name) && $plugin_files ) {
			$ret =  FALSE;
			if (isset($plugin_files['system'])) {
				require_once($plugin_files['system']);
				$class_name = "xpwiki_plugin_{$name}";
				if (XC_CLASS_EXISTS($class_name)) {
					$count[$this->xpwiki->pid][$name] = 1;
					$ret = $class_name;
					if (isset($plugin_files['user'])) {
						require_once($plugin_files['user']);
						$class_name = "xpwiki_".$this->root->mydirname."_plugin_{$name}";
						if (XC_CLASS_EXISTS($class_name)) {
							$ret = $class_name;
						}
					}
					$ret = $class_name;
					$exist[$this->xpwiki->pid][$name] = $ret;
					$count[$this->xpwiki->pid][$name] = 1;
				}
			}
		}
		return $ret;
	}

	// Check if plugin API 'action' exists
	function exist_plugin_action($name) {
		$plugin = & $this->get_plugin_instance($name);
		return	is_object($plugin) ? method_exists($plugin, 'plugin_' . $name . '_action') : FALSE;
	}

	// Check if plugin API 'convert' exists
	function exist_plugin_convert($name) {
		$plugin = & $this->get_plugin_instance($name);
		return	is_object($plugin) ? method_exists($plugin, 'plugin_' . $name . '_convert') : FALSE;
	}

	// Check if plugin API 'inline' exists
	function exist_plugin_inline($name) {
		$plugin = & $this->get_plugin_instance($name);
		return	is_object($plugin) ? method_exists($plugin, 'plugin_' . $name . '_inline') : FALSE;
	}

	// Do init the plugin
	function plugin_init($name, & $plugin) {
		static $checked = array();

		if (isset($checked[$this->xpwiki->pid][$name])) return $checked[$this->xpwiki->pid][$name];

		$func = 'plugin_' . $name . '_init';
		if (method_exists($plugin, $func)) {
			// TRUE or FALSE or NULL (return nothing)
			$checked[$this->xpwiki->pid][$name] = call_user_func(array(& $plugin, $func));
		} else {
			$checked[$this->xpwiki->pid][$name] = NULL; // Not exist
		}

		return $checked[$this->xpwiki->pid][$name];
	}

	// Compatibility
	function do_plugin_init($name) {

		$plugin = & $this->get_plugin_instance($name);

		return $this->plugin_init($name, $plugin);
	}

	// Call API 'action' of the plugin
	function do_plugin_action($name) {
		if (! $this->exist_plugin_action($name)) return array();

		// 実行プラグイン名を登録
		$this->root->plugin_stack[] = $name;

		$plugin = & $this->get_plugin_instance($name);

		// ブラウザとのコネクションが切れても実行し続ける
		$_iua = ignore_user_abort(TRUE);

		$this->root->rtf['page_touch'] = array();
		$retvar = call_user_func(array(& $plugin, 'plugin_' . $name . '_action'));
		$this->plugin_page_touch($name);

		// ignore_user_abort の設定値戻し
		ignore_user_abort($_iua);

		// 実行プラグイン名を抹消
		array_pop($this->root->plugin_stack);

		// exit 指定
		if (is_array($retvar) && isset($retvar['exit'])) {
			$this->clear_output_buffer();
			exit($retvar['exit']);
		}

		if (is_array($retvar) && isset($retvar['body'])) {
			// Insert a hidden field, supports idenrtifying text enconding
			if ($this->cont['PKWK_ENCODING_HINT']) {
				$retvar['body'] =  preg_replace('/(<form[^>]*>)/', '$1' . "\n" .
					'<div><input type="hidden" name="encode_hint" value="' .
					$this->cont['PKWK_ENCODING_HINT'] . '" /></div>', $retvar['body']);
			}
			// set meta_description
			$this->root->meta_description = $this->get_description($retvar['body'], $this->root->description_max_length_meta);
		}

		return $retvar;
	}

	// Call API 'convert' of the plugin
	function do_plugin_convert($name, $args = '', $body = NULL) {

		$plugin = & $this->get_plugin_instance($name);

		if (! $this->cont['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK']) {
			// Multiline plugin?
			$pos  = strpos($args, "\r"); // "\r" is just a delimiter
			if ($pos !== FALSE) {
				$body = substr($args, $pos + 1);
				$args = substr($args, 0, $pos);
			}
		}

		if ($args === '') {
			$aryargs = array();                 // #plugin()
		} else {
			$aryargs = $this->csv_explode(',', $args); // #plugin(A,B,C,D)
		}

		if ($aryargs && $_num = $plugin->can_call_otherdir_convert()) {
			// Other xpWiki dir
			$_num = intval($_num) - 1;
			if (isset($aryargs[$_num]) && intval(strpos($aryargs[$_num], ':')) > 0) {
				list($dir, $arg) = explode(':', $aryargs[$_num], 2);
				if ($this->root->mydirname === $dir) {
					$aryargs[$_num] = $arg;
				} else if ($this->isXpWikiDirname($dir)) {
					$other = & XpWiki::getInitedSingleton($dir);
					if ($other->isXpWiki) {
						$aryargs[$_num] = $arg;
						$plugin->swap_global_vars($this, $other);
						$ret = $other->func->do_plugin_convert($name, $this->csv_implode(',', $aryargs), $body);
						$plugin->swap_global_vars($this, $other);
						return '<div class="xpwiki_r_' . $dir . '">' . $ret . '</div>';
					}
				}
			}
		}

		if (! $this->cont['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK']) {
			if (!is_null($body)) $aryargs[] = $body;     // #plugin(){{body}}
		}

		// 実行プラグイン名を登録
		$this->root->plugin_stack[] = $name;

		$_digest = $this->root->digest;
		$retvar  = call_user_func_array(array(& $plugin, 'plugin_' . $name . '_convert'), $aryargs);
		$this->root->digest  = $_digest; // Revert

		// 実行プラグイン名を抹消
		array_pop($this->root->plugin_stack);

		if ($retvar === FALSE) {
			return htmlspecialchars('#' . $name .
				($args != '' ? '(' . $args . ')' : ''));
		}

		if ($this->cont['PKWK_ENCODING_HINT'] !== '' && strpos($retvar, '<form') !== false) {
			// Insert a hidden field, supports idenrtifying text enconding
			$retvar = preg_replace('/(<form[^>]*>)/', '$1 ' . "\n" .
				'<div><input type="hidden" name="encode_hint" value="' .
				$this->cont['PKWK_ENCODING_HINT'] . '" /></div>', $retvar);
		}

		if (in_array($name, $this->root->description_ignore_blocks)) {
			return $this->wrap_description_ignore($retvar);
		} else {
			return $retvar;
		}
	}

	// Call API 'inline' of the plugin
	function do_plugin_inline($name, $args = '', $body = '') {
		$plugin = & $this->get_plugin_instance($name);

		if ($args !== '') {
			$aryargs = $this->csv_explode(',', $args);
		} else {
			$aryargs = array();
		}

		if ($aryargs && $_num = $plugin->can_call_otherdir_inline()) {
			// Other xpWiki dir
			$_num = intval($_num) - 1;
			if (intval(strpos($aryargs[$_num], ':')) > 0) {
				list($dir, $arg) = explode(':', $aryargs[$_num], 2);
				if ($this->root->mydirname === $dir) {
					$aryargs[$_num] = $arg;
				} else if ($this->isXpWikiDirname($dir)) {
					$other = & XpWiki::getInitedSingleton($dir);
					if ($other->isXpWiki) {
						$aryargs[$_num] = $arg;
						$plugin->swap_global_vars($this, $other);
						$ret = $other->func->do_plugin_inline($name, $this->csv_implode(',', $aryargs), $body);
						$plugin->swap_global_vars($this, $other);
						return $ret;
					}
				}
			}
		}

		// NOTE: A reference of $body is always the last argument
		$aryargs[] = $body; // func_num_args() != 0

		// 実行プラグイン名を登録
		$this->root->plugin_stack[] = $name;

		$_digest = $this->root->digest;
		$retvar  = call_user_func_array(array(& $plugin, 'plugin_' . $name . '_inline'), $aryargs);
		$this->root->digest  = $_digest; // Revert

		// 実行プラグイン名を抹消
		array_pop($this->root->plugin_stack);

		if($retvar === FALSE) {
			// Do nothing
			return htmlspecialchars('&' . $name . ($args ? '(' . $args . ')' : '') . ';');
		} else {
			if (in_array($name, $this->root->description_ignore_inlines)) {
				return $this->wrap_description_ignore($retvar);
			} else {
				return $retvar;
			}
		}
	}

	function plugin_page_touch($name) {
		if (! empty($this->root->rtf['page_touch'])) {
			foreach($this->root->rtf['page_touch'] as $page => $log) {
				$log = join(', ', $log);
				if ($this->is_page($page)) {
					if ($log) {
						$this->root->rtf['esummary'] = $log; // $log require htmlspecialchars()
						$this->push_page_changes($page, $log);
					} else {
						$this->root->rtf['esummary'] = str_replace('$name', $name, $this->root->plugin_edit_summary);
					}
					$this->touch_page($page, FALSE, TRUE);
				}
			}
		}
	}

	// Get HTTP_ACCEPT_LANGUAGE
	function get_accept_language () {
		$accept = @ $_SERVER["HTTP_ACCEPT_LANGUAGE"];
		$allows = explode(',', $this->cont['ACCEPT_UILANG']);
		$allows = array_map('trim', $allows);
		$allowall = (in_array('all', $allows));
		// cookie に指定があればそれを優先
		if (!empty($this->root->cookie['lang'])) {
			$accept = $this->root->cookie['lang'] . "," . $accept;
		}
		if (!empty($accept))
		{
			if (preg_match_all("/([\w\-]+)/i",$accept,$match,PREG_PATTERN_ORDER)) {
				foreach($match[1] as $lang) {
					$lang = strtolower($lang);
					if ($allowall || in_array(substr($lang, 0, 2), $allows)) {
						if (is_file($this->root->mytrustdirpath."/language/xpwiki/{$lang}/lng.php")) {
							return $lang;
						}
						if (strpos($lang, '-') !== FALSE) {
							$lang = preg_replace('/-.+$/', '', $lang);
							if (is_file($this->root->mytrustdirpath."/language/xpwiki/{$lang}/lng.php")) {
								return $lang;
							}
						}
					}
				}
			}
		}
		return $this->cont['LANG']; // 規定値
	}

	function get_zone_by_time ($time) {
		$zones = array(
			-8=>"PST",
			-7=>"MST",
			-6=>"CST",
			-5=>"EST",
			-4=>"AST",
			0=>"GMT",
			1=>"CET",
			2=>"EET",
			9=>"JST",
		);
		if (isset($zones[$time])) { return $zones[$time]; }
		$time_string = ($time === 0)? "" : ($time > 0)? ("+".$time) : (string)$time;
		return "UTC".$time_string;
	}

	function cookie_sanitizer($str) {
		$str = preg_replace('/[\x00-\x1f\x7f]+/', '', $str);
		$str = trim($str);
		return $str;
	}

	function load_cookie () {
		$cookies = array();
		if (isset($_COOKIE[$this->root->mydirname])) {
			$cookies = explode("\t", $_COOKIE[$this->root->mydirname]);
		}
		$this->root->cookie['ucd'] = (isset($cookies[0])) ? $this->cookie_sanitizer($cookies[0]) : '';
		$this->root->cookie['name'] = (isset($cookies[1])) ? $this->cookie_sanitizer($cookies[1]) : '';
		$this->root->cookie['skin'] = (isset($cookies[2])) ? $this->cookie_sanitizer($cookies[2]) : '';
		$this->root->cookie['lang'] = (isset($cookies[3])) ? $this->cookie_sanitizer($cookies[3]) : '';
		if (empty($this->root->userinfo['uname'])) {
			if (empty($this->root->cookie['name'])) {
				$this->root->userinfo['uname'] = $this->root->siteinfo['anonymous'];
			} else {
				$this->root->userinfo['uname'] = $this->root->cookie['name'];
			}
			$this->root->userinfo['uname_s'] = htmlspecialchars($this->root->userinfo['uname']);
		}

		// 他の言語切り替えシステムをチェック
		if (!empty($this->cont['SETLANG_C']) && !empty($this->root->cookie[$this->cont['SETLANG_C']])) {
			if (preg_match($this->cont['ACCEPT_LANG_REGEX'], $this->root->cookie[$this->cont['SETLANG_C']], $match)) {
				$this->root->cookie['lang'] = $match[1];
			}
		}
	}

	function save_cookie () {
		$data =    $this->root->cookie['ucd'].
			"\t" . (($this->root->siteinfo['anonymous'] === $this->root->cookie['name'])? '' : $this->root->cookie['name']).
			"\t" . $this->root->cookie['skin'].
			"\t" . $this->root->cookie['lang'];
		$url = parse_url ( $this->cont['ROOT_URL'] );
		setcookie($this->root->mydirname, $data, $this->cont['UTC']+86400*365, $url['path']); // 1年間
	}

	function load_usercookie () {

		static $sendcookie = false;

		// cookieの読み込み
		$this->load_cookie();

		// user-codeの発行
		if(!$this->root->cookie['ucd']){
			$this->root->cookie['ucd'] = md5(getenv("REMOTE_ADDR"). __FILE__ .gmdate("Ymd", $this->cont['UTC']+9*60*60));
		}
		$this->root->userinfo['ucd'] = substr(crypt($this->root->cookie['ucd'],($this->root->adminpass)? $this->root->adminpass : 'id'),-11);

		// スキン指定をcookieにセット
		if (isset($this->root->get['setskin'])) {
			$this->root->cookie['skin'] = $this->root->get['setskin'];
			if (isset($_SERVER['QUERY_STRING'])) {
				$_SERVER['QUERY_STRING'] = preg_replace("/(^|&)setskin=.*?(?:&|$)/","$1",$_SERVER['QUERY_STRING']);
			}
			if (isset($_SERVER['argv'][0])) {
				$_SERVER['argv'][0] = preg_replace("/(^|&)setskin=.*?(?:&|$)/","$1",$_SERVER['argv'][0]);
			}
		}
		// 正規化
		$skin = preg_replace('#([\w-]+)#', '$1', $this->root->cookie['skin']);
		if (substr($skin, 0, 3) === 'tD-') {
			$skin_dir = $this->cont['DATA_HOME'] . 'skin/tdiary_theme/' . substr($skin, 3);
		} else {
			$skin_dir = $this->cont['DATA_HOME'] . 'skin/' . $skin;
		}
		if (is_dir($skin_dir)) {
			$this->root->cookie['skin'] = $skin;
		} else {
			$this->root->cookie['skin'] = '';
		}

		// 言語指定をcookieにセット
		if (isset($this->root->get[$this->cont['SETLANG']])) {
			$this->root->cookie['lang'] = '';
			// 正規化
			if (preg_match($this->cont['ACCEPT_LANG_REGEX'], $this->root->get[$this->cont['SETLANG']], $match)) {
				$this->root->cookie['lang'] = $match[1];
			}
			//$this->root->cookie['lang'] = ($this->root->get[$this->cont['SETLANG']] === "none")? "" : preg_replace("/[^\w-]+/","",$this->root->get[$this->cont['SETLANG']]);
			if (isset($_SERVER['QUERY_STRING'])) {
				$_SERVER['QUERY_STRING'] = preg_replace("/(^|&)".preg_quote($this->cont['SETLANG'],"/")."=.*?(?:&|$)/","$1",$_SERVER['QUERY_STRING']);
			}
			if (isset($_SERVER['argv'][0])) {
				$_SERVER['argv'][0] = preg_replace("/(^|&)".preg_quote($this->cont['SETLANG'],"/")."=.*?(?:&|$)/","$1",$_SERVER['argv'][0]);
			}
		}

		// cookieを更新
		if (!$sendcookie) {	$this->save_cookie(); }
		$sendcookie = TRUE;
	}

	function save_name2cookie (& $name) {
		$name = $this->cookie_sanitizer($name);
		$this->root->cookie['name'] = $name;
		$this->save_cookie();
	}

	function get_body ($page) {

		// キャッシュ判定
		$r_mode = ($this->root->render_mode === 'block')? 'b_' : '';
		$cache_file = $this->cont['CACHE_DIR']."page/".$r_mode.$this->encode($page).".".$this->cont['UI_LANG'];

		// 強制キャッシュ利用指定フラグ判定
		if (!empty($this->root->rtf['use_cache_always'])) {
			// ゲスト扱いに固定
			$_userinfo = $this->root->userinfo; // Backup
			$this->root->userinfo['admin'] = FALSE;
			$this->root->userinfo['uid'] = 0;
			$this->root->userinfo['uname'] = '';
			$this->root->userinfo['uname_s'] = '';
			$this->root->userinfo['gids'] = array();

			$_uaprofile = $this->cont['UA_PROFILE'];
			$this->cont['UA_PROFILE'] = 'default';

			if (is_file($cache_file)) {
				$use_cache = TRUE;
			} else {
				$use_cache = FALSE;
			}
		} else {
			$use_cache = ($this->root->userinfo['uid'] === 0 && $this->root->pagecache_min > 0 && is_file($cache_file) && (filemtime($cache_file) + $this->root->pagecache_min * 60) > $this->cont['UTC']);
		}

		if ($use_cache) {
			// キャッシュ利用
			if ($cache_dat = unserialize(file_get_contents($cache_file))){
				if (!is_array(@ $GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'])) {
					$GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'] = array();
				}
				$GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'] = array_merge_recursive($GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'], is_array($cache_dat['globals'])? $cache_dat['globals'] : array());
				$body = $cache_dat['body'];
				foreach ($cache_dat['root'] as $_key=>$_val) {
					$this->root->$_key = $_val;
				}
				foreach ($cache_dat['cont'] as $_key=>$_val) {
					$this->cont[$_key] = $_val;
				}
				if ($this->root->pagecache_profiles) {
					$use_cache = in_array($this->cont['UA_PROFILE'], explode(',', str_replace(' ', '', $this->root->pagecache_profiles)));
				}
			} else {
				$use_cache = false;
			}
		}
		if (! $use_cache) {
			// 通常のレンダリング
			$this->root->rtf['convert_nest'] = 0;
			$body = $this->convert_html($this->get_source($page));
			$this->root->content_title = $this->get_heading($page);
			// キャッシュ保存
			if ($this->cont['UA_PROFILE'] === 'default' && (! empty($this->root->rtf['use_cache_always']) || ($this->root->userinfo['uid'] === 0 && $this->root->pagecache_min > 0))) {
				$fp = fopen($cache_file, "wb");
				fwrite($fp, serialize(
					array(
						'body'          => $this->strip_MyHostUrl($body),
						'root'          => array(
							'foot_explain'  => $this->strip_MyHostUrl($this->root->foot_explain),
							'head_pre_tags' => $this->strip_MyHostUrl($this->root->head_pre_tags),
							'head_tags'     => $this->strip_MyHostUrl($this->root->head_tags),
							'related'       => $this->root->related,
							'runmode'       => $this->root->runmode,
							'content_title' => $this->root->content_title,
							'nonflag'       => $this->root->nonflag,
							'replaces_finish'=> $this->root->replaces_finish,
							'pagecache_profiles' => $this->root->pagecache_profiles,
							'meta_description' => $this->root->meta_description
						),
						'cont'          => array(
							'SKIN_CHANGER'  => $this->cont['SKIN_CHANGER']
						),
						'globals'       => (is_array(@ $GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'])? $GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'] : array())
					)
				));
				fclose($fp);
				clearstatcache();
			}
		}
		if (!empty($this->root->rtf['use_cache_always'])) {
			$this->root->userinfo = $_userinfo; // Restore
			$this->cont['UA_PROFILE'] = $_uaprofile;
		}
		return $body;
	}

	function clear_page_cache ($page) {
		// meta description
		$this->cache_del_db($this->get_pgid_by_name($page), 'core:description');

		// page render html cache
		$base = $this->root->mytrustdirpath."/language/xpwiki";
		if ($handle = opendir($base)) {
			$clr_pages = $this->root->always_clear_cache_pages;
			$clr_pages[] = $page;
			if ($this->root->clear_cache_parent)
			{
				// 上位層のページ
				while (strpos($page,"/") !== FALSE) {
					$page = dirname($page);
					$clr_pages[] = $page;
				}
			}
			while ($file = readdir($handle)) {
				if (preg_match('/^[\w]{2}(-[\w]+)?(_utf8)?$/',$file)) {
					foreach ($clr_pages as $_page) {
						// meta description
						$this->cache_del_db($this->get_pgid_by_name($_page), 'core:description');
						@unlink ($this->cont['CACHE_DIR']."page/".$this->encode($_page).".".$file);
						@unlink ($this->cont['CACHE_DIR']."page/b_".$this->encode($_page).".".$file);
						@unlink ($this->cont['CACHE_DIR']."page/r_".$this->encode($_page).".".$file);
					}
				}
			}
			closedir($handle);
		}
		// fusen xml cache
		if ($fusen = $this->get_plugin_instance('fusen')) {
			@ unlink($this->cont['CACHE_DIR'] . "plugin/fusen_". $this->get_pgid_by_name($page) . ".pcache.xml");
			if ($fusen_data = $fusen->plugin_fusen_data($page)) {
				$fusen->plugin_fusen_putjson($fusen_data, $page);
			}
		}
		return ;
	}

	function get_description ($html, $len = 250, $pre_crop = 512000) {

		$ret = substr($html, 0, $pre_crop);

		$ret = preg_replace('#<h([123]).+?/h\\1>|<form.+?/form>|<!--description ignore-->.+?<!--/description ignore-->#is', '', $ret);
		$ret = strip_tags($ret);
		$ret = str_replace(array('&#8203;', "\r", "\n"), '', $ret);
		$ret = str_replace(array('&nbsp;'), ' ', $ret);
		$ret = trim(preg_replace('/\s{2,}/', ' ', $ret));

		$ret = $this->substr_entity($ret, 0, $len);

		return $ret;
	}

	function get_description_cache($page, $len = null, $html = '') {
		$pgid = $this->get_pgid_by_name($page);
		$ttl_check = ($html);
		$description = $this->cache_get_db($pgid, 'core:description', false, false, $ttl_check);
		if (! $description && $html) {
			$description = $this->get_description($html, $this->root->description_max_length_save);
			$this->put_description_cache($description, $pgid);
		}
		if (! is_null($len)) {
			$description = $this->substr_entity($description, 0, $len);
		}
		return $description;
	}

	function put_description_cache($description, $pgid) {
		$this->cache_save_db($description, 'core:description', 864000, $pgid);
	}

	function wrap_description_ignore ($html) {
		return "\n" . '<!--description ignore-->' . "\n" . $html . "\n" . '<!--/description ignore-->' . "\n";
	}

	function get_additional_headtags () {

		if (! empty($this->root->head_tags)) {
			foreach($this->root->head_tags as $_key => $_val) {
				if (strpos($_val, ',') !== false && preg_match('/src=(.+?)\.(?:css|js)/', $_val, $match)) {
					$_files = explode(',', $match[1]);
					sort($_files);
					$_val = preg_replace('/^(.+?src=).+?(\.(?:css|js).+?)$/', "$1".join(',', $_files)."$2", $_val);
					$this->root->head_tags[$_key] = $_val;
				}
			}
		}

		// WikiHelper JavaScript
		$this->root->head_tags['3.default.js'] = '<script type="text/javascript" src="'.$this->cont['LOADER_URL'].'?src=default.'.$this->cont['UI_LANG'].$this->cont['FILE_ENCORD_EXT'].'.js"></script>';

		// key sort (CSS fast)
		ksort($this->root->head_pre_tags);
		ksort($this->root->head_tags);

		// Pre Tags
		$head_pre_tag = ! empty($this->root->head_pre_tags) ? join("\n", $this->root->head_pre_tags) ."\n" : '';

		// Tags will be inserted into <head></head>
		$head_tag = join("\n", $this->root->head_tags) ."\n";

		// Clear
		//$this->root->head_pre_tags = $this->root->head_tags = array();

		return array($head_pre_tag, $head_tag);
	}

	function set_hyp_preload_head_tag($tag) {

		static $tags = array();

		if (! isset($GLOBALS['hyp_preload_head_tag'])) return false;

		if (preg_match_all('#<([a-zA-Z]+).+?/\\1>|<[^>]+?>#s', $tag, $match, PREG_PATTERN_ORDER)) {
			$instags = array();
			foreach($match[0] as $ins) {
				$ins = str_replace($this->cont['MY_HOST_URL'], '', $ins);
				if (! isset($tags[$ins])) {
					$tags[$ins] = true;
					$instags[] = $ins;
				}
			}
			if ($instags) {
				$instags = "\n" . join("\n", $instags);
				$GLOBALS['hyp_preload_head_tag'] .= $instags;
			}
		}

		return true;

	}

	// ページ情報を得る
	function get_pginfo ($page = '', $src = '', $cache_clr = FALSE) {
		static $info = array();

		$pginfo = array();

		if ($cache_clr) {
			if ($page && $src === FALSE) {
				unset($info[$this->root->mydirname][$page]);
				return;
			}
			$info[$this->root->mydirname] = array();
			if ($page === '') { return; }
		}

		if ($page === '') $page = '#';

		if ($src === '' && isset($info[$this->root->mydirname][$page])) { return $info[$this->root->mydirname][$page]; }

		if ($src) {
			if (is_array($src)) {
				$src = join('', $src);
			}
		} else if (strpos($page, '#') === FALSE){
			$src = $this->get_source($page, TRUE, 4096);
		} else {
			$src = '';
		}

		// inherit = 0:継承指定なし, 1:規定値継承指定, 2:強制継承指定, 3:規定値継承した値, 4:強制継承した値
		if ($src && preg_match("/^#pginfo\((.+)\)\s*/m", $src, $match)) {
			$_tmp = array_pad(explode("\t",$match[1]), 14, '');
			$pginfo['uid']       = (int)$_tmp[0];
			$pginfo['ucd']       = $_tmp[1];
			$pginfo['uname']     = $_tmp[2];      // already htmlspecialchars()
			$pginfo['einherit']  = (int)$_tmp[3];
			$pginfo['eaids']     = $_tmp[4];
			$pginfo['egids']     = $_tmp[5];
			$pginfo['vinherit']  = (int)$_tmp[6];
			$pginfo['vaids']     = $_tmp[7];
			$pginfo['vgids']     = $_tmp[8];
			$pginfo['lastuid']   = (int)$_tmp[9];
			$pginfo['lastucd']   = $_tmp[10];
			$pginfo['lastuname'] = $_tmp[11];     // already htmlspecialchars()
			$pginfo['pgorder']   = min(9, max(0, ($_tmp[12] === '')? 1 : floatval($_tmp[12])));
			$pginfo['esummary']  = $_tmp[13];
		} else {
			$pginfo = $this->pageinfo_inherit($page);
			$pginfo['pgorder']   = 1;
			$pginfo['esummary']  = '';

			if (!$this->is_page($page))
			{
				$pginfo['uid'] = $this->root->userinfo['uid'];
				$pginfo['ucd'] = $this->root->userinfo['ucd'];
				$pginfo['uname'] = $this->root->userinfo['uname_s'];
			}
			$pginfo['pgorder'] = 1;
		}

		if ($pginfo['uid'] && !$pginfo['uname']) {
			$_uinfo = $this->get_userinfo_by_id($pginfo['uid']);
			$pginfo['uname'] = $_uinfo['uname_s'];
		}
		if ($pginfo['lastuid'] && !$pginfo['lastuname']) {
			$_uinfo = $this->get_userinfo_by_id($pginfo['lastuid']);
			$pginfo['lastuname'] = $_uinfo['uname_s'];
		}

		$pginfo['reading'] = '';
		if ($page) {
			$info[$this->root->mydirname][$page] = $pginfo;
		}
		return $pginfo;
	}

	// ページ情報の継承を受ける(指定があれば)
	function pageinfo_inherit ($page) {
		$is_page = $this->is_page($page);

		// サイト規定値読み込み
		$pginfo = $this->root->pginfo;

		// Check is user page
		$user_check = $this->page_dirname($page);
		if (! $user_check) $user_check = '/';
		if (in_array($user_check, $this->root->user_pages)) {
			$uname = $this->page_basename($page);
			$pginfo['uid'] = $this->get_uid_by_uname($uname);
			$pginfo['uname']     = htmlspecialchars($uname);
			$pginfo['einherit']  = 1;
			$pginfo['eaids']     = $pginfo['uid'];
			$pginfo['egids']     = 'none';
			$pginfo['vinherit']  = 1;
			if ($user_check === $this->cont['PKWK_CONFIG_PREFIX'] . $this->cont['PKWK_CONFIG_USER']) {
				$pginfo['vaids']     = $pginfo['uid'];
				$pginfo['vgids']     = 'none';
			} else {
				$pginfo['vaids']     = 'all';
				$pginfo['vgids']     = 'all';
			}
			return $pginfo;
		}

		// Noteページ?
		if (strpos($page, $this->root->notepage . '/') === 0) {
			$page = substr($page, strlen($this->root->notepage) + 1) . '/';
		}

		$done['edit'] = $done['view'] = 0;
		while ($done['edit'] < 2 && $done['view'] < 2) {
			if (strpos($page, '/') !== FALSE) {
				//上位ページを見る
				$uppage = $this->page_dirname($page);
				$_pginfo = $this->get_pginfo($uppage);
				// 編集権限
				if ($done['edit'] < 2) {
					if ($_pginfo['einherit'] === 2 || $_pginfo['einherit'] === 4) {
						$pginfo['einherit'] = 4;
						if ($_pginfo['eaids'] === 'none' && $_pginfo['uid'] && !$is_page) {
							$pginfo['eaids'] = $_pginfo['uid'];
						} else {
							$pginfo['eaids'] = $_pginfo['eaids'];
						}
						$pginfo['egids'] = $_pginfo['egids'];
						$done['edit'] = 2;
					}
					if (!$done['edit'] && ($_pginfo['einherit'] === 1 || $_pginfo['einherit'] === 3)) {
						$pginfo['einherit'] = 3;
						if ($_pginfo['eaids'] === 'none' && $_pginfo['uid'] && !$is_page) {
							$pginfo['eaids'] = $_pginfo['uid'];
						} else {
							$pginfo['eaids'] = $_pginfo['eaids'];
						}
						$pginfo['egids'] = $_pginfo['egids'];
						$done['edit'] = 1;
					}
				}
				// 閲覧権限
				if ($done['view'] < 2) {
					if ($_pginfo['vinherit'] === 2 || $_pginfo['vinherit'] === 4) {
						$pginfo['vinherit'] = 4;
						if ($_pginfo['vaids'] === 'none' && $_pginfo['uid'] && !$is_page) {
							$pginfo['vaids'] = $_pginfo['uid'];
						} else {
							$pginfo['vaids'] = $_pginfo['vaids'];
						}
						$pginfo['vgids'] = $_pginfo['vgids'];
						$done['view'] = 2;
					}
					if (!$done['view'] && ($_pginfo['vinherit'] === 1 || $_pginfo['vinherit'] === 3)) {
						$pginfo['vinherit'] = 3;
						if ($_pginfo['vaids'] === 'none' && $_pginfo['uid'] && !$is_page) {
							$pginfo['vaids'] = $_pginfo['uid'];
						} else {
							$pginfo['vaids'] = $_pginfo['vaids'];
						}
						$pginfo['vgids'] = $_pginfo['vgids'];
						$done['view'] = 1;
					}
				}
				// さらに上階層
				$page = $uppage;
			} else {
				// 上階層なし
				$done['edit'] = 2;
				$done['view'] = 2;
			}
		}
		return $pginfo;
	}

	// グループ選択フォーム作成
	function make_grouplist_form ($tagname, $ids = array(), $disabled='', $js='', $attr = '') {
		$groups = $this->get_group_list();
		$mygroups = $this->get_mygroups();

		//$disabled = ($disabled)? ' disabled="disabled"' : '';

		$size = min(10, count($groups));
		$ret = '<select'.$attr.' size="'.$size.'" name="'.$tagname.'[]" id="'.$tagname.'[]" multiple="multiple"'.$disabled.$js.'>'."\n";
		$all = FALSE;
		if ($ids === 'all' || $ids === 'none') {
			$ids = array();
		}
		foreach ($groups as $gid => $gname){
			if ($this->root->userinfo['admin'] || in_array($gid,$mygroups)){
				$sel = (in_array($gid,$ids))? ' selected="selected"' : '';
				$ret .= '<option value="'.$gid.'"'.$sel.'>'.$gname.'</option>';
			}
		}
		$ret .= '</select>';
		return $ret;
	}

	// ページオーナー権限があるかどうか
	function is_owner ($page, $uid = NULL) {
		if (is_null($uid)) {
			$userinfo = $this->root->userinfo;
		} else {
			$uid = intval($uid);
			$userinfo = $this->get_userinfo_by_id($uid);
		}

		if ($userinfo['admin']) { return TRUE; }
		$pginfo = $this->get_pginfo($page);
		if ($this->is_page($page) && $pginfo['uid'] && ($pginfo['uid'] === $userinfo['uid'])) { return TRUE; }
		return FALSE;
	}

	// 管理者のみ編集可能か
	function is_editable_only_admin ($page) {
		if ($this->root->render_mode === 'render') return FALSE;
		if ($this->cont['PKWK_READONLY'] === 1) return TRUE;
		$pginfo = $this->get_pginfo($page);
		$owner_ok = (! $pginfo['uid'] || $this->check_admin($pginfo['uid']));
		return ($owner_ok &&
			(($this->check_admin_gids($pginfo['egids']) && $this->check_admin_aids($pginfo['eaids']))
			|| $this->is_freeze($page)));
	}

	// 与えられた $pginfo['eaids'] or $pginfo['vaids'] がすべて管理者であるかチェック
	function check_admin_aids ($aids) {
		if ($aids === 'none') return TRUE;
		$aids = explode('&', trim($aids, '&'));
		if (! $aids) return FALSE;
		foreach($aids as $id) {
			if (! $this->check_admin($id)) {
				return FALSE;
			}
		}
		return TRUE;
	}

	// 与えられた $pginfo['egids'] or $pginfo['vgids'] がすべて管理者であるかチェック
	function check_admin_gids ($gids) {
		if ($gids === 'none') return TRUE;
		$gids = explode('&', trim($gids, '&'));
		if (! $gids) return FALSE;
		foreach($gids as $id) {
			if (! $this->check_admin_group($id)) {
				return FALSE;
			}
		}
		return TRUE;
	}

	// ページ毎閲覧権限チェック
	function check_readable_page ($page, $auth_flag = TRUE, $exit_flag = TRUE, $uid = NULL, $checkOwn = TRUE) {

		if (is_null($uid) && ! $this->root->module['checkRight']) {
			// for renderer mode
			$this->root->rtf['disable_render_cache'] = TRUE;
			return FALSE;
		}

		if ($checkOwn && $this->is_owner($page, $uid)) {
			// for renderer mode
			$this->root->rtf['disable_render_cache'] = TRUE;
			return TRUE;
		}

		if (is_null($uid)) {
			$userinfo = $this->root->userinfo;
		} else {
			$uid = intval($uid);
			$userinfo = $this->get_userinfo_by_id($uid);
		}

		$ret = FALSE;
		// #pginfo
		$pginfo = $this->get_pginfo($page);
		if ($pginfo['vgids'] === 'none' && $pginfo['vaids'] === 'none') {
			$ret = FALSE;
		} else {
			$vgids = explode('&', trim($pginfo['vgids'], '&'));
			$vaids = explode('&', trim($pginfo['vaids'], '&'));
			$_vg = array_merge($vgids, $userinfo['gids']);
			$vgauth = (count($_vg) === count(array_unique($_vg)))? FALSE : TRUE;
			if (
				$pginfo['vgids'] === 'all' ||
				$pginfo['vaids'] === 'all' ||
				$vgauth ||
				in_array((string)$userinfo['uid'], $vaids, true)
			) {
				$ret = TRUE;
			}
		}
		if ($ret) {
			// for renderer mode
			if ($userinfo['uid']) $this->root->rtf['disable_render_cache'] = TRUE;
			return TRUE;
		}
		if ($exit_flag) {
			$title = $this->root->_msg_not_readable;
			if (!$userinfo['uid'] && $auth_flag) {
				// needs login
				$this->redirect_header($this->root->siteinfo['loginurl'], 1, $title, true);
			} else if ($page === $this->root->defaultpage) {
				$this->redirect_header($this->root->siteinfo['rooturl'], 1, $title);
			} else {
				$this->redirect_header($this->root->script, 1, $title);
			}
			exit;
		}
		// for renderer mode
		$this->root->rtf['disable_render_cache'] = TRUE;
		return FALSE;
	}

	// ページ毎編集権限チェック
	function check_editable_page ($page, $auth_flag = TRUE, $exit_flag = TRUE, $uid = NULL) {

		if (is_null($uid) && ! $this->root->module['checkRight']) {
			// for renderer mode
			$this->root->rtf['disable_render_cache'] = TRUE;
			return FALSE;
		}

		if ($this->is_owner($page, $uid)) {
			// for renderer mode
			$this->root->rtf['disable_render_cache'] = TRUE;
			return TRUE;
		}

		if (!$this->check_readable_page ($page, $auth_flag, $exit_flag, $uid, FALSE)) {
			return FALSE;
		}

		if (is_null($uid)) {
			$userinfo = $this->root->userinfo;
		} else {
			$uid = intval($uid);
			$userinfo = $this->get_userinfo_by_id($uid);
		}

		$ret = FALSE;
		// #pginfo
		$pginfo = $this->get_pginfo($page);
		if ($pginfo['egids'] === 'none' && $pginfo['eaids'] === 'none') {
			$ret = FALSE;
		} else {
			$egids = explode('&', trim($pginfo['egids'], '&'));
			$eaids = explode('&', trim($pginfo['eaids'], '&'));
			$_eg = array_merge($egids, $userinfo['gids']);
			$eauth = (count($_eg) === count(array_unique($_eg)))? FALSE : TRUE;

			if (
				$pginfo['egids'] === 'all' ||
				$pginfo['eaids'] === 'all' ||
				$eauth ||
				in_array((string)$userinfo['uid'], $eaids, true)
			) {
				$ret = TRUE;
			}
		}
		if ($ret) {
			// for renderer mode
			if ($userinfo['uid']) $this->root->rtf['disable_render_cache'] = TRUE;
			return TRUE;
		}
		if ($exit_flag) {
			$title = $this->root->_msg_not_editable;
			if (!$userinfo['uid'] && $auth_flag) {
				// needs login
				$this->redirect_header($this->root->siteinfo['loginurl'], 1, $title, true);
			} else if ($this->is_page($page)) {
				$this->redirect_header($this->root->script.'?'.rawurlencode($page), 1, $title);
			} else {
				$this->redirect_header($this->root->script, 1, $title);
			}
			exit;
		}
		// for renderer mode
		$this->root->rtf['disable_render_cache'] = TRUE;
		return FALSE;
	}

	// なぞなぞ認証をチェック
	function check_riddle () {
		$ret = FALSE;
		if ($this->root->userinfo['admin'] ||
			$this->root->riddle_auth === 0 ||
			($this->root->riddle_auth === 1 && $this->root->userinfo['uid'] !== 0)
		) return TRUE;
		foreach ($this->root->vars as $key => $val) {
			if (substr($key, 0, 6) === 'riddle') {
				$q_key = substr($key, 6);
				foreach ($this->root->riddles as $q => $a) {
					if ($q_key === md5($this->cont['HOME_URL'].$q)) {
						if ($a === rtrim($val)) {
							$ret = TRUE;
						}
						break;
					}
				}
				break;
			}
		}
		return $ret;
	}

	function add_js_var_head ($name, $var = NULL, $pre = FALSE) {
		if (is_null($var)) {
			$src = $name;
			$key = md5($src);
		} else {
			if (is_numeric($var)) {
				// Do nothing
			} else if (is_bool($var)) {
				$var = ($var)? 'true' : 'false';
			} else {
				$var = '"' . htmlspecialchars($var) . '"';
			}
			$key = $src = $name . ' = ' . $var . ';';
		}
		$target = $pre? 'head_pre_tags' : 'head_tags';
		if ($this->root->render_mode === 'render') {
			$src = '<!--' . "\n" . $src . '//-->';
		}
		$key = $this->get_headtag_key('js', $key);
		$this->root->{$target}[$key] = '<script type="text/javascript">' . $src . '</script>';
	}

	function add_tag_head ($file, $pre = FALSE, $charset = '', $defer = false) {
		static $done = array();
		if ($this->root->render_mode !== 'render') {
			if (isset($done[$this->xpwiki->pid][$file])) { return; }
			$done[$this->xpwiki->pid][$file] = TRUE;
		}

		$target = $pre? 'head_pre_tags' : 'head_tags';

		if (preg_match("/^(.+)\.([a-zA-Z]+)$/",$file,$match)) {
			if ($charset) $charset = ' charset="' . $charset . '"';
			if ($match[2] === 'css') {
				$key = 's' . $this->cont['SKIN_NAME'] . 'm' . $this->root->render_mode . 'p' . $this->root->css_prefix . 'c' .$charset;
				$key = $this->get_headtag_key('css', $key);
				if (isset($this->root->{$target}[$key])) {
					$this->root->{$target}[$key] = str_replace('.css"', ',' . $match[1] . '.css"', $this->root->{$target}[$key]);
				} else {
					if ($this->root->render_mode === 'main') {
						$mode = '';
					} else {
						$mode = ($this->root->render_mode === 'block')?
							'b=1&amp;' :
							(($this->root->render_mode === 'render')?
								'r=1&amp;' :
								''
							);
					}
					$cssprefix = $this->root->css_prefix ? 'pre=' . rawurlencode($this->root->css_prefix) . '&amp;' : '';
					$_css = 'skin='.$this->cont['SKIN_NAME'].'&amp;'.$mode.$cssprefix.'src='.$match[1];
					$this->root->{$target}[$key] = '<link rel="stylesheet" type="text/css" media="all" href="'.$this->cont['LOADER_URL'] . '?' . $_css . '.css"' . $charset . ' />';
				}

			} else if ($match[2] === 'js') {
				$defer = $defer? ' defer="defer"' : '';
				$key = $charset . $defer;
				$key = $this->get_headtag_key('js', $key);
				if (isset($this->root->{$target}[$key])) {
					$this->root->{$target}[$key] = str_replace('.js"', ',' . $match[1] . '.js"', $this->root->{$target}[$key]);
				} else {
					$this->root->{$target}[$key] = '<script type="text/javascript" src="'.$this->cont['LOADER_URL'].'?src='.$match[1].'.js"' . $charset . $defer . '></script>';
				}
				if (empty($this->root->rtf['HeadJsAjaxSafe'])) {
					$this->root->rtf['useJavascriptInHead'] = TRUE;
					$this->root->rtf['HeadJsAjaxSafe'] = NULL;
				}
			}
		} else {
			$key = $this->get_headtag_key('js', $file);
			$this->root->{$target}[$key] = $file;
		}
	}

	function add_js_head ($file, $pre = FALSE, $charset = '', $defer = false) {
		static $done = array();
		if ($this->root->render_mode !== 'render') {
			if (isset($done[$this->xpwiki->pid][$file])) { return; }
			$done[$this->xpwiki->pid][$file] = TRUE;
		}

		if ($charset) $charset = ' charset="' . $charset . '"';
		$defer = $defer? ' defer="defer"' : '';
		$target = $pre? 'head_pre_tags' : 'head_tags';

		$key = $this->get_headtag_key('js', $file);
		$this->root->{$target}[$key] = '<script type="text/javascript" src="' . $file . '"' . $charset . $defer . '></script>';
		if (empty($this->root->rtf['HeadJsAjaxSafe'])) {
			$this->root->rtf['useJavascriptInHead'] = TRUE;
			$this->root->rtf['HeadJsAjaxSafe'] = NULL;
		}
	}

	function add_meta_head ($meta) {
		static $done = array();
		if ($this->root->render_mode !== 'render') {
			if (isset($done[$this->xpwiki->pid][$meta])) { return; }
			$done[$this->xpwiki->pid][$meta] = TRUE;
		}
		$key = $this->get_headtag_key('0', $meta);
		$this->root->head_pre_tags[$key] = $meta;
	}

	function get_headtag_key ($type, $key) {
		// default.js start "3."
		switch($type) {
			case 'css':
				$num = 0;
				break;

			case 'js':
				$num = 5;
				break;

			default:
				$num = $type;
		}

		return $num . '.' . $key;
	}

	// リファラチェック $blank = 1 で未設定も不許可(デフォルトで未設定は許可)
	function refcheck($blank = 0, $ref = NULL)
	{
		if (is_null($ref) && isset($_SERVER['HTTP_REFERER'])) $ref = $_SERVER['HTTP_REFERER'];
		if (!$blank && (! $ref || $ref === '-')) return TRUE;
		if (strpos($ref, $this->cont['ROOT_URL']) === 0 ) return TRUE;

		return FALSE;
	}

	// ページ作成者のIDを求める
	function get_pg_auther ($page) {
		$pginfo = $this->get_pginfo($page);
		return $pginfo['uid'];
	}


	//EXIFデータを得る
	function get_exif_data($file, $original = FALSE){
		$ret = array();
		if (function_exists('exif_read_data')) {
			$arr = array();
			if ($arr = $this->cache_get_db ($key = sha1($file), 'exif', false, true)) {
				$arr = unserialize($arr);
				if (!isset($arr['ret'])) {
					// Old type cache
					if ($original) {
						$arr = array();
					} else {
						$arr['ret'] = $arr;
					}
				}
			}
			if (!$arr) {
				$exif_data = @ exif_read_data($file);
				if ($exif_data) {

					$ret['title'] = "-- Shot Info --";

					if (isset($exif_data['Model']))
						$ret['Camera '] = $exif_data['Model'];

					if (isset($exif_data['Make'])) {
						if (strpos($ret['Camera '], $exif_data['Make']) !== 0){
							$ret['Camera '] = $exif_data['Make'] . ' ' . $ret['Camera '];
						}
					}


					if (isset($exif_data['DateTimeOriginal']))
						$ret['Date '] = $exif_data['DateTimeOriginal'];

					if (isset($exif_data['ExposureTime']))
						$ret['Exposure '] = $this->get_exif_numbar($exif_data['ExposureTime'], FALSE, 'fraction').' sec';

					if (isset($exif_data['ExposureBiasValue'])) {
						$ret['Exposure '] .= ' (' . $this->get_exif_numbar($exif_data['ExposureBiasValue'], FALSE).' EV)';
					}

					if (isset($exif_data['ApertureValue'])) {
						$ret['Aperture '] = 'F '.$this->get_exif_numbar($exif_data['ApertureValue'], 'A');
					} else if (isset($exif_data['FNumber'])) {
						$ret['Aperture '] = 'F '.$this->get_exif_numbar($exif_data['FNumber']);
					}

					if (isset($exif_data['FocalLength']))
						$ret['Lens '] = $this->get_exif_numbar($exif_data['FocalLength']).' mm';

					if (isset($exif_data['FocalLengthIn35mmFilm']))
						$ret['Lens '] .= '(35mm:' . $this->get_exif_numbar($exif_data['FocalLengthIn35mmFilm']).')';

					if (isset($exif_data['MaxApertureValue']))
						@$ret['Lens '] .= '/F '.$this->get_exif_numbar($exif_data['MaxApertureValue'], 'A');

					if (isset($exif_data['Flash'])){
						if ($exif_data['Flash'] == 0) {$ret['Flash '] = "OFF";}
						else if ($exif_data['Flash'] == 1) {$ret['Flash '] = "ON";}
						else if ($exif_data['Flash'] == 5) {$ret['Flash '] = "Light(No Reflection)";}
						else if ($exif_data['Flash'] == 7) {$ret['Flash '] = "Light(Reflection)";}
						else if ($exif_data['Flash'] == 9) {$ret['Flash '] = "Always ON";}
						else if ($exif_data['Flash'] == 16) {$ret['Flash '] = "Always OFF";}
						else if ($exif_data['Flash'] == 24) {$ret['Flash '] = "Auto(None)";}
						else if ($exif_data['Flash'] == 25) {$ret['Flash '] = "Auto(Light)";}
						else {$ret['Flash '] = $exif_data['Flash'];}
					}

					if (isset($exif_data['SubjectDistance'])) {
						$ret['Distance '] = $exif_data['SubjectDistance'].' m';
					}

					if (count($ret) === 1) {
						$ret = array();
					}

					/*
					if ($ret) {
						$org['-- :Orignal Exif '] = '--';
						foreach ($exif_data as $akey=>$sect) {
							if (is_array($sect) == FALSE) {
								$org[$akey . ' '] = trim($sect);
							} else {
								foreach($sect as $name=>$val)	$org[$akey . ':' . $name . ' '] = trim($val);
							}
						}
						// 表示しないパラメーター
						unset($org['FileName'], $org['MakerNote']);
					}
					*/

				}
				$arr = array('ret' => $ret, 'org' => $exif_data);
				$this->cache_save_db(serialize($arr), 'exif', 86400 * 30, $key);
			}
			if ($original) {
				$ret = $arr['org'];
			} else {
				$ret = $arr['ret'];
			}
		}
		return $ret;
	}
	function get_exif_numbar ($dat, $APEX=FALSE, $format='') {
		if (preg_match('#^([\d.-]+)/([\d.-]+)$#',$dat,$match)) {
			if ($match[2]) {
				$dat = $match[1] / $match[2];
			} else {
				$dat = $match[1];
			}
		} else {
			$dat = (float)$dat;
		}
		if ($APEX == 'T') {
			$dat = pow(2, $dat);
		} else if ($APEX) {
			$dat = pow(sqrt(2), $dat);
		}
		if ($format !== 'long') {
			if ($format === 'fraction' && $dat <= 1) {
				$dat = '1/' . (int)(1/$dat);
			} else {
				$dat = round($dat * 100) / 100;
			}
		}
		return $dat;
	}
	function get_exif_ev ($exif_data) {
		$ev = $this->get_exif_numbar($exif_data['ExposureBiasValue'], FALSE, TRUE);
		//BrightnessValue+log2(ISOSpeedRatings/3.125)+ExposureBiasValue
		//$bv = $this->get_exif_numbar($exif_data['BrightnessValue'], FALSE, TRUE);
		//$sr = $this->get_exif_numbar($exif_data['ISOSpeedRatings'], FALSE, TRUE);
		//$ev = $bv + log($sr/3.125, 2) + $ev;
		return $ev;
	}

	// php.ini の略式文字列からバイト数を得る
	function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val{strlen($val)-1});
		switch($last) {
			// 'G' は、PHP 5.1.0 より有効となる
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			 case 'k':
				$val *= 1024;
		}
		return $val;
	}

	//ページ名から最初の見出しを得る(ファイルから)
	function get_heading_init($page)
	{
		$_body = $this->get_source($page, TRUE, TRUE);
		if (!$_body) return '';

		$ret = '';
		if ($this->root->title_setting_regex && preg_match($this->root->title_setting_regex,$_body,$match)) {
			$ret = $match[1];
		} else if (preg_match('/^\*+.+\s*$/m',$_body,$match)) {
			$ret = $match[0];
		} else if (preg_match('/^(?! |\s|#|\/\/).+\s*$/m',$_body,$match)) {
			$ret = $match[0];
		}

		if ($ret) {
			$_readonly = $this->cont['PKWK_READONLY'];
			$_symbol_anchor = $this->root->_symbol_anchor;

			$this->cont['PKWK_READONLY'] = 1;
			$this->root->_symbol_anchor = '';
			$this->root->rtf['GET_HEADING_INIT'] = TRUE;

			$ret = $this->convert_html($ret, $page);

			$this->root->_symbol_anchor = $_symbol_anchor;
			$this->cont['PKWK_READONLY'] = $_readonly;

			unset($this->root->rtf['GET_HEADING_INIT']);

			$ret = strip_tags(preg_replace('#<script.+?/script>|<span class="plugin_error">.+?</span>#is', '', $ret));
			$ret = str_replace(array("\r","\n","\t", '&nbsp;'),' ',$ret);
			$ret = preg_replace('/\s+/',' ',$ret);
			$ret = trim($ret);
			$ret = $this->unhtmlspecialchars($ret, ENT_QUOTES);
		}
		return ($ret)? $ret : "- no title -";
	}

	function unhtmlspecialchars ($str, $quote_style = ENT_COMPAT) {
		return htmlspecialchars_decode($str, $quote_style);
	}

	// ページ頭文字読みの配列を取得
	function get_readings() {
		$readings = array();
		$pages = $this->get_existpages(false, "", array('select' => array('reading', 'title')));
		foreach ($pages as $page=>$dat) {
			if (empty($dat['reading'])) {
				$dat['reading'] = $this->get_page_reading($page);
			}
			$readings[$page] = $dat['reading'];
			$titles[$page] = $dat['title'];
		}
		return array($readings, $titles);
	}

	// #pginfo を除去する
	function remove_pginfo ($str) {
		return preg_replace($this->cont['PKWK_PGINFO_REGEX'], '', $str);
	}

	// ページ内容追加履歴の書き出し
	function push_page_changes($page, $txt, $del=false) {

		if (!$this->is_page($page)) return;
		$id = $this->get_pgid_by_name($page);
		$add_file = $this->cont['DIFF_DIR'].$id.".add";

		if ($del) {
			@unlink($add_file);
			return;
		}

		$txt = preg_replace('/^\+(.*\s*)$/m', '$1', $txt);
		$txt = preg_replace('/^#pginfo.+/m', '', $txt);
		$txt = preg_replace('/^#/m', '&#35;', $txt);

		// ゲスト扱いにする
		$_userinfo = $this->root->userinfo;
		$this->root->userinfo['admin'] = FALSE;
		$this->root->userinfo['uid'] = 0;
		$this->root->userinfo['uname'] = '';
		$this->root->userinfo['uname_s'] = '';
		$this->root->userinfo['gids'] = array();

		$this->root->rtf['PUSH_PAGE_CHANGES'] = TRUE;
		$txt = rtrim($this->convert_html($txt, $page));
		unset($this->root->rtf['PUSH_PAGE_CHANGES']);

		$this->root->userinfo = $_userinfo;

		$txt = preg_replace('#</?a\b[^>]*>|<(script|style)\b.+?</\\1>#is', '', $txt);
		if (!$txt) {return;}

		$sep = "&#182;<!--ADD_TEXT_SEP-->\n";
		$limit = 5;

		$data = @file_get_contents($add_file);
		if ($data) {
			$adds = preg_split("/".preg_quote($sep,"/")."/",$data);
			$adds = array_slice($adds,0,$limit-1);
		} else {
			$adds = array();
		}

		array_unshift($adds,$txt);

		if ($fp = @fopen($add_file,"wb")) {
			fputs($fp,join($sep,$adds));
			fclose($fp);
		}
	}

	// ページ内容追加履歴の取得
	function get_page_changes ($page) {
		if (!$this->is_page($page)) return;

		$id = $this->get_pgid_by_name($page);
		$add_file = $this->cont['DIFF_DIR'].$id.".add";
		if (is_file($add_file)) {
			return file_get_contents($add_file);
		} else {
			return '';
		}
	}

	// ページ専用CSSタグを得る
	function get_page_css_tag ($page) {
		$ret = '';
		$_page = '';
		$block = ($this->root->render_mode === 'block')? 'b=1&amp;' : '';

		// トップページ
		$pgid = $this->get_pgid_by_name($this->root->defaultpage);
		if (is_file($this->cont['CACHE_DIR'].$pgid.'.css'))
		{
			$ret .= '<link rel="stylesheet" type="text/css" media="all" href="'.$this->cont['LOADER_URL'].'?'.$block.'src='.$pgid.'.page.css" />'."\n";
		}

		foreach(explode('/',$page) as $val)
		{
			$_page = ($_page)? $_page."/".$val : $val;
			if ($_page !== $this->root->defaultpage) {
				$pgid = $this->get_pgid_by_name($_page);
				if (is_file($this->cont['CACHE_DIR'].$pgid.'.css'))
				{
					$ret .= '<link rel="stylesheet" type="text/css" media="all" href="'.$this->cont['LOADER_URL'].'?'.$block.'src='.$pgid.'.page.css" />'."\n";
				}
			}
		}
		return $ret;
	}

	// ページURIを得る
	function get_page_uri($page, $full = FALSE, $profile=NULL) {
		$_page = $page;
		if ($page === $this->root->defaultpage) {
			$link = '';
		} else {
			if ($this->root->static_url) {
				switch($this->root->static_url) {
					case 3:
					case 2:
						if ($this->root->url_encode_utf8) {
							$page = mb_convert_encoding($page, 'UTF-8', $this->cont['SOURCE_ENCODING']);
						}
						// ! defined('PROTECTOR_VERSION') = (Protector < 3.33)
						if (! defined('PROTECTOR_VERSION') && defined('PROTECTOR_PRECHECK_INCLUDED') && strpos($page, '%27') !== FALSE) {
							$link = '?' . rawurlencode($page);
							break;
						}
						$page = str_replace('%2F', '/', rawurlencode($page));
						$link = $this->root->path_info_script . (($this->root->static_url === 2)? '' : '.php') . '/' . $page;
						break;
					case 1:
					default:
						if ($pgid = $this->get_pgid_by_name($page)) {
							$link = $pgid . '.html';
						} else {
							$link = '?' . rawurlencode($this->root->url_encode_utf8? mb_convert_encoding($page, 'UTF-8', $this->cont['SOURCE_ENCODING']) : $page);
						}
				}
			} else {
				$link = '?' . rawurlencode($this->root->url_encode_utf8? mb_convert_encoding($page, 'UTF-8', $this->cont['SOURCE_ENCODING']) : $page);
			}
			if (is_null($profile)) {
				$profile = $this->cont['UA_PROFILE'];
			}
			if ($profile === 'keitai') {
				$maxlen = 255;
				if (strlen($this->cont['HOME_URL'].$link) > $maxlen) {
					$link = '?pgid=' . $this->get_pgid_by_name($_page);
				}
			}
		}
		return ($full ? $this->cont['HOME_URL'] : '' ) . $link;
	}

	// SKIN Function
	function skin_navigator (& $obj, $key, $value = '', $javascript = '', $withIcon = FALSE, $x = 20, $y = 20) {
		static $cmds = NULL;
		static $disabled = NULL;
		if (is_null($cmds)) {
			$cmds = explode(',', $obj->root->skin_navigator_cmds);
			$cmds = array_map('trim', $cmds);
			$cmds = array_flip($cmds);

			$disabled = explode(',', $obj->root->skin_navigator_disabled);
			$disabled = array_map('trim', $disabled);
			$disabled = array_flip($disabled);
		}
		if (!isset($disabled[$key]) && (isset($cmds['all']) || isset($cmds[$key]))) {
			$lang = & $obj->root->_LANG['skin'];
			$link = & $obj->root->_LINK;
			if (! isset($lang[$key])) { echo $key.' LANG NOT FOUND'; return FALSE; }
			if (! isset($link[$key])) { echo $key.' LINK NOT FOUND'; return FALSE; }
			if (! $obj->cont['PKWK_ALLOW_JAVASCRIPT']) $javascript = '';

			$title = (isset($lang[$key]))? htmlspecialchars($lang[$key]) : '';
			$ret = ($withIcon? $obj->skin_getIcon($obj, $key, $x, $y) : '');
			if ($withIcon !== 'icon') {
				$ret .= ($value === '') ? (isset($lang[$key.'_s'])? $lang[$key.'_s'] : $lang[$key]) : $value;
			}
			if (!$obj->arg_check($key)) {
				$ret = '<a href="' . $link[$key] . '" title="' . $title . '"' . $javascript . '>' . $ret . '</a>';
			} else {
				$ret = '<span class="navigator_current">' . $ret . '</span>';
			}
			echo '<span class="nowrap">' . $ret . '</span>';
		}
		return TRUE;
	}

	// SKIN Function
	function skin_toolbar (& $obj, $key, $x = 20, $y = 20, $javascript = '') {
		$lang  = & $obj->root->_LANG['skin'];
		$link  = & $obj->root->_LINK;
		$image = & $obj->root->_IMAGE['skin'];
		if (! isset($lang[$key]) ) { echo $key.' LANG NOT FOUND';  return FALSE; }
		if (! isset($link[$key]) ) { echo $key.' LINK NOT FOUND';  return FALSE; }
		if (! isset($image[$key])) { echo $key.' IMAGE NOT FOUND'; return FALSE; }

		echo '<a href="' . $link[$key] . '" ' . $javascript . '>' .
			$obj->skin_getIcon($obj, $key, $x, $y) .
			'</a>';

		return TRUE;
	}

	// SKIN Function
	function skin_getIcon(&$obj, $key, $x = 20, $y = 20) {
		$lang  = & $obj->root->_LANG['skin'];
		$image = & $obj->root->_IMAGE['skin'];
		if (! isset($image[$key])) return ' | ';
		$alt = (isset($lang[$key]))? htmlspecialchars($lang[$key]) : '';
		$src = (strpos($image[$key], 'src=') === 0)? $obj->cont['LOADER_URL'] . '?' . $image[$key] : $obj->cont['IMAGE_DIR'] . $image[$key];
		return '<img src="' . $src . '" width="' . $x . '" height="' . $y . '" ' .
				'alt="' . $alt . '" title="' . $alt . '" />';
	}

	// SKIN Function
	function skin_link_extractor($arr) {
		static $i = 0;
		$head = $arr[1];
		$attr = $arr[2];
		$text = $arr[3];
		if (preg_match('/href=(["\'])(.+?)\\1/', $attr, $match)) {
			$link = $match[2];
			if ($link[0] !== '#') {
				if (!isset($this->root->rtf['SkinLinks'][$link])) {
					$i++;
					$this->root->rtf['SkinLinks'][$link] = $i;
					$this->root->rtf['SkinTexts'][$this->root->rtf['SkinLinks'][$link]][] = $text;
				}
				$this->root->rtf['SkinTexts'][$this->root->rtf['SkinLinks'][$link]][] = $text;
				return $arr[1] . $arr[3]. '<sup class="linkslist">[' . $this->root->rtf['SkinLinks'][$link] . ']</sup></a>';
			} else {
				return $arr[3];
			}
		} else {
			return $arr[0];
		}
	}

	// Breadcrumbs
	function get_breadcrumbs_array ($page, $name = 'name', $url = 'url') {
		$parts = explode('/', $page);

		$self = array_pop($parts); // Remove the page itself
		$ret = array();
		//$ret[] = array($name => $self, $url = '');
		while (! empty($parts)) {
			$landing = join('/', $parts);
			$element = htmlspecialchars(array_pop($parts));

			if (! $this->is_page($landing)) {
				// Page not exists
				$ret[] = array($name => $element, $url => '');
			} else {
				// Page exists
				$ret[] = array($name => $element, $url => $this->get_page_uri($landing, TRUE));
			}
		}
		$ret[] = array($name => $this->root->module['title'], $url => $this->root->script);

		return array_reverse($ret);

	}

	// CACHE_DIR に Config ファイルを保存する
	function save_config ($file = '', $section = '', $data = '') {
		if (!$file || !$data || !$section) return;
		$file = $this->cont['CACHE_DIR'].$file;
		if (!is_file($file)) {
			touch($file);
			$org = '';
		} else {
			$org = file_get_contents($file);
			$org = preg_replace('/^<\?php\n(.*)\n\?>$/s', '$1', $org);
		}

		$section_q = preg_quote($section,'#');
		$org = preg_replace('#//<'.$section_q.'>.*?//<'.$section_q.'/>\n#s', '', $org);
		$org .= '//<'.$section_q.'>'."\n".$data."\n".'//<'.$section_q.'/>'."\n";

		$org = '<?php' . "\n" . $org . "\n" . '?>';

		if ($fp = fopen($file, 'wb')) {
			fwrite($fp, $org);
			fclose($fp);
		}
	}

	// ページの親階層を得る
	function page_dirname ($page) {
		return preg_replace('/(^|\/)[^\/]*$/', '',$page);
	}

	// ページのbasenameを得る
	function page_basename ($page) {
		return preg_replace('#^.*/#', '', $page);
	}

	//あるページの関連ページ数を得る
	function links_get_related_count($page)
	{
		$links = $this->links_get_related_db($page);
		$_links = array();
		$count = 0;
		foreach (array_keys($links) as $_page)
		{
			if (preg_match("/{$this->root->non_list}/",$_page))
			{
				continue;
			}
			$count++;
		}
		return $count;
	}

	// Page name normalize
	function pagename_normalize($page) {
		return str_replace($this->root->pagename_illegality, $this->root->pagename_normalizer, $page);
	}

	// Send Location heder
	function send_location ($page='', $hash='', $url='', $title='', $buf_clear=true) {
		if ($buf_clear) {
			$this->clear_output_buffer();
		}

		if ($page !== '') {
			$url = $this->get_page_uri($page, true);
			if (!$title) {
				$title = str_replace('$1', htmlspecialchars($page), $this->root->_title_updated);
			}
		}
		if (!$url) {
			$url = $this->cont['HOME_URL'];
		}

		if ($this->root->viewmode === 'popup') {
			$url .= ((strpos($url, '?') === FALSE)? '?' : '&') . 'popup=1';
		}

		if ($hash) {
			$url .= (($hash{0} !== '#')? '#' : '') . $hash;
		}

		// For WizMobile
		$user = null;
		if (strpos($url, '?') !== FALSE) {
		    if ((class_exists('XC_CLASS_EXISTS') && XC_CLASS_EXISTS('Wizin_User')) || class_exists('Wizin_User')) {
				$user = & Wizin_User::getSingleton();
				if ($user->bIsMobile) {
					$url = str_replace('&amp;', '&', $url);
					list($_url, $_query) = explode('?', $url);
					list($_query, $_hash) = array_pad(explode('#', $_query), 2, '');
					$url = $_url . '?';
					$toenc = $user->sEncoding;
					$fromenc = $this->cont['SOURCE_ENCODING'];
					foreach (explode('&', $_query) as $_q) {
						list($_key, $_dat) = array_pad(explode('=', $_q), 2, '');
						if ($_dat) {
							$url .= $_key . '=' . rawurlencode(mb_convert_encoding(rawurldecode($_dat), $toenc, $fromenc)) . '&';
						} else {
							$url .= $_key . '&';
						}
					}
					$url = rtrim($url, '&');
					if ($_hash) {
						$url .= '#' . $_hash;
					}
				}
			}
		}

		if (headers_sent()) {
			$this->redirect_header($url, 0, $title);
		} else {
			if ($user || defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER) {
				if (is_object($user)) {
					if (! $user->bCookie) {
						$url = $this->href_give_session_id($url);
					}
				} else {
					$kr =& HypKTaiRender::getSingleton();
					if (! $kr->vars['ua']['allowCookie']) {
						$url = $this->href_give_session_id($url);
					}
				}
			}
			header('Location: ' . $url);
		}
		exit;
	}

	function href_give_session_id ($url) {
		$url = str_replace('&amp;', '&', $url);
		$session_name = session_name();
		if (! defined('SID') || ! SID || isset($_COOKIE[$session_name])) return $url;

		$parsed_base = parse_url($this->cont['ROOT_URL']);
		$parsed_url = parse_url($url);

		if (strtolower(substr($url, 0, 6)) === 'mailto') {
			$parsed_url['scheme'] = 'mailto';
			$parsed_url['host'] = $parsed_base['host'];
		}
		if (empty($parsed_url['host']) || ($parsed_url['host'] === $parsed_base['host'] && $parsed_url['scheme'] === $parsed_base['scheme'])) {
			$url = preg_replace('/(?:\?|&)' . preg_quote($session_name, '/') . '=[^&#>]+/', '', $url);

			list($href, $hash) = array_pad(explode('#', $url, 2), 2, '');

			if (!$href) {
				$href = isset($_SERVER['QUERY_STRING'])? '?' . $_SERVER['QUERY_STRING'] : '';
				$href = preg_replace('/(?:\?|&)' . preg_quote($session_name, '/') . '=[^&]+/', '', $href);
			};

			$href .= ((strpos($href, "?") === FALSE)? '?' : '&') . SID;
			$url = $href . ($hash? '#' . $hash : '');
		}

		return $url;
	}

	function send_json ($res) {
		error_reporting(0);

		// clear output buffer
		$this->clear_output_buffer();

		$json = htmlspecialchars(json_encode($res), ENT_NOQUOTES);

		header('Content-Type: text/javascript; charset=utf-8');
		header('Content-Length: '. strlen($json));

		echo $json;
		exit;
	}

	function send_xml ($res, $encode = 'UTF-8', $version = '1.0') {
		error_reporting(0);

		if ($this->cont['UA_PROFILE'] == 'mobile') {
			// For jQuery mobile
			$res = str_replace('<a', '<a data-ajax="false"', $res);
			$res = str_replace('<form', '<form data-ajax="false"', $res);
		}

		if (strtoupper($encode) === 'UTF-8' && strtoupper($this->cont['SOURCE_ENCODING']) === 'ISO-8859-1') {
			$res = utf8_encode($res);
		} else {
			$res = mb_convert_encoding($res, $encode, ($this->cont['SOURCE_ENCODING'] === 'EUC-JP')? 'eucJP-win' : $this->cont['SOURCE_ENCODING']);
		}
		$xml = <<<EOD
<?xml version="{$version}" encoding="{$encode}"?>
$res
EOD;

		// clear output buffer
		$this->clear_output_buffer();

		// mbstring setting
		if (extension_loaded('mbstring')) {
			mb_language($this->cont['MB_LANGUAGE']);
			mb_internal_encoding($this->cont['SOURCE_ENCODING']);
			ini_set('mbstring.http_input', 'pass');
			mb_http_output('pass');
			mb_detect_order('auto');
		}

		header ('Content-type: application/xml; charset='.strtolower($encode)) ;
		header ('Content-Length: '. strlen($xml));
		echo $xml;
		exit;
	}

	function output_ajax ($body) {
		// K-Tai EMOJI
		if (preg_match('/\(\([eisv]:[0-9a-f]{4}\)\)|\[emj:\d{1,4}(?::(?:im|ez|sb))?\]/S', $body)) {
			if (! XC_CLASS_EXISTS('MobilePictogramConverter')) {
				HypCommonFunc::loadClass('MobilePictogramConverter');
			}
			if (XC_CLASS_EXISTS('MobilePictogramConverter')) {
				$mpc =& MobilePictogramConverter::factory_common();
				$mpc->setImagePath(XOOPS_URL . '/images/emoji');
				$mpc->setString($body, FALSE);
				$body = $mpc->autoConvertModKtai();
			}
		}

		// Head Tags
		list($head_pre_tag, $head_tag) = $this->get_additional_headtags();
		$body = str_replace(']]>', ']]&gt;', $body);
		$xml = <<<EOD
<xpwiki>
<content><![CDATA[{$body}]]></content>
<mode>read</mode>
<headPreTag><![CDATA[{$head_pre_tag}]]></headPreTag>
<headTag><![CDATA[{$head_tag}]]></headTag>
</xpwiki>
EOD;
		$this->send_xml($xml);
	}

	function output_popup ($body) {
		// set target
		$body = preg_replace('/(<a[^>]+)(href=(?:"|\')[^#])/isS', '$1target="' . ((intval($this->root->vars['popup']) === 1)? '_parent' : htmlspecialchars(substr($this->root->vars['popup'],0,30))) . '" $2', $body);

		// Head Tags
		list($head_pre_tag, $head_tag) = $this->get_additional_headtags();
		$css_charset = $this->cont['CSS_CHARSET'];
		$class = 'xpwiki_' . $this->root->mydirname;
		$navigator = $this->root->mydirname . '_navigator';
		$cssprefix = $this->root->css_prefix ? 'pre=' . rawurlencode($this->root->css_prefix) . '&amp;' : '';

		// Set Ktai renderer's option.
		if (defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER) {
			$keitai_render =& HypKTaiRender::getSingleton();
			$keitai_render->Config_googleAdSenseConfig = '';
		}

		header('Content-Type: text/html; charset=' . $this->cont['CONTENT_CHARSET']);
		// HTML DTD, <html>, and receive content-type
		if (isset($this->root->pkwk_dtd)) {
			$meta_content_type = $this->pkwk_output_dtd($this->root->pkwk_dtd);
		} else {
			$meta_content_type = $this->pkwk_output_dtd();
		}
		$html = <<<EOD
<head>
$meta_content_type
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
$head_pre_tag
<link rel="stylesheet" type="text/css" media="all" href="{$this->cont['LOADER_URL']}?skin={$this->cont['SKIN_NAME']}&amp;charset={$css_charset}&amp;pw={$this->root->pre_width}&amp;{$cssprefix}src={$this->root->main_css}" charset="{$css_charset}" />
<link rel="stylesheet" type="text/css" media="print"  href="{$this->cont['LOADER_URL']}?skin={$this->cont['SKIN_NAME']}&amp;charset={$css_charset}&amp;pw={$this->root->pre_width}&amp;media=print&amp;{$cssprefix}src={$this->root->main_css}" charset="{$css_charset}" />
$head_tag
<title></title>
</head>
<body class="popup_body_{$this->cont['UA_PROFILE']}">
<div class="{$class}" id="{$navigator}">
<div class="body"><div id="xpwiki_body">
$body
</div></div>
</div>
</body>
</html>
EOD;
		echo $html;
		exit();
	}

	function http_request
		(
			$url,
			$method = 'GET',
			$headers = '',
			$post = array(),
			$redirect_max = NULL,
			$content_charset = '',
			$blocking = TRUE,
			$retry = 1,
			$c_timeout = 3,
			$r_timeout = 10,
			$ua = ''
		)
	{
		if ($this->root->can_not_connect_www) {
			return array(
				'query'  => $d->query,
				'rc'     => 400,
				'header' => 'HTTP/1.x 400 Bad Request',
				'data'   => ''
			);
		}

		if (is_null($redirect_max)) {
			$redirect_max = $this->cont['PKWK_HTTP_REQUEST_URL_REDIRECT_MAX'];
		}

		$d = new Hyp_HTTP_Request();

		$d->url = $url;
		$d->method = $method;
		$d->headers = $headers;
		$d->post = $post;
		$d->redirect_max = $redirect_max;
		$d->content_charset = $content_charset;
		$d->blocking = $blocking;
		$d->connect_try = $retry;
		$d->connect_timeout = $c_timeout;
		$d->read_timeout = $r_timeout;
		if ($ua) {
			$d->ua = $ua;
		} else {
			$d->ua = 'User-Agent: Mozilla/5.0 (xpWiki fetcher/1.0; PlugIn-'.end($this->root->plugin_stack).'; '.$this->cont['MODULE_URL'].$this->root->mydirname.'/)';
			//file_put_contents($this->cont['TRUST_PATH'].'/cache/xpwiki.debug.txt', $d->ua."\n".$url."\n", FILE_APPEND);
		}

		if (empty($d->iniLoaded)) {
			$d->use_proxy = $this->root->use_proxy;
			$d->proxy_host = $this->root->proxy_host;
			$d->proxy_port = $this->root->proxy_port;

			$d->need_proxy_auth = $this->root->need_proxy_auth;
			$d->proxy_auth_user = $this->root->proxy_auth_user;
			$d->proxy_auth_pass = $this->root->proxy_auth_pass;

			$d->no_proxy = $this->root->no_proxy;
		}

		$d->get();

		$ret = array(
			'query'  => $d->query,      // Query String
			'rc'     => $d->rc,         // Result code
			'header' => $d->header,     // Header
			'data'   => $d->data        // Data or Error Msg
		);

		$d = NULL;

		return $ret;
	}

	function compare_diff ($old, $cur, $titles = array()) {
		$this->add_tag_head('compare_diff.css');
		include_once $this->root->mytrustdirpath . '/include/DifferenceEngine.php';

		$this->compare_diff_pre($old);
		$this->compare_diff_pre($cur);

		$df  = new Diff($old, $cur);
		$tdf = new TableDiffFormatter();
		$html = $tdf->format($df);

		if ($titles) {
			$title = <<<EOD
<tr>
<th colspan="2">{$titles[0]}</th>
<th colspan="2">{$titles[1]}</th>
</tr>
EOD;
		} else {
			$title = '';
		}

		return <<<EOD
<table class="diff">
$title
$html
</table>
EOD;
	}

	function compare_diff_pre (& $str ,$tab = 4) {
		if (is_array($str)) $str = join('', $str);
		$str = str_replace(array('<', '>'), array('&lt;', '&gt;'), rtrim(str_replace("\r", '', $str)));
		$str = str_replace(array("\n\t", "\n "), array("\n".str_repeat('&nbsp;', $tab), "\n&nbsp;"), $str);
		$str = explode("\n", $str);
		$str = array_map('rtrim', $str);
	}

	function get_areadiv_closer ($level = 0) {
		$areadiv_closer = '';

		if ($this->root->paraedit_partarea !== 'level') {
			$level = 0;
		}

		for ($_lev = 6; $_lev > 1; $_lev--) {
			if ($level <= $_lev) {
				if (!empty($this->root->rtf['div_area_open'][$this->root->rtf['convert_nest']][$_lev])) {
					foreach ($this->root->rtf['div_area_open'][$this->root->rtf['convert_nest']][$_lev] as $_id) {
						$areadiv_closer .= '<!--' . $_id . '--></div>' . "\n";
					}
					unset($this->root->rtf['div_area_open'][$this->root->rtf['convert_nest']][$_lev]);
				}
			}
		}

		return $areadiv_closer;
	}

	// 文字エンコード変換前に範囲外の文字を実体参照値に変換する
	function encode_numericentity(& $arg, $toencode, $fromencode, $keys = array()) {
		$fromencode = strtoupper($fromencode);
		$toencode = strtoupper($toencode);
		if ($fromencode === $toencode || $toencode === 'UTF-8') return;
		if ($toencode === 'EUC-JP') $toencode = 'eucJP-win';
		if (is_array($arg)) {
			foreach (array_keys($arg) as $key) {
				if (!$keys || in_array($key, $keys)) {
					$this->encode_numericentity($arg[$key], $toencode, $fromencode, $keys);
				}
			}
		} else {
			if ($arg === mb_convert_encoding(mb_convert_encoding($arg, $toencode, $fromencode), $fromencode, $toencode)) {
				return;
			}
			if (extension_loaded('mbstring')) {
				$_sub = mb_substitute_character();
				mb_substitute_character('long');
				$arg = preg_replace('/U\+([0-9A-F]{2,5})/', "\x08$1", $arg);
				if ($fromencode !== 'UTF-8') $arg = mb_convert_encoding($arg, 'UTF-8', $fromencode);
				$arg = mb_convert_encoding($arg, $toencode, 'UTF-8');
				$arg = preg_replace('/U\+([0-9A-F]{2,5})/e', '"&#".base_convert("$1",16,10).";"', $arg);
				$arg = preg_replace('/\x08([0-9A-F]{2,5})/', 'U+$1', $arg);
				mb_substitute_character($_sub);
				$arg = mb_convert_encoding($arg, $fromencode, $toencode);
			} else {
				$str = '';
				$max = mb_strlen($arg, $fromencode);
				$convmap = array(0x0080, 0x10FFFF, 0, 0xFFFFFF);
				for ($i = 0; $i < $max; $i++) {
					$org = mb_substr($arg, $i, 1, $fromencode);
					if ($org === mb_convert_encoding(mb_convert_encoding($org, $toencode, $fromencode), $fromencode, $toencode)) {
						$str .= $org;
					} else {
						$str .= mb_encode_numericentity($org, $convmap, $fromencode);
					}
				}
				$arg = $str;
			}
		}
		return;
	}

	function basename($path) {
		$path = rtrim(str_replace("\\", '/', $path), '/');
		return preg_replace('#.*/([^/]+)$#', '$1', $path);
	}

	function set_current_page($page) {
		// カレントページ
		$_page = $this->root->vars['page'];
		$this->root->get['page'] = $this->root->post['page'] = $this->root->vars['page'] = $page;

		// 編集権限がない場合の挙動指定
		$_PKWK_READONLY = $this->set_readonly_by_editauth($page);

		return array(
			'page'     => $_page,
			'readonly' => $_PKWK_READONLY,
		);
	}

	function set_readonly_by_editauth ($page) {
		// 編集権限がない場合の挙動指定
		$_PKWK_READONLY = $this->cont['PKWK_READONLY'];
		if (! $this->cont['PKWK_READONLY']) {
			if (
				($this->root->plugin_follow_freeze && $this->is_freeze($page))
				||
				($this->root->plugin_follow_editauth && ! $this->check_editable_page($page, FALSE, FALSE))
			) {
				$this->cont['PKWK_READONLY'] = 2;
			}
		}
		return $_PKWK_READONLY;
	}

	function strip_MyHostUrl ($html) {
		if (is_array($html)) {
			foreach($html as $_key => $_val) {
				$html[$_key] = $this->strip_MyHostUrl($_val);
			}
		} else {
			$_my_hosturl = preg_quote($this->cont['MY_HOST_URL'], '#');
			$html = preg_replace('#(<[^>]+(?:href|src|code(?:base)?|data)=["\'])' . $_my_hosturl . '#iS', '$1', $html);
		}
		return $html;
	}

	function add_MyHostUrl ($html) {
		if (is_array($html)) {
			foreach($html as $_key => $_val) {
				$html[$_key] = $this->add_MyHostUrl($_val);
			}
		} else {
			$html = preg_replace('#(<[^>]+(?:href|src|code(?:base)?|data)=["\'])/#iS', '$1'. $this->cont['MY_HOST_URL'] . '/', $html);
		}
		return $html;
	}

	function isXpWikiDirname ($dirname) {
		static $ret = array();
		if (! isset($ret[$dirname])) {
			$ret[$dirname] = (preg_match('/^[a-z0-9_-]+$/i', $dirname) && is_file($this->cont['ROOT_PATH'].$this->cont['MOD_DIR_NAME'].$dirname.'/private/ini/pukiwiki.ini.php'));
		}
		return $ret[$dirname];
	}


	function convert_finisher (& $body) {
		static $uniqueid = 0;
		$uniqueid++;

		// 長い英数を折り返す
		if ($this->root->word_break_limit && HypCommonFunc::get_version() >= '20080217') {
			HypCommonFunc::html_wordwrap($body, $this->root->word_break_limit, $this->root->word_breaker);
		}

		// cont['USER_NAME_REPLACE'] などを 置換
		// '_uNIQUEiD_' : Unique ID (Inreger)
		$bef = array($this->cont['USER_NAME_REPLACE'], $this->cont['USER_CODE_REPLACE'], '_uNIQUEiD_');
		$aft = array($this->root->userinfo['uname_s'], $this->root->userinfo['ucd'], $uniqueid);
		if ($this->root->replaces_finish) {
			foreach($this->root->replaces_finish as $key => $val) {
				if ($key[0] === '_') {
					$bef[] = $key;
					$aft[] = $val;
				}
			}
		}
		$body = str_replace($bef ,$aft , $body);

		// For Safari
		if ($this->cont['UA_NAME'] === 'Safari') {
			$body = preg_replace('/(<form)([^>]*>)/' , '$1 accept-charset="UTF-8"$2', $body);
		}

		if ($this->root->viewmode === 'popup') {
			$body = preg_replace('/(<form[^>]*?>)/' , '$1<input type="hidden" name="popup" value="1" />', $body);
		}

		if ($this->cont['PageForRef'] !== $this->cont['PAGENAME']) {
			$body = preg_replace('/(<form[^>]*?>)/' , '$1<input type="hidden" name="uploadpage" value="'.htmlspecialchars($this->cont['PageForRef']).'" />', $body);
		}

		// TextArea id
		if (strpos($body, '<textarea') !== FALSE) {

			$_func = <<<EOD
static \$i = 0;
if (strpos(\$match[2], ' id="') !== FALSE) {
	if (strpos(\$match[2], ' id="{$this->root->mydirname}:') === FALSE) {
		\$match[2] = str_replace(' id="', ' id="{$this->root->mydirname}:', \$match[2]);
	}
	return \$match[1] . \$match[2] . '>';
} else {
	\$i++;
	\$id = '{$this->root->mydirname}:xpwiki_txtarea_' . \$i;
	return \$match[1] . \$match[2] . ' id="' . \$id . '">';
}
EOD;
			$body = preg_replace_callback('/(<textarea)([^>]*?)>/', create_function('$match', $_func), $body);
		}
	}

	function get_favicon_img ($url, $size = 16, $alt = '', $class = 'xpwikiFavicon') {
		if ($this->root->can_not_connect_www || HypCommonFunc::get_version() < '20080213') {
			return '';
		}

		$url = preg_replace('/\?.*/', '', $url);

		if (!$alt) $alt = $url;

		$favicon = '<img src="'.$this->cont['LOADER_URL'].'?src=favicon&amp;url='.rawurlencode($url).'" width="'.$size.'" height="'.$size.'" border="0" alt="'.htmlspecialchars($alt).'" class="'.$class.'" />';

		return $favicon;
	}

	function get_domid ($plugin, $name = '', $withDirname = false) {
		static $count = array();
		$pgid = $this->get_pgid_by_name($this->root->vars['page']);
		if (! $pgid) {
			// for render mode
			$pgid = $this->cont['UTC'];
		}
		if ($name) {
			$name_key = $name;
			$name .= '_';
		} else {
			$name_key = '__NONAME__';
		}
		if (! isset($count[$this->root->mydirname][$pgid][$plugin][$name_key])) {
			$count[$this->root->mydirname][$pgid][$plugin][$name_key] = 0;
		}
		$id =& $count[$this->root->mydirname][$pgid][$plugin][$name_key];
		$id++;
		$dirname = $withDirname? $this->root->mydirname . ':' : '';
		return $dirname . $this->root->mydirname .'_' . $plugin . '_' . $name . $pgid . '_' . $id;
	}

	function get_emoji_pad ($id, $is_textarea = FALSE, $emj_array = NULL) {
		if (! defined('HYP_K_TAI_RENDER') || HypCommonFunc::get_version() < '20090525') return '';
		if ($is_textarea && strpos($id, $this->root->mydirname . ':') !== 0) {
			$id = $this->root->mydirname . ':' . $id;
		}
		return  HypCommonFunc::make_emoji_pad($id, $this->root->_btn_emojipad, '', $this->cont['ROOT_URL'] . 'images/emoji', (! isset($this->root->vars['ajax'])), $emj_array);
	}

	function get_LC_CTYPE() {
		if ($this->cont['CONTENT_CHARSET'] === 'EUC-JP') {
			return (substr(PHP_OS, 0, 3) === 'WIN')? 'Japanese_Japan.20932' : 'ja_JP.eucJP';
		} else {
			return setlocale(LC_CTYPE, 0);
		}
	}

	function convertIDN($host, $mode = 'auto') {
		if (HypCommonFunc::get_version() < '20080226') { return $host; }
		return HypCommonFunc::convertIDN($host, $mode, $this->cont['SOURCE_ENCODING']);
	}

	function url_regularization(& $url) {
		if (HypCommonFunc::get_version() < '20080226') { return $url; }
		if ($arr = HypCommonFunc::i18n_parse_url($url)) {
			$url = $arr['scheme'] . '://' . $arr['host']
			     . (isset($arr['port'])? ':' . $arr['port'] : '')
			     . (isset($arr['path'])? $arr['path'] : '')
			     . (isset($arr['query'])? '?' . $arr['query'] : '')
			     . (isset($arr['fragment'])? '#' . $arr['fragment'] : '');
		}
		return $url;
	}

	// Process onPageWriteBefore
	function do_onPageWriteBefore ($page, $postdata, $notimestamp, $mode, $deletecache = TRUE) {
		$this->onPageWriteBefore ($page, $postdata, $notimestamp, $mode, $deletecache);
		$base = $this->root->mytrustdirpath."/events/onPageWriteBefore";
		if ($handle = opendir($base)) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match("/^([\w_]+)\.inc\.php$/", $file, $match)) {
					include_once($base ."/".$file);
					$_func = 'xpwiki_onPageWriteBefore_'.$match[1];
					if (function_exists($_func)) {
						$_func($this, $page, $postdata, $notimestamp, $mode);
					}
				}
			}
			closedir($handle);
		}
		if ($deletecache) $this->delete_caches();
	}

	// Process onPageWriteAfter
	function do_onPageWriteAfter ($page, $postdata, $notimestamp, $mode, $diffdata, $deletecache = TRUE) {
		$base = $this->root->mytrustdirpath."/events/onPageWriteAfter";
		if ($handle = opendir($base)) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match("/^([\w_]+)\.inc\.php$/", $file, $match)) {
					include_once($base ."/".$file);
					$_func = 'xpwiki_onPageWriteAfter_'.$match[1];
					if (function_exists($_func)) {
						$_func($this, $page, $postdata, $notimestamp, $mode, $diffdata);
					}
				}
			}
			closedir($handle);
		}
		$this->onPageWriteAfter($page, $postdata, $notimestamp, $mode, $diffdata, $deletecache);
		if ($deletecache) $this->delete_caches();
	}

	function get_autolink_regex_pre_after ($ci = false, $str = '') {
		$utf8 = '';
		// Check wrong character as UTF-8
		if ($this->cont['SOURCE_ENCODING'] === 'UTF-8') {
			if (! $str || preg_replace('/(?!)/u', '', $str, 1)) {
				$utf8 = 'u';
			}
		}

		if ($this->root->autolink_as_word) {
			$asWord1 = '(?<=\W)';
			$asWord2 = '(?=\W)';
		} else {
			$asWord1 = $asWord2 = '';
		}
		if ($ci) {
			$pat_pre = '/(<(script|a|textarea|style|option).*?<\/\\2>|<!--NA-->.+?(?:<!--\/NA-->|$)|<[^>]*>|&(?:#[0-9]+|#x[0-9a-f]+|[0-9a-z]+);)|'.$asWord1.'(';
			$pat_aft = ')'.$asWord2.'/isS' . $utf8;
		} else {
			$pat_pre = '/(<([sS][cC][rR][iI][pP][tT]|[a|A]|[tT][eE][xX][tT][aA][rR][eE][aA]|[sS][tT][yY][lL][eE]|[oO][pP][tT][iI][oO][nN]).*?<\/\\2>|<!--NA-->.+?(?:<!--\/NA-->|$)|<[^>]*>|&(?:#[0-9]+|#x[0-9a-fA-F]+|[0-9a-zA-Z]+);)|'.$asWord1.'(';
			$pat_aft = ')'.$asWord2.'/sS' . $utf8;
		}
		return array($pat_pre, $pat_aft);
	}

	// 抜けた階層を補完してソート
	// The hierarchy that has come off is supplemented and sorted.
	function complementary_pagesort (& $pages, $sort = 'sort') {
		sort($pages);
		$tmp = $pages;
		foreach ($tmp as $page) {
			$parent = $page;
			while ($pos = strrpos($parent, '/')) {
				$parent = substr($parent, 0, $pos);
				if (!in_array($parent, $pages)) {
					$pages[] = $parent;
				} else {
					break;
				}
			}
		}
		if (is_array($sort)) {
			$sort[0]->$sort[1]($pages);
		} else {
			$sort($pages);
		}
		return $pages;
	}

	// page sort with sorter(page order)
	function pagesort(& $pages, $sort = 'pagesort', $sortflag = SORT_REGULAR) {
		switch ($sort) {
			case 'pagesort':
				// keep original index for plugin lsx.
				$_pages = array();
				if (asort($pages)) {
					foreach ($pages as $key => $page) {
						$_pages[$key] = $this->get_sortname($page);
					}
					natcasesort($_pages);
					$sorted = array();
					foreach($_pages as $key => $dumy) {
						$sorted[$key] = $pages[$key];
					}
					$pages = $sorted;
					return TRUE;
				} else {
					return FALSE;
				}
			case 'sort':
				return asort($pages, $sortflag);
			case 'rsort':
				return arsort($pages, $sortflag);
			case 'natsort':
				return natsort($pages);
			case 'natcasesort':
				return natcasesort($pages);
			default:
				return FALSE;
		}
	}

	function get_sortname ($page) {
		static $sortname = array();
		if (isset($sortname[$this->root->mydirname][$page])) {
			return $sortname[$this->root->mydirname][$page];
		}
		if ($pos = strrpos($page, '/')) {
			$name = $this->get_sortname(substr($page, 0, $pos)) . '/' . (isset($this->root->pgorders[$page])? $this->root->pgorders[$page] : $this->get_page_order($page)) . '#' . substr($page, $pos + 1);
		} else {
			$name = (isset($this->root->pgorders[$page])? $this->root->pgorders[$page] : $this->get_page_order($page)) . '#' . $page;
		}
		$sortname[$this->root->mydirname][$page] = $name;
		return $name;
	}

	function extract_pgtitle (& $postdata) {
		$pgtitle = '';
		if (preg_match($this->root->title_setting_regex, $postdata, $match)) {
			$pgtitle = $match[1];
			$postdata = preg_replace($this->root->title_setting_regex, '', $postdata, 1);
		}
		return $pgtitle;
	}

	// Formart finger fixed child of the table cell is deleted.
	function cell_format_tag_del ($td) {
		// Regular expression of color name
		$colors_reg = "aqua|navy|black|olive|blue|purple|fuchsia|red|gray|silver|green|teal|lime|white|maroon|yellow|transparent";

		// Character color specification deletion
		$td = preg_replace("/FC:(#?[0-9abcdef]{6}?|$colors_reg|0)/i","",$td);

		// Background color specification deletion
		$td = preg_replace("/(SC|BC):(#?[0-9abcdef]{6}?|$colors_reg|0)(\(([^),]*)(,no|,one|,1)?\))/i","SC:$2",$td);
		$td = preg_replace("/(SC|BC):(#?[0-9abcdef]{6}?|$colors_reg|0)/i","",$td);

		// Background picture specification deletion
		$td = preg_replace("/(SC|BC):\(([^),]*)(,once|,1)?\)/i","",$td);

		// Character arrangement specification deletion
		$tmp = array();
		if (preg_match("/^(LEFT|CENTER|RIGHT)?(:)(TOP|MIDDLE|BOTTOM)?/i",$td,$tmp)) {
			$td = (!$tmp[1] && !$tmp[3])? $tmp[2] : "";
		}
		return $td;
	}

	function escape_multiline_pre (& $src, $enc = TRUE) {
		// Multiline-enabled block plugin
		if (!$this->cont['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK']) {
			$plugin_reg = join('|', $this->root->multiline_pre_plugins);
			$is_array = FALSE;
			if (is_array($src)) {
				$is_array = TRUE;
				$lines = $src;
				$out = array();
			} else {
				$lines = explode("\n", $src);
				$out = '';
			}
			$func = $enc ? 'base64_encode' : 'base64_decode';
			while (!empty ($lines)) {
				$line = rtrim(array_shift($lines), "\r\n") . "\n";
				if (preg_match('/^#(?:'.$plugin_reg.')(?:\([^\)]*\))?(\{\{+)\s*$/', $line, $matches)) {
					$len = strlen($matches[1]);
					while (!empty ($lines)) {
						$next_line = rtrim(array_shift($lines), "\r\n");
						if (preg_match('/^\}{'.$len.'}/', $next_line)) {
							$line .= $next_line . "\n";
							break;
						} else {
							$line .= $func($next_line) . "\n";
						}
					}
				}
				if ($is_array) {
					$out[] = $line;
				} else {
					$out .= $line;
				}
			}
			$src = $out;
		}
		return;
	}

	function convert_html_multiline ($str) {
		$oldflg = (isset($this->root->rtf['convert_html_multiline']))? $this->root->rtf['convert_html_multiline'] : NULL;
		$this->root->rtf['convert_html_multiline'] = TRUE;

		$str = str_replace("\r", "\n", $str);
		$html = $this->convert_html($str);

		if (is_null($oldflg)) {
			unset($this->root->rtf['convert_html_multiline']);
		} else {
			$this->root->rtf['convert_html_multiline'] = $oldflg;
		}

		return $html;
	}

	function touch_page ($page, $time = FALSE, $lastuser_update = FALSE) {
		if (! $this->is_page($page)) return FALSE;
		if ($lastuser_update) {
			$this->page_write($page, NULL, (is_null($time)? TRUE : FALSE));
		}
		if (! $lastuser_update || $time) {
			$this->pkwk_touch_file($this->get_filename($page), $time);
			$this->touch_db($page);
		}
	}

	function send_update_ping () {
		if ($this->root->update_ping && HypCommonFunc::get_version() >= 20080515) {
			if (! $this->cache_get_db('xmlrpc_ping_send', 'system', false, true)) {

				$this->cache_save_db('done', 'system', 1800, 'xmlrpc_ping_send'); // TTL = 1800 sec.

				$this->unregist_jobstack(array('action' => 'xmlrpc_ping_send'));

				HypCommonFunc::loadClass('HypPinger');
				$p = new HypPinger(
					$this->root->module['title'] . ' / ' . $this->root->siteinfo['sitename'],
					$this->cont['HOME_URL'],
					$this->cont['HOME_URL'] . '?' . rawurldecode($this->root->whatsnew),
					$this->cont['HOME_URL'] . '?cmd=rss',
					''
				);
				$p->setEncording($this->cont['SOURCE_ENCODING']);

				foreach(explode("\n", trim($this->root->update_ping_servers)) as $to) {
					list($url, $extended) = array_pad(explode(' ', trim($to)), 2, '');
					$url = trim($url);
					$extended = $extended? TRUE : FALSE;
					if ($this->is_url($url, TRUE)) {
						$p->addSendTo($url, $extended);
					}
				}

				$p->send();

				$p = NULL;
				unset($p);
			} else {
				// Retry after 5 min.
				$this->regist_jobstack(array('action' => 'xmlrpc_ping_send'), 0, 300);
			}
		}
	}

	function cleanup_template_source (& $source) {
		// 見出しの固有ID部を削除
		$source = preg_replace('/^(\*{1,5}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $source);
		// ref のアップロード用ID部を削除
		$source = preg_replace('/((?:&|#)ref\()ID\$[^,]+/','$1',$source);
		// #freezeを削除
		$source = preg_replace('/^#freeze\s*$/m','',$source);
		// #pginfoを削除
		$source = $this->remove_pginfo($source);
	}

	function make_empty_page ($page, $asSystem = true) {
		if ($asSystem) {
			$_userinfo = $this->root->userinfo;
			$this->root->userinfo['admin'] = true;
			$this->root->userinfo['uid'] = 0;
			$this->root->userinfo['ucd'] = 'System';
			$this->root->userinfo['uname_s'] = $this->root->userinfo['uname'] = '[System]';
			$this->root->userinfo['gids'] = array();
		}
		$this->page_write($page, "\t");
		if ($asSystem) {
			$this->root->userinfo = $_userinfo;
		}
	}

	function rewrite4move2child ($src) {
		$src = preg_replace_callback('/((?:#|&)[A-Za-z0-9_-]+\()([^,)]+)/', array(& $this, '_rewrite4move2child_ref'), $src);
		$from = array('[[./', '[[../');
		$to = array('[[../', '[[../../');
		$src = str_replace($from, $to, $src);
		$src = preg_replace('#\[\[[^\]>]+>\.\./#', '\0../', $src);
		return $src;
	}

	function _rewrite4move2child_ref ($match) {
		if (strpos($match[2], '/') === FALSE || substr($match[2], 0, 3) === '../') {
			$match[2] = '../' . $match[2];
		} else if (substr($match[2], 0, 2) === './') {
			$match[2] = '../' . substr($match[2], 2);
		}
		return $match[1] . $match[2];
	}

	function get_digests ($src) {
		if (is_array($src)) {
			$src = join('', $src);
		}
		$src = $this->remove_pginfo($src);
		return md5($src);
	}

	function get_page_context ($page, $words = NULL, $highlight = FALSE, $contextLength = 255, $contextKeys = 3, $delimiter='...') {
		if (is_null($words)) {
			$words = array($page, $this->basename($page));
		}
		$words = array_unique($words);
		$cache_file = $this->cont['CACHE_DIR']."page/".$this->encode($page).".".$this->cont['UI_LANG'];
		if (is_file($cache_file)) {
			$cache_dat = unserialize(file_get_contents($cache_file));
			$text = $cache_dat['body'];
		} else {
			$text = '';
			if (empty($this->root->rtf['from_get_page_context'])) {
				$pobj = & XpWiki::getSingleton($this->root->mydirname);
				$pobj->init($page);
				$GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'] = null;
				$pobj->root->rtf['use_cache_always'] = TRUE;
				$pobj->root->rtf['from_get_page_context'] = TRUE;
				$pobj->execute();
				$text = $pobj->body;
			}
		}
		$text = preg_replace('#<div class="context">.+?</div>#s', '', $text);
		$text = preg_replace('/<(script|style).+?<\/\\1>/i', '', $text);
		$text = str_replace($this->root->hierarchy_insert, '', $text);
		$context = HypCommonFunc::make_context(strip_tags( $text ), $words, $contextLength, $contextKeys, $delimiter);
		if ($highlight) {
			$context = $this->word_highlight($context, join(' ',$words));
		}
		return '<div class="context">' . $context . '</div>';
	}

	function word_highlight ($body, $word) {
		// BugTrack2/106: Only variables can be passed by reference from PHP 5.0.5
		// with array_splice(), array_flip()
		$words = preg_split('/\s+/', $word, -1, PREG_SPLIT_NO_EMPTY);
		$words = array_splice($words, 0, 10); // Max: 10 words
		$words = array_flip($words);

		$keys = array();
		foreach ($words as $word=>$id) $keys[$word] = strlen($word);
		arsort($keys, SORT_NUMERIC);
		$keys = $this->get_search_words(array_keys($keys), TRUE);
		$id = 0;
		foreach ($keys as $key=>$pattern) {
			$s_key    = htmlspecialchars($key);
			$pattern  = '/' .
				'<(textarea|script|style)[^>]*>.*?<\/\\1>' .	// Ignore textareas
				'|' . '<[^>]*>' .			// Ignore tags
				'|' . ( preg_match('/^(&?#?[\d]+|#?[\d]+;|&#?|#|;)$/', $key)?
				'&[^;]+;' :					// Ignore entities
				'&#[^\d]+;' ) .				// Ignore entities (Not numerical entities only)
				'|' . '(' . $pattern . ')' .		// $matches[1]: Regex for a search word
				'/sS';
			$decorate_Nth_word = create_function(
				'$matches',
				'return (isset($matches[2])) ? ' .
					'\'<strong class="word' .
						$id .
					'">\' . $matches[2] . \'</strong>\' : ' .
					'$matches[0];'
			);
			if (is_array($body)) {
				foreach ($body as $key => $val) {
					$body[$key] = preg_replace_callback($pattern, $decorate_Nth_word, $body[$key]);
				}
			} else {
				$body  = preg_replace_callback($pattern, $decorate_Nth_word, $body);
			}
			++$id;
		}
		return $body;
	}

	function remove_bom($src) {
		if ($this->cont['SOURCE_ENCODING'] === 'UTF-8') {
			$src = str_replace("\xEF\xBB\xBF", '', $src);
		}
		return $src;
	}

	function twitter_post($username, $password, $msg, $link = '', $convert = TRUE) {
		if ($link) {
			$link = ' ' . $this->bitly($link);
		}
		$max = $link? (140 - strlen($link)) : 140;

		if ($convert) {
			$page = isset($this->root->vars['page'])? $this->root->vars['page'] : '';
			$msg = strip_tags($this->convert_html($msg, $page));
		}
		//$msg = preg_replace('/(https?:\/\/[\w\/\@\$()!?&%#:;.,~\'=*+-]+)/ie', "\$this->bitly('$1')", $msg);

		if (mb_strlen($msg) > $max) {
			$msg = mb_substr($msg, 0, $max - 3) . '...';
		}
		if ($link) $msg .= $link;

		$url = 'http://'.$username.':'.$password.'@'.'twitter.com/statuses/update.xml?';
		$url .= 'status='. rawurlencode(mb_convert_encoding($msg, 'UTF-8', $this->cont['SOURCE_ENCODING']));
		$url .= '&source=xpWiki';
		$d = new Hyp_HTTP_Request();
		$d->url = $url;
		$d->method = 'POST';
		$d->blocking = FALSE;
		$d->get();
		$q = $d->query;
		$d = NULL;
	}

	function twitter_update($msg, $page = '', $convert = TRUE) {
		if (! $this->root->userinfo['uid']) return;

		if (! $this->root->twitter_consumer_key || ! $this->root->twitter_consumer_secret) return;

		$user_pref = $this->get_user_pref($this->root->userinfo['uid']);

		if (empty($user_pref['twitter_access_token']) || empty($user_pref['twitter_access_token_secret'])) return;

		if (HypCommonFunc::loadClass('TwitterOAuth')) {
			$link = '';
			if ($page) {
				$link = $this->get_page_uri($page, TRUE);
				if (
					($this->root->bitly_login && $this->root->bitly_apiKey && intval($this->root->static_url) !== 1 && ! preg_match('/^[\x20-\x7E]+$/i', $page))
					 ||
					((!$this->root->bitly_login || !$this->root->bitly_apiKey) && strlen($link) > 140)
				   ){
					$link = $this->cont['HOME_URL'] . '?pgid=' . $this->get_pgid_by_name($page) . '&rd';
				}
				$link = ' ' . $this->bitly($link);
			}
			$max = $link? (140 - strlen($link)) : 140;

			if ($convert) {
				$page = isset($this->root->vars['page'])? $this->root->vars['page'] : '';
				if ($this->root->bitly_clickable) {
					$_bitly_clickable = $this->root->bitly_clickable;
					$this->root->bitly_clickable = 1;
				}
				$_hierarchy_insert = $this->root->hierarchy_insert;
				$this->root->hierarchy_insert = '';
				$msg = strip_tags($this->convert_html($msg, $page));
				$this->root->hierarchy_insert = $_hierarchy_insert;
				if ($this->root->bitly_clickable) $this->root->bitly_clickable = $_bitly_clickable;
			}

			$msg = $this->strip_emoji($msg);

			//$msg = preg_replace('/(https?:\/\/[\w\/\@\$()!?&%#:;.,~\'=*+-]+)/ie', "\$this->bitly('$1')", $msg);

			if (mb_strlen($msg) > $max) {
				$msg = mb_substr($msg, 0, $max - 3) . '...';
			}
			if ($link) $msg .= $link;

			$msg = mb_convert_encoding($msg, 'UTF-8', $this->cont['SOURCE_ENCODING']);

			$to = new TwitterOAuth($this->root->twitter_consumer_key, $this->root->twitter_consumer_secret, $user_pref['twitter_access_token'], $user_pref['twitter_access_token_secret']);
			$to->OAuthRequest('https://twitter.com/statuses/update.xml', 'POST', array('status' => $msg));
		}
	}

	function bitly($url, $returnEmpty = FALSE, $cache = 864000) {
		$ret = $returnEmpty? '' : $url;
		if ($this->root->bitly_login && $this->root->bitly_apiKey) {
			if (strtolower(substr($url, 0, 14)) === 'http://bit.ly/'
			|| ($this->root->bitly_domain_internal && preg_match('#^http://'.preg_quote($this->root->bitly_domain_internal, '#').'/#i', $url))
			|| ($this->root->bitly_domain_external && preg_match('#^http://'.preg_quote($this->root->bitly_domain_external, '#').'/#i', $url))
			    ) {
				$ret = $url;
			} else if (! $cache || ! $ret = $this->cache_get_db($sha1 = sha1($url), 'bitly', false, true)) {
				$domain = '';
				if ($this->root->bitly_domain_internal || $this->root->bitly_domain_external) {
					if (preg_match('/^' . preg_quote($this->cont['ROOT_URL'], '/') . '/i', $url)) {
						if ($this->root->bitly_domain_internal) {
							$domain = $this->root->bitly_domain_internal;
						}
					} else {
						if ($this->root->bitly_domain_external) {
							$domain = $this->root->bitly_domain_external;
						}
					}
				}
				$q = 'http://api.bitly.com/v3/shorten?longUrl=' . urlencode($url) . '&format=txt';
				$q .= '&login=' . $this->root->bitly_login . '&apiKey=' . $this->root->bitly_apiKey;
				if ($domain) {
					$q .= '&domain=' . urlencode($domain);
				}
				$res = $this->http_request($q);
				if ($res['rc'] === 200 && substr($res['data'], 0, 7) === 'http://')	{
					$ret = trim($res['data']);
					if ($domain && $domain !== 'bit.ly') {
						// domain を指定しても bit.ly で返ってくることがある？
						$ret = str_replace('http://bit.ly', 'http://'.$domain, $ret);
					}
					if ($cache) {
						$this->cache_save_db($ret, 'bitly', $cache, $sha1);
					}
				}
			}
		}
		return $ret;
	}

	function bitlize($str) {
		return preg_replace('/(https?:\/\/[\w\/\@\$()!?&%#:;.,~\'=*+-]+)/ie', "\$this->bitly('$1')", $str);
	}

	function bytes2KMT($bytes, $decimal = 1, $threshold = 921) {
		$s = array(' B', ' KB', ' MB', ' TB');
		$max = count($s) - 1;
		$i = 0;
		$d = $bytes;
		while($i < $max && $d > $threshold) {
			$d = $d / 1024;
			$i++;
		}
		$c = pow(10, $decimal);
		return ceil($d * $c)/$c . $s[$i];
	}

	function emoji2img($str) {
		static $mpc = null;
		if (preg_match('/\(\([eisv]:[0-9a-f]{4}\)\)|\[emj:\d{1,4}(?::(?:im|ez|sb))?\]/S', $str)) {
			if (! $mpc) {
				if (! XC_CLASS_EXISTS('MobilePictogramConverter')) {
					HypCommonFunc::loadClass('MobilePictogramConverter');
				}
				$mpc = MobilePictogramConverter::factory_common();
				$mpc->setImagePath($this->cont['ROOT_URL'] . 'images/emoji');
			}
			$mpc->setString($str, FALSE);
			$mpc->userAgent = '';
			$str = $mpc->autoConvertModKtai();
		}
		return $str;
	}

	function strip_emoji($str) {
		return preg_replace('/\(\([eisv]:[0-9a-f]{4}\)\)|\[emj:\d{1,4}(?::(?:im|ez|sb))?\]/S', '', $str);
	}

	function file_get_contents($filename, $incpath = false, $resource_context = null, $offset = -1, $maxlen = -1) {
		return HypCommonFunc::file_get_contents($filename, $incpath, $resource_context, $offset, $maxlen);
	}

	function flock_get_contents($filename, $maxRetry = 10) {
		return HypCommonFunc::flock_get_contents($filename, $maxRetry);
	}

	function flock_put_contents($filename, $src, $mode = 'wb', $maxRetry = 10) {
		return HypCommonFunc::flock_put_contents($filename, $src, $mode, $maxRetry);
	}

	function get_popup_pos($params) {
		$popup_pos = '';
		foreach(array('top', 'left', 'bottom', 'right', 'width', 'height') as $_prm) {
			if (!empty($params[$_prm])) {
				if (preg_match('/^(\d+)(%|p(?:x|c|t)|e(?:m|x)|in|(?:c|m)m)?/', $params[$_prm], $_match)) {
				 	if (empty($_match[2])) $_match[2] = 'px';
				 	$popup_pos .= ',' . $_prm . ':\'' . $_match[1] . $_match[2] . '\'';
				}
			}
		}
		return $popup_pos;
	}

	function nl2br($text, $escaped_quote = false) {
        if ($escaped_quote) $text = str_replace('\\"', '"', $text);
        return str_replace(array("\r\n", "\r", "\n"), '&br;', $text);
	}

	// clear output buffer
	function clear_output_buffer() {
		while( ob_get_level() ) {
			if (! ob_end_clean()) {
				break;
			}
		}
	}

	// entity を考慮した substr
	function substr_entity($str, $start, $len = null, $htmlspecialchar = true) {
		$str = htmlspecialchars_decode($str, ENT_QUOTES);
		if (is_null($len)) {
			$str = mb_substr($str, $start);
		} else {
			$str = mb_substr($str, $start, $len);
		}
		// 末尾に分断された実態参照があれば削除
		$str = preg_replace('/&([^;]+)?$/', '', $str);
		// サニタイズ
		if ($htmlspecialchar) $str = str_replace('&amp;', '&', htmlspecialchars($str));
		return $str;
	}

/*----- DB Functions -----*/
	// Over write pukiwiki_func
	function is_freeze($page, $clearcache = FALSE) {
		static $is_freeze = array();

		if ($clearcache === TRUE) {
			unset($is_freeze[$this->root->mydirname][$page]);
		}

		if (isset($is_freeze[$this->root->mydirname][$page])) return $is_freeze[$this->root->mydirname][$page];

			if (! $this->root->function_freeze || ! $this->is_page($page)) {
			$is_freeze[$this->root->mydirname][$page] = FALSE;
			return FALSE;
		}

		$s_page = addslashes($page);
		$case = ($this->root->page_case_insensitive)? '_ci' : '';
		$db =& $this->xpwiki->db;
		$query = "SELECT `freeze` FROM ".$db->prefix($this->root->mydirname."_pginfo")." WHERE name".$case."='$s_page' LIMIT 1";

		if ($res = $db->query($query)) {
			list($freeze) = $db->fetchRow($res);
			$is_freeze[$this->root->mydirname][$page] = (bool)$freeze;
			return $is_freeze[$this->root->mydirname][$page];
		} else {
			return parent::is_freeze($page, $clearcache);
		}
	}

	//ページ名からページIDを求める
	function get_pgid_by_name ($page, $cache = true, $make = false)
	{
		if ($cache && isset($this->root->pgids[$page])) return $this->root->pgids[$page];

		$s_page = addslashes($page);

		$case = ($this->root->page_case_insensitive)? '_ci' : '';
		$db =& $this->xpwiki->db;
		$query = "SELECT `pgid` FROM ".$db->prefix($this->root->mydirname."_pginfo")." WHERE name".$case."='$s_page' LIMIT 1";
		$res = $db->query($query);
		list($ret) = $db->fetchRow($res);
		if (!$ret && $make) {
			$query = "INSERT INTO ".$db->prefix($this->root->mydirname."_pginfo").
						" (`name`,`name_ci`)" .
						" values('$s_page','$s_page')";
			$res = $db->queryF($query);
			return ($res)? $this->get_pgid_by_name($page) : 0;
		}
		if ($ret) $this->root->pgids[$page] = $ret;
		return $ret;
	}

	// トップ・セカンドレベルのページIDを求める
	function get_pgids_by_name ($page) {
		$pages = array_pad(explode('/', $page), 2, '');
		$pgid1 = $this->get_pgid_by_name($pages[0]);
		$pgid2 = ($pages[1])? $this->get_pgid_by_name($pages[0].'/'.$pages[1]) : 0;
		return array($pgid1, $pgid2);
	}

	//ページIDからページ名を求める
	function get_name_by_pgid($id)
	{
		static $page_name = array();
		if (isset($page_name[$this->root->mydirname][$id])) return $page_name[$this->root->mydirname][$id];

		$db =& $this->xpwiki->db;
		$query = "SELECT `name` FROM ".$db->prefix($this->root->mydirname."_pginfo")." WHERE pgid='$id' LIMIT 1";
		$res = $db->query($query);
		if (!$res) return '';
		list($ret) = $db->fetchRow($res);
		$page_name[$id] = strval($ret);
		return $ret;
	}

	//ページ名から最初の見出しを得る
	function get_heading($page, $init=false)
	{
		static $ret = array();
		$page = $this->strip_bracket($page);

		if (isset($ret[$this->root->mydirname][$page])) return $ret[$this->root->mydirname][$page];

		$page = addslashes($page);
		$db =& $this->xpwiki->db;
		$query = "SELECT `title` FROM ".$db->prefix($this->root->mydirname."_pginfo")." WHERE name='$page' LIMIT 1;";
		$res = $db->query($query);
		if (!$res) return "";
		$_ret = $db->fetchRow($res);
		if ($_ret[0] === '- no title -') $_ret[0] = $page;
		$_ret = preg_replace('/&amp;(#?[a-z0-9]+?);/i', '&$1;', htmlspecialchars($_ret[0], ENT_QUOTES));
		return $ret[$this->root->mydirname][$page] = ($_ret || $init)? $_ret : htmlspecialchars($page,ENT_NOQUOTES);
	}

	// 全ページ名を配列にDB版
	function get_existpages($nocheck = FALSE, $base = '', $options = array())
	{
		// File版を使用
		if (is_string($nocheck) && $nocheck !== $this->cont['DATA_DIR']) {
			return parent::get_existpages($nocheck,$base);
		}

		static $_aryret = array();

		// キャッシュクリア
		if ($base === FALSE) {
			$_aryret[$this->root->mydirname] = array();
			return;
		}

		if (isset($_aryret[$this->root->mydirname]['pages']) && $nocheck === FALSE && $base === '' && !$options) {
			$this->root->pgids = $_aryret[$this->root->mydirname]['pgids'];
			$this->root->pgorders = $_aryret[$this->root->mydirname]['pgorders'];
			return $_aryret[$this->root->mydirname]['pages'];
		}

		$keys = array(
			'where'     => '',
			'limit'     => 0,
			'order'     => '',
			'nolisting' => FALSE,
			'nochild'  => FALSE,
			'nodelete'  => TRUE,
			'withtime'  => FALSE,
			'select'    => array(),
			'asguest'   => FALSE
		);
		foreach ($keys as $key => $def) {
			$$key = (isset($options[$key]))? $options[$key] : $def ;
		}

		if ($asguest) {
			$_userinfo = $this->root->userinfo;
			$this->root->userinfo = $this->get_userinfo_by_id();
		}

		$aryret = array();

		if (!$nocheck) {
			$readable_where = $this->get_readable_where();
			if ($where)
				$where = $readable_where? " (" . $readable_where . ") AND ($where)" : $where;
			else
				$where = $readable_where;
		}

		if ($base)
		{
			if (substr($base,-1) == '/')
			{
				$base = addslashes(substr($base,0,-1));
				if ($nochild)
					$base_where = "name LIKE '$base/%' AND name NOT LIKE '$base/%/%'";
				else
					$base_where = "name LIKE '$base/%'";
			}
			else
			{
				$base = addslashes($this->strip_bracket($base));
				if ($nochild)
					$base_where = "name LIKE '$base%' AND name NOT LIKE '$base%/%'";
				else
					$base_where = "name LIKE '$base%'";
			}
			if ($where)
				$where = " ($base_where) AND ($where)";
			else
				$where = " $base_where";

		}
		else
		{
			if ($nochild)
			{
				$base_where = "name NOT LIKE '%/%'";

				if ($where)
					$where = " ($base_where) AND ($where)";
				else
					$where = " $base_where";
			}
		}
		if ($nolisting && $this->root->non_list_like)
		{
			static $nolist_like = array();
			if (! isset($nolist_like[$this->root->mydirname])) {
				$_nolist_like = array();
				foreach (explode('#', $this->root->non_list_like) as $_like) {
					$_nolist_like[] = 'name NOT LIKE \'' . addslashes($_like) . '\'';
				}
				$nolist_like[$this->root->mydirname] = ' (' . join(' AND ', $_nolist_like) . ')';
			}
			if ($where)
				$where = $nolist_like[$this->root->mydirname] . " AND ($where)";
			else
				$where = $nolist_like[$this->root->mydirname];
		}
		if ($nodelete)
		{
			if ($where)
				$where = " (editedtime != 0) AND ($where)";
			else
				$where = " (editedtime != 0)";
		}
		if ($where) $where = " WHERE ".$where;
		$limit = ($limit)? " LIMIT $limit" : "";
		$_select = '';
		if ($select) {
			$keys = array_merge($select, array('name', 'pgid', 'pgorder'));
			$keys = array_unique($keys);
			$_select = '`' . join('`,`', $keys) . '`';
			$query = 'SELECT '.$_select.' FROM '.$this->xpwiki->db->prefix($this->root->mydirname."_pginfo").$where.$order.$limit;
		} else {
			$query = 'SELECT `editedtime`, `name`, `pgid`, `pgorder` FROM '.$this->xpwiki->db->prefix($this->root->mydirname."_pginfo").$where.$order.$limit;
		}
		$res = $this->xpwiki->db->query($query);
		if ($res)
		{
			if ($select) {
				while($data = $this->xpwiki->db->fetchArray($res)) {
					$aryret[$data['name']] = $data;
					$this->root->pgids[$data['name']] = $data['pgid'];
					$this->root->pgorders[$data['name']] = $data['pgorder'];
				}
			} else {
				while($data = $this->xpwiki->db->fetchRow($res)) {
					$aryret[$this->encode($data[1]).'.txt'] = ($withtime)? $data[0]."\t".$data[1] : $data[1];
					$this->root->pgids[$data[1]] = $data[2];
					$this->root->pgorders[$data[1]] = $data[3];
				}
			}
		}
		if ($nocheck === FALSE && $base === '' && !$options) {
			$_aryret[$this->root->mydirname]['pages'] = $aryret;
			$_aryret[$this->root->mydirname]['pgids'] = $this->root->pgids;
			$_aryret[$this->root->mydirname]['pgorders'] = $this->root->pgorders;
		}

		if ($asguest) {
			$this->root->userinfo = $_userinfo;
		}

		return $aryret;
	}

	// pginfo DB を更新
	function pginfo_db_write($page, $action, $pginfo, $notimestamp = FALSE, $empty_page_making = FALSE)
	{
		// pgid
		$id = $this->get_pgid_by_name($page);

		if ($action !== 'delete') {
			$file = $this->get_filename($page);
			$buildtime = $editedtime = filemtime($file) - $this->cont['LOCALZONE'];
			if ($empty_page_making) {
				$editedtime = 0;
			}
			$s_name = addslashes($page);

			foreach (array('uid', 'ucd', 'uname', 'einherit', 'vinherit', 'lastuid', 'lastucd', 'lastuname', 'reading', 'pgorder') as $key) {
				$$key = addslashes($pginfo[$key]);
			}
			foreach (array('eaids', 'egids', 'vaids', 'vgids') as $key) {
				if ($pginfo[$key] === 'all' || $pginfo[$key] === 'none') {
					$$key = $pginfo[$key];
				} else {
					$$key = '&'.$pginfo[$key].'&';
				}
			}

			// ページ名読み整形
			// 英数字は半角,カタカナは全角,ひらがなはカタカナに
			if (function_exists("mb_convert_kana"))
			{
				$reading = mb_convert_kana($reading,'aKVC');
			}

			//最初の見出し行取得
			$title = addslashes($this->get_heading_init($page));
		}

		// 新規作成
		if ($action == "insert")
		{
			if ($id)
			{
				// 以前に削除したページ
				$value = "`name`='$s_name' ," .
						"`title`='$title' ," .
						"`buildtime`='$buildtime' ," .
						"`editedtime`='$editedtime' ," .
						"`uid`='$uid' ," .
						"`ucd`='$ucd' ," .
						"`uname`='$uname' ," .
						"`freeze`='0' ," .
						"`einherit`='$einherit' ," .
						"`eaids`='$eaids' ," .
						"`egids`='$egids' ," .
						"`vinherit`='$vinherit' ," .
						"`vaids`='$vaids' ," .
						"`vgids`='$vgids' ," .
						"`lastuid`='$lastuid' ," .
						"`lastucd`='$lastucd' ," .
						"`lastuname`='$lastuname' ," .
						"`update`='0' ," .
						"`reading`='$reading' ," .
						"`name_ci`='$s_name' ," .
						"`pgorder`='$pgorder'";
				$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE pgid = '$id' LIMIT 1";
			}
			else
			{
				$query = "INSERT INTO ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo").
						" (`name`,`title`,`buildtime`,`editedtime`,`uid`,`ucd`,`uname`,`freeze`,`einherit`,`eaids`,`egids`,`vinherit`,`vaids`,`vgids`,`lastuid`,`lastucd`,`lastuname`,`update`,`reading`,`name_ci`,`pgorder`)" .
						" values('$s_name','$title','$buildtime','$editedtime','$uid','$ucd','$uname','0','$einherit','$eaids','$egids','$vinherit','$vaids','$vgids','$lastuid','$lastucd','$lastuname','0','$reading','$s_name','$pgorder')";
			}

			$result = $this->xpwiki->db->queryF($query);

			//投稿数カウントアップ
			//if ($uid && $countup_xoops)
			//{
			//	$user =new XoopsUser($uid);
			//	$user->incrementPost();
			//}
		}

		// ページ更新
		elseif ($action == "update")
		{
			$value = "`title`='$title' ," .
					"`editedtime`='$editedtime' ," .
					"`lastuid`='$lastuid' ," .
					"`lastucd`='$lastucd' ," .
					"`lastuname`='$lastuname' ," .
					"`pgorder`='$pgorder'";
			if ($reading) $value .= " ,`reading`='$reading'";
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE pgid = '$id' LIMIT 1";
			$result = $this->xpwiki->db->queryF($query);
		}

		// ページ削除
		elseif ($action == "delete")
		{

			$value = "`title`='' ," .
                     "editedtime=0";
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE pgid = '$id' LIMIT 1";
			$result = $this->xpwiki->db->queryF($query);
		}

		// get_existpages() のキャッシュクリア
		$this->get_existpages(FALSE, FALSE);

		// plain DB update
		if (empty($this->root->rtf['plaindb_up_now'])) {
			$this->need_update_plaindb($page, $action, $notimestamp);
		} else {
			$this->plain_db_write($page, $action, FALSE, $notimestamp);
		}
	}

	// freeze情報更新
	function pginfo_freeze_db_write ($page, $freeze) {

		// pgid
		$id = $this->get_pgid_by_name($page);

		if ($id) {
			$value = "`freeze`='$freeze'";
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE pgid = '$id' LIMIT 1";
			$result = $this->xpwiki->db->queryF($query);
		}
	}

	// 権限情報更新
	function pginfo_perm_db_write ($page, $pginfo, $change_uid = FALSE) {

		// pgid
		$id = $this->get_pgid_by_name($page);

		if ($id) {
			foreach (array('einherit', 'vinherit') as $key) {
				$$key = addslashes($pginfo[$key]);
			}
			foreach (array('eaids', 'egids', 'vaids', 'vgids') as $key) {
				if ($pginfo[$key] === 'all' || $pginfo[$key] === 'none') {
					$$key = $pginfo[$key];
				} else {
					$$key = '&'.trim($pginfo[$key],'&').'&';
				}
			}
			$value = "`einherit`='$einherit' ," .
					"`eaids`='$eaids' ," .
					"`egids`='$egids' ," .
					"`vinherit`='$vinherit' ," .
					"`vaids`='$vaids' ," .
					"`vgids`='$vgids'";
			if ($change_uid) {
				$uname = addslashes($pginfo['uname']);
				$value .= ",`uid`='{$pginfo['uid']}'";
				$value .= ",`uname`='{$uname}'";
			}
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE pgid = '$id' LIMIT 1";
			$result = $this->xpwiki->db->query($query);
		}
	}

	// ページ名のリネーム
	function pginfo_rename_db_write ($fromname, $toname) {
		// pgid
		$id = $this->get_pgid_by_name($fromname);
		if ($id) {
			// 宛先ページが以前に作成されたことがある
			if ($to_id = $this->get_pgid_by_name($toname)) {
				$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." WHERE pgid = '$to_id' LIMIT 1";
				$this->xpwiki->db->query($query);
			}

			// リンク情報更新準備
			$this->plain_db_write($fromname,"delete");

			$_toname = addslashes($toname);
			$reading = addslashes($this->get_page_reading($toname, TRUE));
			$value = "`name`='$_toname', `name_ci`='$_toname', `reading`='$reading'";
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE pgid = '$id' LIMIT 1";
			$result = $this->xpwiki->db->query($query);

			// リンク情報更新
			//$this->plain_db_write($toname,"insert");
			$this->need_update_plaindb($toname,"insert");
		}
	}

	// plane_text DB を更新
	function plain_db_write($page, $action, $init = FALSE, $notimestamp = FALSE)
	{
		if (!$pgid = $this->get_pgid_by_name($page)) return false;

		// For AutoLink
		if (! $init && $action !== 'update'){
			$this->autolink_dat_update();
		}

		$rel_pages = array();
		$data = '';
		// ページ読みのデータページはコンバート処理しない(過負荷対策)
		if ($page !== $this->root->pagereading_config_page)
		{
			$spc = array
			(
				array
				(
					'&lt;',
					'&gt;',
					'&amp;',
					'&quot;',
					'&#039;',
					'&nbsp;',
				)
				,
				array
				(
					'<',
					'>',
					'&',
					'"',
					"'",
					" ",
				)
			);

			// Clear page cache
			$this->clear_page_cache($page);

			$pobj = & XpWiki::getSingleton($this->root->mydirname);
			$pobj->init($page);
			$GLOBALS['Xpwiki_'.$this->root->mydirname]['cache'] = null;
			$pobj->root->userinfo['admin'] = true;
			$pobj->root->userinfo['uname_s'] = '';
			$pobj->root->read_auth = 0;
			$pobj->root->rtf['is_init'] = true;
			$pobj->root->pagecache_min = 0;
			$pobj->execute();
			$data = $pobj->body;

			// remove javascript
			$data = preg_replace("#<script.+?/script>#i","",$data);

			// リンク先ページ名
			//$rel_pages = array_merge(array_keys($pobj->related), array_keys($pobj->notyets));
			$rel_pages = array_keys($pobj->related);
			$rel_pages = array_unique($rel_pages);

			// 未作成ページ
			if ($page !== $this->root->whatsdeleted && $page !== $this->cont['PLUGIN_RENAME_LOGPAGE'])
			{
				$yetlists = array();
				$notyets = array_keys($pobj->notyets);

				if (is_file($this->cont['CACHE_DIR']."yetlist.dat"))
				{
					$yetlists = unserialize(file_get_contents($this->cont['CACHE_DIR']."yetlist.dat"));
				}

				// ページ新規作成されたらリストから除外
				if ($action === 'insert') {
					if (isset($yetlists[$page])) {unset($yetlists[$page]);}
				}

				// とりあえず参照元リストから除去
				foreach($yetlists as $_notyet => $_pages) {
					$yetlists[$_notyet] = array_diff($_pages, array($page));
					if (!$yetlists[$_notyet]) { unset($yetlists[$_notyet]); }
				}

				// 削除時以外は参照元リストに追加
				if ($action !== 'delete' && $notyets) {
					foreach($notyets as $notyet) {
						$yetlists[$notyet][] = $page;
						$yetlists[$notyet] = array_unique($yetlists[$notyet]);
					}
				}

				if ($fp = fopen($this->cont['CACHE_DIR']."yetlist.dat","wb"))
				{
					fputs($fp, serialize($yetlists));
					fclose($fp);
				}
			}

			// 付箋
			if (empty($GLOBALS['Xpwiki_'.$this->root->mydirname]['cache']['fusen']['loaded'])){
				if ($fusen = $this->get_plugin_instance('fusen')) {
					if ($fusen_data = $fusen->plugin_fusen_data($page)) {
						$fusen_tag = $fusen->plugin_fusen_gethtml($fusen_data, '');
						$data .= $fusen_tag;
					}
				}
			}

			$data = preg_replace("/".preg_quote("<a href=\"{$this->root->script}?cmd=edit&amp;page=","/")."[^\"]+".preg_quote("\">{$this->root->_symbol_noexists}</a>","/")."/","",$data);
			$data = str_replace($spc[0],$spc[1],strip_tags($data)).join(',',$rel_pages);

			// 英数字は半角,カタカナは全角,ひらがなはカタカナに
			if (function_exists("mb_convert_kana"))
			{
				$data = mb_convert_kana($data,'aKVC');
			}
		}
		$data = addslashes(preg_replace("/[\s]+/"," ",$data));

		// ページ更新
		if ($action === 'update') {
			$value = "plain='$data'";
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_plain")." SET $value WHERE pgid = $pgid;";
			if ($result = $this->xpwiki->db->queryF($query)) {
				//リンク先ページ
				$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_rel")." WHERE pgid = ".$pgid.";";
				$this->xpwiki->db->queryF($query);
				foreach ($rel_pages as $rel_page)
				{
					$relid = $this->get_pgid_by_name($rel_page);
					if ($pgid == $relid || !$relid) {continue;}
					$query = "INSERT INTO ".$this->xpwiki->db->prefix($this->root->mydirname."_rel")." (pgid,relid) VALUES(".$pgid.",".$relid.");";
					$this->xpwiki->db->queryF($query);
				}

				if (! $init && $notimestamp === FALSE && $this->check_readable_page($page, FALSE, FALSE, 0)) {
					// Send update ping
					$this->send_update_ping();
				}

				return true;
			} else {
				// Update なのにデータがない模様
				$action = 'insert';
			}
		}

		// 新規作成
		if ($action === 'insert')
		{
			// 念のため削除
			$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_plain")." WHERE `pgid`='$pgid' LIMIT 1";
			$this->xpwiki->db->queryF($query);

			$query = "INSERT INTO ".$this->xpwiki->db->prefix($this->root->mydirname."_plain")." (pgid,plain) VALUES($pgid,'$data');";
			$this->xpwiki->db->queryF($query);

			//リンク先ページ
			// 念のため削除
			$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_rel")." WHERE pgid = ".$pgid.";";
			$this->xpwiki->db->queryF($query);
			foreach ($rel_pages as $rel_page)
			{
				$relid = $this->get_pgid_by_name($rel_page);
				if ($pgid == $relid || !$relid) {continue;}
				$query = "INSERT INTO ".$this->xpwiki->db->prefix($this->root->mydirname."_rel")." (pgid,relid) VALUES(".$pgid.",".$relid.");";
				$this->xpwiki->db->queryF($query);
			}

			//リンク元ページ
			// $pageがAutoLinkの対象となり得る場合
			if (! $init && $this->root->autolink
				and (preg_match('/^'.$this->root->WikiName.'$/',$page) ? $this->root->nowikiname : strlen($page) >= $this->root->autolink))
			{
				// $pageを参照していそうなページに一気に追加
				$this->root->search_non_list = 1;

				$lookup_page = $page;

				// 検索ページ名の共通リンクディレクトリを省略
				foreach($this->root->ext_autolinks as $valid => $autolink) {
					if ($autolink['url'] === '') {
						if (strpos($lookup_page, $autolink['base']) === 0) {
							$lookup_page = substr($lookup_page, strlen($autolink['base']) + 1);
							if ($this->root->autolink > strlen($lookup_page)){$lookup_page = $page;}
							break;
						}
					}
				}

				// Page Aliases
				$andor = 'AND';
				if ($alias = join(' ', $this->get_page_alias($page, TRUE))) {
					$andor = 'OR';
					$lookup_page .= ' ' . $alias;
				}

				// 検索実行
				$pages = (! empty($this->root->rtf['is_init']))? $this->do_source_search($lookup_page,$andor,TRUE) : $this->do_search($lookup_page,$andor,TRUE);

				foreach ($pages as $_page)
				{
					$refid = $this->get_pgid_by_name($_page);
					if ($pgid == $refid || !$refid) {continue;}
					$query = "INSERT INTO ".$this->xpwiki->db->prefix($this->root->mydirname."_rel")." (pgid,relid) VALUES(".$refid.",".$pgid.");";
					$result=$this->xpwiki->db->queryF($query);
					// 相手先ページも更新
					$this->plain_db_write($_page, 'update', FALSE, TRUE);
				}
			}

			// Send update ping
			if ($notimestamp === FALSE && $this->check_readable_page($page, FALSE, FALSE, 0)) {
				$this->send_update_ping();
			}
		}

		// ページ削除
		elseif ($action === 'delete')
		{
			$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_plain")." WHERE pgid = $pgid;";
			$result=$this->xpwiki->db->queryF($query);
			//if (!$result) echo $query."<hr>";

			//リンクページ
			$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_rel")." WHERE pgid = ".$pgid." OR relid = ".$pgid.";";
			$result=$this->xpwiki->db->queryF($query);

			// Optimaize DB tables
			$tables = array();
			foreach (array('attach', 'cache', 'count', 'pginfo', 'plain', 'rel', 'tb') as $table) {
				$tables[] = '`' . $this->xpwiki->db->prefix($this->root->mydirname . '_' . $table) .  '`';
			}
			$sql = 'OPTIMIZE TABLE '.join(',', $tables);
			$this->xpwiki->db->queryF($sql);
		}
		else
			return false;

		return true;
	}

	// attach DB を更新
	function attach_db_write($data,$action)
	{
		$ret = TRUE;

		//if (!$pgid = $data['pgid']) return false;

		$id = (int)@$data['id'];
		$pgid = (int)@$data['pgid'];
		$name = addslashes(@$data['name']);
		$type = addslashes(@$data['type']);
		$mtime = (int)@$data['mtime'];
		$size = (int)@$data['size'];
		// $mode normal=0, isbn=1, thumb=2
		$mode = (preg_match("/^ISBN.*\.(dat|jpg)/",$name))? 1 : ((preg_match("/^\d\d?%/",$name))? 2 : 0);
		$age = (int)@$data['status']['age'];
		$count = (int)@$data['status']['count'][$age];
		$pass = addslashes(@$data['status']['pass']);
		$freeze = (int)@$data['status']['freeze'];
		$owner = addslashes(@$data['status']['owner']);
		$copyright = (int)@$data['status']['copyright'];

		// 新規作成
		if ($action == "insert")
		{
			$query = "INSERT INTO ".$this->xpwiki->db->prefix($this->root->mydirname."_attach")." (`pgid`,`name`,`type`,`mtime`,`size`,`mode`,`count`,`age`,`pass`,`freeze`,`copyright`,`owner`) VALUES('$pgid','$name','$type','$mtime','$size','$mode','$count','$age','$pass','$freeze','$copyright','$owner');";
			$result=$this->xpwiki->db->queryF($query);
			//if (!$result) echo $query."<hr>";
		}

		// 更新
		elseif ($action == "update")
		{
			$value = "`pgid`='$pgid'"
			.",`name`='$name'"
			.",`type`='$type'"
			.",`mtime`='$mtime'"
			.",`size`='$size'"
			.",`mode`='$mode'"
			.",`count`='$count'"
			.",`age`='$age'"
			.",`pass`='$pass'"
			.",`freeze`='$freeze'"
			.",`copyright`='$copyright'"
			.",`owner`='$owner'";
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_attach")." SET $value WHERE `id`='$id' LIMIT 1";
			$result=$this->xpwiki->db->queryF($query);
			//if (!$result) echo $query."<hr>";
		}

		// ファイル削除
		elseif ($action == "delete")
		{
			$q_name = ($name)? " AND name='{$name}' AND age='{$age}' LIMIT 1" : "";

			$ret = array();
			$query = "SELECT name FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_attach")." WHERE `pgid` = {$pgid}{$q_name};";
			if ($result=$this->xpwiki->db->query($query))
			{
				while($data = $this->xpwiki->db->fetchRow($result))
				{
					$ret[] = $data[0];
				}
			}
			if (!$ret) $ret = TRUE;

			$query = "DELETE FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_attach")." WHERE `pgid` = {$pgid}{$q_name};";

			$result=$this->xpwiki->db->queryF($query);
			//if (!$result) echo $query."<hr>";
		}
		else
			return false;

		return $ret;
	}

	function get_attachfile_id ($page, $name, $age) {
		$data = 0;
		$pgid = $this->get_pgid_by_name($page);
		$name = addslashes($name);
		$query = "SELECT `id` FROM ".$this->xpwiki->db->prefix($this->root->mydirname."_attach")." WHERE `pgid`='{$pgid}' AND `name`='{$name}' AND age='{$age}' LIMIT 1";
		if ($result=$this->xpwiki->db->query($query)) {
			$data = $this->xpwiki->db->fetchRow($result);
			$data = $data[0];
		}
		return $data;
	}

	// Get attachDB info
	function get_attachdbinfo ($id) {
		$dbinfo = array();
		$query = 'SELECT `type`, `mtime`, `size` FROM '.$this->xpwiki->db->prefix($this->root->mydirname.'_attach').' WHERE `id`=\''.$id.'\' LIMIT 1';
		if ($result = $this->xpwiki->db->query($query)) {
			$dbinfo = $this->xpwiki->db->fetchArray($result);
		}
		return $dbinfo;
	}

	function get_attachstatus ($file) {
		if (is_array($file)) {
			$page = $file['page'];
			$name = $file['name'];
			$age  = $file['age'];
		} else {
			$file = basename($file);
			$pattern = "/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)(?:\.([0-9]+))?$/";
			if (preg_match($pattern, $file, $matches)) {
				$page = $this->decode($matches[1]);
				$name = $this->decode($matches[2]);
				$age  = isset($matches[3]) ? $matches[3] : 0;
			} else {
				return array();
			}
		}
		$obj = & new XpWikiAttachFile($this->xpwiki, $page, $name, $age);
		if ($obj->getstatus()) {
			$status = $obj->status;
		} else {
			$status = array();
		}
		$obj = NULL;
		unset($obj);
		return $status;
	}

	// プラグインからplane_text DB を更新を指示(コンバート時)
	function need_update_plaindb($page = null, $mode = 'update', $notimestamp = TRUE, $soon = TRUE, $wait = 0)
	{
		// Do nothing on plainDB update.
		if (! empty($this->root->rtf['is_init'])) return;

		if (is_null($page)) $page = $this->root->vars['page'];

		// Do nothing on render mode.
		if ($page === '#RenderMode') return;

		// Regist JobStack
		if ($mode === 'update' && $notimestamp) {
			$mode = 'update_notimestamp';
		}
		$data = array('action' => 'plain_up', 'page' => $page, 'mode' => $mode);
		if (!$wait) $this->unregist_jobstack($data);
		$ttl = ($soon)? 0 : 864000;
		$this->regist_jobstack($data, $ttl, $wait);

		return;
	}

	// データベースからリンクされているページを得る
	function links_get_related_db($page, $start = 0, $max = 0)
	{
		static $links = array();

		if (isset($links[$this->root->mydirname][$page])) {
			return $links[$this->root->mydirname][$page];
		}

		$limit = $max? ' LIMIT ' . $start . ',' . $max : '';

		$where = "`relid` = ".$this->get_pgid_by_name($page)." AND p.pgid = r.pgid";
		$r_where = $this->get_readable_where('p.');
		if ($r_where) {
			$where = "($where AND ($r_where))";
		}
		$where = " WHERE " . $where;

		$query = "SELECT p.name, p.editedtime FROM `".$this->xpwiki->db->prefix($this->root->mydirname."_rel")."` AS r, `".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")."` AS p " . $where . $limit;
		$result = $this->xpwiki->db->query($query);
		//echo $query;

		$ret = array();
		if ($result) {
			while(list($name,$time) = $this->xpwiki->db->fetchRow($result)) {
				$ret[$name] = $time;
			}
		}

		if (! $limit) {
			$links[$this->root->mydirname][$page] = $ret;
		}

		return $ret;
	}

	// データベースからリンクされているページ数を得る
	function links_count_related_db($page)
	{
		static $links = array();

		if (isset($links[$this->root->mydirname][$page])) {
			return $links[$this->root->mydirname][$page];
		}

		$where = "`relid` = ".$this->get_pgid_by_name($page)." AND p.pgid = r.pgid";
		$r_where = $this->get_readable_where('p.');
		if ($r_where) {
			$where = "($where AND ($r_where))";
		}
		$where = " WHERE " . $where;

		$query = "SELECT COUNT(*) FROM `".$this->xpwiki->db->prefix($this->root->mydirname."_rel")."` AS r, `".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")."` AS p " . $where;
		$result = $this->xpwiki->db->query($query);
		//echo $query;

		$links[$this->root->mydirname][$page] = 0;
		if ($result) {
			list($links[$this->root->mydirname][$page]) = $this->xpwiki->db->fetchRow($result);
		}

		return $links[$this->root->mydirname][$page];
	}

	// データベースからリンクしているページを得る
	function links_get_linked_db($page)
	{
		static $links = array();

		if (isset($links[$this->root->mydirname][$page])) {return $links[$this->root->mydirname][$page];}
		$links[$this->root->mydirname][$page] = array();

		$where = "r.pgid = ".$this->get_pgid_by_name($page)." AND p.pgid = r.relid";
		$r_where = $this->get_readable_where('p.');
		if ($r_where) {
			$where = "($where AND ($r_where))";
		}
		$where = " WHERE " . $where;

		$query = "SELECT p.name, p.editedtime FROM `".$this->xpwiki->db->prefix($this->root->mydirname."_rel")."` AS r, `".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")."` AS p ".$where;
		$result = $this->xpwiki->db->query($query);
		//echo $query;

		if ($result)
		{
			while(list($name,$time) = $this->xpwiki->db->fetchRow($result))
			{
				$links[$this->root->mydirname][$page][$name] = $time;
			}
		}

		return $links[$this->root->mydirname][$page];
	}

	// 閲覧権限チェック用 WHERE句取得
	function get_readable_where ($table = '', $is_admin = NULL, $uid = NULL) {
		static $where = array();

		// for renderer mode
		$this->root->rtf['disable_render_cache'] = TRUE;

		if (is_null($is_admin)) $is_admin = $this->root->userinfo['admin'];
		if (is_null($uid)) $uid = $this->root->userinfo['uid'];

		$key = ($is_admin)? ("-1".$table) : ("$uid".$table);

		if (!isset($where[$this->root->mydirname][$key]))
		{
			if ($is_admin) {
				$where[$this->root->mydirname][$key] = '';
			} else if (! $this->root->module['checkRight']) {
				$where[$this->root->mydirname][$key] = '0 != 0';
			} else {
				$_where = "";
				if ($uid) $_where .= " ({$table}`uid` = '$uid') OR";
				$_where .= " ({$table}`vaids` = 'all')";
				if ($uid) $_where .= " OR ({$table}`vaids` LIKE '%&{$uid}&%')";
				foreach($this->get_mygroups($uid) as $gid)
				{
					$_where .= " OR ({$table}`vgids` LIKE '%&{$gid}&%')";
				}
				$where[$this->root->mydirname][$key] = $_where.' ';
			}
		}
		return $where[$this->root->mydirname][$key];
	}

	// plain Text を取得する
	function get_plain_text_db ($page) {

		$pgid = $this->get_pgid_by_name($page);

		$query = 'SELECT `plain` FROM `'.$this->xpwiki->db->prefix($this->root->mydirname."_plain").'` WHERE `pgid` = \''.$pgid.'\' LIMIT 1';
		$result = $this->xpwiki->db->query($query);

		$text = '';
		if ($result)
		{
			list($text) = $this->xpwiki->db->fetchRow($result);
		}
		return $text;
	}

	function get_pg_buildtime($page) {
		$pgid = $this->get_pgid_by_name($page);

		$query = 'SELECT `buildtime` FROM `'.$this->xpwiki->db->prefix($this->root->mydirname."_pginfo").'` WHERE `pgid` = \''.$pgid.'\' LIMIT 1';
		$result = $this->xpwiki->db->query($query);

		$time = NULL;
		if ($result)
		{
			list($time) = $this->xpwiki->db->fetchRow($result);
			//$time = $time + $this->cont['LOCALZONE'];
		}
		return $time;
	}

	// ページの最終更新時間を更新
	function touch_db($page)
	{
		if ($id = $this->get_pgid_by_name($page))
		{
			clearstatcache();
			$editedtime = $this->get_filetime($page);
			$value = "`editedtime` = '$editedtime' ,".
					"`lastuid`='{$this->root->userinfo['uid']}' ," .
					"`lastucd`='{$this->root->userinfo['ucd']}' ," .
					"`lastuname`='{$this->root->cookie['name']}'";
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE `pgid`='$id'";
			$result = $this->xpwiki->db->queryF($query);
		}
	}

	// 'Search' main function (DB版)
	function do_search($words, $type = 'AND', $non_format = FALSE, $base = '', $options = array())
	{
		$def_options = array(
			'db'     => TRUE,
			'field'  => 'name,text',
			'limit'  => 0,
			'offset' => 0,
			'userid' => 0,
			'spZen'  => FALSE,
			'context'    => '',
			'resultMax' => 0,
			'msg_more_search' => '',
		);

		$options = array_merge($def_options, $options);

		if ($this->cont['LANG'] === 'ja' && function_exists("mb_convert_kana") && $options['spZen']) {
			$words = mb_convert_kana($words, 's');
		}

		if (!$options['db']) return parent::do_search($words, $type, $non_format, $base);

		$keywords = preg_split('/\s+/', $words, -1, PREG_SPLIT_NO_EMPTY);

		$fields = explode(',', $options['field']);

		$andor = ($type === 'AND')? 'AND' : 'OR';

		$where_readable = $this->get_readable_where('p.');
		$where = "p.editedtime != 0";
		if ($base) {
			$where .= " AND p.name LIKE '".addslashes($base)."%'";
		}
		if ($where_readable) {
			$where = "$where AND ($where_readable)";
		}

		$sel_desc = ($options['context'] === 'db')? ', t.plain' : '';

		$sql = 'SELECT p.name, p.editedtime, p.title'.$sel_desc.' FROM '.$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." p INNER JOIN ".$this->xpwiki->db->prefix($this->root->mydirname."_plain")." t ON t.pgid=p.pgid WHERE ($where) ";
		if ( $options['userid'] != 0 ) {
			$sql .= "AND (p.uid=".$options['userid'].") ";
		}

		if ( is_array($keywords) && $keywords ) {
			// 英数字は半角,カタカナは全角,ひらがなはカタカナに
			$sql .= "AND (";
			$i = 0;
			foreach ($keywords as $keyword) {
				if ($i++ !== 0) $sql .= " $andor ";
				if ($this->cont['LANG'] === 'ja' && function_exists("mb_convert_kana"))
				{
					// 英数字は半角,カタカナは全角,ひらがなはカタカナに
					$word = addslashes(mb_convert_kana($keyword,'aKCV'));
				} else {
					$word = addslashes($keyword);
				}
				if (in_array('name', $fields) && in_array('text', $fields)) {
					$sql .= "(p.name_ci LIKE '%{$word}%' OR t.plain LIKE '%{$word}%')";
				} else if (in_array('name', $fields)) {
					$sql .= "p.name_ci LIKE '%{$word}%'";
				} else if (in_array('text', $fields)) {
					$sql .= "t.plain LIKE '%{$word}%'";
				}
			}
			$sql .= ") ";
		}

		$result = $this->xpwiki->db->query($sql, $options['limit'], $options['offset']);

		$ret = array();

		if (!$keywords) $keywords = array();
		$sword = rawurlencode(join(' ',$keywords));

		$pages = array();
		if (in_array('source', $fields)) {
			foreach($this->do_source_search ($word, $type, true, $base) as $page) {
				$pages[$page] = '';
			}
		}
		while($myrow = $this->xpwiki->db->fetchArray($result)) {
			$pages[$myrow['name']] = array($myrow['editedtime'], $myrow['title']);
			if ($options['context'] === 'db') $pages[$myrow['name']][2] = $myrow['plain'];
		}

		if ($non_format) return array_keys($pages);

		$r_word = rawurlencode($words);
		$s_word = preg_replace('/&amp;#(\d+;)/', '&#$1', htmlspecialchars($words));

		if (empty($pages))
			return str_replace('$1', $s_word, $this->root->_msg_notfoundresult);

		ksort($pages);

		$count = count($this->get_existpages());
		$resCount = count($pages);

		if ($options['resultMax'] && $resCount > $options['resultMax']) {
			$pages = array_splice($pages, 0, $options['resultMax']);
		}

		$retval = '<ul class="list1">' . "\n";
		foreach ($pages as $page => $data) {
			if (empty($data[0])) $data[0] = $this->get_filetime($page);
			if (empty($data[1])) $data[1] = $this->get_heading($page);
			$s_page  = htmlspecialchars($page);
			$passage = $this->root->show_passage ? ' ' . $this->get_passage($data[0]) : '';
			$retval .= ' <li><a href="' . $this->get_page_uri($page, TRUE) . ($this->root->static_url ? '?' : '&amp;') . 'word=' . $r_word . '">' . $s_page .
				'</a><small>' . $passage . '</small> [ ' . htmlspecialchars($data[1]) . ' ]' . "\n";
			if ($options['context'] === 'db') {
				$retval .= '<div class="context">' . HypCommonFunc::make_context($data[2], $keywords) . '</div>';
			} else if ($options['context'] === 'conv') {
				$retval .= $this->get_page_context($page, $keywords);
			}
			$retval .= '</li>';
		}
		$retval .= '</ul>' . "\n";

		$retval .= str_replace('$1', $s_word, str_replace('$2', $resCount,
			str_replace('$3', $count, ($andor === 'AND') ? $this->root->_msg_andresult : $this->root->_msg_orresult)));
		if ($options['msg_more_search'] && $options['resultMax'] && $resCount > $options['resultMax']) {
			$retval .= $options['msg_more_search'];
		}

		return $retval;
	}

	// Wikiソーステキストから検索
	function do_source_search ($word, $type = 'AND', $non_format = FALSE, $base = '') {
		return parent::do_search ($word, $type, $non_format, $base);
	}

	// ページ読みを取得
	function get_page_reading ($page, $init = FALSE) {
		// 無効になっている
		if (! $this->root->pagereading_enable) return '';

		$reading = '';

		if (! $init) {
			$pgid = $this->get_pgid_by_name($page);

			if ($pgid) {
				$query = 'SELECT `reading` FROM `'.$this->xpwiki->db->prefix($this->root->mydirname."_pginfo").'` WHERE `pgid` = \''.$pgid.'\' LIMIT 1';
				$result = $this->xpwiki->db->query($query);

				if ($result)
				{
					list($reading) = $this->xpwiki->db->fetchRow($result);
				}
			}
		} else {
			$pgid = '';
		}

		if ($reading) return $reading;

		// Execute ChaSen/KAKASI, and get annotation
		switch(strtolower($this->root->pagereading_kanji2kana_converter)) {
		case 'chasen':
			if(! is_file($this->root->pagereading_chasen_path))
				$this->die_message('ChaSen not found: ' . $this->root->pagereading_chasen_path);

			$tmpfname = tempnam(realpath($this->cont['CACHE_DIR']), 'PageReading');
			$fp = fopen($tmpfname, 'w') or
				$this->die_message('Cannot write temporary file "' . $tmpfname . '".' . "\n");
			fputs($fp, mb_convert_encoding($page . "\n",
					$this->root->pagereading_kanji2kana_encoding, $this->cont['SOURCE_ENCODING']));
			fclose($fp);

			$chasen = "{$this->root->pagereading_chasen_path} -F %y \"$tmpfname\"";
			$fp     = popen($chasen, 'r');
			if($fp === FALSE) {
				unlink($tmpfname);
				$this->die_message('ChaSen execution failed: ' . $chasen);
			}
			$line = fgets($fp);
			$line = mb_convert_encoding($line, $this->cont['SOURCE_ENCODING'],
				$this->root->pagereading_kanji2kana_encoding);
			$line = chop($line);
			$reading = $line;
			pclose($fp);

			unlink($tmpfname) or
				$this->die_message('Temporary file can not be removed: ' . $tmpfname);
			break;

		case 'kakasi':	/*FALLTHROUGH*/
		case 'kakashi':
			if(! is_file($this->root->pagereading_kakasi_path))
				$this->die_message('KAKASI not found: ' . $this->root->pagereading_kakasi_path);

			$tmpfname = tempnam(realpath($this->cont['CACHE_DIR']), 'PageReading');
			$fp       = fopen($tmpfname, 'w') or
				$this->die_message('Cannot write temporary file "' . $tmpfname . '".' . "\n");
			fputs($fp, mb_convert_encoding($page . "\n",
				$this->root->pagereading_kanji2kana_encoding, $this->cont['SOURCE_ENCODING']));
			fclose($fp);

			$kakasi = "{$this->root->pagereading_kakasi_path} -kK -HK -JK < \"$tmpfname\"";
			$fp     = popen($kakasi, 'r');
			if($fp === FALSE) {
				unlink($tmpfname);
				$this->die_message('KAKASI execution failed: ' . $kakasi);
			}

			$line = fgets($fp);
			$line = mb_convert_encoding($line, $this->cont['SOURCE_ENCODING'],
				$this->root->pagereading_kanji2kana_encoding);
			$line = chop($line);
			$reading = $line;
			pclose($fp);

			unlink($tmpfname) or
				$this->die_message('Temporary file can not be removed: ' . $tmpfname);
			break;

		case 'none':
			$patterns = $replacements = $matches = array();
			foreach ($this->get_source($this->root->pagereading_config_dict) as $line) {
				$line = chop($line);
				if(preg_match('|^ /([^/]+)/,\s*(.+)$|', $line, $matches)) {
					$patterns[]     = $matches[1];
					$replacements[] = $matches[2];
				}
			}
			$reading = $page;
			foreach ($patterns as $no => $pattern)
				$reading = mb_convert_kana(mb_ereg_replace($pattern,
					$replacements[$no], $reading), 'aKCV');
			break;

		default:
			$this->die_message('Unknown kanji-kana converter: ' . $this->root->pagereading_kanji2kana_converter . '.');
			break;
		}

		$page_r = array();
		foreach(explode('/', $reading) as $_reading) {
			$page_r[] = mb_substr($_reading, 0, 1);
		}
		$reading = join('/', $page_r);

		// DBに保存
		if ($pgid) {
			$value = "`reading` = '".addslashes($reading)."'";
			$query = "UPDATE ".$this->xpwiki->db->prefix($this->root->mydirname."_pginfo")." SET $value WHERE `pgid`='$pgid'";
			$result = $this->xpwiki->db->queryF($query);
		}

		return $reading;
	}

	// ページ別名を取得
	function get_page_alias ($page, $as_array = false, $clr = false, $path = 'real') {
		static $pg_ary;

		if ($path !== 'real') $path = 'relative';

		if ($clr || !isset($pg_ary[$this->xpwiki->pid])) {
			$_tmp = $pg_ary[$this->xpwiki->pid] = array();
			$dirreg = '#^' . preg_quote($this->page_dirname($page), '#') . '/#';
			foreach($this->root->page_aliases as $_alias => $_page) {
				$pg_ary[$this->xpwiki->pid][$_page]['real'][] = $_alias;
				$pg_ary[$this->xpwiki->pid][$_page]['relative'][] = preg_replace($dirreg, '../', $_alias);
			}
			foreach($pg_ary[$this->xpwiki->pid] as $_page => $_ary) {
				natcasesort($pg_ary[$this->xpwiki->pid][$_page]['real']);
				natcasesort($pg_ary[$this->xpwiki->pid][$_page]['relative']);
			}
		}

		$ret = (isset($pg_ary[$this->xpwiki->pid][$page]))? $pg_ary[$this->xpwiki->pid][$page][$path] : array();
		if ($as_array) return $ret;
		return join(':', $ret);
	}

	// ページ別名を保存
	function put_page_alias ($page, $alias) {
		if (!$alias && in_array($page, $this->root->page_aliases) === false) return false;

		$alias = trim($alias);
		$alias = preg_replace('/[\r\n]+/', ':', $alias);
		$aliases = explode(':', $alias);
		if ($alias) {
			$aliases = array_map('trim', $aliases);
			$dirname = $this->page_dirname($page) . '/';
			//$aliases = str_replace('../', $dirname, $aliases);
			$aliases = $this->get_fullname($aliases, $page);
			natcasesort($aliases);
			$aliases = array_slice($aliases, 0);
		}

		$aliases_old = $this->get_page_alias($page, true);
		$aliases_old = array_slice($aliases_old, 0);

		if ($aliases_old === $aliases) return false;

		$this->root->page_aliases = array_diff($this->root->page_aliases, array($page));

		if ($alias) {
			foreach($aliases as $_alias) {
				$_check = ($this->root->page_case_insensitive)? $this->get_pagename_realcase($_alias) : $_alias;
				if (!isset($this->root->page_aliases[$_alias]) && !$this->is_page($_check)) {
					$this->root->page_aliases[$_alias] = $page;
				}
			}
		}

		// save
		$this->save_page_alias();

		// Cache remake of get_page_alias()
		$this->get_page_alias('', true, true);

		return true;
	}

	function save_page_alias () {
		natcasesort($this->root->page_aliases);
		$page_aliases_i = array_change_key_case($this->root->page_aliases, CASE_LOWER);

		$quote['from'] = array('\\',   "'",);
		$quote['to']   = array('\\\\', "\\'");

		$dat = "\$root->page_aliases = array(\n";
		foreach($this->root->page_aliases as $_alias => $_page) {
			$_alias = str_replace($quote['from'], $quote['to'], $_alias);
			$_page = str_replace($quote['from'], $quote['to'], $_page);
			$dat .= "\t'{$_alias}' => '{$_page}',\n";
		}
		$dat.= ");\n";

		//$this->save_config('pukiwiki.ini.php', 'page_aliases', $dat);

		$dat .= "\$root->page_aliases_i = array(\n";
		foreach($page_aliases_i as $_alias => $_page) {
			$_alias = str_replace($quote['from'], $quote['to'], $_alias);
			$_page = str_replace($quote['from'], $quote['to'], $_page);
			$dat .= "\t'{$_alias}' => '{$_page}',\n";
		}
		$dat.= ");";

		$this->save_config('pukiwiki.ini.php', 'page_aliases', $dat);

		// Clear cache *.api
		$GLOBALS['xpwiki_cache_deletes'][$this->cont['CACHE_DIR']]['api'] = '*.autolink.api';
	}

	// ページ別名が設定されているか
	function is_alias($name) {
		if ($this->root->page_case_insensitive) {
			$page_aliases = $this->root->page_aliases_i;
			$name = strtolower($name);
		} else {
			$page_aliases = $this->root->page_aliases;
		}
		return isset($page_aliases[$name])? $page_aliases[$name] : FALSE;
	}

	// ページオーダーを取得
	function get_page_order ($page) {
		if (! isset($this->root->pgorders[$page])) {
			$pgid = $this->get_pgid_by_name($page);
			$query = 'SELECT `pgorder` FROM `'.$this->xpwiki->db->prefix($this->root->mydirname."_pginfo").'` WHERE `pgid` = \''.$pgid.'\' LIMIT 1';
			if ($pgid && $result = $this->xpwiki->db->query($query)) {
				list($this->root->pgorders[$page]) = $this->xpwiki->db->fetchRow($result);
				$this->root->pgorders[$page] = floatval($this->root->pgorders[$page]);
			} else {
				$this->root->pgorders[$page] = 1;
			}
		}
		return $this->root->pgorders[$page];
	}

	// 大文字小文字を正しいページ名に矯正する
	function get_pagename_realcase (& $page) {

		if ($this->is_page($page)) return $page;

		$query = 'SELECT `name` FROM `'.$this->xpwiki->db->prefix($this->root->mydirname.'_pginfo').'` WHERE `name_ci` = \''.addslashes($page).'\' LIMIT 1';
		if ($result = $this->xpwiki->db->query($query)) {
			list($fixed_page) = $this->xpwiki->db->fetchRow($result);
			if ($fixed_page) {
				$page = $fixed_page;
				return $page;
			}
		}

		if (strpos($page, '/') !== false) {
			// ページがなくて多階層の場合は上層ページを検査する
			$base = $this->basename($page);
			$dir = dirname($page);
			$this->get_pagename_realcase($dir);
			$page = $dir.'/'.$base;
		}

		return $page;
	}

	// 指定ページ以下のページ数をカウントする
	function get_child_counts($page = '') {
		if ($page !== '') $page = addslashes(rtrim($page, '/') . '/');
		$where = $this->get_readable_where();
		$where = ($where)? " WHERE editedtime != 0 AND (name LIKE '{$page}%') AND (".$where.")" :  " WHERE editedtime != 0 AND (name LIKE '{$page}%')";
		$query = 'SELECT count(*) FROM '.$this->xpwiki->db->prefix($this->root->mydirname."_pginfo").$where;
		//echo $query;
		if ($res = $this->xpwiki->db->query($query)) {
			list($count) = $this->xpwiki->db->fetchRow($res);
		} else {
			$count = 0;
		}
		return $count;
	}

	function get_page_views($page = '') {
		if (!$page || !$this->is_page($page)) return 0;
		$pgid = $this->get_pgid_by_name($page);
		$count = 0;
		$sql = 'SELECT `count` FROM `'.$this->xpwiki->db->prefix($this->root->mydirname."_count").'` WHERE pgid = '.$pgid.' LIMIT 1';
		$res = $this->xpwiki->db->query($sql);
		if ($res) {
			list($count) = $this->xpwiki->db->fetchRow($res);
		}
		return $count;
	}

	//ページ名からページ作成日時と最終更新日時を求める
	function get_page_time_db($page) {
		static $cache = array();

		if (isset($cache[$this->root->mydirname][$page])) {
			return $cache[$this->root->mydirname][$page];
		}
		$time = array(0, 0);
		$s_page = addslashes($page);
		$case = ($this->root->page_case_insensitive)? '_ci' : '';
		$db =& $this->xpwiki->db;
		$query = "SELECT `buildtime`, `editedtime` FROM ".$db->prefix($this->root->mydirname."_pginfo")." WHERE name".$case."='$s_page' LIMIT 1";
		if ($res = $db->query($query)) {
			$time = $db->fetchRow($res);
		}
		$cache[$this->root->mydirname][$page] = $time;
		return $time;
	}

	/* やはり fstat(filemtime) のほうが早い模様
	// Get last-modified filetime of the page (DB版)
	function get_filetime($page) {
		static $times;

		if (isset($times[$this->root->mydirname][$page])) return $times[$this->root->mydirname][$page];

		$name = (@ $this->root->page_case_insensitive) ? 'name_ci' : 'name';
		$time = 0;
		$query = 'SELECT `editedtime` FROM `'.$this->xpwiki->db->prefix($this->root->mydirname.'_pginfo').'` WHERE `'.$name.'` = \''.addslashes($page).'\' LIMIT 1';
		if ($result = $this->xpwiki->db->query($query)) {
			list($time) = $this->xpwiki->db->fetchRow($result);
		}
		$times[$this->root->mydirname][$page] = $time ? $time - $this->cont['LOCALZONE'] : 0;

		return $times[$this->root->mydirname][$page];
	}
	*/

	function get_user_pref($uid = null) {
		if (! is_null($uid)) {
			if (! $uid) return array();
		}
		if ($data = $this->cache_get_db($uid, 'user_pref', FALSE, TRUE)) {
			if (is_array($data)) {
				$ret = array();
				foreach($data as $_data) {
					$ret[] = unserialize($_data);
				}
				return $data;
			} else {
				return unserialize($data);
			}
		} else {
			return array();
		}
	}

	function save_user_pref($uid, $array) {
		$data = serialize($array);
		$this->cache_save_db($data, 'user_pref', 86400*365*3, $uid);
	}

	function cache_save_db ($data, $plugin='core', $ttl=86400, $key=NULL, $mtime = NULL) {
		if (is_null($key)) $key = sha1($data);
		if (is_null($mtime)) $mtime = $this->cont['UTC'];

		$ret = $key;
		$key = addslashes($key);
		$plugin = addslashes($plugin);
		$data = addslashes($data);
		$ttl = intval($ttl);
		$dbtable = $this->xpwiki->db->prefix($this->root->mydirname.'_cache');

		// Old cache delete
		$sql = 'DELETE FROM `'.$dbtable.'` WHERE `ttl` > 0 AND (`mtime` + `ttl`) < '.$this->cont['UTC'];
		if ($res = $this->xpwiki->db->queryF($sql)) {
			list($count) = $this->xpwiki->db->fetchRow($res);
			if ($count) {
				$sql = 'OPTIMIZE TABLE `'.$dbtable.'`';
				$this->xpwiki->db->queryF($sql);
			}
		} else {
			// Table not found.
			return FALSE;
		}
		// check
		$sql = 'SELECT count(*) FROM `'.$dbtable.'` WHERE `key`=\''.$key.'\' AND `plugin`=\''.$plugin.'\'';
		$count = 0;
		if ($res = $this->xpwiki->db->query($sql)) {
			list($count) = $this->xpwiki->db->fetchRow($res);
		}
		if ($count) {
			$sql = 'UPDATE `'.$dbtable.'`';
			$sql .= ' SET `data`=\''.$data.'\',';
			$sql .= '`mtime`=\''.$mtime.'\',';
			$sql .= '`ttl`=\''.$ttl.'\'';
			$sql .= ' WHERE `key`=\''.$key.'\' AND `plugin`=\''.$plugin.'\'';
		} else {
			$sql = 'INSERT INTO `'.$dbtable.'` (`key`, `plugin`, `data`, `mtime`, `ttl`)';
			$sql .= ' VALUES (\''.$key.'\', \''.$plugin.'\', \''.$data.'\', \''.$mtime.'\', \''.$ttl.'\')';
		}
		if ($res = $this->xpwiki->db->queryF($sql)) {
			return $ret;
		} else {
			return FALSE;
		}
	}

	function cache_get_db ($key, $plugin='core', $delete=FALSE, $life=FALSE, $ttl_check=FALSE) {
		if (is_null($key)) {
			$select = ', `key`';
			$key = '';
			$limit = '';
		} else {
			$select = '';
			$key = '`key`=\''.addslashes($key) .'\' AND ';
			if ($ttl_check) {
				$key .= '`ttl` > 0 AND (`mtime` + `ttl`) >= '.$this->cont['UTC'].' AND ';
			}
			$limit = ' LIMIT 1';
		}
		$plugin = addslashes($plugin);
		$data = '';
		$dbtable = $this->xpwiki->db->prefix($this->root->mydirname.'_cache');

		$sql = 'SELECT `data`'.$select.' FROM `'.$dbtable.'` WHERE '.$key.'`plugin`=\''.$plugin.'\''.$limit;
		if ($res = $this->xpwiki->db->query($sql)) {
			if ($key) {
				list($data) = $this->xpwiki->db->fetchRow($res);
				if ($delete) {
					$sql = 'DELETE FROM `'.$dbtable.'` WHERE '.$key.'`plugin`=\''.$plugin.'\'';
					$this->xpwiki->db->queryF($sql);
					$sql = 'OPTIMIZE TABLE `'.$dbtable.'`';
					$this->xpwiki->db->queryF($sql);
				} else if ($life) {
					$sql = 'UPDATE `'.$dbtable.'`';
					$sql .= ' SET `mtime`=\''.$this->cont['UTC'].'\'';
					$sql .= ' WHERE '.$key.'`plugin`=\''.$plugin.'\'';
					$this->xpwiki->db->queryF($sql);
				}
			} else {
				$data = array();
				while($result = $this->xpwiki->db->fetchRow($res)) {
					$data[$result[1]] = $result[0];
				}
			}
		}

		return $data;
	}

	function cache_del_db ($key, $plugin) {
		$key = addslashes($key);
		$plugin = addslashes($plugin);
		$data = '';
		$dbtable = $this->xpwiki->db->prefix($this->root->mydirname.'_cache');

		$sql = 'DELETE FROM `'.$dbtable.'` WHERE `key`=\''.$key.'\' AND `plugin`=\''.$plugin.'\'';
		$ret = $this->xpwiki->db->queryF($sql);
		$sql = 'OPTIMIZE TABLE `'.$dbtable.'`';
		$this->xpwiki->db->queryF($sql);

		return $ret;
	}

	function regist_jobstack ($data, $ttl = 864000, $wait = 0) {
		$s_data = serialize($data);
		$key = md5($s_data . ($wait? '' : $wait));
		$mtime = $this->cont['UTC'] + $wait;
		$this->cache_save_db($s_data, 'jobstack', $ttl, $key, $mtime);
	}

	function unregist_jobstack ($data) {
		$key = md5(serialize($data));
		$this->cache_del_db($key, 'jobstack');
	}

	function get_jobstack_imagetag () {
		$dbtable = $this->xpwiki->db->prefix($this->root->mydirname.'_cache');
		$sql = 'SELECT `key` FROM `'.$dbtable.'` WHERE `plugin`=\'jobstack\' LIMIT 1';
		$check = '';
		if ($res = $this->xpwiki->db->query($sql)) {
			$check = $this->xpwiki->db->getRowsNum($res);
		}
		if ($check) {
			$sid = (defined('SID') && SID)? '&amp;' . SID : '';
			return '<div style="display:none;"><img class="ktai_direct" src="'.$this->cont['HOME_URL'].'gate.php?way=jobstack' . $sid . '" alt="" width="1" height="1" /></div>' . "\n";
		} else {
			return '';
		}
	}
}
?>