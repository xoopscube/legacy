<?php
/**
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/ModuleEditForm.class.php';

class Legacy_ModuleEditAction extends Legacy_AbstractEditAction
{

    public $mReadGroups = [];
    public $mAdminGroups = [];

    public function _getId()
    {
        return isset($_REQUEST['mid']) ? xoops_getrequest('mid') : 0;
    }

    public function isEnableCreate()
    {
        return false;
    }

    public function &_getHandler()
    {
        $handler =& xoops_gethandler('module');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_ModuleEditForm();
        $this->mActionForm->prepare();
    }

    public function _isEditable()
    {
        if (is_object($this->mObject)) {
            return (1 == $this->mObject->get('isactive'));
        } else {
            return false;
        }
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (!$this->_isEditable()) {
            return LEGACY_FRAME_VIEW_ERROR;
        }
        if (null == $this->mObject) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        $this->mActionForm->load($this->mObject);
        return LEGACY_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (!$this->_isEditable()) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        $ret = parent::execute($controller, $xoopsUser);

        if (LEGACY_FRAME_VIEW_SUCCESS == $ret) {
            $handler =& xoops_gethandler('group');
            $permHandler =& xoops_gethandler('groupperm');

            foreach ($this->mActionForm->get('read_groupid') as $readgroupid) {
                $this->mReadGroups[] =& $handler->get($readgroupid);
            }
            foreach ($this->mActionForm->get('admin_groupid') as $admingroupid) {
                $this->mAdminGroups[] =& $handler->get($admingroupid);
            }

            //
            // Insert group permissions.
            //
            $currentReadGroupid = [];
            foreach ($this->mReadGroups as $readgroup) {
                $currentReadGroupid[] = $readgroup->get('groupid');
            }
            $currentAdminGroupid = [];
            foreach ($this->mAdminGroups as $admingroup) {
                $currentAdminGroupid[] = $admingroup->get('groupid');
            }
            //readperm
            $criteria =new CriteriaCompo();
            $criteria->add(new Criteria('gperm_modid', 1));
            $criteria->add(new Criteria('gperm_itemid', $this->mObject->get('mid')));
            $criteria->add(new Criteria('gperm_name', 'module_read'));

            $gpermArr =&  $permHandler->getObjects($criteria);
            foreach ($gpermArr as $gperm) {
                if (!in_array($gperm->get('gperm_groupid'), $currentReadGroupid)) {
                    if (!$permHandler->delete($gperm)) {
                        return LEGACY_FRAME_VIEW_ERROR;
                    }
                }
            }

            foreach ($this->mReadGroups as $readgroup) {
                $insertFlag = true;
                foreach ($gpermArr as $gperm) {
                    if ($gperm->get('gperm_groupid') == $readgroup->get('groupid')) {
                        $insertFlag = false;
                    }
                }

                if ($insertFlag) {
                    $gperm =& $permHandler->create();
                    $gperm->set('gperm_modid', 1);
                    $gperm->set('gperm_groupid', $readgroup->get('groupid'));
                    $gperm->set('gperm_itemid', $this->mObject->get('mid'));
                    $gperm->set('gperm_name', 'module_read');
                    if (!$permHandler->insert($gperm)) {
                        return LEGACY_FRAME_VIEW_ERROR;
                    }
                }
            }
            //admin perm
            $criteria =new CriteriaCompo();
            $criteria->add(new Criteria('gperm_modid', 1));
            $criteria->add(new Criteria('gperm_itemid', $this->mObject->get('mid')));
            $criteria->add(new Criteria('gperm_name', 'module_admin'));

            $gpermArr =&  $permHandler->getObjects($criteria);
            foreach ($gpermArr as $gperm) {
                if (!in_array($gperm->get('gperm_groupid'), $currentAdminGroupid)) {
                    if (!$permHandler->delete($gperm)) {
                        return LEGACY_FRAME_VIEW_ERROR;
                    }
                }
            }

            foreach ($this->mAdminGroups as $admingroup) {
                $insertFlag = true;
                foreach ($gpermArr as $gperm) {
                    if ($gperm->get('gperm_groupid') == $admingroup->get('groupid')) {
                        $insertFlag = false;
                    }
                }

                if ($insertFlag) {
                    $gperm =& $permHandler->create();
                    $gperm->set('gperm_modid', 1);
                    $gperm->set('gperm_groupid', $admingroup->get('groupid'));
                    $gperm->set('gperm_itemid', $this->mObject->get('mid'));
                    $gperm->set('gperm_name', 'module_admin');
                    if (!$permHandler->insert($gperm)) {
                        return LEGACY_FRAME_VIEW_ERROR;
                    }
                }
            }

            //module_cache
            $confighandler =& xoops_gethandler('config');
            $criteria =new CriteriaCompo();
            $criteria->add(new Criteria('conf_name', 'module_cache'));
            $criteria->add(new Criteria('conf_catid', XOOPS_CONF));
            $configObjects =& $confighandler->getConfigs($criteria);
            if (is_object($configObjects[0])) {
                $oldvalue = $configObjects[0]->get('conf_value');
                $t_arr = !empty($oldvalue) ? unserialize($oldvalue) : [];
                if (is_array($t_arr)) {
                    $t_arr[$this->mObject->get('mid')] = $this->mActionForm->get('module_cache');
                    $configObjects[0]->set('conf_value', serialize($t_arr));
                    if (!$confighandler->insertConfig($configObjects[0])) {
                        return LEGACY_FRAME_VIEW_ERROR;
                    }
                }//is_array
            else {
                return LEGACY_FRAME_VIEW_ERROR;
            }
            }//is_object
            else {
                return LEGACY_FRAME_VIEW_ERROR;
            }
        }

        return $ret;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $this->mObject->loadInfo($this->mObject->getShow('dirname'));
        $render->setTemplateName('module_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);

        $handler =& xoops_gethandler('groupperm');
        $grouphandler =& xoops_gethandler('group');
        $groupArr =& $grouphandler->getObjects();
        $render->setAttribute('groupArr', $groupArr);

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('gperm_modid', 1));
        $criteria->add(new Criteria('gperm_itemid', $this->mObject->get('mid')));
        $criteria->add(new Criteria('gperm_name', 'module_read'));
        $gpermReadArr =&  $handler->getObjects($criteria);
        $readgroupid = [];
        foreach ($gpermReadArr as $gpermRead) {
            $readgroupid[] = $gpermRead->get('gperm_groupid');
        }
        $render->setAttribute('readgroupidArr', $readgroupid);

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('gperm_modid', 1));
        $criteria->add(new Criteria('gperm_itemid', $this->mObject->get('mid')));
        $criteria->add(new Criteria('gperm_name', 'module_admin'));
        $gpermAdminArr =&  $handler->getObjects($criteria);
        $admingroupid = [];
        foreach ($gpermAdminArr as $gpermAdmin) {
            $admingroupid[] = $gpermAdmin->get('gperm_groupid');
        }
        $render->setAttribute('admingroupidArr', $admingroupid);
        //for modulecache
        $cachehandler =& xoops_gethandler('cachetime');
        $cachetimeArr =& $cachehandler->getObjects();
        $render->setAttribute('cachetimeArr', $cachetimeArr);
    }


    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ModuleList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=ModuleList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=ModuleList');
    }
}
