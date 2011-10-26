<?php
/**
 *
 * @package Legacy
 * @version $Id: Waiting.class.php,v 1.3 2008/09/25 15:12:44 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_Waiting extends XCube_ActionFilter {
    function preBlockFilter()
    {
        $this->mController->mRoot->mDelegateManager->add('Legacyblock.Waiting.Show',array(&$this,"callbackWaitingShow"));
    }
    
    function callbackWaitingShow(&$modules) {
        $xoopsDB =& Database::getInstance();
        // for News Module
        $module_handler =& xoops_gethandler('module');
        if ($module_handler->getCount(new Criteria('dirname', 'news'))) {
            $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("stories")." WHERE published=0");
            if ( $result ) {
                $blockVal = array();
                $blockVal['adminlink'] = XOOPS_URL."/modules/news/admin/index.php?op=newarticle";
                list($blockVal['pendingnum']) = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_SUBMS;
                $modules[] = $blockVal;
            }
        }
        // for MyLinks Module
        if ($module_handler->getCount(new Criteria('dirname', 'mylinks'))) {
            $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mylinks_links")." WHERE status=0");
            if ( $result ) {
                $blockVal = array();
                $blockVal['adminlink'] = XOOPS_URL."/modules/mylinks/admin/index.php?op=listNewLinks";
                list($blockVal['pendingnum']) = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_WLNKS;
                $modules[] = $blockVal;
            }
            $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mylinks_broken"));
            if ( $result ) {
                $blockVal = array();
                $blockVal['adminlink'] = XOOPS_URL."/modules/mylinks/admin/index.php?op=listBrokenLinks";
                list($blockVal['pendingnum']) = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_BLNK;
                $modules[] = $blockVal;
            }
            $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mylinks_mod"));
            if ( $result ) {
                $blockVal = array();
                $blockVal['adminlink'] = XOOPS_URL."/modules/mylinks/admin/index.php?op=listModReq";
                list($blockVal['pendingnum']) = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_MLNKS;
                $modules[] = $blockVal;
            }
        }
        // for MyDownloads Modules
        if ($module_handler->getCount(new Criteria('dirname', 'mydownloads'))) {
            $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mydownloads_downloads")." WHERE status=0");
            if ( $result ) {
                $blockVal = array();
                $blockVal['adminlink'] = XOOPS_URL."/modules/mydownloads/admin/index.php?op=listNewDownloads";
                list($blockVal['pendingnum']) = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_WDLS;
                $modules[] = $blockVal;
            }
            $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mydownloads_broken")."");
            if ( $result ) {
                $blockVal = array();
                $blockVal['adminlink'] = XOOPS_URL."/modules/mydownloads/admin/index.php?op=listBrokenDownloads";
                list($blockVal['pendingnum']) = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_BFLS;
                $modules[] = $blockVal;
            }
            $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("mydownloads_mod")."");
            if ( $result ) {
                $blockVal = array();
                $blockVal['adminlink'] = XOOPS_URL."/modules/mydownloads/admin/index.php?op=listModReq";
                list($blockVal['pendingnum']) = $xoopsDB->fetchRow($result);
                $blockVal['lang_linkname'] = _MB_LEGACY_MFLS;
                $modules[] = $blockVal;
            }
        }
        // for Comments
        $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("xoopscomments")." WHERE com_status=1");
        if ( $result ) {
            $blockVal = array();
            $blockVal['adminlink'] = XOOPS_URL."/modules/legacy/admin/index.php?action=CommentList&amp;com_modid=0&amp;com_status=1";
            list($blockVal['pendingnum']) = $xoopsDB->fetchRow($result);
            $blockVal['lang_linkname'] =_MB_LEGACY_COMPEND;
            $modules[] = $blockVal;
        }
    }
}
?>
