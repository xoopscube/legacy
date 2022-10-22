<?php
/**
 * Writer wrapper that adds a directory to the written file
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

require_once "File/Archive/Writer.php";

/**
 * Writer wrapper that adds a directory to the written file
 */
class File_Archive_Writer_AddBaseName {
	public $writer;
	public $baseName;

	public function __construct( $baseName, &$writer ) {
		if ( substr( $baseName, - 1 ) == '/' ) {
			$this->baseName = $baseName;
		} else {
			$this->baseName = $baseName . '/';
		}

		$this->writer =& $writer;
	}

	/**
	 * @see File_Archive_Writer::newFile()
	 */
	public function newFile( $filename, $stat = array(), $mime = "application/octet-stream" ) {
		$this->writer->newFile( $this->baseName . $filename, $stat, $mime );
	}

	/**
	 * @see File_Archive_Writer::newFromTempFile()
	 */
	public function newFromTempFile( $tmpfile, $filename, $stat = array(), $mime = "application/octet-stream" ) {
		$this->writer->newFromTempFile( $tmpfile, $this->baseName . $filename, $stat, $mime );
	}

	/**
	 * @see File_Archive_Writer::newFileNeedsMIME()
	 */
	public function newFileNeedsMIME() {
		return $this->writer->newFileNeedsMIME();
	}

	/**
	 * @see File_Archive_Writer::writeData()
	 */
	public function writeData( $data ) {
		$this->writer->writeData( $data );
	}

	/**
	 * @see File_Archive_Writer::writeFile()
	 */
	public function writeFile( $filename ) {
		$this->writer->writeFile( $filename );
	}

	/**
	 * @see File_Archive_Writer::close()
	 */
	public function close() {
		$this->writer->close();
	}
}
