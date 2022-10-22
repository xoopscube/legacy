<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica
 * @copyright  Copyright 2005-2022 XOOPSCube Project
 * @license    GPL 2.0
 */

// checking XOOPS_ROOT_PATH and XOOPS_URL
include_once '../mainfile.php';

$writeok = [ 'cache/', 'templates_c/', 'uploads/', 'uploads/xupdate/', 'modules/protector/configs/' ];
$error   = false;

clearstatcache();

foreach ( $writeok as $wok ) {

    // try to create the directory if it doesn't exist
    is_dir( XOOPS_TRUST_PATH.'/'.$wok) || (mkdir(XOOPS_TRUST_PATH.'/'.$wok, 0777, true) && is_dir(XOOPS_TRUST_PATH.'/'.$wok));
    $wokWritable = false;
    $permissions = fileperms(XOOPS_TRUST_PATH.'/'.$wok);

    if (XOOPS_TRUST_PATH.'/'.$wok && is_writable(XOOPS_TRUST_PATH.'/'.$wok)) {
        $tempFile = tempnam(XOOPS_TRUST_PATH.'/'.$wok, 'tmp');
        if ($tempFile !== false) {
            $res = file_put_contents($tempFile, 'test');
            $wokWritable = $res !== false;
            @unlink($tempFile);
            $fperm = substr(sprintf('%o', $permissions), -4); //output 0777
            $wizard->addArray('checks', _OKIMG . '<code>' .$fperm.'</code><code>'.$wok.'</code> ' . sprintf(_INSTALL_L86, '<p class="data">'.XOOPS_TRUST_PATH. '/'.$wok.'</p>') );
        } else {
            $fperm = substr(sprintf('%o', $permissions), -4); //output 0777
            $wizard->addArray('checks', _NGIMG . '<code>' .$fperm.'</code><code>'.$wok.'</code> ' . sprintf(_INSTALL_L85, '<p class="data"><code style="color:#ff6633">'.XOOPS_TRUST_PATH. '/'.$wok.'</p>') );
            $error = true;
        }
    }
}

if ( ! $error ) {
	$wizard->assign( 'message', '<div class="confirmOk">'. _INSTALL_L87 .'</div>' );
} else {
	$wizard->assign( 'message', '<div class="confirmError">'. _INSTALL_L46 .'</div>' );
	$wizard->setReload( true );
}

$wizard->render( 'install_modcheck.tpl.php' );
