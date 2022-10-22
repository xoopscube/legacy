<?php
/**
 * AdminActionSearch.class.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * This is test menu block for control panel of legacy module.
 *
 * [ASSIGN]
 *  No
 *
 * @package legacy
 */
class Legacy_AdminActionSearch extends Legacy_AbstractBlockProcedure
{
    public function getName()
    {
        return 'action_search';
    }

    public function getTitle()
    {
        return 'TEST: AdminActionSearch';
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
        $render =& $this->getRenderTarget();
        $render->setAttribute('legacy_module', 'legacy');
        $render->setTemplateName('legacy_admin_block_actionsearch.html');

        $root =& XCube_Root::getSingleton();

        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());

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
