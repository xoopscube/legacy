<?php
/**
 *  Index.php
 *
 *  @author    {$author}
 *  @package   Hdinstaller
 *  @version   $Id: app.action.default.php 573 2008-06-08 01:43:28Z mumumu-org $
 */

/**
 *  Index form implementation
 *
 *  @author    {$author}
 *  @access    public
 *  @package   Hdinstaller
 */
require_once dirname(__FILE__).".php";
class Hdinstaller_Form_JsonCopyfile extends Hdinstaller_Form_Json
{
	function __construct(&$c)
	{
		parent::__construct($c);
		$this->setRequired();
	}
}

/**
 *  Index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_Action_JsonCopyfile extends Hdinstaller_Action_Json
{
    /**
     *  preprocess Index action.
     *
     *  @access    public
     *  @return    string  Forward name (null if no errors.)
     */
    function prepare()
    {
/*		$this->af->setApp('xoops_cookie_path', '/itoh/hodajuku/html');
		$this->af->set('xoops_root_path', '/home/smbuser/Site/main/public_html/itoh/hodajuku/html');
		$this->af->set('xoops_trust_path', '/home/smbuser/Site/main/public_html/itoh/hodajuku/xoops_trust_path');
		$this->af->setApp('result', 1);
        return 'json_copyfile';	 */
		
		
		
		if ($this->af->validate() == 0){
			$username = $this->af->get('ftp_username');
			$password = $this->af->get('ftp_password');
		
			// let's copy
			if ($conn_id = ftp_connect('localhost')) {
				if (ftp_login($conn_id, $username, $password)){
					
					$ftp_root = $this->seekFTPRoot($conn_id);
					$chroot = substr(BASE, strlen($ftp_root));
					
					$xoops_root_path = substr($this->af->get('xoops_root_path'), strlen($ftp_root));;
					$remote_path = $chroot.'/tmp/html';
					$this->ftpPut($remote_path, $xoops_root_path, $conn_id);
					
					$xoops_trust_path = substr($this->af->get('xoops_trust_path'), strlen($ftp_root));;
					$remote_path = $chroot.'/tmp/xoops_trust_path';
					$this->ftpPut($remote_path, $xoops_trust_path, $conn_id);
					
					//// after script
					$dir777 = array(
						$xoops_root_path.'/uploads',
						$xoops_root_path.'/uploads/fckeditor',
						$xoops_root_path.'/uploads/wizmobile',
						$xoops_trust_path.'/templates_c',
						$xoops_trust_path.'/cache' ,
						$xoops_trust_path.'/templates_c' ,
						$xoops_trust_path.'/uploads' ,
						$xoops_trust_path.'/session' ,
						$xoops_trust_path.'/log' ,
						$xoops_trust_path.'/tmp',
						$xoops_trust_path.'/modules/protector/configs',
						$xoops_trust_path.'/uploads/d3downloads',
						$xoops_trust_path.'/uploads/pico',
						);
					foreach ($dir777 as $_d){
						ftp_chmod($conn_id, 0777, $_d);
					}
					/// mainfile.php
					ftp_chmod($conn_id, 0666, $xoops_root_path.'/mainfile.php');
					ftp_close($conn_id);
					return null;
				}
			}
		}
        return 'json_error_reload';
    }

    /**
     *  Index action implementation.
     *
     *  @access    public
     *  @return    string  Forward Name.
     */
    function perform()
    {
		$this->af->setApp('result', 1);
		$this->af->setApp('next_message', _('Finish XOOPSCube File install'));
		
		$this->backend->ctl->cleanUpTmp();
		
        return 'json_copyfile';
    }
	
	
	
	/// ディレクトリ指定で一括FTPPUT
	/**
	 * @brief 
	 * @param 元ファイルのあるディレクトリ
	 * @param 新しくPUTするファイルのディレクトリ
	 * @param コネクション
	 * @retval
	 */
	function ftpPut($remote_path, $local_path, $con)
	{
		$ftp_root = $this->seekFTPRoot($con);
		$fr_pos = strlen($ftp_root);
		
		$file_list = $this->getFileList($ftp_root.$remote_path);
		$dir = $file_list['dir'];
		krsort($dir);
		
		/// 一旦、すべてのdirを作っておく
		if (!is_dir($ftp_root.$local_path)){
			$this->ftp_mkdir($con, $local_path);
		}
		
		$remote_pos = strlen($remote_path);
		foreach ($dir as $directory){
			$remote_directory = $ftp_root.$local_path.substr($directory, $fr_pos + $remote_pos);
			if (!is_dir($remote_directory)){ 
				$ftp_remote_directory = $local_path.substr($directory, $fr_pos + $remote_pos); 
				ftp_mkdir($con, $ftp_remote_directory);
			}
		}
		
		/// put files
		ftp_chdir($con, '/');
		foreach ($file_list['file'] as $r_file){
			$l_file = $local_path.substr($r_file, $fr_pos + $remote_pos ); // +1 is remove first flash
			ftp_put($con, $l_file, $r_file, FTP_BINARY);
		}
	}
	
	
	
    /**
     *  ディレクトリ以下の全てのファイルを列挙
     *
     *  @access private
     */
	function  getFileList($dir, $list=array('dir'=> array(), 'file' => array()))
	{
		if (is_dir($dir) == false) {
			return;
		}

		$dh = opendir($dir);
		if ($dh) {
			while (($file = readdir($dh)) !== false) {
				if ($file == '.' || $file == '..'){
					continue;
				}
				else if (is_dir("$dir/$file")) {
					$list = $this->getFileList("$dir/$file", $list);
					$list['dir'][] = "$dir/$file";
				}
				else {
					$list['file'][] = "$dir/$file";
				}
			}
		}
		closedir($dh);
		return $list;
	}
	
	
	/// mkdir -p
	/**
	 * @brief 
	 * @param 新しく作成するディレクトリ
	 * @param コネクション
	 * @retval
	 */
	function ftp_mkdir($con, $dir)
	{
		$parent = dirname($dir);
        if ($dir === $parent) {
            return true;
        }

		$ftp_root = $this->seekFTPRoot($con);
		if (is_dir($ftp_root.$parent) === false) {
			if ($this->ftp_mkdir($con, $parent) === false) {
                return false;
            }
        }
		
        return ftp_mkdir($con, $dir);
	}
	
}
