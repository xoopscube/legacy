<?php
/**
 * Meta Weblog API
 * @package    kernel
 * @subpackage xml
 * @version    XCL 2.5.0
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH.'/class/xml/rpc/xmlrpcapi.php';

class MetaWeblogApi extends XoopsXmlRpcApi
{
    public function __construct(&$params, &$response, &$module)
    {
        parent::__construct($params, $response, $module);
        $this->_setXoopsTagMap('storyid', 'postid');
        $this->_setXoopsTagMap('published', 'dateCreated');
        $this->_setXoopsTagMap('uid', 'userid');
        //$this->_setXoopsTagMap('hometext', 'description');
    }

    public function newPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            if (!$fields =& $this->_getPostFields(null, $this->params[0])) {
                $this->response->add(new XoopsXmlRpcFault(106));
            } else {
                $missing = [];
                $post = [];
                foreach ($fields as $tag => $detail) {
                    $maptag = $this->_getXoopsTagMap($tag);
                    if (!isset($this->params[3][$maptag])) {
                        $data = $this->_getTagCdata($this->params[3]['description'], $maptag, true);
                        if ('' == trim($data)) {
                            if ($detail['required']) {
                                $missing[] = $maptag;
                            }
                        } else {
                            $post[$tag] = $data;
                        }
                    } else {
                        $post[$tag] = $this->params[3][$maptag];
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<'.$m.'> ';
                        echo $m;
                    }
                    $this->response->add(new XoopsXmlRpcFault(109, $msg));
                } else {
                    $newparams = [];
                    $newparams[0] = $this->params[0];
                    $newparams[1] = $this->params[1];
                    $newparams[2] = $this->params[2];
                    foreach ($post as $key => $value) {
                        $newparams[3][$key] =& $value;
                        unset($value);
                    }
                    $newparams[3]['xoops_text'] = $this->params[3]['description'];
                    if (isset($this->params[3]['categories']) && is_array($this->params[3]['categories'])) {
                        foreach ($this->params[3]['categories'] as $k => $v) {
                            $newparams[3]['categories'][$k] = $v;
                        }
                    }
                    $newparams[4] = $this->params[4];
                    $xoopsapi =& $this->_getXoopsApi($newparams);
                    $xoopsapi->_setUser($this->user, $this->isadmin);
                    $xoopsapi->newPost();
                }
            }
        }
    }

    public function editPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            if (!$fields =& $this->_getPostFields($this->params[0])) {
            } else {
                $missing = [];
                $post = [];
                foreach ($fields as $tag => $detail) {
                    $maptag = $this->_getXoopsTagMap($tag);
                    if (!isset($this->params[3][$maptag])) {
                        $data = $this->_getTagCdata($this->params[3]['description'], $maptag, true);
                        if ('' == trim($data)) {
                            if ($detail['required']) {
                                $missing[] = $tag;
                            }
                        } else {
                            $post[$tag] = $data;
                        }
                    } else {
                        $post[$tag] =& $this->params[3][$maptag];
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<'.$m.'> ';
                    }
                    $this->response->add(new XoopsXmlRpcFault(109, $msg));
                } else {
                    $newparams = [];
                    $newparams[0] = $this->params[0];
                    $newparams[1] = $this->params[1];
                    $newparams[2] = $this->params[2];
                    foreach ($post as $key => $value) {
                        $newparams[3][$key] =& $value;
                        unset($value);
                    }
                    if (isset($this->params[3]['categories']) && is_array($this->params[3]['categories'])) {
                        foreach ($this->params[3]['categories'] as $k => $v) {
                            $newparams[3]['categories'][$k] = $v;
                        }
                    }
                    $newparams[3]['xoops_text'] = $this->params[3]['description'];
                    $newparams[4] = $this->params[4];
                    $xoopsapi =& $this->_getXoopsApi($newparams);
                    $xoopsapi->_setUser($this->user, $this->isadmin);
                    $xoopsapi->editPost();
                }
            }
        }
    }

    public function getPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            $xoopsapi =& $this->_getXoopsApi($this->params);
            $xoopsapi->_setUser($this->user, $this->isadmin);
            $ret =& $xoopsapi->getPost(false);
            if (is_array($ret)) {
                $struct = new XoopsXmlRpcStruct();
                $content = '';
                foreach ($ret as $key => $value) {
                    $maptag = $this->_getXoopsTagMap($key);
                    switch ($maptag) {
                    case 'userid':
                        $struct->add('userid', new XoopsXmlRpcString($value));
                        break;
                    case 'dateCreated':
                        $struct->add('dateCreated', new XoopsXmlRpcDatetime($value));
                        break;
                    case 'postid':
                        $struct->add('postid', new XoopsXmlRpcString($value));
                        $struct->add('link', new XoopsXmlRpcString(XOOPS_URL.'/modules/xoopssections/item.php?item='.$value));
                        $struct->add('permaLink', new XoopsXmlRpcString(XOOPS_URL.'/modules/xoopssections/item.php?item='.$value));
                        break;
                    case 'title':
                        $struct->add('title', new XoopsXmlRpcString($value));
                        break;
                    default :
                        $content .= '<'.$key.'>'.trim($value).'</'.$key.'>';
                        break;
                    }
                }
                $struct->add('description', new XoopsXmlRpcString($content));
                $this->response->add($struct);
            } else {
                $this->response->add(new XoopsXmlRpcFault(106));
            }
        }
    }

    public function getRecentPosts()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            $xoopsapi =& $this->_getXoopsApi($this->params);
            $xoopsapi->_setUser($this->user, $this->isadmin);
            $ret =& $xoopsapi->getRecentPosts(false);
            if (is_array($ret)) {
                $arr = new XoopsXmlRpcArray();
                $count = count($ret);
                if (0 == $count) {
                    $this->response->add(new XoopsXmlRpcFault(106, 'Found 0 Entries'));
                } else {
                    for ($i = 0; $i < $count; $i++) {
                        $struct = new XoopsXmlRpcStruct();
                        $content = '';
                        foreach ($ret[$i] as $key => $value) {
                            $maptag = $this->_getXoopsTagMap($key);
                            switch ($maptag) {
                            case 'userid':
                                $struct->add('userid', new XoopsXmlRpcString($value));
                                break;
                            case 'dateCreated':
                                $struct->add('dateCreated', new XoopsXmlRpcDatetime($value));
                                break;
                            case 'postid':
                                $struct->add('postid', new XoopsXmlRpcString($value));
                                $struct->add('link', new XoopsXmlRpcString(XOOPS_URL.'/modules/news/article.php?item_id='.$value));
                                $struct->add('permaLink', new XoopsXmlRpcString(XOOPS_URL.'/modules/news/article.php?item_id='.$value));
                                break;
                            case 'title':
                                $struct->add('title', new XoopsXmlRpcString($value));
                                break;
                            default :
                                $content .= '<'.$key.'>'.trim($value).'</'.$key.'>';
                                break;
                            }
                        }
                        $struct->add('description', new XoopsXmlRpcString($content));
                        $arr->add($struct);
                        unset($struct);
                    }
                    $this->response->add($arr);
                }
            } else {
                $this->response->add(new XoopsXmlRpcFault(106));
            }
        }
    }

    public function getCategories()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new XoopsXmlRpcFault(104));
        } else {
            $xoopsapi =& $this->_getXoopsApi($this->params);
            $xoopsapi->_setUser($this->user, $this->isadmin);
            $ret =& $xoopsapi->getCategories(false);
            if (is_array($ret)) {
                $arr = new XoopsXmlRpcArray();
                foreach ($ret as $id => $detail) {
                    $struct = new XoopsXmlRpcStruct();
                    $struct->add('description', new XoopsXmlRpcString($detail));
                    $struct->add('htmlUrl', new XoopsXmlRpcString(XOOPS_URL.'/modules/news/index.php?storytopic='.$id));
                    $struct->add('rssUrl', new XoopsXmlRpcString(''));
                    $catstruct = new XoopsXmlRpcStruct();
                    $catstruct->add($detail['title'], $struct);
                    $arr->add($catstruct);
                    unset($struct);
                    unset($catstruct);
                }
                $this->response->add($arr);
            } else {
                $this->response->add(new XoopsXmlRpcFault(106));
            }
        }
    }
}
