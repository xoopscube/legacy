<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.1
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

$writeok = array('uploads/', 'mainfile.php');
$error = false;
echo '<h2>wizard/install_modcheck.inc</h2>';

clearstatcache();

foreach ($writeok as $wok) {
    if (!is_dir('../'.$wok)) {
        if (file_exists('../'.$wok)) {
            @chmod('../'.$wok, 0666);
            if (! is_writeable('../'.$wok)) {
                $wizard->addArray('checks', _NGIMG.sprintf(_INSTALL_L83, $wok));
                $error = true;
            } else {
                $wizard->addArray('checks', _OKIMG.sprintf(_INSTALL_L84, $wok));
            }
        }
    } else {
        @chmod('../'.$wok, 0777);
        if (! is_writeable('../'.$wok)) {
            $wizard->addArray('checks', _NGIMG.sprintf(_INSTALL_L85, $wok));
            $error = true;
        } else {
            $wizard->addArray('checks', _OKIMG.sprintf(_INSTALL_L86, $wok));
        }
    }
}

if (! $error) {
    $wizard->assign('message', _INSTALL_L87);
    $wizard->assign( 'message', '<div class="confirmOk">install_modcheck.inc</div>' );
} else {
    $wizard->assign('message', _INSTALL_L46);
    $wizard->setReload(true);
}

$wizard->render('install_modcheck.tpl.php');
