<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Class MyBlocksAdminForXCL21
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/MyBlocksAdmin.class.php';

class MyBlocksAdminForXCL21 extends MyBlocksAdmin {

	public function MyBlocksAadminForXCL21() {
	}

	public static function &getInstance() {
		static $instance;
		if ( ! isset( $instance ) ) {

			$instance = new self();

			$instance->construct();
		}

		return $instance;
	}

	/**
	 * Virtual options
	 *
	 * @param $block_data
	 *
	 * @return false|mixed|string|null
	 */
	public function renderCell4BlockOptions( $block_data ) {
		// if ($this->target_dirname && '_' !== substr($this->target_dirname, 0, 1)) {
		if ( $this->target_dirname && '_' !== $this->target_dirname[0] ) {
			$langman = D3LanguageManager::getInstance();
			$langman->read( 'admin.php', $this->target_dirname );
		}

		$bid = (int) $block_data['bid'];

		$handler =& xoops_gethandler( 'block' );
		$block   =& $handler->create( false );
		$block->load( $bid );

		$legacy_block =& Legacy_Utils::createBlockProcedure( $block );

		return $legacy_block->getOptionForm();
	}

}
