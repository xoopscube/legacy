<?php

/**
 * Unicode Normalizer
 *
 * PHP version 5
 *
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * + Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * + Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * + The names of its contributors may not be used to endorse or
 * promote products derived from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Internationalization
 * @package   I18N_UnicodeNormalizer
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   SVN: $Id: File.php 38 2007-07-23 11:42:30Z mcorne $
 * @link      http://pear.php.net/package/I18N_UnicodeNormalizer
 */

/**
 * File management
 *
 * @category  Internationalization
 * @package   I18N_UnicodeNormalizer
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @version   Release: @package_version@
 * @version   SVN: $Id: File.php 38 2007-07-23 11:42:30Z mcorne $
 * @link      http://pear.php.net/package/I18N_UnicodeNormalizer
 */
class I18N_UnicodeNormalizer_File
{
    /**
     * The header of programmatically generated files
     *
     * @var    array  
     * @access private
     */
    private $header = array(// /
        '<?php',
        '/**',
        ' * Unicode Normalizer',
        ' *',
        ' * File: %1$s', // file name
        ' * Generated automatically %2$s', // class + method name
        ' * %3$s', // data source file
        ' * Date: %4$s', // file creation date
        ' * %5$s', // additional comments
        ' * DO NOT MODIFY !',
        ' *',
        ' * PHP version 5',
        ' *',
        ' * All rights reserved.',
        ' * Redistribution and use in source and binary forms, with or without modification,',
        ' * are permitted provided that the following conditions are met:',
        ' * + Redistributions of source code must retain the above copyright notice,',
        ' * this list of conditions and the following disclaimer.',
        ' * + Redistributions in binary form must reproduce the above copyright notice,',
        ' * this list of conditions and the following disclaimer in the documentation and/or',
        ' * other materials provided with the distribution.',
        ' * + The names of its contributors may not be used to endorse or',
        ' * promote products derived from this software without specific prior written permission.',
        ' * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS',
        ' * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT',
        ' * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR',
        ' * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR',
        ' * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,',
        ' * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,',
        ' * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR',
        ' * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF',
        ' * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING',
        ' * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS',
        ' * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.',
        ' *',
        ' * @category Internationalization',
        ' * @package I18N_UnicodeNormalizer',
        ' * @author Michel Corne <mcorne@yahoo.com>',
        ' * @copyright 2007 Michel Corne',
        ' * @license http://www.opensource.org/licenses/bsd-license.php The BSD License',
        // the SVN tag is set dynamically so it does get changed during the versioning of this file
        ' * @version SVN: $%6$s$',
        ' * @link http://pear.php.net/package/I18N_UnicodeNormalizer',
        ' */',
        '',
        'return %7$s;', // the data dumped into an array
        '?>',
        '',
        );

    /**
     * The class constructor
     *
     * @return void  
     * @access public
     */
    public function __construct()
    {
        // sets the default time zone to UTC
        date_default_timezone_set('UTC');
        // converts the header to a string
        $this->header = implode("\n", $this->header);
    }

    /**
     * Exports data into a PHP array
     *
     * Creates the file header. Exports the data. Creates the file.
     * <pre>
     * File format: &lt;?php /** header... * / return array(...); ?>
     * File usage: $data = include 'foo.php';
     * </pre>
     *
     * @param  string $fileName    the file name
     * @param  mixed  $data        the data
     * @param  string $generatedBy the class::method that generated the data,
     *                             typically the function calling this method,
     *                             default is no information
     * @param  string $sourceName  the name of the file the data was generated from,
     *                             default is no information
     * @param  string $comment     a comment, default is no comment
     * @param  string $date        the date the file is created at,
     *                             the current date is used by default
     * @return void  
     * @access public
     */
    public function put($fileName, $data, $generatedBy = '', $sourceName = '', $comment = '', $date = '')
    {
        // sets this file name, and the file name this file is generated from
        $generatedBy and $generatedBy = "by: $generatedBy";
        $sourceName and $sourceName = "From: $sourceName";
        // defaults the date to the current date
        $date or $date = date(DATE_COOKIE);
        // exports the data into a PHP array to be returned from an include statement
        $data = var_export($data, true);
        $string = sprintf($this->header, $fileName, $generatedBy, $sourceName, $date, $comment, 'Id', $data);

        file_put_contents($fileName, $string);
    }
}

?>