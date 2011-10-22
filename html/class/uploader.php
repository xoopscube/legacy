<?php
// $Id: uploader.php,v 1.1 2007/05/15 02:34:21 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

/**
 * Upload Media files
 *
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
 *      echo 'Saved as: ' . $uploader->getSavedFileName() . '<br />';
 *      echo 'Full path: ' . $uploader->getSavedDestination();
 *   }
 * } else {
 *   echo $uploader->getErrors();
 * }
 * </code>
 *
 * @package        kernel
 * @subpackage    core
 *
 * @author        Kazumi Ono     <onokazu@xoops.org>
 * @copyright    (c) 2000-2003 The Xoops Project - www.xoops.org
 */

define("XCUBE_IMAGETYPE_ENUM_GIF",1);
define("XCUBE_IMAGETYPE_ENUM_JPG",2);
define("XCUBE_IMAGETYPE_ENUM_PNG",3);
define("XCUBE_IMAGETYPE_ENUM_BMP",6);

class XoopsMediaUploader
{
    /**
    * Flag indicating if unrecognized mimetypes should be allowed (use with precaution ! may lead to security issues )
    **/
    var $allowUnknownTypes = false;
    var $mediaName;
    var $mediaType;
    var $mediaSize;
    var $mediaTmpName;
    var $mediaError;
    var $mediaRealType = '';
    var $uploadDir = '';
    var $allowedMimeTypes = array();
    var $allowedExtensions = array();
    var $maxFileSize = 0;
    var $maxWidth;
    var $maxHeight;
    var $targetFileName;
    var $prefix;
    var $errors = array();
    var $savedDestination;
    var $savedFileName;
    var $extensionToMime = array();

	var $_strictCheckExtensions = array();

    /**
     * Constructor
     *
     * @param   string  $uploadDir
     * @param   array   $allowedMimeTypes
     * @param   int     $maxFileSize
     * @param   int     $maxWidth
     * @param   int     $maxHeight
     * @param   int     $cmodvalue
     **/
    function XoopsMediaUploader($uploadDir, $allowedMimeTypes, $maxFileSize=0, $maxWidth=null, $maxHeight=null)
    {
        @$this->extensionToMime = include( XOOPS_ROOT_PATH . '/class/mimetypes.inc.php' );
        if ( !is_array( $this->extensionToMime ) ) {
            $this->extensionToMime = array();
            return false;
        }
        if (is_array($allowedMimeTypes)) {
            $this->allowedMimeTypes =& $allowedMimeTypes;
        }
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = intval($maxFileSize);
        if(isset($maxWidth)) {
            $this->maxWidth = intval($maxWidth);
        }
        if(isset($maxHeight)) {
            $this->maxHeight = intval($maxHeight);
        }

		$this->_strictCheckExtensions = array("gif"=>XCUBE_IMAGETYPE_ENUM_GIF,
                                               "jpg"=>XCUBE_IMAGETYPE_ENUM_JPG,
                                               "jpeg"=>XCUBE_IMAGETYPE_ENUM_JPG,
                                               "png"=>XCUBE_IMAGETYPE_ENUM_PNG,
                                               "bmp"=>XCUBE_IMAGETYPE_ENUM_BMP); 
    }

    function setAllowedExtensions($extensions)
    {
        $this->allowedExtensions = is_array($extensions) ? $extensions : array();
    }

	function setStrictCheckExtensions($extensions)
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
    function fetchMedia($media_name, $index = null)
    {
        if ( empty( $this->extensionToMime ) ) {
            $this->setErrors( 'Error loading mimetypes definition' );
            return false;
        }
        if (!isset($_FILES[$media_name])) {
            $this->setErrors('File not found');
            return false;
        } elseif (is_array($_FILES[$media_name]['name']) && isset($index)) {
            $index = intval($index);
            $this->mediaName = (get_magic_quotes_gpc()) ? stripslashes($_FILES[$media_name]['name'][$index]) : $_FILES[$media_name]['name'][$index];
            $this->mediaType = $_FILES[$media_name]['type'][$index];
            $this->mediaSize = $_FILES[$media_name]['size'][$index];
            $this->mediaTmpName = $_FILES[$media_name]['tmp_name'][$index];
            $this->mediaError = !empty($_FILES[$media_name]['error'][$index]) ? $_FILES[$media_name]['errir'][$index] : 0;
        } else {
            $media_name =& $_FILES[$media_name];
            $this->mediaName = (get_magic_quotes_gpc()) ? stripslashes($media_name['name']) : $media_name['name'];
            $this->mediaName = $media_name['name'];
            $this->mediaType = $media_name['type'];
            $this->mediaSize = $media_name['size'];
            $this->mediaTmpName = $media_name['tmp_name'];
            $this->mediaError = !empty($media_name['error']) ? $media_name['error'] : 0;
        }
        if ( ($ext = strrpos( $this->mediaName, '.' )) !== false ) {
            $this->ext = strtolower ( substr( $this->mediaName, $ext + 1 ) );
            if ( isset( $this->extensionToMime[$this->ext] ) ) {
                $this->mediaRealType = $this->extensionToMime[$this->ext];
                //trigger_error( "XoopsMediaUploader: Set mediaRealType to {$this->mediaRealType} (file extension is ".$this->ext.")", E_USER_NOTICE );
            }
        } else {
            $this->setErrors('Invalid Extension');
            return false;
        }
        $this->errors = array();
        if (intval($this->mediaSize) < 0) {
            $this->setErrors('Invalid File Size');
            return false;
        }
        if ($this->mediaName == '') {
            $this->setErrors('Filename Is Empty');
            return false;
        }
        if ($this->mediaTmpName == 'none' || !is_uploaded_file($this->mediaTmpName)) {
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
    function setTargetFileName($value){
        $this->targetFileName = strval(trim($value));
    }

    /**
     * Set the prefix
     *
     * @param   string  $value
     **/
    function setPrefix($value){
        $this->prefix = strval(trim($value));
    }

    /**
     * Get the uploaded filename
     *
     * @return  string
     **/
    function getMediaName()
    {
        return $this->mediaName;
    }

    /**
     * Get the type of the uploaded file
     *
     * @return  string
     **/
    function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Get the size of the uploaded file
     *
     * @return  int
     **/
    function getMediaSize()
    {
        return $this->mediaSize;
    }

    /**
     * Get the temporary name that the uploaded file was stored under
     *
     * @return  string
     **/
    function getMediaTmpName()
    {
        return $this->mediaTmpName;
    }

    /**
     * Get the saved filename
     *
     * @return  string
     **/
    function getSavedFileName(){
        return $this->savedFileName;
    }

    /**
     * Get the destination the file is saved to
     *
     * @return  string
     **/
    function getSavedDestination(){
        return $this->savedDestination;
    }

    /**
     * Check the file and copy it to the destination
     *
     * @return  bool
     **/
    function upload($chmod = 0644)
    {
        if ($this->uploadDir == '') {
            $this->setErrors('Upload directory not set');
            return false;
        }
        if (!is_dir($this->uploadDir)) {
            $this->setErrors('Failed opening directory: '.$this->uploadDir);
        }
        if (!is_writeable($this->uploadDir)) {
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
            $this->setErrors("Invalid file type");
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
     * @return  bool
     **/
    function _copyFile($chmod)
    {
        if (isset($this->targetFileName)) {
            $this->savedFileName = $this->targetFileName;
        } elseif (isset($this->prefix)) {
            $this->savedFileName = uniqid($this->prefix).'.'.strtolower($this->ext);
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
    function checkMaxFileSize()
    {
        if ($this->mediaSize > $this->maxFileSize) {
            return false;
        }
        return true;
    }

    /**
     * Is the picture the right width?
     *
     * @return  bool
     **/
    function checkMaxWidth()
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
    function checkMaxHeight()
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
    function checkMimeType()
    {
        if (!empty($this->allowedExtensions)) {
            if (!in_array($this->ext, $this->allowedExtensions)) {
                $this->setErrors( 'File extension not allowed' );
                return false;
            }
            // Since the file extension is already checked against
            // allowed file extension values, it is safe to use
            // $this->mediaType for the allowed mime type check as was in
            // <= 2.0.9.2
            if (!empty($this->allowedMimeTypes)&& !in_array($this->mediaType, $this->allowedMimeTypes)) {
                $this->setErrors('Unexpected MIME Type');
                return false;
            }
        } else {
            // use $this->mediaRealType for the allowed mime type check to
            // make it more restrictive/secure
            if (empty( $this->mediaRealType ) && !$this->allowUnknownTypes) {
                return false;
            }
            if (!empty($this->allowedMimeTypes)&& !in_array($this->mediaRealType, $this->allowedMimeTypes)) {
                $this->setErrors('Unexpected MIME Type');
                return false;
            }
        }

		// If this extension need strict check, call method for it.
		if(isset($this->_strictCheckExtensions[$this->ext])) {
			return $this->_checkStrict();
		}
		else {
	        return true;
		}
    }

	function _checkStrict()
	{
		$parseValue = getimagesize($this->mediaTmpName);

		if($parseValue===false)
			return false;

		return $parseValue[2]==$this->_strictCheckExtensions[$this->ext];
	}

    /**
     * Check whether or not the uploaded file type is allowed
     *
     * @return  bool
     **/
    function checkExpectedMimeType()
    {
        if ( empty( $this->mediaRealType ) && !$this->allowUnknownTypes ) {
            return false;
        }

        return ( empty($this->allowedMimeTypes) || in_array($this->mediaRealType, $this->allowedMimeTypes) );
    }

    /**
     * Add an error
     *
     * @param   string  $error
     **/
    function setErrors($error)
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
    function &getErrors($ashtml = true)
    {
        if (!$ashtml) {
            return $this->errors;
        } else {
            $ret = '';
            if (count($this->errors) > 0) {
                $ret = '<h4>Errors Returned While Uploading</h4>';
                foreach ($this->errors as $error) {
                    $ret .= $error.'<br />';
                }
            }
            return $ret;
        }
    }
}
?>