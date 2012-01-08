<?php
// $Id: zipdownloader.php,v 1.1 2007/05/15 02:34:21 minahito Exp $
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
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
include_once XOOPS_ROOT_PATH.'/class/downloader.php';
include_once XOOPS_ROOT_PATH.'/class/class.zipfile.php';

class XoopsZipDownloader extends XoopsDownloader
{
    function XoopsZipDownloader($ext = '.zip', $mimyType = 'application/x-zip')
    {
        $this->archiver = new zipfile();
        $this->ext      = trim($ext);
        $this->mimeType = trim($mimyType);
    }

    function addFile($filepath, $newfilename=null)
    {
        // Read in the file's contents
        $fp = fopen($filepath, "r");
        $data = fread($fp, filesize($filepath));
        fclose($fp);
        $filename = (isset($newfilename) && trim($newfilename) != '') ? trim($newfilename) : $filepath;
        $filepath = is_file($filename) ? $filename : $filepath;
        $this->archiver->addFile($data, $filename, filemtime($filepath));
    }

    function addBinaryFile($filepath, $newfilename=null)
    {
        // Read in the file's contents
        $fp = fopen($filepath, "rb");
        $data = fread($fp, filesize($filepath));
        fclose($fp);
        $filename = (isset($newfilename) && trim($newfilename) != '') ? trim($newfilename) : $filepath;
        $filepath = is_file($filename) ? $filename : $filepath;
        $this->archiver->addFile($data, $filename, filemtime($filepath));
    }

    function addFileData(&$data, $filename, $time=0)
    {
        $this->archiver->addFile($data, $filename, $time);
    }

    function addBinaryFileData(&$data, $filename, $time=0)
    {
        $this->addFileData($data, $filename, $time);
    }

    function download($name, $gzip = true)
    {
        $file = $this->archiver->file();
        $this->_header($name.$this->ext);
        header('Content-Type: application/zip') ;
        header('Content-Length: '.floatval(@strlen($file))) ;
        echo $file;
    }
}
?>