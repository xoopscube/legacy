<?php
/**
 * XCube_FormFile.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      @WARNING
 * This class is a simple wrapper class to process the uploaded file.
 * However, we need to examine the position of this class. We are aiming at the simple file tree.
 * This class is just a helper. We believe that the Cube system should not provide inappropriate help.
 * We put this class in root/class for the advancement of this project.
 * But, we should move it to another directory in a later stage.
 */


//define("XCUBE_FORMFILE_PREVMASK", "0022");
const XCUBE_FORMFILE_CHMOD = 0644;


class XCube_FormFile {
	public $mName;

	public $mKey;

	public $mContentType;

	public $mFileName;
	public $mFileSize = 0;

	public $_mTmpFileName;

	public $mUploadFileFlag = false;

	public function __construct( $name = null, $key = null ) {
		$this->mName = $name;
		$this->mKey  = $key;
	}

	/**
	 * Fetch necessary information from $_FILES by $mName
	 */
	public function fetch() {
		if ( $this->mName && isset( $_FILES[ $this->mName ] ) ) {
			if ( null !== $this->mKey ) {
				$this->setFileName( $_FILES[ $this->mName ]['name'][ $this->mKey ] );
				$this->setContentType( $_FILES[ $this->mName ]['type'][ $this->mKey ] );
				$this->setFileSize( $_FILES[ $this->mName ]['size'][ $this->mKey ] );
				$this->_mTmpFileName = $_FILES[ $this->mName ]['tmp_name'][ $this->mKey ];
			} else {
				$this->setFileName( $_FILES[ $this->mName ]['name'] );
				$this->setContentType( $_FILES[ $this->mName ]['type'] );
				$this->setFileSize( $_FILES[ $this->mName ]['size'] );
				$this->_mTmpFileName = $_FILES[ $this->mName ]['tmp_name'];
			}

			if ( $this->getFileSize() > 0 ) {
				$this->mUploadFileFlag = true;
			}
		}
	}

	public function hasUploadFile() {
		return $this->mUploadFileFlag;
	}

	/**
	 * Return content type
	 * @return string
	 */
	public function getContentType() {
		return $this->mContentType;
	}

	public function getFileData() {
		// Now, implementing.
	}

	/**
	 * Return file name.
	 * @return string
	 */
	public function getFileName() {
		return $this->mFileName;
	}

	/**
	 * Return file size.
	 * @return int
	 */
	public function getFileSize() {
		return $this->mFileSize;
	}

	/**
	 * Return extension from file name.
	 * @return string
	 */
	public function getExtension() {
		$ret      = null;
		$filename = $this->getFileName();
		if ( preg_match( "/\.([a-z0-9\.]+)$/i", $filename, $match ) ) {
			$ret = $match[1];
		}

		return $ret;
	}

	/**
	 * Set extension.
	 *
	 * @param $ext
	 *
	 * @return string
	 */
	public function setExtension( $ext ) {
		$filename = $this->getFileName();
		if ( preg_match( "/(.+)\.\w+$/", $filename, $match ) ) {
			$this->setFileName( $match[1] . ".${ext}" );
		}
	}

	/**
	 * Set content type
	 *
	 * @param string $contenttype
	 */
	public function setContentType( $contenttype ) {
		$this->mContentType = $contenttype;
	}

	/**
	 * Set file name
	 *
	 * @param string $filename
	 */
	public function setFileName( $filename ) {
		$this->mFileName = $filename;
	}

	/**
	 * Set file size
	 *
	 * @param int $filesize
	 */
	public function setFileSize( $filesize ) {
		$this->mFileSize = $filesize;
	}

	/**
	 * Set file body name. The extension is never changed.
	 *
	 * @param string $bodyname
	 */
	public function setBodyName( $bodyname ) {
		$this->setFileName( $bodyname . '.' . $this->getExtension() );
	}

	/**
	 * Get file body name.
	 * @return string
	 */
	public function getBodyName() {
		if ( preg_match( "/(.+)\.\w+$/", $this->getFileName(), $match ) ) {
			return $match[1];
		}

		return null;
	}

	/**
	 * Set random string to file body name. The extension is never changed.
	 *
	 * @param string $prefix Prefix for random string.
	 * @param string $salt Salt for generating token.
	 */
	public function setRandomToBodyName( $prefix, $salt = '' ) {
		$filename = $prefix . $this->_getRandomString( $salt ) . '.' . $this->getExtension();
		$this->setFileName( $filename );
	}

	/**
	 * Set random string to file body name. The extension is changed.
	 *
	 * @param string $prefix Prefix for random string.
	 * @param string $salt Salt for generating token.
	 */
	public function setRandomToFilename( $prefix, $salt = '' ) {
		$filename = $prefix . $this->_getRandomString( $salt );
		$this->setFileName( $filename );
	}

	/**
	 * @brief Generate random string.
	 * https://www.php.net/manual/en/function.mt-rand.php
	 * @param string $salt Salt for generating token.
	 *
	 * @return string
	 */
	public function _getRandomString( $salt = '' ) {
		if ( empty( $salt ) ) {
			$root =& XCube_Root::getSingleton();
			$salt = $root->getSiteConfig( 'Cube', 'Salt' );
		}
		mt_srand( microtime() * 1000000 );

		return md5( $salt . mt_rand() );
	}

	/**
	 * Name this, and store it. If the name is specified as complete file name, store it as the same name.
	 * If the name is specified as directory name, store it as the own name to the directory specified.
	 *
	 * @param Directory $file path or file path.
	 *
	 * @return bool
	 */
	public function saveAs( $file ) {
		$destFile = '';
		if ( preg_match( "#\/$#", $file ) ) {
			$destFile = $file . $this->getFileName();
		} elseif ( is_dir( $file ) ) {
			$destFile = $file . '/' . $this->getFileName();
		} else {
			$destFile = $file;
		}

		$ret = move_uploaded_file( $this->_mTmpFileName, $destFile );

		// $prevMask = @umask(XCUBE_FORMFILE_PREVMASK);
		// @umask($prevMask);
		@chmod( $destFile, XCUBE_FORMFILE_CHMOD );

		return $ret;
	}

	/**
	 * Set random string to file body name, and store it. The extension is never changed.
	 *
	 * @param Directory $dir for store.
	 * @param string $prefix Prefix for random string.
	 * @param string $salt Salt for generating token.
	 *
	 * @return bool
	 * @see saveAs()
	 * @see setRandomToBodyName()
	 */
	public function saveAsRandBody( $dir, $prefix = '', $salt = '' ) {
		$this->setRandomToBodyName( $prefix, $salt );

		return $this->saveAs( $dir );
	}

	/**
	 * Set random string to file name, and store it. The extension is never changed.
	 *
	 * @param Directory $dir for store.
	 * @param string $prefix Prefix for random string.
	 * @param string $salt Salt for generating token.
	 *
	 * @return bool
	 * @see saveAs()
	 * @see setRandomToFileName()
	 */
	public function saveAsRand( $dir, $prefix = '', $salt = '' ) {
		$this->setRandomToFileName( $prefix, $salt );

		return $this->saveAs( $dir );
	}
}

/**
 * The sub-class of XCube_FormFile to handle image upload file easily.
 */
class XCube_FormImageFile extends XCube_FormFile {
	public function fetch() {
		parent::fetch();

		if ( $this->hasUploadFile() && ! $this->_checkFormat() ) {
			$this->mUploadFileFlag = false;
		}
	}

	/**
	 * Gets a width of the uploaded file.
	 * @return int
	 */
	public function getWidth() {
		[ $width, $height, $type, $attr ] = getimagesize( $this->_mTmpFileName );

		return $width;
	}

	/**
	 * Gets a height of the uploaded file.
	 * @return int
	 */
	public function getHeight() {
		[ $width, $height, $type, $attr ] = getimagesize( $this->_mTmpFileName );

		return $height;
	}

	/**
	 * Gets a value indicating whether a format of the uploaded file is allowed.
     * GIF, JPG, PNG
	 * @access private
	 * @return bool
	 */
	public function _checkFormat() {
		if ( ! $this->hasUploadFile() ) {
			return false;
		}

		[ $width, $height, $type, $attr ] = getimagesize( $this->_mTmpFileName );

		switch ( $type ) {
			case IMAGETYPE_GIF:
				$this->setExtension( 'gif' );
				break;

			case IMAGETYPE_JPEG:
				$this->setExtension( 'jpg' );
				break;

			case IMAGETYPE_PNG:
				$this->setExtension( 'png' );
				break;

			default:
				return false;
		}

		return true;
	}
}
