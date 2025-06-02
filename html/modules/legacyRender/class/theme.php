<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyRenderThemeObject extends XoopsSimpleObject
{
    public $mPackage = [];
    public $mActiveResource = true;

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('id', XOBJ_DTYPE_INT, '', true);
        $this->initVar('name', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('tplset_id', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('enable_select', XOBJ_DTYPE_BOOL, '0', true);
        $initVars=$this->mVars;
    }

    public function loadPackage()
    {
        $themeDir = XOOPS_THEME_PATH . '/' . $this->get('name');

        $this->mPackage = []; 
        $this->mActiveResource = false;

        $manifestoFilePath = $themeDir . '/manifesto.ini.php';

        if (file_exists($manifestoFilePath)) {
            $iniHandler = new XCube_IniHandler($manifestoFilePath, true);
            $this->mPackage = $iniHandler->getAllConfig();

            // manifesto.ini.php exists [Manifesto], and [Manifesto][Depends] must be set with 'Legacy_RenderSystem'
            if (isset($this->mPackage['Manifesto'], $this->mPackage['Manifesto']['Depends'])) {
                // Use case-insensitive regex to check for 'Legacy_RenderSystem'
                if (preg_match('/Legacy_RenderSystem(\s|,|$)/i', $this->mPackage['Manifesto']['Depends'])) {
                    $this->mActiveResource = true;
                }
            }
        } else {
            // fallback to checking for theme.html for older themes without manifesto.ini.php
            $legacyThemeHtmlFile = $themeDir . '/theme.html';
            $this->mActiveResource = file_exists($legacyThemeHtmlFile);
        }
    }

    public function isActiveResource()
    {
        return $this->mActiveResource;
    }
}

class LegacyRenderThemeHandler extends XoopsObjectGenericHandler
{
    public $mTable   = 'legacyrender_theme';
    public $mPrimary = 'id';
    public $mClass   = 'LegacyRenderThemeObject';

    public function &getByName($themeName)
    {
        $criteria = new Criteria('name', $themeName);
        $obj =& $this->getObjects($criteria);
        if (count($obj) > 0) { 
            return $obj[0];
        }
        // @gigamaster split workflow
        $obj =& $this->create();
        return $obj;
    }

    /**
     * Search themes that Legacy_RenderSystem can render in file system, then register by handler.
     */
    public function searchThemes()
    {
        $themeList = [];
        $moduleHandler = null; // Lazy load handler

        $themesPath = XOOPS_THEME_PATH;
        if (!is_dir($themesPath) || !($dh = opendir($themesPath))) {
            // Log error or handle - cannot open themes directory
            return $themeList;
        }

        while (false !== ($dir = readdir($dh))) {
            if ('.' === $dir || '..' === $dir) {
                continue;
            }

            $themeDir = $themesPath . '/' . $dir;
            if (!is_dir($themeDir)) {
                continue;
            }

            $isPrimaryDependencyMet = false;    // is 'Legacy_RenderSystem'
            $allSecondaryDependenciesMet = true;

            $manifestoFilePath = $themeDir . '/manifesto.ini.php';

            if (file_exists($manifestoFilePath)) {
                $iniHandler = new XCube_IniHandler($manifestoFilePath, true);
                $manifesto = $iniHandler->getAllConfig();

                if (isset($manifesto['Manifesto'], $manifesto['Manifesto']['Depends'])) {
                    $dependsString = $manifesto['Manifesto']['Depends'];

                    // Check Legacy_RenderSystem
                    if (preg_match('/Legacy_RenderSystem(\s|,|$)/i', $dependsString)) {
                        $isPrimaryDependencyMet = true;

                        // Check for other dependencies
                        $declaredDependencies = preg_split('/[\s,]+/', $dependsString, -1, PREG_SPLIT_NO_EMPTY);
                        
                        $secondaryDependenciesToCheck = [];
                        foreach ($declaredDependencies as $dep) {
                            $trimmedDep = trim($dep);
                            if (!empty($trimmedDep) && strcasecmp($trimmedDep, 'Legacy_RenderSystem') !== 0) {
                                $secondaryDependenciesToCheck[] = $trimmedDep;
                            }
                        }

                        if (!empty($secondaryDependenciesToCheck)) {
                            if ($moduleHandler === null) {
                                $moduleHandler = xoops_gethandler('module');
                            }
                            
                            foreach ($secondaryDependenciesToCheck as $depName) {
                                $criteria = new CriteriaCompo(new Criteria('dirname', $depName));
                                $criteria->add(new Criteria('isactive', 1));
                                
                                if ($moduleHandler->getCount($criteria) == 0) {
                                    $allSecondaryDependenciesMet = false; // no other dependency
                                    break; // No need to check further
                                }
                            }
                        }
                    }
                }
            } else {
                // fallback no manifesto.ini.php
                $legacyThemeHtmlFile = $themeDir . '/theme.html';
                if (file_exists($legacyThemeHtmlFile)) {
                    $isPrimaryDependencyMet = true; // dependencies to check
                }
            }

            if ($isPrimaryDependencyMet && $allSecondaryDependenciesMet) {
                $themeList[] = $dir;
            }
        }
        closedir($dh);
        return $themeList;
    }

    public function updateThemeList()
    {
        $diskThemeNames = $this->searchThemes();
        $dbThemes =& $this->getObjects();

        // Check new theme.
        foreach ($diskThemeNames as $name) {
            $findFlag = false;
            foreach ($dbThemes as $theme) {
                if ($theme->get('name') === $name) {
                    $findFlag = true;
                    break;
                }
            }

            if (!$findFlag) {
                $obj =& $this->create();
                $obj->set('name', $name);
                $this->insert($obj, true); // Consider $force = false to run validations
            }
        }

        // Next, check themes that we got from DB. If it had removed from disk system
        // (or no longer meets dependency criteria), remove from DB.
        foreach ($dbThemes as $theme) {
            if (!in_array($theme->get('name'), $diskThemeNames, true)) { // strict comparison for in_array
                $this->delete($theme, true); // Consider $force = false
            }
        }
    }
}
