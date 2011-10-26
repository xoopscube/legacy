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
class Hdinstaller_Form_JsonRemovedir extends Hdinstaller_Form_Json
{
	function __construct(&$c)
	{
		parent::__construct($c);
		
		$this->form['root_path'] = array(
			'type' => VAR_TYPE_STRING
			);
		
		$this->setRequired(array('ftp_username', 'ftp_password', 'root_path'));
	}
}

/**
 *  Index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_Action_JsonRemovedir extends Hdinstaller_Action_Json
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
			$xoops_root_path = $this->af->get('root_path');
var_dump($username, $password, $xoops_root_path); 
			if ($conn_id = ftp_connect('localhost')) {
				$this->af->setApp($i++, $i++);
				if (ftp_login($conn_id, $username, $password)){
					$ftp_root = $this->seekFTPRoot($conn_id);
					if ($ftp_root !== false){
						$install_dir = str_replace($ftp_root, '', $xoops_root_path).'/install';
						$install_dir_dest = $install_dir . '_'. Ethna_Util::getRandom(16);
						ftp_rename($conn_id, $install_dir, $install_dir_dest);
						$mainfile = str_replace($ftp_root, '', $xoops_root_path).'/mainfile.php';
						ftp_chmod($conn_id, 0644, $mainfile);
						return null;
					}
				}
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
        return 'json';
    }
}
