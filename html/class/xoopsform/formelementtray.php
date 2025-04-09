<?php
/**
 * Group of form elements
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

class XoopsFormElementTray extends XoopsFormElement
{

    /**
     * array of form element objects
     * @var array
     * @access  private
     */
    public $_elements = [];

    /**
     * required elements
     * @var array
     */
    public $_required = [];

    /**
     * HTML to seperate the elements
     * @var	string
     * @access  private
     */
    public $_delimeter;

    /**
     * constructor
     *
     * @param string $caption Caption for the group.
     * @param string $delimeter
     * @param string $name
     */
    public function __construct($caption, $delimeter= '&nbsp;', $name= '')
    {
        $this->setName($name);
        $this->setCaption($caption);
        $this->_delimeter = $delimeter;
    }
    public function XoopsFormElementTray($caption, $delimeter= '&nbsp;', $name= '')
    {
        return $this->__construct($caption, $delimeter, $name);
    }

    /**
     * Is this element a container of other elements?
     *
     * @return	bool true
     */
    public function isContainer()
    {
        return true;
    }

    /**
     * Add an element to the group
     *
     * @param      $formElement
     * @param bool $required
     */
    public function addElement(&$formElement, $required=false)
    {
        $this->_elements[] =& $formElement;
        if ($required) {
            if (!$formElement->isContainer()) {
                $this->_required[] =& $formElement;
            } else {
                $required_elements =& $formElement->getElements(true);
                foreach ($required_elements as $i => $iValue) {
                    $this->_required[] =& $required_elements[$i];
                }
            }
        }
    }

    /**
     * get an array of "required" form elements
     *
     * @return	array   array of {@link XoopsFormElement}s
     */
    public function &getRequired()
    {
        return $this->_required;
    }

    /**
     * Get an array of the elements in this group
     *
     * @param	bool	$recurse	get elements recursively?
     * @return  array   Array of {@link XoopsFormElement} objects.
     */
    public function &getElements($recurse = false)
    {
        if (!$recurse) {
            return $this->_elements;
        }

        $ret = [];
        foreach ($this->_elements as $i => $iValue) {
            if (!$iValue->isContainer()) {
                $ret[] =& $this->_elements[$i];
            } else {
                $elements =& $iValue->getElements(true);
                foreach ($elements as $j => $jValue) {
                    $ret[] =& $elements[$j];
                }
                unset($elements);
            }
        }
        return $ret;
    }

    /**
     * Get the delimiter of this group
     *
     * @return	string  The delimiter
     */
    public function getDelimeter()
    {
        return $this->_delimeter;
    }

    /**
     * prepare HTML to output this group
     *
     * @return	string  HTML output
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_elementtray.html');
        $renderTarget->setAttribute('tray', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
