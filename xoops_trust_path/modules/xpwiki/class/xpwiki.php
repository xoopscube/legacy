<?php
//
// Created on 2006/09/29 by nao-pon http://hypweb.net/
// $Id: xpwiki.php,v 1.102 2012/01/07 07:52:19 nao-pon Exp $
//

class XpWiki {

	var $runmode = "xoops";
	var $module;

	var $root;  // Like a Global variable
	var $const; // Like a Const
	var $func;  // All functions
	var $db;    // Database Connection

	var $title;
	var $page;
	var $skin_title;
	var $body;
	var $html;
	var $breadcrumbs_array;

	var $iniVar;

	var $pid;

	var $isXpWiki = TRUE;

	var $admin_messages = array();

	function XpWiki ($mydirname, $moddir='modules/') {

		static $pid;

		$pid ++;
		$this->pid = $pid;

		$this->root =& new XpWikiRoot();
		$this->cont =& $this->root->c;
		$this->root->mydirname = $mydirname;
		$this->cont['MOD_DIR_NAME'] = $moddir;

		$this->func =& new XpWikiFunc($this);
		$this->func->set_moduleinfo();
		$this->func->set_siteinfo();

		$this->root->mydirpath = $this->cont['ROOT_PATH'].$moddir.$mydirname;
		$this->root->mytrustdirpath = dirname(dirname(__FILE__));

		$this->cont['DATA_HOME'] = $this->root->mydirpath."/";

		$this->cont['HOME_URL'] = $this->cont['ROOT_URL'].$moddir.$mydirname."/";

		$_urls = parse_url($this->cont['ROOT_URL']);
		$this->cont['MY_HOST_URL'] = $_urls['scheme'] . '://' . $_urls['host'] . (isset($_urls['port'])? ':' . $_urls['port'] : '');

		$this->db =& $this->func->get_db_connection();

		// Check pukiwiki.ini.php
		if (! $this->root->module['checkRight'] || ! is_file($this->cont['DATA_HOME'] . 'private/ini/pukiwiki.ini.php')) {
			$this->isXpWiki = FALSE;
		}

	}

	function & getSingleton ($mddir, $iniClear = true) {
		static $obj;
		if (! isset($obj[$mddir])) {
			$obj[$mddir] = new XpWiki($mddir);
		}
		if ($iniClear) {
			$obj[$mddir]->clearIniRoot();
			$obj[$mddir]->clearIniConst();
			clearstatcache();
		}
		return $obj[$mddir];
	}

	function & getInitedSingleton ($mddir) {
		static $obj;
		if (! isset($obj[$mddir])) {
			$obj[$mddir] = new XpWiki($mddir);
			if ($obj[$mddir]->isXpWiki) {
				$obj[$mddir]->init('#RenderMode');
			}
		}
		return $obj[$mddir];
	}

	function init($page = "") {

		static $oid;
		$page = strval($page);
		if ($page !== '') {$this->cont['page_show'] = $page;}

		// GET, POST, COOKIE
		// 基本的に直接操作しない
		$this->root->get    = $_GET;
		$this->root->post   = $_POST;
		$this->root->cookie = $_COOKIE;

		// For Ajax mode
		if (isset($this->root->get['ajax']) || isset($this->root->post['ajax'])) {
			error_reporting(0);
		}

		// ini ファイル読み込み
		$this->func->load_ini();

		// Init runtime var.
		$this->root->init();

		// 各パラメーターを初期化
		$this->func->init();

		// プラグインインスタンスを初期化
		$this->func->get_plugin_instance(NULL);

		// オプション設定
		if (!empty($this->iniVar['root'])) {
			foreach($this->iniVar['root'] as $key => $val) {
				$this->root->$key = $val;
			}
		}
		if (!empty($this->iniVar['const'])) {
			foreach($this->iniVar['const'] as $key => $val) {
				$this->cont[$key] = $val;
			}
		}

		// Set Locale (LC_CTYPE only)
		setlocale(LC_CTYPE, $this->cont['LC_CTYPE']);

		// 追加フェイスマークの処理
		if ($this->root->use_extra_facemark) {
			$this->root->facemark_rules = array_merge($this->func->get_extra_facemark(), $this->root->facemark_rules);
		}

		// フェイスマークを$line_rulesに加える
		if ($this->root->usefacemark) {
			$this->root->line_rules = array_merge($this->root->facemark_rules, $this->root->line_rules);
		}

		// <pre> の幅指定
		if ( stristr($this->root->ua, 'msie')) {
			$this->root->pre_width = rawurlencode($this->root->pre_width_ie);
		}

		// Re-setting
		if (strtolower($this->cont['PKWK_SAFE_MODE']) === 'auto') {
			$this->cont['PKWK_SAFE_MODE'] = (! $this->root->userinfo['admin']);
		}

		// Object id
		if (isset($oid[$this->pid])) {
			$oid[$this->pid]++;
		} else {
			$oid[$this->pid] = 1;
		}
		$this->root->rtf['oid'] = $oid[$this->pid];

	}

	function execute() {

		$root = & $this->root;
		$func = & $this->func;

		$base    = $root->defaultpage;
		$retvars = array();

		if (isset($root->vars['plugin'])) {
			// Plug-in action
			if (! $func->exist_plugin_action($root->vars['plugin'])) {
				$s_plugin = htmlspecialchars($root->vars['plugin']);
				$msg      = "plugin=$s_plugin is not implemented.";
				$retvars  = array('msg'=>$msg,'body'=>$msg);
			} else {
				$retvars  = $func->do_plugin_action($root->vars['plugin']);
				if ($retvars !== FALSE) {
					$base = isset($root->vars['refer']) ? $root->vars['refer'] : '';
					$root->vars['cmd'] = $root->vars['plugin'];
				}
			}

		} else if (isset($root->vars['cmd'])) {
			// Command action
			if (! $func->exist_plugin_action($root->vars['cmd'])) {
				$s_cmd   = htmlspecialchars($root->vars['cmd']);
				$msg     = "cmd=$s_cmd is not implemented.";
				$retvars = array('msg'=>$msg,'body'=>$msg);
			} else {
				$retvars = $func->do_plugin_action($root->vars['cmd']);
				$base    = $root->vars['page'];
			}
		}

		if ($retvars !== FALSE) {
			if (isset($retvars['header'])) {
				$func->clear_output_buffer();
				header($retvars['header']);
				if (isset($retvars['msg'])) {
					header('Content-Length: ' . strlen($retvars['msg']));
					echo $retvars['msg'];
				}
				exit();
			}
			$title = htmlspecialchars($func->strip_bracket($base));
			$page  = $func->make_search($base);

			if (! empty($retvars['msg'])) {
				$title = str_replace('$1', $title, $retvars['msg']);
				$page  = str_replace('$1', $page,  $retvars['msg']);
			}

			if (! empty($retvars['body'])) {
				$body = $retvars['body'];
			} else {
				if ($base === '' || ! $func->is_page($base)) {
					$base  = $root->defaultpage;
					$page  = $func->make_search($base);
				}

				if (! empty($root->vars['cmd']) && $root->vars['cmd'] !== 'read') {
					$func->ref_save($base);
					if (empty($retvars['redirect'])) $retvars['redirect'] = $func->get_page_uri($base, true);
					$func->redirect_header($retvars['redirect'], 0, $title);
					exit();
				} else {
					$root->vars['cmd']  = 'read';
					$root->vars['page'] = $base;
					$body = $func->get_body($base);

					if ($root->trackback) {
						$body .= $func->tb_get_rdf($base);
					}
					$func->ref_save($base);

					// 各ページ用の .css
					$key = $func->get_headtag_key('css', $base);
					$root->head_tags[$key] = $func->get_page_css_tag($base);

				}
			}

			$func->convert_finisher($body);

			// JobStack
			if ($root->render_mode === 'main') {
				$body .= $func->get_jobstack_imagetag();
			}

			// Outputas normal
			if ($root->viewmode === 'normal' || $root->viewmode === 'print') {
				$page_title = strip_tags($title);
				$content_title = (!empty($root->content_title) && $title !== $root->content_title)?
					$func->unhtmlspecialchars($root->content_title, ENT_QUOTES) : '';

				$root->pagetitle = trim(str_replace(
										array('$page_title', '$content_title', '$module_title'),
										array($page_title, $content_title, $root->module_title),
										$root->html_head_title));
				$root->pagetitle_action = isset($root->_LANG['skin'][$root->vars['cmd']])? htmlspecialchars($root->_LANG['skin'][$root->vars['cmd']]) : '';
				$this->title         = $title;
				$this->page          = $base;
				$this->skin_title    = $page;
				$this->body          = $body;
				$this->foot_explain  = $root->foot_explain;
				$this->head_pre_tags = $root->head_pre_tags;
				$this->head_tags     = $root->head_tags;
				$this->related       = $root->related;
				$this->notyets       = $root->notyets;

				return;
			}

			// Output as Ajax -> exit
			if ($root->viewmode === 'ajax') {
				$func->output_ajax($body);
			}

			// Output as Popup -> exit
			if ($root->viewmode === 'popup') {
				$func->output_popup($body);
			}
		}
	}

	function catbody () {
		// Check Skin name
		if (! is_file($this->cont['SKIN_FILE']) || $this->root->runmode === 'xoops_admin') {
			$this->cont['SKIN_NAME'] = 'default';
			$this->cont['SKIN_DIR'] = 'skin/' . $this->cont['SKIN_NAME'] . '/';
			$this->cont['SKIN_FILE'] = $this->cont['DATA_HOME'] . $this->cont['SKIN_DIR'] . 'pukiwiki.skin.php';
		}

		// SKIN select from Cookie or Plugin.
		if ($this->cont['SKIN_CHANGER'] && $this->cont['UA_PROFILE'] !== 'keitai' && (!empty($this->root->cookie['skin']) || is_string($this->cont['SKIN_CHANGER']))) {
			$this->cont['SKIN_NAME'] = empty($this->root->cookie['skin'])? $this->cont['SKIN_CHANGER'] : $this->root->cookie['skin'];
			if (preg_match('/^[\w-]+$/', $this->cont['SKIN_NAME'])) {
				if (substr($this->cont['SKIN_NAME'],0,3) === "tD-") {
					//tDiary's theme
					$theme_name = substr($this->cont['SKIN_NAME'],3);
					$theme_css = $this->cont['DATA_HOME'] . $this->cont['TDIARY_DIR'] . $theme_name . '/' . $theme_name . '.css';
					if (is_file($theme_css)) {
						$this->cont['SKIN_FILE'] = $this->cont['DATA_HOME'] . $this->cont['TDIARY_DIR'] . 'tdiary.skin.php';
						$this->cont['TDIARY_THEME'] =  $theme_name;
					}
				} else {
					//PukiWiki's skin
					$skindir = "skin/" . $this->cont['SKIN_NAME'] . "/";
					$skin = $this->cont['DATA_HOME'] . $skindir . 'pukiwiki.skin.php';
					if (is_file($skin)) {
						$this->cont['SKIN_DIR'] = $skindir;
						$this->cont['SKIN_FILE'] = $skin;
					}
				}
			}
		}

		if ($this->root->viewmode === 'print') {
			if (! empty($this->cont['TDIARY_THEME'])) {
				$skindir = 'skin/tdiary_theme/';
			} else {
				$skindir = 'skin/' . $this->cont['SKIN_NAME'] . '/';
			}
			$skin = $this->cont['DATA_HOME'] . $skindir . 'print.skin.php';
			if (is_file($skin)) {
				$this->cont['SKIN_DIR'] = $skindir;
				$this->cont['SKIN_FILE'] = $skin;
			} else {
				$this->cont['SKIN_DIR'] = 'skin/default/';
				$this->cont['SKIN_FILE'] = $this->root->mytrustdirpath . '/skin/print.skin.php';
			}
		}

		// Set Skin Name for FCKeditor.
		$this->func->add_js_var_head('XpWiki.SkinName["'.$this->root->mydirname.'"]', $this->cont['SKIN_NAME']);

		// catbody
		ob_start();
		$this->func->catbody($this->title, $this->skin_title, $this->body);
		$body = ob_get_contents();
		@ ob_end_clean();

		if (! defined('HYP_K_TAI_RENDER') && preg_match('/\(\([eisv]:[0-9a-f]{4}\)\)|\[emj:\d{1,4}(?::(?:im|ez|sb))?\]/S', $body)) {
			if (! XC_CLASS_EXISTS('MobilePictogramConverter')) {
				HypCommonFunc::loadClass('MobilePictogramConverter');
			}
			if (XC_CLASS_EXISTS('MobilePictogramConverter')) {
				$mpc =& MobilePictogramConverter::factory_common();
				$mpc->setString($body, FALSE);
				if (method_exists($mpc, 'modKtai2textPictMobile') && (defined('HYP_WIZMOBILE_USE') || class_exists('WizMobile'))) {
					// convert to [emj:xxx]
					$body = $mpc->modKtai2textPictMobile();
				} else {
					$mpc->setImagePath($this->cont['ROOT_URL'] . 'images/emoji');
					$mpc->userAgent = ''; // Always IMG output
					$body = $mpc->autoConvertModKtai();
				}
			}
		}

		$this->html = $body;

		if (!empty($this->root->runmode)) $this->runmode = $this->root->runmode;

		$this->breadcrumbs_array = $this->func->get_breadcrumbs_array($this->page);
		$this->breadcrumbs_array[] = array('name' => preg_replace('#^'.preg_quote(preg_replace('#/[^/]+$#','',$this->page).'/', '#').'#', '', $this->title), 'url' =>'');

		return;
	}

	function get_body () {

		return $this->body;

	}

	function get_var ($var) {
		return (isset($this->$var))? $this->$var : null;
	}

	function get_page_views () {
		return $this->func->get_page_views($this->page);
	}

	function get_comment_count () {
		return $this->func->count_page_comments($this->page);
	}

	function get_pginfo () {
		return $this->func->get_pginfo($this->page);
	}

	function get_html_for_block ($page, $width = "100%", $div_class = 'xpwiki_b_$mydirname', $css_tag = NULL, $configs = array(), $byArray = FALSE) {

		// configs
		$this->iniVar = $configs;

		// 初期化
		$src = '';
		if (is_array($page) && !empty($page['source'])) {
			$src = $page['source'];
			$this->init('#RenderMode');
		} else {
			$this->init($page);
		}

		if (is_null($css_tag)) {
			$css_tag = $this->root->main_css;
		}

		$div_class = str_replace('$mydirname', $this->root->mydirname, $div_class);

		// for menu plugin etc..
		$this->root->runmode = "xoops";

		// ブロック取得モード
		$this->root->render_mode = 'block';

		// 実行
		if ($src) {
			$this->body = $this->func->convert_html($src);
			$this->func->convert_finisher($this->body);
		} else {
			$this->execute();
		}

		if (!trim($this->body)) {
			return $byArray? array('', '') : '';
		}

		// SKIN select from Cookie or Plugin.
		if ($this->cont['SKIN_CHANGER']) {
			if ($this->root->cookie['skin']) {$this->cont['SKIN_NAME'] = $this->root->cookie['skin']; }
			if (preg_match('/^[\w-]+$/', $this->cont['SKIN_NAME'])) {
				if (substr($this->cont['SKIN_NAME'],0,3) === "tD-") {
					//tDiary's theme

				} else {
					//PukiWiki's skin
					$skindir = "skin/" . $this->cont['SKIN_NAME'] . "/";
					$skin = $this->cont['DATA_HOME'] . $skindir . 'pukiwiki.skin.php';
					if (is_file($skin)) {
						$this->cont['SKIN_DIR'] = $skindir;
					}
				}
			}
		}
		// Set Skin Name for FCKeditor.
		$this->func->add_js_var_head('XpWiki.SkinName["'.$this->root->mydirname.'"]', $this->cont['SKIN_NAME']);

		// List of footnotes
		natsort($this->root->foot_explain);
		$this->body .= ! empty($this->root->foot_explain) ? $this->root->note_hr . join("\n", $this->root->foot_explain) : '';
		// Head Tags
		list($head_pre_tag, $head_tag) = $this->func->get_additional_headtags();
		$cssprefix = $this->root->css_prefix ? 'pre=' . rawurlencode($this->root->css_prefix) . '&amp;' : '';

		// 出力
		$base = "b_".$this->root->mydirname;
		$css_tag = ($css_tag)? '<link rel="stylesheet" type="text/css" media="all" href="' . $this->cont['LOADER_URL'] . '?charset=' . $this->cont['CSS_CHARSET'] . '&amp;skin=' . $this->cont['SKIN_NAME'] . '&amp;b=1&amp;' . $cssprefix . 'src=' . $css_tag . '" charset="' . $this->cont['CSS_CHARSET'] . '" />'
		             : '';
		$block = <<<EOD
<div class="{$div_class} {$div_class}_{$this->cont['UA_PROFILE']}" style="width:{$width};overflow:hidden;">
{$this->body}
</div>
EOD;
		if ($byArray) {
			return array($block, $head_pre_tag."\n".$css_tag."\n".$head_tag);
		} else {
			return $head_pre_tag."\n".$css_tag."\n".$head_tag."\n".$block;
		}
	}
/*
	// すべてのExtensionを読み込む
	function load_extensions_all () {
		$base = $this->root->mytrustdirpath."/class/extension";
		if ($handle = opendir($base)) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match("/^([\w-]+\).php$/",$file,$match)) {
					include_once($base."/".$file);
					$name = $match[1];
					$class = "XPWikiExtension_".$name;
					if (XC_CLASS_EXISTS($class)) {
						$this->extension->$name = new $class($this);
					}
				}
			}
			closedir($handle);
		}
	}
*/
	// 指定のExtensionを読み込む
	function load_extensions ($exts) {
		$base = $this->root->mytrustdirpath."/class/extension";
		if (!is_array($exts)) {
			$exts = array($exts);
		}
		foreach($exts as $name) {
			if (preg_match("/^[\w-]+$/",$name)) {
				include_once($base."/".$name.".php");
				$class = "XPWikiExtension_".$name;
				if (XC_CLASS_EXISTS($class)) {
					$this->extension->$name = new $class($this);
				}
			}
		}
	}

	// xpWiki render mode
	function transform($text, $cssbase = '') {
		if (!$text) return '';
		$this->init('#RenderMode');
		$this->cont['PKWK_READONLY'] = 2;
		$this->root->top = '';
		$text = str_replace(array("\r\n", "\r"), "\n", $text);

		if ($this->root->render_use_cache) {
			$op = '';
			if (!empty($this->iniVar['root'])) {
				$op .= serialize($this->iniVar['root']);
			}
			if (!empty($this->iniVar['const'])) {
				$op .= serialize($this->iniVar['const']);
			}
			$cache = $this->cont['RENDER_CACHE_DIR'] . 'render_' . sha1($text.$op) . '.' .  $this->cont['UI_LANG'] . (($this->cont['UA_PROFILE'] === 'default')? '' : '.' . $this->cont['UA_PROFILE']);
			if (is_file($cache) &&
				@ filemtime($this->cont['CACHE_DIR'] . 'pagemove.time') < filemtime($cache) &&
				(empty($this->root->render_cache_min) || ((filemtime($cache) +  $this->root->render_cache_min * 60) > $this->cont['UTC']))
			) {
				$texts = file($cache);
				$head_pre_tag = array_shift($texts);
				$head_tag = array_shift($texts);

				$head_pre_tag = str_replace("\x08", "\n", $head_pre_tag);
				$head_tag = str_replace("\x08", "\n", $head_tag);

				$text = join('', $texts);
			} else {
				$text = $this->func->convert_html($text);
				// List of footnotes
				natsort($this->root->foot_explain);
				$text .= ! empty($this->root->foot_explain) ? $this->root->note_hr . join("\n", $this->root->foot_explain) : '';

				list($head_pre_tag, $head_tag) = $this->func->get_additional_headtags();

				if (empty($this->root->rtf['disable_render_cache'])) {
					@ touch ($cache);
					if (is_writable($cache)) {
						if ($fp = fopen($cache, 'wb')) {
							$_head_pre_tag = str_replace(array("\r\n","\r","\n"), "\x08", $head_pre_tag);
							$_head_tag = str_replace(array("\r\n","\r","\n"), "\x08", $head_tag);
							fwrite($fp, $this->func->strip_MyHostUrl($_head_pre_tag . "\n" . $_head_tag . "\n" . $text));
							fclose($fp);
						}
					}
				}
			}
		} else {
			$text = $this->func->convert_html($text);
			// List of footnotes
			//ksort($this->root->foot_explain, SORT_STRING);
			natsort($this->root->foot_explain);
			$text .= ! empty($this->root->foot_explain) ? $this->root->note_hr . join("\n", $this->root->foot_explain) : '';

			list($head_pre_tag, $head_tag) = $this->func->get_additional_headtags();
		}

		$this->root->replaces_finish['__PID_OID__'] = $this->pid . '_' . $this->root->rtf['oid'];
		$this->func->convert_finisher($text);

		$csstag = '';
		if ($cssbase) {
			$cssbase = 'r_'.$cssbase;
			$cssprefix = $this->root->css_prefix ? 'pre=' . rawurlencode($this->root->css_prefix) . '&amp;' : '';
			$csstag = '<link rel="stylesheet" type="text/css" media="all" href="'.$this->cont['LOADER_URL'].'?charset='.$this->cont['CSS_CHARSET'].'&amp;skin='.$this->cont['SKIN_NAME'].'&amp;r=1&amp;'.$cssprefix.'src=' . $this->root->main_css . '" charset="' . $this->cont['CSS_CHARSET'] . '" />';
			$text = '<div class="xpwiki_'.$cssbase.' xpwiki_'.$cssbase.'_'.$this->cont['UA_PROFILE'].'">'."\n".$text."\n".'</div>';
		}

		if (isset($GLOBALS['hyp_preload_head_tag'])) {
			if ($this->func->set_hyp_preload_head_tag($head_pre_tag.$csstag.$head_tag)) {
				return $text;
			}
		}

		return preg_replace("/\n{2,}/", "\n", $head_pre_tag.$csstag.$head_tag) . $text;

	}

	function clearIniRoot () {
		$this->iniVar['root'] = array();
	}

	function setIniRoot($key = '', $val = '') {
		if (!$key) return;
		$key = strval($key);
		$this->iniVar['root'][$key] = $val;
	}

	function clearIniConst () {
		$this->iniVar['const'] = array();
	}

	function setIniConst($key = '', $val = '') {
		if (!$key) return;
		$key = strval($key);
		$this->iniVar['const'][$key] = $val;
	}
}
?>