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
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerfinishFilterForm.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/BannerFinish.class.php'; // For Bannerstats_BannerfinishObject
require_once XOOPS_MODULE_PATH . '/bannerstats/class/BannerClient.class.php'; // For Bannerstats_BannerclientObject


class Bannerstats_BannerfinishListAction extends Bannerstats_AbstractListAction
{
    /**
     * Gets the handler for finished banner objects.
     * @return Bannerstats_BannerfinishHandler|false
     */
    public function &_getHandler()
    {
        $handler = xoops_getmodulehandler('bannerfinish', 'bannerstats');
        return $handler;
    }

    /**
     * Gets the filter form for the finished banner list
     * @return Bannerstats_BannerfinishFilterForm
     */
    public function &_getFilterForm()
    {
        $pageNavi = $this->_getPageNavi();
        $handler = $this->_getHandler();
        $filter = new Bannerstats_BannerfinishFilterForm($pageNavi, $handler);
        return $filter;
    }

    /**
     * Gets the base URL for this action
     * @return string
     */
    public function _getBaseUrl(): string
    {
        return './index.php?action=BannerfinishList';
    }

    /**
     * Prepares and sets data for the list view
     *
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @param XCube_RenderTarget $render
     * @return void
     */
    public function executeViewIndex(&$controller, &$xoopsUser, &$render): void
    {
        $render->setTemplateName('bannerfinish_list.html');

        if (is_array($this->mObjects)) {
            foreach ($this->mObjects as $obj) {
                if ($obj instanceof Bannerstats_BannerfinishObject) {
                    $obj->loadBannerclient();
                }
            }
        }
        $render->setAttribute('objects', $this->mObjects);

        if (is_object($this->mFilter) && $this->mFilter->mNavi instanceof XCube_PageNavigator) {
            $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        } else {
            $root = XCube_Root::getSingleton();
            $pageNavi = new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_OFFSET);
            $render->setAttribute('pageNavi', $pageNavi);
            error_log(get_class($this) . "::" . __FUNCTION__ . " - PageNavigator was not initialized via filter form.");
        }
        
        $root = XCube_Root::getSingleton();
        $cid = $root->mContext->mRequest->getRequest('cid');
        if ($cid !== null && ctype_digit((string)$cid) && (int)$cid > 0) {
            $cid = (int)$cid;
            $clientHandler = xoops_getmodulehandler('bannerclient', 'bannerstats');
            if ($clientHandler) {
                $client = $clientHandler->get($cid);
                if ($client instanceof Bannerstats_BannerclientObject) {
                    $render->setAttribute('currentClient', $client);
                }
            }
        }
    }
}
