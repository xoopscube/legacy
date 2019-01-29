<?php
// $Id: template.php,v 1.1 2007/05/15 02:34:21 minahito Exp $
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
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if (!defined('SMARTY_DIR')) {
    exit();
}
/**
 * Base class: Smarty template engine
 */
require_once SMARTY_DIR.'Smarty.class.php';

/**
 * Template engine
 *
 * @package		kernel
 * @subpackage	core
 *
 * @author		Kazumi Ono 	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsTpl extends Smarty
{

    /**
     * Allow update of template files from the themes/ directory?
     * This should be set to false on an active site to increase performance
     */
    public $_canUpdateFromFile = false;

    /**
     * Constructor
     **/
    public function __construct()
    {
        global $xoopsConfig;
        $this->Smarty();
        $this->compile_id = XOOPS_URL;
        if ($xoopsConfig['theme_fromfile'] == 1) {
            $this->_canUpdateFromFile = true;
            $this->compile_check = true;
        } else {
            $this->_canUpdateFromFile = false;
            $this->compile_check = false;
        }
        $this->left_delimiter =  '<{';
        $this->right_delimiter =  '}>';
        $this->template_dir = XOOPS_THEME_PATH;
        $this->cache_dir = XOOPS_CACHE_PATH;
        $this->compile_dir = XOOPS_COMPILE_PATH;
        //loading under root_path for compatibility with XCL2.1
        $this->plugins_dir = array(SMARTY_DIR.'plugins', XOOPS_ROOT_PATH.'/class/smarty/plugins');
//		$this->default_template_handler_func = 'xoops_template_create';
        $this->use_sub_dirs = false;

        $this->assign(array('xoops_url' => XOOPS_URL,
                            'xoops_rootpath' => XOOPS_ROOT_PATH,
                            'xoops_langcode' => _LANGCODE,
                            'xoops_charset' => _CHARSET,
                            'xoops_version' => XOOPS_VERSION,
                            'xoops_upload_url' => XOOPS_UPLOAD_URL
                            ));

        if (empty($this->debug_tpl)) {
            // set path to debug template from SMARTY_DIR
            $this->debug_tpl = XOOPS_ROOT_PATH.'/modules/legacy/templates/xoops_debug.tpl';
            if ($this->security && is_file($this->debug_tpl)) {
                $this->secure_dir[] = realpath($this->debug_tpl);
            }
            $this->debug_tpl = 'file:' . XOOPS_ROOT_PATH.'/modules/legacy/templates/xoops_debug.tpl';
        }

        // Delegate 'XoopsTpl.New'
        //  Delegate may define additional initialization code for XoopTpl Instance;
        //  varArgs : 
        //      'xoopsTpl'     [I/O] : $this
        //
        XCube_DelegateUtils::call('XoopsTpl.New',  new XCube_Ref($this));
    }
    public function XoopsTpl()
    {
        return self::__construct();
    }

    /**
     * Set the directory for templates
     * 
     * @param   string  $dirname    Directory path without a trailing slash
     **/
    public function xoops_setTemplateDir($dirname)
    {
        $this->template_dir = $dirname;
    }

    /**
     * Get the active template directory
     * 
     * @return  string
     **/
    public function xoops_getTemplateDir()
    {
        return $this->template_dir;
    }

    /**
     * Set debugging mode
     * 
     * @param   boolean     $flag
     **/
    public function xoops_setDebugging($flag=false)
    {
        $this->debugging = is_bool($flag) ? $flag : false;
    }

    /**
     * Set caching
     * 
     * @param   integer     $num
     **/
    public function xoops_setCaching($num=0)
    {
        $this->caching = (int)$num;
    }

    /**
     * Set cache lifetime
     * 
     * @param   integer     $num    Cache lifetime
     **/
    public function xoops_setCacheTime($num=0)
    {
        $num = (int)$num;
        if ($num <= 0) {
            $this->caching = 0;
        } else {
            $this->cache_lifetime = $num;
        }
    }

    /**
     * Set directory for compiled template files
     * 
     * @param   string  $dirname    Full directory path without a trailing slash
     **/
    public function xoops_setCompileDir($dirname)
    {
        $this->compile_dir = $dirname;
    }

    /**
     * Set the directory for cached template files
     * 
     * @param   string  $dirname    Full directory path without a trailing slash
     **/
    public function xoops_setCacheDir($dirname)
    {
        $this->cache_dir = $dirname;
    }

    /**
     * Render output from template data
     * 
     * @deprecated
     *
     * @param   string  $data
     * @return  string  Rendered output  
     **/
    public function xoops_fetchFromData(&$data)
    {
        $dummyfile = XOOPS_CACHE_PATH.'/dummy_'.time();
        $fp = fopen($dummyfile, 'w');
        fwrite($fp, $data);
        fclose($fp);
        $fetched = $this->fetch('file:'.$dummyfile);
        unlink($dummyfile);
        $this->clear_compiled_tpl('file:'.$dummyfile);
        return $fetched;
    }

    /**
     * 
     **/
    public function xoops_canUpdateFromFile()
    {
        return $this->_canUpdateFromFile;
    }
    
    public function &fetchBlock($template, $bid)
    {
        $ret = $this->fetch('db:'.$template, $bid);
        return $ret;
    }
    
    public function isBlockCached($template, $bid)
    {
        return $this->is_cached('db:'.$template, 'blk_'.$bid);
    }
    
    public function isModuleCached($templateName, $dirname)
    {
        if (!$templateName) {
            $templateName='system_dummy.html';
        }

        return $this->is_cached('db:'.$templateName, $this->getModuleCachedTemplateId($dirname));
    }

    public function fetchModule($templateName, $dirname)
    {
        if (!$templateName) {
            $templateName='system_dummy.html';
        }

        return $this->fetch('db:'.$templateName, $this->getModuleCachedTemplateId($dirname));
    }
    
    public function getModuleCachedTemplateId($dirname)
    {
        return 'mod_'.$dirname.'|'.md5(str_replace(XOOPS_URL, '', $GLOBALS['xoopsRequestUri']));
    }

    /**
     * Return smarty's debug console if debug mode is active.
     *
     * @return string
     */
    public function fetchDebugConsole()
    {
        if ($this->debugging) {
            // capture time for debugging info
            $_params = array();
            require_once(SMARTY_CORE_DIR . 'core.get_microtime.php');
            $this->_smarty_debug_info[$_included_tpls_idx]['exec_time'] = (smarty_core_get_microtime($_params, $this) - $_debug_start_time);
            require_once(SMARTY_CORE_DIR . 'core.display_debug_console.php');
            return smarty_core_display_debug_console($_params, $this);
        }
    }
}


/**
 * function to update compiled template file in templates_c folder
 * 
 * @param   string  $tpl_id
 * @param   boolean $clear_old
 * @return  boolean
 **/
function xoops_template_touch($tpl_id, $clear_old = true)
{
    $result = null;
    
    // RaiseEvent 'Legacy.XoopsTpl.TemplateTouch' 
    //  Delegate may define new template touch logic (with XC21, only for clear cache & compiled template)
    //  varArgs : 
    //      'xoopsTpl'     [I/O] : $this
    //
    XCube_DelegateUtils::call('Legacy.XoopsTpl.TemplateTouch', $tpl_id, $clear_old, new XCube_Ref($result));
    
    if ($result === null) {
        $tpl = new XoopsTpl();
        $tpl->force_compile = true;
        $tplfile_handler =& xoops_gethandler('tplfile');
        $tplfile =& $tplfile_handler->get($tpl_id);
        if (is_object($tplfile)) {
            $file = $tplfile->getVar('tpl_file');
            if ($clear_old) {
                $tpl->clear_cache('db:'.$file);
                $tpl->clear_compiled_tpl('db:'.$file);
            }
            // $tpl->fetch('db:'.$file);
            return true;
        }
        return false;
    } else {
        return $result;
    }
}

/**
 * Smarty default template handler function
 * 
 * @deprecated
 *
 * @param $resource_type
 * @param $resource_name
 * @param $template_source
 * @param $template_timestamp
 * @param $smarty_obj
 * @return  bool
 **/
function xoops_template_create($resource_type, $resource_name, &$template_source, &$template_timestamp, &$smarty_obj)
{
    if ($resource_type == 'db') {
        $file_handler =& xoops_gethandler('tplfile');
        $tpl =& $file_handler->find('default', null, null, null, $resource_name, true);
        if (count($tpl) > 0 && is_object($tpl[0])) {
            $template_source = $tpl[0]->getSource();
            $template_timestamp = $tpl[0]->getLastModified();
            return true;
        }
    } else {
    }
    return false;
}

/**
 * Clear the module cache
 * 
 * @deprecated
 *
 * @param   int $mid    Module ID
 * @return 
 **/
function xoops_template_clear_module_cache($mid)
{
    $block_arr =& XoopsBlock::sGetByModule($mid);
    $count = count($block_arr);
    if ($count > 0) {
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->xoops_setCaching(2);
        for ($i = 0; $i < $count; $i++) {
            if ($block_arr[$i]->getVar('template') != '') {
                $xoopsTpl->clear_cache('db:'.$block_arr[$i]->getVar('template'), 'blk_'.$block_arr[$i]->getVar('bid'));
            }
        }
    }
}
