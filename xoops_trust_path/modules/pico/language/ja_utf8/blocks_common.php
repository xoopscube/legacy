<?php

// definitions for editing blocks
define("_MB_PICO_CATLIMIT","カテゴリーを指定する");
define("_MB_PICO_CATLIMITDSC","※複数指定する時はカテゴリー番号をカンマ(,)で区切る。サブカテゴリーは含まないことに注意（必要なら、各サブカテゴリーを明示的に指定すること）。0はトップカテゴリーを意味する。カテゴリーを指定しない時は空欄にする。");
define("_MB_PICO_PARENTCAT","親カテゴリー");
define("_MB_PICO_PARENTCATDSC","ここで指定された親カテゴリー直下のカテゴリーのみが表示される。親カテゴリーを複数指定する時はカテゴリー番号をカンマ(,)で区切る。");
define("_MB_PICO_SELECTORDER","表示順");
define("_MB_PICO_CONTENTSNUM","表示件数");
define("_MB_PICO_THISTEMPLATE","このブロックのテンプレート");
define("_MB_PICO_DISPLAYBODY","本文表示する");
define("_MB_PICO_CONTENT_ID","コンテンツ番号");
define("_MB_PICO_PROCESSBODY","本文を動的生成する");
define("_MB_PICO_TAGSNUM","タグ表示数");
define("_MB_PICO_TAGSLISTORDER","タグ表示順");
define("_MB_PICO_TAGSSQLORDER","タグ抽出順");

// LTR or RTL
if( defined( '_ADM_USE_RTL' ) ) {
	@define( '_ALIGN_START' , _ADM_USE_RTL ? 'right' : 'left' ) ;
	@define( '_ALIGN_END' , _ADM_USE_RTL ? 'left' : 'right' ) ;
} else {
	@define( '_ALIGN_START' , 'left' ) ; // change it right for RTL
	@define( '_ALIGN_END' , 'right' ) ;  // change it left for RTL
}


?>