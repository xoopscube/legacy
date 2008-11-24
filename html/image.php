<?php
// $Id: image.php,v 1.1 2007/05/15 02:34:30 minahito Exp $
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

// Disable image content invalid rewriting by mbstring
if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}

$image_id = isset ($_GET['id']) ? intval($_GET['id']) : 0;
if ($image_id > 0) {
	include './mainfile.php';
	$imagehandler = & xoops_gethandler('image');
	$criteria = new CriteriaCompo(new Criteria('i.image_display', 1));
	$criteria->add(new Criteria('i.image_id', $image_id));
	$image = & $imagehandler->getObjects($criteria, false, true);
	if (count($image) > 0) {
		header('Content-type: '.$image[0]->getVar('image_mimetype'));
		header('Cache-control: max-age=31536000');
		header('Expires: '.gmdate("D, d M Y H:i:s", time() + 31536000).'GMT');
		header('Content-disposition: filename='.$image[0]->getVar('image_name'));
		header('Content-Length: '.strlen($image[0]->getVar('image_body')));
		header('Last-Modified: '.gmdate("D, d M Y H:i:s", $image[0]->getVar('image_created')).'GMT');
		echo $image[0]->getVar('image_body');
		exit ();
	}
}
header('Content-type: image/gif');
readfile('./images/blank.gif');

?>