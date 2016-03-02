<?php
// $Id: configitem.php,v 1.1 2007/05/15 02:34:37 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://xoopscube.jp/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**#@+
 * Config type
 */
define('XOOPS_CONF', 1);
define('XOOPS_CONF_USER', 2);
define('XOOPS_CONF_METAFOOTER', 3);
define('XOOPS_CONF_CENSOR', 4);
define('XOOPS_CONF_SEARCH', 5);
define('XOOPS_CONF_MAILER', 6);
/**#@-*/

/**
 * 
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsConfigItem extends XoopsObject
{

    /**
     * Config options
     * 
     * @var	array
     * @access	private
     */
    public $_confOptions = array();

    /**
     * Constructor
     */
    public function XoopsConfigItem()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        $this->initVar('conf_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_modid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_catid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('conf_name', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_title', XOBJ_DTYPE_TXTBOX);
        $this->initVar('conf_value', XOBJ_DTYPE_TXTAREA);
        $this->initVar('conf_desc', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_formtype', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_valuetype', XOBJ_DTYPE_OTHER);
        $this->initVar('conf_order', XOBJ_DTYPE_INT);
        $initVars = $this->vars;
    }
    
    /**
     * Get a constract of title
     */
    public function getTitle()
    {
        return defined($this->get('conf_title')) ? constant($this->get('conf_title')) : $this->get('conf_title');
    }
    
    /**
     * Get a constract of description. If it isn't defined, return null.
     */
    public function getDesc()
    {
        return defined($this->get('conf_desc')) ? constant($this->get('conf_desc')) : null;
    }
    
    /**
     * @return array()
     */
    public function &getOptionItems()
    {
        $handler = xoops_gethandler('config');
        $optionArr =& $handler->getConfigOptions(new Criteria('conf_id', $this->get('conf_id')));
        
        return $optionArr;
    }

    /**
     * @return array()
     */
    public function getRoledModuleList()
    {
        $handler = xoops_gethandler('config');
        $optionArr =& $handler->getConfigOptions(new Criteria('conf_id', $this->get('conf_id')));
        $list = array();
        foreach ($optionArr as $opt) {
            if ($opt->get('confop_value')=='none') {
                $list[] = '';
            } else {
                $list = array_merge($list, Legacy_Utils::getCommonModuleList($opt->get('confop_value')));
            }
        }
        return $list;
    }

    /**
     * Get a config value in a format ready for output
     * 
     * @return	string
     */
    public function getConfValueForOutput()
    {
        switch ($this->getVar('conf_valuetype')) {
        case 'int':
            return (int)$this->getVar('conf_value', 'N');
        case 'array':
            return unserialize($this->getVar('conf_value', 'N'));
        case 'float':
            return (float)$this->getVar('conf_value', 'N');
        case 'textarea':
            return $this->getVar('conf_value');
        case 'encrypt':
            return  XCube_Utils::decrypt($this->getVar('conf_value', 'N'));
        default:
            return $this->getVar('conf_value', 'N');
        }
    }

    /**
     * Set a config value
     * 
     * @param	mixed   &$value Value
     * @param	bool    $force_slash
     */
    public function setConfValueForInput(&$value, $force_slash = false)
    {
        switch ($this->getVar('conf_valuetype')) {
        case 'array':
            if (!is_array($value)) {
                $value = explode('|', trim($value));
            }
            $this->setVar('conf_value', serialize($value), $force_slash);
            break;
        case 'text':
            $this->setVar('conf_value', trim($value), $force_slash);
            break;
        case 'encrypt':
            $this->setVar('conf_value', XCube_Utils::encrypt(trim($value)), $force_slash);
            break;
        default:
            $this->setVar('conf_value', $value, $force_slash);
            break;
        }
    }

    /**
     * Assign one or more {@link XoopsConfigItemOption}s 
     * 
     * @param	mixed   $option either a {@link XoopsConfigItemOption} object or an array of them
     */
    public function setConfOptions($option)
    {
        if (is_array($option)) {
            $count = count($option);
            for ($i = 0; $i < $count; $i++) {
                $this->setConfOptions($option[$i]);
            }
        } else {
            if (is_object($option)) {
                $this->_confOptions[] =& $option;
            }
        }
    }

    /**
     * Get the {@link XoopsConfigItemOption}s of this Config
     * 
     * @return	array   array of {@link XoopsConfigItemOption} 
     */
    public function &getConfOptions()
    {
        return $this->_confOptions;
    }
    
    /**
     * Compare with contents of $config object. If it's equal, return true.
     * This member function doesn't use 'conf_id' & 'conf_value' & 'conf_order' to compare.
     * 
     * @param XoopsConfigItem $config
     * @return bool
     */
    public function isEqual(&$config)
    {
        $flag = true;
        
        $flag &= ($this->get('conf_modid') == $config->get('conf_modid'));
        $flag &= ($this->get('conf_catid') == $config->get('conf_catid'));
        $flag &= ($this->get('conf_name') == $config->get('conf_name'));
        $flag &= ($this->get('conf_title') == $config->get('conf_title'));
        $flag &= ($this->get('conf_desc') == $config->get('conf_desc'));
        $flag &= ($this->get('conf_formtype') == $config->get('conf_formtype'));
        $flag &= ($this->get('conf_valuetype') == $config->get('conf_valuetype'));
        
        //
        // Compare options
        //
        $thisOptions =& $this->getOptionItems();
        $hisOptions =& $config->getConfOptions();
        
        if (count($thisOptions) == count($hisOptions)) {
            foreach (array_keys($thisOptions) as $t_thiskey) {
                $t_okFlag = false;
                foreach (array_keys($hisOptions) as $t_hiskey) {
                    if ($thisOptions[$t_thiskey]->isEqual($hisOptions[$t_hiskey])) {
                        $t_okFlag = true;
                    }
                }
                
                if (!$t_okFlag) {
                    $flag = false;
                    break;
                }
            }
        } else {
            $flag = false;
        }

        return $flag;
    }

    /**
     * Set values by config info which is array from xoops_version.php.
     * 
     * @var int   $modid      ID of the module
     * @var array $configInfo
     * @var int   $order      conf_order
     */
    public function loadFromConfigInfo($mid, &$configInfo, $order = null)
    {
        $this->set('conf_modid', $mid);
        $this->set('conf_catid', 0);
        $this->set('conf_name', $configInfo['name']);
        $this->set('conf_title', $configInfo['title'], true);
        if (isset($configInfo['description'])) {
            $this->set('conf_desc', $configInfo['description'], true);
        }
        $this->set('conf_formtype', $configInfo['formtype'], true);
        $this->set('conf_valuetype', $configInfo['valuetype'], true);
        $this->setConfValueForInput($configInfo['default'], true);
        if (isset($configInfo['order'])) {
            $this->set('conf_order', $configInfo['order']);
        } else {
            $this->set('conf_order', $order);
        }
        
        if (isset($configInfo['options']) && is_array($configInfo['options'])) {
            $configHandler = xoops_gethandler('config');
            foreach ($configInfo['options'] as $key => $value) {
                $configOption =& $configHandler->createConfigOption();
                $configOption->setVar('confop_name', $key, true);
                $configOption->setVar('confop_value', $value, true);
                $this->setConfOptions($configOption);
                unset($configOption);
            }
        }
    }
}


/**
* XOOPS configuration handler class.  
* 
* This class is responsible for providing data access mechanisms to the data source 
* of XOOPS configuration class objects.
*
* @author       Kazumi Ono <onokazu@xoops.org>
* @copyright    copyright (c) 2000-2003 XOOPS.org
*/
class XoopsConfigItemHandler extends XoopsObjectHandler
{

    /**
     * Create a new {@link XoopsConfigItem}
     * 
     * @see     XoopsConfigItem
     * @param	bool    $isNew  Flag the config as "new"?
     * @return	object  reference to the new config
     */
    public function &create($isNew = true)
    {
        $config =new XoopsConfigItem();
        if ($isNew) {
            $config->setNew();
        }
        return $config;
    }

    /**
     * Load a config from the database
     * 
     * @param	int $id ID of the config
     * @return	object  reference to the config, FALSE on fail
     */
    public function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            $db = &$this->db;
            $sql = 'SELECT * FROM '.$db->prefix('config').' WHERE conf_id='.$id;
            if ($result = $db->query($sql)) {
                $numrows = $db->getRowsNum($result);
                if ($numrows == 1) {
                    $myrow = $db->fetchArray($result);
                    $config =new XoopsConfigItem();
                    $config->assignVars($myrow);
                    $ret =& $config;
                }
            }
        }
        return $ret;
    }

    /**
     * Write a config to the database
     * 
     * @param	object  &$config    {@link XoopsConfigItem} object
     * @return  mixed   FALSE on fail.
     */
    public function insert(&$config)
    {
        if (strtolower(get_class($config)) != 'xoopsconfigitem') {
            return false;
        }
        if (!$config->isDirty()) {
            return true;
        }
        if (!$config->cleanVars()) {
            return false;
        }
        foreach ($config->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $db = &$this->db;
        if ($config->isNew()) {
            $conf_id = $db->genId('config_conf_id_seq');
            $sql = sprintf('INSERT INTO %s (conf_id, conf_modid, conf_catid, conf_name, conf_title, conf_value, conf_desc, conf_formtype, conf_valuetype, conf_order) VALUES (%u, %u, %u, %s, %s, %s, %s, %s, %s, %u)', $db->prefix('config'), $conf_id, $conf_modid, $conf_catid, $db->quoteString($conf_name), $db->quoteString($conf_title), $db->quoteString($conf_value), $db->quoteString($conf_desc), $db->quoteString($conf_formtype), $db->quoteString($conf_valuetype), $conf_order);
        } else {
            $sql = sprintf('UPDATE %s SET conf_modid = %u, conf_catid = %u, conf_name = %s, conf_title = %s, conf_value = %s, conf_desc = %s, conf_formtype = %s, conf_valuetype = %s, conf_order = %u WHERE conf_id = %u', $db->prefix('config'), $conf_modid, $conf_catid, $db->quoteString($conf_name), $db->quoteString($conf_title), $db->quoteString($conf_value), $db->quoteString($conf_desc), $db->quoteString($conf_formtype), $db->quoteString($conf_valuetype), $conf_order, $conf_id);
        }
        if (!$result = $db->query($sql)) {
            return false;
        }
        if (empty($conf_id)) {
            $conf_id = $db->getInsertId();
        }
        $config->assignVar('conf_id', $conf_id);
        return true;
    }

    /**
     * Delete a config from the database
     * 
     * @param	object  &$config    Config to delete
     * @return	bool    Successful?
     */
    public function delete(&$config)
    {
        if (strtolower(get_class($config)) != 'xoopsconfigitem') {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE conf_id = %u', $this->db->prefix('config'), $config->getVar('conf_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Get configs from the database
     * 
     * @param	object  $criteria   {@link CriteriaElement}
     * @param	bool    $id_as_key  return the config's id as key?
     * @return	array   Array of {@link XoopsConfigItem} objects
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $db = $this->db;
        $sql = 'SELECT * FROM '.$db->prefix('config');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            $sql .= ' ORDER BY conf_order ASC';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $db->fetchArray($result)) {
            $config =new XoopsConfigItem();
            $config->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $config;
            } else {
                $ret[$myrow['conf_id']] =& $config;
            }
            unset($config);
        }
        return $ret;
    }

    /**
     * Count configs
     * 
     * @param	object  $criteria   {@link CriteriaElement} 
     * @return	int     Count of configs matching $criteria
     */
    public function getCount($criteria = null)
    {
        $limit = $start = 0;
        $db = &$this->db;
        $sql = 'SELECT * FROM '.$db->prefix('config');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        $result = $db->query($sql);
        if (!$result) {
            return false;
        }
        list($count) = $db->fetchRow($result);
        return $count;
    }
}
