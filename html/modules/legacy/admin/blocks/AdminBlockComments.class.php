<?php
/**
 * Admin Users Online
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_AdminBlockComments extends Legacy_AbstractBlockProcedure
{
    public function getName()
    {
        return 'block_comments';
    }

    public function getTitle()
    {
        return 'Comments';
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
        $root->mLanguageManager->loadBlockMessageCatalog('legacy');

        require_once XOOPS_ROOT_PATH . '/modules/legacy/blocks/legacy_comments.php';

        $options = $_GET['options'] ?? [0];

        $contents = b_legacy_comments_show($options);

        $render =& $this->getRenderTarget();

        // Load theme template ie fallback
        $render->setTemplateName('legacy_admin_block_comments.html');

        $render->setAttribute('legacy_module', 'legacy');

        $render->setAttribute('contents', $contents);

        $render->setAttribute('blockid', $this->getName());

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


