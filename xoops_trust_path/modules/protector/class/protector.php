<?php

/**
 * Protector module for XCL
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nunoo Luciano Gigamaster XCL PHP8.2
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

class protector {


	public $mydirname;

	public $_conn = null;
	public $_conf = [];
	public $_conf_serialized = '';

	// Security-related properties
	public $_bad_globals = [];

	public $message = '';
	public $warning = false;
	public $error = false;
	public $_doubtful_requests = [];
	public $_bigumbrella_doubtfuls = [];

	public $_dblayertrap_doubtfuls = [];
	public $_dblayertrap_doubtful_needles = [
		'information_schema',
		'select',
		"'",
		'"',
	];

	public $_logged = false;

	// Protection flags
	public $_done_badext = false;
	public $_done_intval = false;
	public $_done_dotdot = false;
	public $_done_nullbyte = false;
	public $_done_contami = false;
	public $_done_isocom = false;
	public $_done_union = false;
	public $_done_dos = false;

	public $_safe_badext = true;
	public $_safe_contami = true;
	public $_safe_isocom = true;
	public $_safe_union = true;

	public $_spamcount_uri = 0;

	public $_should_be_banned_time0 = false;
	public $_should_be_banned = false;

	public $_dos_stage = null;

	public $ip_matched_info = null;

	public $last_error_type = 'UNKNOWN';

	public $is_ipv6 = false;
	public $remote_ip = '';

	// Constructor
	public function __construct() {
		return $this->Protector();
	}

	public function Protector() {
		$this->mydirname = 'protector';

		// Preferences from configs/cache
		$this->_conf_serialized = @file_get_contents( $this->get_filepath4confighcache() );
		$this->_conf            = @unserialize( $this->_conf_serialized );
		if ( empty( $this->_conf ) ) {
			$this->_conf = [];
		}

		if ( ! empty( $this->_conf['global_disabled'] ) ) {
			return true;
		}

		// List of global variables that should be protected
		$this->_bad_globals = [
			'GLOBALS',
			'_SESSION',
			'HTTP_SESSION_VARS',
			'_GET',
			'HTTP_GET_VARS',
			'_POST',
			'HTTP_POST_VARS',
			'_COOKIE',
			'HTTP_COOKIE_VARS',
			'_SERVER',
			'HTTP_SERVER_VARS',
			'_REQUEST',
			'_ENV',
			'_FILES',
			'xoopsDB',
			'xoopsUser',
			'xoopsUserId',
			'xoopsUserGroups',
			'xoopsUserIsAdmin',
			'xoopsConfig',
			'xoopsOption',
			'xoopsModule',
			'xoopsModuleConfig'
		];

		$this->remote_ip = $this->get_remote_ip();
		if (str_contains($this->remote_ip, ':')) {
			$this->is_ipv6 = true;
		}

		// Initialize security checks
		$this->_initial_recursive($_GET, 'G');
		$this->_initial_recursive($_POST, 'P');
		$this->_initial_recursive($_COOKIE, 'C');
	}

	// Modern singleton implementation
	public static function getInstance(): self
	{
		static $instance = null;
		if ($instance === null) {
			$instance = new self();
		}

		return $instance;
	}

	// Initial security check for request variables
	public function _initial_recursive($val, $key): void
	{
		if (is_array($val)) {
			foreach ($val as $subkey => $subval) {
				// Check for bad globals
				if (in_array($subkey, $this->_bad_globals, true)) {
					$this->message .= "Attempt to inject '$subkey' was found.\n";
					$this->_safe_contami = false;
					$this->last_error_type = 'CONTAMI';
				}
				$this->_initial_recursive($subval, $key . '_' . base64_encode((string)$subkey));
			}
		} else {
			// Check nullbyte attack
			if (!empty($this->_conf['san_nullbyte']) && str_contains((string)$val, chr(0))) {
				$val = str_replace(chr(0), ' ', (string)$val);
				$this->replace_doubtful($key, $val);
				$this->message .= "Injecting Null-byte '$val' found.\n";
				$this->output_log('NullByte', 0, false, 32);
			}

			// Register as doubtful requests against SQL Injections
			if (preg_match('?[\s\'"`/]?', (string)$val)) {
				$this->_doubtful_requests[(string)$key] = $val;
			}
		}
	}

	// Update configuration in database
	public function updateConfIntoDb(string $name, string $value): void
	{
		$constpref = '_MI_' . strtoupper($this->mydirname);

		// Get database instance using the factory pattern
		$db =& XoopsDatabaseFactory::getDatabaseConnection();
		$db->queryF('UPDATE `' . $db->prefix('config') . "` SET `conf_value`='" . addslashes($value) . "' WHERE `conf_title` like '" . $constpref . "%' AND `conf_name`='" . addslashes($name) . "' LIMIT 1");
		$this->updateConfFromDb();
	}

	// Load configuration from database
	public function updateConfFromDb(): bool
	{
		$constpref = '_MI_' . strtoupper($this->mydirname);

		if (empty($this->_conn)) {
			return false;
		}

		$query = 'SELECT `conf_name`,`conf_value` FROM `' . XOOPS_DB_PREFIX . "_config` WHERE `conf_title` like '" . $constpref . "%'";
		$db_conf = [];

		if (is_object($this->_conn) && $this->_conn instanceof \mysqli) {
			$result = @mysqli_query($this->_conn, $query);
			if (!$result || mysqli_num_rows($result) < 5) {
				return false;
			}
			while ([$key, $val] = mysqli_fetch_row($result)) {
				$db_conf[$key] = $val;
			}
		}

		$db_conf_serialized = serialize($db_conf);

		// Update config cache
		if ($db_conf_serialized != $this->_conf_serialized) {
			$fp = fopen($this->get_filepath4confighcache(), 'w');
			fwrite($fp, $db_conf_serialized);
			fclose($fp);
			$this->_conf = $db_conf;
		}

		return true;
	}

	// Set database connection
	public function setConn($conn): void
	{
		$this->_conn = $conn;
	}

	// Get configuration
	public function getConf(): array
	{
		return $this->_conf;
	}

	// Purge method with better cookie handling
	public function purge(bool $redirect_to_top = false): void
	{
		// Clear all session values
		if (isset($_SESSION)) {
			foreach ($_SESSION as $key => $val) {
				$_SESSION[$key] = '';
				if (isset($GLOBALS[$key])) {
					$GLOBALS[$key] = '';
				}
			}
		}

		if (!headers_sent()) {
			// Clear typical session id of PHP with modern cookie options
			$cookie_options = [
				'expires' => time() - 3600,
				'path' => '/',
				'domain' => '',
				'secure' => 0,
				'httponly' => true,
				'samesite' => 'Lax'
			];

			setcookie('PHPSESSID', '', $cookie_options);
			if (isset($_COOKIE[session_name()])) {
				setcookie(session_name(), '', $cookie_options);
			}

			// Clear autologin cookie xoops2
			$xoops_cookie_path = defined('XOOPS_COOKIE_PATH') ? XOOPS_COOKIE_PATH :
				preg_replace('?https://[^/]+(/.*)$?', '$1', XOOPS_URL);
			if (XOOPS_URL == $xoops_cookie_path) {
				$xoops_cookie_path = '/';
			}

			$autologin_options = [
				'expires' => time() - 3600,
				'path' => $xoops_cookie_path,
				'domain' => '',
				'secure' => 0,
				'httponly' => true,
				'samesite' => 'Lax'
			];

			setcookie('autologin_uname', '', $autologin_options);
			setcookie('autologin_pass', '', $autologin_options);
		}

		// Make sure any pending logs are written before redirecting
		if ($this->last_error_type && !$this->_logged) {
			$this->output_log($this->last_error_type, 0, false, 64);
		}

		if ($redirect_to_top) {
			header('Location: ' . XOOPS_URL . '/');
			exit;
		} else {
			$ret = $this->call_filter('prepurge_exit');
			if (false === $ret) {
				die('Protector detects attacking actions');
			}
		}
	}
    
	// Improved logging method with better error handling
	public function output_log(string $type = '', int $uid = 0, bool $unique_check = false, int $level = 1): bool
	{
		if ($this->_logged) {
			return true;
		}

		if (!($this->_conf['log_level'] & $level)) {
			return true;
		}

		try {
			if (!is_object($this->_conn) || !$this->_conn instanceof \mysqli) {
				$this->_conn = @mysqli_connect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS);
				if (!$this->_conn) {
					throw new \Exception('Database connection failed');
				}
				if (!mysqli_select_db($this->_conn, XOOPS_DB_NAME)) {
					throw new \Exception('Database selection failed');
				}
			}

			$ip = $this->remote_ip;
			$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
			$uri = $_SERVER['REQUEST_URI'] ?? '';

			// Fix for local development environments
			if ($ip === '::1' || $ip === 'localhost' || $ip === '0:0:0:0:0:0:0:1') {
				$ip = '127.0.0.1';
			}

			if ($unique_check) {
				$result = mysqli_query($this->_conn, 'SELECT ip,type FROM ' . XOOPS_DB_PREFIX . '_' . $this->mydirname . '_log ORDER BY timestamp DESC LIMIT 1');
				if ($result) {
					[$last_ip, $last_type] = mysqli_fetch_row($result);
					if ($last_ip == $ip && $last_type == $type) {
						$this->_logged = true;
						return true;
					}
				}
			}

			// Use prepared statement with Unix timestamp
			$current_time = time();
			
			// Check if the uri column exists in the table
			$result = mysqli_query($this->_conn, "SHOW COLUMNS FROM " . XOOPS_DB_PREFIX . "_" . $this->mydirname . "_log LIKE 'uri'");
			$uri_column_exists = mysqli_num_rows($result) > 0;
			
			if ($uri_column_exists) {
				// Store IP directly as string instead of using INET_ATON
				$query = 'INSERT INTO ' . XOOPS_DB_PREFIX . '_' . $this->mydirname . '_log 
					(ip, agent, type, description, uid, timestamp, uri) 
					VALUES (?, ?, ?, ?, ?, ?, ?)';
				
				$stmt = mysqli_prepare($this->_conn, $query);
				
				if ($stmt) {
					mysqli_stmt_bind_param($stmt, 'ssssiss', $ip, $agent, $type, $this->message, $uid, $current_time, $uri);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
				} else {
					// Fallback to direct query if prepared statement fails
					$ip4sql = mysqli_real_escape_string($this->_conn, $ip);
					$agent4sql = mysqli_real_escape_string($this->_conn, $agent);
					$type4sql = mysqli_real_escape_string($this->_conn, $type);
					$message4sql = mysqli_real_escape_string($this->_conn, $this->message);
					$uri4sql = mysqli_real_escape_string($this->_conn, $uri);
					
					mysqli_query($this->_conn, 'INSERT INTO ' . XOOPS_DB_PREFIX . '_' . $this->mydirname . '_log 
						SET ip=\'' . $ip4sql . '\', 
						agent=\'' . $agent4sql . '\', 
						type=\'' . $type4sql . '\', 
						description=\'' . $message4sql . '\', 
						uid=' . (int)$uid . ', 
						timestamp=' . $current_time . ',
						uri=\'' . $uri4sql . '\'');
				}
			} else {
				// Use the old schema without the uri column
				$query = 'INSERT INTO ' . XOOPS_DB_PREFIX . '_' . $this->mydirname . '_log 
					(ip, agent, type, description, uid, timestamp) 
					VALUES (?, ?, ?, ?, ?, ?)';
				
				$stmt = mysqli_prepare($this->_conn, $query);
				
				if ($stmt) {
					mysqli_stmt_bind_param($stmt, 'ssssis', $ip, $agent, $type, $this->message, $uid, $current_time);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
				} else {
					// Fallback to direct query if prepared statement fails
					$ip4sql = mysqli_real_escape_string($this->_conn, $ip);
					$agent4sql = mysqli_real_escape_string($this->_conn, $agent);
					$type4sql = mysqli_real_escape_string($this->_conn, $type);
					$message4sql = mysqli_real_escape_string($this->_conn, $this->message);
					
					mysqli_query($this->_conn, 'INSERT INTO ' . XOOPS_DB_PREFIX . '_' . $this->mydirname . '_log 
						SET ip=\'' . $ip4sql . '\', 
						agent=\'' . $agent4sql . '\', 
						type=\'' . $type4sql . '\', 
						description=\'' . $message4sql . '\', 
						uid=' . (int)$uid . ', 
						timestamp=' . $current_time);
				}
			}

			$this->_logged = true;
			
			// Send notification for high-severity threats (level >= 32)
			if ($level >= 32 && file_exists(dirname(__DIR__) . '/include/notification.inc.php')) {
				require_once dirname(__DIR__) . '/include/notification.inc.php';
				$description = "Security threat detected: $type\n";
				$description .= "IP: " . $this->remote_ip . "\n";
				$description .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
				$description .= "User ID: " . $uid . "\n";
				$description .= "URI: " . $uri . "\n";
				
				// Get additional details if available
				if (!empty($this->message)) {
					$description .= "Details: " . $this->message . "\n";
				}
				
				protector_send_security_notification($type, $description);
			}
			
			return true;
		} catch (\Exception $e) {
			error_log('Protector log error: ' . $e->getMessage());
			// Make sure we still block the attack even if logging fails
			if ($level >= 16) {
				die('Protector detects attacking actions');
			}
			return false;
		}
	}

	// Improved file handling methods with better error handling
	public function write_file_bwlimit(int $expire): bool
	{
		$expire = min((int)$expire, time() + 300);

		try {
			$filepath = $this->get_filepath4bwlimit();
			$fp = @fopen($filepath, 'w');
			if (!$fp) {
				throw new \Exception("Cannot open file: $filepath");
			}

			@flock($fp, LOCK_EX);
			fwrite($fp, $expire . "\n");
			@flock($fp, LOCK_UN);
			fclose($fp);

			return true;
		} catch (\Exception $e) {
			error_log('Protector error: ' . $e->getMessage());
			return false;
		}
	}


	public function get_bwlimit(): int
	{
		$filepath = $this->get_filepath4bwlimit();
		if (!file_exists($filepath)) {
			return 0;
		}

		$content = file_get_contents($filepath);
		$expire = (int)$content;
		return min($expire, time() + 300);
	}

	public function get_filepath4bwlimit(): string
	{
		return XOOPS_TRUST_PATH . '/modules/protector/configs/bwlimit' .
			substr(md5(XOOPS_ROOT_PATH . XOOPS_DB_USER . XOOPS_DB_PREFIX), 0, 6);
	}

	public function write_file_badips(array $bad_ips): bool
	{
		asort($bad_ips);

		try {
			$filepath = $this->get_filepath4badips();
			$fp = @fopen($filepath, 'w');
			if (!$fp) {
				throw new \Exception("Cannot open file: $filepath");
			}

			@flock($fp, LOCK_EX);
			fwrite($fp, serialize($bad_ips) . "\n");
			@flock($fp, LOCK_UN);
			fclose($fp);

			return true;
		} catch (\Exception $e) {
			error_log('Protector error: ' . $e->getMessage());
			return false;
		}
	}

	public function register_bad_ips(int $jailed_time = 0, ?string $ip = null): bool
	{
		if (empty($ip)) {
			$ip = $this->get_remote_ip(@$this->_conf['banip_ipv6prefix']);
		}
		if (empty($ip)) {
			return false;
		}

		$bad_ips = $this->get_bad_ips(true);
		$bad_ips[$ip] = $jailed_time ?: 0x7fffffff;

		return $this->write_file_badips($bad_ips);
	}

	public function get_bad_ips(bool $with_jailed_time = false): array
	{
		$fbadips = $this->get_filepath4badips();

		if (!file_exists($fbadips)) {
			return [];
		}

		$content = file_get_contents($fbadips);
		if (empty($content)) {
			return [];
		}

		$bad_ips = @unserialize($content);
		if (!is_array($bad_ips) || isset($bad_ips[0])) {
			return [];
		}

		// Expire jailed_time entries
		$current_time = time();
		$filtered_ips = [];
		foreach ($bad_ips as $bad_ip => $jailed_time) {
			if ($jailed_time >= $current_time) {
				$filtered_ips[$bad_ip] = $jailed_time;
			}
		}

		if ($with_jailed_time) {
			return $filtered_ips;
		} else {
			return array_keys($filtered_ips);
		}
	}

	public function get_filepath4badips(): string
	{
		return XOOPS_TRUST_PATH . '/modules/protector/configs/badips' .
			substr(md5(XOOPS_ROOT_PATH . XOOPS_DB_USER . XOOPS_DB_PREFIX), 0, 6);
	}

	// Improved group1 IPs handling methods
	public function get_group1_ips(bool $with_info = false): array
	{
		$filepath = self::get_filepath4group1ips();

		if (!file_exists($filepath)) {
			return [];
		}

		$content = file_get_contents($filepath);
		if (empty($content)) {
			return [];
		}

		$group1_ips = @unserialize($content);
		if (!is_array($group1_ips)) {
			return [];
		}

		if ($with_info) {
			return array_flip($group1_ips);
		}

		return $group1_ips;
	}

	public static function get_filepath4group1ips(): string
	{
		return XOOPS_TRUST_PATH . '/modules/protector/configs/group1ips' .
			substr(md5(XOOPS_ROOT_PATH . XOOPS_DB_USER . XOOPS_DB_PREFIX), 0, 6);
	}

	public function get_filepath4confighcache(): string
	{
		return XOOPS_TRUST_PATH . '/modules/protector/configs/configcache' .
			substr(md5(XOOPS_ROOT_PATH . XOOPS_DB_USER . XOOPS_DB_PREFIX), 0, 6);
	}

	// Improved IP matching with better type handling
	public function ip_match(array $ips): bool
	{
		foreach ($ips as $ip => $info) {
			if (empty($ip)) {
				continue;
			}

			$last = substr($ip, -1);

			if ($this->is_ipv6) {
				$ip = strtolower($ip);
				if (preg_match('/^[0-9a-f:]$/', $last)) {
					$last = '';
				}
			}

			switch ($last) {
				case '':
				case '.':
					// Forward match
					if (substr($this->remote_ip, 0, strlen($ip)) === $ip) {
						$this->ip_matched_info = $info;
						return true;
					}
					break;

				case '0':
				case '1':
				case '2':
				case '3':
				case '4':
				case '5':
				case '6':
				case '7':
				case '8':
				case '9':
					// Full match
					if ($this->remote_ip === $ip) {
						$this->ip_matched_info = $info;
						return true;
					}
					break;

				default:
					// Perl regex
					if (@preg_match($ip, $this->remote_ip)) {
						$this->ip_matched_info = $info;
						return true;
					}
					break;
			}
		}

		$this->ip_matched_info = null;
		return false;
	}

	// Improved .htaccess handling for IP banning
	public function deny_by_htaccess(?string $ip = null): bool
	{
		$ip = $ip ?? $this->remote_ip;

		if (empty($ip)) {
			return false;
		}

		if (!function_exists('file_get_contents')) {
			return false;
		}

		$target_htaccess = XOOPS_ROOT_PATH . '/.htaccess';
		$backup_htaccess = XOOPS_ROOT_PATH . '/uploads/.htaccess.bak';

		try {
			// Read current .htaccess content
			$ht_body = @file_get_contents($target_htaccess);

			// Make backup automatically if it doesn't exist
			if ($ht_body && !file_exists($backup_htaccess)) {
				file_put_contents($backup_htaccess, $ht_body);
			}

			// If .htaccess is broken, restore from backup
			if (!$ht_body && file_exists($backup_htaccess)) {
				$ht_body = file_get_contents($backup_htaccess);
			}

			// Initialize if no content
			if (false === $ht_body) {
				$ht_body = '';
			}

			// Update or create the deny rule
			if (preg_match("/^(.*)#PROTECTOR#\s+(DENY FROM .*)\n#PROTECTOR#\n(.*)$/si", $ht_body, $regs)) {
				if (substr($regs[2], -strlen($ip)) == $ip) {
					return true; // IP already banned
				}
				$new_ht_body = $regs[1] . "#PROTECTOR#\n" . $regs[2] . " $ip\n#PROTECTOR#\n" . $regs[3];
			} else {
				$new_ht_body = "#PROTECTOR#\nDENY FROM $ip\n#PROTECTOR#\n" . $ht_body;
			}

			// Write the updated content
			file_put_contents($target_htaccess, $new_ht_body, LOCK_EX);

			return true;
		} catch (\Exception $e) {
			error_log('Protector htaccess error: ' . $e->getMessage());
			return false;
		}
	}

    // Improved database layer trap methods
	public function getDblayertrapDoubtfuls() {
		return $this->_dblayertrap_doubtfuls;
	}

	public function _dblayertrap_check_recursive( $val ) {
		if ( is_array( $val ) ) {
			foreach ( $val as $subval ) {
				$this->_dblayertrap_check_recursive( $subval );
			}
		} else {
			if ( strlen( $val ) < 6 ) {
				return;
			}
			foreach ( $this->_dblayertrap_doubtful_needles as $needle ) {
				if ( stristr( $val, (string) $needle ) ) {
					$this->_dblayertrap_doubtfuls[] = $val;
				}
			}
		}
	}

	/**
	 * @param bool $force_override
	 *
	 * @return null
	 */

	public function dblayertrap_init( $force_override = false ) {
		if ( ! empty( $GLOBALS['xoopsOption']['nocommon'] ) || defined( '_LEGACY_PREVENT_EXEC_COMMON_' ) || defined( '_LEGACY_PREVENT_LOAD_CORE_' ) ) {
			return;
		} // skip

		$this->_dblayertrap_doubtfuls = [];
		$this->_dblayertrap_check_recursive( $_GET );
		$this->_dblayertrap_check_recursive( $_POST );
		$this->_dblayertrap_check_recursive( $_COOKIE );
		if ( empty( $this->_conf['dblayertrap_wo_server'] ) ) {
			$this->_dblayertrap_check_recursive( $_SERVER );
		}

		if ( ! empty( $this->_dblayertrap_doubtfuls ) || $force_override ) {
			@define( 'XOOPS_DB_ALTERNATIVE', 'ProtectorMysqlDatabase' );
			require_once dirname( __DIR__ ) . '/class/ProtectorMysqlDatabase.class.php';
		}
	}

	/**
	 * @param $val
	 */
	protected function _bigumbrella_check_recursive( $val ) {
		if ( is_array( $val ) ) {
			foreach ( $val as $subval ) {
				$this->_bigumbrella_check_recursive( $subval );
			}
		} else {
			if (preg_match('/[<\'"].{15}/s', (string)$val, $regs)) {
				$this->_bigumbrella_doubtfuls[] = $regs[0];
			}
		}
	}

	public function bigumbrella_init() {
		$this->_bigumbrella_doubtfuls = [];
		$this->_bigumbrella_check_recursive( $_GET );
		$this->_bigumbrella_check_recursive( @$_SERVER['PHP_SELF'] );

		if ( ! empty( $this->_bigumbrella_doubtfuls ) ) {
			ob_start( [ $this, 'bigumbrella_outputcheck' ] );
		}
	}

	/**
	 * @param $s
	 *
	 * @return string
	 */
	public function bigumbrella_outputcheck( $s ) {
		if ( defined( 'BIGUMBRELLA_DISABLED' ) ) {
			return $s;
		}

		if ( function_exists( 'headers_list' ) ) {
			foreach ( headers_list() as $header ) {
				if ( stristr( $header, 'Content-Type:' ) && ! stristr( $header, 'text/html' ) ) {
					return $s;
				}
			}
		}

		if ( ! is_array( $this->_bigumbrella_doubtfuls ) ) {
			return 'bigumbrella injection found.';
		}

		foreach ( $this->_bigumbrella_doubtfuls as $doubtful ) {
			if ( strstr( $s, (string) $doubtful ) ) {
				return 'XSS found by Protector.';
			}
		}

		return $s;
	}

	// Improved request sanitization methods
	public function intval_allrequestsendid(): bool
	{
		if ($this->_done_intval) {
			return true;
		}

		$this->_done_intval = true;

		foreach ($_GET as $key => $val) {
			if ('id' == substr($key, -2) && !is_array($_GET[$key])) {
				$newval = preg_replace('/[^0-9a-zA-Z_-]/', '', $val);
				if (isset($_REQUEST[$key]) && $_REQUEST[$key] == $_GET[$key]) {
					$_REQUEST[$key] = $newval;
				}
				$_GET[$key] = $newval;
			}
		}

		foreach ($_POST as $key => $val) {
			if ('id' == substr($key, -2) && !is_array($_POST[$key])) {
				$newval = preg_replace('/[^0-9a-zA-Z_-]/', '', $val);
				if (isset($_REQUEST[$key]) && $_REQUEST[$key] == $_POST[$key]) {
					$_REQUEST[$key] = $newval;
				}
				$_POST[$key] = $newval;
			}
		}

		foreach ($_COOKIE as $key => $val) {
			if ('id' == substr($key, -2) && !is_array($_COOKIE[$key])) {
				$newval = preg_replace('/[^0-9a-zA-Z_-]/', '', $val);
				if (isset($_REQUEST[$key]) && $_REQUEST[$key] == $_COOKIE[$key]) {
					$_REQUEST[$key] = $newval;
				}
				$_COOKIE[$key] = $newval;
			}
		}

		return true;
	}

	public function eliminate_dotdot(): bool
	{
		if ($this->_done_dotdot) {
			return true;
		}

		$this->_done_dotdot = true;

		foreach ($_GET as $key => $val) {
			if (is_array($_GET[$key])) {
				continue;
			}
			if ('../' == substr(trim($val), 0, 3) || strstr($val, '../../')) {
				$this->last_error_type = 'DirTraversal';
				$this->message .= "Directory Traversal '$val' found.\n";
				$this->output_log($this->last_error_type, 0, false, 64);
				$sanitized_val = str_replace(chr(0), '', $val);
				if (' .' != substr($sanitized_val, -2)) {
					$sanitized_val .= ' .';
				}
				$_GET[$key] = $sanitized_val;
				if ($_REQUEST[$key] == $_GET[$key]) {
					$_REQUEST[$key] = $sanitized_val;
				}
			}
		}

		return true;
	}

	// Improved reference handling methods
	public function &get_ref_from_base64index(&$current, array $indexes): mixed
	{
		foreach ($indexes as $index) {
			$index = base64_decode($index);
			if (!is_array($current)) {
				$false = false;
				return $false;
			}
			$current = &$current[$index];
		}

		return $current;
	}

	// Improved request variable replacement
	public function replace_doubtful(string $key, $val): void
	{
		global $_GET, $_POST, $_COOKIE;

		$indexes = explode('_', $key);
		$base_array = array_shift($indexes);

		switch ($base_array) {
			case 'G':
				$main_ref = &$this->get_ref_from_base64index($_GET, $indexes);
				$legacy_ref = &$this->get_ref_from_base64index($_GET, $indexes);
				break;
			case 'P':
				$main_ref = &$this->get_ref_from_base64index($_POST, $indexes);
				$legacy_ref = &$this->get_ref_from_base64index($_POST, $indexes);
				break;
			case 'C':
				$main_ref = &$this->get_ref_from_base64index($_COOKIE, $indexes);
				$legacy_ref = &$this->get_ref_from_base64index($_COOKIE, $indexes);
				break;
			default:
				exit;
		}

		if (!isset($main_ref)) {
			exit;
		}

		$request_ref = &$this->get_ref_from_base64index($_REQUEST, $indexes);
		if (false !== $request_ref && $main_ref == $request_ref) {
			$request_ref = $val;
		}

		$main_ref = $val;
		$legacy_ref = $val;
	}

	// Improved file upload security check
	public function check_uploaded_files(): bool
	{
		if ($this->_done_badext) {
			return $this->_safe_badext;
		}

		$this->_done_badext = true;

		// Extensions never allowed to upload
		$bad_extensions = ['php', 'phtml', 'phtm', 'php3', 'php4', 'cgi', 'pl', 'asp'];

		// Extensions that need image validation
		$image_extensions = [
			IMAGETYPE_GIF => 'gif',
			IMAGETYPE_JPEG => 'jpg',
			IMAGETYPE_PNG => 'png',
			IMAGETYPE_SWF => 'swf',
			IMAGETYPE_PSD => 'psd',
			IMAGETYPE_BMP => 'bmp',
			IMAGETYPE_TIFF_II => 'tif',
			IMAGETYPE_TIFF_MM => 'tif',
			IMAGETYPE_JPC => 'jpc',
			IMAGETYPE_JP2 => 'jp2',
			IMAGETYPE_JPX => 'jpx',
			IMAGETYPE_JB2 => 'jb2',
			IMAGETYPE_SWC => 'swc',
			IMAGETYPE_IFF => 'iff',
			IMAGETYPE_WBMP => 'wbmp',
			IMAGETYPE_XBM => 'xbm'
		];

		foreach ($_FILES as $_file) {
			if (!empty($_file['error'])) {
				continue;
			}

			if (!empty($_file['name']) && is_string($_file['name'])) {
				$ext = strtolower(substr(strrchr($_file['name'], '.'), 1));

				// Normalize common extensions
				if ('jpeg' == $ext) {
					$ext = 'jpg';
				} elseif ('tiff' == $ext) {
					$ext = 'tif';
				} elseif ('swc' == $ext) {
					$ext = 'swf';
				}

				// Check for multiple dot files (Apache mod_mime.c vulnerability)
				if (count(explode('.', str_replace('.tar.gz', '.tgz', $_file['name']))) > 2) {
					$this->message .= "Attempt to upload multiple dot file {$_file['name']}.\n";
					$this->_safe_badext = false;
					$this->last_error_type = 'UPLOAD';
				}

				// Check for dangerous extensions
				if (in_array($ext, $bad_extensions, true)) {
					$this->message .= "Attempt to upload {$_file['name']}.\n";
					$this->_safe_badext = false;
					$this->last_error_type = 'UPLOAD';
				}

				// Verify image files are actually valid images
				if (in_array($ext, $image_extensions, true)) {
					$image_attributes = @getimagesize($_file['tmp_name']);

					// Handle open_basedir restrictions
					if (false === $image_attributes && is_uploaded_file($_file['tmp_name'])) {
						$temp_file = XOOPS_ROOT_PATH . '/uploads/protector_upload_temporary' . md5(time());
						move_uploaded_file($_file['tmp_name'], $temp_file);
						$image_attributes = @getimagesize($temp_file);
						@unlink($temp_file);
					}

					$imagetype = (int)($image_attributes[2] ?? 0);
					if (IMAGETYPE_SWC == $imagetype) {
						$imagetype = IMAGETYPE_SWF;
					}

					if (false === $image_attributes || !isset($image_extensions[$imagetype]) || $image_extensions[$imagetype] != $ext) {
						$this->message .= "Attempt to upload camouflaged image file {$_file['name']}.\n";
						$this->_safe_badext = false;
						$this->last_error_type = 'UPLOAD';
					}
				}
			}
		}

		return $this->_safe_badext;
	}

	// Improved security check methods
	public function check_contami_systemglobals(): bool
	{
		return $this->_safe_contami;
	}

	// Implemented logging method before filter
	public function check_sql_isolatedcommentin(bool $sanitize = true): bool
	{
		if ($this->_done_isocom) {
			return $this->_safe_isocom;
		}

		$this->_done_isocom = true;

		foreach ($this->_doubtful_requests as $key => $val) {
			$str = $val;
			while ($str = strstr($str, '/*')) {
				$str = strstr(substr($str, 2), '*/');
				if (false === $str) {
					$this->message .= "Isolated comment-in found. ($val)\n";
					
					// Check if sanitizing is enabled in config
					if ($sanitize && !empty($this->_conf['isocom_action']) && $this->_conf['isocom_action'] !== 'none') {
						$this->replace_doubtful($key, $val . '*/');
					}
					
					$this->_safe_isocom = false;
					$this->last_error_type = 'ISOCOM';
					
					// Force immediate logging with high priority
					$this->_logged = false; // Reset logged flag to ensure this gets logged
					$this->output_log('ISOCOM', 0, false, 128); // Use highest log level to ensure it's captured
					
					// Make sure the log is written immediately
					if (is_object($this->_conn) && $this->_conn instanceof \mysqli) {
						mysqli_commit($this->_conn);
					}
					
					// If config is set to "none", we should just log and continue
					if (empty($this->_conf['isocom_action']) || $this->_conf['isocom_action'] === 'none') {
						// Just log, don't redirect or block
						return false;
					}
				}
			}
		}

		return $this->_safe_isocom;
	}

	public function check_sql_union(bool $sanitize = true): bool
	{
		if ($this->_done_union) {
			return $this->_safe_union;
		}

		$this->_done_union = true;

		foreach ($this->_doubtful_requests as $key => $val) {
			$str = str_replace(['/*', '*/'], '', preg_replace('?/\*.+\*/?sU', '', $val));
			if (preg_match('/\sUNION\s+(ALL|SELECT)/i', $str)) {
				$this->message .= "Pattern like SQL injection found. ($val)\n";
				if ($sanitize) {
					$this->replace_doubtful($key, preg_replace('/union/i', 'uni-on', $val));
				}
				$this->_safe_union = false;
				$this->last_error_type = 'UNION';
			}
		}

		return $this->_safe_union;
	}

	// Improved DoS attack detection
	public function check_dos_attack(int $uid = 0, bool $can_ban = false): bool {
		global $xoopsDB;

		if ($this->_done_dos) {
			return true;
		}

		$ip = $this->remote_ip;
		$uri = $_SERVER['REQUEST_URI'] ?? '';
		$ip4sql = addslashes($ip);
		$uri4sql = addslashes(substr($uri, 0, 191));
		
		if (empty($ip)) {
			return true;
		}

		// Garbage collection
		$result = $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix($this->mydirname . '_access') . ' WHERE expire < UNIX_TIMESTAMP()');

		// For older versions before updating this module
		if (false === $result) {
			$this->_done_dos = true;
			return true;
		}

		// SQL for recording access log (INSERT should be placed after SELECT)
		$sql4insertlog = 'INSERT INTO ' . $xoopsDB->prefix($this->mydirname . '_access') .
			" SET ip='$ip4sql',request_uri='$uri4sql',expire=UNIX_TIMESTAMP()+'" . (int)$this->_conf['dos_expire'] . "'";

		// Bandwidth limitation
		if (isset($this->_conf['bwlimit_count']) && $this->_conf['bwlimit_count'] >= 10) {
			$result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix($this->mydirname . '_access'));
			[$bw_count] = $xoopsDB->fetchRow($result);
			if ($bw_count > $this->_conf['bwlimit_count']) {
				$this->write_file_bwlimit(time() + $this->_conf['dos_expire']);
			}
		}

		// F5 attack check (High load & same URI)
		$result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix($this->mydirname . '_access') .
			" WHERE ip='$ip4sql' AND request_uri='$uri4sql'");
		[$f5_count] = $xoopsDB->fetchRow($result);
		
		if ($f5_count > $this->_conf['dos_f5count']) {
			// Delayed insert
			$xoopsDB->queryF($sql4insertlog);

			// Call the filter first
			$ret = $this->call_filter('f5attack_overrun');

			// Actions for F5 Attack
			$this->_done_dos = true;
			$this->last_error_type = 'DoS';
			
			switch ($this->_conf['dos_f5action']) {
				default:
				case 'exit':
					$this->output_log($this->last_error_type, $uid, true, 16);
					exit;
				case 'none':
					$this->output_log($this->last_error_type, $uid, true, 16);
					return true;
				case 'biptime0':
					if ($can_ban) {
						$this->register_bad_ips(time() + $this->_conf['banip_time0']);
					}
					break;
				case 'bip':
					if ($can_ban) {
						$this->register_bad_ips();
					}
					break;
				case 'hta':
					if ($can_ban) {
						$this->deny_by_htaccess();
					}
					break;
				case 'sleep':
					sleep(5);
					break;
			}

			return false;
		}
		
		// Add delayed insert for normal access
		$xoopsDB->queryF($sql4insertlog);
		$this->_done_dos = true;
		
		return true;
	}


	// Improved brute force detection
	public function check_brute_force(): bool
	{
		global $xoopsDB;

		$ip = $this->remote_ip;
		$uri = $_SERVER['REQUEST_URI'] ?? '';
		$ip4sql = addslashes($ip);
		$uri4sql = addslashes(substr($uri, 0, 191));

		if (empty($ip)) {
			return true;
		}

		$victim_uname = $_COOKIE['autologin_uname'] ?? ($_POST['uname'] ?? '');
		// Some UA send 'deleted' as a value of the deleted cookie
		if ('deleted' == $victim_uname) {
			return true;
		}

		$mal4sql = addslashes("BRUTE FORCE: $victim_uname");

		// Garbage collection
		$result = $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix($this->mydirname . '_access') . ' WHERE expire < UNIX_TIMESTAMP()');

		// SQL for recording access log
		$sql4insertlog = 'INSERT INTO ' . $xoopsDB->prefix($this->mydirname . '_access') .
			" SET ip='$ip4sql',request_uri='$uri4sql',malicious_actions='$mal4sql',expire=UNIX_TIMESTAMP()+600";

		// Count check
		$result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix($this->mydirname . '_access') .
			" WHERE ip='$ip4sql' AND malicious_actions like 'BRUTE FORCE:%'");
		[$bf_count] = $xoopsDB->fetchRow($result);

		if ($bf_count > $this->_conf['bf_count']) {
			$this->register_bad_ips(time() + $this->_conf['banip_time0']);
			$this->last_error_type = 'BruteForce';
			$this->message .= "Trying to login as '" . addslashes($victim_uname) . "' found.\n";
			$this->output_log('BRUTE FORCE', 0, true, 1);
			$ret = $this->call_filter('bruteforce_overrun');
			if (false === $ret) {
				exit;
			}
		}

		// Delayed insert
		$xoopsDB->queryF($sql4insertlog);

		return true;
	}

	// Improved spam checking methods
	private function _spam_check_point_recursive($val): void
	{
		if (is_array($val)) {
			foreach ($val as $subval) {
				$this->_spam_check_point_recursive($subval);
			}
		} else {
			// Get HTTP host
			$path_array = parse_url(XOOPS_URL);
			$http_host = $path_array['host'] ?? 'www.xoops.org';

			// Count URI occurrences
			$count = -1;
			foreach (preg_split('#https?\:\/\/#i', (string)$val) as $fragment) {
				if (0 !== strncmp($fragment, $http_host, strlen($http_host))) {
					++$count;
				}
			}

			if ($count > 0) {
				$this->_spamcount_uri += $count;
			}

			// Count BBCode like [url=www....] (without [url=https://...])
			$split_count = is_countable(preg_split('/\[url=(?!http|\\"http|\\\'http|' . $http_host . ')/i', (string)$val))
				? count(preg_split('/\[url=(?!http|\\"http|\\\'http|' . $http_host . ')/i', (string)$val))
				: 0;

			$this->_spamcount_uri += $split_count - 1;
		}
	}

	public function spam_check(int $points4deny, int $uid): bool
	{
		$this->_spamcount_uri = 0;
		$this->_spam_check_point_recursive($_POST);

		if ($this->_spamcount_uri >= $points4deny) {
			$this->message .= ($_SERVER['REQUEST_URI'] ?? '') . " SPAM POINT: $this->_spamcount_uri\n";
			$this->output_log('URI SPAM', $uid, false, 128);
			$ret = $this->call_filter('spamcheck_overrun');
			if (false === $ret) {
				exit;
			}
			return false;
		}

		return true;
	}

	// Improved site manipulation check
	public function check_manipulation(): bool
	{
		if ($_SERVER['SCRIPT_FILENAME'] == XOOPS_ROOT_PATH . '/index.php') {
			$root_stat = stat(XOOPS_ROOT_PATH);
			$index_stat = stat(XOOPS_ROOT_PATH . '/index.php');
			$finger_print = $root_stat['mtime'] . ':' . $index_stat['mtime'] . ':' . $index_stat['ino'];

			if (empty($this->_conf['manip_value'])) {
				$this->updateConfIntoDb('manip_value', $finger_print);
			} elseif ($finger_print != $this->_conf['manip_value']) {
				// Notify if finger_print is different from old one
				$ret = $this->call_filter('postcommon_manipu');
				if (false === $ret) {
					die('Protector detects site manipulation.');
				}
				$this->updateConfIntoDb('manip_value', $finger_print);
			}
		}

		return true;
	}

	// Improved feature disabling method
	public function disable_features(): void
	{
		global $_POST, $_GET, $_COOKIE;

		// Disable "Notice: Undefined index: ..." temporarily
		$error_reporting_level = error_reporting(0);

		// Bit 1: Disable XMLRPC and criteria bug
		if ($this->_conf['disable_features'] & 1) {
			// Disable xmlrpc.php in root
			if ('xmlrpc.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -10)) {
				$this->output_log('xmlrpc', 0, true, 1);
				exit;
			}

			// Security bug of Xoops2 class/criteria.php
			if ((isset($_POST['uname']) && '0' === $_POST['uname']) ||
				(isset($_COOKIE['autologin_pass']) && '0' === $_COOKIE['autologin_pass'])
			) {
				$this->output_log('CRITERIA');
				exit;
			}
		}

		// Bit 11: XSS+CSRFs in XOOPS < 2.0.10
		if ($this->_conf['disable_features'] & 1024) {
			// Root controllers
			if (!stristr($_SERVER['SCRIPT_NAME'] ?? '', 'modules')) {
				// Misc.php debug (file check)
				if (
					'misc.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -8) &&
					(($_GET['type'] ?? '') == 'debug' || ($_POST['type'] ?? '') == 'debug') &&
					!preg_match('/^dummy_[0-9]+\.html$/', $_GET['file'] ?? '')
				) {
					$this->output_log('misc debug');
					exit;
				}

				// Misc.php smilies
				if (
					'misc.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -8) &&
					(($_GET['type'] ?? '') == 'smilies' || ($_POST['type'] ?? '') == 'smilies') &&
					!preg_match('/^[0-9a-z_]*$/i', $_GET['target'] ?? '')
				) {
					$this->output_log('misc smilies');
					exit;
				}

				// Edituser.php avatarchoose
				if (
					'edituser.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -12) &&
					($_POST['op'] ?? '') == 'avatarchoose' &&
					strstr($_POST['user_avatar'] ?? '', '..')
				) {
					$this->output_log('edituser avatarchoose');
					exit;
				}
			}

			// Findusers
			if (
				'modules/system/admin.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -24) &&
				(($_GET['fct'] ?? '') == 'findusers' || ($_POST['fct'] ?? '') == 'findusers')
			) {
				foreach ($_POST as $key => $val) {
					if (strstr($key, "'") || strstr($val, "'")) {
						$this->output_log('findusers');
						exit;
					}
				}
			}

			// Preview CSRF protection
			// News submit.php
			if (
				'modules/news/submit.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -23) &&
				isset($_POST['preview']) &&
				0 !== strpos($_SERVER['HTTP_REFERER'] ?? '', XOOPS_URL . '/modules/news/submit.php')
			) {
				$_POST['nohtml'] = 1;
			}

			// News admin/index.php
			if (
				'modules/news/admin/index.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -28) &&
				(($_POST['op'] ?? '') == 'preview' || ($_GET['op'] ?? '') == 'preview') &&
				0 !== strpos($_SERVER['HTTP_REFERER'] ?? '', XOOPS_URL . '/modules/news/admin/index.php')
			) {
				$_POST['nohtml'] = 1;
			}

			// Comment comment_post.php
			if (
				isset($_POST['com_dopreview']) &&
				!strstr(substr($_SERVER['HTTP_REFERER'] ?? '', -16), 'comment_post.php')
			) {
				$_POST['dohtml'] = 0;
			}

			// Disable preview of system's blocksadmin
			if (
				'modules/system/admin.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -24) &&
				(($_GET['fct'] ?? '') == 'blocksadmin' || ($_POST['fct'] ?? '') == 'blocksadmin') &&
				isset($_POST['previewblock'])
			) {
				die("Danger! don't use this preview. Use 'altsys module' instead.(by Protector)");
			}

			// Template preview
			if (
				'modules/system/admin.php' == substr($_SERVER['SCRIPT_NAME'] ?? '', -24) &&
				(($_GET['fct'] ?? '') == 'tplsets' || ($_POST['fct'] ?? '') == 'tplsets')
			) {
				if (($_POST['op'] ?? '') == 'previewpopup' ||
					($_GET['op'] ?? '') == 'previewpopup' ||
					isset($_POST['previewtpl'])
				) {
					die("Danger! don't use this preview.(by Protector)");
				}
			}
		}

		// Restore reporting level
		error_reporting($error_reporting_level);
	}

	// Improved filter calling method
	public function call_filter(string $type, string $dying_message = ''): bool
	{
		// Special handling for isolated comments based on configuration
		if ($type === 'isocom_crawler' && (!empty($this->_conf['isocom_action']) && $this->_conf['isocom_action'] === 'none')) {
			// If config is set to "none", don't call the filter, just return true to continue
			return true;
		}
		
		require_once __DIR__ . '/ProtectorFilter.php';
		$filter_handler = ProtectorFilterHandler::getInstance();
		$ret = $filter_handler->execute($type);

		if (false === $ret && $dying_message) {
			die($dying_message);
		}

		return $ret;
	}

	// Improved IP detection with IPv6 support
	protected function get_remote_ip(int $ipv6prefix = 0, bool $ipv4 = true): string
	{
		$ip = $_SERVER['REMOTE_ADDR'] ?? '';

		if (false === strpos($ip, ':')) {
			return $ipv4 ? $ip : '';
		}

		// Handle IPv6 address
		$ip = strtolower($ip);
		$fulls = [];
		$fields = explode(':', $ip);

		if (false !== strpos($ip, '::')) {
			$inscnt = 9 - count($fields);
			foreach ($fields as $i => $field) {
				if ('' === $field) {
					if (0 === $i) {
						$fulls[] = '0000';
					} else {
						$fulls = array_merge($fulls, array_pad([], $inscnt, '0000'));
					}
				} else {
					$fulls[] = str_pad($field, 4, '0', STR_PAD_LEFT);
				}
			}
		} else {
			foreach ($fields as $field) {
				$fulls[] = str_pad($field, 4, '0', STR_PAD_LEFT);
			}
		}

		$full = implode('', $fulls);

		if ($ipv6prefix && $ipv6prefix < 128) {
			$full = substr($full, 0, $ipv6prefix / 4);
		}

		$fulls = str_split($full, 4);
		$fullip = implode(':', $fulls);

		return $fullip;
	}
}
