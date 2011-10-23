<?php
/**
 *
 * @package Legacy
 * @version $Id: install_dbform.inc.php,v 1.3 2008/09/25 15:12:24 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    include_once '../mainfile.php';
    include_once './class/settingmanager.php';
    $sm = new setting_manager();
    $sm->readConstant();
    $wizard->setContent($sm->editform());
    $wizard->render();
?>
