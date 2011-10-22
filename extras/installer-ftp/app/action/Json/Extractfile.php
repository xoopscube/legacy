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
class Hdinstaller_Form_JsonExtractfile extends Hdinstaller_Form_Json
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
class Hdinstaller_Action_JsonExtractfile extends Hdinstaller_Action_Json
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
			list($package, $version) = explode('@', $this->af->get('target_package'));
			$data_file = $this->backend->ctl->package2dataFile($package, $version);
			require_once 'Archive/Tar.php';
			$tar = new Archive_Tar($data_file, 'gz');
			$status = $tar->extract(dirname($data_file));
/*			chdir(BASE.'/tmp');
			exec('which tar', $which, $status);
			$command = sprintf('%s xzf %s', $which[0], $data_file);
			exec($command, $result, $status); */
			if ($status == 1){
				return null;
			}
			$this->ae->add('extract_error', _('file extract error'). sprintf('[STATUS: %s]', $status));
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
		$this->af->setApp('next_message', _('Files copy to XOOPS_ROOT_PATH and XOOPS_TRUST_PATH.'));
		return 'json';
    }
}
