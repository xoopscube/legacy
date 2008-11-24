<?php
/**
 *
 * @package Legacy
 * @version $Id: install_updateSmilies_go.inc.php,v 1.3 2008/09/25 15:12:19 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    unset($xoopsOption['nocommon']);
    include('../mainfile.php');
    $result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix('smiles'));
    $content = '';
    $title = _INSTALL_L155;
    if (!defined('XOOPS_UPLOAD_PATH')) {
        define('XOOPS_UPLOAD_PATH', '../uploads');
    }
    while ($smiley = $xoopsDB->fetchArray($result)) {
        if (file_exists('../images/smilies/'.$smiley['smile_url']) && false != $fp = fopen('../images/smilies/'.$smiley['smile_url'], 'rb')) {
            $binary = fread($fp, filesize('../images/smilies/'.$smiley['smile_url']));
            fclose($fp);
            if (!preg_match("/\.([a-zA-Z0-9]+)$/", $smiley['smile_url'], $matched)) {
                continue;
            }
            $newsmiley = uniqid('smil').'.'.strtolower($matched[1]);
            if (false != $fp = fopen(XOOPS_UPLOAD_PATH.'/'.$newsmiley, 'wb')) {
                if (-1 != fwrite($fp, $binary)) {
                    $xoopsDB->query("UPDATE ".$xoopsDB->prefix('smiles')." SET smile_url='".$newsmiley."' WHERE id=".$smiley['id']);
                    $content .= _OKIMG.sprintf(_INSTALL_L154, $smiley['smile_url']).'<br />';
                } else {
                    $content .= _NGIMG.sprintf(_INSTALL_L153, $smiley['smile_url']).'<br />';
                }
                fclose($fp);
            }
        } else {
            $content .= _OKIMG.sprintf(_INSTALL_L152, $smiley['smile_url']).'<br />';
        }
    }
    $result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix('ranks'));
    while ($rank = $xoopsDB->fetchArray($result)) {
        if (file_exists('../images/ranks/'.$rank['rank_image']) && false != $fp = fopen('../images/ranks/'.$rank['rank_image'], 'rb')) {
            $binary = fread($fp, filesize('../images/ranks/'.$rank['rank_image']));
            fclose($fp);
            if (!preg_match("/\.([a-zA-Z0-9]+)$/", $rank['rank_image'], $matched)) {
                continue;
            }
            $newrank = uniqid('rank').'.'.strtolower($matched[1]);
            if (false != $fp = fopen(XOOPS_UPLOAD_PATH.'/'.$newrank, 'wb')) {
                if (-1 != fwrite($fp, $binary)) {
                    $content .= _OKIMG.sprintf(_INSTALL_L154, $rank['rank_image']).'<br />';
                    $xoopsDB->query("UPDATE ".$xoopsDB->prefix('ranks')." SET rank_image='".$newrank."' WHERE rank_id=".$rank['rank_id']);
                } else {
                    $content .= _NGIMG.sprintf(_INSTALL_L153, $rank['rank_image']).'<br />';
                }
                fclose($fp);
            }
        } else {
            $content .= _OKIMG.sprintf(_INSTALL_L152, $rank['rank_image']).'<br />';
        }
    }
    $b_next = array('updateAvatars', _INSTALL_L14);
    include './install_tpl.php';
?>
