<?php
/**
 *
 * @package Legacy
 * @version $Id: install_dbsave.inc.php,v 1.3 2008/09/25 15:12:22 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    include_once './class/mainfilemanager.php';
    $mm = new mainfile_manager('../mainfile.php');

    $ret = $mm->copyDistFile();
    if(! $ret){
        $wizard->setContent(_INSTALL_L60);
        $wizard->error();
        exit();
    }

    $mm->setRewrite('XOOPS_ROOT_PATH', $myts->stripSlashesGPC($_POST['root_path']));
    $mm->setRewrite('XOOPS_TRUST_PATH', $myts->stripSlashesGPC($_POST['trust_path']));
    $mm->setRewrite('XOOPS_URL', $myts->stripSlashesGPC($_POST['xoops_url']));
    $mm->setRewrite('XOOPS_DB_TYPE', $myts->stripSlashesGPC($_POST['database']));
    $mm->setRewrite('XOOPS_DB_PREFIX', $myts->stripSlashesGPC($_POST['prefix']));
    $mm->setRewrite('XOOPS_SALT', $myts->stripSlashesGPC($_POST['salt']));
    $mm->setRewrite('XOOPS_DB_HOST', $myts->stripSlashesGPC($_POST['dbhost']));
    $mm->setRewrite('XOOPS_DB_USER', $myts->stripSlashesGPC($_POST['dbuname']));
    $mm->setRewrite('XOOPS_DB_PASS', $myts->stripSlashesGPC($_POST['dbpass']));
    $mm->setRewrite('XOOPS_DB_NAME', $myts->stripSlashesGPC($_POST['dbname']));
    $mm->setRewrite('XOOPS_DB_PCONNECT', intval($_POST['db_pconnect']));
    $mm->setRewrite('XOOPS_GROUP_ADMIN', 1);
    $mm->setRewrite('XOOPS_GROUP_USERS', 2);
    $mm->setRewrite('XOOPS_GROUP_ANONYMOUS', 3);

    // Check if XOOPS_CHECK_PATH should be initially set or not
    $xoopsPathTrans = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] :  $_SERVER['SCRIPT_FILENAME'];
    if ( DIRECTORY_SEPARATOR != '/' ) {
        // IIS6 doubles the \ chars
        $xoopsPathTrans = str_replace( strpos( $xoopsPathTrans, '\\\\', 2 ) ? '\\\\' : DIRECTORY_SEPARATOR, '/', $xoopsPathTrans);
    }
	
    $ret = $mm->doRewrite();
    if(! $ret){
        $wizard->setContent(_INSTALL_L60);
        $wizard->error();
        exit();
    }
    $wizard->assign('reports', $mm->report());
    $wizard->assign('message',_INSTALL_L62);
    $wizard->render('install_dbsave.tpl.php');
?>
