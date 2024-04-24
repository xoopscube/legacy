<?php
/**
 * Sends non HTML files through a http socket
 * @package    kernel
 * @subpackage core
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


class XoopsDownloader
{

    /**#@+
     * file information
     */
    public $mimetype;
    public $ext;
    public $archiver;
    /**#@-*/

    /**
     * Constructor
     */
    public function __construct()
    {
        //EMPTY
    }

    /**
     * Send the HTTP header
     *
     * @param	string  $filename
     *
     * @access	private
     */
    public function _header($filename)
    {
        if (function_exists('mb_http_output')) {
            mb_http_output('pass');
        }
        header('Content-Type: '.$this->mimetype);
        if (preg_match("/MSIE ([0-9]\.[0-9]{1,2})/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: inline; filename="'.$filename.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Expires: 0');
            header('Pragma: no-cache');
        }
    }

    /**
     * XoopsDownloader::addFile()
     *
     * @param   string  $filepath
     * @param   string   $newfilename
     **/
    public function addFile($filepath, $newfilename=null)
    {
        //EMPTY
    }

    /**
     * XoopsDownloader::addBinaryFile()
     *
     * @param   string  $filepath
     * @param   string  $newfilename
     **/
    public function addBinaryFile($filepath, $newfilename=null)
    {
        //EMPTY
    }

    /**
     * XoopsDownloader::addFileData()
     *
     * @param   mixed  $data
     * @param   string $filename
     * @param int      $time
     **/
    public function addFileData(&$data, $filename, $time=0)
    {
        //EMPTY
    }

    /**
     * XoopsDownloader::addBinaryFileData()
     *
     * @param   mixed  $data
     * @param   string $filename
     * @param int      $time
     **/
    public function addBinaryFileData(&$data, $filename, $time=0)
    {
        //EMPTY
    }

    /**
     * XoopsDownloader::download()
     *
     * @param   string $name
     * @param bool     $gzip
     **/
    public function download($name, $gzip = true)
    {
        //EMPTY
    }
}
