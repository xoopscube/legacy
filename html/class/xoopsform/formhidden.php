<?php
/**
 * Form hidden field
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsFormHidden extends XoopsFormElement
{

    /**
     * Value
     * @var	string
     * @access	private
     */
    public $_value;

    /**
     * Constructor
     *
     * @param	string	$name	"name" attribute
     * @param	string	$value	"value" attribute
     */
    public function __construct($name, $value)
    {
        $this->setName($name);
        $this->setHidden();
        $this->setValue($value);
        $this->setCaption('');
    }
    public function XoopsFormHidden($name, $value)
    {
        return $this->__construct($name, $value);
    }

    /**
     * Get the "value" attribute
     *
     * @return	string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets the "value" attribute
     *
     * @patam  $value    string
     * @param $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @return	string	HTML
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_hidden.html');
        $renderTarget->setAttribute('element', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
