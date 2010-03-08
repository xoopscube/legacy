<?php
// $Id: smilies.php,v 1.1 2007/05/15 02:34:46 minahito Exp $
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
function SmilesAdmin()
{
    $db =& Database::getInstance();
    $url_smiles = XOOPS_UPLOAD_URL;
    $myts =& MyTextSanitizer::getInstance();
    xoops_cp_header();
    echo "<h4 style='text-align:left;'>"._AM_SMILESCONTROL."</h4>";

    if ($getsmiles = $db->query("SELECT * FROM ".$db->prefix("smiles"))) {
        if (($numsmiles = $db->getRowsNum($getsmiles)) == "0") {
            //EMPTY
        } else {
            $token=&XoopsMultiTokenHandler::quickCreate('smilies_SmilesUpdate');
            echo '<form action="admin.php" method="post"><table width="100%" class="outer" cellpadding="4" cellspacing="1">';
            echo $token->getHtml();
            echo "<tr align='center'><th align='left'>" ._AM_CODE."</th>";
            echo "<th>" ._AM_SMILIE."</th>";
            echo "<th>"._AM_SMILEEMOTION."</th>";
            echo "<th>" ._AM_DISPLAYF."</th>";
            echo "<th>"._AM_ACTION."</th>";
            echo "</tr>\n";
            $i = 0;
            while ($smiles = $db->fetchArray($getsmiles)) {
                if ($i % 2 == 0) {
                    $class = 'even';
                } else {
                    $class= 'odd';
                }
                $smiles['code'] = $myts->makeTboxData4Show($smiles['code']);
                $smiles['smile_url'] = $myts->makeTboxData4Edit($smiles['smile_url']);
                $smiles['smile_emotion'] = $myts->makeTboxData4Edit($smiles['emotion']);
                echo "<tr align='center' class='$class'>";
                echo "<td align='left'>".$smiles['code']."</td>";
                echo "<td><img src='".$url_smiles."/".$smiles['smile_url']."' alt='' /></td>";
                echo '<td>'.$smiles['smile_emotion'].'</td>';
                echo '<td><input type="hidden" name="smile_id['.$i.']" value="'.$smiles['id'].'" /><input type="hidden" name="old_display['.$i.']" value="'.$smiles['display'].'" /><input type="checkbox" value="1" name="smile_display['.$i.']"';
                if ($smiles['display'] == 1) {
                    echo ' checked="checked"';
                }
                echo " /></td><td><a href='admin.php?fct=smilies&amp;op=SmilesEdit&amp;id=".$smiles['id']."'>" ._AM_EDIT."</a>&nbsp;";
                echo "<a href='admin.php?fct=smilies&amp;op=SmilesDel&amp;id=".$smiles['id']."'>" ._AM_DEL."</a></td>";
                echo "</tr>\n";
                $i++;
            }
            echo '<tr><td class="foot" colspan="5" align="center"><input type="hidden" name="op" value="SmilesUpdate" /><input type="hidden" name="fct" value="smilies" />';
            //echo xoops_token_gethtml();
            echo '<input type="submit" value="'._SUBMIT.'" /></tr></table></form>';
        }
    } else {
        echo _AM_CNRFTSD;
    }
    $smiles['smile_code'] = '';
    $smiles['smile_url'] = 'blank.gif';
    $smiles['smile_desc'] = '';
    $smiles['smile_display'] = 1;
    $smiles['smile_form'] = _AM_ADDSMILE;
    $smiles['op'] = 'SmilesAdd';
    $smiles['id'] = '';
    include XOOPS_ROOT_PATH.'/modules/system/admin/smilies/smileform.php';
    $smile_form->display();
    xoops_cp_footer();
}

function SmilesEdit($id)
{
    $db =& Database::getInstance();
    $myts =& MyTextSanitizer::getInstance();
    xoops_cp_header();
    echo '<a href="admin.php?fct=smilies">'._AM_SMILESCONTROL .'</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;'._AM_EDITSMILE.'<br /><br />';
    if ($getsmiles = $db->query("SELECT * FROM ".$db->prefix("smiles")." WHERE id = $id")) {
        $numsmiles = $db->getRowsNum($getsmiles);
        if ( $numsmiles == 0 ) {
            //EMPTY
        } else {
            if ($smiles = $db->fetchArray($getsmiles)) {
                $smiles['smile_code'] = $myts->makeTboxData4Edit($smiles['code']);
                $smiles['smile_url'] = $myts->makeTboxData4Edit($smiles['smile_url']);
                $smiles['smile_desc'] = $myts->makeTboxData4Edit($smiles['emotion']);
                $smiles['smile_display'] = $smiles['display'];
                $smiles['smile_form'] = _AM_EDITSMILE;
                $smiles['op'] = 'SmilesSave';
                include XOOPS_ROOT_PATH.'/modules/system/admin/smilies/smileform.php';
                //$smile_form->addElement(new XoopsFormHidden('old_smile', $smiles['smile_url']));
                $smile_form->display();
            }
        }
    } else {
        echo _AM_CNRFTSD;
    }
    xoops_cp_footer();
}
?>