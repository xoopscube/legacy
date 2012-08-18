<?php

include_once dirname(__FILE__) . '/Abstract.class.php';
include_once dirname(__FILE__) . '/phpseclib/Net/SFTP.php';

class Xupdate_Ftp_ extends Xupdate_Ftp_Abstract {

	public $sftp;

	/* Constructor */
	public function __construct($XupdateObj, $port_mode=FALSE, $verb=FALSE, $le=FALSE) {
		parent::__construct($XupdateObj);
	}

	/**
	 * app_login
	 *
	 * @param   string  $server
	 *
	 * @return	bool
	 **/
	public function app_login($server){

		$ftp_id = $this->mod_config['FTP_UserName'];
		$ftp_pass = $this->mod_config['FTP_password'];

		// LOGIN
//		@define('NET_SFTP_LOGGING', NET_SFTP_LOG_COMPLEX);
		@define('NET_SFTP_LOGGING', NET_SFTP_LOG_SIMPLE);
		$this->Verbose = TRUE;//TRUE or FALSE
		$this->LocalEcho = FALSE;
		//$this->Passive(TRUE);

		$port = (int)$this->mod_config['SSH_port'];

		//phpseclib
		$this->sftp = new Net_SFTP($server, $port);

		if (!$this->sftp->login($ftp_id, $ftp_pass)) {
			$this->mes.= "SSH Login Failed<br />\n";
			$this->mes.= $this->getSFTPErrors();
			return false;
		}

		$this->mes.= "PWD:".$this->sftp->pwd()."<br />\n";
		$this->mes.= $this->getSFTPLog();

		return true;
	}

	/**
	 * getSFTPErrorsHtml
	 *
	 * @param   void
	 *
	 * @return	array
	 **/
	public function getSFTPErrors()
	{
		return $this->_MessagesToHtml($this->sftp->getSFTPErrors());
	}

	/**
	 * getSFTPErrorsHtml
	 *
	 * @param   void
	 *
	 * @return	array
	 **/
	public function getSFTPLog()
	{
		return $this->_MessagesToHtml($this->sftp->getSFTPLog());
	}

	/**
	 * _MessagesToHtml
	 *
	 * @param   mixed  $message_arr
	 *
	 * @return	string
	 **/
	public function _MessagesToHtml($message_arr)
	{
		$ret = '';
		if (!is_array($message_arr)){
			$ret = htmlspecialchars($message_arr ,ENT_QUOTES ,_CHARSET);
			$ret = str_replace("\r\n" ,"<br/>\n" , $message_arr);
			$ret .= "<br/>\n";
			return $ret;
		}
		foreach($message_arr as $mes){
			$ret.= htmlspecialchars($mes ,ENT_QUOTES ,_CHARSET) . "<br/>\n";
		}
		return $ret;
	}

	/**
	 * quit
	 *
	 * @param   void
	 *
	 * @return	void
	 **/
	public function quit() {
		$this->sftp->disconnect();
	}


	public function chdir($pathname) {
		return $this->sftp->chdir($pathname);
	}

	public function rmdir($pathname) {
		return $this->sftp->rmdir($pathname);
	}

	public function mkdir($pathname) {
		return $this->sftp->mkdir($pathname);
	}

	public function rename($from, $to) {
		return $this->sftp->rename($from, $to);
	}

	public function filesize($pathname) {
		return $this->sftp->size($pathname);
	}

	public function mdtm($pathname) {
		$result =  $this->lstat($pathname);
		if (!$result || !is_array($result) || !isset($result['mtime'])){
			return false;
		}
		return $result['mtime'];	// timestamp
	}

	public function lstat($pathname) {
		return $this->sftp->lstat($pathname);	// false or timestamp
	}
	public function stat($pathname) {
		return $this->sftp->stat($pathname);	// false or timestamp
	}

	public function systype() {
		return false ;
	}

	public function delete($pathname) {
		return $this->sftp->delete($pathname);
	}

	public function chmod($pathname, $mode) {
		return $this->sftp->chmod($mode, $pathname);
	}

	public function rawlist($pathname="", $arg="") {
		return $this->sftp->rawlist($pathname);
	}

	public function nlist($pathname="") {
		return $this->sftp->nlist($pathname);
	}

	public function get($remotefile, $localfile=NULL, $rest=0) {
		return $this->sftp->get($remotefile, $localfile);
	}

	public function put($localfile, $remotefile=NULL, $rest=0) {
		return $this->sftp->put( $remotefile , $localfile ,NET_SFTP_LOCAL_FILE);
	}


}

?>
