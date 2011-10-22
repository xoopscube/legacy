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
class Hdinstaller_View_JsonErrorreload extends Hdinstaller_View_Json
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$this->backend->ctl->cleanUpTmp();
		parent::preforward();
    }
}
