<?php
/**
 * Filter out the files that do not respect a given predicat
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

require_once "File/Archive/Reader/Relay.php";

/**
 * Filter out the files that do not respect a given predicat
 */
class File_Archive_Reader_Filter extends File_Archive_Reader_Relay {
	/**
	 * @var File_Archive_Reader_Predicat
	 * @access private
	 */
	public $predicate;

	/**
	 * $source is the reader to filter
	 */
	public function __construct( $predicate, &$source ) {
		parent::File_Archive_Reader_Relay( $source );
		$this->predicate = $predicate;
	}

	/**
	 * @see File_Archive_Reader::next()
	 */
	public function next() {
		do {
			$error = $this->source->next();
			if ( $error !== true ) {
				return $error;
			}
		} while ( ! $this->predicate->isTrue( $this->source ) );

		return true;
	}

	/**
	 * @see File_Archive_Reader::select()
	 */
	public function select( $filename, $close = true ) {
		if ( $close ) {
			$error = $this->close();
			if ( ( new PEAR )->isError( $error ) ) {
				return $error;
			}
		}

		do {
			$error = $this->source->select( $filename, false );
			if ( $error !== true ) {
				return $error;
			}
		} while ( ! $this->predicate->isTrue( $this->source ) );

		return true;
	}
}
