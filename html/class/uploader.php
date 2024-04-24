<?php
/**
 * Upload Media files
 * @package    kernel
 * @subpackage core
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

/**
 * Example of usage:
 * <code>
 * include_once 'uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $uploader = new XoopsMediaUploader('/home/xoops/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
 * if ($uploader->fetchMedia($_POST['uploade_file_name'])) {
 *   if (!$uploader->upload()) {
 *      echo $uploader->getErrors();
 *   } else {
 *      echo '<h4>File uploaded successfully!</h4>'
 *      echo 'Saved as: ' . $uploader->getSavedFileName() . '<br>';
 *      echo 'Full path: ' . $uploader->getSavedDestination();
 *   }
 * } else {
 *   echo $uploader->getErrors();
 * }
 * </code>
 */

define('XCUBE_IMAGETYPE_ENUM_GIF', 1);
define('XCUBE_IMAGETYPE_ENUM_JPG', 2);
define('XCUBE_IMAGETYPE_ENUM_PNG', 3);
define('XCUBE_IMAGETYPE_ENUM_BMP', 6);

class XoopsMediaUploader
{
    /**
    * Flag indicating if unrecognized mimetypes should be allowed (use with precaution ! may lead to security issues )
    **/
    public $allowUnknownTypes = false;
    public $mediaName;
    public $mediaType;
    public $mediaSize;
    public $mediaTmpName;
    public $mediaError;
    public $mediaRealType = '';
    public $uploadDir = '';
    public $allowedMimeTypes = [];
    public $allowedExtensions = [];
    public $maxFileSize = 0;
    public $maxWidth;
    public $maxHeight;
    public $targetFileName;
    public $prefix;
    public $errors = [];
    public $savedDestination;
    public $savedFileName;
    public $extensionToMime = [];

    public $_strictCheckExtensions = [];

    /**
     * Constructor
     *
     * @param string $uploadDir
     * @param array  $allowedMimeTypes
     * @param int    $maxFileSize
     * @param int    $maxWidth
     * @param int    $maxHeight
     */
    public function __construct($uploadDir, $allowedMimeTypes, $maxFileSize=0, $maxWidth=null, $maxHeight=null)
    {
        @$this->extensionToMime = include(XOOPS_ROOT_PATH . '/class/mimetypes.inc.php');
        if (!is_array($this->extensionToMime)) {
            $this->extensionToMime = [];
            return false;
        }
        if (is_array($allowedMimeTypes)) {
            $this->allowedMimeTypes =& $allowedMimeTypes;
        }
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = (int)$maxFileSize;
        if (isset($maxWidth)) {
            $this->maxWidth = (int)$maxWidth;
        }
        if (isset($maxHeight)) {
            $this->maxHeight = (int)$maxHeight;
        }

        $this->_strictCheckExtensions = [
            'gif'  =>XCUBE_IMAGETYPE_ENUM_GIF,
            'jpg'  =>XCUBE_IMAGETYPE_ENUM_JPG,
            'jpeg' =>XCUBE_IMAGETYPE_ENUM_JPG,
            'png'  =>XCUBE_IMAGETYPE_ENUM_PNG,
            'bmp'  =>XCUBE_IMAGETYPE_ENUM_BMP
        ];
    }

    public function setAllowedExtensions($extensions)
    {
        $this->allowedExtensions = is_array($extensions) ? $extensions : [];
    }

    public function setStrictCheckExtensions($extensions)
    {
        $this->_strictCheckExtensions = $extensions;
    }

    /**
     * Fetch the uploaded file
     *
     * @param   string  $media_name Name of the file field
     * @param   int     $index      Index of the file (if more than one uploaded under that name)
     * @return  bool
     **/
    public function fetchMedia($media_name, $index = null)
    {
        if (empty($this->extensionToMime)) {
            $this->setErrors('Error loading mimetypes definition');
            return false;
        }
        if (!isset($_FILES[$media_name])) {
            $this->setErrors('File not found');
            return false;
        }

        if (is_array($_FILES[$media_name]['name']) && isset($index)) {
            $index = (int)$index;
            $this->mediaName = $_FILES[$media_name]['name'][$index];
            $this->mediaType = $_FILES[$media_name]['type'][$index];
            $this->mediaSize = $_FILES[$media_name]['size'][$index];
            $this->mediaTmpName = $_FILES[$media_name]['tmp_name'][$index];
            $this->mediaError = !empty($_FILES[$media_name]['error'][$index]) ? $_FILES[$media_name]['errir'][$index] : 0;
        } else {
            $media_name =& $_FILES[$media_name];
            $this->mediaName = $media_name['name'];
            $this->mediaName = $media_name['name'];
            $this->mediaType = $media_name['type'];
            $this->mediaSize = $media_name['size'];
            $this->mediaTmpName = $media_name['tmp_name'];
            $this->mediaError = !empty($media_name['error']) ? $media_name['error'] : 0;
        }
        if (false !== ($ext = strrpos($this->mediaName, '.'))) {
            $this->ext = strtolower(substr($this->mediaName, $ext + 1));
            if (isset($this->extensionToMime[$this->ext])) {
                $this->mediaRealType = $this->extensionToMime[$this->ext];
                //trigger_error( "XoopsMediaUploader: Set mediaRealType to {$this->mediaRealType} (file extension is ".$this->ext.")", E_USER_NOTICE );
            }
        } else {
            $this->setErrors('Invalid Extension');
            return false;
        }
        $this->errors = [];
        if ((int)$this->mediaSize < 0) {
            $this->setErrors('Invalid File Size');
            return false;
        }
        if ('' == $this->mediaName) {
            $this->setErrors('Filename Is Empty');
            return false;
        }
        if ('none' === $this->mediaTmpName || !is_uploaded_file($this->mediaTmpName)) {
            $this->setErrors('No file uploaded');
            return false;
        }
        if ($this->mediaError > 0) {
            $this->setErrors('Error occurred: Error #'.$this->mediaError);
            return false;
        }
        return true;
    }

    /**
     * Set the target filename
     *
     * @param   string  $value
     **/
    public function setTargetFileName($value)
    {
        $this->targetFileName = (string)trim($value);
    }

    /**
     * Set the prefix
     *
     * @param   string  $value
     **/
    public function setPrefix($value)
    {
        $this->prefix = (string)trim($value);
    }

    /**
     * Get the uploaded filename
     *
     * @return  string
     **/
    public function getMediaName()
    {
        return $this->mediaName;
    }

    /**
     * Get the type of the uploaded file
     *
     * @return  string
     **/
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Get the size of the uploaded file
     *
     * @return  int
     **/
    public function getMediaSize()
    {
        return $this->mediaSize;
    }

    /**
     * Get the temporary name that the uploaded file was stored under
     *
     * @return  string
     **/
    public function getMediaTmpName()
    {
        return $this->mediaTmpName;
    }

    /**
     * Get the saved filename
     *
     * @return  string
     **/
    public function getSavedFileName()
    {
        return $this->savedFileName;
    }

    /**
     * Get the destination the file is saved to
     *
     * @return  string
     **/
    public function getSavedDestination()
    {
        return $this->savedDestination;
    }

    /**
     * Check the file and copy it to the destination
     *
     * @param int $chmod
     * @return  bool
     */
    public function upload($chmod = 0644)
    {
        if ('' == $this->uploadDir) {
            $this->setErrors('Upload directory not set');
            return false;
        }
        if (!is_dir($this->uploadDir)) {
            $this->setErrors('Failed opening directory: '.$this->uploadDir);
        }
        if (!is_writable($this->uploadDir)) {
            $this->setErrors('Failed opening directory with write permission: '.$this->uploadDir);
        }
        if (!$this->checkMaxFileSize()) {
            $this->setErrors('File size too large: '.$this->mediaSize);
        }
        if (!$this->checkMaxWidth()) {
            $this->setErrors(sprintf('File width must be smaller than %u', $this->maxWidth));
        }
        if (!$this->checkMaxHeight()) {
            $this->setErrors(sprintf('File height must be smaller than %u', $this->maxHeight));
        }
        if (!$this->checkMimeType()) {
            $this->setErrors('Invalid file type');
        }
        if (count($this->errors) > 0) {
            return false;
        }
        if (!$this->_copyFile($chmod)) {
            $this->setErrors('Failed uploading file: '.$this->mediaName);
            return false;
        }
        return true;
    }

    /**
     * Copy the file to its destination
     *
     * @param $chmod
     * @return  bool
     */
    public function _copyFile($chmod)
    {
        if (isset($this->targetFileName)) {
            $this->savedFileName = $this->targetFileName;
        } elseif (isset($this->prefix)) {
            $this->savedFileName = uniqid($this->prefix, true).'.'.strtolower($this->ext);
        } else {
            $this->savedFileName = strtolower($this->mediaName);
        }
        $this->savedDestination = $this->uploadDir.'/'.$this->savedFileName;
        if (!move_uploaded_file($this->mediaTmpName, $this->savedDestination)) {
            return false;
        }
        @chmod($this->savedDestination, $chmod);
        return true;
    }

    /**
     * Is the file the right size?
     *
     * @return  bool
     **/
    public function checkMaxFileSize()
    {
        return !($this->mediaSize > $this->maxFileSize);
    }

    /**
     * Is the picture the right width?
     *
     * @return  bool
     **/
    public function checkMaxWidth()
    {
        if (!isset($this->maxWidth)) {
            return true;
        }
        if (false !== $dimension = getimagesize($this->mediaTmpName)) {
            if ($dimension[0] > $this->maxWidth) {
                return false;
            }
        } else {
            trigger_error(sprintf('Failed fetching image size of %s, skipping max width check..', $this->mediaTmpName), E_USER_WARNING);
        }
        return true;
    }

    /**
     * Is the picture the right height?
     *
     * @return  bool
     **/
    public function checkMaxHeight()
    {
        if (!isset($this->maxHeight)) {
            return true;
        }
        if (false !== $dimension = getimagesize($this->mediaTmpName)) {
            if ($dimension[1] > $this->maxHeight) {
                return false;
            }
        } else {
            trigger_error(sprintf('Failed fetching image size of %s, skipping max height check..', $this->mediaTmpName), E_USER_WARNING);
        }
        return true;
    }

    /**
     * Check whether or not the uploaded file type is allowed
     *
     * @return  bool
     **/
    public function checkMimeType()
    {
        if (!empty($this->allowedExtensions)) {
            if (!in_array($this->ext, $this->allowedExtensions)) {
                $this->setErrors('File extension not allowed');
                return false;
            }
            // Since the file extension is already checked against
            // allowed file extension values, it is safe to use
            // $this->mediaType for the allowed mime type check as was in
            // <= 2.0.9.2
            if (!empty($this->allowedMimeTypes)&& !in_array($this->mediaType, $this->allowedMimeTypes, true)) {
                $this->setErrors('Unexpected MIME Type');
                return false;
            }
        } else {
            // use $this->mediaRealType for the allowed mime type check to
            // make it more restrictive/secure
            if (empty($this->mediaRealType) && !$this->allowUnknownTypes) {
                return false;
            }
            if (!empty($this->allowedMimeTypes)&& !in_array($this->mediaRealType, $this->allowedMimeTypes, true)) {
                $this->setErrors('Unexpected MIME Type');
                return false;
            }
        }

        // If this extension need strict check, call method for it.
        if (isset($this->_strictCheckExtensions[$this->ext])) {
            return $this->_checkStrict();
        } else {
            return true;
        }
    }

    public function _checkStrict()
    {
        $parseValue = getimagesize($this->mediaTmpName);

        if (false === $parseValue) {
            return false;
        }

        return $parseValue[2]==$this->_strictCheckExtensions[$this->ext];
    }

    /**
     * Check whether or not the uploaded file type is allowed
     *
     * @return  bool
     **/
    public function checkExpectedMimeType()
    {
        if (empty($this->mediaRealType) && !$this->allowUnknownTypes) {
            return false;
        }

        return (empty($this->allowedMimeTypes) || in_array($this->mediaRealType, $this->allowedMimeTypes, true));
    }

    /**
     * Add an error
     *
     * @param   string  $error
     **/
    public function setErrors($error)
    {
        $this->errors[] = trim($error);
    }

    /**
     * Get generated errors
     *
     * @param    bool    $ashtml Format using HTML?
     *
     * @return    array|string    Array of array messages OR HTML string
     */
    public function &getErrors($ashtml = true)
    {
        if (!$ashtml) {
            return $this->errors;
        }

        $ret = '';
        if (count($this->errors) > 0) {
            $ret = '<h4>Errors Returned While Uploading</h4>';
            foreach ($this->errors as $error) {
                $ret .= $error.'<br>';
            }
        }
        return $ret;
    }
}
