<?php
/**
 *
 * @package Legacy
 * @version $Id: IndexRedirector.class.php,v 1.3 2008/09/25 15:12:44 kilica Exp $
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_IndexRedirector extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mController->mRoot->mDelegateManager->add('Legacypage.Top.Access', [&$this, 'redirect']);
    }

    public function redirect()
    {
        $startPage = $this->mRoot->mContext->getXoopsConfig('startpage');
        if (null !== $startPage && '--' !== $startPage) {
            $handler =& xoops_gethandler('module');
            $module =& $handler->get($startPage);
            if (is_object($module) && $module->get('isactive')) {
                $this->mController->executeForward(XOOPS_URL . '/modules/' . $module->getShow('dirname') . '/');
            }
        }
    }
}
