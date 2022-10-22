<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

require_once __DIR__ . '/PicoModelContent.class.php';
require_once __DIR__ . '/PicoPermission.class.php';

class PicoCategoryHandler {

	public $mydirname;
	public $permissions;

	public function __construct( $mydirname, $permissions = null ) {
		$this->mydirname = $mydirname;
		if ( $permissions ) {
			$this->permissions = $permissions;
		} else {
			$picoPermission    = &PicoPermission::getInstance();
			$this->permissions = $picoPermission->getPermissions( $mydirname );
		}
	}

	public function getAllCategories( $return_prohibited_also = false ): array {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$sql = 'SELECT cat_id FROM ' . $db->prefix( $this->mydirname . '_categories' ) . ' WHERE 1 ORDER BY cat_order_in_tree';
		if ( ! $crs = $db->query( $sql ) ) {
			if ( $GLOBALS['xoopsUser']->isAdmin() ) {
				echo $db->logger->dumpQueries();
			}
			exit;
		}

		$ret = [];
		//for php5.3+
		while ( list( $cat_id ) = $db->fetchRow( $crs ) ) {
			$objTemp = new PicoCategory( $this->mydirname, $cat_id, $this->permissions );
			if ( $return_prohibited_also || $objTemp->data['can_read'] ) {
				$ret[ $cat_id ] = $objTemp;
			}
		}

		return $ret;
	}

	public function getSubCategories( $cat_id, $return_prohibited_also = false ): array {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$cat_id = (int) $cat_id;
		$sql    = 'SELECT cat_id FROM ' . $db->prefix( $this->mydirname . '_categories' ) . " WHERE pid=$cat_id ORDER BY cat_order_in_tree";
		if ( ! $crs = $db->query( $sql ) ) {
			if ( $GLOBALS['xoopsUser']->isAdmin() ) {
				echo $db->logger->dumpQueries();
			}
			exit;
		}

		$ret = [];
		//for php5.3+
		while ( list( $cat_id ) = $db->fetchRow( $crs ) ) {
			$objTemp = new PicoCategory( $this->mydirname, $cat_id, $this->permissions );
			if ( $return_prohibited_also || $objTemp->data['can_read'] ) {
				$ret[ $cat_id ] = $objTemp;
			}
		}

		return $ret;
	}

	public function &get( $cat_id ): PicoCategory {
		return new PicoCategory( $this->mydirname, $cat_id, $this->permissions );
	}

	public function touchVpathMtime( $cat_id, $mtime = null ): void {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$mtime = empty( $mtime ) ? time() : (int) $mtime;
		$db->queryF( 'UPDATE ' . $db->prefix( $this->mydirname . '_categories' ) . " SET `cat_vpath_mtime`=$mtime WHERE cat_id=$cat_id" );
	}
}

class PicoCategory {

	public $permission;
	public $data = [];
	public $isadminormod;
	public $mydirname;
	public $mod_config;
	public $mod_name;
	public $errorno = 0;
	public $isadmin = false;
	public $child_ids;

	public function __construct( $mydirname, $cat_id, $permissions, $allow_makenew = false, $parentObj = null ) {
		$this->mydirname = $mydirname;

		$db = XoopsDatabaseFactory::getDatabaseConnection();

		// get this "category" from given $cat_id
		$sql = 'SELECT * FROM ' . $db->prefix( $mydirname . '_categories' ) . " WHERE cat_id=$cat_id";
		if ( ! $crs = $db->query( $sql ) ) {
			die( _MD_PICO_ERR_SQL . __LINE__ );
		}
		if ( $db->getRowsNum( $crs ) <= 0 ) {
			if ( $allow_makenew && is_object( $parentObj ) ) {
				$cat_row = $this->getBlankCategoryRow( $parentObj );
			} else {
				$this->errorno = 1; // the category does not exist

				return;
			}
		} else {
			$cat_row = $db->fetchArray( $crs );
		}
		$this->permission   = @$permissions[ @$cat_row['cat_permission_id'] ];
		$this->isadmin      = $permissions['is_module_admin'];
		$this->isadminormod = ! empty( $this->permission['is_moderator'] ) || $this->isadmin;
		$this->data         = [
			                      'id'                  => (int) $cat_row['cat_id'],
			                      'isadmin'             => $this->isadmin,
			                      'isadminormod'        => $this->isadminormod,
			                      'depth_in_tree'       => $cat_row['cat_depth_in_tree'] + 1,
			                      'can_read'            => ( $this->isadminormod || ! empty( $this->permission ) ),
			                      'can_readfull'        => ( $this->isadminormod || @$this->permission['can_readfull'] ),
			                      'can_post'            => ( $this->isadminormod || @$this->permission['can_post'] ),
			                      'can_edit'            => ( $this->isadminormod || @$this->permission['can_edit'] ),
			                      'can_delete'          => ( $this->isadminormod || @$this->permission['can_delete'] ),
			                      'post_auto_approved'  => ( $this->isadminormod || @$this->permission['post_auto_approved'] ),
			                      'can_makesubcategory' => ( $this->isadminormod || @$this->permission['can_makesubcategory'] ),
			                      'cat_options'         => pico_common_unserialize( $cat_row['cat_options'] ),
			                      'paths_raw'           => pico_common_unserialize( $cat_row['cat_path_in_tree'] ),
			                      'redundants'          => pico_common_unserialize( $cat_row['cat_redundants'] ),
		                      ] + $cat_row;

		// array guarantee
		foreach ( [ 'cat_options', 'paths_raw', 'redundants' ] as $key ) {
			if ( ! is_array( $this->data[ $key ] ) ) {
				$this->data[ $key ] = [];
			}
		}

		// set mod_config
		$this->setOverriddenModConfig();
	}

	public function getData(): array {
		return $this->data;
	}

	public function getData4html(): array {
		$myts = &PicoTextSanitizer::sGetInstance();

		return [
			       'link'   => pico_common_make_category_link4html( $this->mod_config, $this->data ),
			       'title'  => $myts->makeTboxData4Show( $this->data['cat_title'], 1, 1 ),
			       'desc'   => $myts->displayTarea( $this->data['cat_desc'], 1 ),
			       'weight' => (int) $this->data['cat_weight'],
		       ] + $this->data;
	}

	public function getData4edit(): array {
		$options4edit = '';
		foreach ( $this->data['cat_options'] as $key => $val ) {
			$options4edit .= htmlspecialchars( $key . ':' . $val . "\n", ENT_QUOTES );
		}

		return [
			       'title'          => htmlspecialchars( $this->data['cat_title'], ENT_QUOTES ),
			       'vpath'          => htmlspecialchars( $this->data['cat_vpath'], ENT_QUOTES ),
			       'desc'           => htmlspecialchars( $this->data['cat_desc'], ENT_QUOTES ),
			       'options'        => $options4edit,
			       'children_count' => count( @$this->data['redundants'] ),
		       ] + $this->getData4html();
	}

	public function getBlankCategoryRow( $parentObj ): array {
		$mod_config = $parentObj->getOverriddenModConfig();
		$pcat_data  = $parentObj->getData();
		$uid        = is_object( @$GLOBALS['xoopsUser'] ) ? $GLOBALS['xoopsUser']->getVar( 'uid' ) : 0;

		return [
			'cat_id'            => - 1,
			'cat_permission_id' => 0,
			'cat_vpath'         => '',
			'pid'               => $pcat_data['id'],
			'cat_title'         => '',
			'cat_desc'          => '',
			'cat_depth_in_tree' => 0,
			'cat_order_in_tree' => 0,
			'cat_path_in_tree'  => '',
			'cat_unique_path'   => '',
			'cat_weight'        => 0,
			'cat_options'       => '',
			'cat_created_time'  => time(),
			'cat_modified_time' => time(),
			'cat_vpath_mtime'   => 0,
			'cat_redundants'    => '',
		];
	}

	public function setOverriddenModConfig(): void {
		$module_handler   = &xoops_gethandler( 'module' );
		$module           = &$module_handler->getByDirname( $this->mydirname );
		$config_handler   = &xoops_gethandler( 'config' );
		$this->mod_config = $config_handler->getConfigList( $module->getVar( 'mid' ) );
		$this->mod_name   = $module->getVar( 'name', 'n' );

		if ( ! is_array( $this->data['cat_options'] ) ) {
			return;
		}
		foreach ( $this->data['cat_options'] as $key => $val ) {
			if ( isset( $this->mod_config[ $key ] ) ) {
				$this->mod_config[ $key ] = $val;
			}
		}
	}

	public function getOverriddenModConfig() {
		return $this->mod_config;
	}

	public function getBreadcrumbs(): array {
		if ( ! is_array( $this->data['paths_raw'] ) ) {
			return [];
		}
		$ret = [];
		foreach ( $this->data['paths_raw'] as $cat_id => $name_raw ) {
			$ret[] = [
				// TODO (returns raw data as possible)
				'url'  => XOOPS_URL . '/modules/' . $this->mydirname . '/' . pico_common_make_category_link4html( $this->mod_config, $cat_id, $this->mydirname ),
				'name' => htmlspecialchars( $name_raw, ENT_QUOTES ),
			];
		}

		return $ret;
	}

	public function getContents( $return_prohibited_also = false ): array {
		$content_handler = new PicoContentHandler( $this->mydirname );

		return $content_handler->getCategoryContents( $this, $return_prohibited_also );
	}

	public function getLatestContents( $num = 0, $fetch_from_subcategories = false ): array {
		$content_handler = new PicoContentHandler( $this->mydirname );

		return $content_handler->getCategoryLatestContents( $this, $num, $fetch_from_subcategories );
	}

	public function crawlChildIds( $node = null ): void {
		if ( empty( $node ) ) {
			$node            = $this->data['redundants']['subcattree_raw'];
			$this->child_ids = [];
		}
		foreach ( $node as $subnode ) {
			$this->child_ids[] = $subnode['cat_id'];
			if ( ! empty( $subnode['subcattree_raw'] ) ) {
				$this->crawlChildIds( $subnode['subcattree_raw'] );
			}
		}
	}

	public function getChildIds() {
		if ( null === $this->child_ids ) {
			$this->crawlChildIds();
		}

		return $this->child_ids;
	}

	public function isError(): bool {
		return $this->errorno > 0;
	}
}
