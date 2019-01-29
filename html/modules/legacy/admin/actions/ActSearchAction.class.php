<?php
/**
 *
 * @package Legacy
 * @version $Id: ActSearchAction.class.php,v 1.4 2008/09/25 15:11:53 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_LEGACY_PATH."/admin/forms/ActionSearchForm.class.php";

class Legacy_ActionSearchArgs
{
    public $mKeywords;
    public $mRecords;

    public function Legacy_ActionSearchArgs($words) {
        self::__construct($words);
    }

    public function __construct($words)
    {
        $this->setKeywords($words);
    }
    
    public function setKeywords($words)
    {
        foreach (explode(" ", $words) as $word) {
            if (strlen($word) > 0) {
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
        return count($this->mRecords) > 0;
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

    public function Legacy_ActionSearchRecord($moduleName, $url, $title, $desc=null)
    {
        self::__construct($moduleName, $url, $title, $desc);
    }

    public function __construct($moduleName, $url, $title, $desc=null)
    {
        $this->mModuleName = $moduleName;
        $this->mActionUrl = $url;
        $this->mTitle = $title;
        $this->mDescription = $desc;
    }
}

/***
 * @internal
 *  Execute action searching. Now, it returns all of modules' results whether
 * the current user can access to.
 * 
 * @todo We should return the result by the current user's permission.
 */
class Legacy_ActSearchAction extends Legacy_Action
{
    public $mModules = array();
    public $mModuleRecords = null;
    public $mRecords = null;
    public $mActionForm = null;
    
    public $mSearchAction = null;

    public function Legacy_ActSearchAction($flag)
    {
        self::__construct($flag);
    }

    public function __construct($flag)
    {
        parent::__construct($flag);
        
        $this->mSearchAction =new XCube_Delegate();
        $this->mSearchAction->add(array(&$this, 'defaultSearch'));
        $this->mSearchAction->register('Legacy_ActSearchAction.SearchAction');
    }

    public function prepare(&$controller, &$xoopsUser)
    {
        parent::prepare($controller, $xoopsUser);

        $db=&$controller->getDB();

        $mod = $db->prefix("modules");
        $perm = $db->prefix("group_permission");
        $groups = implode(",", $xoopsUser->getGroups());
                         
        $sql = "SELECT DISTINCT ${mod}.mid FROM ${mod},${perm} " .
               "WHERE ${mod}.isactive=1 AND ${mod}.mid=${perm}.gperm_itemid AND ${perm}.gperm_name='module_admin' AND ${perm}.gperm_groupid IN (${groups}) " .
               "ORDER BY ${mod}.weight, ${mod}.mid";

        $result=$db->query($sql);
        
        $handler =& xoops_gethandler('module');
        while ($row = $db->fetchArray($result)) {
            $module =& $handler->get($row['mid']);
            $adapter =new Legacy_ModuleAdapter($module); // FIXMED

            $this->mModules[] =& $adapter;
            
            unset($module);
            unset($adapter);
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
        } else {
            return LEGACY_FRAME_VIEW_ERROR;
        }
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
        $render->setTemplateName("legacy_admin_actionsearch_success.html");
        $render->setAttribute("records", $this->mRecords);
        $render->setAttribute("actionForm", $this->mActionForm);
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("legacy_admin_actionsearch_input.html");
        $render->setAttribute("actionForm", $this->mActionForm);
    }
    
    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("legacy_admin_actionsearch_error.html");
        $render->setAttribute("actionForm", $this->mActionForm);
    }
}
