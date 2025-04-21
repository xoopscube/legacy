<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Class SetupAltsysLangMgr
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

if ( ! defined( 'XOOPS_TRUST_PATH' ) || XOOPS_TRUST_PATH == '' ) {
	header( 'Location: ' . XOOPS_URL . '/modules/altsys/setup_xoops_trust_path.php' );

	exit;
}

const ALTSYS_MYLANGUAGE_ROOT_PATH = XOOPS_ROOT_PATH . '/my_language';


class SetupAltsysLangMgr extends XCube_ActionFilter {
	public function preFilter() {
		$this->mController->mCreateLanguageManager->add( 'SetupAltsysLangMgr::createLanguageManager' );
	}

	/**
	 * @param $languageName
	 * @param mixed $langManager
	 */

	public function createLanguageManager( &$langManager, $languageName ) {
		$langManager = new AltsysLangMgr_LanguageManager();
	}
}


require_once XOOPS_ROOT_PATH . '/core/XCube_LanguageManager.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_LanguageManager.class.php';

/**
 * Class AltsysLangMgr_LanguageManager
 */
class AltsysLangMgr_LanguageManager extends Legacy_LanguageManager {
	public $langman = null;

	public $theme_lang_checked = false;

	public function prepare() {
		$langmanpath = XOOPS_TRUST_PATH . '/libs/altsys/class/D3LanguageManager.class.php';

		if ( ! is_file( $langmanpath ) ) {
			die( 'install the latest altsys' );
		}

		require_once $langmanpath;

		$this->langman = D3LanguageManager::getInstance();

		$this->langman->language = $this->mLanguageName;

		parent::prepare();
	}

	/**
	 * @param $dirname
	 * @param $fileBodyName
	 */
	public function _loadLanguage( $dirname, $fileBodyName ) {
		// read/check once (selected_theme)/language/(lang).php
		if ( ! $this->theme_lang_checked ) {

			$root = XCube_Root::getSingleton();

			if ( ! empty( $root->mContext->mXoopsConfig['theme_set'] ) ) {

				$langdir = XOOPS_THEME_PATH . '/' . $root->mContext->mXoopsConfig['theme_set'] . '/language';
				if ( file_exists( $langdir ) ) {
					$langfile = $langdir . '/' . $this->mLanguageName . '.php';
					$engfile  = $langdir . '/english.php';

					if ( is_file( $langfile ) ) {
						require_once $langfile;
					} elseif ( is_file( $engfile ) ) {
						require_once $engfile;
					}
				}
				$this->theme_lang_checked = true;
			}
		}

		// read normal
		$this->langman->read( $fileBodyName . '.php', $dirname );
	}

	/**
	 * @param string $type
	 */
	public function loadPageTypeMessageCatalog( $type ) {
		// I dare not to use langman...
		if ( false === mb_strpos( $type, '.' ) && $this->langman->my_language ) {
			$mylang_file = $this->langman->my_language . '/' . $this->mLanguageName . '/' . $type . '.php';

			if ( is_file( $mylang_file ) ) {
				require_once $mylang_file;
			}
		}

		$original_error_level = error_reporting();

		error_reporting( $original_error_level & ~E_NOTICE );
		parent::loadPageTypeMessageCatalog( $type );

		error_reporting( $original_error_level );

	}

	public function loadGlobalMessageCatalog() {
		$this->_loadLanguage( 'legacy', 'global' );

		//
		// Now, if XOOPS_USE_MULTIBYTES isn't defined, set zero to it.
		//
		if ( ! defined( 'XOOPS_USE_MULTIBYTES' ) ) {
			define( 'XOOPS_USE_MULTIBYTES', 0 );
		}
	}
}
