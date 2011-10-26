<?php
// $Id: main.php,v 1.1 2007/05/15 02:35:15 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if ( !is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid()) ) {
    exit("Access Denied");
}
include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php';

$op = "list";

if ( isset($_GET['op']) ) {
    if ($_GET['op'] == "edit" || $_GET['op'] == "delete" || $_GET['op'] == "delete_ok" || $_GET['op'] == "clone") {
        $op = $_GET['op'];
        $bid = isset($_GET['bid']) ? intval($_GET['bid']) : 0;
    }
} elseif (!empty($_POST['op'])) {
    $op = $_POST['op'];
}

if (isset($_POST['previewblock'])) {
    if (!XoopsMultiTokenHandler::quickValidate('block')) {
        redirect_header("admin.php?fct=blocksadmin");
        exit();
    }
    xoops_cp_header();
    include_once XOOPS_ROOT_PATH.'/class/template.php';
    $xoopsTpl = new XoopsTpl();
    $xoopsTpl->xoops_setCaching(0);
    $bid = !empty($_POST['bid']) ? intval($_POST['bid']) : 0;
    if (!empty($bid)) {
        $block['bid'] = $bid;
        $block['form_title'] = _AM_EDITBLOCK;
        $myblock = new XoopsBlock($bid);
        $block['name'] = $myblock->getVar('name');
    } else {
        if ($op == 'save') {
            $block['form_title'] = _AM_ADDBLOCK;
        } else {
            $block['form_title'] = _AM_CLONEBLOCK;
        }
        $myblock = new XoopsBlock();
        $myblock->setVar('block_type', 'C');
    }
    $myts =& MyTextSanitizer::getInstance();
    $myblock->setVar('title', $myts->stripSlashesGPC($_POST['btitle']));
    $myblock->setVar('content', $myts->stripSlashesGPC($_POST['bcontent']));
    $dummyhtml = '<html><head><meta http-equiv="content-type" content="text/html; charset='._CHARSET.'" /><meta http-equiv="content-language" content="'._LANGCODE.'" /><title>'.htmlspecialchars($xoopsConfig['sitename']).'</title><link rel="stylesheet" type="text/css" media="all" href="'.getcss($xoopsConfig['theme_set']).'" /></head><body><table><tr><th>'.$myblock->getVar('title').'</th></tr><tr><td>'.$myblock->getContent('S', $_POST['bctype']).'</td></tr></table></body></html>';

    $block['edit_form'] = false;
    $block['template'] = '';
    $block['op'] = $op;
    $block['side'] = $_POST['bside'];
    $block['weight'] = $_POST['bweight'];
    $block['visible'] = $_POST['bvisible'];
    $block['title'] = $myblock->getVar('title', 'E');
    $block['content'] = $myblock->getVar('content', 'E');
    $block['modules'] =& $_POST['bmodule'];
    $block['ctype'] = isset($_POST['bctype']) ? $_POST['bctype'] : $myblock->getVar('c_type');
    $block['is_custom'] = true;
    $block['cachetime'] = intval($_POST['bcachetime']);
    echo '<a href="admin.php?fct=blocksadmin">'. _AM_BADMIN .'</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;'.$block['form_title'].'<br /><br />';
    include XOOPS_ROOT_PATH.'/modules/system/admin/blocksadmin/blockform.php';
    $form->display();
    xoops_cp_footer();
    echo '<script type="text/javascript">
    <!--//
    win = openWithSelfMain("", "xoops_system_block_preview", 250, 200, true);
    ';
    $lines = preg_split("/(\r\n|\r|\n)( *)/", $dummyhtml);
    foreach ($lines as $line) {
        echo 'win.document.writeln("'.str_replace('"', '\"', $line).'");';
    }
    echo '
    win.document.close();
    //-->
    </script>';
    exit();
}

if ( $op == "list" ) {
    require_once XOOPS_ROOT_PATH."/modules/system/admin/blocksadmin/blocksadmin.php";
    xoops_cp_header();
    list_blocks();
    xoops_cp_footer();
    exit();
}

if ( $op == "order" ) {
    if (is_array($_POST['bid'])) {
        require_once XOOPS_ROOT_PATH."/modules/system/admin/blocksadmin/blocksadmin.php";
        foreach (array_keys($_POST['bid']) as $i) {
            if ($_POST['oldweight'][$i] != $_POST['weight'][$i] || $_POST['oldvisible'][$i] != $_POST['visible'][$i] || $_POST['oldside'][$i] != $_POST['side'][$i])
            order_block($_POST['bid'][$i], $_POST['weight'][$i], $_POST['visible'][$i], $_POST['side'][$i]);
        }
    }
    redirect_header("admin.php?fct=blocksadmin",1,_AM_DBUPDATED);
    exit();
}

if ( $op == "save" ) {
    if (empty($_POST['bmodule']) || !XoopsMultiTokenHandler::quickValidate('block')) {
        xoops_cp_header();
        xoops_error(sprintf(_AM_NOTSELNG, _AM_VISIBLEIN));
        xoops_cp_footer();
        exit();
    }
    $myblock = new XoopsBlock();
    $myblock->setVar('side', $_POST['bside']);
    $myblock->setVar('weight', $_POST['bweight']);
    $myblock->setVar('visible', $_POST['bvisible']);
    $myblock->setVar('weight', $_POST['bweight']);
    $myblock->setVar('title', $_POST['btitle']);
    $myblock->setVar('content', $_POST['bcontent']);
    $myblock->setVar('c_type', $_POST['bctype']);
    $myblock->setVar('block_type', 'C');
    $myblock->setVar('bcachetime', $_POST['bcachetime']);
    switch ($_POST['bctype']) {
    case 'H':
        $name = _AM_CUSTOMHTML;
        break;
    case 'P':
        $name = _AM_CUSTOMPHP;
        break;
    case 'S':
        $name = _AM_CUSTOMSMILE;
        break;
    default:
        $name = _AM_CUSTOMNOSMILE;
        break;
    }
    $myblock->setVar('name', $name);
    $newid = $myblock->store();
    if (!$newid) {
        xoops_cp_header();
        $myblock->getHtmlErrors();
        xoops_cp_footer();
        exit();
    }
    $db =& Database::getInstance();
    foreach ($_POST['bmodule'] as $bmid) {
        $sql = 'INSERT INTO '.$db->prefix('block_module_link').' (block_id, module_id) VALUES ('.$newid.', '.intval($bmid).')';
            $db->query($sql);
    }
    $groups =& $xoopsUser->getGroups();
    $count = count($groups);
    for ($i = 0; $i < $count; $i++) {
        $sql = "INSERT INTO ".$db->prefix('group_permission')." (gperm_groupid, gperm_itemid, gperm_name, gperm_modid) VALUES (".$groups[$i].", ".$newid.", 'block_read', 1)";
        $db->query($sql);
    }
    redirect_header('admin.php?fct=blocksadmin&amp;t='.time(),1,_AM_DBUPDATED);
    exit();
}

if ( $op == "update" ) {
    $bid = !empty($_POST['bid']) ? intval($_POST['bid']) : 0;
    if ($bid <= 0) {
        exit();
    }
    $bcachetime = isset($_POST['bcachetime']) ? intval($_POST['bcachetime']) : 0;
    $options = isset($_POST['options']) ? $_POST['options'] : array();
    $bcontent = isset($_POST['bcontent']) ? $_POST['bcontent'] : '';
    $bctype = isset($_POST['bctype']) ? $_POST['bctype'] : '';
    if (empty($_POST['bmodule']) || !XoopsMultiTokenHandler::quickValidate('block')) {
        xoops_cp_header();
        xoops_error(sprintf(_AM_NOTSELNG, _AM_VISIBLEIN));
        xoops_cp_footer();
        exit();
    }
    $myblock = new XoopsBlock($bid);
    $myblock->setVar('side', $_POST['bside']);
    $myblock->setVar('weight', $_POST['bweight']);
    $myblock->setVar('visible', $_POST['bvisible']);
    $myblock->setVar('title', $_POST['btitle']);
    $myblock->setVar('content', $bcontent);
    $myblock->setVar('bcachetime', $bcachetime);
    $options_count = count($options);
    if ($options_count > 0) {
        //Convert array values to comma-separated
        for ( $i = 0; $i < $options_count; $i++ ) {
            if (is_array($options[$i])) {
                $options[$i] = implode(',', $options[$i]);
            }
        }
        $options = implode('|', $options);
        $myblock->setVar('options', $options);
    }
    if ($myblock->getVar('block_type') == 'C') {
        switch ($bctype) {
        case 'H':
            $name = _AM_CUSTOMHTML;
            break;
        case 'P':
            $name = _AM_CUSTOMPHP;
            break;
        case 'S':
            $name = _AM_CUSTOMSMILE;
            break;
        default:
            $name = _AM_CUSTOMNOSMILE;
            break;
        }
        $myblock->setVar('name', $name);
        $myblock->setVar('c_type', $bctype);
    } else {
        $myblock->setVar('c_type', 'H');
    }
    $msg = _AM_DBUPDATED;
    if ($myblock->store() != false) {
        $db =& Database::getInstance();
        $sql = sprintf("DELETE FROM %s WHERE block_id = %u", $db->prefix('block_module_link'), $bid);
        $db->query($sql);
        foreach ($_POST['bmodule'] as $bmid) {
            $sql = sprintf("INSERT INTO %s (block_id, module_id) VALUES (%u, %d)", $db->prefix('block_module_link'), $bid, intval($bmid));
            $db->query($sql);
        }
        include_once XOOPS_ROOT_PATH.'/class/template.php';
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->xoops_setCaching(2);
        if ($myblock->getVar('template') != '') {
            if ($xoopsTpl->is_cached('db:'.$myblock->getVar('template'), 'blk_'.$myblock->getVar('bid'))) {
                if (!$xoopsTpl->clear_cache('db:'.$myblock->getVar('template'), 'blk_'.$myblock->getVar('bid'))) {
                    $msg = 'Unable to clear cache for block ID '.$bid;
                }
            }
        } else {
            if ($xoopsTpl->is_cached('db:system_dummy.html', 'blk_'.$bid)) {
                if (!$xoopsTpl->clear_cache('db:system_dummy.html', 'blk_'.$bid)) {
                    $msg = 'Unable to clear cache for block ID '.$bid;
                }
            }
        }
    } else {
        $msg = 'Failed update of block. ID:'.$bid;
    }
    redirect_header('admin.php?fct=blocksadmin&amp;t='.time(),1,$msg);
    exit();
}


if ( $op == "delete_ok" ) {
    $bid = !empty($_POST['bid']) ? intval($_POST['bid']) : 0;
    if ($bid > 0) {
        require_once XOOPS_ROOT_PATH."/modules/system/admin/blocksadmin/blocksadmin.php";
        delete_block_ok($bid);
    }
    exit();
}

if ( $op == "delete" ) {
    xoops_cp_header();
    if ($bid > 0) {
        require_once XOOPS_ROOT_PATH."/modules/system/admin/blocksadmin/blocksadmin.php";
        delete_block($bid);
    }
    xoops_cp_footer();
    exit();
}

if ( $op == "edit" ) {
    xoops_cp_header();
    if ($bid > 0) {
        require_once XOOPS_ROOT_PATH."/modules/system/admin/blocksadmin/blocksadmin.php";
        edit_block($bid);
    }
    xoops_cp_footer();
    exit();
}
/*
if ($op == 'clone') {
    clone_block($bid);
}

if ($op == 'clone_ok') {
    clone_block_ok($bid, $bside, $bweight, $bvisible, $bcachetime, $bmodule, $options);
}
*/
?>