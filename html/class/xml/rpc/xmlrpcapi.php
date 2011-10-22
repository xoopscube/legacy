<?php
// $Id: xmlrpcapi.php,v 1.1 2007/05/15 02:34:53 minahito Exp $
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


class XoopsXmlRpcApi
{

    // reference to method parameters
    var $params;

    // reference to xmlrpc document class object
    var $response;

    // reference to module class object
    var $module;

    // map between xoops tags and blogger specific tags
    var $xoopsTagMap = array();

    // user class object
    var $user;

    var $isadmin = false;



    function XoopsXmlRpcApi(&$params, &$response, &$module)
    {
        $this->params =& $params;
        $this->response =& $response;
        $this->module =& $module;
    }

    function _setUser(&$user, $isadmin = false)
    {
        if (is_object($user)) {
            $this->user =& $user;
            $this->isadmin = $isadmin;
        }
    }

    function _checkUser($username, $password)
    {
        if (isset($this->user)) {
            return true;
        }
        $member_handler =& xoops_gethandler('member');
        $this->user =& $member_handler->loginUser(addslashes($username), addslashes($password));
        if (!is_object($this->user)) {
            unset($this->user);
            return false;
        }
        $moduleperm_handler =& xoops_gethandler('groupperm');
        if (!$moduleperm_handler->checkRight('module_read', $this->module->getVar('mid'), $this->user->getGroups())) {
            unset($this->user);
            return false;
        }
        return true;
    }

    function _checkAdmin()
    {
        if ($this->isadmin) {
            return true;
        }
        if (!isset($this->user)) {
            return false;
        }
        if (!$this->user->isAdmin($this->module->getVar('mid'))) {
            return false;
        }
        $this->isadmin = true;
        return true;
    }

    function &_getPostFields($post_id = null, $blog_id = null)
    {
        $ret = array();
        $ret['title'] = array('required' => true, 'form_type' => 'textbox', 'value_type' => 'text');
        $ret['hometext'] = array('required' => false, 'form_type' => 'textarea', 'data_type' => 'textarea');
        $ret['moretext'] = array('required' => false, 'form_type' => 'textarea', 'data_type' => 'textarea');
        $ret['categories'] = array('required' => false, 'form_type' => 'select_multi', 'data_type' => 'array');
        /*
        if (!isset($blog_id)) {
            if (!isset($post_id)) {
                return false;
            }
            $itemman =& $this->mf->get(MANAGER_ITEM);
            $item =& $itemman->get($post_id);
            $blog_id = $item->getVar('sect_id');
        }
        $sectman =& $this->mf->get(MANAGER_SECTION);
        $this->section =& $sectman->get($blog_id);
        $ret =& $this->section->getVar('sect_fields');
        */
        return $ret;
    }

    function _setXoopsTagMap($xoopstag, $blogtag)
    {
        if (trim($blogtag) != '') {
            $this->xoopsTagMap[$xoopstag] = $blogtag;
        }
    }

    function _getXoopsTagMap($xoopstag)
    {
        if (isset($this->xoopsTagMap[$xoopstag])) {
            return $this->xoopsTagMap[$xoopstag];
        }
        return $xoopstag;
    }

    function _getTagCdata(&$text, $tag, $remove = true)
    {
        $ret = '';
        $match = array();
        if (preg_match("/\<".$tag."\>(.*)\<\/".$tag."\>/is", $text, $match)) {
            if ($remove) {
                $text = str_replace($match[0], '', $text);
            }
            $ret = $match[1];
        }
        return $ret;
    }

    // kind of dirty method to load XOOPS API and create a new object thereof
    // returns itself if the calling object is XOOPS API
    function &_getXoopsApi(&$params)
    {
        if (strtolower(get_class($this)) != 'xoopsapi') {
            require_once(XOOPS_ROOT_PATH.'/class/xml/rpc/xoopsapi.php');
            $instance =new XoopsApi($params, $this->response, $this->module); 
            return $instance;
        } else {
            return $this;
        }
    }
}
?>
