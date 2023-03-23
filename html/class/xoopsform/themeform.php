<?php
/**
 * Form that will output as a theme-enabled HTML table
 * Also adds JavaScript to validate required fields
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * base class
 */
include_once XOOPS_ROOT_PATH . '/class/xoopsform/form.php';


class XoopsThemeForm extends XoopsForm
{
    /**
     * Insert an empty row in the table to serve as a seperator.
     *
     * @param	string  $extra  HTML to be displayed in the empty row.
     * @param	string	$class	CSS class name for <td> tag
     */
    public function insertBreak($extra = '', $class= '')
    {
        $class = ('' !== $class) ? " class='$class'" : '';
        $extra = ('' !== $extra) ? $extra : '&nbsp';
        $this->addElement(new XoopsFormBreak($extra, $class)) ;
    }

    /**
     * create HTML to output the form as a theme-enabled table with validation.
     *
     * @return	string
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_themeform.html');
        $renderTarget->setAttribute('form', $this);

        $renderSystem->render($renderTarget);

        $ret = $renderTarget->getResult();
        $ret .= $this->renderValidationJS(true);

        return $ret;
    }
}
