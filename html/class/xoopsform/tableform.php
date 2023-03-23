<?php
/**
 * Form that will output formatted as a HTML table
 * No styles and no JavaScript to check for required fields.
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
 * the base class
 */
include_once XOOPS_ROOT_PATH . '/class/xoopsform/form.php';


class XoopsTableForm extends XoopsForm
{

    /**
     * create HTML to output the form as a table
     *
     * @return	string
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_tableform.html');
        $renderTarget->setAttribute('form', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
