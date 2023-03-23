<?php
/**
 * module class object
 * @package    kernel
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2008/03/08
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * @package 	kernel
 * @author		Kazumi Ono	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsModule extends XoopsObject
{
    /**
     * @var string
     */
    public $modinfo;
    /**
     * @var string
     */
    public $adminmenu;

    public function __construct()
    {
        parent::__construct();
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        $this->initVar('mid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 150);
        $this->initVar('version', XOBJ_DTYPE_INT, 100, false);
        $this->initVar('last_update', XOBJ_DTYPE_INT, null, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('isactive', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dirname', XOBJ_DTYPE_OTHER, null, true);
        $this->initVar('trust_dirname', XOBJ_DTYPE_OTHER, null, true);
        $this->initVar('role', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('hasmain', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasadmin', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hassearch', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasconfig', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hascomments', XOBJ_DTYPE_INT, 0, false);
        // RMV-NOTIFY
        $this->initVar('hasnotification', XOBJ_DTYPE_INT, 0, false);
        $initVars = $this->vars;
    }

    /**
     * Load module info
     *
     * @param string $dirname	Directory Name
     * @param bool $verbose
     **/
    public function loadInfoAsVar(string $dirname, bool $verbose = true)
    {
        if (!isset($this->modinfo)) {
            $this->loadInfo($dirname, $verbose);
        }
        $this->setVar('name', $this->modinfo['name'], true);
        $this->setVar('version', Legacy_Utils::convertVersionFromModinfoToInt($this->modinfo['version'])); // TODO semver
        $this->setVar('dirname', $this->modinfo['dirname'], true);
        // $trustDirname = isset($this->modinfo['trust_dirname']) ? $this->modinfo['trust_dirname'] : null;
        $trustDirname = $this->modinfo['trust_dirname'] ?? null;
        $this->setVar('trust_dirname', $trustDirname, true);
        //$role = isset($this->modinfo['role']) ? $this->modinfo['role'] : null;
        $role = $this->modinfo['role'] ?? null;
        $this->setVar('role', $role, true);
        $hasmain = (isset($this->modinfo['hasMain']) && 1 == $this->modinfo['hasMain']) ? 1 : 0;
        $hasadmin = (isset($this->modinfo['hasAdmin']) && 1 == $this->modinfo['hasAdmin']) ? 1 : 0;
        $hassearch = (isset($this->modinfo['hasSearch']) && 1 == $this->modinfo['hasSearch']) ? 1 : 0;
        $hasconfig = ((isset($this->modinfo['config']) && is_array($this->modinfo['config'])) || !empty($this->modinfo['hasComments'])) ? 1 : 0;
        $hascomments = (isset($this->modinfo['hasComments']) && 1 == $this->modinfo['hasComments']) ? 1 : 0;
        // RMV-NOTIFY
        $hasnotification = (isset($this->modinfo['hasNotification']) && 1 == $this->modinfo['hasNotification']) ? 1 : 0;
        $this->setVar('hasmain', $hasmain);
        $this->setVar('hasadmin', $hasadmin);
        $this->setVar('hassearch', $hassearch);
        $this->setVar('hasconfig', $hasconfig);
        $this->setVar('hascomments', $hascomments);
        // RMV-NOTIFY
        $this->setVar('hasnotification', $hasnotification);
    }

    /**
     * Get module info
     *
     * @param	string	$name
     * @return	array|string	Array of module information.
     *			If {@link $name} is set, returns a singel module information item as string.
     **/
    public function &getInfo($name=null)
    {
        if (!isset($this->modinfo)) {
            $this->loadInfo($this->getVar('dirname'));
        }
        if (isset($name)) {
            if (isset($this->modinfo[$name])) {
                return $this->modinfo[$name];
            }
            $ret = false;
            return $ret;
        }
        return $this->modinfo;
    }

    /**
     * Get a link to the modules main page
     *
     * @return	string	FALSE on fail
     */
    public function mainLink()
    {
        if (1 == $this->getVar('hasmain')) {
            $ret = '<a href="'.XOOPS_URL.'/modules/'.$this->getVar('dirname').'/">'.$this->getVar('name').'</a>';
            return $ret;
        }
        return false;
    }

    /**
     * Get links to the subpages
     *
     * @return array
     */
    public function &subLink()
    {
        $ret = [];
        if ($this->getInfo('sub') && is_array($this->getInfo('sub'))) {
            foreach ($this->getInfo('sub') as $submenu) {
                $ret[] = ['name' => $submenu['name'], 'url' => $submenu['url']];
            }
        }
        return $ret;
    }

    /**
     * Load the admin menu for the module
     */
    public function loadAdminMenu()
    {
        $menu = $this->getInfo('adminmenu');
        if (is_array($menu)) {
            // Array() Cube style
            $this->adminmenu = $menu;
        } else {
            if ($menu && file_exists($path = XOOPS_ROOT_PATH.'/modules/'.$this->getVar('dirname').'/'.$menu)) {
                include $path;
                $this->adminmenu = (isset($adminmenu) && is_array($adminmenu))? $adminmenu : [];
            }
        }
    }

    /**
     * Get the admin menu for the module
     *
     * @return	string
     */
    public function &getAdminMenu()
    {
        if (!isset($this->adminmenu)) {
            $this->loadAdminMenu();
        }
        return $this->adminmenu;
    }

    /**
     * Load the module info for this module
     *
     * @param	string	$dirname	Module directory
     * @param	bool	$verbose	Give an error on fail?
     */
    public function loadInfo($dirname, $verbose = true)
    {
        global $xoopsConfig;

        //
        // Guard multiplex loading.
        //
        if (!empty($this->modinfo)) {
            return;
        }

        $root =& XCube_Root::getSingleton();
        $root->mLanguageManager->loadModinfoMessageCatalog($dirname);

        if (file_exists(XOOPS_ROOT_PATH.'/modules/'.$dirname.'/xoops_version.php')) {
            include XOOPS_ROOT_PATH.'/modules/'.$dirname.'/xoops_version.php';
        } else {
            if (false != $verbose) {
                echo "Module File for $dirname Not Found!";
            }
            return;
        }

        $this->modinfo =& $modversion;

        if (isset($this->modinfo['version'])) {
            $this->modinfo['version'] = (float)$this->modinfo['version'];
        } else {
            $this->modinfo['version'] = 0;
        }

        // set ['sqlfile']['mysqli']
        if (isset($this->modinfo['sqlfile']) && isset($this->modinfo['sqlfile']['mysql']) && ! isset($this->modinfo['sqlfile']['mysqli'])) {
            $this->modinfo['sqlfile']['mysqli'] = $this->modinfo['sqlfile']['mysql'];
        }
    }

    /**
     * Search contents within a module
     *
     * @param	string $term
     * @param	string $andor	'AND' or 'OR'
     * @param int       $limit
     * @param int       $offset
     * @param int       $userid
     * @return	mixed	Search result.
     **/
    public function &search($term = '', $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        $ret = false;
        if (1 != $this->getVar('hassearch')) {
            return $ret;
        }
        $search =& $this->getInfo('search');
        if (1 != $this->getVar('hassearch') || !isset($search['file']) || !isset($search['func']) || '' == $search['func'] || '' == $search['file']) {
            return $ret;
        }
        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/' . $search['file'])) {
            include_once XOOPS_ROOT_PATH.'/modules/'.$this->getVar('dirname').'/'.$search['file'];
        } else {
            return $ret;
        }
        if (function_exists($search['func'])) {
            $func = $search['func'];
            $ret = $func($term, $andor, $limit, $offset, $userid);
        }
        return $ret;
    }

    /**
     * @return string
     */
    public function getRenderedVersion()
    {
        return sprintf('%01.2f', $this->getVar('version') / 100);
    }

    /**
     * @return bool
     */
    public function hasHelp()
    {
        $info =& $this->getInfo();
        if (isset($info['cube_style']) && false != $info['cube_style'] && isset($info['help']) && strlen($info['help']) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        if ($this->hasHelp()) {
            return $this->modinfo['help'];
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasNeedUpdate()
    {
        $info =& $this->getInfo();
        return ($this->getVar('version') < Legacy_Utils::convertVersionFromModinfoToInt($info['version'])); // TODO semver
    }

    /**#@+
     * For backward compatibility only!
     * @deprecated
     */
    public function mid()
    {
        return $this->getVar('mid');
    }
    public function dirname()
    {
        return $this->getVar('dirname');
    }
    public function name()
    {
        return $this->getVar('name');
    }
    public function &getByDirName($dirname)
    {
        $modhandler = xoops_gethandler('module');
        $ret =& $modhandler->getByDirname($dirname);
        return $ret;
    }
    /**#@-*/
}


/**
 * XOOPS module handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS module class objects.
 *
 * @package 	kernel
 *
 * @author		Kazumi Ono	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsModuleHandler extends XoopsObjectHandler
{
    public $_tmp;

    /**
     * holds an array of cached module references, indexed by module id/dirname
     *
     * @var    array
     * @access private
     */
    public $_cachedModule_mid = [];

    /**
     * holds an array of cached module references, indexed by module dirname
     *
     * @var    array
     * @access private
     */
    public $_cachedModule_dirname = [];

    /**
     * Create a new {@link XoopsModule} object
     *
     * @param bool $isNew Flag the new object as "new"
     * @return	object
     **/
    public function &create($isNew = true)
    {
        $module =new XoopsModule();
        if ($isNew) {
            $module->setNew();
        }
        return $module;
    }

    /**
     * Load a module from the database
     *
     * @param	int 	$id 	ID of the module
     *
     * @return	object	FALSE on fail
     */
    public function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            if (!empty($this->_cachedModule_mid[$id])) {
                return $this->_cachedModule_mid[$id];
            } else {
                $sql = 'SELECT * FROM '.$this->db->prefix('modules').' WHERE mid = '.$id;
                if ($result = $this->db->query($sql)) {
                    $numrows = $this->db->getRowsNum($result);
                    if (1 == $numrows) {
                        $module =new XoopsModule();
                        $myrow = $this->db->fetchArray($result);
                        $module->assignVars($myrow);
                        $this->_cachedModule_mid[$id] =& $module;
                        $this->_cachedModule_dirname[$module->getVar('dirname')] =& $module;
                        $ret =& $module;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Load a module by its dirname
     *
     * @param	string	$dirname
     *
     * @return	object	FALSE on fail
     */
    public function &getByDirname($dirname)
    {
        $ret = false;
        $dirname =    trim($dirname);
        $cache = &$this->_cachedModule_dirname;
        if (!empty($cache[$dirname])) {
            $ret = $cache[$dirname];
        } elseif (0 == count($cache)) {
            $db = $this->db;
            $sql = 'SELECT * FROM ' . $db->prefix('modules');
            if ($result = $db->query($sql)) {
                while ($myrow = $db->fetchArray($result)) {
                    $module = new XoopsModule();
                    $module->assignVars($myrow);
                    $cache[$myrow['dirname']] =& $module;
                    $this->_cachedModule_mid[$myrow['mid']] =& $module;
                    unset($module);
                }
            }
            if (!empty($cache[$dirname])) {
                $ret = $cache[$dirname];
            }
        }
        return $ret;
    }

    /**
     * Write a module to the database
     *
     * @remark This method unsets cache of the module, and re-contruct the cache.
     *		   But this mechanism may break the reference to the previous cache....
     *		   Maybe that's no problem. But, we should notice it.
     * @param	object	&$module reference to a {@link XoopsModule}
     * @return	bool
     **/
    public function insert(&$module)
    {
        if ('xoopsmodule' != strtolower(get_class($module))) {
            return false;
        }

        if (!$module->isDirty()) {
            return true;
        }
        if (!$module->cleanVars()) {
            return false;
        }
        foreach ($module->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($module->isNew()) {
            if (empty($mid)) { //Memo: if system module, mid might be set to 1
                $mid = $this->db->genId('modules_mid_seq');
            }
            $sql = sprintf('INSERT INTO %s (mid, name, version, last_update, weight, isactive, dirname, trust_dirname, role, hasmain, hasadmin, hassearch, hasconfig, hascomments, hasnotification) VALUES (%u, %s, %u, %u, %u, %u, %s, %s, %s, %u, %u, %u, %u, %u, %u)', $this->db->prefix('modules'), $mid, $this->db->quoteString($name), $version, time(), $weight, 1, $this->db->quoteString($dirname), $this->db->quoteString($trust_dirname), $this->db->quoteString($role), $hasmain, $hasadmin, $hassearch, $hasconfig, $hascomments, $hasnotification);
        } else {
            $sql = sprintf('UPDATE %s SET name = %s, dirname = %s, trust_dirname = %s, role = %s, version = %u, last_update = %u, weight = %u, isactive = %u, hasmain = %u, hasadmin = %u, hassearch = %u, hasconfig = %u, hascomments = %u, hasnotification = %u WHERE mid = %u', $this->db->prefix('modules'), $this->db->quoteString($name), $this->db->quoteString($dirname), $this->db->quoteString($trust_dirname), $this->db->quoteString($role), $version, time(), $weight, $isactive, $hasmain, $hasadmin, $hassearch, $hasconfig, $hascomments, $hasnotification, $mid);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $module->unsetNew();
        if (empty($mid)) {
            $mid = $this->db->getInsertId();
        }
        $module->assignVar('mid', $mid);
        if (!empty($this->_cachedModule_dirname[$dirname])) {
            unset($this->_cachedModule_dirname[$dirname]);
        }
        if (!empty($this->_cachedModule_mid[$mid])) {
            unset($this->_cachedModule_mid[$mid]);
        }

        $this->_cachedModule_dirname[$dirname] = $module;
        $this->_cachedModule_mid[$mid] =& $this->_cachedModule_dirname[$dirname];

        if (XC_CLASS_EXISTS('Legacy_AdminSideMenu')) {
            Legacy_AdminSideMenu::clearCache();
        }

        return true;
    }

    /**
     * Delete a module from the database
     *
     * @param	object	&$module
     * @return	bool
     **/
    public function delete(&$module)
    {
        if ('xoopsmodule' != strtolower(get_class($module))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE mid = %u', $this->db->prefix('modules'), $module->getVar('mid'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        // delete admin permissions assigned for this module
        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_admin' AND gperm_itemid = %u", $this->db->prefix('group_permission'), $module->getVar('mid'));
        $this->db->query($sql);
        // delete read permissions assigned for this module
        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_read' AND gperm_itemid = %u", $this->db->prefix('group_permission'), $module->getVar('mid'));
        $this->db->query($sql);

        if (1 == $module->getVar('mid')) {
            $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'system_admin'", $this->db->prefix('group_permission'));
        } else {
            $sql = sprintf('DELETE FROM %s WHERE gperm_modid = %u', $this->db->prefix('group_permission'), $module->getVar('mid'));
        }
        $this->db->query($sql);

        $sql = sprintf('SELECT block_id FROM %s WHERE module_id = %u', $this->db->prefix('block_module_link'), $module->getVar('mid'));
        if ($result = $this->db->query($sql)) {
            $block_id_arr = [];
            while ($myrow = $this->db->fetchArray($result)) {
                array_push($block_id_arr, $myrow['block_id']);
            }
        }
        // loop through block_id_arr
        if (isset($block_id_arr)) {
            foreach ($block_id_arr as $i) {
                $sql = sprintf('SELECT block_id FROM %s WHERE module_id != %u AND block_id = %u', $this->db->prefix('block_module_link'), $module->getVar('mid'), $i);
                if ($result2 = $this->db->query($sql)) {
                    if (0 < $this->db->getRowsNum($result2)) {
                        // this block has other entries, so delete the entry for this module
                        $sql = sprintf('DELETE FROM %s WHERE (module_id = %u) AND (block_id = %u)', $this->db->prefix('block_module_link'), $module->getVar('mid'), $i);
                        $this->db->query($sql);
                    } else {
                        // this block doesnt have other entries, so disable the block and let it show on top page only. otherwise, this block will not display anymore on block admin page!
                        $sql = sprintf('UPDATE %s SET visible = 0 WHERE bid = %u', $this->db->prefix('newblocks'), $i);
                        $this->db->query($sql);
                        $sql = sprintf('UPDATE %s SET module_id = -1 WHERE module_id = %u', $this->db->prefix('block_module_link'), $module->getVar('mid'));
                        $this->db->query($sql);
                    }
                }
            }
        }

        if (!empty($this->_cachedModule_dirname[$module->getVar('dirname')])) {
            unset($this->_cachedModule_dirname[$module->getVar('dirname')]);
        }
        if (!empty($this->_cachedModule_mid[$module->getVar('mid')])) {
            unset($this->_cachedModule_mid[$module->getVar('mid')]);
        }
        return true;
    }

    /**
     * Load some modules
     *
     * @param	object $criteria  {@link CriteriaElement}
     * @param bool      $id_as_key Use the ID as key into the array
     * @return	array
     **/
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $db = &$this->db;
        $sql = 'SELECT * FROM '.$db->prefix('modules');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();

            if (null != $criteria->getSort()) {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            } else {
                $sql .= ' ORDER BY weight '.$criteria->getOrder().', mid ASC';
            }

            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $db->fetchArray($result)) {
            $mid = $myrow['mid'];
            if (isset($this->_cachedModule_mid[$mid])) {
                $module = $this->_cachedModule_mid[$mid];
            } else {
                $module =new XoopsModule();
                $module->assignVars($myrow);
                $this->_cachedModule_mid[$mid] = $module;
                $this->_cachedModule_dirname[$myrow['dirname']] = $module;
            }
            if (!$id_as_key) {
                $ret[] =& $module;
            } else {
                $ret[$mid] =& $module;
            }
            unset($module);
        }
        return $ret;
    }

    /**
     * Count some modules
     *
     * @param	object	$criteria	{@link CriteriaElement}
     * @return	int
     **/
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('modules');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result =& $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }

    /**
     * returns an array of module names
     *
     * @param	bool $criteria
     * @param bool    $dirname_as_key
     *		if true, array keys will be module directory names
     *		if false, array keys will be module id
     * @return	array
     **/
    public function &getList($criteria = null, $dirname_as_key = false)
    {
        $ret = [];
        $modules =& $this->getObjects($criteria, true);
        foreach ($modules as $i=>$module) {
            if (!$dirname_as_key) {
                $ret[$i] =& $module->getVar('name');
            } else {
                $ret[$module->getVar('dirname')] =& $module->getVar('name');
            }
        }
        return $ret;
    }
}
