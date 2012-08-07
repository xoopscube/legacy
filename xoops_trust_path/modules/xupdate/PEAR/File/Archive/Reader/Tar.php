<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Read a tar archive
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
 * @version    CVS: $Id: Tar.php,v 1.31 2008/06/04 15:56:09 cbrunet Exp $
 * @link       http://pear.php.net/package/File_Archive
 */

require_once "File/Archive/Reader/Archive.php";

/**
 * Read a tar archive
 */
class File_Archive_Reader_Tar extends File_Archive_Reader_Archive
{
    /**
     * @var String Name of the file being read
     * @access private
     */
    var $currentFilename = null;
    /**
     * @var Array Stats of the file being read
     *            In TAR reader, indexes 2, 4, 5, 7, 9 are set
     * @access private
     */
    var $currentStat = null;
    /**
     * @var int Number of bytes that still have to be read before the end of
     *          file
     * @access private
     */
    var $leftLength = 0;
    /**
     * @var int Size of the footer
     *          A TAR file is made of chunks of 512 bytes. If 512 does not
     *          divide the file size a footer is added
     * @access private
     */
    var $footerLength = 0;
    /**
     * @var int nb bytes to seek back in order to reach the end of the archive
     *          or null if the end of the archive has not been reached
     */
    var $seekToEnd = null;

    /**
     * @see File_Archive_Reader::skip()
     */
    function skip($length = -1)
    {
        if ($length == -1) {
            $length = $this->leftLength;
        } else {
            $length = min($this->leftLength, $length);
        }
        $skipped = $this->source->skip($length);
        if (!PEAR::isError($skipped)) {
            $this->leftLength -= $skipped;
        }
        return $skipped;
    }

    /**
     * @see File_Archive_Reader::rewind()
     */
    function rewind($length = -1)
    {
        if ($length == -1) {
            $length = $this->currentStat[7] - $this->leftLength;
        } else {
            $length = min($length, $this->currentStat[7] - $this->leftLength);
        }
        $rewinded = $this->source->rewind($length);
        if (!PEAR::isError($rewinded)) {
            $this->leftLength += $rewinded;
        }
        return $rewinded;
    }

    /**
     * @see File_Archive_Reader::tell()
     */
    function tell()
    {
        return $this->currentStat[7] - $this->leftLength;
    }

    /**
     * @see File_Archive_Reader::close()
     */
    function close()
    {
        $this->leftLength = 0;
        $this->currentFilename = null;
        $this->currentStat = null;
        $this->seekToEnd = null;
        return parent::close();
    }

    /**
     * @see File_Archive_Reader::getFilename()
     */
    function getFilename() { return $this->currentFilename; }
    /**
     * @see File_Archive_Reader::getStat()
     */
    function getStat() { return $this->currentStat; }

    /**
     * @see File_Archive_Reader::next()
     */
    function next()
    {
        $error = parent::next();
        if ($error !== true) {
            return $error;
        }

        if ($this->seekToEnd !== null) {
            return false;
        }
        
        while (true) {
			//Advance $this
			$header = $this->_nextAdvance();
			if ((!$header) || PEAR::isError($header)) {
                return $header;
            }
            
			//Are we looking at a Long Link?
			if ($header['type'] == 'L') {
				//This is a filepath too long for the tar format.
				//So the tar specification puts the name in a special entry just before the real data
				//This means the filename is the current piece of data.  Grab it.
				$filename = '';
				while (($str = $this->getData(256)) !== null) {
					if (PEAR::isError($str)) {
                        return $str;
                    }
					$filename .= $str;
				}
				
				//The actual file data is the next item.  Advance there and set the filename to what we just made.
				//Everything about the "next" item is correct except the file name.
				$header = $this->_nextAdvance();
				if ((!$header) || PEAR::isError($header)) {
                    return $header;
                }
				$this->currentFilename = $filename;
			}
			/**
             * Note that actions taken above to handle LongLink may have advanced $this and reset some vars.
             * But that just leaves us in a state to actually handle the thing as if it were a normal file.
             * So continue as if this never happened...
             */
			
			//Other than the above we only care about regular files.
			//NOTE: Any non-numeric type codes will == 0
			//We handle 'L' above, I don't know what others are out there.
            //5 == directory
			if ($header['type'] == 0 || $header['type'] == 5) {
                break;
            }
		}
        return true;
    }
    
    /**
	 * Performs the actual advancement to the next item in the underlying structure
	 * We encapsulate it in a separate function because ot things like @LongLink, where the
	 * next item is part of the current one.
     *
     * @access private
     * @author Josh Vermette (josh@calydonian.com)
     */
	function _nextAdvance() 
    {
		$error = $this->source->skip($this->leftLength + $this->footerLength);
		if (PEAR::isError($error)) {
			return $error;
		}

		$rawHeader = $this->source->getData(512);
		if (PEAR::isError($rawHeader)) {
			return $rawHeader;
		}

		if (strlen($rawHeader)<512 || $rawHeader == pack("a512", "")) {
			$this->seekToEnd = strlen($rawHeader);
			$this->currentFilename = null;
			return false;
		}

		$header = unpack(
                         "a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/".
                         "a8checksum/a1type/a100linkname/a6magic/a2version/".
                         "a32uname/a32gname/a8devmajor/a8devminor/a155prefix",
                         $rawHeader);
			
		$this->currentStat = array(
                                   2 => octdec($header['mode']),
                                   4 => octdec($header['uid']),
                                   5 => octdec($header['gid']),
                                   7 => octdec($header['size']),
                                   9 => octdec($header['mtime'])
                                   );
		$this->currentStat['mode']  = $this->currentStat[2];
		$this->currentStat['uid']   = $this->currentStat[4];
		$this->currentStat['gid']   = $this->currentStat[5];
		$this->currentStat['size']  = $this->currentStat[7];
		$this->currentStat['mtime'] = $this->currentStat[9];

		if ($header['magic'] == 'ustar') {
			$this->currentFilename = $this->getStandardURL(
                                                           $header['prefix'] . $header['filename']
                                                           );
		} else {
			$this->currentFilename = $this->getStandardURL(
                                                           $header['filename']
                                                           );
		}
        
		$this->leftLength = $this->currentStat[7];
		if ($this->leftLength % 512 == 0) {
			$this->footerLength = 0;
		} else {
			$this->footerLength = 512 - $this->leftLength%512;
		}

		$checksum = 8*ord(" ");
		for ($i = 0; $i < 148; $i++) {
			$checksum += ord($rawHeader{$i});
		}

		for ($i = 156; $i < 512; $i++) {
			$checksum += ord($rawHeader{$i});
		}
		
        if (octdec($header['checksum']) != $checksum) {
			die('Checksum error on entry '.$this->currentFilename);
		}
        
		return $header;
	}	

    /**
     * @see File_Archive_Reader::getData()
     */
    function getData($length = -1)
    {
        if ($length == -1) {
            $actualLength = $this->leftLength;
        } else {
            $actualLength = min($this->leftLength, $length);
        }

        if ($this->leftLength == 0) {
            return null;
        } else {
            $data = $this->source->getData($actualLength);
            if (strlen($data) != $actualLength) {
                return PEAR::raiseError('Unexpected end of tar archive');
            }
            $this->leftLength -= $actualLength;
            return $data;
        }
    }

    /**
     * @see File_Archive_Reader::makeWriterRemoveFiles()
     */
    function makeWriterRemoveFiles($pred)
    {
        require_once "File/Archive/Writer/Tar.php";

        $blocks = array();
        $seek = null;
        $gap = 0;
        if ($this->currentFilename !== null && $pred->isTrue($this)) {
            $seek = 512 + $this->currentStat[7] + $this->footerLength;
            $blocks[] = $seek; //Remove this file
        }

        while (($error = $this->next()) === true) {
            $size = 512 + $this->currentStat[7] + $this->footerLength;
            if ($pred->isTrue($this)) {
                if ($seek === null) {
                    $seek = $size;
                    $blocks[] = $size;
                } else if ($gap > 0) {
                    $blocks[] = $gap; //Don't remove the files between the gap
                    $blocks[] = $size;
                    $seek += $size;
                } else {
                    $blocks[count($blocks)-1] += $size;   //Also remove this file
                    $seek += $size;
                }
                $gap = 0;
            } else {
                if ($seek !== null) {
                    $seek += $size;
                    $gap += $size;
                }
            }
        }
        if ($seek === null) {
            $seek = $this->seekToEnd;
        } else {
            $seek += $this->seekToEnd;
            if ($gap == 0) {
                array_pop($blocks);
            } else {
                $blocks[] = $gap;
            }
        }

        $writer = new File_Archive_Writer_Tar(null,
            $this->source->makeWriterRemoveBlocks($blocks, -$seek)
        );
        $this->close();
        return $writer;
    }

    /**
     * @see File_Archive_Reader::makeWriterRemoveBlocks()
     */
    function makeWriterRemoveBlocks($blocks, $seek = 0)
    {
        if ($this->seekToEnd !== null || $this->currentStat === null) {
            return PEAR::raiseError('No file selected');
        }

        $blockPos = $this->currentStat[7] - $this->leftLength + $seek;

        $this->rewind();
        $keep = false;

        $data = $this->getData($blockPos);
        foreach ($blocks as $length) {
            if ($keep) {
                $data .= $this->getData($length);
            } else {
                $this->skip($length);
            }
            $keep = !$keep;
        }
        if ($keep) {
            $data .= $this->getData();
        }

        $filename = $this->currentFilename;
        $stat = $this->currentStat;

        $writer = $this->makeWriterRemove();
        if (PEAR::isError($writer)) {
            return $writer;
        }

        unset($stat[7]);
        $stat[9] = $stat['mtime'] = time();
        $writer->newFile($filename, $stat);
        $writer->writeData($data);
        return $writer;
    }

    /**
     * @see File_Archive_Reader::makeAppendWriter
     */
    function makeAppendWriter()
    {
        require_once "File/Archive/Writer/Tar.php";

        while (($error = $this->next()) === true) { }
        if (PEAR::isError($error)) {
            $this->close();
            return $error;
        }

        $innerWriter = $this->source->makeWriterRemoveBlocks(array(), -$this->seekToEnd);
        if (PEAR::isError($innerWriter)) {
            return $innerWriter;
        }

        $this->close();
        return new File_Archive_Writer_Tar(null, $innerWriter);
    }
}

?>
