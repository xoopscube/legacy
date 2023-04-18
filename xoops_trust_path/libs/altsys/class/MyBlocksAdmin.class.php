<?php
/**
 * Altsys library (UI-Components) Admin blocks and permissions
 * Class MyBlocksAdmin
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

class MyBlocksAdmin {
	public $db;

	public $lang;

	public $cachetime_options = [];

	public $ctype_options = [];

	public $type_options = [];

	public $target_mid = 0;

	public $target_dirname = '';

	public $target_mname = '';

    public $target_mname_bread = '';

	public $block_configs = [];

	public $preview_request = [];

	public function MyBlocksAadmin() {
	}

	public function construct() {
		$this->db =& XoopsDatabaseFactory::getDatabaseConnection();

		$this->lang = @$GLOBALS['xoopsConfig']['language'];

		$this->cachetime_options = [
			0       => _NOCACHE,
			30      => sprintf( _SECONDS, 30 ),
			60      => _MINUTE,
			300     => sprintf( _MINUTES, 5 ),
			1800    => sprintf( _MINUTES, 30 ),
			3600    => _HOUR,
			18000   => sprintf( _HOURS, 5 ),
			86400   => _DAY,
			259200  => sprintf( _DAYS, 3 ),
			604800  => _WEEK,
			2_592_000 => _MONTH,
		];

		$this->ctype_options = [
			'H' => _MD_A_MYBLOCKSADMIN_CTYPE_HTML,
			'T' => _MD_A_MYBLOCKSADMIN_CTYPE_NOSMILE,
			'S' => _MD_A_MYBLOCKSADMIN_CTYPE_SMILE,
			'P' => _MD_A_MYBLOCKSADMIN_CTYPE_PHP,
		];

		$this->type_options = [
			'C' => 'custom block',
			'E' => 'copied custom block',
			'M' => 'module\'s block',
			'D' => 'copied module\'s block',
			'S' => 'system block',
		];
	}

	/**
	 * @return \MyBlocksAdmin
	 */
	public static function getInstance() {
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new self();

			$instance->construct();
		}

		return $instance;
	}

	/**
	 * Virtual
	 */
	public function checkPermission() {
		// only groups with 'module_admin' permissions.
		$module_handler =& xoops_gethandler( 'module' );

		$module =& $module_handler->getByDirname( 'altsys' );

		$moduleperm_handler =& xoops_gethandler( 'groupperm' );

		if ( ! is_object( @$GLOBALS['xoopsUser'] ) || ! $moduleperm_handler->checkRight( 'module_admin', $module->getVar( 'mid' ), $GLOBALS['xoopsUser']->getGroups() ) ) {
            redirect_header( XOOPS_URL . '/', 3, _NOPERM );
		}
	}

	/**
	 * @param $xoopsModule
	 */

	public function init( $xoopsModule ) {
		$target_module = null;
  // altsys "module" MODE

		if ( 'altsys' == $xoopsModule->getVar( 'dirname' ) ) {
			// set target_module if specified by $_GET['dirname']

			$module_handler =& xoops_gethandler( 'module' );

			if ( ! empty( $_GET['dirname'] ) ) {
				$dirname = preg_replace( '/[^0-9a-zA-Z_-]/', '', $_GET['dirname'] );

				$target_module =& $module_handler->getByDirname( $dirname );
			}

			if ( is_object( @$target_module ) ) {
				// module's blocks

				$this->target_mid = $target_module->getVar( 'mid' );

				$this->target_mname = $target_module->getVar( 'name' ) . '&nbsp;' . sprintf( '<b>v. %2.2f </b>', $target_module->getVar( 'version' ) / 100.0 );
                // Since XCL 2.3.x target_mname_bread ( avoid conflict with input hidden of permissions )
                $this->target_mname_bread = $target_module->getVar( 'name' ) . sprintf( '<span class="badge-count" style="font-size:16px;position:relative;bottom:.5em">v %2.2f </span>', $target_module->getVar( 'version' ) / 100.0 );

				$this->target_dirname = $target_module->getVar( 'dirname' );

				$modinfo = $target_module->getInfo();

				// breadcrumbs
				$breadcrumbsObj = AltsysBreadcrumbs::getInstance();

				$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin', '_MI_ALTSYS_MENU_MYBLOCKSADMIN' );

				// Since XCL 2.3.x target_mname_bread ( avoid conflict with input hidden of permissions )
                $breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin&amp;dirname=' . $this->target_dirname, $this->target_mname_bread );

            } else {
				// custom blocks
				$this->target_mid = 0;

				$this->target_mname = _MI_ALTSYS_MENU_CUSTOMBLOCKS;

				$this->target_dirname = '__CustomBlocks__';

				// breadcrumbs
				$breadcrumbsObj = AltsysBreadcrumbs::getInstance();

				$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin', '_MI_ALTSYS_MENU_MYBLOCKSADMIN' );

				$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin&amp;dirname=' . $this->target_dirname, '_MI_ALTSYS_MENU_CUSTOMBLOCKS' );
			}
		} else {
			// myblocksadmin as a library
			$this->target_mid = $xoopsModule->getVar( 'mid' );

			$this->target_mname = $xoopsModule->getVar( 'name' ) . '&nbsp;' . sprintf( '(%2.2f)', $xoopsModule->getVar( 'version' ) / 100.0 );

            // Since XCL 2.3.x target_mname_bread ( avoid conflict with input hidden of permissions )
            $this->target_mname_bread = $xoopsModule->getVar( 'name' ) . sprintf( '<span class="badge-count" style="font-size:16px;position:relative;bottom:.5em">v %2.2f </span>', $xoopsModule->getVar( 'version' ) / 100.0 );

            $this->target_dirname = $xoopsModule->getVar( 'dirname' );

			$mod_url = XOOPS_URL . '/modules/' . $xoopsModule->getVar( 'dirname' );

			$modinfo = $xoopsModule->getInfo();

			$breadcrumbsObj = AltsysBreadcrumbs::getInstance();

            // Since XCL 2.3.x target_mname_bread ( avoid conflict with input hidden of permissions )
            $breadcrumbsObj->appendPath( $mod_url . '/' . @$modinfo['adminindex'], $this->target_mname_bread );

			$breadcrumbsObj->appendPath( $mod_url . '/admin/index.php?mode=admin&amp;lib=altsys&amp;page=myblocksadmin', _MD_A_MYBLOCKSADMIN_BLOCKADMIN );
		}

		// read xoops_version.php of the target
		$this->block_configs = $this->get_block_configs();
	}

	/**
	 * Virtual
	 *
	 * @param $block
	 *
	 * @return bool
	 */
	public function canEdit( $block ) {
		return true;
	}

	/**
	 * Virtual
	 *
	 * @param $block
	 *
	 * @return bool
	 */

	public function canDelete( $block ) {
		// can delete if it is a copy/duplicated block
		return 'D' == $block->getVar( 'block_type' ) || 'C' == $block->getVar( 'block_type' );
	}

	/**
	 * Virtual
	 * ret 0 : cannot
	 * ret 1 : forced by altsys or system
	 * ret 2 : can_clone
	 *
	 * @param $block
	 *
	 * @return int
	 */
	public function canClone( $block ) {
		// can clone link if it is marked as cloneable block
		if ( 'D' == $block->getVar( 'block_type' ) || 'C' == $block->getVar( 'block_type' ) ) {
			return 2;
		}

		// $modversion['blocks'][n]['can_clone']
		foreach ( $this->block_configs as $bconf ) {
			if ( $block->getVar( 'show_func' ) == @$bconf['show_func'] && $block->getVar( 'func_file' ) == @$bconf['file'] && ( empty( $bconf['template'] ) || $block->getVar( 'template' ) == @$bconf['template'] ) ) {
				if ( ! empty( $bconf['can_clone'] ) ) {
					return 2;
				}
			}
		}

		if ( ! empty( $GLOBALS['altsysModuleConfig']['enable_force_clone'] ) ) {
			return 1;
		}

		return 0;
	}

	/**
	 * Virtual options
	 *
	 * @param $block_data
	 *
	 * @return bool|string
	 */
	public function renderCell4BlockOptions( $block_data ) {
		$bid = (int) $block_data['bid'];

		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$handler = xoops_gethandler( 'block' );

			$block = $handler->create( false );

			$block->load( $bid );
		} else {
			$block = new XoopsBlock( $bid );
		}

		return $block->getOptions();
	}

	/**
	 * Virtual
	 * link blocks - modules
	 *
	 * @param $block_data
	 *
	 * @return string
	 */
	public function renderCell4BlockModuleLink( $block_data ) {
		$bid = (int) $block_data['bid'];

		// get selected targets
		if ( is_array( @$block_data['bmodule'] ) ) {
			// bmodule originated from request (preview etc.)
			$selected_mids = $block_data['bmodule'];
		} else {
			// originated from the table of `block_module_link`
			$result = $this->db->query( 'SELECT module_id FROM ' . $this->db->prefix( 'block_module_link' ) . " WHERE block_id='$bid'" );

			$selected_mids = [];

			while ( [$selected_mid] = $this->db->fetchRow( $result ) ) {
				$selected_mids[] = (int) $selected_mid;
			}

			if ( empty( $selected_mids ) ) {
				$selected_mids = [ 0 ];
			} // all pages
		}

		// get all targets
		$module_handler =& xoops_gethandler( 'module' );
		$criteria       = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
		$criteria->add( new Criteria( 'isactive', 1 ) );

		$module_list = $module_handler->getList( $criteria );
		$module_list = [ - 1 => _MD_A_MYBLOCKSADMIN_TOPPAGE, 0 => _MD_A_MYBLOCKSADMIN_ALLPAGES ] + $module_list;

		// build options
		$module_options = '';
		foreach ( $module_list as $mid => $mname ) {
			$mname = htmlspecialchars( $mname );
			//$mname = htmlspecialchars($mname, ENT_QUOTES | ENT_HTML5);
			if ( in_array( $mid, $selected_mids, true ) ) {
				$module_options .= "<option value='$mid' selected='selected'>$mname</option>\n";
			} else {
				$module_options .= "<option value='$mid'>$mname</option>\n";
			}
		}

		return "<select name='bmodules[$bid][]' size='5' multiple='multiple'>$module_options</select>";
	}

	/**
	 * Virtual
	 * group_permission - 'block_read'
	 *
	 * @param $block_data
	 *
	 * @return string
	 */
	public function renderCell4BlockReadGroupPerm( $block_data ) {
		$bid = (int) $block_data['bid'];

		// get selected targets
		if ( is_array( @$block_data['bgroup'] ) ) {
			// bgroup originated from request (preview etc.)
			$selected_gids = $block_data['bgroup'];
		} else {
			// originated from the table of `group_perm`
			$result        = $this->db->query( 'SELECT gperm_groupid FROM ' . $this->db->prefix( 'group_permission' ) . " WHERE gperm_itemid='$bid' AND gperm_name='block_read'" );
			$selected_gids = [];
			while ( [$selected_gid] = $this->db->fetchRow( $result ) ) {
				$selected_gids[] = (int) $selected_gid;
			}
			if ( 0 == $bid && empty( $selected_gids ) ) {
				$selected_gids = $GLOBALS['xoopsUser']->getGroups();
			}
		}

		// get all targets
		$group_handler = xoops_gethandler( 'group' );
		$groups        = $group_handler->getObjects();

		// build options
		$group_options = '';
		foreach ( $groups as $group ) {
			$gid   = $group->getVar( 'groupid' );
			$gname = $group->getVar( 'name', 's' );
			//!Fix Note: do not apply here a strict third parameter
			// if (in_array($gid, $selected_gids, true)) {
			if ( in_array( $gid, $selected_gids ) ) {
				$group_options .= "<option value='$gid' selected='selected'>$gname</option>\n";
			} else {
				$group_options .= "<option value='$gid'>$gname</option>\n";
			}
		}

		return "<select name='bgroups[$bid][]' size='5' multiple='multiple'>$group_options</select>";
	}

	/**
	 * Virtual
	 * Visible and side
	 *
	 * @param $block_data
	 *
	 * @return string
	 */
	public function renderCell4BlockPosition( $block_data ) {
		$bid     = (int) $block_data['bid'];
		$side    = (int) $block_data['side'];
		$visible = (int) $block_data['visible'];

		$sseln            = $ssel0 = $ssel1 = $ssel2 = $ssel3 = $ssel4 = '';
		$scoln            = $scol0 = $scol1 = $scol2 = $scol3 = $scol4 = 'unselected';
		$stextbox         = 'unselected';
		$value4extra_side = '';

		if ( 1 != $visible ) {
			$sseln = " checked='checked'";
			$scoln = 'disabled';
		} else {
			switch ( $side ) {
				case XOOPS_SIDEBLOCK_LEFT:
					$ssel0 = " checked='checked'";
					$scol0 = 'selected';
					break;
				case XOOPS_SIDEBLOCK_RIGHT:
					$ssel1 = " checked='checked'";
					$scol1 = 'selected';
					break;
				case XOOPS_CENTERBLOCK_LEFT:
					$ssel2 = " checked='checked'";
					$scol2 = 'selected';
					break;
				case XOOPS_CENTERBLOCK_RIGHT:
					$ssel4 = " checked='checked'";
					$scol4 = 'selected';
					break;
				case XOOPS_CENTERBLOCK_CENTER:
					$ssel3 = " checked='checked'";
					$scol3 = 'selected';
					break;
				default:
					$value4extra_side = $side;
					$stextbox         = 'selected';
					break;
			}
		}

    // Block-Side Render View
    return "
    <label title='Block-Left'>
        <input type='radio' name='sides[$bid]' value='" . XOOPS_SIDEBLOCK_LEFT . "' class='blockposition' $ssel0 onclick='document.getElementById(\"extra_side_$bid\").value=" . XOOPS_SIDEBLOCK_LEFT . ";'>
    </label>
    <div>-</div>
    <label title='Center-Block-Left'>
        <input type='radio' name='sides[$bid]' value='" . XOOPS_CENTERBLOCK_LEFT . "' class='blockposition' $ssel2 onclick='document.getElementById(\"extra_side_$bid\").value=" . XOOPS_CENTERBLOCK_LEFT . ";'>
    </label>
    <label title='Center-Block-Center'>
        <input type='radio' name='sides[$bid]' value='" . XOOPS_CENTERBLOCK_CENTER . "' class='blockposition' $ssel3 onclick='document.getElementById(\"extra_side_$bid\").value=" . XOOPS_CENTERBLOCK_CENTER . ";'>
    </label>
    <label title='Center-Block-Right'>
        <input type='radio' name='sides[$bid]' value='" . XOOPS_CENTERBLOCK_RIGHT . "' class='blockposition' $ssel4 onclick='document.getElementById(\"extra_side_$bid\").value=" . XOOPS_CENTERBLOCK_RIGHT . ";'>
    </label>
    <div>-</div>
    <label title='Block-Right'>
        <input type='radio' name='sides[$bid]' value='" . XOOPS_SIDEBLOCK_RIGHT . "' class='blockposition' $ssel1 onclick='document.getElementById(\"extra_side_$bid\").value=" . XOOPS_SIDEBLOCK_RIGHT . ";'>
    </label>

    <input type='hidden' name='extra_sides[$bid]' value='" . $value4extra_side . "' class='block-extra-side' id='extra_side_$bid'>

    <label title='" . _NONE . "'>
        <input type='radio' name='sides[$bid]' value='-1' class='blockposition ui-input-red' $sseln onclick='document.getElementById(\"extra_side_$bid\").value=-1;'>
    </label>
	";
	}


	// public
	public function list_blocks() {
		$handler = null;
  global $xoopsGTicket;

		// main query
		$sql       = 'SELECT * FROM ' . $this->db->prefix( 'newblocks' ) . " WHERE mid='$this->target_mid' ORDER BY visible DESC,side,weight";
		$result    = $this->db->query( $sql );
		$block_arr = [];

		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$handler =& xoops_gethandler( 'block' ); //add
		}
		while ( $myrow = $this->db->fetchArray( $result ) ) {
			if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
				$block_one =& $handler->create( false );
				$block_one->assignVars( $myrow );
				$block_arr[] =& $block_one;
			} else {
				$block_arr[] = new XoopsBlock( $myrow );
			}
		}
		if ( empty( $block_arr ) ) {
			return;
		}

		// blocks rendering loop
		$blocks4assign = [];
		foreach ( $block_arr as $i => $block ) {
			$block_data      = [
				'bid'        => (int) $block->getVar( 'bid' ),
				'name'       => $block->getVar( 'name', 'n' ),
				'title'      => $block->getVar( 'title', 'n' ),
				'weight'     => (int) $block->getVar( 'weight' ),
				'bcachetime' => (int) $block->getVar( 'bcachetime' ),
				'side'       => (int) $block->getVar( 'side' ),
				'visible'    => (int) $block->getVar( 'visible' ),
				'can_edit'   => $this->canEdit( $block ),
				'can_delete' => $this->canDelete( $block ),
				'can_clone'  => $this->canClone( $block ),
			];
			$blocks4assign[] = [
                'name_raw'         => $block_data['name'],
                'title_raw'        => $block_data['title'],
                'cell_position'    => $this->renderCell4BlockPosition( $block_data ),
                'cell_module_link' => $this->renderCell4BlockModuleLink( $block_data ),
                'cell_group_perm'  => $this->renderCell4BlockReadGroupPerm( $block_data ),
            ] + $block_data;
		}

		// display
        // Since XCL 2.3.x target_mname_bread ( avoid conflict with input hidden of permissions )
		require_once XOOPS_TRUST_PATH . '/libs/altsys/class/D3Tpl.class.php';
		$tpl = new D3Tpl();
		$tpl->assign(
			[
				'target_mid'        => $this->target_mid,
				'target_dirname'    => $this->target_dirname,
				'target_mname'      => $this->target_mname,
                'target_mname_bread'=> $this->target_mname_bread,
				'language'          => $this->lang,
				'cachetime_options' => $this->cachetime_options,
				'blocks'            => $blocks4assign,
				'gticket_hidden'    => $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'myblocksadmin' ),
			]
		);
		$tpl->display( 'db:altsys_main_blocks_admin_list.html' );
	}

	/**
	 * @return array
	 */
	public function get_block_configs() {
		$modversion = [];
  if ( $this->target_mid <= 0 ) {
			return [];
		}
		include XOOPS_ROOT_PATH . '/modules/' . $this->target_dirname . '/xoops_version.php';

		if ( empty( $modversion['blocks'] ) ) {
			return [];
		}

		return $modversion['blocks'];
	}


	public function list_groups() {
		$handler = null;
  // query for getting blocks
		$sql       = 'SELECT * FROM ' . $this->db->prefix( 'newblocks' ) . " WHERE mid='$this->target_mid' ORDER BY visible DESC,side,weight";
		$result    = $this->db->query( $sql );
		$block_arr = [];

		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$handler =& xoops_gethandler( 'block' ); //add
		}
		while ( $myrow = $this->db->fetchArray( $result ) ) {
			if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
				$block_one =& $handler->create( false );
				$block_one->assignVars( $myrow );
				$block_arr[] =& $block_one;
			} else {
				$block_arr[] = new XoopsBlock( $myrow );
			}
		}

		$item_list = [];
		foreach ( array_keys( $block_arr ) as $i ) {
			$item_list[ $block_arr[ $i ]->getVar( 'bid' ) ] = $block_arr[ $i ]->getVar( 'title' );
		}

        // ADMIN BLOCKS PERMISSIONS
		$form = new MyXoopsGroupPermForm( '<h2 id="block-permissions">'._MD_A_MYBLOCKSADMIN_PERMFORM.'</h2>', 1, 'block_read', '' );
		// skip system (TODO)
		if ( $this->target_mid > 1 ) {
			$form->addAppendix( 'module_admin', $this->target_mid, $this->target_mname . ' ' . _MD_A_MYBLOCKSADMIN_PERM_MADMIN );
			$form->addAppendix( 'module_read', $this->target_mid, $this->target_mname . ' ' . _MD_A_MYBLOCKSADMIN_PERM_MREAD );
		}
		foreach ( $item_list as $item_id => $item_name ) {
			$form->addItem( $item_id, $item_name );
		}
		echo $form->render();
	}

	/**
	 * @param       $bid
	 * @param       $bside
	 * @param       $bweight
	 * @param       $bvisible
	 * @param       $btitle
	 * @param       $bcontent
	 * @param       $bctype
	 * @param       $bcachetime
	 * @param array $options
	 *
	 * @return string
	 */
	public function update_block( $bid, $bside, $bweight, $bvisible, $btitle, $bcontent, $bctype, $bcachetime, $options = [] ) {
		global $xoopsConfig;

		//HACK by domifara
		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$handler =& xoops_gethandler( 'block' );
			$block   =& $handler->create( false );
			$block->load( $bid );
		} else {
			$block = new XoopsBlock( $bid );
		}

		if ( $bside >= 0 ) {
			$block->setVar( 'side', $bside );
		}
		$block->setVar( 'weight', $bweight );
		$block->setVar( 'visible', $bvisible );
		$block->setVar( 'title', $btitle );
		if ( isset( $bcontent ) ) {
			$block->setVar( 'content', $bcontent );
		}
		if ( isset( $bctype ) ) {
			$block->setVar( 'c_type', $bctype );
		}
		$block->setVar( 'bcachetime', $bcachetime );

		//!Fix Test
		// if ($options && is_array($options)) {
		if ( is_array( $options ) && count( $options ) > 0 ) {
			$block->setVar( 'options', implode( '|', $options ) );
		}
		if ( 'C' == $block->getVar( 'block_type' ) ) {
			$name = $this->get_blockname_from_ctype( $block->getVar( 'c_type' ) );
			$block->setVar( 'name', $name );
		}
		$msg = _MD_A_MYBLOCKSADMIN_DBUPDATED;

		if ( false != $block->store() ) {
			include_once XOOPS_ROOT_PATH . '/class/template.php';
			$xoopsTpl = new XoopsTpl();
			$xoopsTpl->xoops_setCaching( 2 );

			if ( '' != $block->getVar( 'template' ) ) {
				if ( $xoopsTpl->is_cached( 'db:' . $block->getVar( 'template' ) ) ) {
					if ( ! $xoopsTpl->clear_cache( 'db:' . $block->getVar( 'template' ) ) ) {
						$msg = 'Unable to clear cache for block ID' . $bid;
					}
				}
			} else {
				if ( $xoopsTpl->is_cached( 'db:system_dummy.html', 'blk_' . $bid ) ) {
					if ( ! $xoopsTpl->clear_cache( 'db:system_dummy.html', 'blk_' . $bid ) ) {
						$msg = 'Unable to clear cache for block ID' . $bid;
					}
				}
			}
		} else {
			$msg = 'Failed update of block. ID:' . $bid;
		}

		return $msg;
	}

	// virtual

	/**
	 * @param int $bid
	 * @param $bmodules
	 */
	public function updateBlockModuleLink( $bid, $bmodules ) {
		$bid   = (int) $bid;
		$table = $this->db->prefix( 'block_module_link' );

		$sql = "DELETE FROM `$table` WHERE `block_id`=$bid";
		$this->db->query( $sql );
		foreach ( $bmodules as $mid ) {
			$mid = (int) $mid;
			$sql = "INSERT INTO `$table` (`block_id`,`module_id`) VALUES ($bid,$mid)";
			$this->db->query( $sql );
		}
	}

	/**
	 * Virtual
	 *
	 * @param int $bid
	 * @param $req_gids
	 */
	public function updateBlockReadGroupPerm( $bid, $req_gids ) {
		$bid      = (int) $bid;
		$table    = $this->db->prefix( 'group_permission' );
		$req_gids = array_map( 'intval', $req_gids );
		sort( $req_gids );

		// compare group ids from request and the records.
		$sql     = "SELECT `gperm_groupid` FROM `$table` WHERE gperm_name='block_read' AND `gperm_itemid`=$bid";
		$result  = $this->db->query( $sql );
		$db_gids = [];
		while ( [$gid] = $this->db->fetchRow( $result ) ) {
			$db_gids[] = $gid;
		}
		$db_gids = array_map( 'intval', $db_gids );
		sort( $db_gids );

		// if they are identical, just return (prevent increase of gperm_id)
		if ( serialize( $req_gids ) == serialize( $db_gids ) ) {
			return;
		}

		$sql = "DELETE FROM `$table` WHERE gperm_name='block_read' AND `gperm_itemid`=$bid";
		$this->db->query( $sql );
		foreach ( $req_gids as $gid ) {
			$gid = (int) $gid;
			$sql = "INSERT INTO `$table` (`gperm_groupid`,`gperm_itemid`,`gperm_modid`,`gperm_name`) VALUES ($gid,$bid,1,'block_read')";
			$this->db->query( $sql );
		}
	}

	/**
	 * @return string
	 */
	public function do_order() {
		$sides = is_array( @$_POST['sides'] ) ? $_POST['sides'] : [];
		foreach ( array_keys( $sides ) as $bid ) {
			$request = $this->fetchRequest4Block( $bid );

			// update the block
			$this->update_block( $request['bid'], $request['side'], $request['weight'], $request['visible'], $request['title'], null, null, $request['bcachetime'], [] );

			// block_module_link update
			$this->updateBlockModuleLink( $bid, $request['bmodule'] );

			// group_permission update
			$this->updateBlockReadGroupPerm( $bid, $request['bgroup'] );
		}

		return _MD_A_MYBLOCKSADMIN_DBUPDATED;
	}

	/**
	 * @param int $bid
	 *
	 * @return array
	 */
	public function fetchRequest4Block( $bid ) {
		$myts = null;
  $bid = (int) $bid;
		( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = MyTextSanitizer::sGetInstance() ) || $myts = MyTextSanitizer::getInstance();

		if ( @$_POST['extra_sides'][ $bid ] > 0 ) {
			$_POST['sides'][ $bid ] = (int) $_POST['extra_sides'][ $bid ];
		}

		if ( @$_POST['sides'][ $bid ] < 0 ) {
			$visible                = 0;
			$_POST['sides'][ $bid ] = - 1;
		} else {
			$visible = 1;
		}

		return [
			'bid'        => $bid,
			'side'       => (int) @$_POST['sides'][ $bid ],
			'weight'     => (int) @$_POST['weights'][ $bid ],
			'visible'    => $visible,
			'title'      => $myts->stripSlashesGPC( @$_POST['titles'][ $bid ] ),
			'content'    => $myts->stripSlashesGPC( @$_POST['contents'][ $bid ] ),
			'ctype'      => preg_replace( '/[^A-Z]/', '', @$_POST['ctypes'][ $bid ] ),
			'bcachetime' => (int) @$_POST['bcachetimes'][ $bid ],
			'bmodule'    => is_array( @$_POST['bmodules'][ $bid ] ) ? $_POST['bmodules'][ $bid ] : [ 0 ],
			'bgroup'     => is_array( @$_POST['bgroups'][ $bid ] ) ? $_POST['bgroups'][ $bid ] : [],
			'options'    => is_array( @$_POST['options'][ $bid ] ) ? $_POST['options'][ $bid ] : [],
		];
	}

	/**
	 * @param int $bid
	 *
	 * @return string
	 */
	public function do_delete( $bid ) {
		$bid = (int) $bid;

		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$handler =& xoops_gethandler( 'block' );
			$block   =& $handler->create( false );
			$block->load( $bid );
		} else {
			$block = new XoopsBlock( $bid );
		}

		if ( ! is_object( $block ) ) {
			die( 'Invalid bid' );
		}
		if ( ! $this->canDelete( $block ) ) {
			die( 'Cannot delete this block' );
		}
		$this->do_deleteBlockReadGroupPerm( $bid ); //HACK add by domifara
		$block->delete();

		return _MD_A_MYBLOCKSADMIN_DBUPDATED;
	}

	/**
	 * add by domifara
	 *
	 * @param int $bid
	 */
	public function do_deleteBlockReadGroupPerm( $bid ) {
		$bid   = (int) $bid;
		$table = $this->db->prefix( 'group_permission' );
		$sql   = "DELETE FROM `$table` WHERE gperm_name='block_read' AND `gperm_itemid`=$bid";
		$this->db->query( $sql );
	}

	/**
	 * @param int $bid
	 */
	public function form_delete( $bid ) {
		$bid = (int) $bid;

		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$handler =& xoops_gethandler( 'block' );
			$block   =& $handler->create( false );
			$block->load( $bid );
		} else {
			$block = new XoopsBlock( $bid );
		}

		if ( ! is_object( $block ) ) {
			die( 'Invalid bid' );
		}
		if ( ! $this->canDelete( $block ) ) {
			die( 'Cannot delete this block' );
		}

		// breadcrumbs
		$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
		$breadcrumbsObj->appendPath( '', _DELETE );

		xoops_confirm( [ 'op' => 'delete_ok' ] + $GLOBALS['xoopsGTicket']->getTicketArray( __LINE__, 1800, 'myblocksadmin' ), "?mode=admin&amp;lib=altsys&amp;page=myblocksadmin&amp;dirname=$this->target_dirname&amp;bid=$bid", sprintf( _MD_A_MYBLOCKSADMIN_FMT_REMOVEBLOCK, $block->getVar( 'title' ) ) );
	}


	public function do_clone( $bid ) {
		$handler = null;
  $bid = (int) $bid;

		$request = $this->fetchRequest4Block( $bid );

		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$handler =& xoops_gethandler( 'block' );
			$block   =& $handler->create( false );
			$block->load( $bid );
		} else {
			$block = new XoopsBlock( $bid );
		}

		if ( ! $block->getVar( 'bid' ) ) {
			die( 'Invalid bid' );
		}
		if ( ! $this->canClone( $block ) ) {
			die( 'Invalid block_type' );
		}

		if ( empty( $_POST['options'] ) ) {
			$options = [];
		} elseif ( is_array( $_POST['options'] ) ) {
			$options = $_POST['options'];
		} else {
			$options = explode( '|', $_POST['options'] );
		}

		// for backward compatibility
		// $cblock =& $block->clone(); or $cblock =& $block->xoopsClone();

		//HACK by domifara
		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$cblock =& $handler->create( false );
		} else {
			$cblock = new XoopsBlock();
		}

		foreach ( $block->vars as $k => $v ) {
			$cblock->assignVar( $k, $v['value'] );
		}
		$cblock->setNew();
		$cblock->setVar( 'bid', 0 );
		$cblock->setVar( 'block_type', 'C' == $block->getVar( 'block_type' ) ? 'C' : 'D' );
		$cblock->setVar( 'func_num', $this->find_func_num_vacancy( $block->getVar( 'mid' ) ) );
		// store the block into DB as a new one
		$newbid = $cblock->store();
		if ( ! $newbid ) {
			return $cblock->getHtmlErrors();
		}

		// update the block by the request
		$this->update_block( $newbid, $request['side'], $request['weight'], $request['visible'], $request['title'], $request['content'], $request['ctype'], $request['bcachetime'], is_array( @$_POST['options'] ) ? $_POST['options'] : [] );

		// block_module_link update
		$this->updateBlockModuleLink( $newbid, $request['bmodule'] );

		// group_permission update
		$this->updateBlockReadGroupPerm( $newbid, $request['bgroup'] );

		return _MD_A_MYBLOCKSADMIN_DBUPDATED;
	}

	/**
	 * @param $mid
	 *
	 * @return int
	 */
	public function find_func_num_vacancy( $mid ) {
		$func_num = 256;
		do {
			$func_num --;
			[ $count ] = $this->db->fetchRow( $this->db->query( 'SELECT COUNT(*) FROM ' . $this->db->prefix( 'newblocks' ) . ' WHERE mid=' . (int) $mid . ' AND func_num=' . $func_num ) );
		} while ( $count > 0 );

		return $func_num > 128 ? $func_num : 255;
	}

	/**
	 * @param int $bid
	 *
	 * @return string
	 */

	public function do_edit( $bid ) {
		$bid = (int) $bid;

		if ( $bid <= 0 ) {
			// new custom block

			if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
				$handler   =& xoops_gethandler( 'block' );
				$new_block =& $handler->create( false );
			} else {
				$new_block = new XoopsBlock();
			}

			$new_block->setNew();
			$new_block->setVar( 'name', $this->get_blockname_from_ctype( 'C' ) );
			$new_block->setVar( 'block_type', 'C' );
			$new_block->setVar( 'func_num', 0 );
			$bid     = $new_block->store();
			$request = $this->fetchRequest4Block( 0 );
			// permission copy
			foreach ( $GLOBALS['xoopsUser']->getGroups() as $gid ) {
				$sql = 'INSERT INTO ' . $this->db->prefix( 'group_permission' ) . " (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ($gid, $bid, 1, 'block_read')";
				$this->db->query( $sql );
			}
		} else {
			$request = $this->fetchRequest4Block( $bid );
		}

		// update the block by the request
		$msg = $this->update_block( $bid, $request['side'], $request['weight'], $request['visible'], $request['title'], $request['content'], $request['ctype'], $request['bcachetime'], is_array( @$_POST['options'] ) ? $_POST['options'] : [] );

		// block_module_link update
		$this->updateBlockModuleLink( $bid, $request['bmodule'] );

		// group_permission update
		$this->updateBlockReadGroupPerm( $bid, $request['bgroup'] );

		return $msg;
	}

	/**
	 * @param        $bid
	 * @param string $mode
	 */
	public function form_edit( $bid, $mode = 'edit' ) {
		$bid = (int) $bid;

		//HACK by domifara
		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$handler =& xoops_gethandler( 'block' );
			$block   =& $handler->create( false );
			$block->load( $bid );
		} else {
			$block = new XoopsBlock( $bid );
		}

		if ( ! $block->getVar( 'bid' ) ) {
			// new defaults
			$bid  = 0;
			$mode = 'new';
			$block->setVar( 'mid', 0 );
			$block->setVar( 'block_type', 'C' );
		}

		switch ( $mode ) {
			case 'clone':
				$form_title   = _MD_A_MYBLOCKSADMIN_CLONEFORM;
				$button_value = _MD_A_MYBLOCKSADMIN_BTN_CLONE;
				$next_op      = 'clone_ok';
				// breadcrumbs
				$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
				$breadcrumbsObj->appendPath( '', _MD_A_MYBLOCKSADMIN_CLONEFORM );
				break;
			case 'new':
				$form_title   = _MD_A_MYBLOCKSADMIN_NEWFORM;
				$button_value = _MD_A_MYBLOCKSADMIN_BTN_NEW;
				$next_op      = 'new_ok';
				// breadcrumbs
				$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
				$breadcrumbsObj->appendPath( '', _MD_A_MYBLOCKSADMIN_NEWFORM );
				break;
			case 'edit':
			default:
				$form_title   = _MD_A_MYBLOCKSADMIN_EDITFORM;
				$button_value = _MD_A_MYBLOCKSADMIN_BTN_EDIT;
				$next_op      = 'edit_ok';
				// breadcrumbs
				$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
				$breadcrumbsObj->appendPath( '', _MD_A_MYBLOCKSADMIN_EDITFORM );
				break;
		}
		//!Fix test
		// $is_custom = in_array($block->getVar('block_type'), ['C', 'E'], true) ? true : false;
		$is_custom             = in_array( $block->getVar( 'block_type' ), [ 'C', 'E' ] );
		$block_template        = $block->getVar( 'template', 'n' );
		$block_template_tplset = '';

		if ( ! $is_custom && $block_template ) {

			// find template of the block
			$tplfile_handler       =& xoops_gethandler( 'tplfile' );
			$found_templates       =& $tplfile_handler->find( $GLOBALS['xoopsConfig']['template_set'], 'block', null, null, $block_template );
			$block_template_tplset = (is_countable($found_templates) ? count( $found_templates ) : 0) > 0 ? $GLOBALS['xoopsConfig']['template_set'] : 'default';
		}
		//HACK by domifara
		/*
			if ( !($block->getVar('c_type')) ){
				$block->setVar('c_type','S');
			}
		*/
		$block_data = $this->preview_request + [
				'bid'             => $bid,
				'name'            => $block->getVar( 'name', 'n' ),
				'title'           => $block->getVar( 'title', 'n' ),
				'weight'          => (int) $block->getVar( 'weight' ),
				'bcachetime'      => (int) $block->getVar( 'bcachetime' ),
				'side'            => (int) $block->getVar( 'side' ),
				'visible'         => (int) $block->getVar( 'visible' ),
				'template'        => $block_template,
				'template_tplset' => $block_template_tplset,
				'options'         => $block->getVar( 'options' ),
				'content'         => $block->getVar( 'content', 'n' ),
				'is_custom'       => $is_custom,
				'type'            => $block->getVar( 'block_type' ),
				'ctype'           => $block->getVar( 'c_type' ),
			];

		$block4assign = [
                'name_raw'         => $block_data['name'],
                'title_raw'        => $block_data['title'],
                'content_raw'      => $block_data['content'],
                'cell_position'    => $this->renderCell4BlockPosition( $block_data ),
                'cell_module_link' => $this->renderCell4BlockModuleLink( $block_data ),
                'cell_group_perm'  => $this->renderCell4BlockReadGroupPerm( $block_data ),
                'cell_options'     => $this->renderCell4BlockOptions( $block_data ),
                'content_preview'  => $this->previewContent( $block_data ),
            ] + $block_data;

		// display
		require_once XOOPS_TRUST_PATH . '/libs/altsys/class/D3Tpl.class.php';
		$tpl = new D3Tpl();

		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$tpl->assign( 'xoops_cube_legacy', true );
			include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
		} else {
			$tpl->assign( 'xoops_cube_legacy', false );
		}
        // Since XCL 2.3.x target_mname_bread ( avoid conflict with input hidden of permissions )
		$tpl->assign(
			[
				'target_dirname'    => $this->target_dirname,
				'target_mname'      => $this->target_mname,
                'target_mname_bread'=> $this->target_mname_bread,
				'language'          => $this->lang,
				'cachetime_options' => $this->cachetime_options,
				'ctype_options'     => $this->ctype_options,
				'block'             => $block4assign,
				'op'                => $next_op,
				'form_title'        => $form_title,
				'submit_button'     => $button_value,
				//            'common_fck_installed' => $this->checkFck(),
				'gticket_hidden'    => $GLOBALS['xoopsGTicket']->getTicketHtml( __LINE__, 1800, 'myblocksadmin' ),
			]
		);

		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$tpl->display( 'db:altsys_main_blocks_admin_edit.html' );
		}

		return;
	}


	/**
	 * @param $block_data
	 *
	 * @return string
	 */

	public function previewContent( $block_data ) {
		$bid = (int) $block_data['bid'];

		if ( ! $block_data['is_custom'] ) {
			return '';
		}
		if ( empty( $this->preview_request ) ) {
			return '';
		}

		//HACK by domifara
		//TODO : No need to hook block here
		$block = new XoopsBlock( $bid );
		/*
			$handler =& xoops_gethandler('block');
			$block =& $handler->create(false) ;
			$block->load($bid) ;
		*/

		if ( $block->getVar( 'mid' ) ) {
			return '';
		}

		$block->setVar( 'title', $block_data['title'] );
		$block->setVar( 'content', $block_data['content'] );

		restore_error_handler();
		$original_level = error_reporting( E_ALL );
		$ret            = $block->getContent( 'S', $block_data['ctype'] );
		error_reporting( $original_level );

		return $ret;
	}

	/**
	 * @param $bctype
	 *
	 * @return mixed|string
	 */
	public function get_blockname_from_ctype( $bctype ) {
		$ctypes = [
			'H' => _MD_A_MYBLOCKSADMIN_CTYPE_HTML,
			'S' => _MD_A_MYBLOCKSADMIN_CTYPE_SMILE,
			'N' => _MD_A_MYBLOCKSADMIN_CTYPE_NOSMILE,
			'P' => _MD_A_MYBLOCKSADMIN_CTYPE_PHP,
		];

		return $ctypes[ $bctype ] ?? _MD_A_MYBLOCKSADMIN_CTYPE_SMILE;
	}


	public function processPost() {
		// Ticket Check
		if ( ! $GLOBALS['xoopsGTicket']->check( true, 'myblocksadmin' ) ) {
			redirect_header( XOOPS_URL . '/', 3, $GLOBALS['xoopsGTicket']->getErrors() );
		}

		$msg = '';
		$bid = (int) @$_GET['bid'];

		if ( ! empty( $_POST['preview'] ) ) {
			// preview
			$this->preview_request = $this->fetchRequest4Block( $bid );
			$_GET['op']            = str_replace( '_ok', '', @$_POST['op'] );

			return; // continue ;
		}

		if ( 'order' == @$_POST['op'] ) {
			// order ok
			$msg = $this->do_order();
		} elseif ( 'delete_ok' == @$_POST['op'] ) {
			// delete ok
			$msg = $this->do_delete( $bid );
		} elseif ( 'clone_ok' == @$_POST['op'] ) {
			// clone ok
			$msg = $this->do_clone( $bid );
		} elseif ( 'edit_ok' == @$_POST['op'] || 'new_ok' == @$_POST['op'] ) {
			// edit ok
			$msg = $this->do_edit( $bid );
		} elseif ( ! empty( $_POST['submit'] ) ) {
			// update module_admin, module_read, block_read
			include dirname( __DIR__ ) . '/include/mygroupperm.php';
			$msg = _MD_A_MYBLOCKSADMIN_PERMUPDATED;
		}

        redirect_header( $_SERVER['REQUEST_URI'], 1, $msg );
		exit;
	}


	public function processGet() {
		$bid = (int) @$_GET['bid'];
		switch ( @$_GET['op'] ) {
			case 'clone':
				$this->form_edit( $bid, 'clone' );
				break;
			case 'new':
			case 'edit':
				$this->form_edit( $bid, 'edit' );
				break;
			case 'delete':
				$this->form_delete( $bid );
				break;
			case 'list':
			default:
				// the first form (blocks)
				$this->list_blocks();
				// the second form (groups)
				$this->list_groups();
				break;
		}
	}
}
