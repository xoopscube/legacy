<?php
/**
 *  Json.php
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @version    $Id: app.view.default.php 532 2008-05-13 22:41:22Z mumumu-org $
 */

/**
 *  Json view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
require_once dirname(__FILE__).".php";
class Hdinstaller_View_JsonCopyfile extends Hdinstaller_View_Json
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		parent::preforward();
		
		
		$cookie_path = str_replace(rtrim($_SERVER['DOCUMENT_ROOT'],'/'),
								   '',
								   $this->af->get('xoops_root_path'));
		!$cookie_path and $cookie_path = '/'; 
		
		$xoops_url = sprintf('http://%s%s',
							 $_SERVER['SERVER_NAME'], $cookie_path );
							 
		$this->af->setApp('xoops_cookie_path', $cookie_path);
		$this->af->setApp('xoops_url', $xoops_url);
		$this->af->setApp('prefix', Ethna_Util::getRandom(6));
		$this->af->setApp('salt', Ethna_Util::getRandom(8));
    }
}
