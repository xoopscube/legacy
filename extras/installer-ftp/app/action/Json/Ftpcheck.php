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
class Hdinstaller_Form_JsonFtpcheck extends Hdinstaller_Form_Json
{
	function __construct(&$c)
	{
		parent::__construct($c);
		$this->setRequired(array('ftp_username', 'ftp_password'));
	}
}

/**
 *  Index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_Action_JsonFtpcheck extends Hdinstaller_Action_Json
{
    /**
     *  preprocess Index action.
     *
     *  @access    public
     *  @return    string  Forward name (null if no errors.)
     */
    function prepare()
    {
		if ($this->af->validate() == 0){
			$username = $this->af->get('ftp_username');
			$password = $this->af->get('ftp_password');
			
			if ($conn_id = ftp_connect('localhost')) {
				if (ftp_login($conn_id, $username, $password)){
					$ftp_root = $this->seekFTPRoot($conn_id);
					if ($ftp_root !== false){
						$chroot = substr(BASE, strlen($ftp_root));
						$tmp_file = $chroot.'/tmp/tmp_file';
						$tmp_dir  = $chroot.'/tmp/tmp_dir';
						$pwd = ftp_pwd($conn_id);
						if (ftp_put($conn_id, $tmp_file, __FILE__, FTP_BINARY)){
							if (ftp_delete($conn_id, $tmp_file)){
								if (ftp_mkdir($conn_id, $tmp_dir)){
									if (ftp_rmdir($conn_id, $tmp_dir)){
										return null;
									}
									else {
										$this->ae->add('ftp_rmdir', _('Failed ftp operation rmdir.'));
									}
								}
								else {
									$this->ae->add('ftp_mkdir', _('Failed ftp operation mkdir.'));
								}
							}
							else {
								$this->ae->add('ftp_delete', _('Failed ftp operation delete.'));
							}
						}
						else {
							$this->ae->add('ftp_put', _('Failed ftp operation put.').sprintf('[debug] pwd=>%s, __FILE__=>%s', $pwd, __FILE__));
						}
					}
					else {
						$this->ae->add('ftp_root_path', _('Failed seek ftp root path.'));
					}
				}
				else {
					$this->ae->add('ftp_login', _('FTP login failed.'));
				}
			}
			else {
				$this->ae->add('ftp_connect', _('Failed ftp connect to localhost.'));
			}
		}
        return 'json_error';
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
		$this->af->set('repository_url', 'http://hodajuku.sourceforge.net/');
        return 'json_ftpcheck';
    }
}
