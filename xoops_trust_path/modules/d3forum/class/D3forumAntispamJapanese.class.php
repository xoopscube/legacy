<?php

define( '_D3FORUM_ANTISPAM_JAPANESE_AMB' , 3 ) ;

require_once dirname(__FILE__).'/D3forumAntispamAbstract.class.php' ;

class D3forumAntispamJapanese extends D3forumAntispamAbstract {

var $dictionary_cache = array() ;

function getKanaKanji( $time = null )
{
	if( empty( $time ) ) $time = time() ;
	
	if( empty( $this->dictionary_cache ) ) {
		// SKK EUC dictionary
		$lines = file( dirname( __FILE__ ) . '/AntispamJapaneseDictionary.txt' ) ;
		foreach( $lines as $line ) {
			$line = mb_convert_encoding( $line , mb_internal_encoding() , 'EUC-JP' ) ;
			if( preg_match( '#(.+) /(.+)/#' , $line , $regs ) ) {
				$this->dictionary_cache[] = array(
					'yomigana' => $regs[1] ,
					'kanji' => $regs[2] ,
				) ;
			}
		}
	}
	
	$size = sizeof( $this->dictionary_cache ) ;
	$ret = array() ;
	for( $i = 0 ; $i < 3 ; $i ++ ) {
		$ret[] = $this->dictionary_cache[ abs( crc32( md5( gmdate( 'YmdH' , $time ) . XOOPS_DB_PREFIX . XOOPS_DB_NAME . $i ) ) ) % $size ] ;
	}

	return $ret ;
}

function getHtml4Assign()
{
	$yomi_kans = $this->getKanaKanji() ;
	shuffle( $yomi_kans ) ;
	$yomi_kan = $yomi_kans[0] ;
	$kanji = $yomi_kan['kanji'] ;

	$html = '<label for="antispam_yomigana">'._MD_D3FORUM_LABEL_JAPANESEINPUTYOMI.': <strong class="antispam_kanji">'.htmlspecialchars($kanji).'</strong></label><input type="text" name="antispam_yomigana" id="antispam_yomigana" value="" />' ;

	return array(
		'html_in_form' => $html ,
		'js_global' => '' ,
		'js_in_validate_function' => 'if ( ! myform.antispam_yomigana.value ) { window.alert("'._MD_D3FORUM_ERR_JAPANESENOTINPUT.'"); myform.antispam_yomigana.focus(); return false; }
' ,
	) ;
}

function checkValidate()
{
	$yomigana = mb_convert_kana( trim( @$_POST['antispam_yomigana'] ) , 'HVc' ) ;

	$yomi_kans = array_merge( $this->getKanaKanji() , $this->getKanaKanji( time() - 3600 ) ) ;

	foreach( $yomi_kans as $yomi_kan ) {
		if( $yomigana == $yomi_kan['yomigana'] ) {
			return true ;
		}
	}

	$this->errors[] = _MD_D3FORUM_ERR_JAPANESEINCORRECT ;
	return false ;
}

}

?>