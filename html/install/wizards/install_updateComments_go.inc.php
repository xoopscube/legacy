<?php
/**
 *
 * @package Legacy
 * @version $Id: install_updateComments_go.inc.php,v 1.3 2008/09/25 15:12:31 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    unset($xoopsOption['nocommon']);
    include '../mainfile.php';
    include '../class/xoopscomments.php';
    include '../include/comment_constants.php';
    $module_handler =& xoops_gethandler('module');
    $old_commentd_mods = array('news' => 'comments', 'xoopspoll' => 'xoopspollcomments');
    $title = _INSTALL_L147;
    $content = '';
    foreach ($old_commentd_mods as $module => $com_table) {
        $moduleobj =& $module_handler->getByDirname($module);
        if (is_object($moduleobj)) {
            $content .= '<h5>'.$moduleobj->getVar('name').'</h5>';
            $comment_handler =& xoops_gethandler('comment');
            $criteria = new CriteriaCompo();
            $criteria->setOrder('DESC');
            $criteria->setSort('com_id');
            $criteria->setLimit(1);
            $last_comment =& $comment_handler->getObjects($criteria);
            $offset = (is_array($last_comment) && count($last_comment) > 0) ? $last_comment[0]->getVar('com_id') : 0;
            $xc = new XoopsComments($xoopsDB->prefix($com_table));
            $top_comments =& $xc->getAllComments(array('pid=0'));

            foreach ($top_comments as $tc) {
                $sql = sprintf("INSERT INTO %s (com_id, com_pid, com_modid, com_icon, com_title, com_text, com_created, com_modified, com_uid, com_ip, com_sig, com_itemid, com_rootid, com_status, dohtml, dosmiley, doxcode, doimage, dobr) VALUES (%u, %u, %u, '%s', '%s', '%s', %u, %u, %u, '%s', %u, %u, %u, %u, %u, %u, %u, %u, %u)", $xoopsDB->prefix('xoopscomments'), $tc->getVar('comment_id') + $offset, 0, $moduleobj->getVar('mid'), '', addslashes($tc->getVar('subject', 'n')), addslashes($tc->getVar('comment', 'n')), $tc->getVar('date'), $tc->getVar('date'), $tc->getVar('user_id'), $tc->getVar('ip'), 0, $tc->getVar('item_id'), $tc->getVar('comment_id') + $offset, XOOPS_COMMENT_ACTIVE, 0, 1, 1, 1, 1);

                if (!$xoopsDB->query($sql)) {
                    $content .= _NGIMG.sprintf(_INSTALL_L146, $tc->getVar('comment_id') + $offset).'<br />';
                } else {
                    $content .= _OKIMG.sprintf(_INSTALL_L145, $tc->getVar('comment_id') + $offset).'<br />';
                    $child_comments = $tc->getCommentTree();
                    foreach ($child_comments as $cc) {
                        $sql = sprintf("INSERT INTO %s (com_id, com_pid, com_modid, com_icon, com_title, com_text, com_created, com_modified, com_uid, com_ip, com_sig, com_itemid, com_rootid, com_status, dohtml, dosmiley, doxcode, doimage, dobr) VALUES (%u, %u, %u, '%s', '%s', '%s', %u, %u, %u, '%s', %u, %u, %u, %u, %u, %u, %u, %u, %u)", $xoopsDB->prefix('xoopscomments'), $cc->getVar('comment_id') + $offset, $cc->getVar('pid') + $offset, $moduleobj->getVar('mid'), '', addslashes($cc->getVar('subject', 'n')), addslashes($cc->getVar('comment', 'n')), $cc->getVar('date'), $cc->getVar('date'), $cc->getVar('user_id'), $cc->getVar('ip'), 0, $cc->getVar('item_id'), $tc->getVar('comment_id') + $offset, XOOPS_COMMENT_ACTIVE, 0, 1, 1, 1, 1);
                        if (!$xoopsDB->query($sql)) {
                            $content .= _NGIMG.sprintf(_INSTALL_L146, $cc->getVar('comment_id') + $offset).'<br />';
                        } else {
                            $content .= _OKIMG.sprintf(_INSTALL_L145, $cc->getVar('comment_id') + $offset).'<br />';
                        }
                    }
                }
            }
        }
    }
    $xoopsDB->query('ALTER TABLE '.$xoopsDB->prefix('xoopscomments').' CHANGE com_id com_id mediumint(8) unsigned NOT NULL auto_increment PRIMARY KEY');
    $b_next = array('updateSmilies', _INSTALL_L14);
    include './install_tpl.php';
?>
