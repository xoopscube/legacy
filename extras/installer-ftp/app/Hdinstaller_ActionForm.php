<?php
// vim: foldmethod=marker
/**
 *  Hdinstaller_ActionForm.php
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @version    $Id: app.actionform.php 568 2008-06-07 22:55:10Z mumumu-org $
 */

// {{{ Hdinstaller_ActionForm
/**
 *  ActionForm class.
 *
 *  @author     {$author}
 *  @package    Hdinstaller
 *  @access     public
 */
class Hdinstaller_ActionForm extends Ethna_ActionForm
{
    /**#@+
     *  @access private
     */

    /** @var    array   form definition (default) */
    var $form_template = array();

    /**#@-*/

    /**
     *  Error handling of form input validation.
     *
     *  @access public
     *  @param  string      $name   form item name.
     *  @param  int         $code   error code.
     */
    function handleError($name, $code)
    {
        return parent::handleError($name, $code);
    }

    /**
     *  setter method for form template.
     *
     *  @access protected
     *  @param  array   $form_template  form template
     *  @return array   form template after setting.
     */
    function _setFormTemplate($form_template)
    {
        return parent::_setFormTemplate($form_template);
    }

    /**
     *  setter method for form definition.
     *
     *  @access protected
     */
    function _setFormDef()
    {
        return parent::_setFormDef();
    }

    /**
     *  フォーム値定義を設定する
     *
     *  @access public
     *  @param  string  $name   設定するフォーム名(nullなら全ての定義を設定)
     *  @param  array   $value  設定するフォーム値定義
     *  @return array   フォーム値定義
     */
    function setDef($name, $value)
    {
        if (is_null($name)) {
            $this->form = $value;
			return ;
        }

        $this->form[$name] = $value;
    }
}
// }}}

?>
