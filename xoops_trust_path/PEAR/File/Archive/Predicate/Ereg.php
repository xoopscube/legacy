<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Keep only the files which name follow a given regular expression
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

require_once "File/Archive/Predicate.php";

/**
 * Keep only the files which name follow a given regular expression
 *
 * @see        File_Archive_Predicate, File_Archive_Reader_Filter ereg
 */
class File_Archive_Predicate_Ereg extends File_Archive_Predicate {
	public $ereg;

	/**
	 * @param string $ereg is the regular expression
	 */
	public function __construct( $ereg ) {
		$this->ereg = $ereg;
	}

	/**
	 * @see File_Archive_Predicate::isTrue()
	 */
	public function isTrue( &$source ) {
		return (bool) ereg( $this->ereg, $source->getFilename() );
	}
}
