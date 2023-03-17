<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.43.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 * @brief      you can override this class by specifying your sub-class into the preferences
 */

define( 'PICO_EXTRA_FIELDS_PREFIX', 'extra_fields_' );
define( 'PICO_EXTRA_FIELDS_PREFIX_SHORT', 'ef_' );
define( 'PICO_EXTRA_IMAGES_PREFIX', 'extra_images_' );
define( 'PICO_EXTRA_IMAGES_PREFIX_SHORT', 'ei_' );
// %1$s: field_name   %2$s: size_key  %3$s: image_id
define( 'PICO_EXTRA_IMAGES_FMT', '%s_%s_%s' );
define( 'PICO_EXTRA_IMAGES_REMOVAL_COMMAND', 'remove.gif' );


class PicoExtraFields {

	public $mydirname;
	public $mod_config;
	public $auto_approval;
	public $isadminormod;
	public $content_id;
	public $images_path;
	public $image_sizes;
    public $image_quality; // since XCL 2.3.x

	public function __construct( $mydirname, $mod_config, $auto_approval, $isadminormod, $content_id ) {
		$this->mydirname     = $mydirname;
		$this->mod_config    = $mod_config;
		$this->auto_approval = $auto_approval;
		$this->isadminormod  = $isadminormod;
		$this->content_id    = $content_id;
		$this->images_path   = XOOPS_ROOT_PATH . '/' . $mod_config['extra_images_dir'];
        $this->image_quality = $this->mod_config['extra_images_quality'];
		$this->image_sizes   = [];
		$size_combos         = preg_split( '/\s+/', $this->mod_config['extra_images_size'] );
		foreach ( $size_combos as $size_combo ) {
			$this->image_sizes[] = array_map( 'intval', preg_split( '/\D+/', $size_combo ) );
		}
	}

	public function getSerializedRequestsFromPost() {
		$ret = [];
		( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &( new MyTextSanitizer )->getInstance();

		// text fields
		foreach ( $_POST as $key => $val ) {
			if ( 0 === strncmp( $key, PICO_EXTRA_FIELDS_PREFIX, strlen( PICO_EXTRA_FIELDS_PREFIX ) ) ) {
				$ret[ substr( $key, strlen( PICO_EXTRA_FIELDS_PREFIX ) ) ] = $this->stripSlashesGPC( $val );
			} elseif ( 0 === strncmp( $key, PICO_EXTRA_FIELDS_PREFIX_SHORT, strlen( PICO_EXTRA_FIELDS_PREFIX_SHORT ) ) ) {
				$ret[ substr( $key, strlen( PICO_EXTRA_FIELDS_PREFIX_SHORT ) ) ] = $this->stripSlashesGPC( $val );
			}
		}

		// process $_FILES (only admin or moderator )
		if ( $this->canUploadImages() && ! empty( $_FILES ) && is_array( $_FILES ) ) {
			$this->uploadImages( $ret );
		}

		return pico_common_serialize( $ret );
	}

	// virtual
	public function canUploadImages() {
		return $this->isadminormod;
	}

	public function uploadImages( &$extra_fields ) {
		foreach ( $_FILES as $key => $file ) {
			if ( 0 === strncmp( $key, PICO_EXTRA_IMAGES_PREFIX, strlen( PICO_EXTRA_IMAGES_PREFIX ) ) ) {
				$this->uploadImage( $extra_fields, $file, substr( $key, strlen( PICO_EXTRA_IMAGES_PREFIX ) ) );
			} elseif ( 0 === strncmp( $key, PICO_EXTRA_IMAGES_PREFIX_SHORT, strlen( PICO_EXTRA_IMAGES_PREFIX_SHORT ) ) ) {
				$this->uploadImage( $extra_fields, $file, substr( $key, strlen( PICO_EXTRA_IMAGES_PREFIX_SHORT ) ) );
			}
		}
	}

	public function uploadImage( &$extra_fields, $file, $field_name ) {
		// check it is true uploaded file
		if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
			return false;
		}

		// check the directory exists
        // TODO REDIRECT MESSAGE TO SETTINGS
		if ( ! is_dir( $this->images_path ) ) {
			die( 'create upload directory for pico first' );
		}

		// command for removing. upload "remove.gif"
		if ( PICO_EXTRA_IMAGES_REMOVAL_COMMAND === $file['name'] ) {
			foreach ( array_keys( $this->image_sizes ) as $size_key ) {
				unlink( $this->getImageFullPath( $field_name, $size_key, $extra_fields[ $field_name ] ) );
			}
			$extra_fields[ $field_name ] = '';

			return true;
		}

		// create id
		$id = $this->createId( $extra_fields, $file, $field_name );

		// create temp file name
		$tmp_image = $this->images_path . '/tmp_' . $id;

		// set mask - 0022  sets default write permissions
        // lets the owner both read and write all newly created files, but everybody else can only read them
		$prev_mask = @umask( 0022 );

		// move temporary
        // TODO REDIRECT MESSAGE TO SETTINGS
		$upload_result = move_uploaded_file( $file['tmp_name'], $tmp_image );
		if ( ! $upload_result ) {
			die( 'check the permission/owner of the directory ' . htmlspecialchars( $this->mod_config['extra_images_dir'], ENT_QUOTES ) );
		}
		@chmod( $tmp_image, 0644 );

		// check the file is image or not
		$check_result = @getimagesize( $tmp_image );
		if ( ! is_array( $check_result ) || empty( $check_result['mime'] ) ) {
			@unlink( $tmp_image );
            // TODO ALERT / REDIRECT MESSAGE
			die( 'An invalid image file is uploaded' );
		}

		// set image_id ( = $id . $ext )
		$image_id = $id . '.' . $this->getExtFromMime( $check_result['mime'] );

		// resize loop - image_magick_path - crop the image source preserving the height {$sizes[0]}x{$sizes[1]}
		foreach ( $this->image_sizes as $size_key => $sizes ) {
			$image_path = $this->getImageFullPath( $field_name, $size_key, $image_id );
			exec( $this->mod_config['image_magick_path'] . "convert -geometry {$sizes[0]}x{$sizes[1]} -quality $this->image_quality $tmp_image $image_path" );
			@chmod( $image_path, 0644 );
		}

		// force remove remove temporary
		@unlink( $tmp_image );

		// @Todo garbage collection
		$this->removeUnlinkedImages( $id );

		// restore mask
		@umask( $prev_mask );

		// set extra_fields
		$extra_fields[ $field_name ] = $image_id;
	}

	public function createId( $extra_fields, $file, $field_name ) {
		$salt = defined( 'XOOPS_SALT' ) ? XOOPS_SALT : XOOPS_DB_PREFIX . XOOPS_DB_USER;

		return substr( md5( time() . $salt ), 8, 16 );
	}

	public function getImageFullPath( $field_name, $size_key, $image_id ) {
		return $this->images_path . '/' . sprintf( PICO_EXTRA_IMAGES_FMT, $field_name, $size_key, $image_id );
	}

    /**
     * Image format allowed
     * @param $mime
     * @return string|null
     */
	public function getExtFromMime( $mime ): ?string {
		switch ( strtolower( $mime ) ) {
			case 'image/gif':
				return 'gif';
			case 'image/png':
				return 'png';
			default:
				return 'jpg';
		}
	}

	public function removeUnlinkedImages( $current_id ) {
		$glob_pattern = '*' . substr( $current_id, - 1 ) . '.*'; // 1/16 random match
		//$glob_pattern = '*' ;

		$db = XoopsDatabaseFactory::getDatabaseConnection();

		foreach ( glob( $this->images_path . '/' . $glob_pattern ) as $filename ) {
			if ( strpos( $filename, $current_id ) !== false ) {
				continue;
			}
			if ( preg_match( '/([0-9a-f]{16}\.[a-z]{3})$/', $filename, $regs ) ) {
				$image_id = $regs[1];
				[ $count ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( $this->mydirname . '_contents' ) . " WHERE extra_fields LIKE '%" . addslashes( $image_id ) . "%'" ) );
				if ( $count <= 0 ) {
					unlink( $filename );
				}
			}
		}
	}

	public function stripSlashesGPC( $data ) {
		//trigger_error("assume magic_quotes_gpc is off", E_USER_NOTICE);
		return $data;
	}
}
