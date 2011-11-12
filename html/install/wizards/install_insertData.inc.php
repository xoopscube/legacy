<?php
/**
 *
 * @package Legacy
 * @version $Id: install_insertData.inc.php,v 1.3 2008/09/25 15:12:21 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    $adminname = $myts->stripSlashesGPC(trim($_POST['adminname']));
    $adminpass = $myts->stripSlashesGPC($_POST['adminpass']);
    $adminmail = $myts->stripSlashesGPC(trim($_POST['adminmail']));
    $timezone = $myts->stripSlashesGPC($_POST['timezone']);

    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i",$adminmail)) {
        $wizard->setContent('<p>'._INSTALL_L73.'</p>');
        $wizard->setBack(array('', _INSTALL_L112));
        $wizard->error();
        exit();
    }
    if ( !isset($adminname) || !isset($adminpass) || !isset($adminmail) || $adminmail == "" || $adminname =="" || $adminpass =="" || $adminpass != $adminpass2) {
        $wizard->setContent('<p>'._INSTALL_L41.'</p>');
        $wizard->setBack(array('', _INSTALL_L112));
        $wizard->error();
        exit();
    }

    include_once '../mainfile.php';
    include_once './include/makedata.php';
    include_once './class/dbmanager.php';
    $dbm = new db_manager;
    include_once './class/cachemanager.php';
    $cm = new cache_manager;

    $language = check_language($language);
    if ( file_exists('./language/'.$language.'/install2.php') ) {
        include_once './language/'.$language.'/install2.php';
    } elseif ( file_exists('./language/english/install2.php') ) {
        include_once './language/english/install2.php';
        $language = 'english';
    } else {
        echo 'no language file (install2.php).';
        exit();
    }

    //$tables = array();
    $result = $dbm->queryFromFile('./sql/'.XOOPS_DB_TYPE.'.data.sql');

    $result = $dbm->queryFromFile('./language/'.$language.'/'.XOOPS_DB_TYPE.'.lang.data.sql');

    $group = make_groups($dbm);
    $result = make_data($dbm, $cm, $adminname, $adminpass, $adminmail, $language, $group, $timezone);

    $wizard->assign('dbm_reports',$dbm->report());
    $wizard->assign('cm_reports',$cm->report());

    include_once './class/mainfilemanager.php';
    $mm = new mainfile_manager('../mainfile.php');
    foreach($group as $key => $val){
        $mm->setRewrite($key, intval($val));
    }
    $result = $mm->doRewrite();
    $wizard->assign('mm_reports',$mm->report());

    setcookie('xoops_session', '', time() - 3600);
    $wizard->render('install_insertData.tpl.php');
?>
