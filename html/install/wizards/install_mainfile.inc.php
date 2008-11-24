<?php
/**
 *
 * @package Legacy
 * @version $Id: install_mainfile.inc.php,v 1.3 2008/09/25 15:12:34 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    // checking XOOPS_ROOT_PATH and XOOPS_URL
    include_once '../mainfile.php';

    $detected = str_replace('\\', '/', getcwd()); // "
    $detected = str_replace('/install', "", $detected);
    if ( substr($detected, -1) == '/' ) {
        $detected = substr($detected, 0, -1);
    }

    if (empty($detected)){
        $wizard->addArray('checks', _NGIMG._INSTALL_L95);
    } elseif ( XOOPS_ROOT_PATH != $detected ) {
        $wizard->addArray('checks', _NGIMG.sprintf(_INSTALL_L96,$detected));
    } else {
        $wizard->addArray('checks', _OKIMG._INSTALL_L97);
    }

    if(!is_dir(XOOPS_ROOT_PATH)){
        $wizard->addArray('checks', _NGIMG._INSTALL_L99);
    }

    if(preg_match('/^http[s]?:\/\/(.*)[^\/]+$/i',XOOPS_URL)){
        $wizard->addArray('checks', _OKIMG._INSTALL_L100);
    }else{
        $wizard->addArray('checks', _NGIMG._INSTALL_L101);
    }

    $wizard->render('install_mainfile.tpl.php');
?>
