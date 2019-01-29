<?php
/**
 * @package Legacy
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

//
// TODO
//
require_once XOOPS_ROOT_PATH . '/modules/legacyRender/kernel/Legacy_RenderSystem.class.php';

define('LEGACY_ADMIN_RENDER_TEMPLATE_DIRNAME', 'templates');

define('LEGACY_ADMIN_RENDER_FALLBACK_PATH', XOOPS_MODULE_PATH . '/legacy/admin/theme');
define('LEGACY_ADMIN_RENDER_FALLBACK_URL', XOOPS_MODULE_URL . '/legacy/admin/theme');

require_once SMARTY_DIR.'/Smarty.class.php';

/**
 * @internal
 * @public
 * @brief The special extended smarty class for Legacy_AdminRenderSystem.
 * This class extends Smarty to mediate the collision compiled file name.
 */
class Legacy_AdminSmarty extends Smarty
{
    public $mModulePrefix = null;

    //
    // If you don't hope to override for theme, set false.
    //
    public $overrideMode = true;
    
    public function Legacy_AdminSmarty()
    {
        self::__construct();
    }

    public function __construct()
    {
        parent::Smarty();

        $this->compile_id = XOOPS_URL;
        $this->_canUpdateFromFile = true;
        $this->compile_check = true;
        $this->compile_dir = XOOPS_COMPILE_PATH;
        $this->left_delimiter = '<{';
        $this->right_delimiter = '}>';
        $this->plugins_dir = array(SMARTY_DIR.'plugins', XOOPS_ROOT_PATH.'/class/smarty/plugins');

        //
        // [TODO]
        //	If we don't set true to the following flag, a user can not recover
        // with deleting additional theme. But, a user should to select true or
        // false by site_custom.ini.php.
        //
        $this->force_compile = false;
    }
    
    public function setModulePrefix($prefix)
    {
        $this->mModulePrefix = $prefix;
    }
    
    public function _get_auto_filename($autoBase, $autoSource = null, $auotId = null)
    {
        $autoSource = $this->mModulePrefix . '_admin_' . $autoSource;
        return parent::_get_auto_filename($autoBase, $autoSource, $auotId);
    }

    public function _fetch_resource_info(&$params)
    {
        $_return = false;

        $root =& XCube_Root::getSingleton();
        $theme = $root->mSiteConfig['Legacy']['Theme'];
        $dirname = $this->mModulePrefix;
        
        if ($dirname != null) {
            $params['resource_base_path'] = XOOPS_THEME_PATH . '/' . $theme . '/modules/' . $dirname;
            $params['quiet'] = true;
            
            $_return = parent::_fetch_resource_info($params);
        }

        if (!$_return) {
            unset($params['resource_base_path']);
            $params['quiet'] = false;
            
            $_return = parent::_fetch_resource_info($params);
        }
        
        return $_return;
    }
}

/**
 * @brief The specific FILE-TYPE render-system.
 * @todo We depends on Legacy_RenderSystem that a add-in module defines. We must stop this situation.
 */
class Legacy_AdminRenderSystem extends Legacy_RenderSystem
{
    public $mController;
    public $mSmarty;
    
    /**
     * This is the buffer which stores standard output when the render-target
     * in renderMain() doesn't use a template.
     * 
     * @access private
     * @var string
     */
    public $_mStdoutBuffer = null;
    
    public function prepare(&$controller)
    {
        $this->mController =& $controller;
        
        $this->mSmarty =new Legacy_AdminSmarty();
        $this->mSmarty->register_modifier('theme', 'Legacy_modifier_theme');
        $this->mSmarty->register_function('stylesheet', 'Legacy_function_stylesheet');

        $this->mSmarty->assign(array(
            'xoops_url'       => XOOPS_URL,
            'xoops_rootpath'   => XOOPS_ROOT_PATH,
            'xoops_langcode'   => _LANGCODE,
            'xoops_charset'    => _CHARSET,
            'xoops_version'    => XOOPS_VERSION,
            'xoops_upload_url' => XOOPS_UPLOAD_URL)
        );

        if ($controller->mRoot->mSiteConfig['Legacy_AdminRenderSystem']['ThemeDevelopmentMode'] == true) {
            $this->mSmarty->force_compile = true;
        }
    }
    
    public function renderBlock(&$target)
    {
        $this->mSmarty->template_dir = XOOPS_ROOT_PATH . '/modules/legacy/admin/templates';

        foreach ($target->getAttributes() as $key => $value) {
            $this->mSmarty->assign($key, $value);
        }
        
        $this->mSmarty->setModulePrefix($target->getAttribute('legacy_module'));
        $result = $this->mSmarty->fetch('blocks/' . $target->getTemplateName());
        $target->setResult($result);

        //
        // Reset
        //
        foreach ($target->getAttributes() as $key => $value) {
            $this->mSmarty->clear_assign($key);
        }
    }
    
    public function renderTheme(&$target)
    {
        //
        // Assign from attributes of the render-target.
        //
        $smarty = $this->mSmarty;
        $vars = array('stdout_buffer'=>$this->_mStdoutBuffer);
        foreach ($target->getAttributes() as $key=>$value) {
            $vars[$key] = $value;
        }

        //jQuery Ready functions
        $context = $this->mController->mRoot->mContext;
        XCube_DelegateUtils::call('Site.JQuery.AddFunction', new XCube_Ref($context->mAttributes['headerScript']));
        $headerScript = $context->getAttribute('headerScript');
        $moduleHeader =  $headerScript->createLibraryTag() . $headerScript->createOnloadFunctionTag();
        $vars['xoops_module_header'] = $moduleHeader;
    
        //
        // Get a virtual current module object from the controller and assign it.
        //
        $moduleObject =& $this->mController->getVirtualCurrentModule();
        $vars['currentModule'] = $moduleObject;

        //
        // Other attributes
        //
        $vars['legacy_sitename'] = $context->getAttribute('legacy_sitename');
        $vars['legacy_pagetitle'] = $context->getAttribute('legacy_pagetitle');
        $vars['legacy_slogan'] = $context->getAttribute('legacy_slogan');
        
        //
        // Theme rendering
        //
        $blocks = array();
        foreach ($context->mAttributes['legacy_BlockContents'][0] as $key => $result) {
            // $smarty->append('xoops_lblocks', $result);
            $blocks[$result['name']] = $result;
        }
        $vars['xoops_lblocks'] = $blocks;

        $smarty->assign($vars);
        
        //
        // Check Theme or Fallback
        //
        $root =& XCube_Root::getSingleton();
        $theme = $root->mSiteConfig['Legacy']['Theme'];
        
        if (file_exists(XOOPS_ROOT_PATH.'/themes/'.$theme.'/admin_theme.html')) {
            $smarty->template_dir=XOOPS_THEME_PATH.'/'.$theme;
        } else {
            $smarty->template_dir=LEGACY_ADMIN_RENDER_FALLBACK_PATH;
        }

        $smarty->setModulePrefix('');
        $result=$smarty->fetch('file:admin_theme.html');

        $target->setResult($result);
    }

    public function renderMain(&$target)
    {
        //
        // Assign from attributes of the render-target.
        //
        foreach ($target->getAttributes() as $key=>$value) {
            $this->mSmarty->assign($key, $value);
        }
        
        $result = null;
        
        if ($target->getTemplateName()) {
            if ($target->getAttribute('legacy_module') != null) {
                $this->mSmarty->setModulePrefix($target->getAttribute('legacy_module'));
                $this->mSmarty->template_dir = XOOPS_MODULE_PATH . '/' . $target->getAttribute('legacy_module') . '/admin/'. LEGACY_ADMIN_RENDER_TEMPLATE_DIRNAME;
            }
            
            $result=$this->mSmarty->fetch('file:'.$target->getTemplateName());
            $buffer = $target->getAttribute('stdout_buffer');
            
            $this->_mStdoutBuffer .= $buffer;
        } else {
            $result=$target->getAttribute('stdout_buffer');
        }
        
        $target->setResult($result);

        //
        // Clear assign.
        //
        foreach ($target->getAttributes() as $key=>$value) {
            $this->mSmarty->clear_assign($key);
        }
    }
}

/***
 * @internal
 * Return URL string by "overriding" rule.
 * (Now, test implement)
 * 1) Search file in specified theme directory.
 * 2) Search file in current module template directory.
 * 3) Search file in fallback theme directory.
 */
function Legacy_modifier_theme($string)
{
    $infoArr = Legacy_get_override_file($string);
    
    if ($infoArr['theme'] != null && $infoArr['dirname'] != null) {
        return XOOPS_THEME_URL . '/' . $infoArr['theme'] . '/modules/' . $infoArr['dirname'] . '/' . $string;
    } elseif ($infoArr['theme'] != null) {
        return XOOPS_THEME_URL . '/' . $infoArr['theme'] . '/' . $string;
    } elseif ($infoArr['dirname'] != null) {
        return XOOPS_MODULE_URL . '/' . $infoArr['dirname'] . '/admin/templates/' . $string;
    }
    
    return LEGACY_ADMIN_RENDER_FALLBACK_URL . '/' . $string;
}

function Legacy_function_stylesheet($params, &$smarty)
{
    if (!isset($params['file'])) {
        $smarty->trigger_error('stylesheet: missing file parameter.');
        return;
    }
    
    $file = $params['file'];
    
    if (strstr($file, '..') !== false) {
        $smarty->trigger_error('stylesheet: missing file parameter.');
        return;
    }
    
    $media = (isset($params['media'])) ? $params['media'] : 'all';

    $infoArr = Legacy_get_override_file($file, 'stylesheets/');

    // TEMP
    // TODO We must return FALLBACK_URL here.
    if ($infoArr['file'] != null) {
        if ($params['static']) {
            $theme=$infoArr['theme'];
            $dirname=$infoArr['dirname'];
            $file='stylesheets/'.$file;
            if (!empty($theme) && !empty($dirname)) {
                $url = XOOPS_THEME_URL . "/$theme/modules/$dirname/$file";
            } elseif (!empty($theme)) {
                $url = XOOPS_THEME_URL . "/$theme/$file";
            } elseif (!empty($infoArr['dirname'])) {
                $url = XOOPS_MODULE_URL . "/$dirname/admin/templates/$file";
            } else {
                $url = LEGACY_ADMIN_RENDER_FALLBACK_URL . "/$file";
            }
        } else {
            if ($infoArr['file'] != null) {
                $request = array();
                foreach ($infoArr as $key => $value) {
                    if ($value != null) {
                        $request[] = "${key}=${value}";
                    }
                }
            }
            $url = XOOPS_MODULE_URL . '/legacyRender/admin/css.php?' . implode('&amp;', $request);
        }

        return '<link rel="stylesheet" type="text/css" media="'. $media .'" href="' . $url . '" />';
    }
}

function Legacy_get_override_file($file, $prefix = null, $isSpDirname = false)
{
    $root =& XCube_Root::getSingleton();
    $moduleObject =& $root->mContext->mXoopsModule;

    if ($isSpDirname && is_object($moduleObject) && $moduleObject->get('dirname') == 'legacy' && isset($_REQUEST['dirname'])) {
        $dirname = xoops_getrequest('dirname');
        if (preg_match('/^[a-z0-9_]+$/i', $dirname)) {
            $handler = xoops_gethandler('module');
            $moduleObject =& $handler->getByDirname($dirname);
        }
    }

    $theme = $root->mSiteConfig['Legacy']['Theme'];

    $ret = array();
    $ret['theme'] = $theme;
    $ret['file'] = $file;
    
    $file = $prefix . $file;

    static $checkCache = array();
    if (isset($checkCache[$file])) {
        return $checkCache[$file];
    }
        
    $themePath = XOOPS_THEME_PATH . '/' . $theme . '/';
    if (!is_object($moduleObject)) {
        if (file_exists($themePath. $file)) {
            return $checkCache[$file] = &$ret;
        }
        
        $ret['theme'] = null;
        return $checkCache[$file] = &$ret;
    } else {
        $ret['dirname'] = $dirname = $moduleObject->get('dirname');

        $mfile = $dirname . '/' . $file;
        if (isset($checkCache[$mfile])) {
            return $checkCache[$mfile];
        }
        if (file_exists($themePath.'modules/'.$mfile)) {
            return $checkCache[$mfile] = &$ret;
        }
        
        if (file_exists($themePath. $file)) {
            $ret['dirname'] = null;
            return $checkCache[$mfile] = &$ret;
        }
        
        $ret['theme'] = null;

        if (file_exists(XOOPS_MODULE_PATH . '/' . $dirname . '/admin/templates/' . $file)) {
            return $checkCache[$mfile] = &$ret;
        }
        
        $ret['dirname'] = null;

        if (file_exists(LEGACY_ADMIN_RENDER_FALLBACK_PATH . '/' . $file)) {
            return $checkCache[$mfile] = &$ret;
        }
        
        $ret['file'] =null;
        return $checkCache[$mfile] = &$ret;
    }
}
