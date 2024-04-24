<?php
/**
 * XOOPS configuration handling class.
 * @package    kernel
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 * @brief      This class acts as an interface for handling general configurations of XOOPS and its modules.
 * @todo       Tests that need to be made:
 *             - error handling
 * @access  public
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH.'/kernel/configoption.php';
require_once XOOPS_ROOT_PATH.'/kernel/configitem.php';


class XoopsConfigHandler
{

    /**
     * holds reference to config item handler(DAO) class
     *
     * @var     object
     * @access	private
     */
    public $_cHandler;

    /**
     * holds reference to config option handler(DAO) class
     *
     * @var	    object
     * @access	private
     */
    public $_oHandler;

    /**
     * holds an array of cached references to config value arrays,
     *  indexed on module id and category id
     *
     * @var     array
     * @access  private
     */
    public $_cachedConfigs = [];

    /**
     * Constructor
     *
     * @param	object  &$db    reference to database object
     */
    public function __construct(&$db)
    {
        $this->_cHandler =new XoopsConfigItemHandler($db);
        $this->_oHandler =new XoopsConfigOptionHandler($db);
    }

    /**
     * Create a config
     *
     * @see     XoopsConfigItem
     * @return	object  reference to the new {@link XoopsConfigItem}
     */
    public function &createConfig()
    {
        $ret =& $this->_cHandler->create();
        return $ret;
    }

    /**
     * Get a config
     *
     * @param	int     $id             ID of the config
     * @param	bool    $withoptions    load the config's options now?
     * @return	object  reference to the {@link XoopsConfig}
     */
    public function &getConfig($id, $withoptions = false)
    {
        $config =& $this->_cHandler->get($id);
        if (true == $withoptions) {
            $config->setConfOptions($this->getConfigOptions(new Criteria('conf_id', $id)));
        }
        return $config;
    }

    /**
     * insert a new config in the database
     *
     * @param object  &$config reference to the {@link XoopsConfigItem}
     * @return bool
     */
    public function insertConfig(&$config)
    {
        if (!$this->_cHandler->insert($config)) {
            return false;
        }
        $options =& $config->getConfOptions();
        $count = is_countable($options) ? count($options) : 0;
        $conf_id = $config->getVar('conf_id');
        for ($i = 0; $i < $count; $i++) {
            $options[$i]->setVar('conf_id', $conf_id);
            if (!$this->_oHandler->insert($options[$i])) {
                echo $options[$i]->getErrors();
            }
        }
        if (!empty($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')])) {
            unset($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')]);
        }
        return true;
    }

    /**
     * Delete a config from the database
     *
     * @param object  &$config reference to a {@link XoopsConfigItem}
     * @return bool
     */
    public function deleteConfig(&$config)
    {
        if (!$this->_cHandler->delete($config)) {
            return false;
        }
        $options =& $config->getConfOptions();
        $count = is_countable($options) ? count($options) : 0;
        if (0 == $count) {
            $options =& $this->getConfigOptions(new Criteria('conf_id', $config->getVar('conf_id')));
            $count = count($options);
        }
        if (is_array($options) && $count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $this->_oHandler->delete($options[$i]);
            }
        }
        if (!empty($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')])) {
            unset($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')]);
        }
        return true;
    }

    /**
     * get one or more Configs
     *
     * @param	object  $criteria       {@link CriteriaElement}
     * @param	bool    $id_as_key      Use the configs' ID as keys?
     * @param	bool    $with_options   get the options now?
     *
     * @return	array   Array of {@link XoopsConfigItem} objects
     */
    public function &getConfigs($criteria = null, $id_as_key = false, $with_options = false)
    {
        $config =& $this->_cHandler->getObjects($criteria, $id_as_key);
        return $config;
    }

    /**
     * Count some configs
     *
     * @param object $criteria {@link CriteriaElement}
     * @return int
     */
    public function getConfigCount($criteria = null)
    {
        return $this->_cHandler->getCount($criteria);
    }

    /**
     * Get configs from a certain category
     *
     * @param	int $category   ID of a category
     * @param	int $module     ID of a module
     *
     * @return	array   array of {@link XoopsConfig}s
     * @todo This method keeps cache for categories. This may be problem...
     */
    public function &getConfigsByCat($category, $module = 0)
    {
        static $_cachedConfigs= [];
        if (!empty($_cachedConfigs[$module][$category])) {
            return $_cachedConfigs[$module][$category];
        } else {
            $ret = [];
            $criteria = new CriteriaCompo(new Criteria('conf_modid', (int)$module));
            if (!empty($category)) {
                $criteria->add(new Criteria('conf_catid', (int)$category));
            }

            // get config values
            $configs = [];
            $db = $this->_cHandler->db;
            $result = $db->query('SELECT conf_name,conf_value,conf_valuetype FROM '.$db->prefix('config').' '.$criteria->renderWhere().' ORDER BY conf_order ASC');
            if ($result) {
                while ([$name, $value, $type] = $db->fetchRow($result)) {
                    switch ($type) {
                        case 'array':
                            $ret[$name] = unserialize($value);
                            break;
                        case 'encrypt':
                            $ret[$name] = XCube_Utils::decrypt($value);
                            break;
                        default:
                            $ret[$name] = $value;
                    }
                }
                $_cachedConfigs[$module][$category] =& $ret;
            }
            return $ret;
        }
    }

    /**
     * Get configs by dirname.
     *
     * @param string $dirname
     * @param int    $category ID of a category. (Reserved)
     * @return array|null
     */
    public function &getConfigsByDirname($dirname, $category = 0)
    {
        $ret = null;
        ;
        $handler = xoops_gethandler('module');
        ;
        $module =& $handler->getByDirname($dirname);
        if (!is_object($module)) {
            return $ret;
        }

        $ret =& $this->getConfigsByCat($category, $module->get('mid'));

        return $ret;
    }

    /**
     * Make a new {@link XoopsConfigOption}
     *
     * @return	object  {@link XoopsConfigOption}
     */
    public function &createConfigOption()
    {
        $ret =& $this->_oHandler->create();
        return $ret;
    }

    /**
     * Get a {@link XoopsConfigOption}
     *
     * @param	int $id ID of the config option
     *
     * @return	object  {@link XoopsConfigOption}
     */
    public function &getConfigOption($id)
    {
        $ret =& $this->_oHandler->get($id);
        return $ret;
    }

    /**
     * Get one or more {@link XoopsConfigOption}s
     *
     * @param	object  $criteria   {@link CriteriaElement}
     * @param	bool    $id_as_key  Use IDs as keys in the array?
     *
     * @return	array   Array of {@link XoopsConfigOption}s
     */
    public function &getConfigOptions($criteria = null, $id_as_key = false)
    {
        $ret =& $this->_oHandler->getObjects($criteria, $id_as_key);
        return $ret;
    }

    /**
     * Count some {@link XoopsConfigOption}s
     *
     * @param	object  $criteria   {@link CriteriaElement}
     *
     * @return	int     Count of {@link XoopsConfigOption}s matching $criteria
     */
    public function getConfigOptionsCount($criteria = null)
    {
        return $this->_oHandler->getCount($criteria);
    }

    /**
     * Get a list of configs
     *
     * @param	int $conf_modid ID of the modules
     * @param	int $conf_catid ID of the category
     *
     * @return	array   Associative array of name=>value pairs.
     */
    public function &getConfigList($conf_modid, $conf_catid = 0)
    {
        if (!empty($this->_cachedConfigs[$conf_modid][$conf_catid])) {
            return $this->_cachedConfigs[$conf_modid][$conf_catid];
        } else {
            $criteria = new CriteriaCompo(new Criteria('conf_modid', $conf_modid));
            if (empty($conf_catid)) {
                $criteria->add(new Criteria('conf_catid', $conf_catid));
            }
            $configs =& $this->_cHandler->getObjects($criteria);
            $confcount = is_countable($configs) ? count($configs) : 0;
            $ret = [];
            for ($i = 0; $i < $confcount; $i++) {
                $ret[$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
            }
            $this->_cachedConfigs[$conf_modid][$conf_catid] =& $ret;
            return $ret;
        }
    }
}
