<?php
/**
 * Form simple text field
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

/**
 * A simple text field
 */
class XoopsFormText extends XoopsFormElement
{
    /**
     * Size
     *
     * @var int
     * @access private
     */
    public $_size;

    /**
     * Maximum length of the text
     *
     * @var int
     * @access private
     */
    public $_maxlength;

    /**
     * Initial text
     *
     * @var string
     * @access private
     */
    public $_value;

    /**
     * Constructor
     *
     * @param string $caption   Caption
     * @param string $name      "name" attribute
     * @param int    $size      Size
     * @param int    $maxlength Maximum length of text
     * @param string $value     Initial text
     */
    public function __construct($caption, $name, $size, $maxlength, $value = '')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_size = (int)$size;
        $this->_maxlength = (int)$maxlength;
        $this->setValue($value);
    }
    public function XoopsFormText($caption, $name, $size, $maxlength, $value= '')
    {
        return $this->__construct($caption, $name, $size, $maxlength, $value);
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    public function getMaxlength()
    {
        return $this->_maxlength;
    }

    /**
     * Get initial content
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set initial text value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @return	string  HTML
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_text.html');
        $renderTarget->setAttribute('element', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
