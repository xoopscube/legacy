<?php
// vim: foldmethod=marker
/**
 *  Hdinstaller_ActionClass.php
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @version    $Id: app.actionclass.php 532 2008-05-13 22:41:22Z mumumu-org $
 */

// {{{ Hdinstaller_ActionClass
/**
 *  action execution class
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @access     public
 */
class Hdinstaller_ActionClass extends Ethna_ActionClass
{
    /**
     *  authenticate before executing action.
     *
     *  @access public
     *  @return string  Forward name.
     *                  (null if no errors. false if we have something wrong.)
     */
    function authenticate()
    {
		$this->backend->ctl->getI18N()->use_gettext = false;
        return parent::authenticate();
    }

    /**
     *  Preparation for executing action. (Form input check, etc.)
     *
     *  @access public
     *  @return string  Forward name.
     *                  (null if no errors. false if we have something wrong.)
     */
    function prepare()
    {
        return parent::prepare();
    }

    /**
     *  execute action.
     *
     *  @access public
     *  @return string  Forward name.
     *                  (we does not forward if returns null.)
     */
    function perform()
    {
        return parent::perform();
    }
}
// }}}

?>
