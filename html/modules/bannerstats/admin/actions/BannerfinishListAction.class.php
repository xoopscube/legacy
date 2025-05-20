<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerfinishFilterForm.class.php';

class Bannerstats_BannerfinishListAction extends Bannerstats_AbstractListAction
{
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('bannerfinish');
        return $handler;
    }

    public function &_getFilterForm()
    {
        $filter =new Bannerstats_BannerfinishFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function _getBaseUrl()
    {
        return './index.php?action=BannerfinishList';
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('bannerfinish_list.html');
        foreach (array_keys($this->mObjects) as $key) {
            $this->mObjects[$key]->loadBannerclient();
        }
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        
        //
        // If cid is specified, load client object and assign it.
        //
        $cid = xoops_getrequest('cid');
        if ($cid > 0) {
            $handler =& xoops_getmodulehandler('bannerclient');
            $client =& $handler->get($cid);
            if (is_object($client)) {
                $render->setAttribute('currentClient', $client);
            }
        }
    }
}
