<?php
if( ! defined( 'XP2_ADMIN_LANG_INCLUDED' ) ) {
	define( 'XP2_ADMIN_LANG_INCLUDED' , 1 ) ;
	
// altsys
	define('_MD_A_MYMENU_MYTPLSADMIN','templates');
	define('_MD_A_MYMENU_MYBLOCKSADMIN','blocks');
	define('_MD_A_MYMENU_MYLANGADMIN','languages');
	define('_MD_A_MYMENU_MYPREFERENCES','general');

	define("_AM_XP2_SYSTEM_INFO","System infomation");
	define("_AM_XP2_XOOPS_CONFIG_INFO","Set value that XPressME acquired from XOOPS");
	define("_AM_XP2_PLUGIN","Active plug-in list at wordpress");
	define("_AM_XP2_BLOCK_STATS","Block Status");
	define("_AM_XP2_STATS","WordPress Status");
	define("_AM_XP2_CATEGORIES","Category count");
	define("_AM_XP2_ARTICLES","Blog article count");
	define("_AM_XP2_AUTHORS","Author count");
	define("_AM_XP2_SYS_REPORT","Show Report Mode");
	define("_AM_XP2_SYS_NORMAL","Show Normal Mode");
	define("_AM_XP2_BLOCK_OPTIONS","Block Options");
	define("_AM_XP2_GROUP_ROLE","Group Role");

// Block Check	
	define("_AM_XP2_BLOCK_OK","The block is normal. ");
	define("_AM_XP2_BLOCK_NG","There is an abnormal block. ");
	define("_AM_XP2_BLOCK_REPAIR_HOWTO","Please correct the block according to the following procedures. ");
	define("_AM_XP2_BLOCK_REPAIR_STEP1","Step 1");
	define("_AM_XP2_BLOCK_REMOVE","Remove xoops block table");
	define("_AM_XP2_BLOCK_REMOVE_NOTE","Please use carefully, because <b>Remove Block</b> deletes records in block table");
	define("_AM_XP2_BLOCK_REPAIR_STEP2","Step 2");
	define("_AM_XP2_BLOCK_UPDATE","The block is restructured with the update of the module.");
	define("_AM_XP2_TO_MODELE_UPDATE","To the module update");
	define("_AM_XP2_BLOCK_REPAIR_STEP3","Step3");
	define("_AM_XP2_BLOCK_ADMIN_SETTING","The arrangement setting of the block and the access authority of the block are set again. ");
	define("_AM_XP2_BLOCK_TO_SETTING","to blocks/permissions");
	define("_AM_XP2_USER_META_KEY","User meta information");
	define("_AM_XP2_USER_META_NONE","There is no meta key necessary for the user level.");
	define("_AM_XP2_USER_META_ERR","There is a meta key named %s that the prefix is different in %d piece.");
	define("_AM_XP2_USER_META_OK","The user meta key is normal.");		
	define("_AM_XP2_NG_BLOCK_REMOVE","Remove xoops abnormal block table");
	define("_AM_XP2_BLOCK_OR","OR");
}
?>