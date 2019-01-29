<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleUninstallAction.class.php,v 1.3 2008/09/25 15:11:51 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_LEGACY_PATH . "/admin/actions/AbstractModuleInstallAction.class.php";
require_once XOOPS_LEGACY_PATH . "/admin/class/ModuleInstallUtils.class.php";
require_once XOOPS_LEGACY_PATH . "/admin/forms/ModuleUninstallForm.class.php";


/**
 * @brief Module Uninstall function having possibility to extend by module developers.
 * 
 * The precondition is that the specified module has been installed && none-actived.
 * 
 * @section cuninstall The custom-uninstaller
 * 
 * Module developers can use their own custom-uninstaller in this action.
 * Unlike the module update function, the standard uninstaller in this action
 * is perhaps no problems. But, duplicatable modules or some modules with the
 * special framework may need the custom-uninstaller.
 * 
 * @subsection convention Convention
 * 
 * See Legacy_ModuleUninstallAction::_getInstaller().
 * 
 * \li $modversion['legacy_installer']['uninstaller']['class'] = {classname};
 * \li $modversion['legacy_installer']['uninstaller']['namespace'] = {namespace}; (Optional)
 * \li $modversion['legacy_installer']['uninstaller']['filepath'] = {filepath}; (Optional)
 * 
 * You must declare your sub-class of Legacy_ModuleUninstaller as
 * {namespace}_{classname} in {filepath}. You must specify classname. Others
 * are decided by the naming convention without your descriptions. Namespace
 * is ucfirst(dirname). Filepath is "admin/class/{classname}.class.php".
 * 
 * For example, "news" module.
 * 
 * $modversion['legacy_installer']['uninstaller']['class'] = "Uninstaller";
 * 
 * You must declare News_Uninstaller in XOOPS_ROOT_PATH . "/modules/news/admin/class/Uninstallerr.class.php".
 * 
 * In the case where you specify the filepath, take care you describe the
 * filepath with absolute path.
 * 
 * @subsection process Uninstall Process
 * 
 * \li Gets a instance of the uninstaller class through Legacy_ModuleUninstallAction::_getInstaller().
 * \li Sets the current XoopsModule to the instance.
 * \li Sets a value indicating whether an administrator hopes the force-mode, to the instance.
 * \li Calls executeUninstall().
 * 
 * @see Legacy_ModuleUninstallAction::_getInstaller()
 * @see Legacy_ModuleUninstaller
 * @see Legacy_ModuleInstallUtils
 * 
 * @todo These classes are good to abstract again.
 */
class Legacy_ModuleUninstallAction extends Legacy_Action
{
    /**
     * @private
     * @var XCube_Delegate
     */
    public $mUninstallSuccess = null;
    
    /**
     * @private
     * @var XCube_Delegate
     */
    public $mUninstallFail = null;
    
    /**
     * @private
     * @var XoopsModule
     */
    public $mXoopsModule = null;
    
    /**
     * @private
     * @var Legacy_ModuleUinstaller
     */
    public $mInstaller = null;
    
    public function Legacy_ModuleUninstallAction($flag)
    {
        self::__construct($flag);
    }

    public function __construct()
    {
        parent::__construct($flag);
        
        $this->mUninstallSuccess =new XCube_Delegate();
        $this->mUninstallSuccess->register('Legacy_ModuleUninstallAction.UninstallSuccess');
        
        $this->mUninstallFail =new XCube_Delegate();
        $this->mUninstallFail->register('Legacy_ModuleUninstallAction.UninstallFail');
    }

    public function prepare(&$controller, &$xoopsUser)
    {
        $dirname = $controller->mRoot->mContext->mRequest->getRequest('dirname');
        
        $handler =& xoops_gethandler('module');
        $this->mXoopsModule =& $handler->getByDirname($dirname);
        
        if (!(is_object($this->mXoopsModule) && $this->mXoopsModule->get('isactive') == 0)) {
            return false;
        }
        $this->mXoopsModule->loadInfoAsVar($dirname);
        
        $this->_setupActionForm();
        
        $this->mInstaller =& $this->_getInstaller();
        
        //
        // Set the current object.
        //
        $this->mInstaller->setCurrentXoopsModule($this->mXoopsModule);
        
        return true;
    }
    
    public function &_getInstaller()
    {
        $dirname = $this->mXoopsModule->get('dirname');
        $installer =&  Legacy_ModuleInstallUtils::createUninstaller($dirname);
        return $installer;
    }
    
    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_ModuleUninstallForm();
        $this->mActionForm->prepare();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $this->mActionForm->load($this->mXoopsModule);
        
        return LEGACY_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (isset($_REQUEST['_form_control_cancel'])) {
            return LEGACY_FRAME_VIEW_CANCEL;
        }
        
        $this->mActionForm->fetch();
        $this->mActionForm->validate();
        
        if ($this->mActionForm->hasError()) {
            return $this->getDefaultView($controller, $xoopsUser);
        }
        
        $this->mInstaller->setForceMode($this->mActionForm->get('force'));
        $this->mInstaller->executeUninstall();

        return LEGACY_FRAME_VIEW_SUCCESS;
    }
    
    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        if (!$this->mInstaller->mLog->hasError()) {
            $this->mUninstallSuccess->call(new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
            XCube_DelegateUtils::call('Legacy.Admin.Event.ModuleUninstall.' . ucfirst($this->mXoopsModule->get('dirname') . '.Success'), new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
            XCube_DelegateUtils::call('Legacy.Admin.Event.ModuleUninstall.Success', new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
        } else {
            $this->mUninstallFail->call(new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
            XCube_DelegateUtils::call('Legacy.Admin.Event.ModuleUninstall.' . ucfirst($this->mXoopsModule->get('dirname') . '.Fail'), new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
            XCube_DelegateUtils::call('Legacy.Admin.Event.ModuleUninstall.Fail', new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
        }

        $renderer->setTemplateName("module_uninstall_success.html");
        $renderer->setAttribute('module', $this->mXoopsModule);
        $renderer->setAttribute('log', $this->mInstaller->mLog->mMessages);
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$renderer)
    {
        $renderer->setTemplateName("module_uninstall.html");
        $renderer->setAttribute('actionForm', $this->mActionForm);
        $renderer->setAttribute('module', $this->mXoopsModule);
        $renderer->setAttribute('currentVersion', round($this->mXoopsModule->get('version') / 100, 2));
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward("./index.php?action=ModuleList");
    }
}
