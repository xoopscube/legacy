<?php
/**
 * File upload field
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

class XoopsFormFile extends XoopsFormElement
{

    /**
     * Maximum size for an uploaded file
     * @var	int
     * @access	private
     */
    public $_maxFileSize;

    /**
     * Constructor
     *
     * @param	string	$caption		Caption
     * @param	string	$name			"name" attribute
     * @param	int		$maxfilesize	Maximum size for an uploaded file
     */
    public function __construct($caption, $name, $maxfilesize)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_maxFileSize = (int)$maxfilesize;
    }
    public function XoopsFormFile($caption, $name, $maxfilesize)
    {
        return $this->__construct($caption, $name, $maxfilesize);
    }

    /**
     * Get the maximum filesize
     *
     * @return	int
     */
    public function getMaxFileSize()
    {
        return $this->_maxFileSize;
    }

    /**
     * prepare HTML for output
     *
     * @return	string	HTML
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_file.html');
        $renderTarget->setAttribute('element', $this);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();
    }
}
