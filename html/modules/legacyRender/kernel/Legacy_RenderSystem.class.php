<?php
/**
 * @version $Id: Legacy_RenderSystem.class.php,v 1.4 2008/08/26 15:58:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH.'/modules/legacyRender/kernel/Legacy_RenderTarget.class.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';

/**
 * If a module handling banners can not work perfectly in your site, change the following
 * "false" to "true". (For Bug#1786123)
 */
define('LEGACY_RENDERSYSTEM_BANNERSETUP_BEFORE', false);

/**
 * @brief The sub-class for Legacy_RenderSystem.
 *
 * Because XoopsTpl class may be used without Cube's boot, this is declared.
 */
class Legacy_XoopsTpl extends XoopsTpl
{
    /**
     * @private
     * If variables having the following key are assigned, converts value with
     * htmlspecialchars_decode, and set it to the context for compatibility.
     */
    public $_mContextReserve = [];

//    public function Legacy_XoopsTpl()
//    {
//        $this->__construct();
//    }

    public function __construct()
    {
        $this->_mContextReserve = ['xoops_pagetitle' => 'legacy_pagetitle'];
        parent::__construct();
    }
    public function assign($tpl_var, $value = null, $dummy = null)
//    public function assign($tpl_var, $value = null)
    {
        if (is_array($tpl_var)) {
            $root =& XCube_Root::getSingleton();
            $context = $root->mContext;
            $reserve = $this->_mContextReserve;
            foreach ($tpl_var as $key => $val) {
                if ('' !== $key) {
                    if (isset($reserve[$key])) {
                        $context->setAttribute($reserve[$key], htmlspecialchars_decode($val));
                    }
                    //$this->_tpl_vars[$key] = $val;
// smarty3
parent::assign($key, $val);
                }
            }
        } else {
            if ($tpl_var) {
                if (isset($this->_mContextReserve[$tpl_var])) {
                    $root =& XCube_Root::getSingleton();
                    $root->mContext->setAttribute($this->_mContextReserve[$tpl_var], htmlspecialchars_decode($value));
                }
                // $this->_tpl_vars[$tpl_var] = $value;
// smarty3
parent::assign($tpl_var, $value);
            }
        }
    }

    //public function assign_by_ref($tpl_var, &$value)
//smarty3
public function assignByRef($tpl_var, &$value, $nocache = false)
    {
        if ('' !== $tpl_var) {
            if (isset($this->_mContextReserve[$tpl_var])) {
                $root =& XCube_Root::getSingleton();
                $root->mContext->setAttribute($this->_mContextReserve[$tpl_var], htmlspecialchars_decode($value));
            }
            //$this->_tpl_vars[$tpl_var] =& $value;
// smarty3
parent::assign($tpl_var, $value);
        }
    }

    public function &get_template_vars($name = null)
    {
        $root =& XCube_Root::getSingleton();
        if (!isset($name)) {
            foreach ($this->_mContextReserve as $t_key => $t_value) {
                if (isset($this->_mContextReserve[$t_value])) {
                    //$this->_tpl_vars[$t_key] = htmlspecialchars($root->mContext->getAttribute($this->_mContextReserve[$t_value]), ENT_QUOTES);
//smarty3
  $this->global_tpl_vars[$t_key] = htmlspecialchars($root->mContext->getAttribute($this->_mContextReserve[$t_value]), ENT_QUOTES);
                                
}
            }
            $value =& parent::get_template_vars($name);
        } elseif (isset($this->_mContextReserve[$name])) {
            $value = htmlspecialchars($root->mContext->getAttribute($this->_mContextReserve[$name]), ENT_QUOTES);
        } else {
          //  $value =& parent::get_template_vars($name);
// smarty3
$value =& parent::getTemplateVars($name);
        }
        return $value;
    }
}
// @TODO test version 2.4.0
//require_once XOOPS_ROOT_PATH . '/core/XCube_Theme.class.php';
/**
 * Compatible render system with XOOPS 2 Themes & Templates.
 *
 * @brief This allows you to directly manage the theme and the main rendering target.
 * And, this implements the variable-sharing-mechanism using Smarty Template Engine.
 */
class Legacy_RenderSystem extends XCube_RenderSystem
{
    public $mXoopsTpl;

    /**
     * Temporary
     */
    public $mThemeRenderTarget;

    /**
     * Temporary
     */
    public $mMainRenderTarget;

    public $_mContentsData = null;

    /**
     * @type XCube_Delegate
     */
    public $mSetupXoopsTpl = null;

    /**
     * @private
     */
    public $_mIsActiveBanner = false;

    public $mBeginRender = null;

    public function Legacy_RenderSystem()
    {
        $this->__construct();
    }

    public function __construct()
    {
        parent::__construct();

        $this->mSetupXoopsTpl =new XCube_Delegate();
        $this->mSetupXoopsTpl->register('Legacy_RenderSystem.SetupXoopsTpl');

        $this->mBeginRender =new XCube_Delegate();
        $this->mBeginRender->register('Legacy_RenderSystem.BeginRender');
    }

    public function prepare(&$controller)
    {
        parent::prepare($controller);

        $root =& $this->mController->mRoot;
        $context =& $root->getContext();
        $textFilter =& $root->getTextFilter();

        // Legacy default setup XoopsTpl
        if (isset($GLOBALS['xoopsTpl'])) {
            $this->mXoopsTpl =& $GLOBALS['xoopsTpl'];
        } else {
            $this->mXoopsTpl =new Legacy_XoopsTpl();
        }
        $mTpl =& $this->mXoopsTpl;
//        $mTpl->register_function('legacy_notifications_select', 'LegacyRender_smartyfunction_notifications_select');
// smarty3
$mTpl->registerPlugin('function', 'legacy_notifications_select', 'LegacyRender_smartyfunction_notifications_select');
        
        $this->mSetupXoopsTpl->call(new XCube_Ref($mTpl));

        // Legacy compatibility
        $GLOBALS['xoopsTpl'] =& $mTpl;

        $mTpl->xoops_setCaching(0);

        // If debugger request debugging, send debug mode signal by any methods.
        if ($controller->mDebugger->isDebugRenderSystem()) {
            $mTpl->xoops_setDebugging(true);
        }

        $mTpl->assign(
            [
            'xoops_requesturi' => htmlspecialchars($GLOBALS['xoopsRequestUri'], ENT_QUOTES),    //@todo ?????????????
            //@todo set JavaScript/Weird, but need extra <script> tags for Xoops Legacy 2.x themes
            'xoops_js' => '//--></script><script type="text/javascript" src="'.XOOPS_URL.'/include/xoops.js"></script><script type="text/javascript"><!--'
            ]
        );

        if (($xoopsRedirect = xoops_getrequest('xoops_redirect')) && '/' === $xoopsRedirect[0]) {
            $mTpl->assign('xoops_redirect', htmlspecialchars($xoopsRedirect, ENT_QUOTES));
        }

        $mTpl->assign('xoops_sitename', $textFilter->toShow($context->getAttribute('legacy_sitename')));
        $mTpl->assign('xoops_pagetitle', $textFilter->toShow($context->getAttribute('legacy_pagetitle')));
        $mTpl->assign('xoops_slogan', $textFilter->toShow($context->getAttribute('legacy_slogan')));

        // --------------------------------------
        // Meta tags
        // --------------------------------------
        $moduleHandler = xoops_gethandler('module');
        $legacyRender =& $moduleHandler->getByDirname('legacyRender');

        if (is_object($legacyRender)) {
            $configHandler = xoops_gethandler('config');
            $configs =& $configHandler->getConfigsByCat(0, $legacyRender->get('mid'));

            //
            // If this site has the setting of banner.
            // TODO this process depends on XOOPS 2.x Legacy.
            //
            $this->_mIsActiveBanner = $configs['banners'];
            if (LEGACY_RENDERSYSTEM_BANNERSETUP_BEFORE == true) {
                if (1 == $configs['banners']) {
                    $mTpl->assign('xoops_banner', xoops_getbanner());
                } else {
                    $mTpl->assign('xoops_banner', '&nbsp;');
                }
            }
        } else {
            $mTpl->assign('xoops_banner', '&nbsp;');
        }

        // --------------------------------------
        // Add User
        // --------------------------------------
        $arr = null;
        if (is_object($context->mXoopsUser)) {
            $arr = [
                'xoops_isuser' => true,
                'xoops_userid' => $context->mXoopsUser->getVar('uid', 'n'),
                'xoops_uname' => $context->mXoopsUser->getVar('uname')
            ];
        } else {
            $arr = [
                'xoops_isuser' => false
            ];
        }

        $mTpl->assign($arr);
    }

    public function setAttribute($key, $value)
    {
        $this->mRenderTarget->setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        $this->mRenderTarget->getAttribute($key);
    }

    /**
     * @protected
     * Assign common variables for Legacy compatibility.
     */
    public function _commonPrepareRender()
    {
        $root =& $this->mController->mRoot;
        $context =& $root->getContext();
        $textFilter =& $root->getTextFilter();
        // @gigamaster themename
        $themeName = $context->getThemeName();
        $vars = [
            'xoops_theme'     =>$themeName,
            'xoops_imageurl'  =>XOOPS_THEME_URL . "/${themeName}/",
            'xoops_themecss'  =>xoops_getcss($themeName),
            'xoops_sitename'  =>$textFilter->toShow($context->getAttribute('legacy_sitename')),
            'xoops_pagetitle' =>$textFilter->toShow($context->getAttribute('legacy_pagetitle')),
            'xoops_slogan'    =>$textFilter->toShow($context->getAttribute('legacy_slogan'))
        ];

        //
        // Assign module information.
        //
        if (null !== $context->mModule) {    // The process of module
            $xoopsModule =& $context->mXoopsModule;
            $vars['xoops_modulename'] = $xoopsModule->getVar('name');
            $vars['xoops_dirname'] = $xoopsModule->getVar('dirname');
        }

        if (isset($GLOBALS['xoopsUserIsAdmin'])) {
            $vars['xoops_isadmin']=$GLOBALS['xoopsUserIsAdmin'];
        }
        $this->mXoopsTpl->assign($vars);
    }

    public function renderBlock(&$target)
    {
        $this->_commonPrepareRender();

        //
        // Temporary
        //
        $mTpl = $this->mXoopsTpl;
        $mTpl->xoops_setCaching(0);

        $vars = $target->getAttributes();
        $mTpl->assign($vars);

        $this->mBeginRender->call(new XCube_Ref($mTpl));
        $result=&$mTpl->fetchBlock($target->getTemplateName(), $target->getAttribute('bid'));
        $target->setResult($result);

        //
        // Reset
        //
//        $mTpl->clear_assign(array_keys($vars));
// smarty3
$mTpl->clearAssign(array_keys($vars));
    }

    public function _render(&$target)
    {
        foreach ($target->getAttributes() as $key=>$value) {
            $this->mXoopsTpl->assign($key, $value);
        }

        $this->mBeginRender->call(new XCube_Ref($this->mXoopsTpl), $target->getAttribute('legacy_buffertype'));
        $result=$this->mXoopsTpl->fetch('db:'.$target->getTemplateName());
        $target->setResult($result);

        foreach ($target->getAttributes() as $key => $value) {
            $this->mXoopsTpl->clear_assign($key);
        }
    }

    public function render(&$target)
    {
        //
        // The following lines are temporary until we will finish changing the style!
        //
        switch ($target->getAttribute('legacy_buffertype')) {
            case XCUBE_RENDER_TARGET_TYPE_BLOCK:
                $this->renderBlock($target);
                break;

            case XCUBE_RENDER_TARGET_TYPE_MAIN:
                $this->renderMain($target);
                break;

            case XCUBE_RENDER_TARGET_TYPE_THEME:
                $this->renderTheme($target);
                break;

            case XCUBE_RENDER_TARGET_TYPE_BUFFER:
            default:
                break;
        }
    }

    public function renderMain(&$target)
    {
        $this->_commonPrepareRender();

        // TODO refactor using null coalescing operator in PHP 7
//$cachedTemplateId = isset($GLOBLAS['xoopsCachedTemplateId']) ? $GLOBLAS['xoopsCachedTemplateId'] : null;
        $cachedTemplateId = $GLOBLAS['xoopsCachedTemplateId'] ?? null;

        foreach ($target->getAttributes() as $key=>$value) {
            $this->mXoopsTpl->assign($key, $value);
        }

        if ($target->getTemplateName()) {
            if (null !== $cachedTemplateId) {
                $contents=$this->mXoopsTpl->fetch('db:'.$target->getTemplateName(), $xoopsCachedTemplateId);
            } else {
                $contents=$this->mXoopsTpl->fetch('db:'.$target->getTemplateName());
            }
        } else if (null !== $cachedTemplateId) {
            $this->mXoopsTpl->assign('dummy_content', $target->getAttribute('stdout_buffer'));
            $contents=$this->mXoopsTpl->fetch($GLOBALS['xoopsCachedTemplate'], $xoopsCachedTemplateId);
        } else {
            $contents=$target->getAttribute('stdout_buffer');
        }

        $target->setResult($contents);
    }

    public function renderTheme(&$target)
    {
        $this->_commonPrepareRender();

        //jQuery Ready functions
        $mRoot = $this->mController->mRoot;
        $mContext = $mRoot->mContext;
        XCube_DelegateUtils::call('Site.JQuery.AddFunction', new XCube_Ref($mContext->mAttributes['headerScript']));
        $headerScript = $mContext->getAttribute('headerScript');
        $mTpl = $this->mXoopsTpl;
        $moduleHeader = $mTpl->get_template_vars('xoops_module_header');
        $moduleHeader =  $headerScript->createLibraryTag() . $moduleHeader . $headerScript->createOnloadFunctionTag();

        //
        // Assign from attributes of the render-target.
        //
        $vars = $target->getAttributes();
        $vars['xoops_module_header'] = $moduleHeader;

        $moduleHandler = xoops_gethandler('module');
        $legacyRender =& $moduleHandler->getByDirname('legacyRender');
        $configHandler = xoops_gethandler('config');
        $configs =& $configHandler->getConfigsByCat(0, $legacyRender->get('mid'));

        $textFilter =& $mRoot->getTextFilter();
        $headerScript = $mContext->getAttribute('headerScript');
        $vars['xoops_meta_keywords'] = $textFilter->toShow($headerScript->getMeta('keywords') ?: $configs['meta_keywords']);
        $vars['xoops_meta_description'] = $textFilter->toShow($headerScript->getMeta('description') ?: $configs['meta_description']);
        $vars['xoops_meta_robots'] = $textFilter->toShow($headerScript->getMeta('robots') ?: $configs['meta_robots']);
        $vars['xoops_meta_rating'] = $textFilter->toShow($headerScript->getMeta('rating') ?: $configs['meta_rating']);
        $vars['xoops_meta_author'] = $textFilter->toShow($headerScript->getMeta('author') ?: $configs['meta_author']);
        $vars['xoops_meta_copyright'] = $textFilter->toShow($headerScript->getMeta('copyright') ?: $configs['meta_copyright']);
        // Extra Meta Webmaster Tools
        $vars['xoops_meta_bing'] = $textFilter->toShow($headerScript->getMeta('msvalidate.01') ?: $configs['meta_bing']);
        $vars['xoops_meta_google'] = $textFilter->toShow($headerScript->getMeta('google-site-verification') ?: $configs['meta_google']);
        $vars['xoops_meta_yandex'] = $textFilter->toShow($headerScript->getMeta('yandex-verification') ?: $configs['meta_yandex']);
        // Extra Meta App ID
        $vars['xoops_meta_fb_app'] = $textFilter->toShow($headerScript->getMeta('fb:app_id') ?: $configs['meta_fb_app']);
        $vars['xoops_meta_twitter_site'] = $textFilter->toShow($headerScript->getMeta('twitter:site') ?: $configs['meta_twitter_site']);
        // footer may be raw HTML text.
        $vars['xoops_footer'] = $configs['footer'];

        //
        // Banner Management Settings
        // TODO this process depends on XOOPS Legacy.
        //
        if (LEGACY_RENDERSYSTEM_BANNERSETUP_BEFORE == false) {
            $vars['xoops_banner'] = (1 == $this->_mIsActiveBanner)?xoops_getbanner():'&nbsp;';
        }

        $mTpl->assign($vars);

        //
        // [TODO]
        // We must implement with a render-target.
        //
        // $this->_processLegacyTemplate();

        // assign
        /// @todo I must move these to somewhere.
        $assignNameMap = [
            XOOPS_SIDEBLOCK_LEFT=> ['showflag' =>'xoops_showlblock', 'block' =>'xoops_lblocks'],
            XOOPS_CENTERBLOCK_LEFT=> ['showflag' =>'xoops_showcblock', 'block' =>'xoops_clblocks'],
            XOOPS_CENTERBLOCK_RIGHT=> ['showflag' =>'xoops_showcblock', 'block' =>'xoops_crblocks'],
            XOOPS_CENTERBLOCK_CENTER=> ['showflag' =>'xoops_showcblock', 'block' =>'xoops_ccblocks'],
            XOOPS_SIDEBLOCK_RIGHT=> ['showflag' =>'xoops_showrblock', 'block' =>'xoops_rblocks']
        ];

        foreach ($assignNameMap as $key=>$val) {
            $mTpl->assign($val['showflag'], $this->_getBlockShowFlag($val['showflag']));
            if (isset($mContext->mAttributes['legacy_BlockContents'][$key])) {
                foreach ($mContext->mAttributes['legacy_BlockContents'][$key] as $result) {
                    $mTpl->append($val['block'], $result);
                }
            }
        }

        $this->mBeginRender->call(new XCube_Ref($mTpl));

        //
        // Render result, and set it to the RenderBuffer of the $target.
        //
        $result=null;
        if ($target->getAttribute('isFileTheme')) {
            $result=$mTpl->fetch($target->getTemplateName().'/theme.html');
        } else {
            $result=$mTpl->fetch('db:'.$target->getTemplateName());
        }

        $result .= $mTpl->fetchDebugConsole();

        $target->setResult($result);
    }
    /**
     * Block Show Flag (i.e. block management)
     * @param $area
     * @return int
     */
    public function _getBlockShowFlag($area)
    {
        switch ($area) {
            case 'xoops_showrblock' :
                if (isset($GLOBALS['show_rblock']) && empty($GLOBALS['show_rblock'])) {
                    return 0;
                }
                return (!empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_SIDEBLOCK_RIGHT])) ? 1 : 0;
                break;
            case 'xoops_showlblock' :
                if (isset($GLOBALS['show_lblock']) && empty($GLOBALS['show_lblock'])) {
                    return 0;
                }
                return (!empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_SIDEBLOCK_LEFT])) ? 1 : 0;
                break;
            case 'xoops_showcblock' :
                if (isset($GLOBALS['show_cblock']) && empty($GLOBALS['show_cblock'])) {
                    return 0;
                }
                return (!empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_CENTERBLOCK_LEFT])||
                        !empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_CENTERBLOCK_RIGHT])||
                        !empty($this->mController->mRoot->mContext->mAttributes['legacy_BlockShowFlags'][XOOPS_CENTERBLOCK_CENTER])) ? 1 : 0;
                break;
            default :
                return 0;
        }
    }

    //
    // TODO : These deprecated functions should not be here !
    //

    /**
     * @deprecated
     */
    public function sendHeader()
    {
        header('Content-Type:text/html; charset='._CHARSET);
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }

    /**
     * @param bool $closeHead
     * @deprecated
     */
    public function showXoopsHeader($closeHead=true)
    {
        global $xoopsConfig;
        $myts =& MyTextSanitizer::sGetInstance();
//if (1 == $xoopsConfig['gzip_compression']) {
        if (1 === $xoopsConfig['gzip_compression']) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }

        $this->sendHeader();
        $this->_renderHeader($closeHead);
    }

    // TODO never output directly

    /**
     * @param bool $closehead
     * @deprecated
     */
    public function _renderHeader($closehead=true)
    {
        global $xoopsConfig, $xoopsTheme, $xoopsConfigMetaFooter;

        echo '<!DOCTYPE html>';

        echo '<html lang="<{$xoops_langcode}>">
		<head>
		<meta http-equiv="content-type" content="text/html; charset='._CHARSET.'">
		<meta http-equiv="content-language" content="'._LANGCODE.'">
		<meta name="robots" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_robots']).'">
		<meta name="keywords" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_keywords']).'">
		<meta name="description" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_desc']).'">
		<meta name="rating" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_rating']).'">
		<meta name="author" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_author']).'">
		<meta name="copyright" content="'.htmlspecialchars($xoopsConfigMetaFooter['meta_copyright']).'">
		<meta name="generator" content="XOOPSCube">
		<title>'.htmlspecialchars($xoopsConfig['sitename']).'</title>
		<script type="text/javascript" src="'.XOOPS_URL.'/include/xoops.js"></script>
		';
        $themecss = getcss($xoopsConfig['theme_set']);
        echo '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/theme/xcl_default/style.css">';
        if ($themecss) {
            echo '<link rel="stylesheet" type="text/css" media="all" href="'.$themecss.'">';
            //echo '<style type="text/css" media="all"><!-- @import url('.$themecss.'); --></style>';
        }
        if ($closehead) {
            echo '</head><body>';
        }
    }

    /**
     * @deprecated
     */
    public function _renderFooter()
    {
        echo '</body></html>';
        ob_end_flush();
    }

    /**
     * @deprecated
     */
    public function showXoopsFooter()
    {
        $this->_renderFooter();
    }

    public function &createRenderTarget($type = LEGACY_RENDER_TARGET_TYPE_MAIN, $option = null)
    {
        $renderTarget = null;
        switch ($type) {
            case XCUBE_RENDER_TARGET_TYPE_MAIN:
                $renderTarget =new Legacy_RenderTargetMain();
                break;

            case LEGACY_RENDER_TARGET_TYPE_BLOCK:
                $renderTarget =new XCube_RenderTarget();
                $renderTarget->setAttribute('legacy_buffertype', LEGACY_RENDER_TARGET_TYPE_BLOCK);
                break;

            default:
                $renderTarget =new XCube_RenderTarget();
                break;
        }

        return $renderTarget;
    }

    /**
     * @TODO This function is not cool!
     * @param bool $isDialog default  = false
     * @return Legacy_DialogRenderTarget|Legacy_ThemeRenderTarget
     */
//    public function &getThemeRenderTarget($isDialog)
public function &getThemeRenderTarget($isDialog = false)
    {
        $screenTarget = $isDialog ? new Legacy_DialogRenderTarget() : new Legacy_ThemeRenderTarget();
        return $screenTarget;
    }
}

/**
 * Notifications create render target main
 * @param $params
 * @param $smarty
 * @return mixed
 */
//function LegacyRender_smartyfunction_notifications_select($params, &$smarty)
// smarty3
function LegacyRender_smartyfunction_notifications_select($params, $smarty)
{
    $root =& XCube_Root::getSingleton();
    $renderSystem =& $root->getRenderSystem('Legacy_RenderSystem');

    $renderTarget =& $renderSystem->createRenderTarget('main');
    $renderTarget->setTemplateName('legacy_notification_select_form.html');

    XCube_DelegateUtils::call('Legacyfunction.Notifications.Select', new XCube_Ref($renderTarget));

    $renderSystem->render($renderTarget);

    return $renderTarget->getResult();
}
