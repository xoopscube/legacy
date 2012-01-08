<?php
// $Id: tardownloader.php,v 1.1 2007/05/15 02:34:21 minahito Exp $
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

/**
 * base class
 */
include_once XOOPS_ROOT_PATH.'/class/downloader.php';
/**
 * Class to handle tar files
 */
include_once XOOPS_ROOT_PATH.'/class/class.tar.php';

/**
 * Send tar files through a http socket
 *
 * @package		kernel
 * @subpackage	core
 *
 * @author		Kazumi Ono 	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsTarDownloader extends XoopsDownloader
{

	/**
	 * Constructor
	 *
	 * @param string $ext       file extension
	 * @param string $mimyType  Mimetype
	 **/
	function XoopsTarDownloader($ext = '.tar.gz', $mimyType = 'application/x-gzip')
	{
		$this->archiver = new tar();
		$this->ext = trim($ext);
		$this->mimeType = trim($mimyType);
	}

	/**
	 * Add a file to the archive
	 *
	 * @param   string  $filepath       Full path to the file
	 * @param   string  $newfilename    Filename (if you don't want to use the original)
	 **/
	function addFile($filepath, $newfilename=null)
	{
		$this->archiver->addFile($filepath);
		if (isset($newfilename)) {
			// dirty, but no other way
			for ($i = 0; $i < $this->archiver->numFiles; $i++) {
				if ($this->archiver->files[$i]['name'] == $filepath) {
					$this->archiver->files[$i]['name'] = trim($newfilename);
					break;
				}
			}
		}
	}

	/**
	 * Add a binary file to the archive
	 *
	 * @param   string  $filepath       Full path to the file
	 * @param   string  $newfilename    Filename (if you don't want to use the original)
	 **/
	function addBinaryFile($filepath, $newfilename=null)
	{
		$this->archiver->addFile($filepath, true);
		if (isset($newfilename)) {
			// dirty, but no other way
			for ($i = 0; $i < $this->archiver->numFiles; $i++) {
				if ($this->archiver->files[$i]['name'] == $filepath) {
					$this->archiver->files[$i]['name'] = trim($newfilename);
					break;
				}
			}
		}
	}

	/**
	 * Add a dummy file to the archive
	 *
	 * @param   string  $data       Data to write
	 * @param   string  $filename   Name for the file in the archive
	 * @param   integer $time
	 **/
	function addFileData(&$data, $filename, $time=0)
	{
		$dummyfile = XOOPS_CACHE_PATH.'/dummy_'.time().'.html';
		$fp = fopen($dummyfile, 'w');
		fwrite($fp, $data);
		fclose($fp);
		$this->archiver->addFile($dummyfile);
		unlink($dummyfile);

		// dirty, but no other way
		for ($i = 0; $i < $this->archiver->numFiles; $i++) {
			if ($this->archiver->files[$i]['name'] == $dummyfile) {
				$this->archiver->files[$i]['name'] = $filename;
				if ($time != 0) {
					$this->archiver->files[$i]['time'] = $time;
				}
				break;
			}
		}
	}

	/**
	 * Add a binary dummy file to the archive
	 *
	 * @param   string  $data   Data to write
	 * @param   string  $filename   Name for the file in the archive
	 * @param   integer $time
	 **/
	function addBinaryFileData(&$data, $filename, $time=0)
	{
		$dummyfile = XOOPS_CACHE_PATH.'/dummy_'.time().'.html';
		$fp = fopen($dummyfile, 'wb');
		fwrite($fp, $data);
		fclose($fp);
		$this->archiver->addFile($dummyfile, true);
		unlink($dummyfile);

		// dirty, but no other way
		for ($i = 0; $i < $this->archiver->numFiles; $i++) {
			if ($this->archiver->files[$i]['name'] == $dummyfile) {
				$this->archiver->files[$i]['name'] = $filename;
				if ($time != 0) {
					$this->archiver->files[$i]['time'] = $time;
				}
				break;
			}
		}
	}

	/**
	 * Send the file to the client
	 *
	 * @param   string  $name   Filename
	 * @param   boolean $gzip   Use GZ compression
	 **/
	function download($name, $gzip = true)
	{
		$file = $this->archiver->toTarOutput($name.$this->ext, $gzip);
		$this->_header($name.$this->ext);
		header('Content-Type: application/x-tar') ;
		header('Content-Length: '.floatval(@strlen($file))) ;
		echo $file;
	}
}
?>