<?php
/**
 *
 * @package Legacy
 * @version $Id: install_modcheck.inc.php,v 1.3 2008/09/25 15:12:20 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    // checking XOOPS_ROOT_PATH and XOOPS_URL
    include_once '../mainfile.php';

    $writeok = array('cache/', 'templates_c', 'uploads/', 'uploads/xupdate/', 'modules/protector/configs/');
    $error = false;
    foreach ($writeok as $wok) {
        if (!is_dir(XOOPS_TRUST_PATH. '/'. $wok)) {
            if (file_exists(XOOPS_TRUST_PATH. '/'. $wok)) {
                @chmod(XOOPS_TRUST_PATH. '/'. $wok, 0666);
                if (! is_writeable(XOOPS_TRUST_PATH. '/'. $wok)) {
                    $wizard->addArray('checks', _NGIMG.sprintf(_INSTALL_L83, $wok));
                    $error = true;
                } else {
                    $wizard->addArray('checks', _OKIMG.sprintf(_INSTALL_L84, $wok));
                }
            }
        } else {
            @chmod(XOOPS_TRUST_PATH. '/'. $wok, 0777);
            if (! is_writeable(XOOPS_TRUST_PATH. '/'. $wok)) {
                $wizard->addArray('checks', _NGIMG.sprintf(_INSTALL_L85, XOOPS_TRUST_PATH. '/'.$wok));
                $error = true;
            } else {
                $wizard->addArray('checks', _OKIMG.sprintf(_INSTALL_L86, XOOPS_TRUST_PATH. '/'.$wok));
            }
        }
    }

    if (! $error) {
        $wizard->assign('message', _INSTALL_L87);
    } else {
        $wizard->assign('message', _INSTALL_L46);
        $wizard->setReload(true);
    }
    $wizard->render('install_modcheck.tpl.php');
