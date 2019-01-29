<?php
/**
 *
 * @package Legacy
 * @version $Id: PreferenceEditAction.class.php,v 1.11 2008/09/25 15:11:54 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/PreferenceEditForm.class.php";

define("LEGACY_PEREFERENCE_ID_GENERAL", 1);

class Legacy_PreferenceEditAction extends Legacy_Action
{
    public $mPreparedFlag = false;
    
    public $mCategory = null;
    public $mModule = null;

    public $mObjects = array();
    public $mActionForm = null;
    
    public $mState = null;

    public function prepare(&$controller, &$xoopsUser)
    {
        $controller->mRoot->mLanguageManager->loadPageTypeMessageCatalog('comment');
        $controller->mRoot->mLanguageManager->loadPageTypeMessageCatalog('notification');

        $this->mState = (xoops_getrequest('confmod_id') > 0) ? new Legacy_ModulePreferenceEditState($this) : new Legacy_PreferenceEditState($this);
        $this->mState->prepare($controller, $xoopsUser);
        
        if ($this->mPreparedFlag) {
            $handler =& xoops_gethandler('config');
            
            $criteria =new CriteriaCompo();
            $criteria->add(new Criteria('conf_modid', $this->mActionForm->getModuleId()));
            $criteria->add(new Criteria('conf_catid', $this->mActionForm->getCategoryId()));
            
            $this->mObjects =& $handler->getConfigs($criteria);
            $this->mActionForm->prepare($this->mObjects);
        }
    }
    
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (!$this->mPreparedFlag) {
            return LEGACY_FRAME_VIEW_ERROR;
        }
        
        return LEGACY_FRAME_VIEW_INPUT;
    }
    
    public function hasPermission(&$controller, &$xoopsUser)
    {
        return $this->mState->hasPermission($controller, $xoopsUser);
    }
    
    public function execute(&$controller, &$xoopsUser)
    {
        if (!$this->mPreparedFlag) {
            return LEGACY_FRAME_VIEW_ERROR;
        }
        
        if (xoops_getrequest('_form_control_cancel') != null) {
            return LEGACY_FRAME_VIEW_CANCEL;
        }
        
        $this->mActionForm->fetch();
        $this->mActionForm->validate();
        
        if ($this->mActionForm->hasError()) {
            return $this->getDefaultView($controller, $xoopsUser);
        }
        
        $this->mActionForm->update($this->mObjects);
        $handler =& xoops_gethandler('config');
        
        foreach (array_keys($this->mObjects) as $key) {
            if (!$handler->insertConfig($this->mObjects[$key])) {
                die("ERROR" . $this->mObjects[$key]->get('conf_name'));
            }
        }
        
        $this->mState->postFilter($this->mObjects, $this->mActionForm);
        
        return LEGACY_FRAME_VIEW_SUCCESS;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("preference_edit.html");
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('objectArr', $this->mObjects);
        
        $render->setAttribute('category', $this->mCategory);
        $render->setAttribute('module', $this->mModule);
        
        $render->setAttribute('mcrypt_enabled', extension_loaded('mcrypt'));
        
        $formtypeArr = array();
        foreach ($this->mObjects as $object) {
            $formtypeArr[] = $object->get('conf_formtype');
        }
        $formtypeArr = array_unique($formtypeArr);
        
        //
        // Make the array of timezone object
        //
        if (in_array('timezone', $formtypeArr)) {
            $handler =& xoops_gethandler('timezone');
            $timezoneArr =& $handler->getObjects();
            $render->setAttribute('timezoneArr', $timezoneArr);
        }
        
        //
        // Make the array of group object
        //
        if (in_array('group', $formtypeArr)||in_array('group_multi', $formtypeArr)||in_array('group_checkbox', $formtypeArr)) {
            $handler =& xoops_gethandler('group');
            $groupArr =& $handler->getObjects();
            $render->setAttribute('groupArr', $groupArr);
        }

        //
        // Make the array of tplset object
        //
        if (in_array('tplset', $formtypeArr)) {
            $handler =& xoops_gethandler('tplset');
            $tplsetArr =& $handler->getObjects();
            $render->setAttribute('tplsetArr', $tplsetArr);
        }
        
        //
        // Make the list of installed languages.
        //
        if (in_array('language', $formtypeArr)) {
            $languageArr = array();
            $dirHandler = opendir(XOOPS_ROOT_PATH . "/language/");
            while ($file = readdir($dirHandler)) {
                if (is_dir(XOOPS_ROOT_PATH . "/language/" . $file) && preg_match("/^[a-z][0-9a-z_\-]+$/", $file)) {
                    $languageArr[$file] = $file;
                }
            }
            closedir($dirHandler);
            $render->setAttribute('languageArr', $languageArr);
        }
        
        //
        // Make the array of module object for selecting startpage.
        //
        if (in_array('startpage', $formtypeArr)||in_array('module_cache', $formtypeArr)) {
            $handler =& xoops_gethandler('module');
            $criteria =new CriteriaCompo();
            $criteria->add(new Criteria('hasmain', 1));
            $criteria->add(new Criteria('isactive', 1));
            $moduleArr = $handler->getObjects($criteria);
            $render->setAttribute('moduleArr', $moduleArr);
        }
        
        //
        // Make the list of theme.
        //
        if (in_array('theme', $formtypeArr)||in_array('theme_multi', $formtypeArr)) {
            $handler =& xoops_getmodulehandler('theme');
            $themeArr =& $handler->getObjects();
            $render->setAttribute('themeArr', $themeArr);
        }
        
        //
        // Make the array of cachetime.
        //
        if (in_array('module_cache', $formtypeArr)) {
            $handler =& xoops_gethandler('cachetime');
            $cachetimeArr = $handler->getObjects();
            $render->setAttribute('cachetimeArr', $cachetimeArr);
        }

        //
        // Make the list of user
        //
        if (in_array('user', $formtypeArr)||in_array('user_multi', $formtypeArr)) {
            $handler =& xoops_gethandler('member');
            $userArr = $handler->getUserList();
            $render->setAttribute('userArr', $userArr);
        }
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $this->mState->executeViewSuccess($controller, $xoopsUser, $render);
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect("./index.php?action=PreferenceList", 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }
    
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $this->mState->executeViewCancel($controller, $xoopsUser, $render);
    }
}

class Legacy_AbstractPreferenceEditState
{
    public $_mMaster = null;

    public function Legacy_AbstractPreferenceEditState(&$master)
    {
        self::__construct($master);
    }

    public function __construct(&$master)
    {
        $this->_mMaster =& $master;
    }
    
    public function prepare(&$controller, &$xoopsUser)
    {
    }
    
    public function hasPermission(&$controller, &$xoopsUser)
    {
    }
    
    public function postFilter(&$objectArr, &$actionForm)
    {
    }
    
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
    }
}

class Legacy_PreferenceEditState extends Legacy_AbstractPreferenceEditState
{
    public function prepare(&$controller, &$xoopsUser)
    {
        parent::prepare($controller, $xoopsUser);
        
        $handler =& xoops_gethandler('configcategory');
        $this->_mMaster->mCategory =& $handler->get(intval(xoops_getrequest('confcat_id')));
        
        if (!is_object($this->_mMaster->mCategory)) {
            return;
        }

        $this->_mMaster->mActionForm =new Legacy_PreferenceEditForm($this->_mMaster->mCategory);
        
        $this->_mMaster->mPreparedFlag = true;
    }
    
    public function hasPermission(&$controller, &$xoopsUser)
    {
        $moduleHandler =& xoops_gethandler('module');
        $module =& $moduleHandler->getByDirname('legacy');
        
        $permHandler =& xoops_gethandler('groupperm');
        return $permHandler->checkRight('module_admin', $module->get('mid'), $xoopsUser->getGroups());
    }
    
    public function postFilter(&$objectArr, &$actionForm)
    {
        $themeName = null;
        $allowedThemes = null;
        foreach (array_keys($objectArr) as $key) {
            $name = $objectArr[$key]->get('conf_name');
            if ($name == 'theme_set') {
                $themeName = $objectArr[$key]->getConfValueForOutput();
            } elseif ($name == 'theme_set_allowed') {
                $allowedThemes = $actionForm->get('theme_set_allowed');
            } elseif ($name == 'use_mysession') {
                $useMysession = $actionForm->get('use_mysession');
            } elseif ($name == 'session_name') {
                $sessionName = $actionForm->get('session_name');
            } elseif ($name == 'session_expire') {
                $sessionExpire = $actionForm->get('session_expire');
            }
        }
        
        if ($name != null && $allowedThemes != null) {
            XCube_DelegateUtils::call('Legacy.Event.ThemeSettingChanged', $themeName, $allowedThemes);
        }
        
        if ($this->_mMaster->mCategory->get('confcat_id') == LEGACY_PEREFERENCE_ID_GENERAL) { //GIJ
            $root =& XCube_Root::getSingleton();
            if ($useMysession) {
                $root->mSession->setParam($sessionName, $sessionExpire);
            } else {
                $root->mSession->setParam();
            }
            $root->mSession->rename();
        } // GIJ
    }
    
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward("./index.php?action=PreferenceList");
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward("./index.php?action=PreferenceList");
    }
}

class Legacy_ModulePreferenceEditState extends Legacy_AbstractPreferenceEditState
{
    public function prepare(&$controller, &$xoopsUser)
    {
        parent::prepare($controller, $xoopsUser);
        
        $handler =& xoops_gethandler('module');
        $this->_mMaster->mModule =& $handler->get(intval(xoops_getrequest('confmod_id')));

        if (!(is_object($this->_mMaster->mModule) && $this->_mMaster->mModule->get('isactive') &&
              ($this->_mMaster->mModule->get('hasconfig') ||
               $this->_mMaster->mModule->get('hascomments') ||
               $this->_mMaster->mModule->get('hasnotification')))) {
            // Exception
            $controller->executeForward(XOOPS_URL . '/admin.php');
        }

        $this->_mMaster->mActionForm =new Legacy_ModulePreferenceEditForm($this->_mMaster->mModule);
        
        //
        // Load constants
        //
        $root =& XCube_Root::getSingleton();
        $root->mLanguageManager->loadModinfoMessageCatalog($this->_mMaster->mModule->get('dirname'));
        
        $this->_mMaster->mPreparedFlag = true;
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        $controller->mRoot->mRoleManager->loadRolesByModule($this->_mMaster->mModule);
        return $controller->mRoot->mContext->mUser->isInRole('Module.' . $this->_mMaster->mModule->get('dirname') . '.Admin');
    }
    
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $module = Legacy_Utils::createModule($this->_mMaster->mModule);
        XCube_DelegateUtils::call('Legacy.Admin.Event.ModulePreference.' . ucfirst($this->_mMaster->mModule->get('dirname')) . '.Success', new XCube_Ref($module), new XCube_Ref($this->_mMaster->mModule));
        XCube_DelegateUtils::call('Legacy.Admin.Event.ModulePreference.Success', new XCube_Ref($module), new XCube_Ref($this->_mMaster->mModule));
        $controller->executeForward($module->getAdminIndex());
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $module = Legacy_Utils::createModule($this->_mMaster->mModule);
        $controller->executeForward($module->getAdminIndex());
    }
}
