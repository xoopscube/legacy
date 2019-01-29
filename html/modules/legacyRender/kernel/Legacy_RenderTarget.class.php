<?php
/**
 * @version $Id: Legacy_RenderTarget.class.php,v 1.1 2007/05/15 02:35:07 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

define("LEGACY_RENDER_TARGET_TYPE_BUFFER", null);
define("LEGACY_RENDER_TARGET_TYPE_THEME", 'theme');
define("LEGACY_RENDER_TARGET_TYPE_BLOCK", 'block');
define("LEGACY_RENDER_TARGET_TYPE_MAIN", 'main');

class Legacy_AbstractThemeRenderTarget extends XCube_RenderTarget
{
    public $mSendHeaderFlag=false;

    public function Legacy_AbstractThemeRenderTarget()
    {
        self::__construct();
    }

    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('legacy_buffertype', LEGACY_RENDER_TARGET_TYPE_THEME);
    }

    public function sendHeader()
    {
        header('Content-Type:text/html; charset='._CHARSET);
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }

    public function setResult(&$result)
    {
        parent::setResult($result);
        if (!$this->mSendHeaderFlag) {
            $this->sendHeader();
            $this->mSendHeaderFlag=true;
        }

        print $result;
    }
}

class Legacy_ThemeRenderTarget extends Legacy_AbstractThemeRenderTarget
{
    public function Legacy_ThemeRenderTarget()
    {
        self::__construct();
    }

    public function __construct()
    {
        parent::__construct();
        $this->setAttribute("isFileTheme", true);
    }
}

class Legacy_DialogRenderTarget extends Legacy_AbstractThemeRenderTarget
{
    public function Legacy_DialogRenderTarget()
    {
        self::__construct();
    }

    public function __construct()
    {
        parent::__construct();
        $this->setAttribute("isFileTheme", false);
    }
    
    public function getTemplateName()
    {
        return "legacy_render_dialog.html";
    }
}

class Legacy_RenderTargetMain extends XCube_RenderTarget
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('legacy_buffertype', LEGACY_RENDER_TARGET_TYPE_MAIN);
    }
}
