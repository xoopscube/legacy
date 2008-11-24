<?php
/**
 *
 * @package Legacy
 * @version $Id: install_checkDB.inc.php,v 1.3 2008/09/25 15:12:17 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    include_once '../mainfile.php';
    include_once './class/dbmanager.php';
    $dbm = new db_manager;
    $title = _INSTALL_L104;
    if (! $dbm->isConnectable()) {
        $wizard->addArray('checks',_NGIMG._INSTALL_L106);
        $wizard->addArray('msgs',_INSTALL_L107);
        $wizard->setBack(array('start', _INSTALL_L103));
        $wizard->setReload(true);
    } else {
        $wizard->addArray('checks',_OKIMG._INSTALL_L108);
        if (! $dbm->dbExists()) {
            $wizard->addArray('checks',_NGIMG.sprintf(_INSTALL_L109, XOOPS_DB_NAME));
            $wizard->addArray('msgs',_INSTALL_L21.'<br /><b>'.XOOPS_DB_NAME.'</b>');
            $wizard->addArray('msgs',_INSTALL_L22);
        } else {
            $wizard->addArray('checks',_OKIMG.sprintf(_INSTALL_L110, XOOPS_DB_NAME));
            if (!$dbm->tableExists('users')) {
                $wizard->addArray('msgs',_INSTALL_L111);
                $wizard->setNext(array('createTables', _INSTALL_L40));
            } else if (!$dbm->tableExists('config')) {
                $wizard->addArray('msgs',_INSTALL_L130);
                $wizard->setNext(array('updateTables', _INSTALL_L14));
            } else {
                $wizard->addArray('checks',_NGIMG._INSTALL_L131);
                $wizard->setNext('');
            }
        }
    }
    $wizard->render('install_checkDB.tpl.php');
?>
