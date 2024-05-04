<?php
/**
 * *
 *  * Preload is Top page
 *  *
 *  * Usage
 *  * <{if $xoops_is_top}> It's the top page! <{else}> It's not the top page <{/if}>
 *  *
 *  * @package    Preload
 *  * @author     Original Author : Ryuji AMANO
 *  * @copyright  (c) 2005-2024 The XOOPSCube Project
 *  * @license    Legacy : https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 *  * @license    Cube : https://github.com/xoopscube/xcl/blob/master/BSD_license.txt
 *  * @version    Release: @package_230@
 *  * @link       https://github.com/xoopscube/xcl
 * *
 */
class IsToppage extends XCube_ActionFilter
{
    protected $isTop = false;
    public function preBlockFilter()
    {
        $this->mController->mRoot->mDelegateManager->add("Legacypage.Top.Access", array(&$this, 'topAccess'));
    }

    public function topAccess()
    {
        $this->isTop = true;
        $GLOBALS['xoopsTpl']->assign('xoops_is_top', $this->isTop);
    }
}
