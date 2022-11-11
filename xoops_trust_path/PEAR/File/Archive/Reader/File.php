<?php
/**
 * Reader that represents a single file
 *
 * PHP versions 4 and 5
 * PHP version 7 (Nuno Luciano aka gigamaster)
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
 * @license    https://www.gnu.org/copyleft/lesser.html  LGPL
 * @version    CVS: $Id$
 * @link       https://pear.php.net/package/File_Archive
 */

require_once "File/Archive/Reader.php";
require_once "MIME/Type.php";

/**
 * Reader that represents a single file
 */
class File_Archive_Reader_File extends File_Archive_Reader {
	/**
	 * @var object Handle to the file being read
	 * @access private
	 */
	public $handle = null;
	/**
	 * @var string Name of the physical file being read
	 * @access private
	 */
	public $filename;
	/**
	 * @var string Name of the file returned by the reader
	 * @access private
	 */
	public $symbolic;
	/**
	 * @var array Stats of the file
	 *      Will only be set after a call to $this->getStat()
	 * @access private
	 */
	public $stat = null;
	/**
	 * @var string Mime type of the file
	 *      Will only be set after a call to $this->getMime()
	 */
	public $mime = null;
	/**
	 * @var boolean Has the file already been read
	 * @access private
	 */
	public $alreadyRead = false;

	/**
	 * $filename is the physical file to read
	 * $symbolic is the name declared by the reader
	 * If $symbolic is not specified, $filename is assumed
	 */
	public function __construct( $filename, $symbolic = null, $mime = null ) {
		$this->filename = $filename;
		$this->mime     = $mime;
		if ( $symbolic === null ) {
			$this->symbolic = $this->getStandardURL( $filename );
		} else {
			$this->symbolic = $this->getStandardURL( $symbolic );
		}
	}

	/**
	 * @see File_Archive_Reader::close()
	 *
	 * Close the file handle
	 */
	public function close() {
		$this->alreadyRead = false;
		if ( $this->handle !== null ) {
			fclose( $this->handle );
			$this->handle = null;
		}
	}

	/**
	 * @see File_Archive_Reader::next()
	 *
	 * The first time next is called, it will open the file handle and return
	 * true. Then it will return false
	 * Raise an error if the file does not exist
	 */
	public function next() {
		if ( $this->alreadyRead ) {
			return false;
		} else {
			$this->alreadyRead = true;

			return true;
		}
	}

	/**
	 * @see File_Archive_Reader::getFilename()
	 */
	public function getFilename() {
		return $this->symbolic;
	}

	/**
	 * @see File_Archive_Reader::getDataFilename()
	 *
	 * Return the name of the file
	 */
	public function getDataFilename() {
		return $this->filename;
	}

	/**
	 * @see File_Archive_Reader::getStat() stat()
	 */
	public function getStat() {
		if ( $this->stat === null ) {
			$this->stat = @stat( $this->filename );

			//If we can't use the stat function
			if ( $this->stat === false ) {
				$this->stat = array();
			}
		}

		return $this->stat;
	}

	/**
	 * @see File_Archive_Reader::getMime
	 */
	public function getMime() {
		if ( $this->mime === null ) {
			PEAR::pushErrorHandling( PEAR_ERROR_RETURN );
			$this->mime = MIME_Type::autoDetect( $this->getDataFilename() );
			PEAR::popErrorHandling();

			if ( ( new PEAR )->isError( $this->mime ) ) {
				$this->mime = parent::getMime();
			}
		}

		return $this->mime;
	}

	/**
	 * Opens the file if it was not already opened
	 */
	public function _ensureFileOpened() {
		if ( $this->handle === null ) {
			$this->handle = @fopen( $this->filename, "r" );

			if ( ! is_resource( $this->handle ) ) {
				$this->handle = null;

				return PEAR::raiseError( "Can't open {$this->filename} for reading" );
			}
			if ( $this->handle === false ) {
				$this->handle = null;

				return PEAR::raiseError( "File {$this->filename} not found" );
			}
		}
	}

	/**
	 * @see File_Archive_Reader::getData()
	 */
	public function getData( $length = - 1 ) {
		$error = $this->_ensureFileOpened();
		if ( ( new PEAR )->isError( $error ) ) {
			return $error;
		}

		if ( feof( $this->handle ) ) {
			return null;
		}
		if ( $length == - 1 ) {
			$contents  = '';
			$blockSize = ( new File_Archive )->getOption( 'blockSize' );
			// Ensure that magic_quote_runtime isn't set,
			// if we don't want to have corrupted archives.
			$saveMagicQuotes = get_magic_quotes_runtime();
			set_magic_quotes_runtime( 0 );
			while ( ! feof( $this->handle ) ) {
				$contents .= fread( $this->handle, $blockSize );
			}
			set_magic_quotes_runtime( $saveMagicQuotes );

			return $contents;
		} else {
			if ( $length == 0 ) {
				return "";
			} else {
				return fread( $this->handle, $length );
			}
		}
	}

	/**
	 * @see File_Archive_Reader::skip()
	 */
	public function skip( $length = - 1 ) {
		$error = $this->_ensureFileOpened();
		if ( ( new PEAR )->isError( $error ) ) {
			return $error;
		}

		$before = ftell( $this->handle );
		if ( ( $length == - 1 && @fseek( $this->handle, 0, SEEK_END ) === - 1 ) ||
		     ( $length >= 0 && @fseek( $this->handle, $length, SEEK_CUR ) === - 1 ) ) {
			return parent::skip( $length );
		} else {
			return ftell( $this->handle ) - $before;
		}
	}

	/**
	 * @see File_Archive_Reader::rewind
	 */
	public function rewind( $length = - 1 ) {
		if ( $this->handle === null ) {
			return 0;
		}

		$before = ftell( $this->handle );
		if ( ( $length == - 1 && @fseek( $this->handle, 0, SEEK_SET ) === - 1 ) ||
		     ( $length >= 0 && @fseek( $this->handle, - $length, SEEK_CUR ) === - 1 ) ) {
			return parent::rewind( $length );
		} else {
			return $before - ftell( $this->handle );
		}
	}

	/**
	 * @see File_Archive_Reader::tell()
	 */
	public function tell() {
		if ( $this->handle === null ) {
			return 0;
		} else {
			return ftell( $this->handle );
		}
	}


	/**
	 * @see File_Archive_Reader::makeWriterRemoveFiles()
	 */
	public function makeWriterRemoveFiles( $pred ) {
		return PEAR::raiseError(
			'File_Archive_Reader_File represents a single file, you cant remove it' );
	}

	/**
	 * @see File_Archive_Reader::makeWriterRemoveBlocks()
	 */
	public function makeWriterRemoveBlocks( $blocks, $seek = 0 ) {
		require_once "File/Archive/Writer/Files.php";

		$writer = new File_Archive_Writer_Files();

		$file = $this->getDataFilename();
		$pos  = $this->tell();
		$this->close();

		$writer->openFileRemoveBlock( $file, $pos + $seek, $blocks );

		return $writer;
	}

	/**
	 * @see File_Archive_Reader::makeAppendWriter
	 */
	public function makeAppendWriter() {
		return PEAR::raiseError(
			'File_Archive_Reader_File represents a single file.' .
			' makeAppendWriter cant be executed on it'
		);
	}
}
