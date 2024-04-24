<?php
/**
 * Zip downloader
 * @package    class
 * @subpackage core
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
include_once XOOPS_ROOT_PATH.'/class/downloader.php';
include_once XOOPS_ROOT_PATH.'/class/class.zipfile.php';

class XoopsZipDownloader extends XoopsDownloader
{
    public function __construct($ext = '.zip', $mimyType = 'application/x-zip')
    {
        $this->archiver = new zipfile();
        $this->ext      = trim($ext);
        $this->mimeType = trim($mimyType);
    }

    public function addFile($filepath, $newfilename=null)
    {
        // Read in the file's contents
        $fp = fopen($filepath, 'rb');
        $data = fread($fp, filesize($filepath));
        fclose($fp);
        $filename = (isset($newfilename) && '' !== trim($newfilename)) ? trim($newfilename) : $filepath;
        $filepath = is_file($filename) ? $filename : $filepath;
        $this->archiver->addFile($data, $filename, filemtime($filepath));
    }

    public function addBinaryFile($filepath, $newfilename=null)
    {
        // Read in the file's contents
        $fp = fopen($filepath, 'rb');
        $data = fread($fp, filesize($filepath));
        fclose($fp);
        $filename = (isset($newfilename) && '' !== trim($newfilename)) ? trim($newfilename) : $filepath;
        $filepath = is_file($filename) ? $filename : $filepath;
        $this->archiver->addFile($data, $filename, filemtime($filepath));
    }

    public function addFileData(&$data, $filename, $time=0)
    {
        $this->archiver->addFile($data, $filename, $time);
    }

    public function addBinaryFileData(&$data, $filename, $time=0)
    {
        $this->addFileData($data, $filename, $time);
    }

    public function download($name, $gzip = true)
    {
        $file = $this->archiver->file();
        $this->_header($name.$this->ext);
        header('Content-Type: application/zip') ;
        header('Content-Length: ' . (float)@strlen($file)) ;
        echo $file;
    }
}
