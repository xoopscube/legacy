<?php

include_once dirname(__FILE__) . '/Abstract.class.php';

class Xupdate_Ftp_ extends Xupdate_Ftp_Abstract {

	/* Constructor */
	public function __construct($XupdateObj, $port_mode=FALSE, $verb=FALSE, $le=FALSE) {
		parent::__construct($XupdateObj);

	}

	protected function Passive($pasv=false) {
		$ret = ftp_pasv($this->_conn_id, $pasv);
		$this->SendMSG("Passive mode ".($pasv?"on":"off"));
		return $ret;
	}

	protected function connect($server=NULL) {
		if(!empty($server)) {
			if(!$this->SetServer($server)) return false;
		}
		$this->_conn_id = ftp_connect($this->_host) ;
		if( $this->_conn_id === FALSE ) {
			return false;
		} else {
			$this->_ready=true;
			return TRUE;
		}
		return FALSE;
	}

	protected function ssl_connect($server=NULL) {
		if(!empty($server)) {
			if(!$this->SetServer($server)) return false;
		}
		$this->_conn_id = ftp_ssl_connect($this->_host) ;
		if( $this->_conn_id === FALSE ) {
			return false;
		} else {
			$this->_ready=true;
			return TRUE;
		}
		return FALSE;
	}

	protected function login($user=NULL, $pass=NULL) {
		$this->_login = !is_null($user) ? $user : "anonymous";
		$this->_password = !is_null($pass) ? $pass : "anonymous@example.com";
		//adump($this->_conn_id);
		//adump($this->_login);
		//adump($this->_password);
		if( ftp_login ( $this->_conn_id, $this->_login, $this->_password ) === true ) {
			$this->SendMSG("Authentication succeeded");
			return TRUE;
		} else {
			$this->SendMSG("Authentication failed");
			return FALSE;
		}
	}

	protected function quit($force=false) {
		return ftp_close($this->_conn_id);
	}

	protected function pwd() {
		if ( ($rtn = ftp_pwd( $this->_conn_id )) == false ) {
			$this->SendMSG("pwd command failed".CRLF);
			return FALSE;
		} else {
//fix ereg_replace -> preg_replace for php5.3+
			$this->SendMSG(preg_replace("/^[0-9]{3} \"(.+)\" .+".CRLF."/"., "\\1", $rtn));
			return TRUE;
		}
	}

	protected function cdup() {
		return ftp_cdup( $this->_conn_id );
	}

	protected function chdir($pathname) {
		return ftp_chdir( $this->_conn_id, $pathname );
	}

	protected function rmdir($pathname) {
		return ftp_rmdir( $this->_conn_id, $pathname );
	}

	protected function mkdir($pathname) {
        //$pathname = str_replace( '/','\\',$pathname );
        //adump($pathname);
		return ftp_mkdir( $this->_conn_id, $pathname );
	}

	protected function rename($from, $to) {
		return ftp_rename( $this->_conn_id, $from, $to );
	}

	protected function filesize($pathname) {
		return ftp_size( $this->_conn_id, $pathname );
	}

	protected function mdtm($pathname) {
		return ftp_mdtm( $this->_conn_id, $pathname );	// timestamp
	}

	protected function systype() {
		return ftp_systype( $this->_conn_id );
	}

	protected function delete($pathname) {
		return ftp_delete( $this->_conn_id, $pathname );
	}

	protected function site($command, $fnction="site") {
		return ftp_site( $this->_conn_id, $command );
	}

	protected function chmod($pathname, $mode) {
		return ftp_chmod( $this->_conn_id, $mode, $pathname );
	}

	protected function rawlist($pathname="", $arg="") {
		$recursive = false ;
		return ftp_rawlist( $this->_conn_id, $pathname, $recursive );
	}

	protected function nlist($pathname="") {
		return ftp_nlist( $this->_conn_id, $pathname );
	}

	protected function get($remotefile, $localfile=NULL, $rest=0) {
		$pi=pathinfo($remotefile);
//fix set '' to ["extension"] , when $pi["extension"] is nothing in pathinfo
		$pi["extension"] = !isset($pi["extension"]) ? '' : $pi["extension"];
		if($this->_type==FTP_ASCII or ($this->_type==FTP_AUTOASCII and in_array(strtoupper($pi["extension"]), $this->AutoAsciiExt))) {
			$mode=FTP_ASCII;
		} else {
			$mode=FTP_BINARY;
		}
		return ftp_get( $this->_conn_id, $localfile, $remotefile, $mode );
	}

	protected function put($localfile, $remotefile=NULL, $rest=0) {
		$pi=pathinfo($remotefile);
//fix set '' to ["extension"] , when $pi["extension"] is nothing in pathinfo
		$pi["extension"] = !isset($pi["extension"]) ? '' : $pi["extension"];
		if($this->_type==FTP_ASCII or ($this->_type==FTP_AUTOASCII and in_array(strtoupper($pi["extension"]), $this->AutoAsciiExt))) {
			$mode=FTP_ASCII;
		} else {
			$mode=FTP_BINARY;
		}
        //adump("r:".$remotefile); adump("l:".$localfile);
		return ftp_put( $this->_conn_id, $remotefile, $localfile, $mode );
	}


}

?>
