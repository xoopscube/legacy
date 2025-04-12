<?php

class protector_postcommon_post_htmlpurify4everyone extends ProtectorFilterAbstract {
	public $purifier;
	public $method;

	public function execute() {
		// HTMLPurifier library
		if ( file_exists( LIBRARY_PATH . '/htmlpurifier/library/HTMLPurifier.auto.php' ) ) {

			require_once LIBRARY_PATH . '/htmlpurifier/library/HTMLPurifier.auto.php';
			$config = HTMLPurifier_Config::createDefault();
			$config->set( 'Cache.SerializerPath', XOOPS_TRUST_PATH . '/modules/protector/configs' );
			$config->set( 'Core.Encoding', 'UTF-8' );
			//$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
			$this->purifier = new HTMLPurifier( $config );
			$this->method   = 'purify';

			$_POST = $this->purify_recursive( $_POST );
		}
	}

	public function purify_recursive( $data ) {
		static $encoding = null;
		null === $encoding && ( $encoding = ( _CHARSET === 'UTF-8' ? '' : _CHARSET ) );
		if ( is_array( $data ) ) {
			return array_map( [ $this, 'purify_recursive' ], $data );
		} else {
			if ( strlen( $data ) > 32 ) {
				$_substitute = mb_substitute_character();
				mb_substitute_character( 'none' );
				$encoding && ( $data = mb_convert_encoding( $data, 'UTF-8', $encoding ) );
				$data = call_user_func( [ $this->purifier, $this->method ], $data );
				$encoding && ( $data = mb_convert_encoding( $data, $encoding, 'UTF-8' ) );
				mb_substitute_character( $_substitute );
			}

			return $data;
		}
	}
}
