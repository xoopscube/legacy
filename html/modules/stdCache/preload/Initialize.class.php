<?php
/**
 * @package stdCache
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: Initialize.class.php,v 1.1 2007/05/15 02:35:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
 
class StdCache_Initialize extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mController->mSetBlockCachePolicy->add([$this, 'setForBlock'], XCUBE_DELEGATE_PRIORITY_FIRST + 10);
        $this->mController->mSetModuleCachePolicy->add([$this, 'setForModule'], XCUBE_DELEGATE_PRIORITY_FIRST + 10);
    }
    
    public function setForBlock($cacheInfo)
    {
        $user = $this->mRoot->mContext->mXoopsUser;
        $block = $cacheInfo->mBlock;
        switch ($block->getVar('show_func', 'n')) {
        case 'b_legacy_mainmenu_show':    // mainmenu context
            [$option] = explode('|', $block->getVar('options', 'n'));
            if ($option) {
                break;
            }            // but options setting no context
            $module = $this->mRoot->mContext->mXoopsModule;
            $cacheInfo->mIdentityArr['dirname'] = is_object($module)?$module->getVar('dirname'):'';
            break;
            // something other block with context cache
        }
        if (is_object($user)) {
            $cacheInfo->mGroupArr = $user->getGroups();
            $cacheInfo->setEnableCache(!in_array(XOOPS_GROUP_ADMIN, $user->getGroups()));
        } else {
            $cacheInfo->mGroupArr = [XOOPS_GROUP_ANONYMOUS];
            $cacheInfo->setEnableCache(true);
        }
    }

    public function setForModule($cacheInfo)
    {
        $user =& $this->mRoot->mContext->mXoopsUser;
        if (is_object($user)) {
            $cacheInfo->mGroupArr = $user->getGroups();
            $cacheInfo->setEnableCache(!in_array(XOOPS_GROUP_ADMIN, $user->getGroups()));
        } else {
            $cacheInfo->mGroupArr = [XOOPS_GROUP_ANONYMOUS];
            $cacheInfo->setEnableCache(true);
        }
    }
}
