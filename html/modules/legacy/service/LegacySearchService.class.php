<?php
/**
 *
 * @package Legacy
 * @version $Id: LegacySearchService.class.php,v 1.4 2008/09/25 15:12:43 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_SearchModule extends XCube_Object
{
    function getPropertyDefinition()
    {
        $ret = array(
            S_PUBLIC_VAR("int mid"),
            S_PUBLIC_VAR("string name")
        );
        
        return $ret;
    }
}

class Legacy_SearchModuleArray extends XCube_ObjectArray
{
    function getClassName()
    {
        return "Legacy_SearchModule";
    }
}


class Legacy_SearchItem extends XCube_Object
{
    function getPropertyDefinition()
    {
        $ret = array(
            S_PUBLIC_VAR("string image"),
            S_PUBLIC_VAR("string link"),
            S_PUBLIC_VAR("string title"),
            S_PUBLIC_VAR("int uid"),
            S_PUBLIC_VAR("int time")
        );
        
        return $ret;
    }
}

class Legacy_SearchItemArray extends XCube_ObjectArray
{
    function getClassName()
    {
        return "Legacy_SearchItem";
    }
}

class Legacy_SearchModuleResult extends XCube_Object
{
    function getPropertyDefinition()
    {
        $ret = array(
            S_PUBLIC_VAR("int mid"),
            S_PUBLIC_VAR("string name"),
            S_PUBLIC_VAR("int has_more"),
            S_PUBLIC_VAR("Legacy_SearchItemArray results"),
            S_PUBLIC_VAR("string showall_link")
        );
        
        return $ret;
    }
}

class Legacy_SearchModuleResultArray extends XCube_ObjectArray
{
    function getClassName()
    {
        return "Legacy_SearchModuleResult";
    }
}

class Legacy_ArrayOfInt extends XCube_ObjectArray
{
    function getClassName()
    {
        return "int";
    }
}

class Legacy_ArrayOfString extends XCube_ObjectArray
{
    function getClassName()
    {
        return "string";
    }
}

/**
 * Sample class
 */
class Legacy_SearchService extends XCube_Service
{
    var $mServiceName = "Legacy_SearchService";
    var $mNameSpace = "Legacy";
    var $mClassName = "Legacy_SearchService";
    
    function prepare()
    {
        $this->addType('Legacy_SearchModule');
        $this->addType('Legacy_SearchModuleArray');
        $this->addType('Legacy_SearchItem');
        $this->addType('Legacy_SearchItemArray');
        $this->addType('Legacy_SearchModuleResult');
        $this->addType('Legacy_SearchModuleResultArray');
        $this->addType('Legacy_ArrayOfInt');
        $this->addType('Legacy_ArrayOfString');
    
        $this->addFunction(S_PUBLIC_FUNC('Legacy_SearchItemArray searchItems(int mid, Legacy_ArrayOfString queries, string andor, int maxhit, int start)'));
        $this->addFunction(S_PUBLIC_FUNC('Legacy_SearchItemArray searchItemsOfUser(int mid, int uid, int maxhit, int start)'));
        $this->addFunction(S_PUBLIC_FUNC('Legacy_SearchModuleArray getActiveModules()'));
    }
    
    function getActiveModules()
    {
        //
        // At first, get active module IDs.
        //
        $handler =& xoops_gethandler('module');
        
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('isactive', 1));
        $criteria->add(new Criteria('hassearch', 1));
        $moduleArr =& $handler->getObjects($criteria);


        $handler =& xoops_gethandler('groupperm');
        $groupArr = Legacy_SearchUtils::getUserGroups();

        $ret = array();
        foreach ($moduleArr as $module) {
            if ($handler->checkRight('module_read', $module->get('mid'), $groupArr)) {
                $ret[] = array(
                    'mid' => $module->get('mid'),
                    'name' => $module->get('name')
                );
            }
        }
        
        return $ret;
    }
    
    function searchItems()
    {
        //
        // TODO Need validation
        //
        $root =& XCube_Root::getSingleton();
        $request =& $root->mContext->mRequest;
        
        $mid = intval($request->getRequest('mid'));
        $queries = $request->getRequest('queries');
        $andor = $request->getRequest('andor');
        $maxhit = intval($request->getRequest('maxhit'));
        $start = intval($request->getRequest('start'));
        
        $ret = $this->_searchItems($mid, $queries, $andor, $maxhit, $start, 0);
        
        return $ret;
    }
    
    function searchItemsOfUser()
    {
        //
        // TODO Need validation
        //
        $root =& XCube_Root::getSingleton();
        $request =& $root->mContext->mRequest;
        
        $mid = intval($request->getRequest('mid'));
        $maxhit = intval($request->getRequest('maxhit'));
        $start = intval($request->getRequest('start'));
        $uid = intval($request->getRequest('uid'));
        
        $ret = $this->_searchItems($mid, null, 'and', $maxhit, $start, $uid);
        
        return $ret;
    }
    
    /**
     * @access private
     */
    function _searchItems($mid, $queries, $andor, $max_hit, $start, $uid)
    {
        $ret = array();

        $modleArr = $this->getActiveModules();
        
        $flag = false;
        foreach ($modleArr as $module) {
            if ($mid == $module['mid']) {
                $flag = true;
                break;
            }
        }
        
        if (!$flag) {
            return $ret;
        }
        
        $root =& XCube_Root::getSingleton();
        $timezone = $root->mContext->getXoopsConfig('server_TZ') * 3600;

        $handler =& xoops_gethandler('module');
        $xoopsModule =& $handler->get($mid);
        if (!is_object($xoopsModule)) {
            return $ret;
        }
        
        if (!$xoopsModule->get('isactive') || !$xoopsModule->get('hassearch')) {
            return $ret;
        }

        $module =& Legacy_Utils::createModule($xoopsModule);
        $results = $module->doLegacyGlobalSearch($queries, $andor, $max_hit, $start, $uid);
                
        if (is_array($results) && count($results) > 0) {
            foreach (array_keys($results) as $key) {
                //
                // TODO If this service will come to web service, we should
                // change format from unixtime to string by timeoffset.
                //
                if ($results[$key]['time'] != 0) {
                    $results[$key]['time'] = $results[$key]['time'] - $timezone;
                }
            }
        }
        
        return $results;
    }
}

class Legacy_SearchUtils
{
    function getUserGroups()
    {
        $root =& XCube_Root::getSingleton();
        $user =& $root->mController->mRoot->mContext->mXoopsUser;
        $groups = array();
        
        if (!is_object($user)) {
            $groups = XOOPS_GROUP_ANONYMOUS;
        }
        else {
            $groups = $user->getGroups();
        }
        
        return $groups;
    }
}

?>
