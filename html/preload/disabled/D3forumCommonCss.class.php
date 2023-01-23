<?php
//================================================================
// CommonCss Preload
// by  photosite, https://www.photositelinks.com/
//================================================================
// modified by naao for d3forum comment integrated pagenavi style

if (!defined('XOOPS_ROOT_PATH')) exit();

class D3forumCommonCss extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('XoopsTpl.New', [&$this, 'D3forumCommonCssfile']);
    }

    public function D3forumCommonCssfile(&$xoopsTpl)
    {
        $filename = XOOPS_URL . '/common/css/d3forum_common.css';
        $filepath = XOOPS_ROOT_PATH . '/common/css/d3forum_common.css';
        if (file_exists($filepath)) {
            $xoops_module_header = '<link rel="stylesheet" type="text/css" media="screen" href="' . $filename . '" />';
            $xoopsTpl->assign('xoops_module_header', $xoops_module_header . "\n" . $xoopsTpl->get_template_vars('xoops_module_header'));
        }
    }
}
