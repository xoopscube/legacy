<?php
/**
 * Form select field
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

class XoopsFormSelect extends XoopsFormElement
{

    /**
     * Options
     * @var array
     * @access	private
     */
    public $_options = [];

    /**
     * Allow multiple selections?
     * @var	bool
     * @access	private
     */
    public $_multiple = false;

    /**
     * Number of rows. "1" makes a dropdown list.
     * @var	int
     * @access	private
     */
    public $_size;

    /**
     * Pre-selcted values
     * @var	array
     * @access	private
     */
    public $_value = [];

    /**
     * Constructor
     *
     * @param	string	$caption	Caption
     * @param	string	$name       "name" attribute
     * @param	mixed	$value	    Pre-selected value (or array of them).
     * @param	int		$size	    Number or rows. "1" makes a drop-down-list
     * @param	bool    $multiple   Allow multiple selections?
     */
    public function __construct($caption, $name, $value=null, $size=1, $multiple=false)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_multiple = $multiple;
        $this->_size = (int)$size;
        if (isset($value)) {
            $this->setValue($value);
        }
    }
    public function XoopsFormSelect($caption, $name, $value=null, $size=1, $multiple=false)
    {
        return $this->__construct($caption, $name, $value, $size, $multiple);
    }

    /**
     * Are multiple selections allowed?
     *
     * @return	bool
     */
    public function isMultiple()
    {
        return $this->_multiple;
    }

    /**
     * Get the size
     *
     * @return	int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return	array
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set pre-selected values
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
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
     * @param	string  $value  "value" attribute
     * @param	string  $name   "name" attribute
     */
    public function addOption($value, $name= '')
    {
        if ($name !== '') {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Add multiple options
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
     * Get all options
     *
     * @return	array   Associative array of value->name pairs
     */
    public function getOptions()
    {
        return $this->_options;
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
        $renderTarget->setTemplateName('legacy_xoopsform_select.html');
        $renderTarget->setAttribute('element', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
