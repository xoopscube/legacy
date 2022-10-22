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
    * PHP Settings
    */
    if (!defined('XC_ADMINDASHBOARD_PHPSETTING')) {
        define('XC_ADMINDASHBOARD_PHPSETTING', 1);
    }
    /*
    * Waiting (pending) contents
    */
    if (!defined('XC_ADMINDASHBOARD_WAITING')) {
        define('XC_ADMINDASHBOARD_WAITING', 1);
    }
    /*
    * !TODO - Comments
    */
    if (!defined('XC_ADMINDASHBOARD_COMMENTS')) {
        define('XC_ADMINDASHBOARD_COMMENTS', 0);
    }
    /*
    * Full PHP Info
    */
    if (!defined('XC_ADMINDASHBOARD_PHPINFO')) {
        define('XC_ADMINDASHBOARD_PHPINFO', 0);
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
    *  Display on sidemenu the admin block "admin-theme"
    *  !TODO - ADMINTHEME - display(1) or not display(0): admin-theme select block
    */
    if (!defined('XC_ADMINBLOCK_ADMINTHEME')) {
        define('XC_ADMINBLOCK_ADMINTHEME', 0);
    }

        /*
        * !TODO - Move to catalog - new language constants
        */
		if (!defined('_MB_LEGACY_XCLEGACYVERSION')) {
		define('_MB_LEGACY_XCLEGACYVERSION', "XCL Version");
		}
		if (!defined('_MB_LEGACY_XCVERSION')) {
		define('_MB_LEGACY_XCVERSION', "XCube Version");
		}
		if (!defined('_MB_LEGACY_SYSTEMINFO')) {
		define('_MB_LEGACY_SYSTEMINFO', "Site/System Info");
		}
		if (!defined('_MB_LEGACY_PHPVERSION')) {
		define('_MB_LEGACY_PHPVERSION', "PHP Version");
		}
		if (!defined('_MB_LEGACY_MYSQLVERSION')) {
		define('_MB_LEGACY_MYSQLVERSION', "MYSQL Version");
		}
		if (!defined('_MB_LEGACY_OS')) {
		define('_MB_LEGACY_OS', "Operating System");
		}
		if (!defined('_MB_LEGACY_SERVER')) {
		define('_MB_LEGACY_SERVER', "Server");
		}
		if (!defined('_MB_LEGACY_USERAGENT')) {
		define('_MB_LEGACY_USERAGENT', "User Agent");
		}
		if (!defined('_MB_LEGACY_ONLINEINFO')) {
		define('_MB_LEGACY_ONLINEINFO', "Who's online");
		}
		if (!defined('_MB_LEGACY_ADMINTHEMESELECT')) {
		define('_MB_LEGACY_ADMINTHEMESELECT', "A-Theme Changer");
        }

		// Add custom language constants here!


class Legacy_AdminDashboard extends XCube_ActionFilter
{
    /**
     * XCL global constants
     * @param bool $is_report
     */
//    function xpress_config_from_xoops_view($is_report = false)
//    {
//        global $xoopsUserIsAdmin,$xoopsUser;
//
//        $user_groups = $xoopsUser->getGroups();
//        $is_admin_group = in_array ('1', $user_groups, true);
//
//        require_once dirname (__FILE__, 2) .'/class/config_from_xoops.class.php' ;
//        $xoops_config = new ConfigFromXoops;
//        if ($is_report) {
//            echo '<h3>' . _AM_XP2_XOOPS_CONFIG_INFO . '</h3>';
//            xpress_config_report_view();
//            echo "<br />\n";
//        } else {
//            echo '<h3>' . _AM_XP2_XOOPS_CONFIG_INFO . '</h3>';
//
//            if ($xoopsUserIsAdmin && $is_admin_group){
//                xpress_config_nomal_view();
//            } else {
//                xpress_config_report_view();
//            }
//
//            echo '<hr>';
//        }
//    }

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

            elseif ( $uitype == 2 ) {

                // Customize the design of dashboard.html
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
            $systeminfo_message[] = _AD_LEGACY_XCLEGACYVERSION. ' : ' .XOOPS_VERSION;
            $systeminfo_message[] = _MD_AM_DTHEME. ' : ' .$root->mContext->mXoopsConfig['theme_set'];
            $systeminfo_message[] = _MD_AM_DTPLSET. ' : ' .$root->mContext->mXoopsConfig['template_set'];
            $systeminfo_message[] = _MD_AM_LANGUAGE. ' : ' .$root->mContext->mXoopsConfig['language'];

                $debugmode = (int)($root->mContext->mXoopsConfig['debug_mode']);
                if ($debugmode == 0) {
                    $systeminfo_message[] = _MD_AM_DEBUGMODE. ' : ' ._MD_AM_DEBUGMODE0;
                } elseif ($debugmode == 1) {
                    $systeminfo_message[] = _MD_AM_DEBUGMODE. ' : ' ._MD_AM_DEBUGMODE1;
                } elseif ($debugmode == 2) {
                    $systeminfo_message[] = _MD_AM_DEBUGMODE. ' : ' ._MD_AM_DEBUGMODE2;
                } elseif ($debugmode == 3) {
                    $systeminfo_message[] = _MD_AM_DEBUGMODE. ' : ' ._MD_AM_DEBUGMODE3;
                }

            $systemconfig = [];

            $systemconfig['phpversion'] = phpversion();

                $db = &$root->mController->getDB();
                $result = $db->query('SELECT VERSION()');
                [$mysqlversion] = $db->fetchRow($result);

            $systemconfig['mysqlversion'] = $mysqlversion;
            $systemconfig['os'] = substr(php_uname(), 0, 7);
            $systemconfig['server'] = xoops_getenv('SERVER_SOFTWARE');
            $systemconfig['useragent'] = xoops_getenv('HTTP_USER_AGENT');

            $systeminfo_message[] = _AD_LEGACY_OS. ' : ' .$systemconfig['os'];
            $systeminfo_message[] = _AD_LEGACY_SERVER. ' : ' .$systemconfig['server'];
            $systeminfo_message[] = _AD_LEGACY_USERAGENT. ' : ' .$systemconfig['useragent'];
            $systeminfo_message[] = _AD_LEGACY_PHPVERSION. ' : ' .$systemconfig['phpversion'];
            $systeminfo_message[] = _AD_LEGACY_MYSQLVERSION. ' : ' .$systemconfig['mysqlversion'];

            xoops_result($systeminfo_message, _AD_LEGACY_SYSTEMINFO, 'tips');
        }


        /*
        *  PHP Settings
        */
        if (XC_ADMINDASHBOARD_PHPSETTING) {

            $phpsetting_message = [];

            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_SM. ' : ' .(ini_get('safe_mode')? "<span style=color:red>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:green>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_MET. ' : ' .(ini_get('max_execution_time')? ini_get('max_execution_time')." sec." : _AD_LEGACY_PHPSETTING_OFF);
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_ML. ' : ' .(ini_get('memory_limit')? ini_get('memory_limit')."b" : _AD_LEGACY_PHPSETTING_OFF);
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_DE. ' : ' .(ini_get('display_errors')? "<span style=color:green>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:red>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_SOT. ' : ' .(ini_get('short_open_tag')? "<span style=color:green>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:red>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_FU. ' : ' .(ini_get('file_uploads')? _AD_LEGACY_PHPSETTING_ON." ( "._AD_LEGACY_PHPSETTING_FU_UMAX.ini_get('upload_max_filesize').", "._AD_LEGACY_PHPSETTING_FU_PMAX.ini_get('post_max_size')." )" : _AD_LEGACY_PHPSETTING_OFF);
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_MQ. ' : ' .(ini_get('magic_quotes_gpc')? "<span style=color:green>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:red>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_RG. ' : ' .(ini_get('register_globals')? "<span style=color:red>" ._AD_LEGACY_PHPSETTING_ON." (recommended OFF)</span>" : "<span style=color:green>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_OB. ' : ' .(ini_get('output_buffering')? "<span style=color:red>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:green>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_OBD. ' : ' .(ini_get('open_basedir')? "<span style=color:green>" ._AD_LEGACY_PHPSETTING_ON."</span>" : "<span style=color:red>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_UFO. ' : ' .(ini_get('allow_url_fopen')? "<span style=color:red>" ._AD_LEGACY_PHPSETTING_ON." (recommended OFF)</span>" : "<span style=color:green>" ._AD_LEGACY_PHPSETTING_OFF. "</span>");

            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_DOM. ' : ' .(extension_loaded('dom')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_EXIF. ' : ' .(extension_loaded('exif')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_GTXT. ' : ' .(extension_loaded('gettext')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_JSON. ' : ' .(extension_loaded('json')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_XML. ' : ' .(extension_loaded('xml')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_CRL. ' : ' .(extension_loaded('curl')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_ZLIB. ' : ' .(extension_loaded('zlib')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_SOAP. ' : ' .(extension_loaded('soap')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_MB. ' : ' .(extension_loaded('mbstring')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_ICONV. ' : ' .(function_exists('iconv')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");
            $phpsetting_message[] = _AD_LEGACY_PHPSETTING_GD. ' : ' .(function_exists('gd_info')? "<span style=color:green>" ._YES. "</span>" : "<span style=color:red>" ._NO. " (required by recent modules)</span>");

            if (function_exists('gd_info')) {
                $gd_info = gd_info() ;
                $phpsetting_message[] =  "GD Version: {$gd_info['GD Version']}" ;
            }

            if (function_exists('imagecreatetruecolor')) {
                $phpsetting_message[] = _AD_LEGACY_PHPSETTING_GD. ' Image create Truecolor';
            }
            if( extension_loaded('imagick') || class_exists("Imagick") ){
                /*do Imagick*/
                $imagick_version = Imagick::getVersion();
                $imagick_version_number = $imagick_version['versionNumber'];
                $imagick_version_string = $imagick_version['versionString'];
                $phpsetting_message[] = 'Imagick: Image create Truecolor <span style="color:green">' ._YES. '</span>';
                $phpsetting_message[] = 'Imagick version number'.$imagick_version_number;
                $phpsetting_message[] = 'Imagick version'.$imagick_version_string;
            }



            echo '<hr>';

            echo '<h2>Test for versions and locations of ImageMagick</h2>';
            echo '<b>Path: </b> convert<br>';

            function alist ($array) {  //This function prints a text array as an html list.
                $alist = "<ul>";
                for ($i = 0; $i < sizeof($array); $i++) {
                    $alist .= "<li>$array[$i]";
                }
                $alist .= "</ul>";
                return $alist;
            }


            exec("convert -version", $out, $rcode); //Try to get ImageMagick "convert" program version number.
            echo "Version return code is $rcode <br>"; //Print the return code: 0 if OK, nonzero if error.
            echo alist($out); //Print the output of "convert -version"
            echo '<br>';
            echo '<b>This should test for ImageMagick version 5.x</b><br>';
            echo '<b>Path: </b> /usr/bin/convert<br>';

            exec("/usr/bin/convert -version", $out, $rcode); //Try to get ImageMagick "convert" program version number.
            echo "Version return code is $rcode <br>"; //Print the return code: 0 if OK, nonzero if error.
            echo alist($out); //Print the output of "convert -version"

            echo '<br>';
            echo '<b>This should test for ImageMagick version 6.x</b><br>';
            echo '<b>Path: </b> /usr/local/bin/convert<br>';

            exec("/usr/local/bin/convert -version", $out, $rcode); //Try to get ImageMagick "convert" program version number.
            echo "Version return code is $rcode <br>"; //Print the return code: 0 if OK, nonzero if error.
            echo alist($out); //Print the output of "convert -version";


            echo "<pre>";
            system("type -a convert");
            echo "</pre>";


            xoops_result($phpsetting_message, _AD_LEGACY_PHPSETTING, 'tips');
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
    public static function display_message(array $attributes = [], string $template= '', bool $return)
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


