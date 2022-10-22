<?php
/**
 * Form checkbox
 * @package    kernel
 * @subpackage form
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsFormCheckBox extends XoopsFormElement
{

    /**
     * Availlable options
     * @var array
     * @access	private
     */
    public $_options = [];

    /**
     * pre-selected values in array
     * @var	array
     * @access	private
     */
    public $_value = [];

    /**
     * Constructor
     *
     * @param	string  $caption
     * @param	string  $name
     * @param	mixed   $value  Either one value as a string or an array of them.
     */
    public function __construct($caption, $name, $value = null)
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (isset($value)) {
            $this->setValue($value);
        }
    }
    public function XoopsFormCheckBox($caption, $name, $value = null)
    {
        return self::__construct($caption, $name, $value);
    }

    /**
     * Get the "value"
     *
     * @return	array
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the "value"
     *
     * @param	array
     */
    public function setValue($value)
    {
        $this->_value = [];
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->_value[] = $v;
            }
        } else {
            $this->_value[] = $value;
        }
    }

    /**
     * Add an option
     *
     * @param	string  $value
     * @param	string  $name
     */
    public function addOption($value, $name= '')
    {
        if ('' != $name) {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Add multiple Options at once
     *
     * @param	array   $options    Associative array of value->name pairs
     */
    public function addOptionArray($options)
    {
        if (is_array($options)) {
            foreach ($options as $k=>$v) {
                $this->addOption($k, $v);
            }
        }
    }

    /**
     * Get an array with all the options
     *
     * @return	array   Associative array of value->name pairs
     */
    public function getOptions()
    {
        return $this->_options;
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

        if (count($this->getOptions()) > 1 && '[]' != substr($this->getName(), -2, 2)) {
            $newname = $this->getName() . '[]';
            $this->setName($newname);
        }

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_checkbox.html');
        $renderTarget->setAttribute('element', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
