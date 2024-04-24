<?php
/**
 * XML RPC API
 * @package    kernel
 * @subpackage xml
 * @version    XCL 2.4.0
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 * */

class XoopsXmlRpcApi
{

    // reference to method parameters
    public $params;

    // reference to xmlrpc document class object
    public $response;

    // reference to module class object
    public $module;

    // map between xoops tags and blogger specific tags
    public $xoopsTagMap = [];

    // user class object
    public $user;

    public $isadmin = false;



    public function __construct(&$params, &$response, &$module)
    {
        $this->params =& $params;
        $this->response =& $response;
        $this->module =& $module;
    }

    public function _setUser(&$user, $isadmin = false)
    {
        if (is_object($user)) {
            $this->user =& $user;
            $this->isadmin = $isadmin;
        }
    }

    public function _checkUser($username, $password)
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

    public function _checkAdmin()
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

    public function &_getPostFields($post_id = null, $blog_id = null)
    {
        $ret = [];
        $ret['title'] = ['required' => true, 'form_type' => 'textbox', 'value_type' => 'text'];
        $ret['hometext'] = ['required' => false, 'form_type' => 'textarea', 'data_type' => 'textarea'];
        $ret['moretext'] = ['required' => false, 'form_type' => 'textarea', 'data_type' => 'textarea'];
        $ret['categories'] = ['required' => false, 'form_type' => 'select_multi', 'data_type' => 'array'];
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

    public function _setXoopsTagMap($xoopstag, $blogtag)
    {
        if ('' != trim($blogtag)) {
            $this->xoopsTagMap[$xoopstag] = $blogtag;
        }
    }

    public function _getXoopsTagMap($xoopstag)
    {
        if (isset($this->xoopsTagMap[$xoopstag])) {
            return $this->xoopsTagMap[$xoopstag];
        }
        return $xoopstag;
    }

    public function _getTagCdata(&$text, $tag, $remove = true)
    {
        $ret = '';
        $match = [];
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
    public function &_getXoopsApi(&$params)
    {
        if ('xoopsapi' != strtolower(get_class($this))) {
            require_once(XOOPS_ROOT_PATH.'/class/xml/rpc/xoopsapi.php');
            $instance =new XoopsApi($params, $this->response, $this->module);
            return $instance;
        } else {
            return $this;
        }
    }
}
