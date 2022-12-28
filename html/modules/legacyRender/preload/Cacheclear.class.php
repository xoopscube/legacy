<?php
/**
 * @package legacyRender
 * @version $Id: Cacheclear.class.php,v 1.2 2007/06/18 07:41:55 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/***
 * @internal
 * @todo
 *    This may have to be admin-preload.
 */
class LegacyRender_Cacheclear extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Legacy_ModuleInstallAction.InstallSuccess', [$this, 'cacheClear']);
        $this->mRoot->mDelegateManager->add('Legacy_ModuleUpdateAction.UpdateSuccess', [$this, 'cacheClear']);
        $this->mRoot->mDelegateManager->add('Legacy_ModuleUninstaller._fireNotifyUninstallTemplateBegun', [$this, 'cacheClear']);
    }
    
    public function cacheClear(&$module)
    {
        $handler =& xoops_getmodulehandler('tplfile', 'legacyRender');
        
        $criteria =new Criteria('tpl_module', $module->get('dirname'));
        $tplfileArr = $handler->getObjects($criteria);
        
        $xoopsTpl =new XoopsTpl();
        foreach (array_keys($tplfileArr) as $key) {
            $xoopsTpl->clearCache('db:' . $tplfileArr[$key]->get('tpl_file'));
            $xoopsTpl->clearCompiledTemplate('db:' . $tplfileArr[$key]->get('tpl_file'));
        }
    }
}
