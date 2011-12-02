<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
@include_once XOOPS_ROOT_PATH . '/modules/cubeUtils/class/MultiLanguage.class.php';
class MultiLanguagePreLoad extends XCube_ActionFilter
{
    function preFilter()
    {
        if (file_exists(XOOPS_ROOT_PATH.'/modules/cubeUtils/class/MultiLanguage.class.php')) {
            $cubeUtilMlang = new CubeUtil_MultiLanguage();
            $this->mController->mGetLanguageName->add(array(&$cubeUtilMlang, 'getLanguageName'),XCUBE_DELEGATE_PRIORITY_FINAL);
            // Following Global variable is prepared for calling direct filterling function
            $GLOBALS['cubeUtilMlang'] =& $cubeUtilMlang;
        }
        $this->mController->mCreateLanguageManager->add(array(&$this, 'createLanguageManager'));
        // Followin line is a little bit tricky to include charset_mysql.php
        $this->mController->mSetupTextFilter->add(array(&$this, 'setupTextFilter'),XCUBE_DELEGATE_PRIORITY_FIRST);
    }

    function preBlockFilter()
    {
        $this->mController->mSetBlockCachePolicy->add(array(&$this, 'addLanguageAsIdentity'), XCUBE_DELEGATE_PRIORITY_FIRST + 20);
        $this->mController->mSetModuleCachePolicy->add(array(&$this, 'addLanguageAsIdentity'), XCUBE_DELEGATE_PRIORITY_FIRST + 20);
    }

    function createLanguageManager(&$langManager, $languageName)
    {
        if (defined('CUBE_UTILS_ML_OUTPUT_CHARSET')) {
            if (!defined('_CHARSET')) define('_CHARSET', CUBE_UTILS_ML_OUTPUT_CHARSET);
        }
        if (defined('CUBE_UTILS_ML_OUTPUT_MULTIBYTE')) {
            if (!defined('XOOPS_USE_MULTIBYTES')) define('XOOPS_USE_MULTIBYTES', CUBE_UTILS_ML_OUTPUT_MULTIBYTE);
        }
    }

    function setupTextFilter(&$instance)
    {
        if (defined('CUBE_UTILS_ML_DBSETUP_LANGUAGE')) {
            $filename = XOOPS_MODULE_PATH . '/legacy/language/' . CUBE_UTILS_ML_DBSETUP_LANGUAGE . '/charset_' . XOOPS_DB_TYPE . '.php';
            if (file_exists($filename)) {
                require_once($filename);
            }
        }
    }

    function addLanguageAsIdentity($cacheInfo)
    {
        $language = $this->mController->mRoot->mLanguageManager->getLanguage();
        $cacheInfo->mIdentityArr[] = "Module_cubeUtils_Language:".$language;
    }
}
?>
