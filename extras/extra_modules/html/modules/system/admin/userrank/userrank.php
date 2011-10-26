<?php
// $Id: userrank.php,v 1.1 2007/05/15 02:34:52 minahito Exp $
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

function RankForumAdmin()
{
    $db =& Database::getInstance();
    xoops_cp_header();
    echo "<h4 style='text-align:left;'>"._AM_RANKSSETTINGS."</h4>
    <table width='100%' class='outer' cellpadding='4' cellspacing='1'>
    <tr align='center'>
    <th align='left'>"._AM_TITLE."</th>
    <th>"._AM_MINPOST."</th>
    <th>"._AM_MAXPOST."</th>
    <th>"._AM_IMAGE."</th>
    <th>"._AM_SPERANK."</th>
    <th>"._AM_ACTION."</th></tr>";
    $result = $db->query("SELECT * FROM ".$db->prefix("ranks")." ORDER BY rank_id");
    $count = 0;
    while ( $rank = $db->fetchArray($result) ) {
        if ($count % 2 == 0) {
            $class = 'even';
        } else {
            $class = 'odd';
        }
        echo "<tr class='$class' align='center'>
        <td align='left'>".$rank['rank_title']."</td>
        <td>".$rank['rank_min']."</td>
        <td>".$rank['rank_max']."</td>
        <td>";
        if ($rank['rank_image'] && file_exists(XOOPS_UPLOAD_PATH.'/'.$rank['rank_image'])) {
            echo '<img src="'.XOOPS_UPLOAD_URL.'/'.$rank['rank_image'].'" alt="" /></td>';
        } else {
            echo '&nbsp;';
        }
        if ($rank['rank_special'] == 1) {
            echo '<td>'._AM_ON.'</td>';
        } else {
            echo '<td>'._AM_OFF.'</td>';
        }
        echo"<td><a href='admin.php?fct=userrank&amp;op=RankForumEdit&amp;rank_id=".$rank['rank_id']."'>"._AM_EDIT."</a> <a href='admin.php?fct=userrank&amp;op=RankForumDel&amp;rank_id=".$rank['rank_id']."&amp;ok=0'>"._AM_DEL."</a></td></tr>";
        $count++;
    }
    echo '</table><br /><br />';
    $rank['rank_min'] = 0;
    $rank['rank_max'] = 0;
    $rank['rank_special'] = 0;
    $rank['rank_id'] = '';
    $rank['rank_title'] = '';
    $rank['rank_image'] = 'blank.gif';
    $rank['form_title'] = _AM_ADDNEWRANK;
    $rank['op'] = 'RankForumAdd';
    include_once XOOPS_ROOT_PATH.'/modules/system/admin/userrank/rankform.php';
    $rank_form->display();
    xoops_cp_footer();
}


function RankForumEdit($rank_id)
{
    $db =& Database::getInstance();
    $myts =& MyTextSanitizer::getInstance();
    xoops_cp_header();
    echo '<a href="admin.php?fct=userrank">'. _AM_RANKSSETTINGS .'</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;'._AM_EDITRANK.'<br /><br />';
    $result = $db->query("SELECT * FROM ".$db->prefix("ranks")." WHERE rank_id=".$rank_id);
    $rank = $db->fetchArray($result);
    $rank['rank_title'] = $myts->makeTboxData4Edit($rank['rank_title']);
    $rank['rank_image'] = $myts->makeTboxData4Edit($rank['rank_image']);
    $rank['form_title'] = _AM_EDITRANK;
    $rank['op'] = 'RankForumSave';
    include_once XOOPS_ROOT_PATH.'/modules/system/admin/userrank/rankform.php';
    $rank_form->addElement(new XoopsFormHidden('old_rank', $rank['rank_image']));
    $rank_form->display();
    xoops_cp_footer();
}

?>