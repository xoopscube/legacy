<?php

include_once dirname(__FILE__) . '/Abstract.class.php';

class Xupdate_Ftp_ extends Xupdate_Ftp_Abstract {

	/**
	 * app_login
	 *
	 * @param   string  $server
	 *
	 * @return	bool
	 **/
	public function app_login($server){
		$test = XOOPS_ROOT_PATH . '/xupdate_test';
		return (@ mkdir($test) && @ chmod($test, 0777) && @ rmdir($test));
	}

	/**
	 * quit
	 *
	 * @param   void
	 *
	 * @return	void
	 **/
	public function quit() {
		return true;
	}

	public function chdir($pathname) {
		return @ chdir($pathname);
	}

	public function rmdir($pathname) {
		return @ rmdir($pathname);
	}

	public function mkdir($pathname) {
		return @ mkdir($pathname);
	}

	public function rename($from, $to) {
		return @ rename($from, $to);
	}

	public function filesize($pathname) {
		return filesize($pathname);
	}

	public function mdtm($pathname) {
		return filemtime($pathname);
	}

	public function lstat($pathname) {
		return lstat($pathname);
	}

	public function stat($pathname) {
		return fstat($pathname);
	}

	public function systype() {
		return false ;
	}

	public function delete($pathname) {
		return unlink($pathname);
	}

	public function chmod($pathname, $mode) {
		return chmod($pathname, $mode);
	}

	public function nlist($pathname="") {
		return glob($pathname . '/*');
	}

	public function get($remotefile, $localfile=NULL, $rest=0) {
		return @ copy($remotefile, $localfile);
	}

	public function put($localfile, $remotefile=NULL, $rest=0) {
		return @ copy($localfile, $remotefile);
	}


}

?>

