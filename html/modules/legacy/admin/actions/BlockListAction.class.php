<?php
/**
 * BlockListAction.class.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/BlockFilterForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/BlockListForm.class.php';

class Legacy_BlockListAction extends Legacy_AbstractListAction
{
    public $mBlockObjects = [];
    public $mActionForm = null;
    public $mpageArr = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0];

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->mActionForm =new Legacy_BlockListForm();
        $this->mActionForm->prepare();
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('newblocks');
        return $handler;
    }

    public function &_getFilterForm()
    {
        $filter =new Legacy_BlockFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function &_getPageNavi()
    {
        $navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);

        $root =& XCube_Root::getSingleton();
        $perpage = $root->mContext->mRequest->getRequest($navi->mPrefix.'perpage');

        if (isset($perpage) && 0 == (int)$perpage) {
            $navi->setPerpage(0);
        }

        // naao added selectedMid filter
        $selectedMid = (int)$root->mContext->mRequest->getRequest('selmid') ;
        if (0 !== $selectedMid) {
            $navi->addExtra('selmid', $selectedMid);
        }
        $selectedGid = (int)$root->mContext->mRequest->getRequest('selgid') ;
        if (0 !== $selectedGid) {
            $navi->addExtra('selgid', $selectedGid);
        }
        return $navi;
    }

    public function _getBaseUrl()
    {
        return './index.php?action=BlockList';
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('block_list.html');
        foreach (array_keys($this->mObjects) as $key) {
            $this->mObjects[$key]->loadModule();
            $this->mObjects[$key]->loadColumn();
            $this->mObjects[$key]->loadCachetime();
        }

        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);

        $render->setAttribute('modules', $controller->mActiveModules);
        $render->setAttribute('filterForm', $this->mFilter);
        $render->setAttribute('pageArr', $this->mpageArr);

        // added query for view module pages
        $root =& XCube_Root::getSingleton();
        $render->setAttribute('selectedMid', $root->mContext->mRequest->getRequest('selmid'));
        $handler =& xoops_gethandler('module');
        $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));
        $criteria->add(new Criteria('weight', 0, '>'));
        $view_modules = $handler->getObjects($criteria);
        $render->setAttribute('view_modules', $view_modules);

        // added query for groups
        $handler =& xoops_gethandler('group');
        $groupArr =& $handler->getObjects();
        $render->setAttribute('groupArr', $groupArr);
        $render->setAttribute('selectedGid', $root->mContext->mRequest->getRequest('selgid'));

        //
        // Load cache-time pattern objects and set.
        //
        $handler =& xoops_gethandler('cachetime');
        $cachetimeArr =& $handler->getObjects();
        $render->setAttribute('cachetimeArr', $cachetimeArr);
        $render->setAttribute('actionForm', $this->mActionForm);
        //
        $handler =& xoops_getmodulehandler('columnside');
        $columnSideArr =& $handler->getObjects();
        $render->setAttribute('columnSideArr', $columnSideArr);

        $block_handler =& $this->_getHandler();
        $block_total = $block_handler->getCount();
        $inactive_block_total = $block_handler->getCount(new Criteria('isactive', 0));
        $active_block_total = $block_total-$inactive_block_total;
        $render->setAttribute('BlockTotal', $block_total);
        $render->setAttribute('ActiveBlockTotal', $active_block_total);
        $render->setAttribute('InactiveBlockTotal', $inactive_block_total);

        $active_installed_criteria = new CriteriaCompo(new Criteria('visible', 1));
        $active_installed_criteria->add(new Criteria('isactive', 1));
        $active_installed_block_total = $block_handler->getCount($active_installed_criteria);
        $render->setAttribute('ActiveInstalledBlockTotal', $active_installed_block_total);
        $render->setAttribute('ActiveUninstalledBlockTotal', $active_block_total - $active_installed_block_total);

        $inactive_installed_criteria = new CriteriaCompo(new Criteria('visible', 1));
        $inactive_installed_criteria->add(new Criteria('isactive', 0));
        $inactive_installed_block_total = $block_handler->getCount($inactive_installed_criteria);
        $render->setAttribute('InactiveInstalledBlockTotal', $inactive_installed_block_total);
        $render->setAttribute('InactiveUninstalledBlockTotal', $inactive_block_total - $inactive_installed_block_total);
    }


    public function execute(&$controller, &$xoopsUser)
    {
        $form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
        if (null !== $form_cancel) {
            return LEGACY_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return $this->_processConfirm($controller, $xoopsUser);
        }

        return $this->_processSave($controller, $xoopsUser);
    }

    public function _processConfirm(&$controller, &$xoopsUser)
    {
        $titleArr = $this->mActionForm->get('title');
        $blockHandler =& xoops_getmodulehandler('newblocks');
        //
        // Do mapping.
        //
        foreach (array_keys($titleArr) as $bid) {
            $block =& $blockHandler->get($bid);
            if (is_object($block) && 1 == $block->get('isactive') && 1 == $block->get('visible')) {
                $this->mBlockObjects[$bid] =& $block;
                $this->mBlockObjects[$bid]->loadColumn();
                $this->mBlockObjects[$bid]->loadCachetime();
            }
            unset($block);
        }

        return LEGACY_FRAME_VIEW_INPUT;
    }

    public function _processSave(&$controller, &$xoopsUser)
    {
        $titleArr = $this->mActionForm->get('title');
        $blockHandler =& xoops_getmodulehandler('newblocks');

        foreach (array_keys($titleArr) as $bid) {
            $block =& $blockHandler->get($bid);
            if (is_object($block) && 1 == $block->get('isactive') && 1 == $block->get('visible')) {
                $olddata['title'] = $block->get('title');
                $olddata['weight'] = $block->get('weight');
                $olddata['side'] = $block->get('side');
                $olddata['bcachetime'] = $block->get('bcachetime');
                $newdata['title'] = $this->mActionForm->get('title', $bid);
                $newdata['weight'] = $this->mActionForm->get('weight', $bid);
                $newdata['side'] = $this->mActionForm->get('side', $bid);
                $newdata['bcachetime'] = $this->mActionForm->get('bcachetime', $bid);
                if (count(array_diff_assoc($olddata, $newdata)) > 0) {
                    $block->set('title', $this->mActionForm->get('title', $bid));
                    $block->set('weight', $this->mActionForm->get('weight', $bid));
                    $block->set('side', $this->mActionForm->get('side', $bid));
                    $block->set('bcachetime', $this->mActionForm->get('bcachetime', $bid));
                    $block->set('last_modified', time());
                    if (!$blockHandler->insert($block)) {
                        return LEGACY_FRAME_VIEW_ERROR;
                    }
                }//count if
            }//object if
        }

        //uninstall process
                foreach (array_keys($titleArr) as $bid) {
                    if (1 == $this->mActionForm->get('uninstall', $bid)) {
                        $block =& $blockHandler->get($bid);
                        if (is_object($block) && 1 == $block->get('isactive') && 1 == $block->get('visible')) {
                            $block->set('visible', 0);
                            if (!$blockHandler->insert($block)) {
                                return LEGACY_FRAME_VIEW_ERROR;
                            }
                        }//object if
                    }//if
                }

        return LEGACY_FRAME_VIEW_SUCCESS;
    }


    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('block_list_confirm.html');
        $render->setAttribute('blockObjects', $this->mBlockObjects);
        $render->setAttribute('actionForm', $this->mActionForm);

        $t_arr = $this->mActionForm->get('title');
        $render->setAttribute('bids', array_keys($t_arr));

        $handler =& xoops_getmodulehandler('columnside');
        $columnSideArr =& $handler->getObjects($criteria = null, $id_as_key = true);
        $render->setAttribute('columnSideArr', $columnSideArr);
        $handler =& xoops_gethandler('cachetime');
        $cachetimeArr =& $handler->getObjects($criteria = null, $id_as_key = true);
        $render->setAttribute('cachetimeArr', $cachetimeArr);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BlockList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=BlockInstallList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeForward('./index.php?action=BlockList');
    }
}
