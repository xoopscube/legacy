<?php
/**
 * @package legacyRender
 * HtaccessViewAction.class.php
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
//require_once XOOPS_ROOT_PATH.'/modules/legacyRender/kernel/Legacy_RenderSystem.class.php';
//require_once XOOPS_ROOT_PATH . '/class/template.php';

class LegacyRender_AdminRenderAction extends LegacyRender_Action
{
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return LEGACYRENDER_FRAME_VIEW_SUCCESS;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {

        $retArray = Legacy_Utils::checkSystemModules();
        $accessAllowFlag = false;
        $xoopsConfig = $controller->mRoot->mContext->getXoopsConfig();
        $mRoot = $controller->mRoot;
        $mContext = $mRoot->mContext;
        
        // Render System - get configurations categories
        $moduleHandler = xoops_gethandler('module');
        $legacyRender =& $moduleHandler->getByDirname('legacyRender');
        $configHandler = xoops_gethandler('config');
        $configs =& $configHandler->getConfigsByCat(0, $legacyRender->get('mid'));

        $textFilter =& $mRoot->getTextFilter();
        $headerScript = $mContext->getAttribute('headerScript');
        // Meta
        $render->setAttribute('xoops_meta_keywords',$textFilter->toShow($headerScript->getMeta('keywords') ?: $configs['meta_keywords']));
        $render->setAttribute('xoops_meta_description',$textFilter->toShow($headerScript->getMeta('description') ?: $configs['meta_description']));
        $render->setAttribute('xoops_meta_robots',$textFilter->toShow($headerScript->getMeta('robots') ?: $configs['meta_robots']));
        $render->setAttribute('xoops_meta_rating',$textFilter->toShow($headerScript->getMeta('rating') ?: $configs['meta_rating']));
        $render->setAttribute('xoops_meta_author',$textFilter->toShow($headerScript->getMeta('author') ?: $configs['meta_author']));
        $render->setAttribute('xoops_meta_copyright',$textFilter->toShow($headerScript->getMeta('copyright') ?: $configs['meta_copyright']));
        // Extra Meta Webmaster Tools
        $render->setAttribute('xoops_meta_bing',$textFilter->toShow($headerScript->getMeta('msvalidate.01') ?: $configs['meta_bing']));
        $render->setAttribute('xoops_meta_google',$textFilter->toShow($headerScript->getMeta('google-site-verification') ?: $configs['meta_google']));
        $render->setAttribute('xoops_meta_yandex',$textFilter->toShow($headerScript->getMeta('yandex-verification') ?: $configs['meta_yandex']));
        // Extra Meta App ID
        $render->setAttribute('xoops_meta_fb_app',$textFilter->toShow($headerScript->getMeta('fb:app_id') ?: $configs['meta_fb_app']));
        $render->setAttribute('xoops_meta_twitter_site',$textFilter->toShow($headerScript->getMeta('twitter:site') ?: $configs['meta_twitter_site']));
        // footer may be raw HTML text.
        $render->setAttribute('xoops_footer',$configs['footer']);


        // xcl 2.3.x
        $render->setAttribute('meta_keywords',$textFilter->toShow($headerScript->getMeta('keywords') ?: $configs['meta_keywords']));
        $render->setAttribute('meta_description',$textFilter->toShow($headerScript->getMeta('description') ?: $configs['meta_description']));
        $render->setAttribute('meta_robots',$textFilter->toShow($headerScript->getMeta('robots') ?: $configs['meta_robots']));
        $render->setAttribute('meta_rating',$textFilter->toShow($headerScript->getMeta('rating') ?: $configs['meta_rating']));
        $render->setAttribute('meta_author',$textFilter->toShow($headerScript->getMeta('author') ?: $configs['meta_author']));
        $render->setAttribute('meta_copyright',$textFilter->toShow($headerScript->getMeta('copyright') ?: $configs['meta_copyright']));
        // Extra Meta Webmaster Tools
        $render->setAttribute('meta_bing',$textFilter->toShow($headerScript->getMeta('msvalidate.01') ?: $configs['meta_bing']));
        $render->setAttribute('meta_google',$textFilter->toShow($headerScript->getMeta('google-site-verification') ?: $configs['meta_google']));
        $render->setAttribute('meta_yandex',$textFilter->toShow($headerScript->getMeta('yandex-verification') ?: $configs['meta_yandex']));
        // Extra Meta App ID
        $render->setAttribute('meta_fb_app',$textFilter->toShow($headerScript->getMeta('fb:app_id') ?: $configs['meta_fb_app']));
        $render->setAttribute('meta_twitter_site',$textFilter->toShow($headerScript->getMeta('twitter:site') ?: $configs['meta_twitter_site']));
        // Main
        $render->setAttribute('favicon',$configs['favicon']);
        $render->setAttribute('logotype',$configs['logotype']);
        // footer may be raw HTML text.
        $render->setAttribute('footer',$configs['footer']);
        $render->setAttribute('test',$configs['meta_robots']);
        $render->setAttribute('site_name',$xoopsConfig['sitename']);
        $render->setAttribute('language',$xoopsConfig['language']);
        $render->setAttribute('theme_set',$xoopsConfig['theme_set']);
        $render->setAttribute('page_title' ,$textFilter->toShow($mContext->getAttribute('legacy_pagetitle')) );
        $render->setAttribute('slogan',$textFilter->toShow($mContext->getAttribute('legacy_slogan')) );
        // XOOPS2
        $render->setAttribute('xoops_sitename', htmlspecialchars($xoopsConfig['sitename']));
        $render->setAttribute('xoops_themecss', xoops_getcss());
        $render->setAttribute('xoops_imageurl', XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/');
        // Template
        $render->setTemplateName('admin_render.html');
    }
}
