<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

define( '_D3FORUM_ANTISPAM_JAPANESE_AMB', 3 );

require_once __DIR__ . '/D3forumAntispamAbstract.class.php';

class D3forumAntispamJapanese extends D3forumAntispamAbstract {

	private $dictionary_cache = [];

	private $dictionary_file;

	public function __construct() {
		$this->setVar( 'dictionary_file', 'AntispamJapaneseDictionary.txt' );
	}

	public function getKanaKanji( $time = null ) {
		if ( empty( $time ) ) {
			$time = time();
		}

		if ( ! isset( $this->dictionary_cache[ $this->dictionary_file ] ) ) {
			// SKK EUC dictionary
			$lines = file( $this->dictionary_file );

			foreach ( $lines as $line ) {

				$line = mb_convert_encoding( $line, mb_internal_encoding(), 'EUC-JP' );

				if ( preg_match( '#(.+) /(.+)/#', $line, $regs ) ) {
					$this->dictionary_cache[ $this->dictionary_file ][] = [
						'yomigana' => $regs[1],
						'kanji'    => $regs[2],
					];
				}
			}
		}

		$size = count( $this->dictionary_cache[ $this->dictionary_file ] );

		$ret = [];

		for ( $i = 0; $i < 3; $i ++ ) {
			$ret[] = $this->dictionary_cache[ $this->dictionary_file ][ abs( crc32( md5( gmdate( 'YmdH', $time ) . XOOPS_DB_PREFIX . XOOPS_DB_NAME . $i ) ) ) % $size ];
		}

		return $ret;
	}

	public function getHtml4Assign() {
		$yomi_kans = $this->getKanaKanji();

		shuffle( $yomi_kans );

		$yomi_kan = $yomi_kans[0];

		$kanji = $yomi_kan['kanji'];

		$html = '<label for="antispam_yomigana">' . _MD_D3FORUM_LABEL_JAPANESEINPUTYOMI . ': <strong class="antispam_kanji">' . htmlspecialchars( $kanji ) . '</strong></label><input type="text" name="antispam_yomigana" id="antispam_yomigana" value="">';

		return [
			'html_in_form'            => $html,
			'js_global'               => '',
			'js_in_validate_function' => 'if ( ! myform.antispam_yomigana.value ) { window.alert("' . _MD_D3FORUM_ERR_JAPANESENOTINPUT . '"); myform.antispam_yomigana.focus(); return false; }',
		];
	}

	public function checkValidate() {
		$yomigana = mb_convert_kana( trim( @$_POST['antispam_yomigana'] ), 'HVc' );

		$yomi_kans = array_merge( $this->getKanaKanji(), $this->getKanaKanji( time() - 3600 ) );

		foreach ( $yomi_kans as $yomi_kan ) {
			if ( $yomigana == $yomi_kan['yomigana'] ) {
				return true;
			}
		}

		$this->errors[] = _MD_D3FORUM_ERR_JAPANESEINCORRECT;

		return false;
	}

	private function setDictionary( $file ) {
		if ( '/' !== $file[0] ) {
			$file = __DIR__ . '/' . $file;
		}
		if ( is_readable( $file ) ) {
			$this->dictionary_file = $file;

			return true;
		}

		return false;
	}

	public function setVar( $key, $val ) {
		if ( 'dictionary_file' === $key ) {
			if ( ! $val || ! $this->setDictionary( $val ) ) {
				$this->setDictionary( 'AntispamJapaneseDictionary.txt' );
			}
		} else {
			parent::setVar( $key, $val );
		}
	}

}
