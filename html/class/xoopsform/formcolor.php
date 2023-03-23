<?php
/**
 * Form input color
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 * @brief      only accept 7 character hex strings
 *             refer also to regular expression (regex) in template
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsFormColor extends XoopsFormElement
{

    /**
     * Value
     * @var	string
     * @access	private
     */
    public $_value;

    /**
     * Type "color"
     * @var	string
     * @access	private
     */
    public $_type;

    /**
     * Constructor
     *
     * @param	string  $caption    Caption
     * @param	string  $name
     * @param	string  $value
     * @param	string  $type       "color"
     */
    public function __construct($caption, $name, $value= '#face74', $type= 'color')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_type = $type;
        $this->setValue($value);
    }
    public function XoopsFormColor($caption, $name, $value= '', $type= 'color')
    {
        return self::__construct($caption, $name, $value, $type);
    }

    /**
     * Get the initial value
     *
     * @return	string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the initial value
     *
     * @param $value
     * @return    string
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Get the type
     *
     * @return	string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * prepare HTML for output
     *
     * @return	string
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_color.html');
        $renderTarget->setAttribute('element', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
