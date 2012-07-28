<?php
/**
 *
 * @package Legacy
 * @version $Id: LegacySearchService.class.php,v 1.4 2008/09/25 15:12:43 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
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
        static $ret;
        if (isset($ret)) return $ret;

        $handler =& xoops_gethandler('module');
        
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('isactive', 1));
        $criteria->add(new Criteria('hassearch', 1));

		// shortcut for speedup
		$db = $handler->db;

		$sort = $criteria->getSort();
		$sql = 'SELECT mid,name FROM '.$db->prefix('modules').' '.$criteria->renderWhere().
			($sort?' ORDER BY '.$sort.' '.$criteria->getOrder():' ORDER BY weight '.$criteria->getOrder().', mid ASC');

		$result = $db->query($sql);

        $handler =& xoops_gethandler('groupperm');
        $groupArr = Legacy_SearchUtils::getUserGroups();

        $ret = array();
        while (list($mid, $name) = $db->fetchRow($result)) {
            if ($handler->checkRight('module_read', $mid, $groupArr)) {
				$ret[] = array('mid' => $mid, 'name' => $name);
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
        
        return $this->_searchItems((int)$request->getRequest('mid'), $request->getRequest('queries'), $request->getRequest('andor'), (int)$request->getRequest('maxhit'), (int)$request->getRequest('start'), 0);
    }
    
    function searchItemsOfUser()
    {
        //
        // TODO Need validation
        //
        $root =& XCube_Root::getSingleton();
        $request =& $root->mContext->mRequest;
        
        return $this->_searchItems((int)$request->getRequest('mid'), null, 'and', (int)$request->getRequest('maxhit'), (int)$request->getRequest('start'), (int)$request->getRequest('uid'));
    }
    
    /**
     * @access private
     */
    private function _searchItems($mid, $queries, $andor, $max_hit, $start, $uid)
    {
        $ret = array();

		static $moduleArr;
		if (!isset($moduleArr)) {
			$moduleArr = array();
			foreach ($this->getActiveModules() as $mod) {
				$moduleArr[$mod['mid']] = $mod['name'];
			}
		}

        if (!isset($moduleArr[$mid])) return $ret;

        static $timezone;
        if (!isset($timezone)) {
            $root =& XCube_Root::getSingleton();
            $timezone = $root->mContext->getXoopsConfig('server_TZ') * 3600;
        }

        $handler =& xoops_gethandler('module');
        $xoopsModule =& $handler->get($mid);
        if (!is_object($xoopsModule)) {
            return $ret;
        }
        
        if (!$xoopsModule->get('isactive') || !$xoopsModule->get('hassearch')) {
            return $ret;
        }

        $module =& Legacy_Utils::createModule($xoopsModule, false);
        $results = $module->doLegacyGlobalSearch($queries, $andor, $max_hit, $start, $uid);
                
        if (is_array($results) && count($results) > 0) {
            foreach (array_keys($results) as $key) {
                $timeval =& $results[$key]['time'];
                //
                // TODO If this service will come to web service, we should
                // change format from unixtime to string by timeoffset.
                //
                if ($timeval) $timeval -= $timezone;
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
