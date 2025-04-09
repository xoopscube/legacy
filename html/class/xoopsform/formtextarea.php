<?php
/**
 * Form textarea
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

class XoopsFormTextArea extends XoopsFormElement
{
    /**
     * number of columns
     * @var	int
     * @access  private
     */
    public $_cols;

    /**
     * number of rows
     * @var	int
     * @access  private
     */
    public $_rows;

    /**
     * initial content
     * @var	string
     * @access  private
     */
    public $_value;

    /**
     * Constuctor
     *
     * @param	string  $caption    caption
     * @param	string  $name       name
     * @param	string  $value      initial content
     * @param	int     $rows       number of rows
     * @param	int     $cols       number of columns
     */
    public function __construct($caption, $name, $value= '', $rows=5, $cols=50)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_rows = (int)$rows;
        $this->_cols = (int)$cols;
        $this->setValue($value);
    }
    public function XoopsFormTextArea($caption, $name, $value= '', $rows=5, $cols=50)
    {
        return $this->__construct($caption, $name, $value, $rows, $cols);
    }

    /**
     * get number of rows
     *
     * @return	int
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Get number of columns
     *
     * @return	int
     */
    public function getCols()
    {
        return $this->_cols;
    }

    /**
     * Get initial content
     *
     * @return	string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set initial content
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * prepare HTML for output
     *
     * @return	sting HTML
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget();

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_textarea.html');
        $renderTarget->setAttribute('element', $this);
        $renderTarget->setAttribute('class', $this->getClass());

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
