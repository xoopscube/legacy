<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleUpdateAction.class.php,v 1.3 2008/09/25 15:11:54 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_LEGACY_PATH . "/admin/actions/AbstractModuleInstallAction.class.php";
require_once XOOPS_LEGACY_PATH . "/admin/class/ModuleInstallUtils.class.php";
require_once XOOPS_LEGACY_PATH . "/admin/forms/ModuleUpdateForm.class.php";

/**
 * @brief Module Update function having possibility to extend by module developers.
 * 
 * The precondition is that the specified module has been installed.
 * 
 * @section cupdate The custom-update-installer
 * 
 * Module developers can use their own custom-update-installer in this action.
 * This function uses Legacy_ModulePhasedUpgrader to update moudles. But, this
 * class can't smart update modules correctly & automatically, because the
 * module updat function bases on XOOPS2 JP spec. We have no rules to declare
 * modules strictly.
 * 
 * To solve it, module developers should use the custom-update-installer,
 * because module developers know detail changelog of their module.
 * 
 * @subsection convention Convention
 * 
 * See Legacy_ModuleUpdateAction::_getInstaller().
 * 
 * \li $modversion['legacy_installer']['updater']['class'] = {classname};
 * \li $modversion['legacy_installer']['updater']['namespace'] = {namespace}; (Optional)
 * \li $modversion['legacy_installer']['updater']['filepath'] = {filepath}; (Optional)
 * 
 * You must declare your sub-class of Legacy_ModulePhasedUpgrader as
 * {namespace}_{classname} in {filepath}. You must specify classname. Others
 * are decided by the naming convention without your descriptions. Namespace
 * is ucfirst(dirname). Filepath is "admin/class/{classname}.class.php".
 * 
 * For example, "news" module.
 * 
 * $modversion['legacy_installer']['updater']['class'] = "Updater";
 * 
 * You must declare News_Updater in XOOPS_ROOT_PATH . "/modules/news/admin/class/Updater.class.php".
 * 
 * In the case where you specify the filepath, take care you describe the
 * filepath with absolute path.
 * 
 * @subsection process Install Process
 * 
 * \li Gets a instance of the update installer class through Legacy_ModuleUpdateAction::_getInstaller().
 * \li Sets the current XoopsModule to the instance.
 * \li Builds the target XoopsModule from xoops_version, and sets it to the instance.
 * \li Sets a value indicating whether an administrator hopes the force-mode, to the instance.
 * \li Calls executeUpgrade().
 * 
 * @see Legacy_ModuleUpdateAction::_getInstaller()
 * @see Legacy_ModulePhasedUpgrader
 * @see Legacy_ModuleInstallUtils
 */
class Legacy_ModuleUpdateAction extends Legacy_Action
{
    /**
     * @var XCube_Delegate
     */
    public $mUpdateSuccess = null;
    
    /**
     * @var XCube_Delegate
     */
    public $mUpdateFail = null;
    
    public $mXoopsModule = null;
    
    public $mInstaller = null;
    
    public function  Legacy_ModuleUpdateAction($flag)
    {
        self::__construct($flag);
    }

    public function __construct($flag)
    {
        parent::__construct($flag);
        
        $this->mUpdateSuccess =new XCube_Delegate();
        $this->mUpdateSuccess->register('Legacy_ModuleUpdateAction.UpdateSuccess');
        
        $this->mUpdateFail =new XCube_Delegate();
        $this->mUpdateFail->register('Legacy_ModuleUpdateAction.UpdateFail');
    }
    
    public function prepare(&$controller, &$xoopsUser)
    {
        $dirname = $controller->mRoot->mContext->mRequest->getRequest('dirname');
        
        $handler =& xoops_gethandler('module');
        $this->mXoopsModule =& $handler->getByDirname($dirname);
        
        if (!is_object($this->mXoopsModule)) {
            return false;
        }
        
        $this->_setupActionForm();
        
        $this->mInstaller =& $this->_getInstaller();
        
        //
        // Set the current object.
        //
        $this->mInstaller->setCurrentXoopsModule($this->mXoopsModule);
        
        //
        // Load the manifesto, and set it as the target object.
        //
        $name = $this->mXoopsModule->get('name');
        $this->mXoopsModule->loadInfoAsVar($dirname);
        $this->mXoopsModule->set('name', $name);
        $this->mInstaller->setTargetXoopsModule($this->mXoopsModule);
        
        return true;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_ModuleUpdateForm();
        $this->mActionForm->prepare();
    }

    /**
     * Creates a instance of the upgrade installer to mInstaller. And returns
     * it.
     * 
     * The precondition is the existence of mXoopsModule.
     */
    public function &_getInstaller()
    {
        $dirname = $this->mXoopsModule->get('dirname');
        $installer =& Legacy_ModuleInstallUtils::createUpdater($dirname);
        return $installer;
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
        $this->mInstaller->executeUpgrade();

        return LEGACY_FRAME_VIEW_SUCCESS;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        if (!$this->mInstaller->mLog->hasError()) {
            $this->mUpdateSuccess->call(new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
            XCube_DelegateUtils::call('Legacy.Admin.Event.ModuleUpdate.' . ucfirst($this->mXoopsModule->get('dirname')) . '.Success', new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
            XCube_DelegateUtils::call('Legacy.Admin.Event.ModuleUpdate.Success', new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
        } else {
            $this->mUpdateFail->call(new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
            XCube_DelegateUtils::call('Legacy.Admin.Event.ModuleUpdate.' . ucfirst($this->mXoopsModule->get('dirname')) . '.Fail', new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
            XCube_DelegateUtils::call('Legacy.Admin.Event.ModuleUpdate.Fail', new XCube_Ref($this->mXoopsModule), new XCube_Ref($this->mInstaller->mLog));
        }
        
        $renderer->setTemplateName("module_update_success.html");
        $renderer->setAttribute('module', $this->mXoopsModule);
        $renderer->setAttribute('log', $this->mInstaller->mLog->mMessages);
        $renderer->setAttribute('currentVersion', round($this->mInstaller->getCurrentVersion() / 100, 2));
        $renderer->setAttribute('targetVersion', round($this->mInstaller->getTargetPhase() / 100, 2));
        $renderer->setAttribute('isPhasedMode', $this->mInstaller->hasUpgradeMethod());
        $renderer->setAttribute('isLatestUpgrade', $this->mInstaller->isLatestUpgrade());
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$renderer)
    {
        $renderer->setTemplateName("module_update.html");
        $renderer->setAttribute('module', $this->mXoopsModule);
        $renderer->setAttribute('actionForm', $this->mActionForm);
        $renderer->setAttribute('currentVersion', round($this->mInstaller->getCurrentVersion() / 100, 2));
        $renderer->setAttribute('targetVersion', round($this->mInstaller->getTargetPhase() / 100, 2));
        $renderer->setAttribute('isPhasedMode', $this->mInstaller->hasUpgradeMethod());
    }
    
    public function executeViewCancel(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward("./index.php?action=ModuleList");
    }
}
