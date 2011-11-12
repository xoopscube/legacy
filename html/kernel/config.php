<?php
// $Id: config.php,v 1.1 2007/05/15 02:34:37 minahito Exp $
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

require_once XOOPS_ROOT_PATH.'/kernel/configoption.php';
require_once XOOPS_ROOT_PATH.'/kernel/configitem.php';

/**
 * @package     kernel
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */


/**
* XOOPS configuration handling class.
* This class acts as an interface for handling general configurations of XOOPS
* and its modules.
*
*
* @author  Kazumi Ono <webmaster@myweb.ne.jp>
* @todo    Tests that need to be made:
*          - error handling
* @access  public
*/

class XoopsConfigHandler
{

    /**
     * holds reference to config item handler(DAO) class
     * 
     * @var     object
     * @access	private
     */
    var $_cHandler;

    /**
     * holds reference to config option handler(DAO) class
     * 
     * @var	    object
     * @access	private
     */
    var $_oHandler;

	/**
	 * holds an array of cached references to config value arrays,
	 *  indexed on module id and category id
	 *
	 * @var     array
	 * @access  private
	 */
	var $_cachedConfigs = array();

    /**
     * Constructor
     * 
     * @param	object  &$db    reference to database object
     */
    function XoopsConfigHandler(&$db)
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
    function &createConfig()
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
    function &getConfig($id, $withoptions = false)
    {
        $config =& $this->_cHandler->get($id);
        if ($withoptions == true) {
            $config->setConfOptions($this->getConfigOptions(new Criteria('conf_id', $id)));
        }
        return $config;
    }

    /**
     * insert a new config in the database
     * 
     * @param	object  &$config    reference to the {@link XoopsConfigItem} 
     */
    function insertConfig(&$config)
    {
        if (!$this->_cHandler->insert($config)) {
            return false;
        }
        $options =& $config->getConfOptions();
        $count = count($options);
		$conf_id = $config->getVar('conf_id');
        for ($i = 0; $i < $count; $i++) {
            $options[$i]->setVar('conf_id', $conf_id);
            if (!$this->_oHandler->insert($options[$i])) {
				echo $options[$i]->getErrors();
			}
        }
		if (!empty($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')])) {
			unset ($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')]);
		}
        return true;
    }

    /**
     * Delete a config from the database
     * 
     * @param	object  &$config    reference to a {@link XoopsConfigItem} 
     */
    function deleteConfig(&$config)
    {
        if (!$this->_cHandler->delete($config)) {
            return false;
        }
        $options =& $config->getConfOptions();
        $count = count($options);
        if ($count == 0) {
            $options =& $this->getConfigOptions(new Criteria('conf_id', $config->getVar('conf_id')));
            $count = count($options);
        }
        if (is_array($options) && $count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $this->_oHandler->delete($options[$i]);
            }
        }
		if (!empty($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')])) {
			unset ($this->_cachedConfigs[$config->getVar('conf_modid')][$config->getVar('conf_catid')]);
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
    function &getConfigs($criteria = null, $id_as_key = false, $with_options = false)
    {
        $config =& $this->_cHandler->getObjects($criteria, $id_as_key);
        return $config;
    }

    /**
     * Count some configs
     * 
     * @param	object  $criteria   {@link CriteriaElement} 
     */
    function getConfigCount($criteria = null)
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
    function &getConfigsByCat($category, $module = 0)
    {
        static $_cachedConfigs;
		if (!empty($_cachedConfigs[$module][$category])) {
			return $_cachedConfigs[$module][$category];
		} else {
        	$ret = array();
        	$criteria = new CriteriaCompo(new Criteria('conf_modid', (int)$module));
        	if (!empty($category)) {
            	$criteria->add(new Criteria('conf_catid', (int)$category));
        	}
        	$configs =& $this->getConfigs($criteria, true);
			if (is_array($configs)) {
            	foreach (array_keys($configs) as $i) {
					$conf = &$configs[$i];
                	$ret[$conf->getVar('conf_name')] = $conf->getConfValueForOutput();
            	}
        	}
			$_cachedConfigs[$module][$category] =& $ret;
        	return $ret;
		}
    }
	
	/**
	 * Get configs by dirname.
	 * 
	 * @param string $dirname
	 * @param int    $category   ID of a category. (Reserved)
	 */
	function &getConfigsByDirname($dirname, $category = 0)
	{
		$ret = null;;
		$handler =& xoops_gethandler('module');;
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
    function &createConfigOption(){
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
    function &getConfigOption($id)
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
    function &getConfigOptions($criteria = null, $id_as_key = false)
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
    function getConfigOptionsCount($criteria = null)
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
    function &getConfigList($conf_modid, $conf_catid = 0)
    {
		if (!empty($this->_cachedConfigs[$conf_modid][$conf_catid])) {
			return $this->_cachedConfigs[$conf_modid][$conf_catid];
		} else {
        	$criteria = new CriteriaCompo(new Criteria('conf_modid', $conf_modid));
        	if (empty($conf_catid)) {
            	$criteria->add(new Criteria('conf_catid', $conf_catid));
        	}
        	$configs =& $this->_cHandler->getObjects($criteria);
        	$confcount = count($configs);
        	$ret = array();
        	for ($i = 0; $i < $confcount; $i++) {
            	$ret[$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
        	}
			$this->_cachedConfigs[$conf_modid][$conf_catid] =& $ret;
        	return $ret;
		}
    }
}
?>
