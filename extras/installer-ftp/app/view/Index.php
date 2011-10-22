<?php
/**
 *  Index.php
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @version    $Id: app.view.default.php 532 2008-05-13 22:41:22Z mumumu-org $
 */

/**
 *  Index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_View_Index extends Hdinstaller_ViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$this->subtitle = _('Select language');
	
		$config = $this->backend->getConfig();
		$this->af->setApp('allow_language', $config->get('allow_language'));
		$this->af->setApp('cur_lang', current($this->backend->ctl->getLanguage()));
    }
}

?>
