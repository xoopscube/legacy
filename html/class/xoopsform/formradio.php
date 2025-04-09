<?php
/**
 * Form Group of radio buttons
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsFormRadio extends XoopsFormElement
{

    /**
     * Array of Options
     * @var	array
     * @access	private
     */
    public $_options = [];

    /**
     * Pre-selected value
     * @var	string
     * @access	private
     */
    public $_value = null;

    /**
     * Constructor
     *
     * @param	string	$caption	Caption
     * @param	string	$name		"name" attribute
     * @param	string	$value		Pre-selected value
     */
    public function __construct($caption, $name, $value = null)
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (isset($value)) {
            $this->setValue($value);
        }
    }
    public function XoopsFormRadio($caption, $name, $value = null)
    {
        return self::__construct($caption, $name, $value);
    }

    /**
     * Get the pre-selected value
     *
     * @return	string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the pre-selected value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Add an option
     *
     * @param	string	$value	"value" attribute - This gets submitted as form-data.
     * @param	string	$name	"name" attribute - This is displayed. If empty, we use the "value" instead.
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
     * Adds multiple options
     *
     * @param	array	$options	Associative array of value->name pairs.
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
     * Gets the options
     *
     * @return	array	Associative array of value->name pairs.
     */
    public function getOptions()
    {
        return $this->_options;
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

        $renderTarget =& $renderSystem->createRenderTarget();

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_radio.html');
        $renderTarget->setAttribute('element', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
