<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

// checking XOOPS_ROOT_PATH and XOOPS_URL
include_once '../mainfile.php';

$writeok = ['cache/', 'templates_c/', 'templates_c/xelfinder/'];
$error = false;
echo '<h2>wizard/install_modcheck_trust.inc</h2>';

clearstatcache();

foreach ($writeok as $wok) {

    // try to create this directory if it doesn't exist
    is_dir(XOOPS_TRUST_PATH . '/' . $wok) || (mkdir(XOOPS_TRUST_PATH . '/' . $wok, 0777, true) && is_dir(XOOPS_TRUST_PATH . '/' . $wok));
    $wokWritable = false;
    $permissions = fileperms(XOOPS_TRUST_PATH . '/' . $wok);
    //$permissions = fileperms('../' . $wok);
    if ($wok && is_writable(XOOPS_TRUST_PATH . '/' . $wok)) {

        $tempFile = tempnam(XOOPS_TRUST_PATH . '/' . $wok, 'tmp');
        if ($tempFile !== false) {
            $res = file_put_contents($tempFile, 'test');

            $wokWritable = $res !== false;
            @unlink($tempFile);
            $fperm = substr(sprintf('%o', $permissions), -4); //output 0777
            $wizard->addArray('checks', _OKIMG. '<code>' .$fperm.'</code>' . sprintf(_INSTALL_L86, XOOPS_TRUST_PATH. '/'.$wok));
        } else {
            $fperm = substr(sprintf('%o', $permissions), -4); //output 0777
            $wizard->addArray('checks', _NGIMG. '<span style="color:#e43140">' .$fperm.'</span>' .sprintf(_INSTALL_L85, XOOPS_TRUST_PATH. '/'.$wok));
            $error = true;
        }
    }

//    if (!is_dir(XOOPS_TRUST_PATH. '/'. $wok)) {
//        if (file_exists(XOOPS_TRUST_PATH. '/'. $wok)) {
//            @chmod(XOOPS_TRUST_PATH. '/'. $wok, 0666);
//            if (! is_writeable(XOOPS_TRUST_PATH. '/'. $wok)) {
//                $wizard->addArray('checks', _NGIMG.sprintf(_INSTALL_L83, $wok));
//                $error = true;
//            } else {
//                $wizard->addArray('checks', _OKIMG.sprintf(_INSTALL_L84, $wok));
//            }
//        }
//    } else {
//        @chmod(XOOPS_TRUST_PATH. '/'. $wok, 0777);
//        $fperm = substr(sprintf('%o', $permissions), -4); //output 0777
//        if (! is_writeable(XOOPS_TRUST_PATH. '/'. $wok)) {
//
//        } else {
//            $wizard->addArray('checks', _OKIMG. '<code>' .$fperm.'</code>' . sprintf(_INSTALL_L86, XOOPS_TRUST_PATH. '/'.$wok));
//        }
//    }
}

if (! $error) {
    $wizard->assign('message', '<div class="confirmOk">'._INSTALL_L87.'</div>');
    //$wizard->assign( 'message', '<div class="confirmOk">install_modcheck_trust.inc</div>' );
} else {
    $wizard->assign('message', '<div class="confirmError">'._INSTALL_L46.'</div>');
    $wizard->setReload(true);
}

//install_modcheck_trust_mkdir($directory);

$wizard->render('install_modcheck.tpl.php');

function install_modcheck_trust_mkdir(/*** string ***/ $directory)
{
    if (! is_dir($directory)) {
        umask(0);
        mkdir($directory, 0777);
    }
}
