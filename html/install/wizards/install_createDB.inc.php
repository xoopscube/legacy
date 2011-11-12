<?php
/**
 *
 * @package Legacy
 * @version $Id: install_createDB.inc.php,v 1.3 2008/09/25 15:12:20 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    include_once '../mainfile.php';
    include_once './class/dbmanager.php';
    $dbm = new db_manager;
    if(! $dbm->createDB()){
        $wizard->setContent('<p>'._INSTALL_L31.'</p>');
        $wizard->setNext(array('checkDB', _INSTALL_L104));
        $wizard->setBack(array('start', _INSTALL_L103));
    } else {
        $wizard->setContent('<div class="confirmOk">'.sprintf(_INSTALL_L43, XOOPS_DB_NAME).'</div>');
    }
    $wizard->render();
?>
