<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_Utils.class.php,v 1.5 2008/09/25 15:11:21 kilica Exp $
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

/***
 * @internal
 * @public
 * @brief The collection of static utility functions for Legacy.
 */
class Legacy_Utils
{
    /***
     * Checks whether must modules have been installed.
     * @static
     * @return mixed Returns hashmap including the list of uninstalled,
     * disabled and recommended modules, basically. But, if there is no problem,
     * returns true.
     */
    public static function checkSystemModules()
    {
        $root=&XCube_Root::getSingleton();
        $systemModules = array_map('trim', explode(',', $root->getSiteConfig('Cube', 'SystemModules')));
        $recommendedModules = array_map('trim', explode(',', $root->getSiteConfig('Cube', 'RecommendedModules')));
        $moduleHandler =& xoops_gethandler('module');
        $uninstalledModules = [];
        $disabledModules = [];
        foreach ($systemModules as $systemModule) {
            if (!empty($systemModule)) {
                if (!($moduleObject =& $moduleHandler->getByDirname($systemModule))) {
                    $uninstalledModules[] = $systemModule;
                } elseif (!$moduleObject->get('isactive')) {
                    $disabledModules[] = $systemModule;
                }
            }
        }
        if (0 == count($uninstalledModules) && 0 == count($disabledModules)) {
            return true;
        } else {
            return ['uninstalled' =>$uninstalledModules, 'disabled' =>$disabledModules, 'recommended' =>$recommendedModules];
        }
    }

    /***
     * Creates a instance of the module with the generating convention. And,
     * returns it.
     * @param XoopsModule $module
     * @param bool $loadConfig
     * @return Legacy_AbstractModule \Legacy_Module
     */
    public static function &createModule(XoopsModule $module, bool $loadConfig=true)
    {
        $instance = null;

        //
        // TODO need cache here?
        //
        XCube_DelegateUtils::call('Legacy_Utils.CreateModule', new XCube_Ref($instance), $module, $loadConfig);

        if (is_object($instance) && $instance instanceof \Legacy_AbstractModule) {
            return $instance;
        }

        $dirname = $module->get('dirname');

        //
        // IMPORTANT CONVENTION
        //
        $className = ucfirst($dirname) . '_Module';
        if (!XC_CLASS_EXISTS($className)) {
            $filePath = XOOPS_ROOT_PATH . "/modules/${dirname}/class/Module.class.php";
            if (file_exists($filePath)) {
                require_once $filePath;
            }
        }

        if (XC_CLASS_EXISTS($className)) {
            $instance = new $className($module, $loadConfig);
        } else {
            $instance = new Legacy_ModuleAdapter($module, $loadConfig);
        }

        return $instance;
    }

    /***
     * Creates a instance of the block procedure with the generating convention.
     * And, returns it.
     * @static
     * @param $block
     * @return Legacy_BlockProcedure
     */
    public static function &createBlockProcedure(&$block)
    {
        //
        // IMPORTANT CONVENTION
        //
        $retBlock = null;

        //
        // TODO need cache here?
        //
        XCube_DelegateUtils::call('Legacy_Utils.CreateBlockProcedure', new XCube_Ref($retBlock), $block);

        if (is_object($retBlock) && $retBlock instanceof \Legacy_AbstractBlockProcedure) {
            return $retBlock;
        }

        $func = $block->get('show_func');
        if ('cl::' == substr($func, 0, 4)) {
            $className = ucfirst($block->get('dirname')) . '_' . substr($func, 4);
            if (!XC_CLASS_EXISTS($className)) {
                $filePath = XOOPS_ROOT_PATH . '/modules/' . $block->get('dirname') . '/blocks/' . $block->get('func_file');
                if (!file_exists($filePath)) {
                    $retBlock = new Legacy_BlockProcedureAdapter($block);
                    return $retBlock;
                }

                require_once $filePath;

                if (!XC_CLASS_EXISTS($className)) {
                    $retBlock = new Legacy_BlockProcedureAdapter($block);
                    return $retBlock;
                }
            }

            $retBlock = new $className($block);
        } else {
            $retBlock = new Legacy_BlockProcedureAdapter($block);
        }

        return $retBlock;
    }

    /***
     * Calls user controll event.
     */
    public static function raiseUserControlEvent()
    {
        $root =& XCube_Root::getSingleton();
        foreach (array_keys($_REQUEST) as $key) {
            if (0 === strpos($key, 'Legacy_Event_User_')) {
                $eventName = substr($key, 18);
                XCube_DelegateUtils::call('Legacy.Event.User.' . $eventName);
                $root->mContext->mAttributes['userEvent'][$eventName] = true;
            }
        }
    }

    /***
     * Converts the version of the module from $modversion value to integer
     * number.
     * @param string $version
     * @return int
     */
    public static function convertVersionFromModinfoToInt($version)
    {
        return round(100 * (float)$version);
    }

    /***
     * Converts the version of the module from DB value to float.
     * @param int $version
     * @return float
     */
    public static function convertVersionIntToFloat($version)
    {
        return round((float)( (int) $version / 100), 2);
    }

    /**
     * getUid
     *
     * @param	void
     *
     * @return	int
    **/
    public static function getUid()
    {
        $root = XCube_Root::getSingleton();
        return ($root->mContext->mUser->isInRole('Site.RegisteredUser')) ? $root->mContext->mXoopsUser->get('uid') : 0;
    }

    /**
     * getUserName
     *
     * @param	void
     *
     * @return	int
    **/
    public static function getUserName(/*** int ***/ $uid)
    {
        $name = null;
        XCube_DelegateUtils::call('Legacy_User.GetUserName', new XCube_Ref($name), $uid);
        if (! $name) {
            $handler =& xoops_gethandler('member');
            $user =& $handler->getUser((int)$uid);
            if ($user) {
                $name = $user->getShow('uname');
            }
        }
        return $name;
    }

    /**
     * getDirnameListByTrustDirname
     *
     * @param	string	$trustDirname
     *
     * @return	string[]
    **/
    public static function getDirnameListByTrustDirname(/*** string ***/ $trustDirname)
    {
        $list = [];
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('isactive', 0, '>'));
        $cri->add(new Criteria('trust_dirname', $trustDirname));
        $cri->addSort('weight', 'ASC');
        $cri->addSort('mid', 'ASC');
        foreach (xoops_gethandler('module')->getObjects($cri) as $module) {
            $list[] = $module->get('dirname');
        }
        return $list;
    }

    /**
     * getTrustDirnameByDirname
     *
     * @param	string	$dirname
     *
     * @return	string
    **/
    public static function getTrustDirnameByDirname(/*** string ***/ $dirname)
    {
        $handler =& xoops_gethandler('module');
        $module =& $handler->getByDirname($dirname);
        if ($module) {
            return $module->get('trust_dirname') ?: null;
        }
    }

    /**
     * formatPagetitle
     *
     * @param	string	$modulename
     * @param	string	$pagetitle ex. "Hello!", "How to install XCL?"
     * @param	string	$action ex.edit, delete, list
     *
     * @return	string
    **/
    public static function formatPagetitle(/*** string ***/ $modulename, /*** string ***/ $pagetitle, /*** string ***/ $action)
    {
        $handler = xoops_gethandler('config');
        $configArr = $handler->getConfigsByDirname('legacyRender');

        $replace = [$modulename, $pagetitle, $action];
        $search = ['{modulename}', '{pagetitle}', '{action}'];
        $ret = str_replace($search, $replace, $configArr['pagetitle']);

        $ret = (! $modulename) ? preg_replace("/\[modulename\](.*)\[\/modulename\]/U", '', $ret) : preg_replace("/\[modulename\](.*)\[\/modulename\]/U", '$1', $ret);
        $ret = (! $pagetitle) ? preg_replace("/\[pagetitle\](.*)\[\/pagetitle\]/U", '', $ret) : preg_replace("/\[pagetitle\](.*)\[\/pagetitle\]/U", '$1', $ret);
        $ret = (! $action) ? preg_replace("/\[action\](.*)\[\/action\]/U", '', $ret) : preg_replace("/\[action\](.*)\[\/action\]/U", '$1', $ret);

        return $ret;
    }

    /**
     * getModuleHandler
     *
     * @param	string	$name
     * @param	string	$dirname
     *
     * @return	XoopsObjectGenericHandler
    **/
    public static function getModuleHandler(/*** string ***/ $name, /*** string ***/ $dirname)
    {
        $trustDirname = self::getTrustDirnameByDirname($dirname);
        if (isset($trustDirname)) {
            $path = XOOPS_TRUST_PATH. '/modules/'. $trustDirname .'/class/handler/' . ucfirst($name) . '.class.php';
            $className = ucfirst($trustDirname) . '_' . ucfirst($name) . 'Handler';
            self::_loadClassFile($path, $className);

            $root =& XCube_Root::getSingleton();
            $instance = new $className($root->mController->getDB(), $dirname);
            return $instance;
        } else {
            return xoops_getmodulehandler($name, $dirname);
        }
    }

    /**
     * renderUri
     *
     * @param	string	$dirname
     * @param	string	$dataname
     * @param	int		$data_id
     * @param	string	$action
     * @param	string	$query
     *
     * @return	XoopsObjectGenericHandler
    **/
    public static function renderUri(/*** string ***/ $dirname, /*** string ***/ $dataname=null, /*** int ***/ $data_id=0, /*** string ***/ $action=null, /*** string ***/ $query=null)
    {
        $uri = null;
        if (true == XCube_Root::getSingleton()->mContext->getXoopsConfig('cool_uri')) {
            if (isset($dataname)) {
                if ($data_id>0) {
                    if (isset($action)) {
                        $uri = sprintf('/%s/%s/%d/%s', $dirname, $dataname, $data_id, $action);
                    } else {
                        $uri = sprintf('/%s/%s/%d', $dirname, $dataname, $data_id);
                    }
                } else {
                    if (isset($action)) {
                        $uri = sprintf('/%s/%s/%s', $dirname, $dataname, $action);
                    } else {
                        $uri = sprintf('/%s/%s', $dirname, $dataname);
                    }
                }
            } else {
                if ($data_id>0) {
                    if (isset($action)) {
                        die();
                    } else {
                        $uri = sprintf('/%s/%d', $dirname, $data_id);
                    }
                } else {
                    if (isset($action)) {
                        die();
                    } else {
                        $uri = '/'.$dirname;
                    }
                }
            }
            $uri = (isset($query)) ? XOOPS_URL.$uri.'?'.$query : XOOPS_URL. $uri;
        } else {
            XCube_DelegateUtils::call('Module.'.$dirname.'.Global.Event.GetNormalUri', new XCube_Ref($uri), $dirname, $dataname, $data_id, $action, $query);

            $uri = XOOPS_MODULE_URL. $uri;
        }

        return $uri;
    }

    /**
     * getCommonModuleList
     *
     * @param	string		$role	ex) cat, group, workflow, image
     *
     * @return	string[]	dirnames
    **/
    public static function getCommonModuleList(/*** string ***/ $role)
    {
        $list = [];
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('isactive', 0, '>'));
        $cri->add(new Criteria('role', $role));
        $cri->addSort('weight', 'ASC');
        $cri->addSort('mid', 'ASC');
        foreach (xoops_gethandler('module')->getObjects($cri) as $module) {
            $list[] = $module->get('dirname');
        }
        return $list;
    }

    /**
     * _loadClassFile
     *
     * @param	string	$path
     * @param	string	$class
     *
     * @return	bool
    **/
    private static function _loadClassFile(/*** string ***/ $path, /*** string ***/ $class)
    {
        if (!file_exists($path)) {
            return false;
        }
        require_once $path;

        return class_exists($class);
    }
}
