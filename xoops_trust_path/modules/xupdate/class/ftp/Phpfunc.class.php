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

include_once __DIR__ . '/Abstract.class.php';

class Xupdate_Ftp_ extends Xupdate_Ftp_Abstract {

	/* Constructor */
	public function __construct( $XupdateObj, $port_mode = false, $verb = false, $le = false ) {
		parent::__construct( $XupdateObj );
	}

// <!-- --------------------------------------------------------------------------------------- -->
// <!--       Public functions                                                                  -->
// <!-- --------------------------------------------------------------------------------------- -->
	/**
	 * app_login
	 *
	 * @param string $server
	 *
	 * @return    bool
	 **/
	public function app_login( $server ) {
		$ftp_use_ssl = $this->mod_config['FTP_SSL'];
		$ftp_id      = $this->mod_config['FTP_UserName'];
		$ftp_pass    = $this->mod_config['FTP_password'];

		// LOGIN

		$this->Verbose   = true;
		$this->LocalEcho = false;
		//$this->Passive(TRUE);

		if ( ! $this->SetServer( $server, 21, true ) ) {
			$this->quit();
			$this->mes .= "Setiing server failed<br>\n";

			return false;
		}

		if ( $ftp_use_ssl ) {
			$ftp_connected = $this->ssl_connect();
		} else {
			$ftp_connected = $this->connect();
		}
		if ( true !== $ftp_connected ) {
			$this->mes .= "Cannot connect<br>\n";

			return false;
		} else {
			if ( true !== $this->login( $ftp_id, $ftp_pass ) ) {
				$this->mes .= "login failed<br>\n";

				return false;
			} else {
				$this->mes .= "login succeeded<br>\n";
			}
		}
		if ( ! $this->Passive( true ) ) {
			$this->mes .= "Passive FAILS!<br>\n";
		}

		// SET BINARY MODE
		if ( ! $this->SetType( FTP_BINARY ) ) {
			$this->mes .= "Binary mode FAILS!<br>\n";
		}//bugfix 'FTP_BINARY'->FTP_BINARY

		$this->mes .= 'PWD:';
		$this->pwd();

		return true;
	}

	protected function SetType( $mode = FTP_AUTOASCII ) {
		if ( ! in_array( $mode, $this->AuthorizedTransferMode ) ) {
			$this->SendMSG( 'Wrong type' );

			return false;
		}
		$this->_type = $mode;
		$this->SendMSG( 'Transfer type: ' . ( FTP_BINARY == $this->_type ? 'binary' : ( FTP_ASCII == $this->_type ? 'ASCII' : 'auto ASCII' ) ) );

		return true;
	}

	protected function Passive( $pasv = false ) {
		//fix Fatal error: Call to undefined function ftp_pasv()
		if ( ! function_exists( 'ftp_pasv' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_pasv' );

			return false;
		}
		$ret = ftp_pasv( $this->_conn_id, $pasv );
		$this->SendMSG( 'Passive mode ' . ( $pasv ? 'on' : 'off' ) );

		return $ret;
	}

	protected function connect( $server = null ) {
		if ( ! empty( $server ) ) {
			if ( ! $this->SetServer( $server ) ) {
				return false;
			}
		}
		//fix Fatal error: Call to undefined function ftp_connect()
		if ( ! function_exists( 'ftp_connect' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_connect' );

			return false;
		}
		$this->_conn_id = ftp_connect( $this->_host );
		if ( false === $this->_conn_id ) {
			return false;
		} else {
			$this->_ready = true;

			return true;
		}

		return false;
	}

	protected function ssl_connect( $server = null ) {
		if ( ! empty( $server ) ) {
			if ( ! $this->SetServer( $server ) ) {
				return false;
			}
		}
		if ( ! function_exists( 'ftp_ssl_connect' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_ssl_connect' );

			return false;
		}
		$this->_conn_id = ftp_ssl_connect( $this->_host );
		if ( false === $this->_conn_id ) {
			return false;
		} else {
			$this->_ready = true;

			return true;
		}

		return false;
	}

	protected function login( $user = null, $pass = null ) {
		$this->_login    = $user ?? 'anonymous';
		$this->_password = $pass ?? 'anonymous@example.com';
		if ( ! function_exists( 'ftp_login' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_login' );

			return false;
		}
		//adump($this->_conn_id);
		//adump($this->_login);
		//adump($this->_password);
		if ( true === ftp_login( $this->_conn_id, $this->_login, $this->_password ) ) {
			$this->SendMSG( 'Authentication succeeded' );

			return true;
		} else {
			$this->SendMSG( 'Authentication failed' );

			return false;
		}
	}

	protected function quit( $force = false ) {
		if ( ! function_exists( 'ftp_close' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_close' );

			return false;
		}

		return ftp_close( $this->_conn_id );
	}

	protected function pwd() {
		if ( ! function_exists( 'ftp_pwd' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_pwd' );

			return false;
		}
		if ( false == ( $rtn = ftp_pwd( $this->_conn_id ) ) ) {
			$this->SendMSG( 'pwd command failed' . CRLF );

			return false;
		} else {
			//fix ereg_replace -> preg_replace for php5.3+
			$this->SendMSG( preg_replace( '/^[0-9]{3} "(.+)" .+' . CRLF . '/', "\\1", $rtn ) );

			return true;
		}
	}

	protected function cdup() {
		if ( ! function_exists( 'ftp_cdup' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_cdup' );

			return false;
		}

		return ftp_cdup( $this->_conn_id );
	}

	protected function chdir( $pathname ) {
		if ( ! function_exists( 'ftp_chdir' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_chdir' );

			return false;
		}

		// hide error (@) for Xupdate_Ftp::seekFTPRoot()
		return @ftp_chdir( $this->_conn_id, $pathname );
	}

	protected function rmdir( $pathname ) {
		if ( ! function_exists( 'ftp_rmdir' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_rmdir' );

			return false;
		}

		return ftp_rmdir( $this->_conn_id, $pathname );
	}

	protected function mkdir( $pathname ) {
		if ( ! function_exists( 'ftp_mkdir' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_mkdir' );

			return false;
		}
		//$pathname = str_replace( '/','\\',$pathname );
		//adump($pathname);
		return ftp_mkdir( $this->_conn_id, $pathname );
	}

	protected function rename( $from, $to ) {
		if ( ! function_exists( 'ftp_rename' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_rename' );

			return false;
		}

		return ftp_rename( $this->_conn_id, $from, $to );
	}

	protected function filesize( $pathname ) {
		if ( ! function_exists( 'ftp_size' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_size' );

			return false;
		}

		return ftp_size( $this->_conn_id, $pathname );
	}

	protected function mdtm( $pathname ) {
		if ( ! function_exists( 'ftp_mdtm' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_mdtm' );

			return false;
		}

		return ftp_mdtm( $this->_conn_id, $pathname );    // timestamp
	}

	protected function systype() {
		if ( ! function_exists( 'ftp_systype' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_systype' );

			return false;
		}

		return ftp_systype( $this->_conn_id );
	}

	protected function delete( $pathname ) {
		if ( ! function_exists( 'ftp_delete' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_delete' );

			return false;
		}

		return ftp_delete( $this->_conn_id, $pathname );
	}

	protected function site( $command, $fnction = 'site' ) {
		if ( ! function_exists( 'ftp_site' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_site' );

			return false;
		}

		return ftp_site( $this->_conn_id, $command );
	}

	protected function chmod( $pathname, $mode ) {
		if ( ! function_exists( 'ftp_chmod' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_chmod' );

			return false;
		}

		return ftp_chmod( $this->_conn_id, $mode, $pathname );
	}

	protected function rawlist( $pathname = '', $arg = '' ) {
		if ( ! function_exists( 'ftp_rawlist' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_rawlist' );

			return false;
		}
		$recursive = false;

		return ftp_rawlist( $this->_conn_id, $pathname, $recursive );
	}

	protected function nlist( $pathname = '' ) {
		if ( ! function_exists( 'ftp_nlist' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_nlist' );

			return false;
		}

		return ftp_nlist( $this->_conn_id, $pathname );
	}

	protected function get( $remotefile, $localfile = null, $rest = 0 ) {
		if ( ! function_exists( 'ftp_get' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_get' );

			return false;
		}
		$pi = pathinfo( $remotefile );
//fix set '' to ["extension"] , when $pi["extension"] is nothing in pathinfo
		$pi['extension'] = ! isset( $pi['extension'] ) ? '' : $pi['extension'];
		if ( FTP_ASCII == $this->_type or ( FTP_AUTOASCII == $this->_type and in_array( strtoupper( $pi['extension'] ), $this->AutoAsciiExt ) ) ) {
			$mode = FTP_ASCII;
		} else {
			$mode = FTP_BINARY;
		}

		return ftp_get( $this->_conn_id, $localfile, $remotefile, $mode );
	}

	protected function put( $localfile, $remotefile = null, $rest = 0 ) {
		if ( ! function_exists( 'ftp_put' ) ) {
			$this->SendMSG( 'Fatal error: Call to undefined function ftp_put' );

			return false;
		}
		$pi = pathinfo( $remotefile );
//fix set '' to ["extension"] , when $pi["extension"] is nothing in pathinfo
		$pi['extension'] = ! isset( $pi['extension'] ) ? '' : $pi['extension'];
		if ( FTP_ASCII == $this->_type or ( FTP_AUTOASCII == $this->_type and in_array( strtoupper( $pi['extension'] ), $this->AutoAsciiExt ) ) ) {
			$mode = FTP_ASCII;
		} else {
			$mode = FTP_BINARY;
		}

		//adump("r:".$remotefile); adump("l:".$localfile);
		return ftp_put( $this->_conn_id, $remotefile, $localfile, $mode );
	}
}
