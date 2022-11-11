<?php
/**
 * Regroups several readers to make them appear as a single one
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
 * Regroups several readers to make them appear as a single one
 */
class File_Archive_Reader_Multi extends File_Archive_Reader_Relay {
	/**
	 * @var Array All the sources regrouped in this reader
	 * @access private
	 */
	public $sources = array();
	/**
	 * @var Int Index of the source being read currently
	 * @access private
	 */
	public $currentIndex = 0;

	public function __construct() {
		parent::File_Archive_Reader_Relay( $tmp = null );
	}

	/**
	 * Add a new reader to the list of readers
	 *
	 * @param File_Archive_Reader $source The source to add
	 */
	public function addSource( &$source ) {
		$this->sources[] =& $source;
	}

	/**
	 * @see File_Archive_Reader::next()
	 */
	public function next() {
		while ( array_key_exists( $this->currentIndex, $this->sources ) ) {
			$this->source =& $this->sources[ $this->currentIndex ];

			if ( ( $error = $this->source->next() ) === false ) {
				$error = $this->source->close();
				if ( ( new PEAR )->isError( $error ) ) {
					return $error;
				}
				$this->currentIndex ++;
			} else {
				return $error;
			}
		}

		return false;
	}

	/**
	 * @see File_Archive_Reader::close()
	 */
	public function close() {
		$error              = parent::close();
		$this->currentIndex = 0;

		return $error;
	}
}
