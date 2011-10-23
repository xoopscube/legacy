<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

define('LECAT_ADMIN_RENDER_TEMPLATE_DIRNAME','templates');
define('LECAT_ADMIN_RENDER_FALLBACK_PATH',XOOPS_MODULE_PATH . '/legacy/admin/theme');    // TODO will be use other path
define('LECAT_ADMIN_RENDER_FALLBACK_URL',XOOPS_MODULE_URL . '/legacy/admin/theme');    // TODO will be use other url

/**
 * Lecat_AdminRenderSystem
**/
class Lecat_AdminRenderSystem extends Legacy_AdminRenderSystem
{
    /**
     * prepare
     * 
     * @param   XCube_Controller  &$controller
     * 
     * @return  void
    **/
    public function prepare(/*** XCube_Controller ***/ &$controller)
    {
        $this->mController =& $controller;
    
        $this->mSmarty =new Legacy_AdminSmarty();    // TODO will be use other class?
        $this->mSmarty->register_modifier('theme',array($this,'modifierTheme'));
        $this->mSmarty->register_function('stylesheet',array($this,'functionStylesheet'));
    
        $this->mSmarty->assign(
            array(
                'xoops_url'        => XOOPS_URL,
                'xoops_rootpath'   => XOOPS_ROOT_PATH,
                'xoops_langcode'   => _LANGCODE,
                'xoops_charset'    => _CHARSET,
                'xoops_version'    => XOOPS_VERSION,
                'xoops_upload_url' => XOOPS_UPLOAD_URL
            )
        );
    
        // TODO event name is this?
        XCube_DelegateUtils::call('XoopsTpl.New',new XCube_Ref($this->mSmarty));
    
        $this->mSmarty->force_compile = (
            $controller->mRoot->mSiteConfig['Legacy_AdminRenderSystem']['ThemeDevelopmentMode'] ||
            $controller->mRoot->mContext->getXoopsConfig('theme_fromfile')
        );
    }

    /**
     * renderBlock
     * 
     * @param   XCube_RenderTarget  &$target
     * 
     * @return  void
    **/
    public function renderBlock(/*** XCube_RenderTarget ***/ &$target)
    {
        parent::renderBlock($target);    // TODO will be use other method
    }

    /**
     * renderTheme
     * 
     * @param   XCube_RenderTarget  &$target
     * 
     * @return  void
    **/
    public function renderTheme(/*** XCube_RenderTarget ***/ &$target)
    {
        $module =& $this->mController->getVirtualCurrentModule();
        $context =& $this->mController->mRoot->getContext();
        $this->mSmarty->assign($target->getAttributes());
        $this->mSmarty->assign(
            array(
                'stdout_buffer'    => $this->_mStdoutBuffer,
                'currentModule'       => $module,
                'legacy_sitename'  => $context->getAttribute('legacy_sitename'),
                'legacy_pagetitle' => $context->getAttribute('legacy_pagetitle'),
                'legacy_slogan'    => $context->getAttribute('legacy_slogan')
            )
        );
    
        $blocks = array();
        foreach($context->mAttributes['legacy_BlockContents'][0] as $block)
        {
            $blocks[$block['name']] = $block;
        }
        $this->mSmarty->assign('xoops_lblocks',$blocks);
    
        $info = Lecat_AdminRenderSystem::getOverrideFileInfo('admin_theme.html');
        $this->mSmarty->template_dir = ($info['file'] != null) ?
            substr($file['path'],0,-15) :
            LECAT_ADMIN_RENDER_FALLBACK_PATH;
        $this->mSmarty->setModulePrefix('');
    
        $target->setResult($this->mSmarty->fetch('file:admin_theme.html'));
    }

    /**
     * renderMain
     * 
     * @param   XCube_RenderTarget  &$target
     * 
     * @return  void
    **/
    public function renderMain(/*** XCube_RenderTarget ***/ &$target)
    {
        $info = Lecat_AdminRenderSystem::getOverrideFileInfo($target->getTemplateName());
        $this->mSmarty->compile_id = $info['dirname'];
        $this->mSmarty->assign($target->getAttributes());
        $this->mSmarty->template_dir = substr($info['path'],0,-strlen($info['file']));
    
        $res = $this->mSmarty->fetch('file:' . $info['file']);
        $target->setResult($res);
        $this->_mStdoutBuffer .= $target->getAttribute('stdout_buffer');
    
        foreach($target->getAttributes() as $key => $val)
        {
            $this->mSmarty->clear_assign($key);
        }
    }

    /**
     * getOverrideFileInfo
     * 
     * @param   string  $file
     * @param   string  $prefix
     * @param   bool  $isSpDirName
     * 
     * @return  {string 'theme',string 'file',string 'dirname'}
    **/
    public static function getOverrideFileInfo(/*** string ***/ $file,/*** string ***/ $prefix = null,/*** bool ***/ $isSpDirName = false)
    {
        $ret = array(
            'url'     => null,
            'path'    => null,
            'theme'   => null,
            'dirname' => null,
            'file'    => null
        );
        if(strpos($file,'..') !== false || strpos($prefix,'..' !== false))
        {
            return $ret;
        }
        $root =& XCube_Root::getSingleton();
    
        $module =& $root->mContext->mXoopsModule;
        $dirName = $root->mContext->mRequest->getRequest('dirname');
        if($isSpDirName && preg_match('/^\w+$/',$dirName))
        {
            $handler =& Lecat_Utils::getXoopsHandler('module');
            $module =& $handler->getByDirname($dirName);
        }
    
        $isModule = is_object($module);
        $theme = $root->mSiteConfig['Legacy']['Theme'];
        $ret['theme'] = $theme;
        $dirName = $isModule ? $module->get('dirname') : null;
        $trustDirName = $isModule ? $module->getInfo('trust_dirname') : null;
        $ret['file']  = $file;
    
        $file = $prefix . $file;
    
        switch(true)
        {
            case $isModule && file_exists(
                $path = sprintf(
                    '%s/modules/%s/%s',
                    XOOPS_THEME_PATH,$theme,$dirName,$file
                )
            ):
                $ret['url'] = sprintf(
                    '%s/%s/modules/%s/%s',
                    XOOPS_THEME_URL,$theme,$dirName,$file
                );
                $ret['path'] = $path;
                return $ret;
            case $isModule && file_exists(
                $path = sprintf(
                    '%s/themes/%s/modules/%s/%s',
                    XOOPS_TRUST_PATH,$theme,$trustDirName,$file
                )
            ):
                $ret['path'] = $path;
                $ret['dirname'] = $trustDirName;
                return $ret;
            case file_exists(
                $path = sprintf(
                    '%s/%s/%s',
                    XOOPS_THEME_PATH,$theme,$file
                )
            ):
                $ret['url'] = sprintf(
                    '%s/%s/%s',
                    XOOPS_THEME_URL,$theme,$file
                );
                $ret['path'] = $path;
                $ret['dirname'] = null;
                return $ret;
            case file_exists(
                $path = sprintf(
                    '%s/themes/%s/%s',
                    XOOPS_TRUST_PATH,$theme,$file
                )
            ):
                $ret['path'] = $path;
                $ret['dirname'] = null;
                return $ret;
            case $isModule && file_exists(
                $path = sprintf(
                    '%s/%s/admin/templates/%s',
                    XOOPS_MODULE_PATH,$dirName,$file
                )
            ):
                $ret['url'] = sprintf(
                    '%s/%s/admin/templates/%s',
                    XOOPS_MODULE_URL,$dirName,$file
                );
                $ret['path'] = $path;
                $ret['theme'] = null;
                return $ret;
            case $isModule && file_exists(
                $path = sprintf(
                    '%s/modules/%s/admin/templates/%s',
                    XOOPS_TRUST_PATH,$trustDirName,$file
                )
            ):
                $ret['path'] = $path;
                $ret['theme'] = null;
                $ret['dirname'] = $trustDirName;
                return $ret;
            case file_exists($path = LECAT_ADMIN_RENDER_FALLBACK_PATH . '/' .$file):
                $ret['url'] = LECAT_ADMIN_RENDER_FALLBACK_URL . '/' . $file;
                $ret['path'] = $path;
                $ret['theme'] = null;
                $ret['dirname'] = null;
                return $ret;
            default:
                $ret['theme'] = null;
                $ret['dirname'] = null;
                $ret['file'] = null;
                return $ret;
        }
    }

    /**
     * modifierTheme
     * 
     * @param   string  $str
     * 
     * @return  string
    **/
    public static function modifierTheme(/*** string ***/ $str)
    {
        $info = Lecat_AdminRenderSystem::getOverrideFileInfo($str);
        if($info['url'] != null)
        {
            return $info['url'];
        }
        return LECAT_ADMIN_RENDER_FALLBACK_URL . '/' . $str;
    }

    /**
     * functionStylesheet
     * 
     * @param   {string 'file',string 'media'}  $param
     * @param   Smarty  &$smarty
     * 
     * @return  void
    **/
    public static function functionStylesheet(/*** {string 'file',string 'media'} ***/ $param,/*** Smarty ***/ &$smarty)
    {
        if(!isset($params['file']) || strpos($params['file'],'..') !== false)
        {
            return;
        }
    
        $info = Lecat_AdminRenderSystem::getOverrideFileInfo($params['file'],'stylesheets/');
        if($info['file'] == null)
        {
            return;
        }
    
        // TODO will be use other method
        printf(
            '<link rel="stylesheet" typw="text/css" media="%s" href="%s/legacy/admin/css.php?file=%s%s%s" />',
            (isset($params['media']) ? $params['media'] : 'all'),
            XOOPS_MODULE_URL,
            $info['file'],
            ($info['dirname'] != null ? '&amp;dirname=' . $info['dirname'] : ''),
            ($info['theme'] != null ? '&amp;theme=' . $info['theme'] : '')
        );
    }
}

?>
