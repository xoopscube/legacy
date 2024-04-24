<?php
/**
 * Comment new
 * @package    XCL
 * @subpackage comment
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2008/03/14
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

require_once XOOPS_MODULE_PATH . '/legacy/forms/CommentEditForm.class.php';

//
// Load message resource
//
$t_root =& XCube_Root::getSingleton();

$t_root->mLanguageManager->loadModuleMessageCatalog('legacy');

require_once XOOPS_ROOT_PATH.'/include/comment_constants.php';

if ('system' != $xoopsModule->getVar('dirname') && XOOPS_COMMENT_APPROVENONE == $xoopsModuleConfig['com_rule'])
{
    exit();
}

$t_root->mLanguageManager->loadPageTypeMessageCatalog('comment');    ///< Is this must?

$com_itemid = isset($_GET['com_itemid']) ? (int)$_GET['com_itemid'] : 0;

if ($com_itemid > 0)
{
    include XOOPS_ROOT_PATH.'/header.php';

    if (isset($com_replytitle))
    {
        if (isset($com_replytext)) {
            themecenterposts($com_replytitle, $com_replytext);
        }
        $myts =& MyTextSanitizer::sGetInstance();
        $com_title = $myts->htmlSpecialChars($com_replytitle);
        if (!preg_match('/^re:/i', $com_title)) {
            $com_title = 'Re: ' . xoops_substr($com_title, 0, 56);
        }
    } else {
        $com_title = '';
    }

    $com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';

    if ('' == $com_mode)
    {
        if (is_object($xoopsUser)) {
            $com_mode = $xoopsUser->getVar('umode');
        } else {
            $com_mode = $xoopsConfig['com_mode'];
        }
    }

    if (!isset($_GET['com_order']))
    {
        if (is_object($xoopsUser)) {
            $com_order = $xoopsUser->getVar('uorder');
        } else {
            $com_order = $xoopsConfig['com_order'];
        }
    } else {
        $com_order = (int)$_GET['com_order'];
    }

    $noname = 0;

    $handler =& xoops_gethandler('comment');
    $comment =& $handler->create();

    //
    // Initialize manually.
    //
    $comment->set('com_itemid', $com_itemid);
    $comment->set('com_modid', $xoopsModule->get('mid'));
    $comment->set('com_title', $com_title);

    if (is_object($xoopsUser)) {
        $comment->set('uid', $xoopsUser->get('uid'));
    } else {
        $comment->set('uid', 0);
    }

    //
    // Create action form instance and load from a comment object.
    //
    if (is_object($xoopsUser) && $xoopsUser->isAdmin())
    {
        $actionForm =new Legacy_CommentEditForm_Admin();
    } else {
        $actionForm =new Legacy_CommentEditForm();
    }

    $actionForm->prepare();
    $actionForm->load($comment);

    //
    // Get the icons of subject.
    //
    $handler =& xoops_gethandler('subjecticon');
    $subjectIcons =& $handler->getObjects();

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

    $extraParams = [];
    if ('system' !== $xoopsModule->get('dirname'))
    {
        $comment_config = $xoopsModule->getInfo('comments');
        if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
            foreach ($comment_config['extraParams'] as $extra_param) {
                $extraParams[$extra_param] = xoops_getrequest($extra_param);
            }
        }
    }

    $renderTarget->setAttribute('extraParams', $extraParams);

    //
    // Rendering
    //
    $renderSystem->render($renderTarget);

    //
    // Display now.
    //
    print $renderTarget->getResult();

        require_once XOOPS_ROOT_PATH . '/footer.php';
}
