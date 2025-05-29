<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerFilterForm.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/BannerClient.class.php';

class Bannerstats_BannerListAction extends Bannerstats_AbstractListAction
{
    /**
     * Gets the handler for banner objects
     * @return Bannerstats_BannerHandler|false
     */
    public function &_getHandler()
    {
        $handler = xoops_getmodulehandler('banner', 'bannerstats');
        return $handler;
    }

    /**
     * Gets the filter form for the banner list
     * @return Bannerstats_BannerFilterForm
     */
    public function &_getFilterForm()
    {
        $pageNavi = $this->_getPageNavi();
        $handler = $this->_getHandler();
        $filter = new Bannerstats_BannerFilterForm($pageNavi, $handler);
        return $filter;
    }

    /**
     * Gets the base URL for this action
     * @return string
     */
    public function _getBaseUrl(): string
    {
        return './index.php?action=BannerList';
    }

    /**
     * Prepares and sets data for the list view
     *
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     * @return void
     */
    public function executeViewIndex(&$controller, &$xoopsUser, &$render): void
    {
        $render->setTemplateName('banner_list.html');

        if (is_array($this->mObjects)) {
            foreach (array_keys($this->mObjects) as $key) {
                if (isset($this->mObjects[$key]) && $this->mObjects[$key] instanceof Bannerstats_BannerObject) {
                    $this->mObjects[$key]->loadBannerclient();
                }
            }
        }
        $render->setAttribute('objects', $this->mObjects);

        if (is_object($this->mFilter) && isset($this->mFilter->mNavi)) {
            $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        } else {
            $root = XCube_Root::getSingleton();
            $pageNavi = new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_OFFSET);
            $render->setAttribute('pageNavi', $pageNavi);
        }
        
        $cid = xoops_getrequest('cid') ? (int)xoops_getrequest('cid') : 0;
        if ($cid > 0) {
            $clientHandler = xoops_getmodulehandler('bannerclient', 'bannerstats');
            if ($clientHandler) {
                $client = $clientHandler->get($cid);
                if (is_object($client) && $client instanceof Bannerstats_BannerclientObject) {
                    $render->setAttribute('currentClient', $client);
                }
            }
        }
    }
}
