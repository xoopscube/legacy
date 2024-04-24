<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica
 * @copyright  Copyright 2005-2024 XOOPSCube Project
 * @license    GPL 2.0
 */

$writeok = ['mainfile.php', 'uploads/'];
$error = false;

clearstatcache();

foreach ($writeok as $wok) {
    $permissions = fileperms('../' . $wok);

    if (is_dir('../' . $wok)) {
        // Force chmod
        @chmod('../' . $wok, 0777);
        $fperm = substr(sprintf('%o', $permissions), -4); //output 0777

        if (file_exists('../' . $wok) && is_writable('../' . $wok)) {
            $wizard->addArray('checks', _OKIMG . '<code>' .$fperm.'</code><code>'.$wok.'</code> '. sprintf( _INSTALL_L86,'<p class="data">'. $wok .'</p>') );
        } else {
            $wizard->addArray('checks', _NGIMG . '<code style="color:#ff6633">' .$fperm.'</code><code>'.$wok.'</code> '. sprintf( _INSTALL_L85, '<p class="data">'. $wok .'</p>'));
            $wizard->setBack( [ 'start', _INSTALL_L103 ] );
            $error = true;
        }
    }

    if (!is_dir('../' . $wok)) {
        // Force chmod
        @chmod('../' . $wok, 0666);
        $fperm = substr(sprintf('%o', $permissions), -4); //output 0666

        if (file_exists('../' . $wok) && is_writable('../' . $wok)) {
            $wizard->addArray('checks', _OKIMG . '<code>' .$fperm.'</code><code>'.$wok.'</code> ' . sprintf( _INSTALL_L84,'<p class="data">'.  $wok .'</p>') );
        } elseif (!is_writable('../' . $wok)) {
            $wizard->addArray('checks', _NGIMG . '<code style="color:#ff6633">' .$fperm.'</code><code> '.$wok.'</code>'. sprintf( _INSTALL_L83,'<p class="data">'.  $wok .'</p>') );
            $wizard->setBack( [ 'start', _INSTALL_L103 ] );
            $error = true;
        }
    }

}

if (!$error) {
    $wizard->assign('message', '<div class="confirmOk">' . _INSTALL_L87 . '</div>');
} else {
    $wizard->assign('message', '<div class="confirmError">' . _INSTALL_L46 . '</div>');
    $wizard->setReload(true);
}

$wizard->render('install_modcheck.tpl.php');
