<?php
/**
 * AdminBlockOverview.class.php
 * @package    Legacy
 * @version    XCL 2.3.2
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_AdminBlockOverview extends Legacy_AbstractBlockProcedure
{
    public function getName()
    {
        return 'block_overview';
    }

    public function getTitle()
    {
        return 'Admin System Overview';
    }

    public function getEntryIndex()
    {
        return 0;
    }

    public function isEnableCache()
    {
        return false;
    }

    public function execute()
    {
        $root =& XCube_Root::getSingleton();
        // Catalog Legacy
        $root->mLanguageManager->loadBlockMessageCatalog('legacy');
        // Catalog User
        $root->mLanguageManager->loadModuleAdminMessageCatalog('user');

        $render =& $this->getRenderTarget();
        // Load theme template ie fallback
        $render->setAttribute('legacy_module', 'legacy');

        // MODULES OVERVIEW
        $moduleHandler =& xoops_gethandler('module');
        //$moduleHandler =& xoops_getmodulehandler('module');
        $module_total = $moduleHandler->getCount();
        $active_module_total = $moduleHandler->getCount(new Criteria('isactive', 1));
        $render->setAttribute('ModuleTotal', $module_total);
        $render->setAttribute('activeModuleTotal', $active_module_total);
        $render->setAttribute('inactiveModuleTotal', $module_total - $active_module_total);

        // BLOCKS
        $block_handler =& xoops_getmodulehandler('newblocks','legacy');
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

        // Users
        $member_handler =& xoops_getmodulehandler('users', 'user');
        $active_total = $member_handler->getCount(new Criteria('level', 0, '>'));
        $inactive_total = $member_handler->getCount(new Criteria('level', 0));
        $render->setAttribute('activeUserTotal', $active_total);
        $render->setAttribute('inactiveUserTotal', $inactive_total);
        $render->setAttribute('UserTotal', $active_total+$inactive_total);

        $render->setAttribute('blockid', $this->getName());

        $render->setTemplateName('legacy_admin_block_overview.html');

        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());

        // Render as block
        $renderSystem->renderBlock($render);
    }

    public function hasResult()
    {
        return true;
    }

    public function &getResult()
    {
        $dmy = 'dummy';
        return $dmy;
    }

    public function getRenderSystemName()
    {
        return 'Legacy_AdminRenderSystem';
    }
}


