<?php
/**
 *
 * @package Legacy
 * @version $Id: install_langselect.inc.php,v 1.3 2008/09/25 15:12:33 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    if (!defined('_INSTALL_L128')) {
        define('_INSTALL_L128', 'Choose language to be used for the installation process');
    }
    $langarr = getDirList('./language/');
    foreach ($langarr as $lang) {
        $wizard->addArray('languages', $lang);
        if (strtolower($lang) == $language) {
            $wizard->addArray('selected','selected="selected"');
        } else {
            $wizard->addArray('selected','');
        }
    }
    $wizard->render('install_langselect.tpl.php');
?>
