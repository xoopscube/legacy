<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

class PicoAutoRegisterWraps {

	public $mydirname = '';
	public $config = [];
	public $wrap_base = '';

	public function __construct( $mydirname, $config ) {
		$this->mydirname = $mydirname;
		$this->config    = $config;
		$this->wrap_base = XOOPS_TRUST_PATH . _MD_PICO_WRAPBASE . '/' . $mydirname;
	}

	// public
	public function updateContent( $content_id, $vpath ): int {
		$db         = XoopsDatabaseFactory::getDatabaseConnection();
		$content_id = (int) $content_id;
		$file_info  = $this->getFileInfo( $vpath );

		// check the file is newer than the contents
		$sql = 'SELECT modified_time FROM ' . $db->prefix( $this->mydirname . '_contents' ) . " WHERE content_id=$content_id";
		[ $modified_time ] = $db->fetchRow( $db->query( $sql ) );

		if ( $modified_time < $file_info['mtime'] ) {
			// make backup
			require_once dirname( __DIR__ ) . '/include/transact_functions.php';
			pico_transact_backupcontent( $this->mydirname, $content_id );

			// update the content
			$set4subject = $file_info['subject'] ? '`subject`=' . $db->quoteString( $file_info['subject'] ) . ',' : '';
			$sql         = 'UPDATE ' . $db->prefix( $this->mydirname . '_contents' ) . " SET $set4subject `modified_time`={$file_info['mtime']},body_cached='',for_search='',`last_cached_time`=0,modifier_uid=0,modifier_ip='' WHERE content_id=$content_id";
			$db->queryF( $sql );

			return $db->getAffectedRows();
		}

		return 0;
	}

	// protected ?
	public function getRegisteringWeight( $cat_id, $vpath ): int {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		[ $weight ] = $db->fetchRow( $db->query( 'SELECT MAX(weight) FROM ' . $db->prefix( $this->mydirname . '_contents' ) . " WHERE `cat_id`=$cat_id" ) );

		return $weight + 1;
	}

	// protected ?
	public function getInsertSQL( $cat_id, $vpath ): string {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$weight    = $this->getRegisteringWeight( $cat_id, $vpath );
		$file_info = $this->getFileInfo( $vpath );

		return "SET `cat_id`=$cat_id,`vpath`="
		       . $db->quoteString( $vpath )
		       . ',`subject`='
		       . $db->quoteString( $file_info['subject_alt'] )
		       . ',`body`='
		       . $db->quoteString( $file_info['body'] )
		       . ",`created_time`={$file_info['mtime']},`modified_time`={$file_info['mtime']},expiring_time=0x7fffffff,poster_uid=0,modifier_uid=0,poster_ip='',modifier_ip='',use_cache=0,weight=$weight,filters='wraps',show_in_navi=1,show_in_menu=1,allow_comment=0,visible=1,approval=1,htmlheader='',htmlheader_waiting='',body_waiting='',body_cached='',tags='',extra_fields='',for_search=''";
	}

	// public
	public function registerContent( $cat_id, $vpath ): ?bool {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$cat_id = (int) $cat_id;

		// insert a new record into the category
		$sql = 'INSERT INTO ' . $db->prefix( $this->mydirname . '_contents' ) . ' ' . $this->getInsertSQL( $cat_id, $vpath );

		// dare to ignore duplicate key error
		// if( ! $db->queryF( $sql ) ) die( _MD_PICO_ERR_SQL.__LINE__.__CLASS__ ) ;
		if ( $db->queryF( $sql ) ) {
			$content_id = $db->getInsertId();

			// rebuild category tree
			require_once dirname( __DIR__ ) . '/include/transact_functions.php';
			pico_sync_cattree( $this->mydirname );

			return $content_id;
		}

		return false;
	}

	// protected ?
	public function removeContent( $content_id ): void {
		// delete transaction
		require_once dirname( __DIR__ ) . '/include/transact_functions.php';
		pico_delete_content( $this->mydirname, $content_id );
	}

	// public
	public function syncCatvpath( $cat_id, $cat_vpath, $wrap_dir ): int {
		$content_handler   = new PicoContentHandler( $this->mydirname );
		$registered_vpaths = array_flip( $content_handler->getAutoRegisteredContents( $cat_id ) );
		$removal_vpaths    = $registered_vpaths;

		$affected_rows = 0;

		$dh                = opendir( $wrap_dir );
		$additional_vpaths = [];
		while ( false !== ( $file = readdir( $dh ) ) ) {
			if ( preg_match( _MD_PICO_AUTOREGIST4PREGEX, $file ) ) {
				$vpath = $cat_vpath . '/' . $file;
				if ( isset( $removal_vpaths[ $vpath ] ) ) {
					// already registered
					unset( $removal_vpaths[ $vpath ] );
					$affected_rows += $this->updateContent( $registered_vpaths[ $vpath ], $vpath );
				} else {
					// to be registered
					$additional_vpaths[ $vpath ] = 0;
				}
			}
		}
		closedir( $dh );

		// remove
		foreach ( $removal_vpaths as $vpath => $content_id ) {
			$this->removeContent( $content_id );
			$affected_rows ++;
		}

		// register
		foreach ( $additional_vpaths as $vpath => $content_id ) {
			if ( $this->registerContent( $cat_id, $vpath ) ) {
				$affected_rows ++;
			}
		}

		return $affected_rows;
	}

	// public
	public function registerByCatvpath( $category_row ): int {
		$cat_id = (int) $category_row['cat_id'];

		$cat_vpath = str_replace( '..', '', $category_row['cat_vpath'] );

		$wrap_dir = $this->wrap_base . $cat_vpath;

		$affected_rows = 0;
		// trigger: mtime of the directory
		if ( is_dir( $wrap_dir ) && filemtime( $wrap_dir ) > $category_row['cat_vpath_mtime'] ) {
			// do sync between the_category/contents and cat_vpath/files
			$affected_rows = $this->syncCatvpath( $cat_id, $cat_vpath, $wrap_dir );

			// touch `cat_vpath_mtime`
			$categoryHandler = new PicoCategoryHandler( $this->mydirname );
			$categoryHandler->touchVpathMtime( $cat_id );
		}

		return $affected_rows;
	}

	// protected
	public function getFileInfo( $vpath ): array {
		$wrap_full_path = $this->wrap_base . $vpath;

		ob_start();
		include $wrap_full_path;
		$full_content = pico_convert_encoding_to_ie( ob_get_clean() );

		// file name
		$filename = substr( strrchr( $wrap_full_path, '/' ), 1 );

		// parse full_content (get subject, body etc.)
		if ( preg_match( '/\<title\>([^<>]+)\<\/title\>/is', $full_content, $regs ) ) {
			$subject = pico_common_unhtmlspecialchars( $regs[1] );
		} else {
			$subject = false;
		}
		if ( preg_match( '/\<body[^<>]*\>(.*)\<\/body\>/is', $full_content, $regs ) ) {
			$body = $regs[1];
		} else {
			$body = $full_content;
		}

		return [
			'mtime'       => (int) @filemtime( $wrap_full_path ),
			'subject'     => $subject,
			'subject_alt' => $subject ?: $filename,
			'filename'    => $filename,
			'body'        => $body,
		];
	}
}
