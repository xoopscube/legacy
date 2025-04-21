<?php
/**
 * HelpAction.class.php
 * The subclass of Smarty for the Help viewer
 *
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

 if (!defined('XOOPS_ROOT_PATH')) {
     exit();
 }

/***
 * @internal
 *
 * The subclass of Smarty for the Help viewer, allows using Smarty in html help files.
 * This class extends Smarty to avoid filename collisions in compiled filenames.
 * Smarty custom modifiers supported in Help view :
 * 'helpurl'   - modify a relative URL to the dynamic page link.
 * 'helpimage' - modify an image URL.
 * Since XCL 2.3
 * Usage : <{$help}>=module_dirname
 * Example
 * Module  : <a href="<{$help}>=legacy">System</a>
 * File    : <a href="<{$help}>=legacy&amp;file=block.html">Block</a>
 * These modifiers take into account the existence of language files.
 */
class Legacy_HelpSmarty extends Smarty
{
    /**
     * @var string
     */
    public $mDirname = null;

    /**
     * @var XoopsModule
     */
    public $mModuleObject = null;

    /**
     * @var string
     */
    public $mFilename = null;

    /**
     * @var bool
     */
    private $_canUpdateFromFile = true;

    public function __construct()
    {
        parent::Smarty();

        $this->compile_id = null;
        $this->compile_check = true;
        $this->compile_dir = XOOPS_COMPILE_PATH;
        $this->left_delimiter = '<{';
        $this->right_delimiter = '}>';

        $this->force_compile = true;

        $this->register_modifier('helpurl', 'Legacy_modifier_helpurl');
        $this->register_modifier('helpimage', 'Legacy_modifier_helpimage');
    }

    public function setDirname($dirname)
    {
        $this->mDirname = $dirname;
    }

    /**
     * @param string $autoBase
     * @param null $autoSource
     * @param null $autoId
     * @return string
     */
    public function _get_auto_filename($autoBase, $autoSource = null, $autoId = null)
    {
        $autoSource = $this->mDirname . '_help_' . $autoSource;
        return parent::_get_auto_filename($autoBase, $autoSource, $autoId);
    }
}

/**
 * @param $file
 * @param null $dirname
 * @return string
 */
function Legacy_modifier_helpurl($file, $dirname = null)
{
    $root =& XCube_Root::getSingleton();

    $language = $root->mContext->getXoopsConfig('language');
    $dirname = $root->mContext->getAttribute('legacy_help_dirname');

    if (null === $dirname) {
        $moduleObject =& $root->mContext->mXoopsModule;
        $dirname = $moduleObject->get('dirname');
    }

    //
    // TODO We should check file_exists -> line 201
    //

    return XOOPS_MODULE_URL . "/legacy/admin/index.php?action=Help&amp;dirname={$dirname}&amp;file={$file}";
}

/**
 * @param $file
 * @return string
 */
function Legacy_modifier_helpimage($file)
{
    $root =& XCube_Root::getSingleton();

    $language = $root->mContext->getXoopsConfig('language');
    $dirname = $root->mContext->getAttribute('legacy_help_dirname');

    $path = "/{$dirname}/language/{$language}/help/images/{$file}";
    if (!file_exists(XOOPS_MODULE_PATH . $path) && 'english' !== $language) {
        $path = "/{$dirname}/language/english/help/images/{$file}";
    }

    return XOOPS_MODULE_URL . $path;
}

/***
 * @internal
 * This action will show the information of a module specified to user.
 */
class Legacy_HelpAction extends Legacy_Action
{
    public $mModuleObject = null;
    public $mContents = null;

    public $mErrorMessage = null;

    /**
     * @access private
     */
    public $_mDirname = null;

    /**
     * @var XCube_Delegate
     */
    public $mCreateHelpSmarty = null;

    public function __construct($flag)
    {
        parent::__construct($flag);

        $this->mCreateHelpSmarty =new XCube_Delegate();
        $this->mCreateHelpSmarty->add([&$this, '_createHelpSmarty']);
        $this->mCreateHelpSmarty->register('Legacy_HelpAction.CreateHelpSmarty');
    }

    public function prepare(&$controller, &$xoopsUser)
    {
        parent::prepare($controller, $xoopsUser);
        $this->_mDirname = xoops_getrequest('dirname');
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        $dirname = xoops_getrequest('dirname');
        $controller->mRoot->mRoleManager->loadRolesByDirname($this->_mDirname);
        return $controller->mRoot->mContext->mUser->isInRole('Module.' . $dirname . '.Admin');
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $moduleHandler =& xoops_gethandler('module');
        $this->mModuleObject =& $moduleHandler->getByDirname($this->_mDirname);
        $language = $controller->mRoot->mContext->getXoopsConfig('language');

        //
        // TODO We must change the following lines to ActionForm.
        //
        $helpfile = xoops_getrequest('file') ?: $this->mModuleObject->getHelp();

        //
        // Smarty
        //
        $smarty = null;
        $this->mCreateHelpSmarty->call(new XCube_Ref($smarty));
        $smarty->setDirname($this->_mDirname);

        //
        // file check
        //
        // TODO We should not access files in language directory directly.
        //
        $template_dir = XOOPS_MODULE_PATH . '/' . $this->_mDirname . "/language/{$language}/help";
        if (!file_exists($template_dir . '/' . $helpfile)) {
            $template_dir = XOOPS_MODULE_PATH . '/' . $this->_mDirname . '/language/english/help';
            if (!file_exists($template_dir . '/' . $helpfile)) {
                $this->mErrorMessage = _AD_LEGACY_ERROR_NO_HELP_FILE;
                return LEGACY_FRAME_VIEW_ERROR;
            }
        }

        $controller->mRoot->mContext->setAttribute('legacy_help_dirname', $this->_mDirname);
        // Since XCL 2.3
        // Usage   : <{$help}>=module_dirname
        // Example
        // Module  : <a href="<{$help}>=legacy">System</a>
        // File    : <a href="<{$help}>=legacy&amp;file=block.html">Block</a>
        $mHelp = XOOPS_MODULE_URL . "/legacy/admin/index.php?action=Help&amp;dirname";
        $smarty->assign('help',$mHelp);
        $smarty->template_dir = $template_dir;
        $this->mContents = $smarty->fetch('file:' . $helpfile);

        return LEGACY_FRAME_VIEW_SUCCESS;
    }


    public function _createHelpSmarty(&$smarty)
    {
        if (!is_object($smarty)) {
            $smarty = new Legacy_HelpSmarty();
        }
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        $renderer->setTemplateName('help.html');

        $module =& Legacy_Utils::createModule($this->mModuleObject);

        $renderer->setAttribute('module', $module);
        $renderer->setAttribute('contents', $this->mContents);
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeRedirect('./index.php?action=ModuleList', 1, $this->mErrorMessage);
    }
}
