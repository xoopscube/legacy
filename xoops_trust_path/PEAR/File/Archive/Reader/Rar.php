<?php
/**
 * Read a rar archive, requires PECL rar extension
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

require_once "File/Archive/Reader/Archive.php";
require_once "File/Archive/Writer/Files.php";
require_once "File/Archive/Reader/File.php";

/**
 * Read a rar archive, requires PECL rar extension
 */
class File_Archive_Reader_Rar extends File_Archive_Reader_Archive {
	public $rarFile = null;
	public $rarEntry = null;
	public $rarList = array();

	public $rarTmpName = null;
	public $entryTmpName = null;
	public $fileReader = null;

	/**
	 * @see File_Archive_Reader::next()
	 */
	public function next() {
		$error = parent::next();
		if ( ( new PEAR )->isError( $error ) ) {
			return $error;
		}

		if ( $this->rarFile === null ) {
			$dataFilename = $this->source->getDataFilename();
			if ( $dataFilename !== null ) {
				$this->rarTmpName = null;
				$this->rarFile    = rar_open( $dataFilename );
			} else {
				$this->rarTmpName = tempnam( ( new File_Archive )->getOption( 'tmpDirectory' ), 'far' );

				//Generate the tmp data
				$dest = new File_Archive_Writer_Files();
				$dest->newFile( $this->tmpName );
				$this->source->sendData( $dest );
				$dest->close();

				$this->rarFile = rar_open( $this->tmpName );
			}
			if ( ! $this->rarFile ) {
				return PEAR::raiseError( "Unable to open rar file $dataFilename" );
			}

			if ( $this->rarList === null ) {
				$this->rarList = rar_list( $this->rarFile );
				reset( $this->rarList );
			}
		}
		if ( $fileReader !== null ) {
			$this->fileReader->close();
			$this->fileReader = null;
		}

		$entryName    = next( $this->rarList );
		$this->source = null;
		if ( $entryName === false ) {
			return false;
		}
		$this->rarEntry = rar_entry_get( $this->rarFile, $entryName );
		if ( ! $this->rarEntry ) {
			return PEAR::raiseError( "Error reading entry $entryName" );
		}

		return true;
	}

	/**
	 * @see File_Archive_Reader::close()
	 */
	public function close() {
		rar_close( $this->rarEntry );
		if ( $this->fileReader !== null ) {
			$this->fileReader->close();
		}
		if ( $this->rarTmpName !== null ) {
			unlink( $this->rarTmpName );
		}
		if ( $this->entryTmpName !== null ) {
			unlink( $this->entryTmpName );
		}
		$this->rarFile  = null;
		$this->rarEntry = null;
		reset( $this->rarList );

		return parent::close();
	}

	/**
	 * Ensure data has been extracted to $this->entryTmpName
	 */
	public function ensureDataExtracted() {
		if ( $this->fileReader !== null ) {
			return;
		}
		if ( $this->entryTmpName === null ) {
			$this->entryTmpName = tempnam( ( new File_Archive )->getOption( 'tmpDirectory' ), 'far' );
		}
		$this->rarEntry->extract( false, $this->entryTmpName );
		$this->fileReader = new File_Archive_Reader_File(
			$this->entryTmpName, $this->rarEntry->getName()
		);
	}

	/**
	 * @see File_Archive_Reader::skip()
	 */
	public function skip( $length = - 1 ) {
		$this->ensureDataExtracted();

		return $this->fileReader->skip( $length );
	}

	/**
	 * @see File_Archive_Reader::rewind()
	 */
	public function rewind( $length = - 1 ) {
		$this->ensureDataExtracted();

		return $this->fileReader->rewind( $length );
	}

	/**
	 * @see File_Archive_Reader::tell()
	 */
	public function tell() {
		if ( $this->fileReader === null ) {
			return 0;
		} else {
			return $this->fileReader->tell();
		}
	}

	/**
	 * @see File_Archive_Reader::getFilename()
	 */
	public function getFilename() {
		return $this->rarEntry->getName();
	}

	/**
	 * @see File_Archive_Reader::getFileList()
	 */
	public function getFileList() {
		return $this->rarList;
	}

	/**
	 * @see File_Archive_Reader::getStat()
	 */
	public function getStat() {
		return $this->currentStat;
	}

	/**
	 * @see File_Archive_Reader::getDataFilename()
	 */
	public function getDataFilename() {
		$this->ensureDataExtracted();

		return $this->entryTmpName;
	}

	/**
	 * @see File_Archive_Reader::getData()
	 */
	public function getData( $length = - 1 ) {
		$this->ensureDataExtracted();

		return $this->fileReader->getData( $length );
	}
}
