<?php
/**
 * load jQuery Core, UI and plugin libraries
 * using defer and preload Since XCL 2.3.x
 * This Interface was originally generated by Cube tool.
 * @package    Legacy
 * @version    XCL 2.3.2
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     code generator
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_HeaderScript
{
    protected $_mType = 'google';
    protected $_mCore = '1';
    protected $_mUi = '1';

    protected $_mLibrary = [];
    protected $_mScript = [];
    protected $_mMeta = [
        'keywords' =>'',
        'description' =>'',
        'robots' =>'',
        'rating' =>'',
        'author' =>'',
        'copyright' =>'',
        'msvalidate.01' =>'',
        'google-site-verification' =>'',
        'yandex-verification' =>'',
        'fb:app_id' =>'',
        'twitter:site' =>''
    ];
    protected $_mOnloadScript = [];
    protected $_mStylesheet = [];
    protected $_mLink = [];

    public $mUsePrototype = false;    //use prototype.js ?
    public $mFuncNamePrefix = '';    //jQuery $() function's name prefix for compatibility with prototype.js

    /**
     * __construct
     *
     * @param	void
     *
     * @return	void
    **/
    public function __construct()
    {
        $root = XCube_Root::getSingleton();

        //setup jQuery library location
        $this->_mCore = $this->_getRenderConfig('jquery_core');
        $this->_mUi = $this->_getRenderConfig('jquery_ui');
        $core = str_replace('.', '', $this->_mCore);
        $this->_mType = is_numeric($core) ? 'google' : 'local';

        //use compatibility mode with prototype.js ?
        if (1 == $root->getSiteConfig('jQuery', 'usePrototype')) {
            $this->mUsePrototype = true;
            $this->mPrototypeUrl = $root->getSiteConfig('jQuery', 'prototypeUrl');
            $this->mFuncNamePrefix = $root->getSiteConfig('jQuery', 'funcNamePrefix');
        }

        $this->_setupDefaultStylesheet();
    }

    /**
     * _setupDefaultStylesheet
     *
     * @param	void
     *
     * @return	void
    **/
    public function _setupDefaultStylesheet()
    {
        if ($this->_getRenderConfig('css_file')) {
            $this->addStylesheet($this->_getRenderConfig('css_file'), false);
        }
    }

    /**
     * addLibrary
     *
     * @param string $url
     * @param bool $xoopsUrl
     *
     * @return	void
    **/
    public function addLibrary( string $url, bool $xoopsUrl=true )
    {
        $libUrl = (true == $xoopsUrl) ? XOOPS_URL . $url : $url;
        if (!in_array($libUrl, $this->_mLibrary, true)) {
            $this->_mLibrary[] = $libUrl;
        }
    }

    /**
     * addStylesheet
     *
     * @param string $url
     * @param bool $xoopsUrl
     *
     * @return	void
    **/
    public function addStylesheet( string $url, bool $xoopsUrl=true )
    {
        $libUrl = (true == $xoopsUrl) ? XOOPS_URL . $url : $url;
        if (!in_array($libUrl, $this->_mStylesheet, true)) {
            $this->_mStylesheet[] = $libUrl;
        }
    }

    /**
     * addScript
     *
     * @param string $script
     * @param bool $isOnloadFunction
     *
     * @return	void
    **/
    public function addScript( string $script, bool $isOnloadFunction=true )
    {
        if (true == $isOnloadFunction) {
            $this->_mOnloadScript[] = $script;
        } else {
            $this->_mScript[] = $script;
        }
    }

    /**
     * getLibraryArr
     *
     * @param	void
     *
     * @return	string[]
    **/
    public function getLibraryArr()
    {
        return $this->_mLibrary;
    }

    /**
     * getScriptArr
     *
     * @param bool $isOnloadFunction
     *
     * @return	string[]
    **/
    public function getScriptArr( bool $isOnloadFunction=true )
    {
        if (true == $isOnloadFunction) {
            return $this->_mOnloadScript;
        } else {
            return $this->_mScript;
        }
    }

    /**
     * addLink
     *
     * @param string $rel
     * @param string $href
     * @param string $type
     * @param string|null $title
     *
     * @return	void
    **/
    public function addLink( string $rel, string $href, string $type, string $title=null )
    {
        $this->_mLink[] = ['rel' =>$rel, 'type' =>$type, 'title' =>$title, 'href' =>$href];
    }

    /**
     * addMeta
     *
     * @param string $name
     * @param string $content
     *
     * @return	void
    **/
    public function addMeta( string $name, string $content )
    {
        $this->_mMeta[$name] = $content;
    }

    /**
     * getMeta
     *
     * @param string $name
     *
     * @return	string
    **/
    public function getMeta( string $name )
    {
        return $this->_mMeta[$name];
    }

    /**
     * createLibraryTag
     *
     * @param	void
     *
     * @return	string
    **/
    public function createLibraryTag()
    {
        $html = '';

        //prototype.js compatibility
        if ($this->mUsePrototype) {
            $html .= '<script src="'. $this->mPrototypeUrl .'"></script>';
        }

        // load main library
        if ('google' == $this->_mType) {
            $html .= $this->_loadGoogleJQueryLibrary();
        } elseif ('local' == $this->_mType) {
            $html .= $this->_loadLocalJQueryLibrary();
        }

        // load plugin libraries
        // Preload Since XCL 2.3.x
        foreach ($this->_mLibrary as $lib) {
            //$html .= '<link rel="preload" as="script" src="' . $lib . "\">\n";
            $html .= "<link rel=\"preload\" as=\"script\" href=" . $lib . " onload=\"this.onload=null;this.rel='script'\">\n";
            $html .= "<noscript><link rel=\"stylesheet\" href=" . $lib . "></noscript>\n";

            $html .= '<script defer src="' . $lib . "\"></script>\n";
        }

        // load css
        // Preload Since XCL 2.3.x
        foreach ($this->_mStylesheet as $css) {
            $html .= "<link rel=\"preload\" as=\"style\" href=" . $css . " onload=\"this.onload=null;this.rel='stylesheet'\">\n";
            $html .= "<noscript><link rel=\"stylesheet\" href=" . $css . "></noscript>\n";
           //<noscript><link rel=\"stylesheet\" href=" . $css . "></noscript><!-- test -->\n";
        }

        // load link
        // Preload Since XCL 2.3.x
        foreach ($this->_mLink as $link) {
            $title = $link['title'] ? 'title="'.$link['title'].'" ' : null;
            $html .= sprintf("<link rel=\"preload\" as=\"%s\" onload=\"this.onload=null;this.rel=\'%s'\" href=\"%s\" $title>\n", $link['type'], $link['rel'], $link['href']);
        }

        //set rss auto-discovery
        if ($this->_getRenderConfig('feed_url')) {
            $html .= sprintf('<link rel="alternate" type="application/rss+xml" title="rss" href="%s" />'."\n", $this->_getRenderConfig('feed_url'));
        }
        return $html;
    }

    /**
     * _loadGoogleJQueryLibrary
     *
     * @param	void
     *
     * @return	string
    **/
    protected function _loadGoogleJQueryLibrary()
    {
        $apiKey = XCube_Root::getSingleton()->getSiteConfig('jQuery', 'GoogleApiKey');
        $apiKey = (isset($apiKey)) ? '?key='.$apiKey : null;
        return '<script src="//www.google.com/jsapi'.$apiKey.'" crossorigin="anonymous"></script>
<script crossorigin="anonymous">
google.load("language", "1");
google.load("jquery", "'. $this->_mCore .'");
google.load("jqueryui", "'. $this->_mUi .'");
</script>
';
    }

    /**
     * load Local JQuery Library
     * Defer UI since XCL 2.3.x
     * @param	void
     *
     * @return	string
    **/
    protected function _loadLocalJQueryLibrary()
    {
        $html = '';
        if ($this->_mCore) {
            $html .= '<script src="'. $this->_mCore .'"></script>';
        }
        if ($this->_mUi) {
            $html .= '<script defer src="'. $this->_mUi .'"></script>';
        }

        return $html;
    }

    /**
     * createOnloadFunctionTag
     *
     * @param	void
     *
     * @return	string
    **/
    public function createOnloadFunctionTag()
    {
        $html = null;
        if (count($this->_mOnloadScript)>0||count($this->_mScript)>0) {
            $html = "<script crossorigin=\"anonymous\" aria-label=\"script\">\n";
            if ('google' == $this->_mType) {
                $html .= "google.setOnLoadCallback(function() {\n";
            }
            if (true == $this->mUsePrototype) {
                $html .= "jQuery.noConflict();\n";
            }
            $html .= "jQuery(function($){\n";
            $html .= $this->_makeScript(true);
            if ('google' == $this->_mType) {
                $html .= "\n});\n";
            }
            $html .= "\n});\n";
            $html .= $this->_makeScript(false);
            $html .= '</script>' . "\n";
        }
        return $html;
    }

    /**
     * _makeScript
     *
     * @param bool $isOnloadFunction
     *
     * @return	string
    **/
    protected function _makeScript(bool $isOnloadFunction=true)
    {
        $html = null;
        $scriptArr = (true === $isOnloadFunction) ? $this->_mOnloadScript : $this->_mScript;
        foreach ($scriptArr as $script) {
            $html .= $this->_convertFuncName($script);
        }
        return $html;
    }

    /**
     * _convertFuncName
     *
     * @param string $script
     *
     * @return	string
    **/
    protected function _convertFuncName(string $script)
    {
        if ($this->mFuncNamePrefix) {
            $script = str_replace('$(', $this->mFuncNamePrefix . '$(', $script);
        }
        return $script;
    }

    /**
     * Get Render Configuration
     *
     * @param string $key
     *
     * @return	string
    **/
    protected function _getRenderConfig(string $key)
    {
        $handler =& xoops_gethandler('config');
        $configArr =& $handler->getConfigsByDirname('legacyRender');

        // @todo @gigamaster PHP74 Null coalesce operator - No need to explicitly initialize the variable.
        // return $configArr[$key];
        return $configArr[$key] ?? '';
    }
}
