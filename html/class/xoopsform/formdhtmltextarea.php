<?php
/**
 * Form textarea with xoops formatting and smilies buttons
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
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
 * base class
 */
include_once XOOPS_ROOT_PATH . '/class/xoopsform/formtextarea.php';

// Make sure you have included /include/xoopscodes.php, otherwise DHTML will not work properly!

class XoopsFormDhtmlTextArea extends XoopsFormTextArea
{
    /**
     * Hidden text
     * @var string
     * @access  private
     */
    public $_xoopsHiddenText;

    /**
     * Editor type
     * @var string
     * @access  private
     */
    private $_editor;

    /**
     * Editor check for recursive prevention
     * @static
     * @var array
     * @access  private
     */
    private static $_editorCheck = [];

    /**
     * Constructor
     *
     * @param   string  $caption    Caption
     * @param   string  $name       "name" attribute
     * @param   string  $value      Initial text
     * @param   int     $rows       Number of rows
     * @param   int     $cols       Number of columns
     * @param   string  $hiddentext Hidden Text
     */
    public function __construct($caption, $name, $value, $rows=5, $cols=50, $hiddentext= 'xoopsHiddenText')
    {
        parent::__construct($caption, $name, $value, $rows, $cols);
        $this->_xoopsHiddenText = $hiddentext;
    }

    /**
     * Prepare HTML for output
     *
     * @return  string  HTML
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();

        $editor = $this->getEditor();
        $id = $this->getId();
        if ($editor && !isset(self::$_editorCheck[$id])) {
            self::$_editorCheck[$id] = true;
            $params['name'] = trim($this->getName(false));
            $params['class'] = trim($this->getClass());
            $params['cols'] = $this->getCols();
            $params['rows'] = $this->getRows();
            $params['value'] = $this->getValue();
            $params['id'] = $id;
            $params['editor'] = $editor;

            $html = '';
            switch ($params['editor']) {
                case 'html':
                    XCube_DelegateUtils::call('Site.TextareaEditor.HTML.Show', new XCube_Ref($html), $params);
                    break;
                case 'none':
                    XCube_DelegateUtils::call('Site.TextareaEditor.None.Show', new XCube_Ref($html), $params);
                    break;
                case 'bbcode':
                default:
                    XCube_DelegateUtils::call('Site.TextareaEditor.BBCode.Show', new XCube_Ref($html), $params);
                    break;
            }

            return $html;
        }

        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_dhtmltextarea.html');
        $renderTarget->setAttribute('element', $this);
        $renderTarget->setAttribute('class', $this->getClass());

        $renderSystem->render($renderTarget);

        $ret = $renderTarget->getResult();
        $ret .= $this->_renderSmileys();

        return $ret;
    }

    /**
     * prepare HTML for output of the smiley list.
     *
     * @return  string HTML
     */
    public function _renderSmileys()
    {
        $handler =& xoops_getmodulehandler('smiles', 'legacy');
        $smilesArr =& $handler->getObjects(new Criteria('display', 1));

        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_opt_smileys.html');
        $renderTarget->setAttribute('element', $this);
        $renderTarget->setAttribute('smilesArr', $smilesArr);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }

    /**
     * set editor mode for XCL 2.2
     *
     * @param  string  editor type
     */
    public function setEditor($editor)
    {
        $this->_editor = strtolower($editor);
    }

    /**
     * get the "editor" value
     *
     * @return 	string  editor type
     */
    public function getEditor()
    {
        return $this->_editor;
    }
}
