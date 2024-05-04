<?php
/**
 * BackendAction.class.php
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/***
 * @internal
 */
class Legacy_BackendAction extends Legacy_Action
{
    public $mItems = [];

    /**
     * The spec of getRSS():
     * append your RSS item to $eventArgs array. You don't need to sanitize your values. Use raw value.
     *
     *  $item['title']
     *  $item['link'] ... permanent link
     *  $item['guid'] ... permanent link
     *  $item['pubdate'] ... unixtime
     *  $item['description'] ... not required.
     *  $item['category'] ... not required.
     *  $item['author'] ... not required.
     *
     * @var XCube_Delegate
     */
    public $mGetRSSItems = null;

    public function __construct($flag)
    {
        parent::__construct($flag);

        $this->mGetRSSItems =new XCube_Delegate();
        $this->mGetRSSItems->register('Legacy_BackendAction.GetRSSItems');
    }

    public function getDefaultView(&$controll, &$xoopsUser)
    {
        $items = [];
        $this->mGetRSSItems->call(new XCube_Ref($items));

        $sortArr = [];
        foreach ($items as $item) {
            $i = (int)$item['pubdate'];
            for (; isset($sortArr[$i]) ; $i++);

            $sortArr[$i] = $item;
        }
        krsort($sortArr);
        $this->mItems = $sortArr;
        return LEGACY_FRAME_VIEW_INDEX;
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $xoopsConfig = $controller->mRoot->mContext->mXoopsConfig;

        //
        // Set up the render buffer.
        //
        $renderSystem =& $controller->mRoot->getRenderSystem('Legacy_RenderSystem');

        $renderTarget =& $renderSystem->createRenderTarget('main');

        $renderTarget->setTemplateName('legacy_rss.html');

        $renderTarget->setAttribute('channel_title', $xoopsConfig['sitename']);
        $renderTarget->setAttribute('channel_link', XOOPS_URL . '/');
        $renderTarget->setAttribute('channel_desc', $xoopsConfig['slogan']);
        $renderTarget->setAttribute('channel_lastbuild', formatTimestamp(time(), 'rss'));
        $renderTarget->setAttribute('channel_webmaster', $xoopsConfig['adminmail']);
        $renderTarget->setAttribute('channel_editor', $xoopsConfig['adminmail']);
        $renderTarget->setAttribute('channel_category', 'News');
        $renderTarget->setAttribute('channel_generator', 'XOOPSCube');
        $renderTarget->setAttribute('image_url', XOOPS_URL . '/images/logo.png'); /* Image must be in the GIF, JPEG or PNG formats.*/

        $dimention = getimagesize(XOOPS_ROOT_PATH . '/images/logo.png');

        $width = 0;
        if (empty($dimention[0])) {
            $width = 88;
        } else {
            $width = ($dimention[0] > 144) ? 144 : $dimention[0];
        }

        $height = 0;
        if (empty($dimention[1])) {
            $height = 31;
        } else {
            $height = ($dimention[1] > 400) ? 400 : $dimention[1];
        }

        $renderTarget->setAttribute('image_width', $width);
        $renderTarget->setAttribute('image_height', $height);
        $renderTarget->setAttribute('items', $this->mItems);

        //
        // Rendering
        //
        $renderSystem->render($renderTarget);

        if (function_exists('mb_http_output')) {
            mb_http_output('pass');
        }
        header('Content-Type:text/xml; charset=utf-8');


        print xoops_utf8_encode($renderTarget->getResult());

        exit(0);
    }
}
