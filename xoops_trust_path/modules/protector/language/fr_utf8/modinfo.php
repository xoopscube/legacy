<?php

if (defined('FOR_XOOPS_LANG_CHECKER')) {
 $mydirname = 'protector' ;
}
$constpref = '_MI_' . strtoupper($mydirname) ;

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED' ) ) {
	define( $constpref . '_LOADED', 1 );

    // The name of this module
define($constpref."_NAME", "Protector");

// A brief description of this module
define($constpref."_DESC", "Protector apporte à votre site une sécurité supplémentaire contre les attaques du type DoS, SQL Injection, et contaminations de variables.");

// Menu
define( $constpref . '_DASHBOARD', 'Dashboard' );
define( $constpref . '_ADVISORY', 'Security Advisor' );
define( $constpref . '_LOGLIST', 'Log List' );
define( $constpref . '_LOGCLEARED', 'Log Cleared' );
define( $constpref . '_IPBAN', 'Banned IPs' );
define( $constpref . '_PREFIXMANAGER', 'Gestion du préfixe BDD');
define( $constpref . '_SAFELIST', 'IP autorisée' );
define( $constpref . '_ADMENU_MYBLOCKSADMIN', 'Permissions');
define( $constpref . '_CSP_REPORTS', 'CSP Reports');

// Configs
define($constpref."_GLOBAL_DISBL", "<h5>🚧 Désactiver Temporairement</h5>");
define($constpref."_GLOBAL_DISBLDSC", "Les diverses protections sont désactivées temporairement.<br />Reactiver Protector après avoir resolut votre problème");
define( $constpref . '_DEFAULT_LANG', 'Default language' );
define( $constpref . '_DEFAULT_LANGDSC', 'Specify the language set to display messages before processing common.php' );

define($constpref."_RELIABLE_IPS", "Adresses IP autorisées");
define($constpref."_RELIABLE_IPSDSC", "Ajouter les addresses IP autorisées en les séparant avec le caractère |<br /> ^ pour le début de la chaîne<br /> $ pour la fin de la chaîne.");

define($constpref."_LOG_LEVEL", "Niveau de connexion");
define($constpref."_LOG_LEVELDSC", "");

define($constpref."_BANIP_TIME0", "Temps d'exclusion d'une adresse IP (secondes)");

define( $constpref . '_BANIP_IPV6PREFIX', 'IPv6 deny list registration prefix' );
define( $constpref . '_BANIP_IPV6PREFIXDSC', 'Number of prefix bit at IPv6 address registration (128 bit to all bits)' );

define($constpref."_LOGLEVEL0", "Aucun");
define($constpref."_LOGLEVEL15", "Discret");
define($constpref."_LOGLEVEL63", "discret");
define($constpref."_LOGLEVEL255", "complet");

define($constpref."_HIJACK_TOPBIT", "Nombre de bits IP à protéger par sesssion");
define($constpref."_HIJACK_TOPBITDSC", "Anti Session Hi-Jacking: par défaut 32(bit). (Tous les bits sont protégés)<br />Si votre adresse IP n'est pas fixe, reglér la rangée d'adresses IP par le nombre de bits.<br />ex. pour une adresse IP qui évolue dans la rangée 192.168.0.0 à 192.168.0.255, ajouter ceci: 24 (bits)");
define( $constpref . '_HIJACK_TOPBITV6', 'Protected IP bits for the session(IPv6)' );
define( $constpref . '_HIJACK_TOPBITV6DSC', 'Anti Session Hijacking:<br>Default 128(bit). (All bits are protected)<br>When your IP is not static, set the IP range by number of the bits.' );
define($constpref."_HIJACK_DENYGP", "Groupes non autorisés a modifier leur adresse IP au cours d'une session");
define($constpref."_HIJACK_DENYGPDSC", "Anti Session Hi-Jacking:<br />Sélectionner le(s) groupe(s) interdit(s) de modifier leur adresse IP au cours d'une session.<br />(Conseil : ajouter les administrateurs.)");
define($constpref."_SAN_NULLBYTE", "Filtrer les bits null");
define($constpref."_SAN_NULLBYTEDSC", "Le caractère de terminaison '\\0' est souvent utilisé dans des attaques malveillantes.<br />un bit null sera transformé en espace .<br />(Conseil : il est fortement recommandaté d'activer cette option)");
define($constpref."_DIE_NULLBYTE", "Déconnecter si des bits null sont utilisés");
define($constpref."_DIE_NULLBYTEDSC", "Le caractère de terminaison '\\0' est souvent utilisé dans des attaques malveillantes.<br />(Conseil : il est fortement recommandé d'activer cette option)");
define($constpref."_DIE_BADEXT", "Déconnecter si des fichiers interdits sont téléchargés");
define($constpref."_DIE_BADEXTDSC", "Protector peut detécter le téléchargement de fichiers avec une extension interdite comme .php , et arreter la session.<br />(Conseil : si vous attachez fréquemment des fichiers php dans B-Wiki ou PukiWikiMod, n'activez pas cette option.)");
define($constpref."_CONTAMI_ACTION", "Action lors d'une contamination");
define($constpref."_CONTAMI_ACTIONDS", "Selectionner l'action lorsque protector détecte une tentative de contamination des variables globales du systéme XOOPS.<br />(Conseil : écran blanc récommendé)");
define($constpref."_ISOCOM_ACTION", "Action lors d'un commentaire isolé");
define($constpref."_ISOCOM_ACTIONDSC", "Anti Injection SQL:<br />Sélectionner l'action à effectuer lorsque Protector détecte '/*' .<br />(Conseil : 'filtrer')");
define($constpref."_UNION_ACTION", "Action lors d'une requête UNION");
define($constpref."_UNION_ACTIONDSC", "Anti Injection SQL:<br />Sélectionner l'action à effectuer lorsque Protector détecte une syntaxe sql UNION.<br />(Conseil : 'filtrer')");
define($constpref."_ID_INTVAL", "Forcer la transformation en nombre entier (intval) de variables comme ID");
define($constpref."_ID_INTVALDSC", "Protection contre attaques XSS et injections SQL en traitant les appels '*id' comme un nombre entier.<br />(Conseil : activer cette option. Certains modules peuvent cesser de fonctionner.)");
define($constpref."_FILE_DOTDOT", "Protection contre des attaques de type traversée de répertoires");
define($constpref."_FILE_DOTDOTDSC", "Elimination de «..» pour toutes les requêtes semblables à une tentative d'accés par traversée de répertoires");

define($constpref."_BF_COUNT", "Anti Brute Force");
define($constpref."_BF_COUNTDSC", "Détermine le nombre de tentatives de connexion autorisées pour un anonyme dans un intervale de 10 minutes. En cas d'échec l'adresse IP sera interdite.");

define( $constpref . '_BWLIMIT_COUNT', 'Bandwidth limitation' );
define( $constpref . '_BWLIMIT_COUNTDSC', 'Specify the max number of connections allowed to mainfile.php at the same time.<br>This value should be set to "0" for a normal environment with enough bandwidth and CPU.<br>Less than 10 will be ignored.' );

define($constpref."_DOS_SKIPMODS", "Modules à exclure du contrôle DoS (F5)/Crawler");
define($constpref."_DOS_SKIPMODSDSC", "Ajouter les noms des répertoires des modules séparés par |. Par exemple, les modules de chat.");

define($constpref."_DOS_EXPIRE", "Délai en secondes pour réagir aux rechargements fréquents d'une page (attaque 'touche F5')");
define($constpref."_DOS_EXPIREDSC", "Limite en secondes pour les tentatives de rechargement de page (attaque 'touche F5') et aspirateurs de site.");

define($constpref."_DOS_F5COUNT", "Nombre de tentatives F5 autorisées");
define($constpref."_DOS_F5COUNTDSC", "Protection contre des attaques DoS :<br/>Ajouter une valeur pour détermine le nombre de rechargements d'une connexion avant de considérer comme une attaque malicieuse.");
define($constpref."_DOS_F5ACTION", "Action lors d'une attaque F5");

define($constpref."_DOS_CRCOUNT", "Nombre de tentatives pour considérer un crawler comme malicieux");
define($constpref."_DOS_CRCOUNTDSC", "Protection contre des crawlers-aspirateurs malicieux (par exemple, les bots chasseurs d'emails):<br/>Ajouter une valeur pour détermine le nombre d'accès du crawler avant de le considérer comme une attaque malicieuse.");
define($constpref."_DOS_CRACTION", "Action lors des crawlers malicieux");

define($constpref."_DOS_CRSAFE", "User-Agent autorisés");
define($constpref."_DOS_CRSAFEDSC", "Regex Perl pour les User-Agents.<br /> Evite de considérer le crawler comme un aspirateur.<br/> Ex.: msnbot|Googlebot|Yahoo! Slurp");

define($constpref."_OPT_NONE", "Aucune (enregistrer seulement)");
define($constpref."_OPT_SAN", "Filtrer");
define($constpref."_OPT_EXIT", "Ecran blanc");
define($constpref."_OPT_BIP", "Interdire l'IP (indéfiniment)");
define($constpref."_OPT_BIPTIME0", "Interdire l'IP (temporairement)");

define($constpref."_DOSOPT_NONE", "Aucune (enregistrer seulement)");
define($constpref."_DOSOPT_SLEEP", "Veille");
define($constpref."_DOSOPT_EXIT", "Ecran blanc");
define($constpref."_DOSOPT_BIP", "Interdire l'IP (indéfiniment)");
define($constpref."_DOSOPT_BIPTIME0", "Interdire l'IP (temporairement)");
;
define($constpref."_DOSOPT_HTA", "Interdire via .htaccess(Experimental)");

define($constpref."_BIP_EXCEPT", "Groupes jamais enregistrés avec IP interdites");
define($constpref."_BIP_EXCEPTDSC", "Les utilisateurs de ces groupe ne seront jamais interdits d'accès.<br />(Conseil : administrateurs recommandé)");

define($constpref."_DISABLES", "Désactiver les fonctions dangereuses dans XOOPS");
define( $constpref . '_DBLAYERTRAP', '🗄 Enable DB Layer trapping Anti-SQL-Injection' );
define( $constpref . '_DBLAYERTRAPDSC', 'This feature offers strong protection against most SQL injection attacks and requires databasefactory support (check status on the Advisory page). Ensure this setting is enabled and never accidentally turned off!' );
define( $constpref . '_DBTRAPWOSRV', '🗄 Disable the check of the $_SERVER superglobal for Anti-SQL-Injection' );
define( $constpref . '_DBTRAPWOSRVDSC', 'Enabling this option may resolve false SQL injection attack detections caused by DB Layer trapping on some servers. However, be aware that it weakens the security of the DB Layer trapping anti-SQL injection system.<br> Use it cautiously.' );

define($constpref."_BIGUMBRELLA", "Activer la protection anti-XSS (Big Umbrella)");
define($constpref."_BIGUMBRELLADSC", "Protection contre les attaques par l'intermédiaire des vulnérabilitées XSS. Sans garantie à 100%");

define($constpref."_SPAMURI4U", "anti-SPAM: URLs par utilisateurs");
define($constpref."_SPAMURI4UDSC", "Nombre limite d'URL dans les données POST d'un utilisateur qui n'est pas administrateur, pour le considérer comme du SPAM. Pour désactiver cette option, laisser sur 0 .");
define($constpref."_SPAMURI4G", "anti-SPAM: URLs par anonymes");
define($constpref."_SPAMURI4GDSC", "Nombre limite d'URL dans les données POST d'un visiteur anonyme, pour consider comme du SPAM. Pour désactiver cette option, laisser sur 0 .");
define( $constpref . '_FILTERS', 'Enable filters' );
define( $constpref . '_FILTERSDSC', 'To activate filters, list each filter file name (located in the /filters_byconfig/ directory) on a separate line e.g.: postcommon_post_deny_by_httpbl.php' );

define( $constpref . '_MANIPUCHECK', '<b>Enable check files change</b>' );
define( $constpref . '_MANIPUCHECKDSC', '🔔 Notify administrators if the root folder or index changes.' );
define( $constpref . '_MANIPUVALUE', '<b>Value to check files change</b>' );
define( $constpref . '_MANIPUVALUEDSC', '⛔ Warning, do not change this field !' );

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
