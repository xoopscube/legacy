<?php
// $Id: themeimgform.php,v 1.1 2007/05/15 02:34:47 minahito Exp $
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

$image_handler =& xoops_gethandler('imageset', 'imagesetimg');
$criteria = new CriteriaCompo(new Criteria('tplset_name', $tplset));
// skin image sets have reference ID 0
$criteria->add(new Criteria('imgset_refid', 0));
$imgs =& $image_handler->getObjects($criteria);
$icount = count($imgs);
if ($tplset != 'default') {
	if ($icount > 0) {
		echo '<form action="admin.php" method="post" enctype="multipart/form-data"><table width="100%" class="outer" cellspacing="1"><tr><th colspan="3">'._MD_EDITSKINIMG.'</th></tr>';
		for ($i = 0; $i < $icount; $i++) {
			echo '<tr><td rowspan="3" valign="middle" align="center" class="odd"><img src="admin.php?fct=tplsets&amp;op=showimage&amp;id='.$imgs[$i]->getVar('imgsetimg_id').'" alt="" /></td><td class="head">'._MD_IMGFILE.'</td><td class="even">'.$imgs[$i]->getVar('imgsetimg_file').'</td></tr><tr><td class="head">'._MD_IMGNEWFILE.'</td><td class="even"><input type="file" name="imgfiles['.$imgs[$i]->getVar('imgsetimg_id').']" /></td></tr><tr><td class="head">'._MD_IMGDELETE.'</td><td class="even"><input type="checkbox" name="imgdelete['.$imgs[$i]->getVar('imgsetimg_id').']" value="1" /><input type="hidden" name="imgids[]" value="'.$imgs[$i]->getVar('imgsetimg_id').'" /></td></tr>';
		}
		echo '<tr class="foot"><td colspan="3" align="center"><input type="hidden" name="tplset" value="'.$tplset.'" /><input type="hidden" name="op" value="updateimage" /><input type="hidden" name="fct" value="tplsets" /><input type="hidden" name="imgset" value="'.$imgs[0]->getVar('imgsetimg_imgset').'" /><input type="submit" name="imgsubmit" value="'._SUBMIT.'" /></td></tr></table></form>';
	}
	echo '<form action="admin.php" method="post" enctype="multipart/form-data"><table width="100%" class="outer" cellspacing="1"><tr><th colspan="3">'._MD_ADDSKINIMG.'</th></tr>';
	echo '<tr><td class="head">'._MD_IMGNEWFILE.'</td><td class="even"><input type="file" name="imgfile" /></td></tr>';
	echo '<tr><td class="head">&nbsp;</td><td class="even"><input type="hidden" name="tplset" value="'.$tplset.'" /><input type="hidden" name="op" value="addimage" /><input type="hidden" name="fct" value="tplsets" /><input type="submit" name="imgsubmit" value="'._SUBMIT.'" /><input type="hidden" name="imgset" value="';
	if ($icount > 0) {
		echo $imgs[0]->getVar('imgsetimg_imgset');
	}
	echo '" /></td></tr></table></form>';
} else {
	echo '<table width="100%" class="outer" cellspacing="1"><tr><th colspan="3">'._MD_SKINIMGS.'</th></tr>';
	for ($i = 0; $i < $icount; $i++) {
		echo '<tr><td valign="middle" align="center" class="odd"><img src="admin.php?fct=tplsets&amp;op=showimage&amp;id='.$imgs[$i]->getVar('imgsetimg_id').'" alt="" /></td><td class="head">'._MD_IMGFILE.'</td><td class="even">'.$imgs[$i]->getVar('imgsetimg_file').'</td></tr>';
	}
	echo '</table>';
}
?>