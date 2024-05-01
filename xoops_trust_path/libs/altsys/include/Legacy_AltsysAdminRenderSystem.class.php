<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Class Legacy_AltsysAdminRenderSystem
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once XOOPS_ROOT_PATH . '/modules/legacyRender/kernel/Legacy_AdminRenderSystem.class.php';
require_once XOOPS_TRUST_PATH . '/libs/altsys/include/altsys_functions.php';
require_once XOOPS_TRUST_PATH . '/libs/altsys/include/admin_in_theme_functions.php';

class Legacy_AltsysAdminRenderSystem extends Legacy_AdminRenderSystem {
	public function renderTheme( &$target ) {
		global $altsysModuleConfig;

		if ( empty( $altsysModuleConfig['admin_in_theme'] ) ) {
			parent::renderTheme( $target );
		} else {
			$attributes = $target->getAttributes();

			altsys_admin_in_theme_in_last( $attributes['xoops_contents'] );
			exit;
		}
	}
}
