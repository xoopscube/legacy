<?php
define('X2_ADD_SMARTYPLUGINS_DIR', XOOPS_TRUST_PATH . '/libs/smartyplugins/x2');
define('HYP_COMMON_PRELOAD_CONF', '/uploads/hyp_common/hypconf_'.md5(XOOPS_URL . (defined('XOOPS_SALT')?XOOPS_SALT:XOOPS_DB_PASS)).'.conf');
//// mbstring ////
if (! extension_loaded('mbstring')) {
	include_once dirname(dirname(__FILE__)) . '/mbemulator/mb-emulator.php';
}

include_once dirname(dirname(__FILE__)) . '/hyp_common_func.php';

// For not cube.
if (! XC_CLASS_EXISTS('XCube_ActionFilter')) {
class XCube_ActionFilter
{
	var $mController;
	var $mRoot;
	function XCube_ActionFilter(&$controller) {}
	function preFilter() {}
	function preBlockFilter() {}
	function postFilter() {}
}
}

class HypCommonPreLoadBase extends XCube_ActionFilter {

	var $configEncoding;       // Configエンコーディング

	var $encodehint_word;      // POSTエンコーディング判定用文字
	var $encodehint_name;      // POSTエンコーディング判定用 Filed name

	var $use_set_query_words;  // 検索ワードを定数にセット
	var $use_words_highlight;  // 検索ワードをハイライト表示
	var $msg_words_highlight;  // ハイライトキーワードメッセージ

	var $use_proxy_check;      // POST時プロキシチェックする
	var $no_proxy_check;       // 除外IP
	var $msg_proxy_check;

	var $use_dependence_filter;// 機種依存文字フィルター

	var $use_post_spam_filter; // POST SPAM フィルター
	var $use_mail_notify;      // POST SPAM メール通知
	var $post_spam_a;          // <a> タグ 1個あたりのポイント
	var $post_spam_bb;         // BBリンク 1個あたりのポイント
	var $post_spam_url;        // URL      1個あたりのポイント
	var $post_spam_host;       // Spam HOST の加算ポイント
	var $post_spam_word;       // Spam Word の加算ポイント
	var $post_spam_filed;      // Spam 無効フィールドの加算ポイント
	var $post_spam_trap;       // Spam 罠用無効フィールド名
	var $post_spam_trap_set;   // 無効フィールドの罠を自動で仕掛ける

	var $post_spam_user;       // POST SPAM 閾値: ログインユーザー
	var $post_spam_guest;      // POST SPAM 閾値: ゲスト
	var $post_spam_rules;      // コンストラクタ内で設定

	// 検索ワード定数名
	var $q_word;               // 検索ワード
	var $q_word2;              // 検索ワード分かち書き
	var $se_name;              // 検索元名
	var $kakasi_cache_dir;

	var $wizMobileUse = FALSE;
	var $detect_order_org = array();

	// コンストラクタ
	function HypCommonPreLoadBase (& $controller) {

		if (! isset($this->use_set_query_words)) $this->use_set_query_words = 0;
		if (! isset($this->use_words_highlight)) $this->use_words_highlight = 0;
		if (! isset($this->use_proxy_check )) $this->use_proxy_check = 0;
		if (! isset($this->use_dependence_filter)) $this->use_dependence_filter = 0;
		if (! isset($this->use_post_spam_filter)) $this->use_post_spam_filter = 0;
		if (! isset($this->post_spam_trap_set)) $this->post_spam_trap_set = 0;
		if (! isset($this->use_k_tai_render)) $this->use_k_tai_render = 0;
		if (! isset($this->use_smart_redirect)) $this->use_smart_redirect = 0;

		if (! isset($this->configEncoding)) $this->configEncoding = 'ISO-8859-1';

		if (! isset($this->encodehint_word)) $this->encodehint_word = '';
		if (! isset($this->encodehint_name)) $this->encodehint_name = 'HypEncHint';
		if (! isset($this->detect_order)) $this->detect_order = 'ASCII, JIS, UTF-8, eucJP-win, EUC-JP, SJIS-win, SJIS';

		if (! isset($this->msg_words_highlight)) $this->msg_words_highlight = 'These key words are highlighted.';

		if (! isset($this->no_proxy_check)) $this->no_proxy_check  = '/^(127\.0\.0\.1|192\.168\.1\.)/';
		if (! isset($this->msg_proxy_check)) $this->msg_proxy_check = 'Can not post from public proxy.';

		if (! isset($this->input_filter_strength)) $this->input_filter_strength = 0;

		if (! isset($this->use_mail_notify)) $this->use_mail_notify = 1;
		if (! isset($this->send_mail_interval)) $this->send_mail_interval = 60;
		if (! isset($this->post_spam_a)) $this->post_spam_a   = 1;
		if (! isset($this->post_spam_bb)) $this->post_spam_bb  = 1;
		if (! isset($this->post_spam_url)) $this->post_spam_url = 1;
		if (! isset($this->post_spam_unhost)) $this->post_spam_unhost= 5;
		if (! isset($this->post_spam_host)) $this->post_spam_host  = 31;
		if (! isset($this->post_spam_word)) $this->post_spam_word  = 10;
		if (! isset($this->post_spam_filed)) $this->post_spam_filed = 200;
		if (! isset($this->post_spam_trap)) $this->post_spam_trap  = '___url';
		if (! isset($this->post_spam_user)) $this->post_spam_user  = 150;
		if (! isset($this->post_spam_guest)) $this->post_spam_guest = 15;
		if (! isset($this->post_spam_pass_names)) $this->post_spam_pass_names = 'reference_quote,msg_before,msg_after';
		if (! isset($this->post_spam_badip)) $this->post_spam_badip = 100;
		if (! isset($this->post_spam_badip_ttl)) $this->post_spam_badip_ttl = 900;
		if (! isset($this->post_spam_badip_forever)) $this->post_spam_badip_forever = 200;
		if (! isset($this->post_spam_badip_ttl0)) $this->post_spam_badip_ttl0 = 2592000;
		if (! isset($this->post_spam_checkers)) $this->post_spam_checkers = array(
			//'list.dsbl.org',
			'niku.2ch.net',
			array(
				'dnsbl.spam-champuru.livedoor.com',
				'/^192\.168\.1\.2/'
			),
		);
		if (! isset($this->post_spam_rules)) $this->post_spam_rules = array(
			"/((?:ht|f)tps?:\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+).+?\\1.+?\\1/i" => 11,
			'/[\x01-\x08\x0b-\x0c\x0e\x10-\x1a\x1c-\x1f\x7f]+/' => 31,
			'/^\s*(?:Hi|Aloha)! (?:<a[^>]+?href=|\[url=|http:\/\/)/i' => 15,
		);
		if (! isset($this->ignore_fileds)) $this->ignore_fileds = array();

		if (! isset($this->q_word)) $this->q_word  = 'XOOPS_QUERY_WORD';
		if (! isset($this->q_word2)) $this->q_word2 = 'XOOPS_QUERY_WORD2';
		if (! isset($this->se_name)) $this->se_name = 'XOOPS_SEARCH_ENGINE_NAME';

		if (! isset($this->extlink_class_name)) $this->extlink_class_name = 'ext';

		if (! isset($this->kakasi_cache_dir)) $this->kakasi_cache_dir = XOOPS_TRUST_PATH.'/uploads/hyp_common/kakasi/';

		if (! isset($this->smart_redirect_min_sec)) $this->smart_redirect_min_sec = 5;

		if (! isset($this->bot_ua_reg)) $this->bot_ua_reg = '/bot|Slurp|Crawler|Sidewinder|spider|Y!J|Ask/i';

		if (! isset($this->k_tai_conf['ua_regex'])) $this->k_tai_conf['ua_regex'] = '#(?:Android|Windows Phone|SoftBank|Vodafone|J-PHONE|DoCoMo|UP\.Browser|DDIPOCKET|WILLCOM|iPhone|iPod|mixi-mobile-converter|Googlebot-Mobile|Google Wireless Transcoder|Hatena-Mobile-Gateway)#';
		if (! isset($this->k_tai_conf['jquery_profiles'])) $this->k_tai_conf['jquery_profiles'] = 'android,iphone,ipod,windows phone';
		if (! isset($this->k_tai_conf['jquery_theme'])) $this->k_tai_conf['jquery_theme'] = 'b';
		if (! isset($this->k_tai_conf['jquery_theme_content'])) $this->k_tai_conf['jquery_theme_content'] = 'd';
		if (! isset($this->k_tai_conf['jquery_theme_block'])) $this->k_tai_conf['jquery_theme_block'] = 'c';
		if (! isset($this->k_tai_conf['jquery_no_reduce'])) $this->k_tai_conf['jquery_no_reduce'] = true;
		if (! isset($this->k_tai_conf['jquery_remove_flash'])) $this->k_tai_conf['jquery_remove_flash'] = '';
		if (! isset($this->k_tai_conf['jquery_resolve_table'])) $this->k_tai_conf['jquery_resolve_table'] = false;
		if (! isset($this->k_tai_conf['jquery_image_convert'])) $this->k_tai_conf['jquery_image_convert'] = 0;

		if (! isset($this->k_tai_conf['rebuilds'])) $this->k_tai_conf['rebuilds'] = array(
			'header'         => array( 'above' => '',
			                          'below' => ''),
			'body'           => array( 'above' => '',
			                          'below' => ''),
			'footer'         => array( 'above' => '',
			                          'below' => ''),
			'headerlogo'     => array( 'above' => '<center>',
			                          'below' => '</center>'),
			'headerbar'      => array( 'above' => '<hr>',
			                          'below' => ''),
			'breadcrumbs'    => array( 'above' => '',
			                          'below' => ''),
			'leftcolumn'     => array( 'above' => '<hr>',
			                          'below' => ''),
			'centerCcolumn'  => array( 'above' => '<hr>',
			                          'below' => ''),
			'centerLcolumn'  => array( 'above' => '',
			                          'below' => ''),
			'centerRcolumn'  => array( 'above' => '',
			                          'below' => ''),
			'content'        => array( 'above' => '<hr>',
			                          'below' => ''),
			'rightcolumn'    => array( 'above' => '<hr>',
			                          'below' => ''),
			'footerbar'      => array( 'above' => '',
			                          'below' => ''),
			'easylogin'      => array( 'above' => '<div style="text-align:center;background-color:#DBBCA6;font-size:small">[ ',
			                          'below' => ' ]</div>'),
			'redirectMessage'=> array( 'above' => '<marquee loop="3">',
			                          'below' => '</marquee>'),
			'blockMenu'      => array( 'above' => '<div style="background-color:#E0EEEE;font-size:small">',
			                          'below' => '</div>'),
			'blockContent'   => array( 'above' => '',
			                          'below' => ''),
			'toMain'         => array( 'above' => '<hr /><div style="text-align:center">',
			                          'below' => '</div>'),
			'subMenu'        => array( 'above' => '<div id="submenu" style="background-color:#ccccff"><h2 style="text-align:center">サブメニュー</h2></div>',
			                          'below' => ''),
			);
		if (! isset($this->k_tai_conf['rebuildsEx'])) $this->k_tai_conf['rebuildsEx'] = array();
		if (! isset($this->k_tai_conf['rebuildsEx']['jqm'])) $this->k_tai_conf['rebuildsEx']['jqm'] = array(
			'header'         => array( 'above' => '<div data-role="header" data-theme="'.$this->k_tai_conf['jquery_theme'].'">',
			                          'below' => '</div>'),
			'body'           => array( 'above' => '<div data-role="content" id="keitaiContents" data-theme="'.$this->k_tai_conf['jquery_theme'].'">',
			                          'below' => '</div>'),
			'footer'         => array( 'above' => '<div data-role="footer" data-theme="'.$this->k_tai_conf['jquery_theme'].'">',
			                          'below' => '</div>'),
			'headerlogo'     => array( 'above' => '<h1>',
			                          'below' => '</h1>'),
			'easylogin'      => array( 'above' => '',
			                          'below' => ''),
			'blockMenu'      => array( 'above' => '</div><div data-role="footer" data-position="fixed" style="line-height:1">',
			                          'below' => ''),
		);
		if (! isset($this->k_tai_conf['jqm_css'])) $this->k_tai_conf['jqm_css'] = '';

		if (! isset($this->k_tai_conf['themeSet'])) $this->k_tai_conf['themeSet'] = 'ktai_default';
		if (! isset($this->k_tai_conf['templateSet'])) $this->k_tai_conf['templateSet'] = 'ktai';
		if (! isset($this->k_tai_conf['themeSets'])) $this->k_tai_conf['themeSets'] = array();
		if (! isset($this->k_tai_conf['templateSets'])) $this->k_tai_conf['templateSets'] = array();
		if (! isset($this->k_tai_conf['template'])) $this->k_tai_conf['template'] = 'default';
		if (! isset($this->k_tai_conf['templates'])) $this->k_tai_conf['templates'] = array();
		if (! isset($this->k_tai_conf['templates']['jqm']))$this->k_tai_conf['templates']['jqm'] = 'smart';
		if (! isset($this->k_tai_conf['bodyAttribute'])) $this->k_tai_conf['bodyAttribute'] = '';
		if (! isset($this->k_tai_conf['disabledBlockIds'])) $this->k_tai_conf['disabledBlockIds'] = array();
		if (! isset($this->k_tai_conf['limitedBlockIds'])) $this->k_tai_conf['limitedBlockIds'] = array();
		if (! isset($this->k_tai_conf['showBlockIds'])) $this->k_tai_conf['showBlockIds'] = array();
		if (! isset($this->k_tai_conf['pictSizeMax'])) $this->k_tai_conf['pictSizeMax'] = '200';
		if (! isset($this->k_tai_conf['showImgHosts'])) $this->k_tai_conf['showImgHosts'] = array('amazon.com', 'yimg.jp', 'yimg.com', 'google.com');
		if (! isset($this->k_tai_conf['directImgHosts'])) $this->k_tai_conf['directImgHosts'] = array('google-analytics.com', 'maps.google.com', 'ad.jp.ap.valuecommerce.com', 'ba.afl.rakuten.co.jp', 'assoc-amazon.jp', 'ad.linksynergy.com');
		if (! isset($this->k_tai_conf['directLinkHosts'])) $this->k_tai_conf['directLinkHosts'] = array('amazon.co.jp', 'ck.jp.ap.valuecommerce.com', 'afl.rakuten.co.jp', 'maps.google.com', 'google.co.jp');
		if (! isset($this->k_tai_conf['redirect'])) $this->k_tai_conf['redirect'] = XOOPS_URL . '/class/hyp_common/gate.php?way=redirect&amp;_d=0&amp;_u=0&amp;_x=0&amp;l=';
		if (! isset($this->k_tai_conf['easyLogin'])) $this->k_tai_conf['easyLogin'] = 1;
		if (! isset($this->k_tai_conf['noCheckIpRange'])) $this->k_tai_conf['noCheckIpRange'] = 0;
		if (! isset($this->k_tai_conf['docomoGuidTTL'])) $this->k_tai_conf['docomoGuidTTL'] = 300;

		if (! isset($this->k_tai_conf['msg']['easylogin'])) $this->k_tai_conf['msg']['easylogin'] = 'EasyLogin';
		if (! isset($this->k_tai_conf['msg']['logout'])) $this->k_tai_conf['msg']['logout'] = 'Logout';
		if (! isset($this->k_tai_conf['msg']['easyloginSet'])) $this->k_tai_conf['msg']['easyloginSet'] = 'Easylogin:ON';
		if (! isset($this->k_tai_conf['msg']['easyloginUnset'])) $this->k_tai_conf['msg']['easyloginUnset'] = 'Easylogin:OFF';
		if (! isset($this->k_tai_conf['msg']['toMain'])) $this->k_tai_conf['msg']['toMain'] = 'Show main contents';
		if (! isset($this->k_tai_conf['msg']['mainMenu'])) $this->k_tai_conf['msg']['mainMenu'] = 'Main Menu';
		if (! isset($this->k_tai_conf['msg']['subMenu'])) $this->k_tai_conf['msg']['subMenu'] = 'Sub Menu';
		if (! isset($this->k_tai_conf['msg']['switchSmart'])) $this->k_tai_conf['msg']['switchSmart'] = 'To Smart phone\'s';

		if (! isset($this->k_tai_conf['icon']['toMain'])) $this->k_tai_conf['icon']['toMain'] = '((e:f7e4))';
		if (! isset($this->k_tai_conf['style']['highlight'])) $this->k_tai_conf['style']['highlight'] = 'background-color:#ffc0cb';
		if (! isset($this->k_tai_conf['easyLoginConfPath'])) $this->k_tai_conf['easyLoginConfPath'] = '/userinfo.php';
		if (! isset($this->k_tai_conf['easyLoginConfuid'])) $this->k_tai_conf['easyLoginConfuid'] = 'uid';
		if (! isset($this->k_tai_conf['easyLoginConfInsert'])) $this->k_tai_conf['easyLoginConfInsert'] = 'content';
		if (! isset($this->k_tai_conf['getKeys']['page'])) $this->k_tai_conf['getKeys']['page'] = '_p_';
		if (! isset($this->k_tai_conf['getKeys']['page'])) $this->k_tai_conf['getKeys']['hash'] = '_h_';
		if (! isset($this->k_tai_conf['getKeys']['page'])) $this->k_tai_conf['getKeys']['block'] = '_b_';
		if (! isset($this->k_tai_conf['googleAdsense']['config'])) $this->k_tai_conf['googleAdsense']['config'] = XOOPS_TRUST_PATH . '/class/hyp_common/ktairender/adsenseConf.php';
		if (! isset($this->k_tai_conf['googleAdsense']['below'])) $this->k_tai_conf['googleAdsense']['below'] = 'header';
		if (! isset($this->k_tai_conf['googleAnalyticsId'])) $this->k_tai_conf['googleAnalyticsId'] = '';

		if (! isset($this->k_tai_conf['urlRewrites'])) $this->k_tai_conf['urlRewrites'] = null;
		if (! isset($this->k_tai_conf['urlImgRewrites'])) $this->k_tai_conf['urlImgRewrites'] = null;

		if (! isset($this->xpwiki_render_dirname)) $this->xpwiki_render_dirname = '';
		if (! isset($this->xpwiki_render_use_wikihelper)) $this->xpwiki_render_use_wikihelper = 0;
		if (! isset($this->xpwiki_render_notuse_wikihelper_modules)) $this->xpwiki_render_notuse_wikihelper_modules = array();

		// init
		$this->nowModuleDirname = '';
		$this->detect_order_org = mb_detect_order();

		// Load conf file.
		$conffile = XOOPS_TRUST_PATH . HYP_COMMON_PRELOAD_CONF;
		$sections = array('main_switch', 'xpwiki_render', 'spam_block');
		if (is_file($conffile) && $conf = parse_ini_file($conffile, true)) {
			foreach($conf as $name => $section) {
				if ($name === 'k_tai_conf') {
					foreach($section as $key => $val) {
						$this->k_tai_conf[$key.'#'.XOOPS_URL] = $val;
					}
				} else if (in_array($name, $sections)) {
					foreach($section as $key => $val) {
						$this->$key = $val;
					}
				}
			}
			$this->xpwiki_render_notuse_wikihelper_modules = array_filter($this->xpwiki_render_notuse_wikihelper_modules);
			if (isset($this->k_tai_conf['disabledBlockIds#'.XOOPS_URL])) {
				$this->k_tai_conf['disabledBlockIds#'.XOOPS_URL] = array_filter($this->k_tai_conf['disabledBlockIds#'.XOOPS_URL]);
			}
			if (isset($this->k_tai_conf['limitedBlockIds#'.XOOPS_URL])) {
				$this->k_tai_conf['limitedBlockIds#'.XOOPS_URL] = array_filter($this->k_tai_conf['limitedBlockIds#'.XOOPS_URL]);
			}
			if (isset($this->k_tai_conf['jquery_theme#'.XOOPS_URL])) {
				$this->k_tai_conf['rebuildsEx']['jqm']['header']['above'] = preg_replace('/data-theme="[a-z]"/', 'data-theme="' . $this->k_tai_conf['jquery_theme#'.XOOPS_URL] . '"', $this->k_tai_conf['rebuildsEx']['jqm']['header']['above']);
				$this->k_tai_conf['rebuildsEx']['jqm']['body']['above']   = preg_replace('/data-theme="[a-z]"/', 'data-theme="' . $this->k_tai_conf['jquery_theme#'.XOOPS_URL] . '"', $this->k_tai_conf['rebuildsEx']['jqm']['body']['above']);
				$this->k_tai_conf['rebuildsEx']['jqm']['footer']['above'] = preg_replace('/data-theme="[a-z]"/', 'data-theme="' . $this->k_tai_conf['jquery_theme#'.XOOPS_URL] . '"', $this->k_tai_conf['rebuildsEx']['jqm']['footer']['above']);
			}
			if ($this->post_spam_badip_ttl == -1) {
				$this->post_spam_badip_ttl = null;
			}
		}

		parent::XCube_ActionFilter($controller);
	}

	function preFilter() {

		// Set const "HYP_IS_BOT_UA"
		if (preg_match($this->bot_ua_reg, $_SERVER['HTTP_USER_AGENT'])) {
			define('HYP_IS_BOT_UA', true);
		}

		// Use K_TAI Render
		if (! empty($this->use_k_tai_render)) {
			if (isset($_GET['_hypktaipc'])) {
				if (! $_GET['_hypktaisw']) {
					setcookie('_hypktaipc', '', 1, '/');
					unset($_COOKIE['_hypktaipc']);
				}
			}
			if (empty($_COOKIE['_hypktaipc']) && isset($_SERVER['HTTP_USER_AGENT']) &&
				preg_match($this->k_tai_conf['ua_regex'], $_SERVER['HTTP_USER_AGENT'])) {

				// Reset each site values.
				foreach (array_keys($this->k_tai_conf) as $key) {
					if (strpos($key, '#') === FALSE) {
						$sitekey = $key . '#' . XOOPS_URL;
						if (isset($this->k_tai_conf[$sitekey])) {
							$this->k_tai_conf[$key] = $this->k_tai_conf[$sitekey];
						}
					}
				}

				// Set HypKTaiRender
				HypCommonFunc::loadClass('HypKTaiRender');
				$this->HypKTaiRender =& HypKTaiRender::getSingleton();
				$this->HypKTaiRender->set_myRoot(XOOPS_URL);
				$this->HypKTaiRender->Config_emojiDir = XOOPS_URL . '/images/emoji';
				$this->HypKTaiRender->Config_redirect = $this->k_tai_conf['redirect'];
				$this->HypKTaiRender->Config_showImgHosts = $this->k_tai_conf['showImgHosts'];
				$this->HypKTaiRender->Config_directImgHosts = $this->k_tai_conf['directImgHosts'];
				$this->HypKTaiRender->Config_directLinkHosts = $this->k_tai_conf['directLinkHosts'];
				$this->HypKTaiRender->Config_hypCommonURL = XOOPS_URL . '/class/hyp_common';
				$this->HypKTaiRender->Config_icons = array_merge($this->HypKTaiRender->Config_icons, $this->k_tai_conf['icon']);
				$this->HypKTaiRender->pagekey = $this->k_tai_conf['getKeys']['page'];
				$this->HypKTaiRender->hashkey = $this->k_tai_conf['getKeys']['hash'];
				$this->HypKTaiRender->Config_pictSizeMax = $this->k_tai_conf['pictSizeMax'];
				$this->HypKTaiRender->Config_docomoGuidTTL = $this->k_tai_conf['docomoGuidTTL'];
				$this->HypKTaiRender->marge_urlRewites('urlRewrites', $this->k_tai_conf['urlRewrites']);
				$this->HypKTaiRender->marge_urlRewites('urlImgRewrites', $this->k_tai_conf['urlImgRewrites']);

				// use jquery mobile?
				$this->HypKTaiRender->Config_jquery = $use_jqm = (in_array($this->HypKTaiRender->vars['ua']['carrier'], explode(',', $this->k_tai_conf['jquery_profiles'])));
				$this->HypKTaiRender->Config_jquery_remove_flash = $this->k_tai_conf['jquery_remove_flash'];
				$this->HypKTaiRender->Config_jquery_resolve_table = $this->k_tai_conf['jquery_resolve_table'];
				$this->HypKTaiRender->Config_jquery_image_convert = $this->k_tai_conf['jquery_image_convert'];

				// jQuery use: 2, Normal: 1
				define('HYP_K_TAI_RENDER', ($use_jqm? 2 : 1));

				// theme & template set
				if ($use_jqm && isset($this->k_tai_conf['themeSets']['jqm'])) {
					$this->k_tai_conf['themeSet'] = $this->k_tai_conf['themeSets']['jqm'];
				}
				if (isset($this->k_tai_conf['themeSets'][$this->HypKTaiRender->vars['ua']['carrier']]) && $this->k_tai_conf['themeSets'][$this->HypKTaiRender->vars['ua']['carrier']]) {
					$this->k_tai_conf['themeSet'] = $this->k_tai_conf['themeSets'][$this->HypKTaiRender->vars['ua']['carrier']];
				}
				if ($use_jqm && isset($this->k_tai_conf['templateSets']['jqm'])) {
					$this->k_tai_conf['templateSet'] = $this->k_tai_conf['templateSets']['jqm'];
				}
				if (isset($this->k_tai_conf['templateSets'][$this->HypKTaiRender->vars['ua']['carrier']]) && $this->k_tai_conf['templateSets'][$this->HypKTaiRender->vars['ua']['carrier']]) {
					$this->k_tai_conf['templateSet'] = $this->k_tai_conf['templateSets'][$this->HypKTaiRender->vars['ua']['carrier']];
				}
				if ($use_jqm && isset($this->k_tai_conf['templates']['jqm'])) {
					$this->k_tai_conf['template'] = $this->k_tai_conf['templates']['jqm'];
				}
				// keitai render template
				if ($use_jqm && isset($this->k_tai_conf['rebuildsEx']['jqm'])) {
					$this->k_tai_conf['rebuilds'] = array_merge($this->k_tai_conf['rebuilds'], $this->k_tai_conf['rebuildsEx']['jqm']);
				}

				// jqm.css
				if ($use_jqm && !empty($this->k_tai_conf['jqm_css'])) {
					$use_jqm_css = false;
					list($this->k_tai_conf['jqm_css'], $time) = explode(':', $this->k_tai_conf['jqm_css'], 2);
					$jqm_css = '/class/hyp_common/cache/' . $this->k_tai_conf['jqm_css'];
					if (! is_file(XOOPS_ROOT_PATH . $jqm_css) || filemtime(XOOPS_ROOT_PATH . $jqm_css) < $time) {
						if (@ copy(XOOPS_TRUST_PATH . '/uploads/hyp_common/' . urlencode(substr(XOOPS_URL, 7)) . '_' . $this->k_tai_conf['jqm_css'], XOOPS_ROOT_PATH . $jqm_css)) {
							$this->k_tai_conf['jqm_css'] = XOOPS_URL . $jqm_css;
						} else {
							$this->k_tai_conf['jqm_css'] = '';
						}
					} else {
						$this->k_tai_conf['jqm_css'] = XOOPS_URL . $jqm_css;
					}
				}

				// Session setting
				@ ini_set('session.use_trans_sid', 0);
				if (! $this->HypKTaiRender->vars['ua']['allowCookie']) {
					$parseUrl = parse_url(XOOPS_URL);
					if ($_SERVER['HTTP_HOST'] !== $parseUrl['host']) {
						header('HTTP', true, 400);
						exit('400 Bad Request');
					}
					@ ini_set('session.use_cookies',      '0');
					@ ini_set('session.use_only_cookies', '0');
				} else {
					@ ini_set('session.use_cookies',      '1');
					@ ini_set('session.use_only_cookies', '1');
				}

				// HTTP_REFERER
				if (! empty($_POST) && empty($_SERVER['HTTP_REFERER'])) {
					$_SERVER['HTTP_REFERER'] = XOOPS_URL . '/';
				}
			} else {
				define('HYP_K_TAI_RENDER', FALSE);
			}
		}

		// xpWiki renderer setting
		if (defined('XOOPS_CUBE_LEGACY') && !defined('XPWIKI_RENDERER_DIR') && $this->xpwiki_render_dirname) {
			if (! defined('XPWIKI_RENDERER_DIR')) define('XPWIKI_RENDERER_DIR', $this->xpwiki_render_dirname);
			if (! defined('XPWIKI_RENDERER_USE_WIKIHELPER')) define('XPWIKI_RENDERER_USE_WIKIHELPER', $this->xpwiki_render_use_wikihelper);
			include_once XOOPS_TRUST_PATH . '/class/hyp_common/xc_classes/Hyp_TextFilter.php';
			$this->mController->mSetupTextFilter->add('Hyp_TextFilter::getInstance', XCUBE_DELEGATE_PRIORITY_FINAL-2);
		}
	}

	function preBlockFilter()
	{
		// Use K_TAI Render (XCL only)
		if (defined('XOOPS_CUBE_LEGACY') && defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER) {

			// Set theme set
			if (isset($this->k_tai_conf['themeSet']) && is_file(XOOPS_THEME_PATH . '/' . $this->k_tai_conf['themeSet'] . '/theme.html')) {
				$GLOBALS['xoopsConfig']['theme_set'] = $this->k_tai_conf['themeSet'];
				$this->mRoot->mContext->setThemeName($this->k_tai_conf['themeSet']);
				$this->mRoot->mDelegateManager->add( 'XoopsTpl.New' , array(& $this , '_xoopsConfig_theme_set' ) , XCUBE_DELEGATE_PRIORITY_FIRST) ;
			}

			// Set template set
			if (! empty($this->k_tai_conf['templateSet'])) {
				$GLOBALS['xoopsConfig']['template_set'] = $this->k_tai_conf['templateSet'];
				$this->mRoot->mDelegateManager->add( 'XoopsTpl.New' , array(& $this , '_xoopsConfig_template_set' ) , XCUBE_DELEGATE_PRIORITY_FIRST) ;
			}

	        // For cubeUtils (disable auto login)
	        $config_handler =& xoops_gethandler('config');
	        $moduleConfigCubeUtils =& $config_handler->getConfigsByDirname('cubeUtils');
			if ($moduleConfigCubeUtils) {
	        	$moduleConfigCubeUtils['cubeUtils_use_autologin'] = FALSE;
			}

			include_once(dirname(dirname(__FILE__)).'/xc_classes/disabledBlock.php');
			$this->mRoot->mDelegateManager->add( 'Legacy_Utils.CreateBlockProcedure' , array(& $this , 'blockControlXCL' )) ;

			// For STD cache module (cache disabled)
			$this->mController->mSetBlockCachePolicy->add(array(& $this, '_stdCacheHook'), XCUBE_DELEGATE_PRIORITY_FIRST + 11);
			$this->mController->mSetModuleCachePolicy->add(array(& $this, '_stdCacheHook'), XCUBE_DELEGATE_PRIORITY_FIRST + 11);
		}
	}

	function _xoopsConfig_theme_set () {
		$GLOBALS['xoopsConfig']['theme_set'] = $this->k_tai_conf['themeSet'];
	}

	function _xoopsConfig_template_set () {
		$GLOBALS['xoopsConfig']['template_set'] = $this->k_tai_conf['templateSet'];
	}

	function _stdCacheHook (& $cacheInfo) {
		$cacheInfo->setEnableCache(false);
	}

	// Block Control
	function blockControlXCL (& $retBlock, $block) {
		if (! empty($this->k_tai_conf['disabledBlockIds']) && is_array($this->k_tai_conf['disabledBlockIds'])) {
			if (in_array($block->getVar('bid'), $this->k_tai_conf['disabledBlockIds'])) {
				$retBlock = new HypXCLDisabledBlock();
				return;
			}
		}
		if (! empty($this->k_tai_conf['limitedBlockIds']) && is_array($this->k_tai_conf['limitedBlockIds'])) {
			if (! in_array($block->getVar('bid'), $this->k_tai_conf['limitedBlockIds'])) {
				$retBlock = new HypXCLDisabledBlock();
				return;
			}
		}
	}
	function blockControlX2 ($bid) {
	    if (! empty($this->k_tai_conf['disabledBlockIds']) && is_array($this->k_tai_conf['disabledBlockIds'])) {
	    	if (in_array($bid, $this->k_tai_conf['disabledBlockIds'])) {
	    		return FALSE;
	    	}
	    }
	    if (! empty($this->k_tai_conf['limitedBlockIds']) && is_array($this->k_tai_conf['limitedBlockIds'])) {
	    	if (! in_array($bid, $this->k_tai_conf['limitedBlockIds'])) {
	    		return FALSE;
	    	}
	    }
	    return TRUE;
	}

	function postFilter() {

		if (defined('HYP_COMMON_SKIP_POST_FILTER')) return;

		// Set mb_detect_order
		if ($this->detect_order) {
			mb_detect_order($this->detect_order);
		}

		// For WizMobile
		if (XC_CLASS_EXISTS('WizMobile')) {
			define('HYP_WIZMOBILE_USE', true);
		}

		// XOOPS の表示文字エンコーディング
		$this->encode = strtoupper(_CHARSET);

		// 設定ファイルのエンコーディングを検査
		if ($this->encode !== 'UTF-8' && $this->encode !== strtoupper($this->configEncoding)) {
			$this->encodehint_word = '';
		}

		if (! $this->wizMobileUse && ! empty($_GET)) {
			// 文字コードを正規化
			$enchint = (isset($_GET[$this->encodehint_name]))? $_GET[$this->encodehint_name] : ((isset($_GET['encode_hint']))? $_GET['encode_hint'] : '');
			if ($enchint && function_exists('mb_detect_encoding')) {
				define ('HYP_GET_ENCODING', strtoupper(mb_detect_encoding($enchint)));
				$_GET = HypCommonFunc::input_filter($_GET, $this->input_filter_strength, HYP_GET_ENCODING);
				if (HYP_GET_ENCODING !== $this->encode) {
					mb_convert_variables($this->encode, HYP_GET_ENCODING, $_GET);
					if (isset($_GET['charset'])) $_GET['charset'] = $this->encode;
				}
			} else {
				$_GET = HypCommonFunc::input_filter($_GET, $this->input_filter_strength);
			}
		}

		global $xoopsUser, $xoopsUserIsAdmin, $xoopsModule;

		if (is_object($xoopsModule)) {
			$this->nowModuleDirname = $xoopsModule->getVar('dirname');
		}

		// For addHeadTag()
		if (! isset($GLOBALS['hyp_preload_head_tag'])) $GLOBALS['hyp_preload_head_tag'] = '';

		if (! empty($_POST)) {

			// POST 文字列の文字エンコードを判定
			$enchint = (isset($_POST[$this->encodehint_name]))? $_POST[$this->encodehint_name] : ((isset($_POST['encode_hint']))? $_POST['encode_hint'] : '');
			if ($enchint && function_exists('mb_detect_encoding')) {
				define ('HYP_POST_ENCODING', strtoupper(mb_detect_encoding($enchint)));
			} else if (isset($_POST['charset'])) {
				define ('HYP_POST_ENCODING', strtoupper($_POST['charset']));
			}

			// 携帯レンダーの場合絵文字変換
			if ((defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER) || isset($_SERVER['HTTP_X_ORIGINAL_USER_AGENT'])) {
				$_POST = $this->_modKtaiEmojiEncode($_POST);
			}

			// Input フィルター (remove "\0")
			$_POST = HypCommonFunc::input_filter($_POST, $this->input_filter_strength, (defined('HYP_POST_ENCODING')? HYP_POST_ENCODING : null));

			// Proxy Check
			if ($this->use_proxy_check) {
				if (! defined('HYP_K_TAI_RENDER') || HYP_K_TAI_RENDER !== 1 || ! $this->HypKTaiRender->vars['ua']['inIPRange']) {
					HypCommonFunc::BBQ_Check($this->no_proxy_check, $this->msg_proxy_check, NULL, $this->post_spam_checkers);
				}
			}

			// 文字エンコーディング外の文字を数値エンティティに変換
			if (defined('HYP_POST_ENCODING') && HYP_POST_ENCODING === 'UTF-8' && $this->encode !== 'UTF-8') {
				HypCommonFunc::encode_numericentity($_POST, $this->encode, 'UTF-8');
			}

			// 機種依存文字フィルター
			if (defined('HYP_POST_ENCODING') && $this->use_dependence_filter) {
				$_POST = HypCommonFunc::dependence_filter($_POST);
			}

			// 文字コードを正規化
			if (! $this->wizMobileUse && defined('HYP_POST_ENCODING') && $this->encode !== HYP_POST_ENCODING) {
				mb_convert_variables($this->encode, HYP_POST_ENCODING, $_POST);
				if (isset($_POST['charset'])) $_POST['charset'] = $this->encode;
			}

			// PostSpam をチェック
			if ($this->use_post_spam_filter) {
				// 加算 pt
				if ($this->post_spam_rules) {
					foreach ($this->post_spam_rules as $rule => $point) {
						if ($rule && $point) {
							HypCommonFunc::PostSpam_filter($rule, $point);
						}
					}
				}

				// チェックをパスするフィールド名
				if (! empty($this->post_spam_pass_names)) {
					HypCommonFunc::PostSpam_filter('pass_keys', explode(',', $this->post_spam_pass_names));
				}

				// 無効なフィールド定義
				if (! empty($this->post_spam_trap)) {
					$this->ignore_fileds[$this->post_spam_trap] = array('');
				}
				if (is_array($this->ignore_fileds) && $this->ignore_fileds) {
					HypCommonFunc::PostSpam_filter('array_rule', array('ignore_fileds' => array($this->ignore_fileds, $this->post_spam_filed)));
				}

				// PukiWikiMod のスパム定義読み込み 31pt
				$datfile = XOOPS_ROOT_PATH.'/modules/pukiwiki/cache/spamdeny.dat';
				if (is_file($datfile)) {
					HypCommonFunc::PostSpam_filter("/".trim(join("",file($datfile)))."/i", 31);
				}

				// Default スパムサイト定義読み込み
				$datfiles = array();
				$datfiles[] = HYP_COMMON_ROOT_PATH . '/dat/spamsites.dat';
				$datfiles[] = HYP_COMMON_ROOT_PATH . '/config/spamsites.conf.dat';
				$checks = array();
				$mtime = 0;
				foreach($datfiles as $datfile) {
					if (is_file($datfile)) {
						$mtime = max(filemtime($datfile), $mtime);
						$checks[] = $datfile;
					}
				}
				if ($checks) {
					$cachefile = XOOPS_TRUST_PATH . '/cache/hyp_spamsites.dat';
					if ($mtime > @ filemtime($cachefile)) {
						$words = array();
						foreach($checks as $datfile) {
							$words = array_merge($words, file($datfile));
						}
						$regs = HypCommonFunc::get_matcher_regex_safe($words, "\x08");
						HypCommonFunc::flock_put_contents($cachefile, $regs);
					} else {
						$regs = join('', file($cachefile));
					}
					foreach(explode("\x08", $regs) as $reg) {
						HypCommonFunc::PostSpam_filter('/((ht|f)tps?:\/\/(.+\.)*|@|url=)' . $reg . '/i', $this->post_spam_host);
					}
				}

				// Default スパムワード定義読み込み
				$datfiles = array();
				$datfiles[] = HYP_COMMON_ROOT_PATH . '/dat/spamwords.dat';
				$datfiles[] = HYP_COMMON_ROOT_PATH . '/config/spamwords.conf.dat';
				$checks = array();
				$mtime = 0;
				foreach($datfiles as $datfile) {
					if (is_file($datfile)) {
						$mtime = max(filemtime($datfile), $mtime);
						$checks[] = $datfile;
					}
				}
				if ($checks) {
					$cachefile = XOOPS_TRUST_PATH . '/cache/hyp_spamwords_'.$this->encode.'.dat';
					if ($mtime > @ filemtime($cachefile)) {
						$words = array();
						foreach($checks as $datfile) {
							$_lines = file($datfile);
							if ($_lines[0][0] === '@') {
								$_enc = trim(substr(rtrim($_lines[0]), 1));
								array_shift($_lines);
								mb_convert_variables($this->encode, $_enc, $_lines);
							}
							$words = array_merge($words, $_lines);
						}
						$regs = HypCommonFunc::get_matcher_regex_safe($words, "\x08");
						HypCommonFunc::flock_put_contents($cachefile, $regs);
					} else {
						$regs = join('', file($cachefile));
					}
					foreach(explode("\x08", $regs) as $reg) {
						HypCommonFunc::PostSpam_filter('/' . $reg . '/i', $this->post_spam_word);
					}
				}

				// 判定
				if (!$xoopsUserIsAdmin) {
					// 閾値
					$spamlev = (is_object($xoopsUser))? $this->post_spam_user : $this->post_spam_guest;
					$level = HypCommonFunc::get_postspam_avr($this->post_spam_a, $this->post_spam_bb, $this->post_spam_url, $this->encode, $this->encodehint_name);

					// URL中の存在しないホスト名をチェック
					if ($this->post_spam_unhost && ! is_object($xoopsUser)) {
						$level += HypCommonFunc::URL_Check($_POST) * $this->post_spam_unhost;
					}

					if ($level > $spamlev) {
						$ttl = ($level > $this->post_spam_badip_forever)? $this->post_spam_badip_ttl0 : $this->post_spam_badip_ttl;
						if ($level > $this->post_spam_badip) { HypCommonFunc::register_bad_ips(null, $ttl); }
						if ($this->use_mail_notify) { $this->sendMail($level); }
						exit('Processing was not completed.');
					} else {
						if ($this->use_mail_notify > 1) { $this->sendMail($level); }
					}
				}
			}
		}

		// Insert tag into <head>
		ob_start(array(& $this, 'addHeadTag'));

		// Set Query Words
		if ($this->use_set_query_words) {
			HypCommonFunc::set_query_words($this->q_word, $this->q_word2, $this->se_name, $this->kakasi_cache_dir, $this->encode);
			if ($this->use_words_highlight) {
				if (constant($this->q_word)) {
					$GLOBALS['hyp_preload_head_tag'] .= '<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/class/hyp_common/words_highlight.css" />';
				}
				ob_start(array(& $this, 'wordsHighlight'));
			}
		}

		// Use K_TAI Render
		if (defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER) {

			// docomo i-mode ID のチェック
			$idCheck = $this->HypKTaiRender->checkDeviceId( XOOPS_DB_PASS );
			if ($idCheck === 'redirect') {
				exit();
			} else if (! $idCheck && is_object($xoopsUser)) {
				// ログインしている場合のみ
				$_SESSION = array();
				exit('Device ID does not match.');
			}
			// Redirect 指定ファイルの確認 ( by _onShutdownKtai() )
			$this->_checkRedirectFile();

			// Check login
			$this->_checkEasyLogin();

			// Setup session ID
			$this->HypKTaiRender->setupSID();

			// HTTP_REFERER
			if (empty($this->HypKTaiRender->SERVER['HTTP_REFERER'])) {
				if (! empty($_SESSION['hypKtaiReferer'])) {
					$_SERVER['HTTP_REFERER'] = $this->HypKTaiRender->SERVER['HTTP_REFERER'] = $_SESSION['hypKtaiReferer'];
				} else if (! empty($_SERVER['HTTP_REFERER'])) {
					// セッションに積んでないのに preFilter() で自動セット = CSRF
					exit('Bad Request.');
				}
			}
			$_SESSION['hypKtaiReferer'] = $this->HypKTaiRender->myRoot . $this->HypKTaiRender->SERVER['REQUEST_URI'];
			if (isset($_SERVER['HTTP_REFERER'])) {
				$_SERVER['HTTP_REFERER'] = $this->HypKTaiRender->removeQueryFromUrl($_SERVER['HTTP_REFERER'], array($this->HypKTaiRender->session_name, 'guid'));
			}

			// Remove control keys
			$this->k_tai_conf['getKeys'][] = $this->HypKTaiRender->session_name;
			$this->k_tai_conf['getKeys'][] = 'guid';
			if (isset($_SERVER['QUERY_STRING'])) {
				$_SERVER['QUERY_STRING'] = ltrim($this->HypKTaiRender->removeQueryFromUrl('?' . $_SERVER['QUERY_STRING'], $this->k_tai_conf['getKeys']), '?');
			}
			if (isset($_SERVER['argv'][0])) {
				$_SERVER['argv'][0] = ltrim($this->HypKTaiRender->removeQueryFromUrl('?' . $_SERVER['argv'][0], $this->k_tai_conf['getKeys']), '?');
			}
			foreach(array('REQUEST_URI', '_REQUEST_URI') as $_key) {
				if (isset($_SERVER[$_key])) {
					$_SERVER[$_key] = $this->HypKTaiRender->removeQueryFromUrl($_SERVER[$_key], $this->k_tai_conf['getKeys']);
				}
			}

			// $this->k_tai_conf['msg'] 文字コード変換
			if ($this->encode !== strtoupper($this->configEncoding)) {
				if (function_exists('mb_convert_encoding') && $this->configEncoding && $this->encode !== $this->configEncoding) {
					mb_convert_variables($this->encode, $this->configEncoding, $this->k_tai_conf['msg']);
					mb_convert_variables($this->encode, $this->configEncoding, $this->k_tai_conf['rebuilds']);
				}
			}

			// 言語定数セット
			foreach($this->k_tai_conf['msg'] as $key => $val) {
				define('KTAI_RENDER_MSG_' . strtoupper($key), $val);
			}

			// Set theme set
			if (isset($this->k_tai_conf['themeSet']) && is_file(XOOPS_THEME_PATH . '/' . $this->k_tai_conf['themeSet'] . '/theme.html')) {
				$GLOBALS['xoopsConfig']['theme_set'] = $this->k_tai_conf['themeSet'];
				// For ImpressCMS 1.2
				if (isset($GLOBALS['icmsConfig'])) {
					$GLOBALS['icmsConfig']['theme_set'] = $this->k_tai_conf['themeSet'];
				}
				if (defined('XOOPS_CUBE_LEGACY')) {
					// Over write user setting
					$this->mRoot->mContext->setThemeName($this->k_tai_conf['themeSet']);
				}
			}
			// Set template set
			if (! empty($this->k_tai_conf['templateSet'])) {
				$GLOBALS['xoopsConfig']['template_set'] = $this->k_tai_conf['templateSet'];
				// For ImpressCMS 1.2
				if (isset($GLOBALS['icmsConfig'])) {
					$GLOBALS['icmsConfig']['template_set'] = $this->k_tai_conf['templateSet'];
				}
			}
			// Hint character for encoding judgment
			if (! empty($this->encodehint_word)) {
				if (function_exists('mb_convert_encoding') && $this->configEncoding && $this->encode !== $this->configEncoding) {
					$encodehint_word = mb_convert_encoding($this->encodehint_word, $this->encode, $this->configEncoding);
				} else {
					$encodehint_word = $this->encodehint_word;
				}
				$this->HypKTaiRender->Config_encodeHintWord = $encodehint_word;
				$this->HypKTaiRender->Config_encodeHintName = $this->encodehint_name;
				$this->encodehint_word = '';
			}
			// google AdSense
			if ($this->k_tai_conf['googleAdsense']['config']) {
				$this->HypKTaiRender->Config_googleAdSenseConfig = $this->k_tai_conf['googleAdsense']['config'];
				$this->HypKTaiRender->Config_googleAdSenseBelow = $this->k_tai_conf['googleAdsense']['below'];
			}

			// keitai Filter
			ob_start(array(& $this, 'keitaiFilter'));

			// smart redirection for smartphone
			if (HYP_K_TAI_RENDER > 1) {
				ob_start(array(& $this, 'smartRedirect'));
			}

			register_shutdown_function(array(& $this, '_onShutdownKtai'));
		} else {
			// <from> Filter
			if (! $this->wizMobileUse) {
				ob_start(array(& $this, 'formFilter'));
			}
			// emoji Filter
			if (! empty($this->use_k_tai_render)) {
				ob_start(array(& $this, 'emojiFilter'));
			}

			if (isset($_SERVER['HTTP_X_ORIGINAL_USER_AGENT']) && $this->encode !== 'UTF-8'){
				ob_start(array(& $this, 'utf8Filter'));
			}

			// Add button to smartphone style
			if (! empty($_COOKIE['_hypktaipc'])) {
				// $this->k_tai_conf['msg'] 文字コード変換
				if ($this->encode !== strtoupper($this->configEncoding)) {
					if (function_exists('mb_convert_encoding') && $this->configEncoding && $this->encode !== $this->configEncoding) {
						mb_convert_variables($this->encode, $this->configEncoding, $this->k_tai_conf['msg']);
					}
				}
				ob_start(array(& $this, 'switchOfSmartPhone'));
			}

			// smart redirection
			if (! empty($this->use_smart_redirect)) {
				ob_start(array(& $this, 'smartRedirect'));
			}

		}

		// Restor mb_detect_order
		if ($this->detect_order_org) {
			mb_detect_order($this->detect_order_org);
		}
	}

	function _onShutdownKtai() {
		if (! $this->HypKTaiRender->vars['ua']['allowCookie']) {
			$url = '';
			$arh = FALSE;
			if (function_exists('apache_response_headers')) {
				$arh = apache_response_headers();
				if (is_array($arh)) {
					foreach(array('Location', 'location', 'LOCATION') as $key) {
						if (isset($arh[$key])) {
							$url = trim($arh[$key]);
							break;
						}
					}
				}
			}
			if ($arh === FALSE && function_exists('headers_list')) {
				foreach (headers_list() as $header) {
					if (preg_match('/^Location:(.+)$/is', $header, $match)) {
						$url = trim($match[1]);
						break;
					}
				}
			}
			if ($url) {
				$nosession = (strpos($url, session_name() . '=') === FALSE);
				$url = $this->HypKTaiRender->getRealUrl($url);
				$url = $this->HypKTaiRender->addSID($url, XOOPS_URL);
				if (! headers_sent()) {
					header('Location: ' . $url, TRUE);
				} else if ($this->HypKTaiRender->vars['ua']['uid'] && $nosession) {
					$file = XOOPS_ROOT_PATH . '/cache/' . md5($this->HypKTaiRender->vars['ua']['uid'] . XOOPS_DB_PASS) . '.redirect';
					$fp = fopen($file, 'w');
					fwrite($fp, $url);
					fclose($fp);
				}
			}
		}
	}

	function _checkEasyLogin () {
		if (empty($_SESSION['xoopsUserId'])) {
			$this->HypKTaiRender->vars['ua']['xoopsUid'] = 0;
			$this->HypKTaiRender->vars['ua']['isGuest'] = TRUE;
		} else {
			$this->HypKTaiRender->vars['ua']['xoopsUid'] = intval($_SESSION['xoopsUserId']);
			$this->HypKTaiRender->vars['ua']['isGuest'] = FALSE;
			if (empty($this->k_tai_conf['noCheckIpRange']) && ! $this->HypKTaiRender->checkIp ($_SERVER['REMOTE_ADDR'], $this->HypKTaiRender->vars['ua']['carrier'])) {
				$_SESSION = array();
				exit('Your IP "' . $_SERVER['REMOTE_ADDR'] . '" doesn\'t match to IP range of "'.$this->HypKTaiRender->vars['ua']['carrier'].'".');
			}
		}

		if (! empty($this->k_tai_conf['easyLogin'])) {

			if (isset($_GET['_EASYLOGIN']) || ($this->HypKTaiRender->vars['ua']['xoopsUid'] && (isset($_GET['_EASYLOGINSET']) || isset($_GET['_EASYLOGINUNSET'])))) {
				if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')) {
					exit('Can not use "Easy Login" on SSL connection.');
				}

				if (empty($this->HypKTaiRender->vars['ua']['uid'])) {
					exit('Could not got your device ID.');
				}

				if (empty($this->k_tai_conf['noCheckIpRange']) && ! $this->HypKTaiRender->vars['ua']['inIPRange']) {
					exit('Your IP "' . $_SERVER['REMOTE_ADDR'] . '" doesn\'t match to IP range of "'.$this->HypKTaiRender->vars['ua']['carrier'].'".');
				}

				$mode = '';
				if (isset($_GET['_EASYLOGIN'])) {
					$mode = 'login';
				} else if (isset($_GET['_EASYLOGINSET'])) {
					$mode = 'set';
				} else if (isset($_GET['_EASYLOGINUNSET'])) {
					$mode = 'unset';
				}

				$uaUid = md5($this->HypKTaiRender->vars['ua']['uid'] . XOOPS_DB_PASS);

				// Read data file
				$myroot = str_replace('/', '_', preg_replace('#https?://#i', '', XOOPS_URL));
				$datfile = XOOPS_TRUST_PATH . '/uploads/hyp_common/' . $myroot . '_easylogin.dat';
				if (is_file($datfile)) {
					$uids = unserialize(HypCommonFunc::flock_get_contents($datfile));
				} else {
					$uids = array();
				}

				if ($this->HypKTaiRender->vars['ua']['xoopsUid']) {
					// Check & save uids data
					if (! isset($uids[$uaUid]) || $uids[$uaUid] !== $this->HypKTaiRender->vars['ua']['xoopsUid'] || $mode === 'unset') {
						if ($mode === 'unset') {
							unset($uids[$uaUid]);
						} else {
							$uids[$uaUid] = $this->HypKTaiRender->vars['ua']['xoopsUid'];
						}
						HypCommonFunc::flock_put_contents($datfile, serialize($uids));

						$uri = $this->HypKTaiRender->SERVER['REQUEST_URI'];
						//$uri = $this->HypKTaiRender->removeSID($uri);
						$url = $this->HypKTaiRender->myRoot . $this->HypKTaiRender->removeQueryFromUrl($uri, array($this->HypKTaiRender->session_name, 'guid', '_EASYLOGIN', '_EASYLOGINSET', '_EASYLOGINUNSET'));

						$url = $this->HypKTaiRender->addSID($url);
						header('Location: ' . $url);
						exit();
					}
				} else if ($mode === 'login') {
					// Do easy login

					$uri = $this->HypKTaiRender->SERVER['REQUEST_URI'];
					$uri = $this->HypKTaiRender->removeSID($uri);

					// Default is login form
					$url = XOOPS_URL . '/user.php?xoops_redirect=' . rawurlencode($uri);

					if (! empty($uids[$uaUid])) {
				        // Login success
				        $member_handler =& xoops_gethandler('member');
				        $user =& $member_handler->getUser($uids[$uaUid]);
						if (false !== $user && $user->getVar('level') > 0) {
					        session_regenerate_id();
							// Update last login
							$user->setVar('last_login', time());
							$member_handler->insertUser($user, TRUE);

							// Set session vars
							$_SESSION['xoopsUserId'] = $uids[$uaUid];
							$_SESSION['xoopsUserGroups'] = $user->getGroups();
							$user_theme = $user->getVar('theme');
							if (in_array($user_theme, $GLOBALS['xoopsConfig']['theme_set_allowed'])) {
								$_SESSION['xoopsUserTheme'] = $user_theme;
							}

							$url = $this->HypKTaiRender->myRoot . $this->HypKTaiRender->removeQueryFromUrl($uri, array($this->HypKTaiRender->session_name, 'guid', '_EASYLOGIN'));
							$config_handler =& xoops_gethandler('config');
							$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
							include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/user.php';
							$_SESSION['hyp_redirect_message'] = sprintf(_US_LOGGINGU, $user->getVar('uname'));
							$_SESSION['hyp_redirect_uname'] = $user->getVar('uname');
				        }
					}
					// Redirect
					$url = $this->HypKTaiRender->addSID($url);
					header('Location: ' . $url);
					exit();
				}
			}
		}
	}

	function _checkRedirectFile() {
		// Redirect 指定ファイルの確認 ( by _onShutdownKtai() )
		if (! $this->HypKTaiRender->vars['ua']['allowCookie'] && $this->HypKTaiRender->vars['ua']['uid']) {
			$redirectfile = XOOPS_ROOT_PATH . '/cache/' . md5($this->HypKTaiRender->vars['ua']['uid'] . XOOPS_DB_PASS) . '.redirect';
			if (is_file($redirectfile)) {
				if (filemtime($redirectfile) + 10 > time()) {
					list($url) = file($redirectfile);
					unlink($redirectfile);
					header('Location: '. $url);
					exit();
				} else {
					unlink($redirectfile);
				}
			}
		}
	}

	function _modKtaiEmojiEncode ($vars) {

		if (! defined('HYP_POST_ENCODING')) return $vars;

		if (is_array($vars)) {
			foreach($vars as $key=>$var) {
				$vars[$key] = $this->_modKtaiEmojiEncode($var);
			}
			return $vars;
		}
		static $mpc;
		static $to;
		static $euc = FALSE;

		$to = $mpc = NULL;

		if (is_null($mpc)) {
			if (! XC_CLASS_EXISTS('MobilePictogramConverter')) {
				HypCommonFunc::loadClass('MobilePictogramConverter');
			}

			$carrier = '';
			$mpc = '';

			$from_encode = '';
			switch (HYP_POST_ENCODING) {
				case 'UTF-8':
				case 'UTF_8':
				case 'UTF8':
					$from_encode = MPC_FROM_CHARSET_UTF8;
					break;
				case 'SJIS':
				case 'SHIFT-JIS':
				case 'SHIFT_JIS':
				case 'SJIS-WIN':
					$from_encode = MPC_FROM_CHARSET_SJIS;
					break;
				case 'EUCJP-WIN':
				case 'EUC-JP':
					$euc = TRUE;
					$from_encode = MPC_FROM_CHARSET_SJIS; // fake
					// EUC-JP なフォームからで絵文字が化けている場合に備えて除去。結果的に絵文字対応できない。
					$vars = preg_replace('/[\x00-\x08\x0b-\x0c\x0e\x10-\x1a\x1c-\x1f\x7f]+/', '', $vars);
					if ($vars !== $vars) {
						$from_encode = '';
					}
					break;
			}

			if ($from_encode) {
				$check = '';
				if (defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER) {
					$check = $this->HypKTaiRender->vars['ua']['carrier'];
				} else {
					$ua = $_SERVER['HTTP_X_ORIGINAL_USER_AGENT'];
					if (preg_match('#(?:(SoftBank|Vodafone|J-PHONE)|(DoCoMo)|(UP\.Browser))#i', $ua, $match)) {
						if (! empty($match[1])) {
							$check = 'softbank';
						} else if (! empty($match[2])) {
							$check = 'docomo';
						} else {
							$check = 'au';
						}
					}
				}
				switch ($check) {
					case 'docomo':
						$to = MPC_TO_FOMA;
						$carrier = MPC_FROM_FOMA;

						break;
					case 'softbank':
						$to = MPC_TO_SOFTBANK;
						$carrier = MPC_FROM_SOFTBANK;
						break;
					case 'au':
						$to = MPC_TO_EZWEB;
						$carrier = MPC_FROM_EZWEB;
						break;
					default:
						$carrier = '';
				}
				if ($carrier) {
					$mpc =& MobilePictogramConverter::factory('', $carrier, $from_encode, MPC_FROM_OPTION_RAW);
				}
			}
		}

		if (! $mpc || ! $vars) return $vars;

		if ($euc) {
			return $mpc->euc2ktaimod($vars);
		} else {
			$mpc->setString($vars);
			return $mpc->Convert($to, MPC_TO_OPTION_MODKTAI);
		}
	}

	function addHeadTag( $s ) {
		if ($s === '' || strpos($s, '<html') === FALSE) return false;

		if ($this->xpwiki_render_dirname && $this->xpwiki_render_use_wikihelper) {
			$notUseWikihelper = false;
			if ($this->nowModuleDirname) {
				$notUseWikihelper = in_array($this->nowModuleDirname, $this->xpwiki_render_notuse_wikihelper_modules);
			}
			if (! $notUseWikihelper) {
				$js = '<script type="text/javascript" src="'.XOOPS_URL.'/modules/'.$this->xpwiki_render_dirname.'/skin/loader.php?src=wikihelper_loader.js"></script>';
				if (empty($GLOBALS['hyp_preload_head_tag']) || strpos($GLOBALS['hyp_preload_head_tag'], $js) === false) {
					$GLOBALS['hyp_preload_head_tag'] .= "\n" . $js;
				}
			}
		}

		if (! empty($GLOBALS['hyp_preload_head_tag'])) {
			list($head, $body) = array_pad(explode('</head>', $s, 2), 2, '');
			if (! $body) return false;
			$s = $head . $GLOBALS['hyp_preload_head_tag'] . '</head>' . $body;
		}
		return $s;
	}

	function wordsHighlight( $s ) {

		if ($s === '' || strpos($s, '<html') === FALSE) return false;

		if (function_exists('mb_convert_encoding') && $this->configEncoding && $this->encode !== $this->configEncoding) {
			$this->msg_words_highlight = mb_convert_encoding($this->msg_words_highlight, $this->encode, $this->configEncoding);
		}
		return HypGetQueryWord::word_highlight($s, (defined($this->q_word2)? constant($this->q_word) . ' ' . constant($this->q_word2) : constant($this->q_word)), $this->encode, $this->msg_words_highlight, $this->extlink_class_name);
	}

	function smartRedirect( $s ) {
		$part = substr($s, 0, 4096);
		if ($s === '' || strpos($part, '<html') === FALSE) return false;
		if (strpos($part, 'http-equiv') !== FALSE && preg_match('#<meta[^>]+http-equiv=("|\')Refresh\\1[^>]+content=("|\')([\d]+);\s*url=(.+)\\2[^>]*>#iUS', $part, $match)) {
			if (headers_sent()) return $s;
			$wait = $match[3];
			$s_url = $match[4];
			$url = strtr(str_replace('&amp;', '&', $s_url), "\r\n\0", "   ");
			if (preg_match('#<body[^>]*?>(.+?)</body>#is', $s, $body)) {
				$body = $body[1];
				$body = preg_replace('#<p>.*?<a[^>]*?href="'.preg_quote($s_url, '#').'".*?</p>#', '', $body);
				$_SESSION['hyp_redirect_message'] = $body;
				$_SESSION['hyp_redirect_wait'] = $wait;
			}
			header('Location: ' .$url);
			return '';
		} else {
			if (!empty($_SESSION['hyp_redirect_message'])) {
				$wait = max($this->smart_redirect_min_sec, $_SESSION['hyp_redirect_wait']);
				$msg = '<div id="redirect_message" style="text-align:center;">' . $_SESSION['hyp_redirect_message'] . '</div>';
				if (defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER) $msg = '<!--redirectMessage-->' . $msg. '<!--/redirectMessage-->';
				$js_head = <<<EOD
<script type="text/javascript">
//<![CDATA[
(function(wait){
	var elm = document.getElementById('redirect_message');
	var stl = elm.style;
	stl.position = 'fixed';
	stl.top = '10px';
	stl.left = '20%';
	stl.width = '60%';
	stl.zIndex = '100000';
	stl.textAlign = 'center';
	stl.backgroundColor = 'white';
	stl.filter = 'alpha(opacity=70)';
	stl.MozOpacity = '0.7';
	stl.opacity = '0.7';
	stl.border = '1px solid gray';
	stl.cursor = 'pointer';
	elm.onclick = function(){elm.style.display = 'none'};
	var btn = document.createElement('INPUT');
	btn.type = 'button';
	btn.style.width = '100%';
	btn.style.cursor = 'pointer';
	btn.value = 'OK ( ' + wait + ' sec to Close )';
	btn.onclick = function(){elm.style.display = 'none'};
	elm.appendChild(btn);
}($wait));
//]]>
</script>
EOD;
				$js_foot = <<<EOD
<script type="text/javascript">
//<![CDATA[
(function(wait){
	var elm = document.getElementById('redirect_message');
	var org_onload = (!!window.onload)? window.onload : false;
	window.onload = function() {
		setTimeout(function(){elm.style.display = 'none'} ,(wait * 1000));
		if (org_onload) org_onload();
	}
}($wait));
//]]>
</script>
EOD;
				$s = preg_replace('#<body[^>]*?>#is', '$0' . $msg . $js_head, $s);
				$s = preg_replace('#</body>#i', $js_foot . '$0', $s);
			}
			unset($_SESSION['hyp_redirect_message'], $_SESSION['hyp_redirect_wait']);
			return $s;
		}
	}

	function formFilter( $s ) {

		if ($s === '' || strpos($s, '<html') === FALSE) return false;

		$insert = '';

		// スパムロボット用の罠を仕掛ける
		if (! empty($this->post_spam_trap_set)) {
			$insert .= "\n<input name=\"{$this->post_spam_trap}\" type=\"text\" size=\"1\" style=\"display:none;speak:none;\" autocomplete=\"off\" />";
		}
		// エンコーディング判定用ヒント文字
		if (! empty($this->encodehint_word)) {
			if (function_exists('mb_convert_encoding') && $this->configEncoding && $this->encode !== $this->configEncoding) {
				$encodehint_word = mb_convert_encoding($this->encodehint_word, $this->encode, $this->configEncoding);
			} else {
				$encodehint_word = $this->encodehint_word;
			}
			$insert .= "\n<input name=\"{$this->encodehint_name}\" type=\"hidden\" value=\"{$encodehint_word}\" />";
		}
		if ($insert) {
			$insert = "\n".$insert."\n";
			return preg_replace('/<form[^>]+?>/isS' ,
				"$0".$insert, $s);
		}
		return $s;
	}

	function keitaiFilter ( $s ) {

		if ($s === '') return false;

		$head = $header = $body = $footer = $pagetitle = '';
		$header_template = $body_template = $footer_template = '';
		$encode = '';

		$rebuilds = $this->k_tai_conf['rebuilds'];

		if (isset($rebuilds['redirectMessage'])) {
			// check "redirectMessage" at last.
			$_redirectMessage = $rebuilds['redirectMessage'];
			unset($rebuilds['redirectMessage']);
			$rebuilds['redirectMessage'] = $_redirectMessage;
		}

		// テンプレート読み込み
		if ($rebuilds && $this->k_tai_conf['template']) {
			$templates_dir = dirname(dirname( __FILE__ )) . '/ktairender/templates/' . $this->k_tai_conf['template']  . '/';
			foreach(array('header', 'body', 'footer') as $_name) {
				if (is_file( $templates_dir . $_name . '.html' )) {
					$var_name = $_name . '_template';
					$$var_name = file_get_contents( $templates_dir . $_name . '.html' );
				}
			}
		}

		$r =& $this->HypKTaiRender;

		// use jquery mobile?
		$use_jquery = $r->Config_jquery;

		$is_rss = false;
		// Is RSS?
		if (preg_match('/<(?:feed.+?<entry|(?:rss|rdf).+?<channel)/isS', substr($s, 0, 1000))) {
			HypCommonFunc::loadClass('HypRss2Html');
			$rh = new HypRss2Html($s);
			$rh->detect_order = $this->detect_order;
			$s = $rh->getHtml();
			//$s = mb_convert_encoding($s, $this->encode, $r->encoding);
			$encode = $rh->encoding;
			$header = '<h1>RSS of ' . $rh->base['TITLE'] . '</h1><a href="'.$rh->base['LINK'].'" data-icon="home" data-iconpos="notext">Home</a>';
			$s = str_replace('<head>', '<head><link href="'.$_SERVER['REQUEST_URI'].'" title="RSS of ' . $rh->base['TITLE'] . '" type="application/rss+xml" rel="alternate" />', $s);
			$is_rss = true;
			//$use_jquery = false;
		}

		// preg_match では、サイズが大きいページで正常処理できないことがあるので。
		$s = str_replace(array('</BODY>', '</HEAD>', '<BODY', '<HEAD'), array('</body>', '</head>', '<body', '<head'), $s);
		$arr1 = explode('<head', $s, 2);
		if (isset($arr1[1]) && strpos($arr1[1], '</head>') !== FALSE) {
			$arr2 = explode('</head>', $arr1[1], 2);
			$head = substr($arr2[0], strpos($arr2[0], '>') + 1);
		}
		$arr1 = explode('<body', $s, 2);
		if (isset($arr1[1]) && strpos($arr1[1], '</body>') !== FALSE) {
			$arr2 = explode('</body>', $arr1[1], 2);
			$body = substr($arr2[0], strpos($arr2[0], '>') + 1);
		}

		if ($head && ! $encode) {
			$encode = HypCommonFunc::get_encoding_by_meta($head, TRUE);
		}
		if (! $encode) $encode = $this->encode;

		if ($body) {
			// 携帯のみ有効にする部分
			$body = str_replace('<!--HypKTaiOnly', '', $body);
			$body = str_replace('HypKTaiOnly-->', '', $body);

			// 無視する部分(<!--HypKTaiIgnore-->...<!--/HypKTaiIgnore-->)を削除
			while(strpos($body, '<!--HypKTaiIgnore-->') !== FALSE) {
				$arr1 = explode('<!--HypKTaiIgnore-->', $body, 2);
				$arr2 = array_pad(explode('<!--/HypKTaiIgnore-->', $arr1[1], 2), 2, '');
				$body = $arr1[0] . $arr2[1];
			}
			// Block を処理
			$bid = isset($_GET[$this->k_tai_conf['getKeys']['block']])? intval($_GET[$this->k_tai_conf['getKeys']['block']]) : 0;
			$_showblocks = $showblocks = $blocks = $submenu = $blockmenu = array();
			$base = '?';
			$querys = isset($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING'] : '';
			if ($querys) {
				$base .= str_replace('&', '&amp;', $querys);
			}
			while(strpos($body, '<!--KTaiBlock_') !== FALSE) {
				$arr1 = explode('<!--KTaiBlock_', $body, 2);
				$arr2 = array_pad(explode('<!--/KTaiBlock-->', $arr1[1], 2), 2, '');
				list($id, $bcontent) = explode('-->', $arr2[0], 2);
				$title = preg_replace('#^.*?<!--KTaiTitle-->(.+?)<!--/KTaiTitle-->.*?$#s', '$1', $bcontent);
				$no_title = false;
				if ($title === $bcontent || ! $title) {
					$title = 'Block No.' . $id;
					$no_title = true;
				}
				if ($use_jquery) {
					$bcontent = preg_replace('#<h[1-6].*?<!--KTaiTitle-->(.+?)<!--/KTaiTitle-->.*?/h[1-6]>#s', '', $bcontent);
					if ($no_title) {
						$body = $arr1[0] . '<div id="ktaiblock'.$id.'">' . $bcontent . '</div>' .  $arr2[1];
					} else {
						if (in_array($id, $this->k_tai_conf['showBlockIds'])) {
							$body = $arr1[0] . '<div id="ktaiblock'.$id.'" data-role="collapsible" data-theme="'.$this->k_tai_conf['jquery_theme_block'].'" data-content-theme="'.$this->k_tai_conf['jquery_theme_block'].'" data-collapsed="false"><h3>'.$title.'</h3>' . $bcontent. '</div>' . $arr2[1];
						} else {
							$body = $arr1[0] . '<div id="ktaiblock'.$id.'" data-role="collapsible" data-theme="'.$this->k_tai_conf['jquery_theme_block'].'" data-content-theme="'.$this->k_tai_conf['jquery_theme_block'].'" data-collapsed="true"><h3>'.$title.'</h3>' . $bcontent. '</div>' . $arr2[1];
						}
						$blockmenu[$id] = '<a href="#ktaiblock'.$id.'" data-ajax="false">' . $title . '</a>';
					}
				} else {
					if ($no_title || in_array($id, $this->k_tai_conf['showBlockIds'])) {
						$body = $arr1[0] . '<div id="ktaiblock'.$id.'">' . $bcontent. '</div>' . $arr2[1];
						if (! $no_title) $showblocks['ktaiblock'.$id] = $title;
					} else {
						$blocks[$id]['content'] = $bcontent;
						if ($bid != $id) {
							$submenu[$id] = '<a href="' . $base . '&amp;'.$this->k_tai_conf['getKeys']['block'].'='.$id.'">' . $title . '</a>';
						} else {
							$submenu[$id] = '<span style="'.$this->k_tai_conf['style']['highlight'].'">' . $title . '</span>';
						}
						$body = $arr1[0] . $arr2[1];
					}
				}
			}
			if (! empty($_showblocks)) {
				$showblocks += $_showblocks;
			}

			if ($submenu) {
				$body .= '<!--subMenu--><ul>';
				foreach($submenu as $sub) {
					$body .= '<li>' . $sub . '</li>';
				}
				$body .= '</ul><!--/subMenu-->';
				$showblocks['submenu'] = $this->k_tai_conf['msg']['subMenu'];
			}
			if ($showblocks) {
				$block_menu = array();
				foreach($showblocks as $id => $title) {
					$block_menu[] = '<a href="#'.$id.'">' . $title . '</a>';
				}
				$body .= '<!--blockMenu-->' . join(' / ', $block_menu) . '<!--/blockMenu-->';
			}
			if ($blockmenu) {
				$_url = XOOPS_URL . '/';
				$blockmenu = join('</li><li>', $blockmenu);
				$body .= <<<EOD
<!--blockMenu-->
<div data-role="header">
 <a href="{$_url}" data-ajax="false" data-icon="home" data-iconpos="notext">Home</a>
 <h4>
  <a id="keitaifixedbar_main" href="#keitaiMainContents" data-ajax="false" style="display:inline;text-decoration:none;"><pagetitle></a>
 </h4>
 <a id="keitaifixedbar_block" href="#" data-ajax="false" data-icon="grid" data-iconpos="notext">block</a>
 <div id="keitaiblockmenu" style="display:none" data-role="header">
  <div data-role="navbar">
   <ul><li>{$blockmenu}</li></ul>
  </div>
 </div>
</div>
<!--/blockMenu-->
EOD;

			}

			if ($rebuilds) {
				$parts = array();
				$rebuild_found = FALSE;

				if (! empty($_SESSION['hyp_redirect_message'])){
					$body = '<!--redirectMessage-->' . $_SESSION['hyp_redirect_message'] . '<!--/redirectMessage-->' . $body;
					unset($_SESSION['hyp_redirect_message']);
				}

				if (isset($_GET[$this->k_tai_conf['getKeys']['block']]) && isset($blocks[$_GET[$this->k_tai_conf['getKeys']['block']]])) {
					$body .= '<!--toMain-->' . $this->k_tai_conf['icon']['toMain'].'<a href="'.$base.'">' . $this->k_tai_conf['msg']['toMain'] . '</a><!--/toMain-->';
					$body .= '<!--blockContent--><ns>' . $blocks[$_GET[$this->k_tai_conf['getKeys']['block']]]['content'] . '</ns><!--/blockContent-->';
				}

				foreach($rebuilds as $id => $var) {
					$qid = preg_quote($id, '#');
					$parts[$id] = '';
					// preg_match では、サイズが大きいページで正常処理できないことがあるので。
					$arr1 = explode('<!--' . $id . '-->', $body, 2);
					if (isset($arr1[1]) && strpos($arr1[1], '<!--/' . $id . '-->') !== FALSE) {
						$arr2 = explode('<!--/' . $id . '-->', $arr1[1], 2);
						$target = $arr2[0];
						//$target = trim(preg_replace('/<!--.+?-->/sS', '', $target));
						if (! $use_jquery) $target = trim(preg_replace('/<!--.+?-->/sS', '', $target));
						if (trim(preg_replace('/<\/?(?:div|span|ns|p)[^>]*?>/S', '', $target))) {
							$parts[$id] = $var['above'] . $target . $var['below'];
							if ($id !== 'redirectMessage') {
								$rebuild_found = TRUE;
							} else {
								if ($rebuild_found) {
									$target = strip_tags($target);
								}
							}
							$parts[$id] = $var['above'] . $target . $var['below'];
						}
					}
				}

				if ($rebuild_found) {
					if (isset($_GET[$this->k_tai_conf['getKeys']['block']]) && isset($blocks[$_GET[$this->k_tai_conf['getKeys']['block']]])) {
						if (empty($parts['content'])) {
							$parts['toMain'] = '';
						}
						//$parts['content'] = $blocks[$_GET[$this->k_tai_conf['getKeys']['block']]]['content'];
						$parts['content'] = '';
					}

					if ($use_jquery && !empty($parts['content'])) {
						$parts['content'] = '<div id="keitaiMainContents" data-role="collapsible" data-theme="'.$this->k_tai_conf['jquery_theme_content'].'" data-content-theme="'.$this->k_tai_conf['jquery_theme_content'].'" data-collapsed="false">' . $parts['content'] . '</div>';
					}

					// Easy login
					if (! empty($this->k_tai_conf['easyLogin'])) {
						$to_pc = '<li><a href="#" onclick="return jQuery.keitaiSwitchToPc();">PC</a></li>';
						if (! empty($r->vars['ua']['isGuest'])) {
							$add = '_EASYLOGIN';
							if ($r->vars['ua']['carrier'] === 'docomo') {
								$add .= '&guid=on';
							}
							//$url = $r->myRoot . $r->removeSID($r->SERVER['REQUEST_URI']);
							$url = $r->myRoot . $r->removeQueryFromUrl($r->SERVER['REQUEST_URI'], array('guid', $r->session_name));
							$url .= ((strpos($url, '?') === FALSE)? '?' : '&') . $add;
							$url = str_replace('&', '&amp;', $url);
							if ($use_jquery) {
								$easylogin = '<ul><li><a href="' . $url . '">' . $this->k_tai_conf['msg']['easylogin'] . '</a></li>'.$to_pc.'</ul>';
							} else {
								$easylogin = '<a href="' . $url . '">' . $this->k_tai_conf['msg']['easylogin'] . '</a>';
							}
						} else {
							$uname = '';
							if (empty($_SESSION['hyp_redirect_uname'])) {
								$member_handler =& xoops_gethandler('member');
								$xoopsUser =& $member_handler->getUser($this->HypKTaiRender->vars['ua']['xoopsUid']);
								$uname = $xoopsUser->getVar('uname');
							} else {
								$uname = $_SESSION['hyp_redirect_uname'];
								unset($_SESSION['hyp_redirect_uname']);
							}
							if ($uname) {
								$uname = htmlspecialchars($uname);
								$guid = ($r->vars['ua']['carrier'] === 'docomo')? '&amp;guid=on' : '';
								$uname = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $this->HypKTaiRender->vars['ua']['xoopsUid'] . $guid . '">' . $uname . '</a>';
							}
							if ($use_jquery) {
								$easylogin = '<ul><li>' . $uname . '</li><li><a href="' . XOOPS_URL . '/user.php?op=logout">' . $this->k_tai_conf['msg']['logout'] . '</a></li>'.$to_pc.'</ul>';
							} else {
								$easylogin = $uname . ' <a href="' . XOOPS_URL . '/user.php?op=logout">' . $this->k_tai_conf['msg']['logout'] . '</a>';
							}

							// 簡単ログイン:設定 or 解除
							if (isset($this->k_tai_conf['easyLoginConfPath']) && isset($this->k_tai_conf['easyLoginConfuid'])) {
								$purl = parse_url(XOOPS_URL);
								$nowpath = $r->SERVER['PHP_SELF'];
								if (isset($purl['path'])) {
									$nowpath = preg_replace('#^' . $purl['path'] . '#', '', $nowpath);
								}
								if (strpos($nowpath, $this->k_tai_conf['easyLoginConfPath']) === 0 && $this->HypKTaiRender->vars['ua']['xoopsUid'] == @ $_GET[$this->k_tai_conf['easyLoginConfuid']]) {

									$uaUid = md5($r->vars['ua']['uid'] . XOOPS_DB_PASS);

									// Read easy login data file
									$myroot = str_replace('/', '_', preg_replace('#https?://#i', '', XOOPS_URL));
									$datfile = XOOPS_TRUST_PATH . '/uploads/hyp_common/' . $myroot . '_easylogin.dat';
									if (is_file($datfile)) {
										$uids = unserialize(HypCommonFunc::flock_get_contents($datfile));
									} else {
										$uids = array();
									}

									if (isset($uids[$uaUid])) {
										$add = '_EASYLOGINUNSET';
										$msg = 'easyloginUnset';
									} else {
										$add = '_EASYLOGINSET';
										$msg = 'easyloginSet';
									}

									if ($r->vars['ua']['carrier'] === 'docomo') {
										$add .= '&guid=on';
									}
									$url = $r->myRoot . $r->removeQueryFromUrl($r->SERVER['REQUEST_URI'], array('guid', '_EASYLOGINUNSET', '_EASYLOGINSET'));
									$url .= ((strpos($url, '?') === FALSE)? '?' : '&') . $add;
									$url = str_replace('&', '&amp;', $url);
									$parts[$this->k_tai_conf['easyLoginConfInsert']] = '<hr /><div style="text-align:center">[<a href="' . $url . '">' . $this->k_tai_conf['msg'][$msg] . '</a>]</div>' . @ $parts[$this->k_tai_conf['easyLoginConfInsert']];
								}
							}
						}
						$parts['easylogin'] = $rebuilds['easylogin']['above'] . $easylogin . $rebuilds['easylogin']['below'];
					}

					foreach(array_keys($rebuilds) as $id) {
						$header_template = str_replace('<' . $id . '>', $parts[$id], $header_template);
						$body_template = str_replace('<' . $id . '>', $parts[$id], $body_template);
						$footer_template = str_replace('<' . $id . '>', $parts[$id], $footer_template);
					}

					if ($header_template) $header = $header_template;
					if ($body_template) $body = $body_template;
					if ($footer_template) $footer = $footer_template;
				} elseif ($use_jquery && ! $is_rss && strpos($head, '<!--jqm_theme') === false) {
					return false;
				}
			}
		} else {
			return $s;
		}

		if ($head) {
			// Redirect
			if (preg_match('#<meta[^>]+http-equiv=("|\')Refresh\\1[^>]+content=("|\')[\d]+;\s*url=(.+)\\2[^>]*>#iUS', $head, $match)) {
				//$url = str_replace('&amp;', '&', $match[3]);
				$url = strtr(str_replace('&amp;', '&', $match[3]), "\r\n\0", "   ");
				if ($body) {
					$body = preg_replace('#<p>.*?<a[^>]*?href="'.preg_quote($match[3], '#').'.*?</p>#', '', $body);
					$_SESSION['hyp_redirect_message'] = strip_tags($body);
				}
				$url = $r->getRealUrl($url);
				$url = $r->addSID($url, XOOPS_URL);
				header('Location: ' .$url);
				return '';
			}

			// <head>
			$_head = '<head>';
			if (preg_match('#<title[^>]*>(.*)</title>#isUS', $head, $match)) {
				$pagetitle = $match[1];
				$_head .= mb_convert_encoding($match[0], ($use_jquery? $encode : 'SJIS-win'), $encode);
			}
			if (isset($r->vars['ua']['meta'])) {
				$_head .= $r->vars['ua']['meta'];
			}

			// Check RSS & CSS
			$_css_type = ($use_jquery && $this->k_tai_conf['jquery_no_reduce'])? 'all|screen|handheld' : 'handheld';
			$rss = array();
			$jquery_script = array();

			if (preg_match_all('#<link([^>]+?)>#iS', $head, $match)) {
				foreach($match[1] as $key => $attrs) {
					if (preg_match('#type=("|\')application/(?:atom|rss)\+xml\\1#iS', $attrs)) {
						if (preg_match('#href=("|\')([^ <>"\']+)\\1#is', $attrs, $match2)) {
							$title = 'RSS';
							$url = $match2[2];
							if (preg_match('#title=("|\')([^<>"\']+)\\1#isS', $attrs, $match3)) {
								$title = $match3[2];
							}
							if (! $is_rss) $rss[] = '<a href="'.$url.'" data-ajax="true">'.$title.'</a>';
							if ($use_jquery) $_head .= $match[0][$key];
						}
					} else if (preg_match('#rel=("|\')stylesheet\\1#iS', $attrs)) {
						if (preg_match('# media=("|\')[a-z, ]*\b(?:'.$_css_type.'|'.$r->vars['ua']['carrier'].')\b[a-z, ]*\\1#iS', $attrs)) {
							$_head .= '<link' . preg_replace('# media=("|\')[^"\']*?\\1#iS', '', $attrs) . '>';
						}
					}
				}
			}
			if (preg_match_all('#<script(.+?)/script>\r?\n?#isS', $head, $match)) {
				foreach($match[1] as $i => $attrs) {
					if (preg_match('#jquery\.#iS', $attrs)) {
						$jquery_script[] = $match[0][$i];
						$head = str_replace($match[0][$i], '', $head);
					}
				}
			}
			if ($rss) {
				if ($use_jquery && count($rss) > 1) {
					$body = '<div data-role="collapsible" data-theme="'.$this->k_tai_conf['jquery_theme_block'].'" data-content-theme="'.$this->k_tai_conf['jquery_theme_block'].'" data-collapsed="true"><h4>RSS Links</h4>' . $r->Config_icons['RSS'] . join('<br />' . $r->Config_icons['RSS'], $rss) . '</div>' . $body;
				} else {
					$body = '<div style="font-size:0.9em">' . $r->Config_icons['RSS'] . join('<br />' . $r->Config_icons['RSS'], $rss) . '</div>' . $body;
				}
			}

			if ($use_jquery) {
				$_head .= '<link href="'.XOOPS_THEME_URL.'/'.$this->k_tai_conf['themeSet'].'/jquery.mobile.min.css" rel="stylesheet" type="text/css" />';
				if ($this->k_tai_conf['jqm_css']) {
					$_head .= '<link href="'.$this->k_tai_conf['jqm_css'].'" rel="stylesheet" type="text/css" />';
				}
				if (! $rebuild_found) {
					$_head .= '<link href="'.XOOPS_THEME_URL.'/'.$this->k_tai_conf['themeSet'].'/smart.css" rel="stylesheet" type="text/css" />';
				}
				if ($this->k_tai_conf['jquery_no_reduce']) {
					$_head .= preg_replace('#<link([^>]+?)>\r?\n?|<title.+?/title>\r?\n?#iS', '', $head);
				}
				$_head .= '<script type="text/javascript" src="'.XOOPS_THEME_URL.'/'.$this->k_tai_conf['themeSet'].'/jquery.min.js"></script>';
				$_head .= '<script type="text/javascript" src="'.XOOPS_THEME_URL.'/'.$this->k_tai_conf['themeSet'].'/jquery.mobile-config.js"></script>';
				$_head .= '<script type="text/javascript" src="'.XOOPS_THEME_URL.'/'.$this->k_tai_conf['themeSet'].'/jquery.mobile.min.js"></script>';
				$_head .= join('', $jquery_script);
				$_head .= '<script type="text/javascript" src="'.XOOPS_THEME_URL.'/'.$this->k_tai_conf['themeSet'].'/jquery.extra.js"></script>';
				if (preg_match('/<!--jqm_theme_([a-z])/', $head, $_match)) {
					$this->k_tai_conf['jquery_theme'] = $_match[1];
					$this->k_tai_conf['rebuilds']['header']['above'] = preg_replace('/data-theme="[a-z]"/', 'data-theme="' . $this->k_tai_conf['jquery_theme'] . '"', $this->k_tai_conf['rebuilds']['header']['above']);
					$this->k_tai_conf['rebuilds']['body']['above']   = preg_replace('/data-theme="[a-z]"/', 'data-theme="' . $this->k_tai_conf['jquery_theme'] . '"', $this->k_tai_conf['rebuilds']['body']['above']);
					$this->k_tai_conf['rebuilds']['footer']['above'] = preg_replace('/data-theme="[a-z]"/', 'data-theme="' . $this->k_tai_conf['jquery_theme'] . '"', $this->k_tai_conf['rebuilds']['footer']['above']);
				}
			}

			$_head .= '</head>';
			$head = $_head;
		}

		// Remove  xoopsCode buttons & Smilies buttons.
		if (strpos($body, '<div id="message_bbcode_buttons_pre"') !== FALSE) {
			$body = preg_replace('#<div id="message_bbcode_buttons_pre".+?/div>#sS', '', $body);
			$body = preg_replace('#<div id="message_bbcode_buttons_post".+?/div>#sS', '', $body);
			$body = preg_replace('#<input type="checkbox" id="message_bbcode_onoff".+?<br />#sS', '', $body);
			$body = preg_replace('#<input type="checkbox" id="d3f_post_advanced_options_onoff".+?>#sS', '', $body);
		}
		if (strpos($body, '<a name=\'moresmiley\'>') !== FALSE) {
			$body = preg_replace('#<a name=\'moresmiley\'>.+?<textarea#sS', '<textarea', $body);
			$body = preg_replace('#(?:<img |<a href="\#" )onclick=\'xoopsCodeSmilie\(.+?</a>\]#sS', '', $body);
		}

		if ($r->vars['ua']['carrier'] === 'docomo') {
			$body = preg_replace('/<form[^>]+?user\.php[^>]+?>/isS', '$0<input type="hidden" name="guid" value="ON">', $body);
		}

		if ($this->k_tai_conf['googleAnalyticsId']) {
			$header .= $r->googleAnalyticsGetImgTag($this->k_tai_conf['googleAnalyticsId'], $pagetitle);
		}

		$header = $this->k_tai_conf['rebuilds']['header']['above'] . $header . $this->k_tai_conf['rebuilds']['header']['below'];
		$body = $this->k_tai_conf['rebuilds']['body']['above'] . $body . $this->k_tai_conf['rebuilds']['body']['below'];
		$footer = $this->k_tai_conf['rebuilds']['footer']['above'] . $footer . $this->k_tai_conf['rebuilds']['footer']['below'];

		if ($use_jquery) {
			$header .= '<separator>';
			$body .= '<separator>';
			if ($this->k_tai_conf['jquery_no_reduce']) {
				$r->Config_no_diet = true;
			}
		}

		$r->contents['header'] = $header;
		$r->contents['body'] = $body;
		$r->contents['footer'] = $footer;

		$r->inputEncode = $encode;
		$r->outputEncode = $use_jquery? $encode : 'SJIS';
		$r->outputMode = $use_jquery? 'html5' : 'xhtml';
		$r->langcode = _LANGCODE;

		$r->doOptimize();

		$charset = (strtoupper($r->outputEncode) === 'SJIS')? 'Shift_JIS' : $encode;

		// Set <body> attribute
		$bodyAttr = ($this->k_tai_conf['bodyAttribute'])? ' ' . trim($this->k_tai_conf['bodyAttribute']) : '';
		if (! empty($r->vars['ua']['bodyAttribute'])) {
			$bodyAttr = ' ' . trim($r->vars['ua']['bodyAttribute']);
		}

		$outBody = $r->outputBody;

		$outBody = str_replace('<pagetitle>', $pagetitle, $outBody);
		if ($use_jquery) {
			$_array = explode('<separator>', $outBody);
			$outBody  = '<div data-role="page" data-theme="'.$this->k_tai_conf['jquery_theme'].'">';
			$outBody .= $_array[0];
			$outBody .= $_array[1];
			$outBody .= $_array[2];
			$outBody .= '</div>';
		}

		$s = $r->getHtmlDeclaration() . $head . '<body' . $bodyAttr . '>' . $outBody . '</body></html>';

		$ctype = $r->getOutputContentType();

		$r = NULL;
		unset($r);

		header('Content-Type: ' . $ctype . '; charset=' . $charset);
		header('Content-Length: ' . strlen($s));
		header('Cache-Control: no-cache');

		return $s;
	}

	function emojiFilter ($str) {

		if ($str === '' || strpos($str, '<html') === FALSE) return false;

		if (preg_match('/\(\([eisv]:[0-9a-f]{4}\)\)|\[emj:\d{1,4}(?::(?:im|ez|sb))?\]/S', $str)) {
			if (! XC_CLASS_EXISTS('MobilePictogramConverter')) {
				HypCommonFunc::loadClass('MobilePictogramConverter');
			}
			$mpc =& MobilePictogramConverter::factory_common();
			$mpc->setImagePath(XOOPS_URL . '/images/emoji');
			$mpc->setString($str, FALSE);
			$str = $mpc->autoConvertModKtai();
		}

		return $str;
	}

	function utf8Filter($str) {
		if (strpos($str, '<html') !== FALSE) {
			$str = preg_replace('/^(<\?xml[^>]+?encoding=["\'])[a-z0-9_-]+/i', '$1UTF-8', $str);
			$str = preg_replace('/(<meta[^>]+?http-equiv=["\']content-type["\'][^>]+?charset=)[a-z0-9_-]+/i', '$1UTF-8', $str);

			$str = mb_convert_encoding($str, 'UTF-8', $this->encode);
			header('Content-Type: text/html; charset=UTF-8');
			return $str;
		} else {
			return false;
		}
	}

	function sendMail ($spamlev) {

		global $xoopsUser;
		$info = array();

		$info['TIME'] = date('r', time());
		if (is_object($xoopsUser)) {
			$info['UID'] = (int)$xoopsUser->uid();
			$info['UNAME'] = $xoopsUser->uname();
		} else {
			$info['UID'] = 0;
			$info['UNAME'] = 'Guest';
		}
		$info['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
		$info['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
		$info['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
		$info['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
		$info['SPAM LEVEL'] = $spamlev;

		$_info = '';
		foreach($info as $key => $value)
			$_info .= $key . ': ' . $value . "\n";

		$_info .= str_repeat('-', 30) . "\n";

		$post = $_POST;
		// Key:excerpt があればトラックかも->文字コード変換
		if (isset($post['excerpt']) && function_exists('mb_convert_variables')) {
			if (isset($post['charset']) && $post['charset'] != '') {
				// TrackBack Ping で指定されていることがある
				// うまくいかない場合は自動検出に切り替え
				if (mb_convert_variables($this->encode,
				    $post['charset'], $post) !== $post['charset']) {
					mb_convert_variables($this->encode, 'auto', $post);
				}
			} else if (! empty($post)) {
				// 全部まとめて、自動検出／変換
				mb_convert_variables($this->encode, 'auto', $post);
			}
		}

		$message = $_info . '$_POST :' . "\n" . print_r($post, TRUE);
		$message .= "\n" . str_repeat('=', 30) . "\n\n";

		if ($this->send_mail_interval) {
			$mail_tmp = XOOPS_TRUST_PATH . '/uploads/hyp_common/' . str_replace('/', '_', preg_replace('#https?://#i', '', XOOPS_URL)) . '.SPAM.hyp';
			if (! file_exists($mail_tmp)) {
				HypCommonFunc::flock_put_contents($mail_tmp, $message);
				return;
			} else {
				$mtime = filemtime($mail_tmp);
				if ($mtime + $this->send_mail_interval * 60 > time()) {
					if (HypCommonFunc::flock_put_contents($mail_tmp, $message, 'ab')) {
						HypCommonFunc::touch($mail_tmp, $mtime);
					}
					return;
				} else {
					$message = HypCommonFunc::flock_get_contents($mail_tmp) . $message;
					unlink($mail_tmp);
				}
			}
		}

		$config_handler =& xoops_gethandler('config');
		$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);

		$subject = '[' . $xoopsConfig['sitename'] . '] POST Spam Report';

		$xoopsMailer =& getMailer();
		$xoopsMailer->useMail();
		$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
		$xoopsMailer->setFromName($xoopsConfig['sitename']);
		$xoopsMailer->setSubject($subject);
		$xoopsMailer->setBody($message);
		$xoopsMailer->setToEmails($xoopsConfig['adminmail']);
		$xoopsMailer->send();
		$xoopsMailer->reset();

	}

	function googleAnalyticsFilter ($str) {
		if (strpos($str, 'var pageTracker = _gat._getTracker(') !== FALSE) {
			if (preg_match('#<script.+?_gat\._getTracker\("([^"]+?)"\).+?</script>#sS', $str, $match)) {
				$img = $this->HypKTaiRender->googleAnalyticsGetImgTag($match[1]);
				$str = preg_replace('#<script.+?_gat\._getTracker\("([^"]+?)"\).+?</script>#sS', '$0<noscript>'.$img.'</noscript>', $str);
			}
		}
		return $str;
	}

	function switchOfSmartPhone($str) {
		if ($str === '' || strpos($str, '<html') === FALSE) return false;

		$uri = $_SERVER['REQUEST_URI'];
		$uri .= ((strpos($uri, '?') === false)? '?' : '&') . '_hypktaipc=0';
		$htag = '<link href="'.XOOPS_THEME_URL.'/'.$this->k_tai_conf['themeSet'].'/keitaiswitch.css" rel="stylesheet" type="text/css" />' . "\n";
		$htag .= <<<EOD
<script type="text/javascript">
	function hypKtaiSwitchToSmart() {
		var expires = new Date();
		expires.setDate(expires.getDate() - 1);
		document.cookie = "_hypktaipc=0;expires=" + expires.toUTCString() + ";path=/";
		if (location.href.match('_hypktaipc=1')) {
			location.href = location.href.replace(/[?&]_hypktaipc=1/, '');
		} else {
			location.reload(true);
		}
		return false;
	}
</script>
EOD;
		$sw = '<div><a href="'.$uri.'" class="ktai_smart_btn" onclick="return hypKtaiSwitchToSmart();">'.$this->k_tai_conf['msg']['switchSmart'].'</a></div>';
		list($head, $body) = explode('</head>', $str);
		$head .= $htag . '</head>';
		$body = preg_replace('/<body[^>]*?>/S', '$0' . $sw, $body, 1);
		$str = $head . $body;

		return $str;
	}

	function & getKtaiRenderObject() {
		return $this->HypKTaiRender;
	}
}

if (is_file(XOOPS_ROOT_PATH.'/class/hyp_common/hyp_preload.conf.php')) {
	include_once(XOOPS_ROOT_PATH.'/class/hyp_common/hyp_preload.conf.php');
} else if (is_file(dirname(__FILE__).'/hyp_preload.conf.php')) {
	include_once(dirname(__FILE__).'/hyp_preload.conf.php');
}

// 以下は "hyp_preload.conf.php" がない場合に読み込まれます。
// 設定を変更する場合は、"hyp_preload.conf.php" で行ってください。
if (! XC_CLASS_EXISTS('HypCommonPreLoad')) {
class HypCommonPreLoad extends HypCommonPreLoadBase {

	function HypCommonPreLoad (& $controller) {

		// 各機能のメインスイッチ (On = 1, Off = 0)
		$this->use_set_query_words   = 0; // 検索ワードを定数にセット
		$this->use_words_highlight   = 0; // 検索ワードをハイライト表示
		$this->use_proxy_check       = 0; // POST時プロキシチェックする
		$this->use_dependence_filter = 0; // 機種依存文字フィルター
		$this->use_post_spam_filter  = 0; // POST SPAM フィルター
		$this->post_spam_trap_set    = 0; // 無効フィールドのBot罠を自動で仕掛ける
		$this->use_k_tai_render      = 0; // 携帯対応レンダーを有効にする
		$this->use_smart_redirect    = 0; // スマートリダイレクトを有効にする

		// 各種設定
		$this->configEncoding = 'EUC-JP'; // このファイルの文字コード

		$this->encodehint_word = 'ぷ';    // POSTエンコーディング判定用文字
		$this->encodehint_name = 'HypEncHint'; // POSTエンコーディング判定用 Filed name
		$this->detect_order = 'ASCII, JIS, UTF-8, eucJP-win, EUC-JP, SJIS-win, SJIS';

		$this->msg_words_highlight = 'これらのキーワードがハイライトされています';

		$this->no_proxy_check  = '/^(127\.0\.0\.1|192\.168\.1\.)/'; // 除外IP
		$this->msg_proxy_check = 'Can not post from public proxy.';

		// Input filter 制御文字の除去
		// 0: null 以外許可, 1: SoftBankの絵文字と\t,\r,\n は許可, 2: \t,\r,\n のみ許可
		$this->input_filter_strength = 0;

		// POST SPAM
		$this->use_mail_notify    = 1;    // POST SPAM メール通知 0:なし, 1:SPAM判定のみ, 2:すべて
		$this->send_mail_interval = 60;   // まとめ送りのインターバル(分) (0 で随時送信)
		$this->post_spam_a   = 1;         // <a> タグ 1個あたりのポイント
		$this->post_spam_bb  = 1;         // BBリンク 1個あたりのポイント
		$this->post_spam_url = 1;         // URL      1個あたりのポイント
		$this->post_spam_unhost= 5;       // 不明 HOST の加算ポイント
		$this->post_spam_host  = 31;      // Spam HOST の加算ポイント
		$this->post_spam_word  = 10;      // Spam Word の加算ポイント
		$this->post_spam_filed = 200;     // Spam 無効フィールドの加算ポイント
		$this->post_spam_trap  = '___url';// Spam 罠用無効フィールド名

		$this->post_spam_user  = 150;     // POST SPAM 閾値: ログインユーザー
		$this->post_spam_guest = 15;      // POST SPAM 閾値: ゲスト
		$this->post_spam_badip = 100;     // アクセス拒否リストへ登録する閾値

		// 処理をパスするフォームフィールド名 (,<カンマ> 区切り)
		// reference_quote : d3forum
		// msg_before,msg_after : PukiWikiMod
		$this->post_spam_pass_names = 'reference_quote,msg_before,msg_after';

		// Protector 併用設定 (Protector の拒否IP登録の保護グループ設定も有効)
		$this->post_spam_badip_ttl     = 900;     // アクセス拒否の拒否継続時間[Sec](0:無期限,null:Protector不使用)
		$this->post_spam_badip_forever = 200;     // 無期限アクセス拒否閾値
		$this->post_spam_badip_ttl0    = 2592000; // 無期限アクセス拒否継続時間[Sec](0:本当に無期限)

		// Proxy Checkers
		$this->post_spam_checkers = array(
			//'list.dsbl.org',
			'niku.2ch.net',
			array(
				'dnsbl.spam-champuru.livedoor.com',
				'/^192\.168\.1\.2/'
			),
		);

		// POST SPAM のポイント加算設定
		$this->post_spam_rules = array(
			// 同じURLが1行に3回 11pt
			"/((?:ht|f)tps?:\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+).+?\\1.+?\\1/i" => 11,

			// 65文字以上の英数文字のみで構成されている 15pt
			// '/^[\x00-\x7f\s]{65,}$/' => 15,

			// 無効な文字コードがある 31pt
			'/[\x01-\x08\x0b-\x0c\x0e\x10-\x1a\x1c-\x1f\x7f]+/' => 31,

			// よくあるSPAM 15pt
			'/^\s*(?:Hi|Aloha)! (?:<a[^>]+?href=|\[url=|http:\/\/)/i' => 15,
		);

		// 無効なフィールド定義
		$this->ignore_fileds = array(
			// 'url' => array('newbb/post.php', 'comment_post.php'),
		);

		// 検索ワード定数名
		$this->q_word  = 'XOOPS_QUERY_WORD';         // 検索ワード
		$this->q_word2 = 'XOOPS_QUERY_WORD2';        // 検索ワード分かち書き(分かち書き不使用なら空文字''で設定)
		$this->se_name = 'XOOPS_SEARCH_ENGINE_NAME'; // 検索元名

		// 外部リンクに付加する class属性値
		// use_words_highlight = 1 の場合に有効
		// 空値指定で class属性の付加なし
		$this->extlink_class_name = 'ext';

		// KAKASI での分かち書き結果のキャッシュ先
		$this->kakasi_cache_dir = XOOPS_TRUST_PATH.'/uploads/hyp_common/kakasi/';

		// スマートリダイレクトのポップアップ最短秒数
		$this->smart_redirect_min_sec = 5;

		// 定数 "HYP_IS_BOT_UA" をセットする UserAgant PCRE 正規表現
		$this->bot_ua_reg = '/bot|Slurp|Crawler|Sidewinder|spider|Y!J|Ask/i';

		/////////////////////////
		// 携帯対応レンダー設定

		// 携帯端末判定用 UA 正規表現
		$this->k_tai_conf['ua_regex'] = '#(?:Android|Windows Phone|SoftBank|Vodafone|J-PHONE|DoCoMo|UP\.Browser|DDIPOCKET|WILLCOM|iPhone|iPod|mixi-mobile-converter|Googlebot-Mobile|Google Wireless Transcoder|Hatena-Mobile-Gateway)#';

		// jQuery mobile を使用するプロファイル
		$this->k_tai_conf['jquery_profiles'] = 'android,iphone,ipod,windows phone';

		// jQuery mobile のテーマ
		// ページ
		$this->k_tai_conf['jquery_theme'] = 'b';
		// メインコンテンツ
		$this->k_tai_conf['jquery_theme_content'] = 'd';
		// ブロックコンテンツ
		$this->k_tai_conf['jquery_theme_block'] = 'c';

		// jQuery 使用時はHTMLの携帯用変換を行わない
		$this->k_tai_conf['jquery_no_reduce'] = true;
		// jQuery Mobile 適用時に Flash を除去するプロファイル名をカンマ区切りで記述。
		$this->k_tai_conf['jquery_remove_flash'] = ''; // 'iphone,ipod,ipad'
		// jQuery Mobile 適用時に入れ子になっているテーブルを展開する。
		$this->k_tai_conf['jquery_resolve_table'] = false;
		// jQuery Mobile 適用時に画像を指定幅[px]サイズまで縮小する。「0」で無効。
		$this->k_tai_conf['jquery_image_convert'] = 0;

		// HTML再構築用タグ設定
		$this->k_tai_conf['rebuilds'] = array(
			'header'         => array( 'above' => '',
			                          'below' => ''),
			'body'           => array( 'above' => '',
			                          'below' => ''),
			'footer'         => array( 'above' => '',
			                          'below' => ''),
			'headerlogo'     => array( 'above' => '<center>',
			                          'below' => '</center>'),
			'headerbar'      => array( 'above' => '<hr>',
			                          'below' => ''),
			'breadcrumbs'    => array( 'above' => '',
			                          'below' => ''),
			'leftcolumn'     => array( 'above' => '<hr>',
			                          'below' => ''),
			'centerCcolumn'  => array( 'above' => '<hr>',
			                          'below' => ''),
			'centerLcolumn'  => array( 'above' => '',
			                          'below' => ''),
			'centerRcolumn'  => array( 'above' => '',
			                          'below' => ''),
			'content'        => array( 'above' => '<hr>',
			                          'below' => ''),
			'rightcolumn'    => array( 'above' => '<hr>',
			                          'below' => ''),
			'footerbar'      => array( 'above' => '',
			                          'below' => ''),
			'easylogin'      => array( 'above' => '<div style="text-align:center;background-color:#DBBCA6;font-size:small">[ ',
			                          'below' => ' ]</div>'),
			'redirectMessage'=> array( 'above' => '<marquee loop="3">',
			                          'below' => '</marquee>'),
			'blockMenu'      => array( 'above' => '<div style="background-color:#E0EEEE;font-size:small">',
			                          'below' => '</div>'),
			'blockContent'   => array( 'above' => '',
			                          'below' => ''),
			'toMain'         => array( 'above' => '<hr /><div style="text-align:center">',
			                          'below' => '</div>'),
			'subMenu'        => array( 'above' => '<div id="submenu" style="background-color:#ccccff"><h2 style="text-align:center">サブメニュー</h2></div>',
			                          'below' => ''),
		);
		// jQuery Mobile 上書き用
		$this->k_tai_conf['rebuildsEx']['jqm'] = array(
			'header'         => array( 'above' => '<div data-role="header" data-theme="'.$this->k_tai_conf['jquery_theme'].'">',
			                          'below' => '</div>'),
			'body'           => array( 'above' => '<div data-role="content" id="keitaiContents" data-theme="'.$this->k_tai_conf['jquery_theme'].'">',
			                          'below' => '</div>'),
			'footer'         => array( 'above' => '<div data-role="footer" data-theme="'.$this->k_tai_conf['jquery_theme'].'">',
			                          'below' => '</div>'),
			'easylogin'      => array( 'above' => '',
			                          'below' => ''),
			'blockMenu'      => array( 'above' => '<div data-role="header" style="line-height:1">',
			                          'below' => '</div>'),
		);

		// 携帯用XOOPSテーマセット
		$this->k_tai_conf['themeSet'] = 'ktai_default';
		$this->k_tai_conf['themeSets'] = array();
		//$this->k_tai_conf['themeSets']['jqm'] = ''; // jQuery mobile 一括
		// carrier 別の設定 (carrier をキーにして設定)
		//$this->k_tai_conf['themeSets']['android'] = '';
		//$this->k_tai_conf['themeSets']['iphone'] = '';
		//$this->k_tai_conf['themeSets']['ipod'] = '';
		//$this->k_tai_conf['themeSets']['windows phone'] = '';

		// 携帯用XOOPSテンプレートセット
		$this->k_tai_conf['templateSet'] = '';
		$this->k_tai_conf['templateSets'] = array();
		//$this->k_tai_conf['templateSets']['jqm'] = ''; // jQuery mobile 一括
		// carrier 別の設定 (carrier をキーにして設定)
		//$this->k_tai_conf['templateSets']['android'] = '';
		//$this->k_tai_conf['templateSets']['iphone'] = '';
		//$this->k_tai_conf['templateSets']['ipod'] = '';
		//$this->k_tai_conf['templateSets']['windows phone'] = '';

		// 使用テンプレート
		$this->k_tai_conf['template'] = 'default';
		$this->k_tai_conf['templates']['jqm'] = 'smart'; // jQuery mobile 用

		// <body> attributes
		$this->k_tai_conf['bodyAttribute'] = '';

		// 無効にするブロックの bid (Block Id) (無指定:フィルタリングしない)
		$this->k_tai_conf['disabledBlockIds'] = array();

		// 有効にするブロックの bid (Block Id) (無指定:フィルタリングしない)
		$this->k_tai_conf['limitedBlockIds'] = array();

		// 常に表示するブロックの bid (Block Id) (メインメニューなど)
		$this->k_tai_conf['showBlockIds'] = array();

		// インラインイメージのリサイズ最大ピクセル
		$this->k_tai_conf['pictSizeMax'] = '200';

		// インラインイメージを表示するホスト名(後方一致)
		$this->k_tai_conf['showImgHosts'] = array('amazon.com', 'yimg.jp', 'yimg.com', 'google.com');

		// 直接画像を表示するホスト名(後方一致)
		$this->k_tai_conf['directImgHosts'] = array('google-analytics.com', 'maps.google.com', 'ad.jp.ap.valuecommerce.com', 'ba.afl.rakuten.co.jp', 'assoc-amazon.jp', 'ad.linksynergy.com');

		// リダイレクトスクリプトを経由しないホスト名(後方一致)
		$this->k_tai_conf['directLinkHosts'] = array('amazon.co.jp', 'ck.jp.ap.valuecommerce.com', 'afl.rakuten.co.jp', 'maps.google.com');

		// 外部リンク用リダイレクトスクリプト
		$this->k_tai_conf['redirect'] = XOOPS_URL . '/class/hyp_common/gate.php?way=redirect&amp;_d=0&amp;_u=0&amp;_x=0&amp;l=';

		// Easy login を有効にする
		$this->k_tai_conf['easyLogin'] = 1;
		// Easy login で IP アドレス帯域をチェックしない
		$this->k_tai_conf['noCheckIpRange'] = 0;
		// docomo の端末IDを確認する間隔(秒)
		$this->k_tai_conf['docomoGuidTTL'] = 300;

		// リンクメッセージ
		$this->k_tai_conf['msg']['easylogin'] = '簡単ログイン';
		$this->k_tai_conf['msg']['logout'] = 'ログアウト';
		$this->k_tai_conf['msg']['easyloginSet'] = '簡単ログイン:設定';
		$this->k_tai_conf['msg']['easyloginUnset'] = '簡単ログイン:解除';
		$this->k_tai_conf['msg']['toMain'] = '本文を表示';
		$this->k_tai_conf['msg']['mainMenu'] = 'メインメニュー';
		$this->k_tai_conf['msg']['subMenu'] = 'サブメニュー';
		$this->k_tai_conf['msg']['switchSmart'] = 'スマホスタイルへ';

		// アイコン
		$this->k_tai_conf['icon']['first']   = '((s:465d))';
		$this->k_tai_conf['icon']['prev']    = '((s:465b))';
		$this->k_tai_conf['icon']['next']    = '((s:465a))';
		$this->k_tai_conf['icon']['last']    = '((s:465c))';
		$this->k_tai_conf['icon']['extLink'] = '((i:f8d9))';
		$this->k_tai_conf['icon']['hTag']    = '((i:f8e4))';
		$this->k_tai_conf['icon']['RSS']     = '((e:f699))';

		$this->k_tai_conf['icon']['toMain']  = '((e:f7e4))';

		// style
		$this->k_tai_conf['style']['highlight'] = 'background-color:#ffc0cb';

		// Easy login: 設定 or 解除リンクを表示するURI(XOOPS_URL以降)とuidのGETキーと挿入位置
		$this->k_tai_conf['easyLoginConfPath'] = '/userinfo.php';
		$this->k_tai_conf['easyLoginConfuid'] = 'uid';
		$this->k_tai_conf['easyLoginConfInsert'] = 'content';

		// GET query keys
		$this->k_tai_conf['getKeys']['page'] = '_p_';
		$this->k_tai_conf['getKeys']['hash'] = '_h_';
		$this->k_tai_conf['getKeys']['block'] = '_b_';

		//// Google Adsense 設定
		// config ファイルのパス
		$this->k_tai_conf['googleAdsense']['config'] = '';
		// 挿入場所 ('header', 'body', 'footer') の下、無指定時はページ最上部
		$this->k_tai_conf['googleAdsense']['below'] = '';

		// Google Analytics 設定
		$this->k_tai_conf['googleAnalyticsId'] = '';

		// <a> タグの href 属性の書き換えルール
		//$this->k_tai_conf['urlRewrites']['regex'][] = '';
		//$this->k_tai_conf['urlRewrites']['tostr'][] = '';

		// <img> タグの src 属性の書き換えルール
		//$this->k_tai_conf['urlImgRewrites']['regex'][] = '';
		//$this->k_tai_conf['urlImgRewrites']['tostr'][] = '';

		// 携帯対応レンダー設定 以上
		/////////////////////////////


		///////////////////////////////
		// 以下は変更してはいけません。
		parent::HypCommonPreLoadBase($controller);

	}
}
}
?>