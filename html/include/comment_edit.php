<?php
/**
 * Comment delete
 * Comment edit
 * @package    XCL
 * @subpackage comment
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

//
// Guard directly access.
//
if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
    exit();
}
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';

require_once XOOPS_MODULE_PATH . '/legacy/forms/CommentEditForm.class.php';

//
// Load message resource
//
$t_root =& XCube_Root::getSingleton();

$langManager =& $t_root->getLanguageManager();
$langManager->loadModuleMessageCatalog('legacy');

if ('system' !== $xoopsModule->getVar('dirname') && XOOPS_COMMENT_APPROVENONE === $xoopsModuleConfig['com_rule']) {
    exit();
}

$t_root->mLanguageManager->loadPageTypeMessageCatalog('comment');

$com_id = isset($_GET['com_id']) ? (int)$_GET['com_id'] : 0;
$com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';
if ('' === $com_mode) {
    if (is_object($xoopsUser)) {
        $com_mode = $xoopsUser->getVar('umode');
    } else {
        $com_mode = $xoopsConfig['com_mode'];
    }
}
if (!isset($_GET['com_order'])) {
    if (is_object($xoopsUser)) {
        $com_order = $xoopsUser->getVar('uorder');
    } else {
        $com_order = $xoopsConfig['com_order'];
    }
} else {
    $com_order = (int)$_GET['com_order'];
}
$comment_handler =& xoops_gethandler('comment');
$comment =& $comment_handler->get($com_id);
$dohtml = $comment->getVar('dohtml');
$dosmiley = $comment->getVar('dosmiley');
$dobr = $comment->getVar('dobr');
$doxcode = $comment->getVar('doxcode');
$com_icon = $comment->getVar('com_icon');
$com_itemid = $comment->getVar('com_itemid');
$com_title = $comment->getVar('com_title', 'E');
$com_text = $comment->getVar('com_text', 'E');
$com_pid = $comment->getVar('com_pid');
$com_status = $comment->getVar('com_status');
$com_rootid = $comment->getVar('com_rootid');

//
// Get the icons of subject.
//
$handler =& xoops_gethandler('subjecticon');
$subjectIcons =& $handler->getObjects();

if ('system' !== $xoopsModule->getVar('dirname')) {
    if (is_object($xoopsUser) && $xoopsUser->isAdmin()) {
        $actionForm =new Legacy_CommentEditForm_Admin();
    } else {
        $actionForm =new Legacy_CommentEditForm();
    }
    $actionForm->prepare();
    $actionForm->load($comment);

    //
    // Render comment-form to render buffer using Legacy_RenderSystem.
    //
    $renderSystem =& $t_root->getRenderSystem($t_root->mContext->mBaseRenderSystemName);
    $renderTarget =& $renderSystem->createRenderTarget('main');

    $renderTarget->setTemplateName('legacy_comment_edit.html');

    $renderTarget->setAttribute('actionForm', $actionForm);
    $renderTarget->setAttribute('subjectIcons', $subjectIcons);
    $renderTarget->setAttribute('xoopsModuleConfig', $xoopsModuleConfig);
    $renderTarget->setAttribute('com_order', $com_order);

    //
    // Rendering
    //
    $renderSystem->render($renderTarget);

    //
    // Display now.
    //
    print $renderTarget->getResult();

    require_once XOOPS_ROOT_PATH.'/footer.php';

    } else {
        //
        // TODO
        //
        xoops_cp_header();
        require_once XOOPS_ROOT_PATH.'/include/comment_form.php';
        xoops_cp_footer();
    }
