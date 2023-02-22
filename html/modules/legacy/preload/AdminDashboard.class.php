<?php
/**
 * Preload  : AdminDashboard
 * Version  : 2.3
 * Package  : XCL
 * Module   : Legacy
 * Author   : Nuno Luciano aka Gigamaster
 * Credits  : Original AdminSystemCheckPlus Preload
 *            by Wanikoo ( https://www.wanisys.net/ )
*/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * TODO dashboard UI options >> module preferences
 * Dashboard Display Options
 * Admin / Template / dashboard.html
 * Main Page Display(1) or not display(0)
 * @return bool
 */
if (!defined('XC_ADMINDASHBOARD_UINFO')) {
    define('XC_ADMINDASHBOARD_UINFO', 1);
}

    /*
    * Main Page System Info
    */
    if (!defined('XC_ADMINDASHBOARD_SYSTEMINFO')) {
        define('XC_ADMINDASHBOARD_SYSTEMINFO', 1);
    }
    /*
    * PHP Settings - requirements
    */
    if (!defined('XC_ADMINDASHBOARD_PHPSETTING')) {
        define('XC_ADMINDASHBOARD_PHPSETTING', 1);
    }
    /*
    * Full PHP Info
    */
    if (!defined('XC_ADMINDASHBOARD_PHPINFO')) {
        define('XC_ADMINDASHBOARD_PHPINFO', 0);
    }
    /*
    * Waiting (pending) contents
    */
    if (!defined('XC_ADMINDASHBOARD_WAITING')) {
        define('XC_ADMINDASHBOARD_WAITING', 0);
    }
    /*
    * !TODO - Comments
    */
    if (!defined('XC_ADMINDASHBOARD_COMMENTS')) {
        define('XC_ADMINDASHBOARD_COMMENTS', 0);
    }
    /* Admin blocks only 'admin_theme.html'. Display (1) or not display (0).
    *  @return bool
    */
    // Display on main page the admin block "online-info"
    if (!defined('XC_ADMINBLOCK_ONLINEINFO')) {
        define('XC_ADMINBLOCK_ONLINEINFO', 1);
    }
    // Display on sidemenu the admin block "waiting-contents"
    if (!defined('XC_ADMINBLOCK_WAITING')) {
    define('XC_ADMINBLOCK_WAITING', 0);
    }
    // Display on sidemenu the admin block "system info"
    if (!defined('XC_ADMINBLOCK_SYSINFO')) {
        define('XC_ADMINBLOCK_SYSINFO', 0);
    }
    /*
    *  TODO - Display on sidemenu the admin block "admin-theme"
    *  !ADMINTHEME - display(1) or not display(0): admin-theme select block
    */
    if (!defined('XC_ADMINBLOCK_ADMINTHEME')) {
        define('XC_ADMINBLOCK_ADMINTHEME', 0);
    }

        /*
         * Add custom language constants here!
         * !TODO - Move to catalog - new language constants
        */
//		if (!defined('_MB_LEGACY_ADMINTHEMESELECT')) {
//		define('_MB_LEGACY_ADMINTHEMESELECT', "A-Theme Changer");
//        }


class Legacy_AdminDashboard extends XCube_ActionFilter
{
    public function preBlockFilter()
    {

        $root=&XCube_Root::getSingleton();

        $root->mDelegateManager->add("Legacypage.Admin.SystemCheck", "Legacy_AdminDashboard::AdminDashboardSystem", XCUBE_DELEGATE_PRIORITY_NORMAL+1);

        /*
        * Note! Switch from RenderSystem to AdminRenderSystem
        * Ref. Legacy/kernel/Legacy_AdminControllerStrategy
        */
        if ($root->mController->_mStrategy && get_class($root->mController->_mStrategy) === 'Legacy_AdminControllerStrategy') {
            $this->mController->_mStrategy->mSetupBlock->add( [$this, 'AdminSetupBlock'] );
        }

    }

	// If you want to add any new block, please customize this function !
	// Refer to legacy/admin/blocks!
	public function AdminSetupBlock()
	{

		// online info block
		if(XC_ADMINBLOCK_ONLINEINFO && file_exists(XOOPS_LEGACY_PATH . '/admin/blocks/AdminOnlineInfo.class.php')) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminOnlineInfo.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminOnlineInfo();
            }
        // system info block
		if(XC_ADMINBLOCK_SYSINFO && file_exists(XOOPS_LEGACY_PATH . '/admin/blocks/AdminSystemInfo.class.php')) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminSystemInfo.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminSystemInfo();
            }
        // waiting contents block
		if(XC_ADMINBLOCK_WAITING && file_exists(XOOPS_LEGACY_PATH . '/admin/blocks/AdminWaiting.class.php')) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminWaiting.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminWaiting();
            }
		// admin-theme changer block
		if(XC_ADMINBLOCK_ADMINTHEME && file_exists(XOOPS_LEGACY_PATH . '/admin/blocks/AdminThemeSelect.class.php') && file_exists(XOOPS_LEGACY_PATH . '/admin/preload/AdminThemeSelectPreload.class.php')) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminThemeSelect.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminThemeSelect();
            }

		// Add your custom block here!

	}


    public static function AdminDashboardSystem()
    {

        $root =& XCube_Root::getSingleton();

        if(XC_ADMINDASHBOARD_UINFO) {
            /*
            * Select the UIType Display Option
            * 0 - output is direct
            * 1 - output with template "legacy_dummy.html"
            * 2 - output with template "dashboard.html"
            */
            $uitype = 2;

            // Customize the design of html/admin.php
            if ( $uitype == 0 ) {
                $uiadmin = '<b>Welcome to XOOPSCube Admin!</b><br>Have a nice time!';
                echo $uiadmin;
            }

            // Customize the design of html/admin.php
            elseif ( $uitype == 1 ) {
                $uiadminhtml = '<b>Welcome to XOOPSCube !</b>
                                <br>Have a nice and happy time!
                                <br><b>Output with template "legacy_dummy.html"</b>!';
                $attributes = [];
                $attributes['dummy_content'] = $uiadminhtml;
                $template = XOOPS_LEGACY_PATH. '/templates/legacy_dummy.html';
                self::display_message($attributes, $template, $return = false);
            }

            // Customize the design of dashboard.html
            elseif ( $uitype == 2 ) {

                if ( file_exists(XOOPS_LEGACY_PATH . '/admin/templates/dashboard.html') ) {

                    $udb_title = 'XCL Documentation';
                    $udb_msg = [];
                    $udb_msg[] = 'XCL provides an out-of-the-box internal search function to find admin features.';
                    $udb_msg[] = 'Separate documentation is available for each module.';

                    $attributes = [];
                    $attributes['title'] = $udb_title;
                    $attributes['messages'] = $udb_msg;

                    $template = XOOPS_LEGACY_PATH. '/admin/templates/dashboard.html';

                    self::display_message($attributes, $template, $return = false);

                }
            }

        }

        /**
         * ADMIN System Info
         */
        if (XC_ADMINDASHBOARD_SYSTEMINFO) {

            $systeminfo_message = [];

            if (defined('XOOPS_DISTRIBUTION_VERSION')) {
                $systeminfo_message[] = 'Distribution : ' .XOOPS_DISTRIBUTION_VERSION;
            }
            $systeminfo_message[] = '<div class="ui-card-2-col">';
            $systeminfo_message[] = '<h5>📦 ' . _AD_LEGACY_XCLEGACYVERSION. '<span class="badge">' .XOOPS_VERSION. '</span></h5>';
            $systeminfo_message[] = '<p>' ._MD_AM_DTHEME. '<span class="badge">' .$root->mContext->mXoopsConfig['theme_set']. '</span></p>';
            $systeminfo_message[] = '<p>' ._MD_AM_DTPLSET. '<span class="badge">' .$root->mContext->mXoopsConfig['template_set']. '</span></p>';
            $systeminfo_message[] = '<p>' ._MD_AM_LANGUAGE. '<span class="badge">' .$root->mContext->mXoopsConfig['language']. '</span></p>';

                $debugmode = (int)($root->mContext->mXoopsConfig['debug_mode']);
                if ($debugmode == 0) {
                    $systeminfo_message[] = _MD_AM_DEBUGMODE. ' <span class="badge"> ' ._MD_AM_DEBUGMODE0. '</span>';
                } elseif ($debugmode == 1) {
                    $systeminfo_message[] = _MD_AM_DEBUGMODE. ' <span class="badge"> ' ._MD_AM_DEBUGMODE1. '</span>';
                } elseif ($debugmode == 2) {
                    $systeminfo_message[] = _MD_AM_DEBUGMODE. ' <span class="badge"> ' ._MD_AM_DEBUGMODE2. '</span>';
                } elseif ($debugmode == 3) {
                    $systeminfo_message[] = _MD_AM_DEBUGMODE. ' <span class="badge"> ' ._MD_AM_DEBUGMODE3. '</span>';
                }

            $systemconfig = [];

            $systemconfig['phpversion']   = phpversion();
                        $db               = &$root->mController->getDB();
                        $result           = $db->query('SELECT VERSION()');
                        [$mysqlversion]   = $db->fetchRow($result);
            $systemconfig['mysqlversion'] = $mysqlversion;
            $systemconfig['os']           = substr(php_uname(), 0, 7);
            $systemconfig['server']       = xoops_getenv('SERVER_SOFTWARE');
            $systemconfig['useragent']    = xoops_getenv('HTTP_USER_AGENT');

            $systeminfo_message[] = '<p>'._AD_LEGACY_OS. '<span class="badge">' .$systemconfig['os']. '</span></p>';
            $systeminfo_message[] = '<p>'._AD_LEGACY_SERVER. '<span class="badge">' .$systemconfig['server']. '</span></p>';
            $systeminfo_message[] = '<p>'._AD_LEGACY_USERAGENT. '<span class="badge" style="white-space:revert;">' .$systemconfig['useragent']. '</span></p>';
            $systeminfo_message[] = '<p>'._AD_LEGACY_PHPVERSION. '<span class="badge">' .$systemconfig['phpversion']. '</span></p>';
            $systeminfo_message[] = '<p>'._AD_LEGACY_MYSQLVERSION. '<span class="badge">' .$systemconfig['mysqlversion']. '</span></p>';
            $systeminfo_message[] = "</div>";

                xoops_result($systeminfo_message, _AD_LEGACY_SYSTEMINFO);
        }

        /*
        *  PHP Settings
        */
        if (XC_ADMINDASHBOARD_PHPSETTING) {

            $phpsetting_message = [];
            $d_open ="<div data-layout='row sm-column'><div data-self='size-1of3 sm-half'>";
            $d_close= "</div>";
            $badge = "<div><span class='badge'>";
            $green ="<div><span class='badge' style='color:var(--color-green)'>";
            $red = "<div><span class='badge' style='color:var(--color-red)'>";
            $closed = "</span></div></div>";
            $on = $green . _AD_LEGACY_PHPSETTING_ON . $closed;
            $off = $red . _AD_LEGACY_PHPSETTING_OFF . $closed;
            $yes = $green . _YES . $closed;
            $no = $red . _NO . $closed;

            $phpsetting_message[] = '<div class="ui-card-2-col">';
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_SM . $d_close . (ini_get('safe_mode')? $on : $off);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_MET. $d_close . (ini_get('max_execution_time')? $badge . ini_get('max_execution_time') . " sec." . $closed : $off);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_ML. $d_close . (ini_get('memory_limit')? $badge . ini_get('memory_limit') . "b" . $closed : $off);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_DE . $d_close . (ini_get('display_errors')? $on : $off);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_SOT. $d_close . (ini_get('short_open_tag')? $on : $off);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_FU. $d_close . (ini_get('file_uploads')?
            $green . _AD_LEGACY_PHPSETTING_ON . "</span><br>"
            . "<span class='badge'>" . _AD_LEGACY_PHPSETTING_FU_UMAX . ini_get('upload_max_filesize') . "</span>"
            . "<span class='badge'>" . _AD_LEGACY_PHPSETTING_FU_PMAX . ini_get('post_max_size') . $closed
            : $red . _AD_LEGACY_PHPSETTING_OFF . $closed);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_OB . $d_close . (ini_get('output_buffering')? $on : $off);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_OBD  . $d_close . (ini_get('open_basedir')? $on : $off);
            // Recommended on << off
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_UFO . $d_close . (ini_get('allow_url_fopen')? $red ._AD_LEGACY_PHPSETTING_ON." (recommended OFF)" . $closed : $green ._AD_LEGACY_PHPSETTING_OFF. $closed);
            // PHP EXTENSIONS
            $phpsetting_message[] = $d_open . 'OpenSSL encrypt' . $d_close . (function_exists('openssl_encrypt')? $yes : $no);
            $phpsetting_message[] = $d_open . 'Open SSL' . $d_close . (extension_loaded('openssl')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_CRL . $d_close . (extension_loaded('curl')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_DOM . $d_close . (extension_loaded('dom')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_EXIF . $d_close . (extension_loaded('exif')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_GTXT. $d_close . (extension_loaded('gettext')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_JSON. $d_close . (extension_loaded('json')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_MB . $d_close . (extension_loaded('mbstring')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_SOAP . $d_close . (extension_loaded('soap')? $yes : $no);
            $phpsetting_message[] = $d_open . 'Zip/Tar PharData class' . $d_close . (class_exists('PharData')? $yes : $no);
            $phpsetting_message[] = $d_open . 'ZipArchive class' . $d_close . (class_exists('ZipArchive')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_ZLIB . $d_close . (extension_loaded('zlib')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_XML. $d_close . (extension_loaded('xml')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_ICONV . $d_close . (function_exists('iconv')? $yes : $no);
            $phpsetting_message[] = $d_open . _AD_LEGACY_PHPSETTING_GD . $d_close . (function_exists('gd_info')? $yes : $no);


            /* Check GD version */
            if (function_exists('gd_info')) {
                $gd_info = gd_info() ;
                $phpsetting_message[] =  $d_open . "GD version" . $d_close . $badge . "{$gd_info['GD Version']}" . $closed;
            }
            if (function_exists('imagecreatetruecolor')) {
                $phpsetting_message[] =  $d_open . 'GdImage' . $d_close . $badge . "Image create Truecolor" . $closed;
            }
            if( extension_loaded('imagick') || class_exists("Imagick") ){
                /* Check Imagick */
                $imagick_version = Imagick::getVersion();
                $imagick_version_number = $imagick_version['versionNumber'];
                $imagick_version_string = $imagick_version['versionString'];
                $phpsetting_message[] = $d_open .'Imagick'. $d_close . $green ."Image create Truecolor" . $closed;
                $phpsetting_message[] = $d_open .'Imagick version number'. $d_close .$badge .$imagick_version_number . $closed;
//                $phpsetting_message[] = $d_open .'Imagick version'. $d_close .$badge .$imagick_version_string . $closed;
            }
            $phpsetting_message[] = '</div>';

                xoops_result($phpsetting_message, _AD_LEGACY_PHPSETTING);
        }


        /*
        * Admin Dashboard Block Waiting
        */
        if (XC_ADMINDASHBOARD_WAITING) {

            $modules = [];
            XCube_DelegateUtils::call('Legacyblock.Waiting.Show', new XCube_Ref($modules));

            $attributes = [];
            $attributes['block']['modules'] = $modules;

            $template = self::getTemplate('legacy_admin_block_waiting.html', 'blocks/');

            $result = self::display_message($attributes, $template, $return = true);
            xoops_result($result, _MI_LEGACY_BLOCK_WAITING_NAME);
        }


        /*
        * !TODO - Admin Dashboard Block Comments
        */
        if (XC_ADMINDASHBOARD_COMMENTS) {

            $modules = [];
            XCube_DelegateUtils::call('b_legacy_comments_show', new XCube_Ref($modules));

            $attributes = [];
            $attributes['block']['modules'] = $modules;

            $template = self::getTemplate('legacy_block_comments.html', 'blocks/');

            $result = self::display_message($attributes, $template, $return = true);

            xoops_result($result, _MB_LEGACY_DISPLAYC);
        }


        /*
        * Admin Dashboard PHP Information
        */
        if (XC_ADMINDASHBOARD_PHPINFO) {

            ob_start();
            phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
            $phpinfo = ob_get_clean();

            preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
            $output = preg_replace('#<table#', '<table class="outer""', $output[1][0]);
            $output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
            $output = preg_replace('#border="0" cellpadding="3" width="600"#', '', $output);
            $output = preg_replace('#<hr>#', '', $output);
            $output = preg_replace('#class="e"#', 'class="even"', $output);
            $output = preg_replace('#class="h"#', 'class="odd"', $output);
            $output = preg_replace('#class="v"#', 'class="even"', $output);
            $output = preg_replace('#class="p"#', 'class="odd"', $output);
            $output = str_replace(['<div class="center">', '</div>'], '', $output);

            $attributes = [];
            $attributes['dummy_content'] = $output;

            $template = XOOPS_LEGACY_PATH. '/templates/legacy_dummy.html';

            self::display_message($attributes, $template, $return = false);
        }

    }


    /**
     * Admin Dashboard Display Message
     * @param array $attributes
     * @param string $template
     * @param bool $return
     * @return mixed
     */
    public static function display_message(array $attributes = [], string $template= '', bool $return = false)
    {
        $root =& XCube_Root::getSingleton();

        $renderSystem =& $root->getRenderSystem($root->mContext->mBaseRenderSystemName);

        $renderTarget =& $renderSystem->createRenderTarget('main');
        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName($template);

        foreach (array_keys($attributes) as $attribute) {
            $renderTarget->setAttribute($attribute, $attributes[$attribute]);
        }

        $renderSystem->render($renderTarget);
        if ($return == true) {
            return $renderTarget->getResult();
        }

        print $renderTarget->getResult();
    }


    /**
     * Admin Dashboard Get Template
     * @param $file
     * @param null $prefix
     * @return string
     */
    private static function getTemplate($file, $prefix = null)
    {
        $infoArr = Legacy_get_override_file($file, $prefix);
        if ($prefix) {
            $file = $prefix . $file;
        }

        if ($infoArr['theme'] !== null && $infoArr['dirname'] !== null) {
            return XOOPS_THEME_PATH . '/' . $infoArr['theme'] . '/modules/' . $infoArr['dirname'] . '/' . $file;
        }

        if ($infoArr['theme'] !== null) {
            return XOOPS_THEME_PATH . '/' . $infoArr['theme'] . '/' . $file;
        }

        if ($infoArr['dirname'] !== null) {
            return XOOPS_MODULE_PATH . '/' . $infoArr['dirname'] . '/admin/templates/' . $file;
        }

        return LEGACY_ADMIN_RENDER_FALLBACK_PATH . '/' . $file;
    }
}


