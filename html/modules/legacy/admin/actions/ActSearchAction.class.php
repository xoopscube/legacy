<?php
/**
 * ActSearchAction.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_LEGACY_PATH . '/admin/forms/ActionSearchForm.class.php';

class Legacy_ActionSearchArgs
{
    public $mKeywords;
    public $mRecords;

    public function __construct($words)
    {
        $this->setKeywords($words);
    }

    public function setKeywords($words)
    {
        foreach (explode(' ', $words) as $word) {
            if ($word !== '') {
                $this->mKeywords[] = $word;
            }
        }
    }

    public function getKeywords()
    {
        return $this->mKeywords;
    }

    public function addRecord($moduleName, $url, $title, $desc = null)
    {
        $this->mRecords[] =new Legacy_ActionSearchRecord($moduleName, $url, $title, $desc);
    }

    public function &getRecords()
    {
        return $this->mRecords;
    }

    /**
     * @return bool
     */
    public function hasRecord()
    {
         if(is_countable($this->mRecords)) {
             return count($this->mRecords) > 0;
         }
         return false;
    }

}

/**
 * An item on one search record. This is a class as a structure.
 *
 * @todo we may change it to Array.
 */
class Legacy_ActionSearchRecord
{
    public $mModuleName;
    public $mActionUrl;
    public $mTitle;
    public $mDescription;

// TODO mDescription = $desc returns nothing !

    public function __construct($moduleName, $url, $title, $desc)
    {
        $this->mModuleName = $moduleName;
        $this->mActionUrl = $url;
        $this->mTitle = $title;
        $this->mDescription = $desc;
    }
}

/***
 * @internal
 * Execute action search. Now,it returns the results of all modules that the current user can access.
 *
 * @todo We should return the result by the current user's permission.
 */
class Legacy_ActSearchAction extends Legacy_Action
{
    public $mModules = [];
    public $mModuleRecords = null;
    public $mRecords = null;
    public $mActionForm = null;

    public $mSearchAction = null;

    public function __construct($flag)
    {
        parent::__construct($flag);

        $this->mSearchAction =new XCube_Delegate();
        $this->mSearchAction->add([&$this, 'defaultSearch']);
        $this->mSearchAction->register('Legacy_ActSearchAction.SearchAction');
    }

    public function prepare(&$controller, &$xoopsUser)
    {
        parent::prepare($controller, $xoopsUser);

        $db=&$controller->getDB();

        $mod = $db->prefix('modules');
        $perm = $db->prefix('group_permission');
        $groups = implode(',', $xoopsUser->getGroups());

        $sql = "SELECT DISTINCT {$mod}.weight, {$mod}.mid FROM {$mod},{$perm} " .
               "WHERE {$mod}.isactive=1 AND {$mod}.mid={$perm}.gperm_itemid AND {$perm}.gperm_name='module_admin' AND {$perm}.gperm_groupid IN ({$groups}) " .
               "ORDER BY {$mod}.weight, {$mod}.mid";

        $result=$db->query($sql);

        $handler =& xoops_gethandler('module');
        while ($row = $db->fetchArray($result)) {
            $module =& $handler->get($row['mid']);
            $adapter =new Legacy_ModuleAdapter($module); // FIXMED

            $this->mModules[] =& $adapter;

            unset($module, $adapter);
        }
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        $permHandler =& xoops_gethandler('groupperm');
        return $permHandler->checkRight('module_admin', -1, $xoopsUser->getGroups());
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $this->_processActionForm();

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return LEGACY_FRAME_VIEW_INPUT;
        }

        $searchArgs =new Legacy_ActionSearchArgs($this->mActionForm->get('keywords'));
        $this->mSearchAction->call(new XCube_Ref($searchArgs));

        if ($searchArgs->hasRecord()) {
            $this->mRecords =& $searchArgs->getRecords();
            return LEGACY_FRAME_VIEW_SUCCESS;
        }

        return LEGACY_FRAME_VIEW_ERROR;
    }

    public function defaultSearch(&$searchArgs)
    {
        foreach (array_keys($this->mModules) as $key) {
            $this->mModules[$key]->doActionSearch($searchArgs);
        }
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return $this->getDefaultView($controller, $xoopsUser);
    }

    public function _processActionForm()
    {
        $this->mActionForm =new Legacy_ActionSearchForm();
        $this->mActionForm->prepare();
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_admin_actionsearch_success.html');
        $render->setAttribute('records', $this->mRecords);
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_admin_actionsearch_input.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_admin_actionsearch_error.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }
}
