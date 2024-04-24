<?php
/**
 * Form text label
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsFormLabel extends XoopsFormElement
{

    /**
     * Text
     * @var	string
     * @access	private
     */
    public $_value;

    /**
     * Constructor
     *
     * @param	string	$caption	Caption
     * @param	string	$value		Text
     */
    public function __construct($caption= '', $value= '')
    {
        $this->setCaption($caption);
        $this->_value = $value;
    }
    public function XoopsFormLabel($caption= '', $value= '')
    {
        return $this->__construct($caption, $value);
    }

    /**
     * Get the text
     *
     * @return	string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Prepare HTML for output
     *
     * @return	string
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_label.html');
        $renderTarget->setAttribute('element', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
