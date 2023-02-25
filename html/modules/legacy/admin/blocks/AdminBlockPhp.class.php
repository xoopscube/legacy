<?php
/**
 * AdminBlockPhp.class.php
 * @package    Legacy
 * @version    XCL 2.3.2
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
// TODO cache block ?
//define('LEGACY_ADMINBLOCKPHP_CACHEPREFIX', XOOPS_CACHE_PATH.'/'.urlencode(XOOPS_URL).'_admin_blockphp_');

/**
 * This is test menu block for control panel of legacy module.
 *
 * [ASSIGN]
 *  No
 *
 * @package legacy
 */
class Legacy_AdminBlockPhp extends Legacy_AbstractBlockProcedure
{
    public function getName()
    {
        return 'block_php';
    }

    public function getTitle()
    {
        return 'PHP Settings'; // TODO language constant
    }

    public function getEntryIndex()
    {
        return 0;
    }

    public function isEnableCache()
    {
        return false;
    }

    public function execute()
    {
        $root =& XCube_Root::getSingleton();

        // load admin message catalog of legacy for _AD_LEGACY_LANG_NO_SETTING, even if the current module is not Legacy.
        $langMgr =& $root->mLanguageManager;
        $langMgr->loadModuleAdminMessageCatalog('legacy');
        // load info 'modinfo' message catalog
        $langMgr->loadModinfoMessageCatalog('legacy');

        $render =& $this->getRenderTarget();

        /**
         * Assign PHP Settings
         * @array
         */
        $php_setting = [];
        $rowOpen ="<div data-layout='row sm-column'><div data-self='size-1of3 sm-half'>";
        $rowDiv= "</div>";
        $badge = "<div><span class='badge'>";
        $green ="<div><span class='badge' style='color:var(--color-green)'>";
        $red = "<div><span class='badge' style='color:var(--color-red)'>";
        $rowClose = "</span></div></div>";
        $on = $green . _AD_LEGACY_PHPSETTING_ON . $rowClose;
        $off = $red . _AD_LEGACY_PHPSETTING_OFF . $rowClose;
        $yes = $green . _YES . $rowClose;
        $no = $red . _NO . $rowClose;
        // https://www.php.net/manual/en/mbstring.configuration.php
        $php_setting[] = '<div class="ui-card-2-col">';
        $php_setting[] = $rowOpen . 'Default_charset' . $rowDiv . (ini_get( 'default_charset' )? $badge . ini_get('default_charset') . $rowClose : $off);
        $php_setting[] = $rowOpen .'mbstring extension'  . $rowDiv . ( extension_loaded( 'mbstring' ) ? $yes : $no);
        $php_setting[] = $rowOpen .'mbstring.language' . $rowDiv . (ini_get( 'mbstring.language' )? $badge . ini_get('mbstring.language') . $rowClose : $off);
        $php_setting[] = $rowOpen .'Encoding translation' . $rowDiv . ( ini_get( 'mbstring.encoding_translation' )? $on : $off);
        // PHP ini settings
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_DE . $rowDiv . (ini_get('display_errors')? $on : $off);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_SM . $rowDiv . (ini_get('safe_mode')? $on : $off);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_MET. $rowDiv . (ini_get('max_execution_time')? $badge . ini_get('max_execution_time') . " sec." . $rowClose : $off);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_ML. $rowDiv . (ini_get('memory_limit')? $badge . ini_get('memory_limit') . "b" . $rowClose : $off);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_SOT. $rowDiv . (ini_get('short_open_tag')? $on : $off);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_FU. $rowDiv . (ini_get('file_uploads')?
                $green . _AD_LEGACY_PHPSETTING_ON . "</span><br>"
                . "<span class='badge'>" . _AD_LEGACY_PHPSETTING_FU_UMAX.ini_get('upload_max_filesize') . "</span><br>"
                . "<span class='badge'>" . _AD_LEGACY_PHPSETTING_FU_PMAX.ini_get('post_max_size') . $rowClose
                : $red . _AD_LEGACY_PHPSETTING_OFF . $rowClose);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_OB . $rowDiv . (ini_get('output_buffering')? $on : $off);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_OBD  . $rowDiv . (ini_get('open_basedir')? $on : $off);
        // Recommended on << off
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_UFO . $rowDiv . (ini_get('allow_url_fopen')? $red ._AD_LEGACY_PHPSETTING_ON." (recommended OFF)" . $rowClose : $green ._AD_LEGACY_PHPSETTING_OFF. $rowClose);
        // PHP EXTENSIONS
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_DOM . $rowDiv . (extension_loaded('dom')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_EXIF . $rowDiv . (extension_loaded('exif')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_GTXT. $rowDiv . (extension_loaded('gettext')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_JSON. $rowDiv . (extension_loaded('json')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_XML. $rowDiv . (extension_loaded('xml')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_CRL . $rowDiv . (extension_loaded('curl')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_ZLIB . $rowDiv . (extension_loaded('zlib')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_SOAP . $rowDiv . (extension_loaded('soap')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_MB . $rowDiv . (extension_loaded('mbstring')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_ICONV . $rowDiv . (function_exists('iconv')? $yes : $no);
        $php_setting[] = $rowOpen . _AD_LEGACY_PHPSETTING_GD . $rowDiv . (function_exists('gd_info')? $yes : $no);

        /* Check GD version */
        if (function_exists('gd_info')) {
            $gd_info = gd_info() ;
            $php_setting[] =  $rowOpen . "GD Version". $rowDiv . $badge . "{$gd_info['GD Version']}" . $rowClose;
        }
        if (function_exists('imagecreatetruecolor')) {
            $php_setting[] =  $rowOpen . _AD_LEGACY_PHPSETTING_GD. $rowDiv . $badge . "Image create Truecolor" . $rowClose;
        }
        if( extension_loaded('imagick') || class_exists("Imagick") ){
            /* Check Imagick */
            $imagick_version = Imagick::getVersion();
            $imagick_version_number = $imagick_version['versionNumber'];
            $imagick_version_string = $imagick_version['versionString'];
            $php_setting[] = $rowOpen .'Imagick'. $rowDiv . $green ."Image create Truecolor" . $rowClose;
            $php_setting[] = $rowOpen .'Imagick version number'. $rowDiv .$badge .$imagick_version_number . $rowClose;
            // $php_setting[] = $rowOpen .'Imagick version'. $rowDiv .$badge .$imagick_version_string . $rowClose;
        }
        $php_setting[] = '</div>';

        // Set attributes
        $render->setAttribute('legacy_module', 'legacy');

        $render->setAttribute('php_setting', $php_setting);

        $render->setTemplateName('legacy_admin_block_php.html');

        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());

        // Render as block
        $renderSystem->renderBlock($render);

    }

    public function hasResult()
    {
        return true;
    }

    public function &getResult()
    {
        $dmy = 'dummy';
        return $dmy;
    }

    public function getRenderSystemName()
    {
        return 'Legacy_AdminRenderSystem';
    }
}
