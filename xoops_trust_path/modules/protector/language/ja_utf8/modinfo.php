<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'protector';
}
$constpref = '_MI_' . strtoupper( $mydirname );

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED' ) ) {
	define( $constpref . '_LOADED', 1 );

	// The name of this module
	define( $constpref . '_NAME', 'Protector' );

	// A brief description of this module
	define( $constpref . '_DESC', '悪意ある攻撃からXOOPSを守るためのモジュール<br>DoS,SQL Injection,変数汚染といった攻撃を主に防ぎます。' );

	// Menu
	define( $constpref . '_DASHBOARD', 'Dashboard' );
	define( $constpref . '_ADVISORY', 'セキュリティガイド' );
	define( $constpref . '_LOGLIST', 'Security Log' );
	define( $constpref . '_LOGCLEARED', 'Log Cleared' );
	define( $constpref . '_IPBAN', 'Banned IPs' );
	define( $constpref . '_PREFIXMANAGER', 'DBプレフィックス ' );
	define( $constpref . '_SAFELIST', 'IPセーフリスト' );
	define( $constpref . '_ADMENU_MYBLOCKSADMIN', 'アクセス権限' );
	define( $constpref . '_CSP_REPORTS', 'CSP Reports');

	// Configs
	define( $constpref . '_GLOBAL_DISBL', '<h5>🚧 動作の一時的中断</h5>' );
	define( $constpref . '_GLOBAL_DISBLDSC', 'あらゆる防御動作を一時的に無効化します。<br>問題が解決されたら無効化を解除することをお忘れなく' );

	define( $constpref . '_DEFAULT_LANG', 'サイトのデフォルト言語' );
	define( $constpref . '_DEFAULT_LANGDSC', 'common処理前の強制終了メッセージを表示する言語を指定します' );

	define( $constpref . '_RELIABLE_IPS', '信用できるIP' );
	define( $constpref . '_RELIABLE_IPSDSC', 'DoS等の攻撃検知を行わない生IPアドレスを、| で区切って記述します。^は先頭を、$は末尾を表します。' );

	define( $constpref . '_LOG_LEVEL', 'ログレベル' );
	define( $constpref . '_LOG_LEVELDSC', '' );

	define( $constpref . '_BANIP_TIME0', '期限付IP拒否の期限(秒)' );

	define( $constpref . '_BANIP_IPV6PREFIX', 'IPv6拒否リスト登録プレフィクス' );
	define( $constpref . '_BANIP_IPV6PREFIXDSC', 'IPv6 アドレス登録時のプレフィクスビット数(128 で全ビット)' );

	define( $constpref . '_LOGLEVEL0', 'ログ出力一切なし' );
	define( $constpref . '_LOGLEVEL15', '危険性の高いものだけログを取る' );
	define( $constpref . '_LOGLEVEL63', '危険性の低いものはログしない' );
	define( $constpref . '_LOGLEVEL255', '全種類のロギングを有効とする' );

	define( $constpref . '_HIJACK_TOPBIT', 'セッションを継続する保護ビット(IPv4)' );
	define( $constpref . '_HIJACK_TOPBITDSC', 'セッションハイジャック対策：<br>通常は32(bit)で、全ビットを保護します。<br>Proxyの利用などで、アクセス毎にIPアドレスが変わる場合には、変動しない最長のビット数を指定します。<br>例えば、192.168.0.0〜192.168.0.255で変動する可能性がある場合、ここには24(bit)と指定します。' );
	define( $constpref . '_HIJACK_TOPBITV6', 'セッションを継続する保護ビット(IPv6)' );
	define( $constpref . '_HIJACK_TOPBITV6DSC', 'セッションハイジャック対策：<br>通常は128(bit)で、全ビットを保護します。<br>Proxyの利用などで、アクセス毎にIPアドレスが変わる場合には、変動しない最長のビット数を指定します。' );
	define( $constpref . '_HIJACK_DENYGP', 'IP変動を禁止するグループ' );
	define( $constpref . '_HIJACK_DENYGPDSC', 'セッションハイジャック対策：<br>セッション中に異なるIPアドレス範囲（上にてビット数指定）からのアクセスを禁止するグループを指定します<br>（管理者についてONにすることをお勧めします）' );
	define( $constpref . '_SAN_NULLBYTE', 'ヌル文字列をスペースに変更する' );
	define( $constpref . '_SAN_NULLBYTEDSC', '文字列終了キャラクターである "\\0" は、悪意ある攻撃に利用されます。<br>これを見つけた時点でスペースに書き換えます<br>（ONがお勧めです）' );
	define( $constpref . '_DIE_NULLBYTE', 'ヌル文字列を見つけた時点での強制終了' );
	define( $constpref . '_DIE_NULLBYTEDSC', '文字列終了キャラクターである "\\0" は、悪意ある攻撃に利用されます。<br>（ONがお勧めです）' );
	define( $constpref . '_DIE_BADEXT', '実行可能ファイルアップロードによる強制終了' );
	define( $constpref . '_DIE_BADEXTDSC', '拡張子が.phpなど、サーバ上で実行可能となりえるファイルがアップロードされた場合に強制終了します。<br>B-WikiやPukiWikiModをお使いで、頻繁にPHPソースファイルを添付する方は、OFFにして下さい' );
	define( $constpref . '_CONTAMI_ACTION', '変数汚染が見つかった時の処理' );
	define( $constpref . '_CONTAMI_ACTIONDS', 'XOOPSのシステムグローバルを上書きしようとする攻撃を見つけた場合の処理を選択します。<br>（初期値は「強制終了」）' );
	define( $constpref . '_ISOCOM_ACTION', '孤立コメントが見つかった時の処理' );
	define( $constpref . '_ISOCOM_ACTIONDSC', 'SQLインジェクション対策：<br>ペアになる*/のない/*を見つけた時の処理を決めます。<br>無害化方法：最後に */ をつけます<br>「無害化」がお勧めです' );
	define( $constpref . '_UNION_ACTION', 'UNIONが見つかった時の処理' );
	define( $constpref . '_UNION_ACTIONDSC', 'SQLインジェクション対策：<br>SQLのUNION構文を検出した時の処理を決めます。<br>無害化方法：UNION を uni-on とします<br>「無害化」がお勧めです' );
	define( $constpref . '_ID_INTVAL', 'ID風変数の強制変換' );
	define( $constpref . '_ID_INTVALDSC', '変数名がidで終わるものを、数字だと強制認識させます。myLinks派生モジュールに特に有効で、XSSなども防げますが、一部のモジュールで動作不良の原因となる可能性があります。' );
	define( $constpref . '_FILE_DOTDOT', 'DirectoryTraversalの禁止' );
	define( $constpref . '_FILE_DOTDOTDSC', 'DirectoryTraversalを試みていると判断されたリクエスト文字列から、".." というパターンを取り除きます' );

	define( $constpref . '_BF_COUNT', 'Brute Force対策' );
	define( $constpref . '_BF_COUNTDSC', 'パスワード総当たりに対抗します。10分間中、ここで指定した回数以上、ログインに失敗すると、そのIPを拒否します。' );

	define( $constpref . '_BWLIMIT_COUNT', 'サーバへの過負荷対策' );
	define( $constpref . '_BWLIMIT_COUNTDSC', '監視時間内に許可する最大アクセス数を指定します。CPU帯域などが貧弱な環境で、サーバへの過負荷を避けたい時にのみ指定してください。安全のために10未満の数値の場合は無視されます' );

	define( $constpref . '_DOS_SKIPMODS', 'DoS監視の対象から外すモジュール' );
	define( $constpref . '_DOS_SKIPMODSDSC', '外したいモジュールのdirnameを|で区切って入力してください。チャット系モジュールなどに有効です' );

	define( $constpref . '_DOS_EXPIRE', 'DoS等の監視時間 (秒)' );
	define( $constpref . '_DOS_EXPIREDSC', 'DoSや悪意あるクローラーのアクセス頻度を追うための監視単位時間' );

	define( $constpref . '_DOS_F5COUNT', 'F5アタックと見なす回数' );
	define( $constpref . '_DOS_F5COUNTDSC', 'DoS攻撃の防御<br>上で設定した監視時間内に、この回数以上、同一URIへのアクセスがあったら、攻撃されたと見なします' );
	define( $constpref . '_DOS_F5ACTION', 'F5アタックへの対処' );

	define( $constpref . '_DOS_CRCOUNT', '悪意あるクローラーと見なす回数' );
	define( $constpref . '_DOS_CRCOUNTDSC', '悪意あるクローラー（メアド収集ボット等）への対策<br>上で設定した監視時間内に、この回数以上、サイト内をさぐったら、悪意あるクローラーと見なします' );
	define( $constpref . '_DOS_CRACTION', '悪意あるクローラーへの対処' );

	define( $constpref . '_DOS_CRSAFE', '拒否しない User-Agent' );
	define( $constpref . '_DOS_CRSAFEDSC', '無条件でクロール許可するエージェント名を、perlの正規表現で記述します<br>例) /(msnbot|Googlebot|Yahoo! Slurp)/i' );

	define( $constpref . '_OPT_NONE', 'なし (ログのみ取る)' );
	define( $constpref . '_OPT_SAN', '無害化' );
	define( $constpref . '_OPT_EXIT', '強制終了' );
	define( $constpref . '_OPT_BIP', '拒否IP登録(無期限)' );
	define( $constpref . '_OPT_BIPTIME0', '拒否IP登録(期限付)' );

	define( $constpref . '_DOSOPT_NONE', 'なし (ログのみ取る)' );
	define( $constpref . '_DOSOPT_SLEEP', 'Sleep(非推奨)' );
	define( $constpref . '_DOSOPT_EXIT', 'exit' );
	define( $constpref . '_DOSOPT_BIP', '拒否IPリストに載せる(無期限)' );
	define( $constpref . '_DOSOPT_BIPTIME0', '拒否IPリストに載せる(期限付)' );
	define( $constpref . '_DOSOPT_HTA', '.htaccessにDENY登録(試験的実装)' );

	define( $constpref . '_BIP_EXCEPT', '拒否IP登録の保護グループ' );
	define( $constpref . '_BIP_EXCEPTDSC', 'ここで指定されたユーザーからのアクセスは、条件を満たしてしまっても、拒否IPとして登録されません。ただし、そのユーザーがログインしていないと意味がありませんので、ご注意下さい。' );

	define( $constpref . '_DISABLES', '危険な機能の無効化' );

	define( $constpref . '_DBLAYERTRAP', 'DBレイヤートラップanti-SQL-Injectionを有効にする' );
	define( $constpref . '_DBLAYERTRAPDSC', 'これを有効にすれば、かなり多くのパターンのSQL Injection脆弱性をカバーすることができるでしょう。ただし、利用しているコアシステム側でこの機能に対応している必要があります。セキュリティガイドで確認できます。ONにすることを強くお勧めします。誤判定を繰り返す場合は、下の設定を変更してみてください。' );
	define( $constpref . '_DBTRAPWOSRV', 'DBレイヤートラップでサーバ変数を除外する' );
	define( $constpref . '_DBTRAPWOSRVDSC', 'サーバ設定によってはDBレイヤートラップ機能が常に有効になってしまう可能性があります。SQL Injectionの誤判定が頻発する場合はここをONにしてみてください。ただしここをONにすることでSQL Injectionチェックがかなり甘くなるので、あくまで緊急回避策としてだけ利用してください。' );

	define( $constpref . '_BIGUMBRELLA', '「大きな傘」anti-XSSを有効にする' );
	define( $constpref . '_BIGUMBRELLADSC', 'これを有効にすれば、かなり多くのパターンのXSS脆弱性をキャンセルすることができるでしょう。ただし、100%ではありません。' );

	define( $constpref . '_SPAMURI4U', 'SPAM対策:一般ユーザに許すURL数' );
	define( $constpref . '_SPAMURI4UDSC', '管理者以外の一般ユーザの投稿内容に、この数以上のURLがあったらSPAMと見なします。0なら無制限許可です。' );
	define( $constpref . '_SPAMURI4G', 'SPAM対策:ゲストに許すURL数' );
	define( $constpref . '_SPAMURI4GDSC', 'ゲストの投稿内容に、この数以上のURLがあったらSPAMと見なします。0なら無制限許可です。' );

	define( $constpref . '_FILTERS', 'このサイトで有効にするフィルター' );
	define( $constpref . '_FILTERSDSC', 'filters_byconfig内のファイル名を１行ずつ指定します' );

	define( $constpref . '_MANIPUCHECK', 'サイト改ざんチェックを有効にする' );
	define( $constpref . '_MANIPUCHECKDSC', '簡易的な書き換えチェックを行い、index.php等に変更があったらその旨を通知します' );
	define( $constpref . '_MANIPUVALUE', 'サイト改ざんチェック値' );
	define( $constpref . '_MANIPUVALUEDSC', '⛔ 意味を理解していない限り編集しないでください' );

	// Threat Intelligence settings
	define( $constpref . '_HTTPBL_ENABLED', '<h5>🔶 Enable HTTP:BL</h5>');
	define( $constpref . '_HTTPBL_ENABLED_DESC', 'Enable Project Honeypot HTTP:BL service for IP reputation checking');
	define( $constpref . '_HTTPBL_KEY', 'HTTP:BL API Key');
	define( $constpref . '_HTTPBL_KEY_DESC', 'Enter your Project Honeypot HTTP:BL API key. Get one at projecthoneypot.org');
	define( $constpref . '_HTTPBL_THREAT_THRESHOLD', 'Threat Threshold');
	define( $constpref . '_HTTPBL_THREAT_THRESHOLD_DESC', 'IPs with a threat score above this value will be blocked (0-255). Recommended: 25-50');

	define( $constpref . '_FEED_URLS', 'Threat Feed URLs');
	define( $constpref . '_FEED_URLS_DESC', 'Enter URLs for IP blacklists, one per line. Supported formats: CSV, TXT with one IP per line');

	define( $constpref . '_CHECK_LOGIN', 'Check Login Attempts');
	define( $constpref . '_CHECK_LOGIN_DESC', 'Verify IPs against threat intelligence during login attempts');
	define( $constpref . '_CHECK_REGISTER', 'Check Registration');
	define( $constpref . '_CHECK_REGISTER_DESC', 'Verify IPs against threat intelligence during user registration');
	define( $constpref . '_CHECK_FORMS', 'Check Form Submissions');
	define( $constpref . '_CHECK_FORMS_DESC', 'Verify IPs against threat intelligence during any form submission (may impact performance)');
	define( $constpref . '_CHECK_ADMIN', 'Check Admin Access');
	define( $constpref . '_CHECK_ADMIN_DESC', 'Verify IPs against threat intelligence during admin area access');

	define( $constpref . '_CACHE_DURATION', 'Cache Duration');
	define( $constpref . '_CACHE_DURATION_DESC', 'How long to cache threat intelligence results');
	define( $constpref . '_CACHE_1HOUR', '1 Hour');
	define( $constpref . '_CACHE_6HOURS', '6 Hours');
	define( $constpref . '_CACHE_1DAY', '1 Day');
	define( $constpref . '_CACHE_1WEEK', '1 Week');

	// Threat Intelligence Dashboard
define( $constpref . '_THREAT_INTELLIGENCE_DASHBOARD', 'Threat Intelligence Dashboard');
define( $constpref . '_THREAT_INTELLIGENCE_SETTINGS', 'Threat Intelligence Settings');
define( $constpref . '_NOTHREATSTATS', 'No threat intelligence events recorded yet');
define( $constpref . '_DATE', 'Date/Time');
define( $constpref . '_IP', 'IP Address');
define( $constpref . '_AGENT', 'User Agent');
define( $constpref . '_DESCRIPTION', 'Description');

// Proxy settings
define('_MI_PROTECTOR_PROXY_ENABLED', '<h5><a id="enable-proxy">🌐</a> Enable Web Proxy</h5>');
define('_MI_PROTECTOR_PROXY_ENABLED_DESC', 'Enable the web proxy functionality');

define('_MI_PROTECTOR_PROXY_ALLOWED_DOMAINS', 'Allowed Domains');
define('_MI_PROTECTOR_PROXY_ALLOWED_DOMAINS_DESC', 'Enter one domain per line. Leave empty to allow all domains not in the blocked list. Use .example.com to match all subdomains.');

define('_MI_PROTECTOR_PROXY_BLOCKED_DOMAINS', 'Blocked Domains');
define('_MI_PROTECTOR_PROXY_BLOCKED_DOMAINS_DESC', 'Enter one domain per line. These domains will always be blocked. Use .example.com to match all subdomains.');

define('_MI_PROTECTOR_PROXY_CACHE_ENABLED', 'Enable Caching');
define('_MI_PROTECTOR_PROXY_CACHE_ENABLED_DESC', 'Cache proxied content to improve performance');

define('_MI_PROTECTOR_PROXY_CACHE_TIME', 'Cache Time (seconds)');
define('_MI_PROTECTOR_PROXY_CACHE_TIME_DESC', 'How long to keep cached content (in seconds)');

define('_MI_PROTECTOR_PROXY_LOG_REQUESTS', 'Log Requests');
define('_MI_PROTECTOR_PROXY_LOG_REQUESTS_DESC', 'Log all proxy requests');

define('_MI_PROTECTOR_PROXY_STRIP_JS', 'Strip JavaScript');
define('_MI_PROTECTOR_PROXY_STRIP_JS_DESC', 'Remove JavaScript from proxied content');

define('_MI_PROTECTOR_PROXY_STRIP_COOKIES', 'Strip Cookies');
define('_MI_PROTECTOR_PROXY_STRIP_COOKIES_DESC', 'Do not forward cookies from proxied sites');

define('_MI_PROTECTOR_PROXY_USER_AGENT', 'Custom User Agent');
define('_MI_PROTECTOR_PROXY_USER_AGENT_DESC', 'Set a custom user agent for proxy requests. Leave empty to use the default.');

define('_MI_PROTECTOR_PROXY_PLUGINS_ENABLED', 'Enabled Plugins');
define('_MI_PROTECTOR_PROXY_PLUGINS_ENABLED_DESC', 'Select which proxy plugins to enable');

// Module access permissions
define('_MI_PROTECTOR_MODULE_ACCESS_GROUPS', 'Module Access Groups');
define('_MI_PROTECTOR_MODULE_ACCESS_GROUPS_DESC', 'Select which groups can access the Protector module administration');

// Proxy access permissions
define('_MI_PROTECTOR_PROXY_ACCESS_GROUPS', 'Proxy Access Groups');
define('_MI_PROTECTOR_PROXY_ACCESS_GROUPS_DESC', 'Select which groups can use the web proxy functionality');

// Notification related constants
define('_MI_PROTECTOR_NOTIFY_GLOBAL', 'Global Notifications');
define('_MI_PROTECTOR_NOTIFY_GLOBAL_DESC', 'Global Protector notifications');

define('_MI_PROTECTOR_NOTIFY_SECURITY_THREAT', 'Security Threat Detected');
define('_MI_PROTECTOR_NOTIFY_SECURITY_THREAT_CAP', 'Notify me when security threats are detected');
define('_MI_PROTECTOR_NOTIFY_SECURITY_THREAT_DESC', 'Receive notifications when the system detects security threats');
define('_MI_PROTECTOR_NOTIFY_SECURITY_THREAT_SUBJECT', 'Security Threat Alert: {SITE_NAME}');

define('_MI_PROTECTOR_NOTIFY_PROXY_ACCESS', 'Proxy Access Alert');
define('_MI_PROTECTOR_NOTIFY_PROXY_ACCESS_CAP', 'Notify me about proxy access events');
define('_MI_PROTECTOR_NOTIFY_PROXY_ACCESS_DESC', 'Receive notifications about proxy access events');
define('_MI_PROTECTOR_NOTIFY_PROXY_ACCESS_SUBJECT', 'Proxy Access Alert: {SITE_NAME}');

define('_MI_PROTECTOR_NOTIFICATION_ENABLED', '<h5><a id="enable-notification">🔔</a> Enable Notifications</h5>');
define('_MI_PROTECTOR_NOTIFICATION_ENABLED_DESC', 'Choose how you want to receive notifications from Protector');
define('_MI_PROTECTOR_NOTIFICATION_DISABLE', 'Disable notifications');
define('_MI_PROTECTOR_NOTIFICATION_ENABLE_INBOX', 'Enable inbox notifications only');
define('_MI_PROTECTOR_NOTIFICATION_ENABLE_EMAIL', 'Enable email notifications only');
define('_MI_PROTECTOR_NOTIFICATION_ENABLE_BOTH', 'Enable both inbox and email notifications');

define('_MI_PROTECTOR_NOTIFICATION_EVENTS', 'Notification Events');
define('_MI_PROTECTOR_NOTIFICATION_EVENTS_DESC', 'Select which events should trigger notifications');

// CORS Proxy settings
define('_MI_PROTECTOR_PROXY_CORS_ORIGIN', '<h5><a id="enable-cors">☁</a> CORS: Allowed Origins</h5>');
define('_MI_PROTECTOR_PROXY_CORS_ORIGIN_DESC', 'Specify which origins are allowed to access resources through the proxy. Use * for all origins or a comma-separated list of domains.');

define('_MI_PROTECTOR_PROXY_CORS_METHODS', 'CORS: Allowed Methods');
define('_MI_PROTECTOR_PROXY_CORS_METHODS_DESC', 'HTTP methods allowed when accessing the resource. Separate multiple methods with commas.');

define('_MI_PROTECTOR_PROXY_CORS_HEADERS', 'CORS: Allowed Headers');
define('_MI_PROTECTOR_PROXY_CORS_HEADERS_DESC', 'Headers that are allowed to be used with the request. Use * for all headers or a comma-separated list.');

define('_MI_PROTECTOR_PROXY_CORS_DEBUG', 'CORS: Debug Mode');
define('_MI_PROTECTOR_PROXY_CORS_DEBUG_DESC', 'Enable debug logging for CORS requests and responses.');

// Content Security Policy settings
define('_MI_PROTECTOR_ENABLE_CSP', '<h5>🛡️ Enable Content Security Policy</h5>');
define('_MI_PROTECTOR_ENABLE_CSP_DESC', 'Activate Content Security Policy (CSP) to help prevent XSS attacks and other code injection attacks');

define('_MI_PROTECTOR_CSP_LEGACY_SUPPORT', 'Add CSP Meta Tag Support');
define('_MI_PROTECTOR_CSP_LEGACY_SUPPORT_DESC', 'Also add CSP as a meta tag for older browsers that don\'t support CSP headers');

define('_MI_PROTECTOR_CSP_DEFAULT_SRC', 'Default Sources');
define('_MI_PROTECTOR_CSP_DEFAULT_SRC_DESC', 'Default policy for loading content such as JavaScript, Images, CSS, Fonts, AJAX requests, Frames, HTML5 Media');

define('_MI_PROTECTOR_CSP_SCRIPT_SRC', 'Script Sources');
define('_MI_PROTECTOR_CSP_SCRIPT_SRC_DESC', 'Defines valid sources of JavaScript. Use \'unsafe-inline\' to allow inline scripts and \'unsafe-eval\' to allow eval()');

define('_MI_PROTECTOR_CSP_STYLE_SRC', 'Style Sources');
define('_MI_PROTECTOR_CSP_STYLE_SRC_DESC', 'Defines valid sources of stylesheets or CSS. Use \'unsafe-inline\' to allow inline styles');

define('_MI_PROTECTOR_CSP_IMG_SRC', 'Image Sources');
define('_MI_PROTECTOR_CSP_IMG_SRC_DESC', 'Defines valid sources of images. Add \'data:\' to allow data: URIs for images');

define('_MI_PROTECTOR_CSP_CONNECT_SRC', 'Connect Sources');
define('_MI_PROTECTOR_CSP_CONNECT_SRC_DESC', 'Defines valid sources for fetch, XMLHttpRequest, WebSocket, and EventSource connections');

define('_MI_PROTECTOR_CSP_FONT_SRC', 'Font Sources');
define('_MI_PROTECTOR_CSP_FONT_SRC_DESC', 'Defines valid sources for fonts loaded using @font-face');

define('_MI_PROTECTOR_CSP_OBJECT_SRC', 'Object Sources');
define('_MI_PROTECTOR_CSP_OBJECT_SRC_DESC', 'Defines valid sources for the <object>, <embed>, and <applet> elements');

define('_MI_PROTECTOR_CSP_MEDIA_SRC', 'Media Sources');
define('_MI_PROTECTOR_CSP_MEDIA_SRC_DESC', 'Defines valid sources for loading media using the <audio>, <video> and <track> elements');

define('_MI_PROTECTOR_CSP_FRAME_SRC', 'Frame Sources');
define('_MI_PROTECTOR_CSP_FRAME_SRC_DESC', 'Defines valid sources for loading frames');

define('_MI_PROTECTOR_CSP_REPORT_URI', 'Report URI');
define('_MI_PROTECTOR_CSP_REPORT_URI_DESC', 'URI to which the browser sends reports about policy violations');

// CSP Reporting
define('_MI_PROTECTOR_NOTIFY_CSP', 'Notify on Critical CSP Violations');
define('_MI_PROTECTOR_NOTIFY_CSP_DESC', 'Send email notifications for critical Content Security Policy violations (script-src)');
define('_MI_PROTECTOR_NOTIFY_ALL_CSP', 'Notify on All CSP Violations');
define('_MI_PROTECTOR_NOTIFY_ALL_CSP_DESC', 'Send email notifications for all Content Security Policy violations (may generate many emails)');

}
