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
define( "_GNAV_GPERM_G_INSERTABLE" , "Post (need approval)" ) ;
define( "_GNAV_GPERM_G_SUPERINSERT" , "Super Post" ) ;
define( "_GNAV_GPERM_G_EDITABLE" , "Edit (need approval)" ) ;
define( "_GNAV_GPERM_G_SUPEREDIT" , "Super Edit" ) ;
define( "_GNAV_GPERM_G_DELETABLE" , "Delete (need approval)" ) ;
define( "_GNAV_GPERM_G_SUPERDELETE" , "Super Delete" ) ;
define( "_GNAV_GPERM_G_TOUCHOTHERS" , "Touch photos posted by others" ) ;
define( "_GNAV_GPERM_G_SUPERTOUCHOTHERS" , "Super Touch others" ) ;
define( "_GNAV_GPERM_G_RATEVIEW" , "View Rate" ) ;
define( "_GNAV_GPERM_G_RATEVOTE" , "Vote" ) ;
define( "_GNAV_GPERM_G_WYSIWYG" , "Edit on WYSIWYG Editor" ) ;

// Caption
define( "_GNAV_CAPTION_TOTAL" , "Total:" ) ;
define( "_GNAV_CAPTION_GUESTNAME" , "Guest" ) ;
define( "_GNAV_CAPTION_REFRESH" , "Refresh" ) ;
define( "_GNAV_CAPTION_IMAGEXYT" , "Size(Type)" ) ;
define( "_GNAV_CAPTION_CATEGORY" , "Category" ) ;

}

?>
