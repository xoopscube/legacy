<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyTheme
{
    public $mDirName=null;
    public $mFileName=null;
    public $ScreenShot=null;
    public $mManifesto=null;

    public function __construct($dirName, $manifesto=null)
    {
        $this->mDirName=$dirName;
        if (null !== $manifesto) {
            $this->initializeByManifesto($manifesto);
        }
    }

    public function initializeByManifesto($manifesto)
    {
        //
        // Manifesto sanitized to prevent that an attacker triggers javascript with a fake theme.
        //
        $this->mManifesto=$manifesto;
        $this->ScreenShot=$manifesto['Theme']['ScreenShot'];
    }
}

class LegacyThemeHandler
{
    public $_mThemeList = [];

    public function __construct()
    {
        if ($handler=opendir(XOOPS_THEME_PATH)) {
            while (false !== ($dir=readdir($handler))) {
                if ('.' === $dir || '..' === $dir) {
                    continue;
                }

                $themeDir= XOOPS_THEME_PATH . '/' . $dir;
                if (is_dir($themeDir)) {
                    $manifesto = [];
                    if (file_exists($mnfFile = $themeDir . '/manifesto.ini.php')) {
                        $iniHandler = new XCube_IniHandler($mnfFile, true);
                        $manifesto = $iniHandler->getAllConfig();
                    }

                    if ((is_countable($manifesto) ? count($manifesto) : 0) > 0) {
                        //
                        // If this system can use this theme, add this to list.
                        //
                        // @gigamaster merged isset and applied strict ( === )
                       if (isset($manifesto['Manifesto']) && isset($manifesto['Manifesto']['Depends']) && 'Legacy_RenderSystem' == $manifesto['Manifesto']['Depends']) {
                            $this->_mThemeList[]=new LegacyTheme($dir, $manifesto);
                        }
                    } else {
                        $file= $themeDir . '/theme.html';
                        if (file_exists($file)) {
                            $this->_mThemeList[]=new LegacyTheme($dir);
                        }
                    }
                }
            }
            closedir($handler);
        }
    }

    public function &enumAll()
    {
        return $this->_mThemeList;
    }
}
