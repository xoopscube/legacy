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
class Hdinstaller_Form_JsonGetrepository extends Hdinstaller_Form_Json
{
	function __construct(&$c)
	{
		parent::__construct($c);
		$this->setRequired(array('repository_url'));
	}
}

/**
 *  Index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_Action_JsonGetrepository extends Hdinstaller_Action_Json
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
			$url = sprintf('%s/repository.sphp', rtrim($this->af->get('repository_url'), '/'));
			require_once 'HTTP/Request.php';
			$req = new HTTP_Request($url);
			$req->sendRequest();
			if ($req->getResponseCode() == '200'){
				$data = $req->getResponseBody();
				$ret = @unserialize($data);
				if (is_array($ret)){
					$this->af->setApp('repository_data', $ret);
					$cache_file = $this->backend->ctl->repositoryURL2CacheFile($url);
					file_put_contents($cache_file, $data);
					chmod($cache_file, 0666);
					return null;
				}
			}
		}
		$this->ae->add('repository_url', _('The repository is not available!'));
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
        return 'json_getrepository';
    }
}
