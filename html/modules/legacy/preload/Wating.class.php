<?php
/**
 *
 * @package Legacy
 * @version $Id: Waiting.class.php,v 1.3 2008/09/25 15:12:44 kilica Exp $
 * @copyright (c) 2005-2023 The XOOPSCube Project
 * @license   GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_Waiting extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mController->mRoot->mDelegateManager->add('Legacyblock.Waiting.Show', [&$this, 'callbackWaitingShow']);
    }

    public function callbackWaitingShow(&$modules)
    {
        $xoopsDB =& Database::getInstance();
        $module_handler =& xoops_gethandler('module');
        // for News Module
        if ($module_handler->getCount(new Criteria('dirname', 'news'))) {
            $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('stories') . ' WHERE published=0');
            if ($result) {
                $blockVal = [];
                $blockVal['adminlink'] = XOOPS_URL . '/modules/news/admin/index.php?op=newarticle';
                [$blockVal['pendingnum']] = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_SUBMS;
                $modules[] = $blockVal;
            }
        }
        // for MyLinks Module
        if ($module_handler->getCount(new Criteria('dirname', 'mylinks'))) {
            $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('mylinks_links') . ' WHERE status=0');
            if ($result) {
                $blockVal = [];
                $blockVal['adminlink'] = XOOPS_URL . '/modules/mylinks/admin/index.php?op=listNewLinks';
                [$blockVal['pendingnum']] = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_WLNKS;
                $modules[] = $blockVal;
            }
            $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('mylinks_broken'));
            if ($result) {
                $blockVal = [];
                $blockVal['adminlink'] = XOOPS_URL . '/modules/mylinks/admin/index.php?op=listBrokenLinks';
                [$blockVal['pendingnum']] = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_BLNK;
                $modules[] = $blockVal;
            }
            $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('mylinks_mod'));
            if ($result) {
                $blockVal = [];
                $blockVal['adminlink'] = XOOPS_URL . '/modules/mylinks/admin/index.php?op=listModReq';
                [$blockVal['pendingnum']] = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_MLNKS;
                $modules[] = $blockVal;
            }
        }
        // for MyDownloads Modules
        if ($module_handler->getCount(new Criteria('dirname', 'mydownloads'))) {
            $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('mydownloads_downloads') . ' WHERE status=0');
            if ($result) {
                $blockVal = [];
                $blockVal['adminlink'] = XOOPS_URL . '/modules/mydownloads/admin/index.php?op=listNewDownloads';
                [$blockVal['pendingnum']] = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_WDLS;
                $modules[] = $blockVal;
            }
            $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('mydownloads_broken') . '');
            if ($result) {
                $blockVal = [];
                $blockVal['adminlink'] = XOOPS_URL . '/modules/mydownloads/admin/index.php?op=listBrokenDownloads';
                [$blockVal['pendingnum']] = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_BFLS;
                $modules[] = $blockVal;
            }
            $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('mydownloads_mod') . '');
            if ($result) {
                $blockVal = [];
                $blockVal['adminlink'] = XOOPS_URL . '/modules/mydownloads/admin/index.php?op=listModReq';
                [$blockVal['pendingnum']] = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_MFLS;
                $modules[] = $blockVal;
            }
        }
        // for Comments
        $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('xoopscomments') . ' WHERE com_status=1');
        if ($result) {
            $blockVal = [];
            $blockVal['adminlink'] = XOOPS_URL . '/modules/legacy/admin/index.php?action=CommentList&amp;com_modid=0&amp;com_status=1';
            [$blockVal['pendingnum']] = $xoopsDB->fetchRow($result);
            $blockVal['lang_linkname'] =_MB_LEGACY_COMPEND;
            $modules[] = $blockVal;
        }
    }
}
