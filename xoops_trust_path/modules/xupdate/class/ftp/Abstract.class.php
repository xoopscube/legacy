<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2024 The XOOPSCube Project
 * @license GPL v2.0
 */

if ( ! defined( 'CRLF' ) ) {
	define( 'CRLF', "\r\n" );
}
if ( ! defined( 'FTP_AUTOASCII' ) ) {
	define( 'FTP_AUTOASCII', - 1 );
}
if ( ! defined( 'FTP_BINARY' ) ) {
	define( 'FTP_BINARY', 1 );
}
if ( ! defined( 'FTP_ASCII' ) ) {
	define( 'FTP_ASCII', 0 );
}
if ( ! defined( 'FTP_FORCE' ) ) {
	define( 'FTP_FORCE', true );
}
if ( ! defined( 'FTP_OS_Unix' ) ) {
	define( 'FTP_OS_Unix', 'u' );
}
if ( ! defined( 'FTP_OS_Windows' ) ) {
	define( 'FTP_OS_Windows', 'w' );
}
if ( ! defined( 'FTP_OS_Mac' ) ) {
	define( 'FTP_OS_Mac', 'm' );
}

// Abstract Class for xupdate ftp
class Xupdate_Ftp_Abstract {

	/* xupdate module variables */
	protected $XupdateObj = null;    // xupdate module object
	protected $mod_config;

	/* Public variables */
	protected $LocalEcho;
	protected $Verbose;
	protected $OS_local = FTP_OS_Unix;
	protected $OS_remote = FTP_OS_Unix;
	public $exploredDirPath;

	/* Private variables */
	protected $_lastaction = null;
	protected $_errors;
	protected $_type;
	protected $_umask;
	protected $_timeout;
	protected $_passive;
	protected $_host;
	protected $_fullhost;
	protected $_port;
	protected $_datahost;
	protected $_dataport;
	protected $_ftp_control_sock;
	protected $_ftp_data_sock;
	protected $_ftp_temp_sock;
	protected $_ftp_buff_size = 4096;
	protected $_login = 'anonymous';
	protected $_password = 'anon@ftp.com';
	protected $_connected = false;
	protected $_ready = false;
	protected $_code = 0;
	protected $_message = '';
	protected $_can_restore = false;
	protected $_port_available;
	protected $_curtype = null;
	protected $_features = [];

	protected $_conn_id; // connection id stream on PHP_FTP

	protected $_error_array;
	protected $AuthorizedTransferMode;
	protected $OS_FullName;
	protected $_eol_code;
	protected $AutoAsciiExt;

	protected $mes = '';

	protected $item_options;
	protected $no_overwrite;

	/* Constructor */
	public function __construct( $XupdateObj, $verb = false, $le = false, $port_mode = false ) {
		// ToDo Cube流に直し
		$this->XupdateObj =& $XupdateObj;
		$this->mod_config =& $this->XupdateObj->mod_config;

		$this->LocalEcho              = $le;
		$this->Verbose                = $verb;
		$this->_lastaction            = null;
		$this->_error_array           = [];
		$this->_eol_code              = [ FTP_OS_Unix => "\n", FTP_OS_Mac => "\r", FTP_OS_Windows => "\r\n" ];
		$this->AuthorizedTransferMode = [ FTP_AUTOASCII, FTP_ASCII, FTP_BINARY ];
		$this->OS_FullName            = [ FTP_OS_Unix => 'UNIX', FTP_OS_Windows => 'WINDOWS', FTP_OS_Mac => 'MACOS' ];
		$this->AutoAsciiExt           = [
			'ASP',
			'BAT',
			'C',
			'CPP',
			'CSS',
			'CSV',
			'JS',
			'H',
			'HTM',
			'HTML',
			'SHTML',
			'INI',
			'LOG',
			'PHP3',
			'PHTML',
			'PL',
			'PERL',
			'SH',
			'SQL',
			'TXT'
		];
		$this->_port_available        = ( true == $port_mode );
		$this->SendMSG( 'Staring FTP client class' . ( $this->_port_available ? '' : ' without PORT mode support' ) );
		$this->_connected     = false;
		$this->_ready         = false;
		$this->_can_restore   = false;
		$this->_code          = 0;
		$this->_message       = '';
		$this->_ftp_buff_size = 4096;
		$this->_curtype       = null;
		$this->SetUmask( 0022 );
		$this->SetType( FTP_AUTOASCII );
		$this->SetTimeout( 30 );
		//$this->Passive(!$this->_port_available);
		$this->_login    = 'anonymous';
		$this->_password = 'anon@ftp.com';
		$this->_features = [];
		$this->OS_local  = FTP_OS_Unix;
		$this->OS_remote = FTP_OS_Unix;
		$this->features  = [];
		if ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) ) ) {
			$this->OS_local = FTP_OS_Windows;
		} elseif ( 'MAC' === strtoupper( substr( PHP_OS, 0, 3 ) ) ) {
			$this->OS_local = FTP_OS_Mac;
		}

		$this->_conn_id = null; // connection id stream on PHP_FTP
	}

	/**
	 * 以下、FTP ライブラリのオリジナルメソッド
	 *
	 * @param $list
	 *
	 * @return bool
	 */

	protected function parselisting( $list ) {
		return false;
	}

	protected function SendMSG( $message = '', $crlf = true ) {
		if ( $this->Verbose ) {
			$this->mes .= $message . ( $crlf ? CRLF . '<br>' : '' );
			if ( $this->LocalEcho ) {
				flush();
			}
		}

		return true;
	}

	protected function SetType( $mode = FTP_AUTOASCII ) {
		return false;
	}

	protected function _settype( $mode = FTP_ASCII ) {
		return false;
	}

	protected function Passive( $pasv = null ) {
		return false;
	}

	protected function SetServer( $host, $port = 21, $reconnect = true ) {
		if ( ! is_int( $port ) ) {
			//$this->verbose=true;
			$this->SendMSG( 'Incorrect port syntax' );

			return false;
		} else {
			$ip  = @gethostbyname( $host );
			$dns = @gethostbyaddr( $host );
			if ( ! $ip ) {
				$ip = $host;
			}
			if ( ! $dns ) {
				$dns = $host;
			}
			if ( - 1 === ip2long( $ip ) ) {
				$this->SendMSG( 'Wrong host name/address "' . $host . '"' );

				return false;
			}
			$this->_host     = $ip;
			$this->_fullhost = $dns;
			$this->_port     = $port;
			$this->_dataport = $port - 1;
		}
		$this->SendMSG( 'Host "' . $this->_fullhost . '(' . $this->_host . '):' . $this->_port . '"' );
		if ( $reconnect ) {
			if ( $this->_connected ) {
				$this->SendMSG( 'Reconnecting' );
				if ( ! $this->quit( FTP_FORCE ) ) {
					return false;
				}
				if ( ! $this->connect() ) {
					return false;
				}
			}
		}

		return true;
	}

	protected function SetUmask( $umask = 0022 ) {
		return false;
	}

	protected function SetTimeout( $timeout = 30 ) {
		return false;
	}

	protected function connect( $server = null ) {
		return false;
	}

	protected function ssl_connect( $server = null ) {
		return false;
	}

	protected function quit( $force = false ) {
		return false;
	}

	protected function login( $user = null, $pass = null ) {
		return false;
	}

	protected function pwd() {
		return false;
	}

	protected function cdup() {
		return false;
	}

	protected function chdir( $pathname ) {
		return false;
	}

	protected function rmdir( $pathname ) {
		return false;
	}

	protected function mkdir( $pathname ) {
		return false;
	}

	protected function rename( $from, $to ) {
		return false;
	}

	protected function filesize( $pathname ) {
		return false;
	}

	protected function abort() {
		return false;
	}

	protected function mdtm( $pathname ) {
		return false;
	}

	protected function systype() {
		return false;
	}

	protected function delete( $pathname ) {
		return false;
	}

	protected function site( $command, $fnction = 'site' ) {
		return false;
	}

	protected function chmod( $pathname, $mode ) {
		return false;
	}

	protected function restore( $from ) {
		return false;
	}

	protected function features() {
		return false;
	}

	protected function rawlist( $pathname = '', $arg = '' ) {
		return false;
	}

	protected function nlist( $pathname = '' ) {
		return false;
	}

	protected function is_exists( $pathname ) {
		return false;
	}

	protected function file_exists( $pathname ) {
		return false;
	}

	protected function get( $remotefile, $localfile = null, $rest = 0 ) {
		return false;
	}

	protected function put( $localfile, $remotefile = null, $rest = 0 ) {
		return false;
	}

	protected function mput( $local = '.', $remote = null, $continious = false ) {
		return false;
	}

	protected function mget( $remote, $local = '.', $continious = false ) {
		return false;
	}

	protected function mdel( $remote, $continious = false ) {
		return false;
	}

	protected function mmkdir( $dir, $mode = 0777 ) {
		return false;
	}

	protected function glob( $pattern, $handle = null ) {
		return false;
	}

	protected function glob_pattern_match( $pattern, $string ) {
		return false;
	}

	protected function glob_regexp( $pattern, $probe ) {
		return false;
	}

// <!-- --------------------------------------------------------------------------------------- -->
// <!--       Private functions                                                                 -->
// <!-- --------------------------------------------------------------------------------------- -->
	protected function _checkCode() {
		return false;
	}

	protected function _list( $arg = '', $cmd = 'LIST', $fnction = '_list' ) {
		return false;
	}

// <!-- --------------------------------------------------------------------------------------- -->
// <!-- Partie : gestion des erreurs                                                            -->
// <!-- --------------------------------------------------------------------------------------- -->
// Genere une erreur pour traitement externe a la classe
	protected function PushError( $fctname, $msg, $desc = false ) {
		return false;
	}

// Recupere une erreur externe
	protected function PopError() {
		return false;
	}

//
	protected function _settimeout( $sock ) {
		return false;
	}

	protected function _connect( $host, $port ) {
		return false;
	}

	protected function _readmsg( $fnction = '_readmsg' ) {
		return false;
	}

	protected function _exec( $cmd, $fnction = '_exec' ) {
		return false;
	}

	protected function _data_prepare( $mode = FTP_ASCII ) {
		return false;
	}

	protected function _data_read( $mode = FTP_ASCII, $fp = null ) {
		return false;
	}

	protected function _data_write( $mode = FTP_ASCII, $fp = null ) {
		return false;
	}

	protected function _data_write_block( $mode, $block ) {
		return false;
	}

	protected function _data_close() {
		return false;
	}

	protected function _quit( $force = false ) {
		return false;
	}
} // end class
;
