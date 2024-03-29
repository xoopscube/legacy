<?php
/**
 * Write to several writers
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
 * Write to several writers
 */
class File_Archive_Writer_Multi extends File_Archive_Writer {
	/**
	 * @var File_Archive_Writer_Writer Data will be copied to these two writers
	 * @access private
	 */
	public $writers;

	public function addWriter( &$writer ) {
		$this->writers[] =& $writer;
	}

	/**
	 * @see File_Archive_Writer::newFile()
	 */
	public function newFile( $filename, $stat = array(), $mime = "application/octet-stream" ) {
		$globalError = null;
		foreach ( $this->writers as $key => $foo ) {
			$error = $this->writers[ $key ]->newFile( $filename, $stat, $mime );
			if ( ( new PEAR )->isError( $error ) ) {
				$globalError = $error;
			}
		}
		if ( ( new PEAR )->isError( $globalError ) ) {
			return $globalError;
		}
	}

	/**
	 * @see File_Archive_Writer::newFileNeedsMIME()
	 */
	public function newFileNeedsMIME() {
		foreach ( $this->writers as $key => $foo ) {
			if ( $this->writers[ $key ]->newFileNeedsMIME() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @see File_Archive_Writer::writeData()
	 */
	public function writeData( $data ) {
		$globalError = null;
		foreach ( $this->writers as $key => $foo ) {
			$error = $this->writers[ $key ]->writeData( $data );
			if ( ( new PEAR )->isError( $error ) ) {
				$globalError = $error;
			}
		}
		if ( ( new PEAR )->isError( $globalError ) ) {
			return $globalError;
		}
	}

	/**
	 * @see File_Archive_Writer::writeFile()
	 */
	public function writeFile( $filename ) {
		$globalError = null;
		foreach ( $this->writers as $key => $foo ) {
			$error = $this->writers[ $key ]->writeFile( $filename );
			if ( ( new PEAR )->isError( $error ) ) {
				$globalError = $error;
			}
		}
		if ( ( new PEAR )->isError( $globalError ) ) {
			return $globalError;
		}
	}

	/**
	 * @see File_Archive_Writer::close()
	 */
	public function close() {
		$globalError = null;
		foreach ( $this->writers as $key => $foo ) {
			$error = $this->writers[ $key ]->close();
			if ( ( new PEAR )->isError( $error ) ) {
				$globalError = $error;
			}
		}
		if ( ( new PEAR )->isError( $globalError ) ) {
			return $globalError;
		}
	}
}
