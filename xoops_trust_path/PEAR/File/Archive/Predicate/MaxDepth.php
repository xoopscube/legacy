<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Remove the URLs with a too high number of nested directories
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
 * Remove the URLs with a too high number of nested directories
 *
 * @see        File_Archive_Predicate, File_Archive_Reader_Filter
 */
class File_Archive_Predicate_MaxDepth extends File_Archive_Predicate {
	public $maxDepth;

	/**
	 * @param int $maxDepth Maximal number of folders before the actual file in
	 *        $source->getFilename().
	 *        '1/2/3/4/foo.txt' will be accepted with $maxDepth == 4 and
	 *        rejected with $maxDepth == 5
	 */
	public function __construct( $maxDepth ) {
		$this->maxDepth = $maxDepth;
	}

	/**
	 * @see File_Archive_Predicate::isTrue()
	 */
	public function isTrue( &$source ) {
		$url = parse_url( $source->getFilename() );

		return substr_count( $url['path'], '/' ) <= $this->maxDepth;
	}
}
