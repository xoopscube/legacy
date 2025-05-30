<?php
/**
 * tar Class
 * @package    class
 * @subpackage core
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Other authors onokazu, 12/25/2002
 * @author     Josh Barger <joshb@npt.com>
 * @copyright  (c) 2002  Josh Barger
 * @license    GPL 2.0
 */

/**
 * tar Class
 *
 * This class reads and writes Tape-Archive (TAR) Files and Gzip
 * compressed TAR files, which are mainly used on UNIX systems.
 * This class works on both windows AND unix systems, and does
 * NOT rely on external applications!! Woohoo!
 */
class tar
{
    /**#@+
     * Unprocessed Archive Information
     */
    public $filename;
    public $isGzipped;
    public $tar_file;
    /**#@-*/

    /**#@+
     * Processed Archive Information
     */
    public $files;
    public $directories;
    public $numFiles;
    public $numDirectories;
    /**#@-*/

    /**
     * Class Constructor -- Does nothing...
     */
    public function __construct()
    {
        return true;
    }

    /**
     * Computes the unsigned Checksum of a file's header to try to ensure valid file
     * PHP8 Fatal error - Uncaught TypeError: Unsupported operand types: string + int
     * XCL 2.3.x PHP8 fix to add a string to an integer, replace $unsigned_chksum = ''; with $unsigned_chksum = null;
     * @param $bytestring
     *
     * @access    private
     * @return float|int|string
     */
    public function __computeUnsignedChecksum($bytestring)
    {
        $unsigned_chksum = null;
        for ($i=0; $i<512; $i++) {
            $unsigned_chksum += ord($bytestring[$i]);
        }
        for ($i=0; $i<8; $i++) {
            $unsigned_chksum -= ord($bytestring[148 + $i]);
        }
        $unsigned_chksum += ord(' ') * 8;

        return $unsigned_chksum;
    }


    /**
     * Converts a NULL padded string to a non-NULL padded string
     *
     * @param   string  $string
     *
     * @return  string
     *
     * @access	private
     **/
    public function __parseNullPaddedString($string)
    {
        $position = strpos($string, chr(0));
        return substr($string, 0, $position);
    }

    /**
     * This function parses the current TAR file
     *
     * @return  bool    always TRUE
     *
     * @access	private
     **/
    public function __parseTar()
    {
        // Read Files from archive
        $tar_length = strlen($this->tar_file);
        $main_offset = 0;
        $this->numFiles = 0;
        while ($main_offset < $tar_length) {
            // If we read a block of 512 nulls, we are at the end of the archive
            if (substr($this->tar_file, $main_offset, 512) == str_repeat(chr(0), 512)) {
                break;
            }

            // Parse file name
            $file_name        = $this->__parseNullPaddedString(substr($this->tar_file, $main_offset, 100));

            // Parse the file mode
            $file_mode        = substr($this->tar_file, $main_offset + 100, 8);

            // Parse the file user ID
            $file_uid        = octdec(substr($this->tar_file, $main_offset + 108, 8));

            // Parse the file group ID
            $file_gid        = octdec(substr($this->tar_file, $main_offset + 116, 8));

            // Parse the file size
            $file_size        = octdec(substr($this->tar_file, $main_offset + 124, 12));

            // Parse the file update time - unix timestamp format
            $file_time        = octdec(substr($this->tar_file, $main_offset + 136, 12));

            // Parse Checksum
            $file_chksum        = octdec(substr($this->tar_file, $main_offset + 148, 6));

            // Parse user name
            $file_uname        = $this->__parseNullPaddedString(substr($this->tar_file, $main_offset + 265, 32));

            // Parse Group name
            $file_gname        = $this->__parseNullPaddedString(substr($this->tar_file, $main_offset + 297, 32));

            // Make sure our file is valid
            if ($this->__computeUnsignedChecksum(substr($this->tar_file, $main_offset, 512)) != $file_chksum) {
                return false;
            }

            // Parse File Contents
            $file_contents        = substr($this->tar_file, $main_offset + 512, $file_size);

            /*	### Unused Header Information ###
                $activeFile["typeflag"]		= substr($this->tar_file,$main_offset + 156,1);
                $activeFile["linkname"]		= substr($this->tar_file,$main_offset + 157,100);
                $activeFile["magic"]		= substr($this->tar_file,$main_offset + 257,6);
                $activeFile["version"]		= substr($this->tar_file,$main_offset + 263,2);
                $activeFile["devmajor"]		= substr($this->tar_file,$main_offset + 329,8);
                $activeFile["devminor"]		= substr($this->tar_file,$main_offset + 337,8);
                $activeFile["prefix"]		= substr($this->tar_file,$main_offset + 345,155);
                $activeFile["endheader"]	= substr($this->tar_file,$main_offset + 500,12);
            */

            if ($file_size > 0) {
                // Increment number of files
                $this->numFiles++;

                // Create us a new file in our array
                $activeFile = &$this->files[];

                // Asign Values
                $activeFile['name']       = $file_name;
                $activeFile['mode']       = $file_mode;
                $activeFile['size']       = $file_size;
                $activeFile['time']       = $file_time;
                $activeFile['user_id']    = $file_uid;
                $activeFile['group_id']   = $file_gid;
                $activeFile['user_name']  = $file_uname;
                $activeFile['group_name'] = $file_gname;
                $activeFile['checksum']   = $file_chksum;
                $activeFile['file']       = $file_contents;
            } else {
                // Increment number of directories
                $this->numDirectories++;

                // Create a new directory in our array
                $activeDir = &$this->directories[];

                // Assign values
                $activeDir['name']       = $file_name;
                $activeDir['mode']       = $file_mode;
                $activeDir['time']       = $file_time;
                $activeDir['user_id']    = $file_uid;
                $activeDir['group_id']   = $file_gid;
                $activeDir['user_name']  = $file_uname;
                $activeDir['group_name'] = $file_gname;
                $activeDir['checksum']   = $file_chksum;
            }

            // Move our offset the number of blocks we have processed
            $main_offset += 512 + (ceil($file_size / 512) * 512);
        }

        return true;
    }

    /**
     * Read a non gzipped tar file in for processing.
     *
     * @param   string  $filename   full filename
     * @return  bool    always TRUE
     *
     * @access	private
     **/
    public function __readTar($filename='')
    {
        // Set the filename to load
        if (!$filename) {
            $filename = $this->filename;
        }

        // Read in the TAR file
        $fp = fopen($filename, 'rb');
        $this->tar_file = fread($fp, filesize($filename));
        fclose($fp);

        if ($this->tar_file[0] == chr(31) && $this->tar_file[1] == chr(139) && $this->tar_file[2] == chr(8)) {
            if (!function_exists('gzinflate')) {
                return false;
            }

            $this->isGzipped = true;

            $this->tar_file = gzinflate(substr($this->tar_file, 10, -4));
        }

        // Parse the TAR file
        $this->__parseTar();

        return true;
    }

    /**
     * Generates a TAR file from the processed data
     *
     * @return  bool    always TRUE
     *
     * @access	private
     **/
    public function __generateTAR()
    {
        // Clear any data currently in $this->tar_file
        unset($this->tar_file);

        // Generate Records for each directory, if we have directories
        if ($this->numDirectories > 0) {
            foreach ($this->directories as $key => $information) {
                unset($header);

                // Generate tar header for this directory
                // Filename, Permissions, UID, GID, size, Time, checksum, typeflag, linkname, magic, version, user name, group name, devmajor, devminor, prefix, end
                $header .= str_pad($information['name'], 100, chr(0));
                $header .= str_pad(decoct($information['mode']), 7, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_pad(decoct($information['user_id']), 7, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_pad(decoct($information['group_id']), 7, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_pad(decoct(0), 11, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_pad(decoct($information['time']), 11, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_repeat(' ', 8);
                $header .= '5';
                $header .= str_repeat(chr(0), 100);
                $header .= str_pad('ustar', 6, chr(32));
                $header .= chr(32) . chr(0);
                $header .= str_pad('', 32, chr(0));
                $header .= str_pad('', 32, chr(0));
                $header .= str_repeat(chr(0), 8);
                $header .= str_repeat(chr(0), 8);
                $header .= str_repeat(chr(0), 155);
                $header .= str_repeat(chr(0), 12);

                // Compute header checksum
                $checksum = str_pad(decoct($this->__computeUnsignedChecksum($header)), 6, '0', STR_PAD_LEFT);
                for ($i=0; $i<6; $i++) {
                    $header[(148 + $i)] = substr($checksum, $i, 1);
                }
                $header[154] = chr(0);
                $header[155] = chr(32);

                // Add new tar formatted data to tar file contents
                $this->tar_file .= $header;
            }
        }

        // Generate Records for each file, if we have files (We should...)
        if ($this->numFiles > 0) {
            $this->tar_file = '';
            foreach ($this->files as $key => $information) {
                unset($header);

                // Generate the TAR header for this file
                // Filename, Permissions, UID, GID, size, Time, checksum, typeflag, linkname, magic, version, user name, group name, devmajor, devminor, prefix, end
                $header = str_pad($information['name'], 100, chr(0));
                $header .= str_pad(decoct($information['mode']), 7, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_pad(decoct($information['user_id']), 7, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_pad(decoct($information['group_id']), 7, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_pad(decoct($information['size']), 11, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_pad(decoct($information['time']), 11, '0', STR_PAD_LEFT) . chr(0);
                $header .= str_repeat(' ', 8);
                $header .= '0';
                $header .= str_repeat(chr(0), 100);
                $header .= str_pad('ustar', 6, chr(32));
                $header .= chr(32) . chr(0);
                $header .= str_pad($information['user_name'], 32, chr(0));    // How do I get a file's user name from PHP?
                $header .= str_pad($information['group_name'], 32, chr(0));    // How do I get a file's group name from PHP?
                $header .= str_repeat(chr(0), 8);
                $header .= str_repeat(chr(0), 8);
                $header .= str_repeat(chr(0), 155);
                $header .= str_repeat(chr(0), 12);

                // Compute header checksum
                $checksum = str_pad(decoct($this->__computeUnsignedChecksum($header)), 6, '0', STR_PAD_LEFT);
                for ($i=0; $i<6; $i++) {
                    $header[(148 + $i)] = substr($checksum, $i, 1);
                }
                $header[154] = chr(0);
                $header[155] = chr(32);

                // Pad file contents to byte count divisible by 512
                $file_contents = str_pad($information['file'], (ceil($information['size'] / 512) * 512), chr(0));

                // Add new tar formatted data to tar file contents
                $this->tar_file .= $header . $file_contents;
            }
        }

        // Add 512 bytes of NULLs to designate EOF
        $this->tar_file .= str_repeat(chr(0), 512);

        return true;
    }


    /**
     * Open a TAR file
     *
     * @param   string  $filename
     * @return  bool
     **/
    public function openTAR($filename)
    {
        // Clear any values from previous tar archives
        unset($this->filename);
        unset($this->isGzipped);
        unset($this->tar_file);
        unset($this->files);
        unset($this->directories);
        unset($this->numFiles);
        unset($this->numDirectories);

        // If the tar file doesn't exist...
        if (!file_exists($filename)) {
            return false;
        }

        $this->filename = $filename;

        // Parse this file
        $this->__readTar();

        return true;
    }

    /**
     * Appends a tar file to the end of the currently opened tar file.
     *
     * @param   string  $filename
     * @return  bool
     **/
    public function appendTar($filename)
    {
        // If the tar file doesn't exist...
        if (!file_exists($filename)) {
            return false;
        }

        $this->__readTar($filename);

        return true;
    }

    /**
     * Retrieves information about a file in the current tar archive
     *
     * @param   string  $filename
     * @return  string  FALSE on fail
     **/
    public function getFile($filename)
    {
        if ($this->numFiles > 0) {
            foreach ($this->files as $key => $information) {
                if ($information['name'] == $filename) {
                    return $information;
                }
            }
        }

        return false;
    }

    /**
     * Retrieves information about a directory in the current tar archive
     *
     * @param   string  $dirname
     * @return  string  FALSE on fail
     **/
    public function getDirectory($dirname)
    {
        if ($this->numDirectories > 0) {
            foreach ($this->directories as $key => $information) {
                if ($information['name'] == $dirname) {
                    return $information;
                }
            }
        }

        return false;
    }

    /**
     * Check if this tar archive contains a specific file
     *
     * @param   string  $filename
     * @return  bool
     **/
    public function containsFile($filename)
    {
        if ($this->numFiles > 0) {
            foreach ($this->files as $key => $information) {
                if ($information['name'] == $filename) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if this tar archive contains a specific directory
     *
     * @param   string  $dirname
     * @return  bool
     **/
    public function containsDirectory($dirname)
    {
        if ($this->numDirectories > 0) {
            foreach ($this->directories as $key => $information) {
                if ($information['name'] == $dirname) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Add a directory to this tar archive
     *
     * @param   string  $dirname
     * @return  bool
     **/
    public function addDirectory($dirname)
    {
        if (!file_exists($dirname)) {
            return false;
        }

        // Get directory information
        $file_information = stat($dirname);

        // Add directory to processed data
        $this->numDirectories++;
        $activeDir             = &$this->directories[];
        $activeDir['name']     = $dirname;
        $activeDir['mode']     = $file_information['mode'];
        $activeDir['time']     = $file_information['time'];
        $activeDir['user_id']  = $file_information['uid'];
        $activeDir['group_id'] = $file_information['gid'];
        $activeDir['checksum'] = $checksum;

        return true;
    }

    /**
     * Add a file to the tar archive
     *
     * @param   string $filename
     * @param bool     $binary Binary file?
     * @return  bool
     **/
    public function addFile($filename, $binary = false)
    {
        // Make sure the file we are adding exists!
        if (!file_exists($filename)) {
            return false;
        }

        // Make sure there are no other files in the archive that have this same filename
        if ($this->containsFile($filename)) {
            return false;
        }

        // Get file information
        $file_information = stat($filename);

        // Read in the file's contents
        if (!$binary) {
            $fp = fopen($filename, 'r');
        } else {
            $fp = fopen($filename, 'rb');
        }
        $file_contents = filesize($filename) ? fread($fp, filesize($filename)) : '' ;
        fclose($fp);

        // Add file to processed data
        $this->numFiles++;
        $activeFile               = &$this->files[];
        $activeFile['name']       = $filename;
        $activeFile['mode']       = $file_information['mode'];
        $activeFile['user_id']    = $file_information['uid'];
        $activeFile['group_id']   = $file_information['gid'];
        $activeFile['size']       = $file_information['size'];
        $activeFile['time']       = $file_information['mtime'];
        $activeFile['checksum']   = isset($checksum) ? $checksum : '';
        $activeFile['user_name']  = '';
        $activeFile['group_name'] = '';
        // "trim" May be needless. by nao-pon
        //$activeFile["file"]		= trim($file_contents);
        $activeFile['file'] = $file_contents;

        return true;
    }

    /**
     * Remove a file from the tar archive
     *
     * @param   string  $filename
     * @return  bool
     **/
    public function removeFile($filename)
    {
        if ($this->numFiles > 0) {
            foreach ($this->files as $key => $information) {
                if ($information['name'] == $filename) {
                    $this->numFiles--;
                    unset($this->files[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Remove a directory from the tar archive
     *
     * @param   string  $dirname
     * @return  bool
     **/
    public function removeDirectory($dirname)
    {
        if ($this->numDirectories > 0) {
            foreach ($this->directories as $key => $information) {
                if ($information['name'] == $dirname) {
                    $this->numDirectories--;
                    unset($this->directories[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Write the currently loaded tar archive to disk
     *
     * @return  bool
     **/
    public function saveTar()
    {
        if (!$this->filename) {
            return false;
        }

        // Write tar to current file using specified gzip compression
        $this->toTar($this->filename, $this->isGzipped);

        return true;
    }

    /**
     * Saves tar archive to a different file than the current file
     *
     * @param   string  $filename
     * @param   bool    $useGzip    Use GZ compression?
     * @return  bool
     **/
    public function toTar($filename, $useGzip)
    {
        if (!$filename) {
            return false;
        }

        // Encode processed files into TAR file format
        $this->__generateTar();

        // GZ Compress the data if we need to
        if ($useGzip) {
            // Make sure we have gzip support
            if (!function_exists('gzencode')) {
                return false;
            }

            $file = gzencode($this->tar_file);
        } else {
            $file = $this->tar_file;
        }

        // Write the TAR file
        $fp = fopen($filename, 'wb');
        fwrite($fp, $file);
        fclose($fp);

        return true;
    }

    /**
     * Sends tar archive to stdout
     *
     * @param   string  $filename
     * @param   bool    $useGzip    Use GZ compression?
     * @return  string
     **/
    public function toTarOutput($filename, $useGzip)
    {
        if (!$filename) {
            return false;
        }

        // Encode processed files into TAR file format
        $this->__generateTar();

        // GZ Compress the data if we need to
        if ($useGzip) {
            // Make sure we have gzip support
            if (!function_exists('gzencode')) {
                return false;
            }

            $file = gzencode($this->tar_file);
        } else {
            $file = $this->tar_file;
        }

        return $file;
    }
}
