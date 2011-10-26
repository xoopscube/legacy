<?php
// vim: foldmethod=marker
/**
 *  Hdinstaller_ViewClass.php
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @version    $Id: app.viewclass.php 661 2008-10-10 08:53:09Z mumumu-org $
 */

// {{{ Hdinstaller_ViewClass
/**
 *  View class.
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @access     public
 */
class Hdinstaller_ViewClass extends Ethna_ViewClass
{
	/**
	 * 
	 */
	 var $title = '';
	 
	 
	/**
	 * 
	 */
	 var $subtitle = '';
 
    /**
     *  set common default value.
     *
     *  @access protected
     *  @param  object  Hdinstaller_Renderer  Renderer object.
     */
    function _setDefault(&$renderer)
    {
		$this->backend->ctl->getI18N()->use_gettext = true;
		
		$this->title = _('Hodajuku Distribution Install/Upgrade System');
		if ($this->subtitle){
			$this->af->setApp('subtitle', $this->subtitle);
		}
		
		$this->af->setApp('title', $this->title);
		
		$this->af->setApp('lang', current($this->backend->ctl->getLanguage()));
    }

}
// }}}

?>
