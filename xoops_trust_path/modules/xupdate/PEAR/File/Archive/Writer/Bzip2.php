<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Compress a single file to Bzip2 format
 *
 * PHP versions 4 and 5
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330,Boston,MA 02111-1307 USA
 *
 * @category   File Formats
 * @package    File_Archive
 * @author     Vincent Lascaux <vincentlascaux@php.net>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/File_Archive
 */

require_once "File/Archive/Writer.php";

/**
 * Compress a single file to Bzip2 format
 */
class File_Archive_Writer_Bzip2 extends File_Archive_Writer
{

    /**
     * compressionLevel 
     * 
     * @var integer
     * @access public
     * @deprecated
     */
    public $compressionLevel=9;
    public $bzfile;
    public $tmpName;
    public $nbFiles = 0;

    public $innerWriter;
    public $autoClose;
    public $filename;
    public $stat;

    /**
     * @param string $filename Name to give to the archive
     * @param File_Archive_Writer $innerWriter The inner writer to which the
     *        compressed data will be written
     * @param array $stat The stat of the archive (see the PHP stat() function).
     *        No element are required in this array
     * @param bool $autoClose Indicate if the inner writer must be closed when
     *        closing this
     */
    public function File_Archive_Writer_Bzip2($filename, &$innerWriter,
                                       $stat = array(), $autoClose = true)
    {
        $this->innerWriter =& $innerWriter;
        $this->autoClose = $autoClose;

        $this->filename = $filename;
        $this->stat = $stat;

        if ($this->filename === null) {
            $this->newFile(null);
        }
    }

    /**
     * Set the compression level. Do nothing because PHP bz2 ext doesn't
     * support this.
     *
     * @param int $compressionLevel From 0 (no compression) to 9 (best
     *        compression)
     * @deprecated
     */
    public function setCompressionLevel($compressionLevel)
    {
        $this->compressionLevel = $compressionLevel;
    }

    /**
     * @see File_Archive_Writer::newFile()
     *
     * Check that one single file is written in the BZip2 archive
     */
    public function newFile($filename, $stat = array(),
                     $mime = "application/octet-stream")
    {
        if ($this->nbFiles > 1) {
            return PEAR::raiseError("A Bzip2 archive can only contain one single file.".
                                    "Use Tbz archive to be able to write several files");
        }
        $this->nbFiles++;

        $this->tmpName = tempnam(File_Archive::getOption('tmpDirectory'), 'far');
        $this->bzfile = bzopen($this->tmpName, 'w');

        return true;
    }

    /**
     * Actually write the tmp file to the inner writer
     * Close and delete temporary file
     *
     * @see File_Archive_Writer::close()
     */
    public function close()
    {
        bzclose($this->bzfile);

        if ($this->filename === null) {
            //Assume innerWriter is already opened on a file...
            $this->innerWriter->writeFile($this->tmpName);
            unlink($this->tmpName);
        } else {
            $this->innerWriter->newFromTempFile(
                $this->tmpName, $this->filename, $this->stat, 'application/x-compressed'
            );
        }

        if ($this->autoClose) {
            return $this->innerWriter->close();
        }
    }

    /**
     * @see File_Archive_Writer::writeData()
     */
    public function writeData($data)
    {
        bzwrite($this->bzfile, $data);
    }
}
