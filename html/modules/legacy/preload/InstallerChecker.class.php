<?php
/**
 *
 * @package Legacy
 * @version $Id: InstallerChecker.class.php,v 1.4 2008/09/25 15:12:43 kilica Exp $
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    https://github.com/xoopscube/xcl/blob/master/docs/GPL_V2.txt
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

if (!defined('LEGACY_INSTALLERCHECKER_ACTIVE')) {
    define('LEGACY_INSTALLERCHECKER_ACTIVE', true);
}

require_once XOOPS_ROOT_PATH . '/core/XCube_Utils.class.php';
/**
 * This filter checks whether the install-wizard directory is removed.
 * If it is not removed yet, this filter warns to remove the install-wizard
 * directory.
 */
class Legacy_InstallerChecker extends XCube_ActionFilter
{

    public function preBlockFilter()
    {


        if ( LEGACY_INSTALLERCHECKER_ACTIVE == true && is_dir(XOOPS_ROOT_PATH . '/install' ) ) {

            $root =& XCube_Root::getSingleton();
            $root->mLanguageManager->loadModuleMessageCatalog('legacy');
            $xoopsConfig = $root->mContext->mXoopsConfig;


            // Directory /Install
            // File mainfile.php
            // Action controls
            $delete_path    = XOOPS_ROOT_PATH . '/install';
            $mainfile       = XOOPS_ROOT_PATH . '/mainfile.php';
            $pre_disable    = XOOPS_ROOT_PATH . '/preload/disabled/AntiInstallerChecker.class.php';
            $pre_active     = XOOPS_ROOT_PATH . '/preload/AntiInstallerChecker.class.php';
            $btn_chmod      = 'chmod';
            $btn_delete     = 'Delete';
            $btn_preload    = 'Activate';

            $permissions = fileperms( $mainfile );
            $fperm = substr(sprintf( '%o', $permissions), -4 ); //output 0777
            if ( is_writable( $mainfile ) ) {
                $is_chmod = true;
            } else{
                $is_chmod = false;
            }

            // PRELOAD
            // Copy preload from disabled directory
             function preloadActive( string $pre_disable, string $pre_active ) {

                if( !copy( $pre_disable, $pre_active ) ) {
                    echo "File can't be copied! \n";
                } else {
                    echo "File has been copied! \n";
                }

            } 


            // ACTION CONTROLS
            if( isset( $_POST[$btn_chmod] ) ) {
                chmod( $mainfile, 0444 );
                //echo "<meta http-equiv='refresh' content='0'>";  refresh permissions
                header("Refresh: 0");
            }

            if( isset( $_POST[$btn_delete] ) ) {
                (new XCube_Utils)->recursiveRemove( $delete_path );
                header("Refresh: 0");
            }

            if( isset( $_POST[$btn_preload] ) ) {
                preloadActive( $pre_disable, $pre_active );
                //(new XCube_Utils)->preloadActive( $pre_disable, $pre_active );
                header("Refresh: 0");
            }

            if( isset($_POST['submit'] ) ) {
                header("Refresh: 0");
            }

            // Directory /Install Warning
            if ( is_dir( $delete_path ) ) {
                $install_warn_dir = XCube_Utils::formatString(_MD_LEGACY_MESSAGE_INSTALL_COMPLETE_WARNING, XOOPS_ROOT_PATH . '/install');
            }



            // RENDER view
            require_once XOOPS_ROOT_PATH . '/class/template.php';

            $xoopsTpl =new XoopsTpl();

            $xoopsTpl->assign(
                [
                    'xoops_sitename'        => htmlspecialchars($xoopsConfig['sitename']),
                    'xoops_themecss'        => xoops_getcss(),
                    'xoops_imageurl'        => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/',
                    'install_confirm'       => XCube_Utils::formatString(_MD_LEGACY_MESSAGE_INSTALL_COMPLETE_CONFIRM, XOOPS_ROOT_PATH . '/install'),
                    'install_warning_dir'   => $install_warn_dir,
                    'install_warning_tip'   => XCube_Utils::formatString(_WARN_INSTALL_TIP),
                    'is_chmod'              => $is_chmod,
                    'fperm'                 => $fperm,
                    'btn_chmod'             => $btn_chmod,
                    'btn_delete'            => $btn_delete,
                    'btn_preload'           => $btn_preload,
                ]
            );

            $xoopsTpl->compile_check = true;

            // Filebase template with absolute file path
            $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/legacy/templates/legacy_install_completed.html');
            exit();
        }
    }
}
