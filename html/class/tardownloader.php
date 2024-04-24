<?php
/**
 * Send tar files through a http socket
 * @package    kernel
 * @subpackage util
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

/**
 * base class
 */
include_once XOOPS_ROOT_PATH.'/class/downloader.php';
/**
 * Class to handle tar files
 */
include_once XOOPS_ROOT_PATH.'/class/class.tar.php';


class XoopsTarDownloader extends XoopsDownloader
{

    /**
     * Constructor
     *
     * @param string $ext       file extension
     * @param string $mimyType  Mimetype
     **/
    public function __construct($ext = '.tar.gz', $mimyType = 'application/x-gzip')
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
    public function addFile($filepath, $newfilename=null)
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
    public function addBinaryFile($filepath, $newfilename=null)
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
     * @param   string $data       Data to write
     * @param   string $filename   Name for the file in the archive
     * @param int      $time
     **/
    public function addFileData(&$data, $filename, $time=0)
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
                if (0 != $time) {
                    $this->archiver->files[$i]['time'] = $time;
                }
                break;
            }
        }
    }

    /**
     * Add a binary dummy file to the archive
     *
     * @param   string $data   Data to write
     * @param   string $filename   Name for the file in the archive
     * @param int      $time
     **/
    public function addBinaryFileData(&$data, $filename, $time=0)
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
                if (0 != $time) {
                    $this->archiver->files[$i]['time'] = $time;
                }
                break;
            }
        }
    }

    /**
     * Send the file to the client
     *
     * @param   string $name Filename
     * @param bool     $gzip Use GZ compression
     **/
    public function download($name, $gzip = true)
    {
        $file = $this->archiver->toTarOutput($name.$this->ext, $gzip);
        $this->_header($name.$this->ext);
        header('Content-Type: application/x-tar') ;
        header('Content-Length: ' . (float)@strlen($file)) ;
        echo $file;
    }
}
