<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( 'GNAVI_CNST_LOADED' ) ) {

define( 'GNAVI_CNST_LOADED' , 1 ) ;

// System Constants (Don't Edit)
define( "GNAV_GPERM_INSERTABLE" , 1 ) ;
define( "GNAV_GPERM_SUPERINSERT" , 2 ) ;
define( "GNAV_GPERM_EDITABLE" , 4 ) ;
define( "GNAV_GPERM_SUPEREDIT" , 8 ) ;
define( "GNAV_GPERM_DELETABLE" , 16 ) ;
define( "GNAV_GPERM_SUPERDELETE" , 32 ) ;
define( "GNAV_GPERM_TOUCHOTHERS" , 64 ) ;
define( "GNAV_GPERM_SUPERTOUCHOTHERS" , 128 ) ;
define( "GNAV_GPERM_RATEVIEW" , 256 ) ;
define( "GNAV_GPERM_RATEVOTE" , 512 ) ;
define( "GNAV_GPERM_WYSIWYG" , 1024 ) ;

// Global Group Permission
define( "_GNAV_GPERM_G_INSERTABLE" , "投稿可（要承認）" ) ;
define( "_GNAV_GPERM_G_SUPERINSERT" , "投稿可（承認不要）" ) ;
define( "_GNAV_GPERM_G_EDITABLE" , "編集可（要承認）" ) ;
define( "_GNAV_GPERM_G_SUPEREDIT" , "編集可（承認不要）" ) ;
define( "_GNAV_GPERM_G_DELETABLE" , "削除可（要承認）" ) ;
define( "_GNAV_GPERM_G_SUPERDELETE" , "削除可（承認不要）" ) ;
define( "_GNAV_GPERM_G_TOUCHOTHERS" , "他ユーザのイメージを編集・削除可（要承認）" ) ;
define( "_GNAV_GPERM_G_SUPERTOUCHOTHERS" , "他ユーザのイメージを編集・削除可（承認不要）" ) ;
define( "_GNAV_GPERM_G_RATEVIEW" , "投票閲覧可" ) ;
define( "_GNAV_GPERM_G_RATEVOTE" , "投票可" ) ;
define( "_GNAV_GPERM_G_WYSIWYG" , "WYSIWYGで編集可" ) ;

// Caption
define( "_GNAV_CAPTION_TOTAL" , "Total:" ) ;
define( "_GNAV_CAPTION_GUESTNAME" , "ゲスト" ) ;
define( "_GNAV_CAPTION_REFRESH" , "更新" ) ;
define( "_GNAV_CAPTION_IMAGEXYT" , "サイズ" ) ;
define( "_GNAV_CAPTION_CATEGORY" , "カテゴリー" ) ;

	// encoding conversion if possible and needed
	/*
	function gnavi_callback_after_stripslashes_local( $text )
	{
		if( function_exists( 'mb_convert_encoding' ) && mb_internal_encoding() !=  mb_http_output() ) {
			return mb_convert_encoding( $text , mb_internal_encoding() , mb_detect_order() ) ;
		} else {
			return $text ;
		}
	}
	*/

}

?>
